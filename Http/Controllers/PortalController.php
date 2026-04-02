<?php

namespace App\Http\Controllers;

use App\Models\ConsultationRequest;
use App\Models\Dietitian;
use App\Models\FeedbackRequest;
use App\Models\FoodItem;
use App\Models\FoodLogEntry;
use App\Models\MealPlan;
use App\Models\PlannedMealEntry;
use App\Models\UserExperience;
use Database\Seeders\PortalDemoSeeder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PortalController extends Controller
{
    public function user(Request $request, string $page = 'dashboard'): View
    {
        $pages = $this->userPages();

        abort_unless(array_key_exists($page, $pages), 404);

        $experience = $this->currentExperience($request);
        $selectedDate = $this->selectedDate($request);

        return view('portal', [
            'page' => $page,
            'pages' => $pages,
            'currentPage' => $pages[$page],
            'portalData' => $this->portalData($experience, $selectedDate, $request),
        ]);
    }

    public function admin(Request $request, string $page = 'dashboard'): View
    {
        $pages = $this->adminPages();

        abort_unless(array_key_exists($page, $pages), 404);

        $this->seedDemoData();

        return view('admin-portal', [
            'page' => $page,
            'pages' => $pages,
            'currentPage' => $pages[$page],
            'adminData' => $this->adminData($request),
        ]);
    }

    /**
     * @return array<string, array{name: string, route: string}>
     */
    private function userPages(): array
    {
        return [
            'dashboard' => ['name' => 'Dashboard', 'route' => 'portal.dashboard'],
            'food-log' => ['name' => 'Food Log', 'route' => 'portal.food-log'],
            'calendar' => ['name' => 'Calendar', 'route' => 'portal.calendar'],
            'insights' => ['name' => 'Insights', 'route' => 'portal.insights'],
            'meal-plans' => ['name' => 'Meal Plans', 'route' => 'portal.meal-plans'],
            'feedback' => ['name' => 'Feedback', 'route' => 'portal.feedback'],
            'profile' => ['name' => 'Profile', 'route' => 'portal.profile'],
        ];
    }

    /**
     * @return array<string, array{name: string, route: string}>
     */
    private function adminPages(): array
    {
        return [
            'dashboard' => ['name' => 'Dashboard', 'route' => 'admin.dashboard'],
            'users' => ['name' => 'Users', 'route' => 'admin.users'],
            'dietitians' => ['name' => 'Dietitians', 'route' => 'admin.dietitians'],
            'feedback' => ['name' => 'Feedback', 'route' => 'admin.feedback'],
            'content' => ['name' => 'Content', 'route' => 'admin.content'],
            'analytics' => ['name' => 'Analytics', 'route' => 'admin.analytics'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function portalData(UserExperience $experience, Carbon $selectedDate, Request $request): array
    {
        $todayEntries = $experience->foodLogEntries()
            ->whereDate('entry_date', today())
            ->orderBy('meal_slot')
            ->latest('id')
            ->get();

        $mealSlots = ['Breakfast', 'Lunch', 'Dinner', 'Snacks'];
        $groupedEntries = collect($mealSlots)->mapWithKeys(function (string $slot) use ($todayEntries): array {
            $entries = $todayEntries->where('meal_slot', $slot)->values();

            return [
                $slot => [
                    'count' => $entries->count(),
                    'summary' => sprintf(
                        '%d cal • %.1fg protein',
                        (int) $entries->sum('calories'),
                        $entries->sum(fn (FoodLogEntry $entry): float => (float) $entry->protein),
                    ),
                    'items' => $entries,
                ],
            ];
        });

        $activePlan = MealPlan::query()
            ->with('items')
            ->find($experience->active_meal_plan_id);

        $customPlans = $experience->mealPlans()->with('items')->latest()->get();
        $templatePlans = MealPlan::query()->where('is_template', true)->with('items')->orderBy('name')->get();

        $editablePlan = $customPlans->firstWhere('id', (int) $request->integer('edit_plan'))
            ?? $activePlan
            ?? $customPlans->first();

        $dietitian = $experience->activeDietitian()->first() ?? Dietitian::query()->first();
        $feedbackItems = $experience->feedbackRequests()->with('dietitian')->latest('submitted_on')->get();
        $consultationCount = ConsultationRequest::query()->where('user_experience_id', $experience->id)->count();

        $macroTotals = [
            'Calories' => ['value' => $todayEntries->sum('calories'), 'goal' => 2000, 'suffix' => ''],
            'Protein' => ['value' => round($todayEntries->sum(fn (FoodLogEntry $entry): float => (float) $entry->protein), 1), 'goal' => 150, 'suffix' => 'g'],
            'Meals Logged' => ['value' => $todayEntries->count(), 'goal' => 4, 'suffix' => ' meals'],
            'Unread Notes' => ['value' => $feedbackItems->where('is_read', false)->count(), 'goal' => 0, 'suffix' => ''],
        ];

        return [
            'hero' => $this->heroContent(),
            'dashboardStats' => collect($macroTotals)->map(function (array $metric, string $label): array {
                $goal = max((float) $metric['goal'], 1);
                $progress = min((((float) $metric['value']) / $goal) * 100, 100);

                return [
                    'label' => $label,
                    'value' => $metric['value'],
                    'goal' => $metric['goal'],
                    'suffix' => $metric['suffix'],
                    'progress' => round($progress, 1),
                ];
            })->values()->all(),
            'foodLogMeals' => $groupedEntries,
            'foodItems' => FoodItem::query()->where('is_active', true)->orderBy('name')->get(),
            'selectedMeal' => $request->query('meal', 'Breakfast'),
            'mealPlans' => $templatePlans,
            'customPlans' => $customPlans,
            'editablePlan' => $editablePlan,
            'activePlan' => $activePlan,
            'calendar' => $this->calendarData($experience, $selectedDate, $activePlan, $todayEntries),
            'feedbackItems' => $feedbackItems,
            'dietitian' => $dietitian,
            'consultationCount' => $consultationCount,
            'profile' => $this->profileData($experience),
            'insights' => $this->insightsData($experience, $todayEntries, $activePlan),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function adminData(Request $request): array
    {
        $foodItems = FoodItem::query()->orderBy('name')->get();
        $mealPlans = MealPlan::query()->where('is_template', true)->with('items')->latest()->get();
        $dietitians = Dietitian::query()->orderBy('name')->get();
        $users = UserExperience::query()
            ->with(['activeDietitian', 'activeMealPlan', 'foodLogEntries', 'plannedMealEntries', 'feedbackRequests'])
            ->orderBy('full_name')
            ->get();
        $feedbackRequests = FeedbackRequest::query()->with(['dietitian', 'userExperience'])->latest('submitted_on')->get();
        $editingUser = $users->firstWhere('id', $request->integer('edit_user')) ?? $users->first();

        return [
            'summary' => [
                ['label' => 'Users', 'value' => $users->count()],
                ['label' => 'Dietitians', 'value' => $dietitians->count()],
                ['label' => 'Food Items', 'value' => $foodItems->count()],
                ['label' => 'Meal Plans', 'value' => $mealPlans->count()],
            ],
            'users' => $users,
            'dietitians' => $dietitians,
            'foodItems' => $foodItems,
            'mealPlans' => $mealPlans,
            'feedbackRequests' => $feedbackRequests,
            'editingUser' => $editingUser,
            'editingFoodItem' => FoodItem::query()->find($request->integer('edit_food')),
            'editingMealPlan' => MealPlan::query()->where('is_template', true)->with('items')->find($request->integer('edit_meal_plan')),
        ];
    }

    /**
     * @return array<string, array{title: string, subtitle: string}>
     */
    private function heroContent(): array
    {
        return [
            'dashboard' => ['title' => 'Your nutrition at a glance', 'subtitle' => 'Track what you eat, switch plans, and keep your profile current.'],
            'food-log' => ['title' => 'Food log', 'subtitle' => 'Add foods to each meal and keep your totals current.'],
            'calendar' => ['title' => 'Meal calendar', 'subtitle' => 'Preview the next few days from your active meal plan.'],
            'insights' => ['title' => 'Insights', 'subtitle' => 'See trends pulled from your saved activity and active plan.'],
            'meal-plans' => ['title' => 'Meal plans', 'subtitle' => 'Use a template, then customize every meal item however you want.'],
            'feedback' => ['title' => 'Feedback and support', 'subtitle' => 'Request help, schedule a consultation, and keep up with dietitian notes.'],
            'profile' => ['title' => 'Profile', 'subtitle' => 'Your saved body metrics and goals update the experience across the app.'],
        ];
    }

    /**
     * @param  Collection<int, FoodLogEntry>  $todayEntries
     * @return array<string, mixed>
     */
    private function insightsData(UserExperience $experience, Collection $todayEntries, ?MealPlan $activePlan): array
    {
        $calories = (int) $todayEntries->sum('calories');
        $protein = round($todayEntries->sum(fn (FoodLogEntry $entry): float => (float) $entry->protein), 1);
        $goalGap = max((float) $experience->current_weight_kg - (float) $experience->target_weight_kg, 0);

        // Get historical data for progress analysis
        $last7Days = collect(range(0, 6))->map(function ($days) {
            return today()->subDays($days)->toDateString();
        });

        $weeklyEntries = $experience->foodLogEntries()
            ->whereIn('entry_date', $last7Days)
            ->get()
            ->groupBy('entry_date');

        $weeklyCalories = $last7Days->map(function ($date) use ($weeklyEntries) {
            return $weeklyEntries->get($date, collect())->sum('calories');
        })->values();

        $avgWeeklyCalories = $weeklyCalories->avg();
        $daysLogged = $weeklyEntries->count();
        $proteinGoal = 150;
        $calorieGoal = 2000;

        // Generate dynamic insight highlights based on progress
        $insightHighlights = [];

        // Calorie analysis
        if ($avgWeeklyCalories > $calorieGoal * 1.1) {
            $insightHighlights[] = [
                'title' => 'High calorie intake',
                'copy' => 'Your average daily calories are above your target. Consider reducing portion sizes.',
                'tone' => 'portal-tone-red',
            ];
        } elseif ($avgWeeklyCalories < $calorieGoal * 0.9) {
            $insightHighlights[] = [
                'title' => 'Low calorie intake',
                'copy' => 'You\'re consuming fewer calories than recommended. Try adding nutrient-dense foods.',
                'tone' => 'portal-tone-yellow',
            ];
        } else {
            $insightHighlights[] = [
                'title' => 'Great calorie balance!',
                'copy' => 'You\'re maintaining healthy caloric intake levels this week.',
                'tone' => 'portal-tone-green',
            ];
        }

        // Protein analysis
        $avgProtein = $weeklyEntries->flatten()->avg(fn ($entry) => $entry->protein) ?? 0;
        if ($avgProtein >= $proteinGoal * 0.9) {
            $insightHighlights[] = [
                'title' => 'Excellent protein intake!',
                'copy' => 'You\'re consistently meeting your protein goals. Keep up the great work!',
                'tone' => 'portal-tone-green',
            ];
        } elseif ($avgProtein >= $proteinGoal * 0.7) {
            $insightHighlights[] = [
                'title' => 'Good protein progress',
                'copy' => 'You\'re getting decent protein, but there\'s room for improvement.',
                'tone' => 'portal-tone-blue',
            ];
        } else {
            $insightHighlights[] = [
                'title' => 'Boost your protein',
                'copy' => 'Consider adding more protein-rich foods like lean meats, eggs, or legumes.',
                'tone' => 'portal-tone-yellow',
            ];
        }

        // Logging consistency
        if ($daysLogged >= 6) {
            $insightHighlights[] = [
                'title' => 'Consistent logging!',
                'copy' => 'You\'ve been great about logging your meals this week.',
                'tone' => 'portal-tone-green',
            ];
        } elseif ($daysLogged >= 4) {
            $insightHighlights[] = [
                'title' => 'Good logging habits',
                'copy' => 'You\'re logging most days. Try to log every meal for better insights.',
                'tone' => 'portal-tone-blue',
            ];
        } else {
            $insightHighlights[] = [
                'title' => 'Increase meal logging',
                'copy' => 'Regular meal logging helps us provide better personalized recommendations.',
                'tone' => 'portal-tone-yellow',
            ];
        }

        // Weight progress analysis
        if ($experience->bmi_history && count($experience->bmi_history) > 1) {
            $recentWeights = collect($experience->bmi_history)->take(4)->pluck('weight')->map(fn ($w) => (float) str_replace(' kg', '', $w));
            if ($recentWeights->count() > 1) {
                $weightTrend = $recentWeights->first() - $recentWeights->last();
                if ($weightTrend > 0.5) {
                    $insightHighlights[] = [
                        'title' => 'Great weight progress!',
                        'copy' => 'You\'ve lost weight recently. Keep up the healthy habits!',
                        'tone' => 'portal-tone-green',
                    ];
                } elseif ($weightTrend < -0.5) {
                    $insightHighlights[] = [
                        'title' => 'Weight gain detected',
                        'copy' => 'Consider reviewing your calorie intake and portion sizes.',
                        'tone' => 'portal-tone-red',
                    ];
                }
            }
        }

        // Generate dynamic performance cards
        $performanceCards = [
            [
                'title' => 'Average Calories',
                'value' => number_format($avgWeeklyCalories, 0),
                'suffix' => '',
                'target' => 'Target: '.$calorieGoal,
                'progress' => min(($avgWeeklyCalories / $calorieGoal) * 100, 150),
            ],
            [
                'title' => 'Protein Goal Achievement',
                'value' => number_format(($avgProtein / $proteinGoal) * 100, 0),
                'suffix' => '%',
                'target' => 'Target: 100%',
                'progress' => min(($avgProtein / $proteinGoal) * 100, 100),
            ],
            [
                'title' => 'Meal Logging Consistency',
                'value' => $daysLogged,
                'suffix' => ' /7 days',
                'target' => 'Target: 7/7 days',
                'progress' => ($daysLogged / 7) * 100,
            ],
        ];

        // Generate dynamic achievements based on progress
        $achievements = [];

        // 7-day streak achievement
        if ($daysLogged >= 7) {
            $achievements[] = [
                'title' => '7-Day Streak',
                'copy' => 'Logged meals for 7 consecutive days',
                'tone' => 'portal-tone-green',
                'unlocked' => true,
            ];
        } else {
            $achievements[] = [
                'title' => '7-Day Streak',
                'copy' => 'Log meals for 7 days in a row',
                'tone' => 'portal-tone-green',
                'unlocked' => false,
            ];
        }

        // Protein master achievement
        $highProteinDays = $weeklyEntries->filter(function ($dayEntries) {
            $protein = $dayEntries->sum(fn ($entry) => $entry->protein);

            return $protein >= 130; // 130g is 87% of 150g goal
        })->count();

        if ($highProteinDays >= 5) {
            $achievements[] = [
                'title' => 'Protein Master',
                'copy' => 'Met protein goals for 5+ days this week',
                'tone' => 'portal-tone-green',
                'unlocked' => true,
            ];
        } else {
            $achievements[] = [
                'title' => 'Protein Master',
                'copy' => 'Meet protein goals for 5 days this week',
                'tone' => 'portal-tone-green',
                'unlocked' => false,
            ];
        }

        // Calorie consistency achievement
        $consistentDays = $weeklyEntries->filter(function ($dayEntries) use ($calorieGoal) {
            $calories = $dayEntries->sum('calories');

            return abs($calories - $calorieGoal) <= 200; // Within 200 calories of goal
        })->count();

        if ($consistentDays >= 5) {
            $achievements[] = [
                'title' => 'Calorie Consistency',
                'copy' => 'Stayed within 200 calories of goal for 5+ days',
                'tone' => 'portal-tone-blue',
                'unlocked' => true,
            ];
        } else {
            $achievements[] = [
                'title' => 'Calorie Consistency',
                'copy' => 'Stay within 200 calories of your goal for 5 days',
                'tone' => 'portal-tone-blue',
                'unlocked' => false,
            ];
        }

        // Meal planning achievement
        $plannedMealsCount = PlannedMealEntry::query()
            ->where('user_experience_id', $experience->id)
            ->whereBetween('scheduled_date', [today()->startOfWeek(), today()->endOfWeek()])
            ->count();

        if ($plannedMealsCount >= 14) { // 2 meals per day for 7 days
            $achievements[] = [
                'title' => 'Meal Planner Pro',
                'copy' => 'Planned 14+ meals for the week',
                'tone' => 'portal-tone-purple',
                'unlocked' => true,
            ];
        } else {
            $achievements[] = [
                'title' => 'Meal Planner Pro',
                'copy' => 'Plan meals for the entire week',
                'tone' => 'portal-tone-purple',
                'unlocked' => false,
            ];
        }

        // Generate dynamic recommendations based on user's data
        $recommendations = [];

        // Calorie-based recommendations
        if ($avgWeeklyCalories < $calorieGoal * 0.85) {
            $recommendations[] = [
                'title' => 'Increase Calorie Intake',
                'priority' => 'high priority',
                'priority_tone' => 'portal-badge--red',
                'copy' => 'Your calorie intake is below target. Consider adding calorie-dense, nutrient-rich foods.',
                'tips' => ['Add healthy fats like avocados and nuts', 'Include more complex carbohydrates', 'Consider protein shakes or smoothies'],
            ];
        } elseif ($avgWeeklyCalories > $calorieGoal * 1.15) {
            $recommendations[] = [
                'title' => 'Reduce Calorie Intake',
                'priority' => 'high priority',
                'priority_tone' => 'portal-badge--red',
                'copy' => 'Your calorie intake exceeds your target. Focus on portion control and nutrient density.',
                'tips' => ['Use smaller plates to control portions', 'Choose water or unsweetened beverages', 'Focus on high-volume, low-calorie foods'],
            ];
        }

        // Protein recommendations
        if ($avgProtein < $proteinGoal * 0.8) {
            $recommendations[] = [
                'title' => 'Increase Protein Intake',
                'priority' => 'medium priority',
                'priority_tone' => 'portal-badge--yellow',
                'copy' => 'You\'re not meeting your protein goals. Protein is essential for muscle maintenance and satiety.',
                'tips' => ['Include protein in every meal', 'Try Greek yogurt or cottage cheese', 'Consider lean meats, fish, or plant-based proteins'],
            ];
        }

        // Logging consistency recommendations
        if ($daysLogged < 5) {
            $recommendations[] = [
                'title' => 'Improve Meal Logging',
                'priority' => 'medium priority',
                'priority_tone' => 'portal-badge--yellow',
                'copy' => 'Consistent meal logging helps us provide better insights and recommendations.',
                'tips' => ['Set reminders to log meals', 'Use the mobile app for quick logging', 'Log meals right after eating for accuracy'],
            ];
        }

        // Meal planning recommendations
        if ($plannedMealsCount < 7) {
            $recommendations[] = [
                'title' => 'Start Meal Planning',
                'priority' => 'low priority',
                'priority_tone' => 'portal-badge--blue',
                'copy' => 'Meal planning can help you stay consistent with your nutrition goals.',
                'tips' => ['Plan meals for 2-3 days at a time', 'Prep ingredients in advance', 'Use the calendar feature to schedule meals'],
            ];
        }

        return [
            'cards' => [
                ['title' => 'Calories today', 'value' => $calories, 'meta' => 'Goal 2000'],
                ['title' => 'Protein today', 'value' => $protein.'g', 'meta' => 'Goal 150g'],
                ['title' => 'Current goal gap', 'value' => $goalGap.'kg', 'meta' => 'Remaining to target'],
                ['title' => 'Active plan', 'value' => $activePlan?->name ?? 'No active plan', 'meta' => $activePlan ? $activePlan->daily_calories.' cal/day' : 'Create a plan'],
            ],
            'recommendations' => [
                'Add more foods to dinner if your logged calories are far below target.',
                'Use the meal-plan editor to remove or swap foods instead of starting over.',
                'Save your profile metrics after updates so the BMI and intake summaries stay aligned.',
            ],
            'insightHighlights' => $insightHighlights,
            'performanceCards' => $performanceCards,
            'achievements' => $achievements,
            'personalizedRecommendations' => $recommendations,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function profileData(UserExperience $experience): array
    {
        $heightInMeters = $experience->height_cm ? $experience->height_cm / 100 : 0;
        $bmi = $heightInMeters > 0 && $experience->current_weight_kg
            ? round(((float) $experience->current_weight_kg) / ($heightInMeters * $heightInMeters), 1)
            : 0.0;

        $profile = [
            'full_name' => $experience->full_name && trim($experience->full_name) !== '' ? $experience->full_name : '',
            'age' => $experience->age > 0 ? $experience->age : '',
            'gender' => $experience->gender && trim($experience->gender) !== '' ? $experience->gender : '',
            'activity_level' => $experience->activity_level && trim($experience->activity_level) !== '' ? $experience->activity_level : '',
            'primary_goal' => $experience->primary_goal && trim($experience->primary_goal) !== '' ? $experience->primary_goal : '',
            'height_cm' => $experience->height_cm > 0 ? $experience->height_cm : '',
            'current_weight_kg' => $experience->current_weight_kg > 0 ? $experience->current_weight_kg : '',
            'target_weight_kg' => $experience->target_weight_kg > 0 ? $experience->target_weight_kg : '',
            'starting_weight_kg' => $experience->starting_weight_kg > 0 ? $experience->starting_weight_kg : '',
            'bmi' => $bmi > 0 ? $bmi : '',
            'status' => $bmi > 0 ? $this->bmiStatus($bmi) : '',
            'remaining_weight_kg' => ($experience->current_weight_kg && $experience->target_weight_kg)
                ? max((float) $experience->current_weight_kg - (float) $experience->target_weight_kg, 0)
                : '',
            'bmi_history' => $experience->bmi_history ?? [],
        ];

        return $profile;
    }

    /**
     * @param  Collection<int, FoodLogEntry>  $todayEntries
     * @return array<string, mixed>
     */
    private function calendarData(UserExperience $experience, Carbon $selectedDate, ?MealPlan $activePlan, Collection $todayEntries): array
    {
        $monthStart = $selectedDate->copy()->startOfMonth();
        $daysInMonth = $monthStart->daysInMonth;
        $plannedEntries = PlannedMealEntry::query()
            ->where('user_experience_id', $experience->id)
            ->whereBetween('scheduled_date', [
                $monthStart->copy()->startOfMonth()->toDateString(),
                $monthStart->copy()->endOfMonth()->toDateString(),
            ])
            ->orderBy('scheduled_date')
            ->orderBy('meal_slot')
            ->get()
            ->groupBy(fn (PlannedMealEntry $entry): string => $entry->scheduled_date->toDateString());

        $days = collect(range(1, $daysInMonth))->map(function (int $day) use ($monthStart, $selectedDate, $todayEntries, $plannedEntries): array {
            $date = $monthStart->copy()->day($day);
            $entriesForDate = $plannedEntries->get($date->toDateString(), collect())->keyBy('meal_slot');

            return [
                'label' => $day,
                'date' => $date->toDateString(),
                'selected' => $selectedDate->isSameDay($date),
                'has_log' => $todayEntries->isNotEmpty() && $date->isToday(),
                'has_plan' => $entriesForDate->isNotEmpty(),
                'count' => $entriesForDate->count(),
                'entries' => $entriesForDate,
            ];
        });

        $upcoming = collect(range(0, 2))->map(function (int $offset) use ($experience, $selectedDate): array {
            $date = $selectedDate->copy()->addDays($offset);
            $plannedEntries = PlannedMealEntry::query()
                ->where('user_experience_id', $experience->id)
                ->whereDate('scheduled_date', $date)
                ->orderBy('meal_slot')
                ->get();
            $groupedMeals = $plannedEntries
                ->groupBy('meal_slot')
                ->map(fn (Collection $items): array => $items->map(
                    fn (PlannedMealEntry $item): string => $item->food_name.' ('.$item->grams.'g • '.$item->calories.' cal)'
                )->all())
                ->all();

            return [
                'label' => $date->format('l, M j'),
                'date' => $date->toDateString(),
                'meals' => $groupedMeals,
            ];
        })->all();

        return [
            'month_label' => $monthStart->format('F Y'),
            'selected_date' => $selectedDate->toDateString(),
            'previous_month' => $monthStart->copy()->subMonthNoOverflow()->toDateString(),
            'next_month' => $monthStart->copy()->addMonthNoOverflow()->toDateString(),
            'days' => $days,
            'upcoming' => $upcoming,
        ];
    }

    private function selectedDate(Request $request): Carbon
    {
        $date = $request->query('date');

        return $date ? Carbon::parse($date) : today();
    }

    private function currentExperience(Request $request): UserExperience
    {
        $this->seedDemoData();

        $sessionKey = $request->session()->get('portal_session_key') ?: $request->cookie('portal_session_key');

        if (! is_string($sessionKey) || trim($sessionKey) === '') {
            if ($request->user() !== null) {
                $sessionKey = $request->user()->experiences()->latest('id')->value('session_key');
            }
        }

        if (! is_string($sessionKey) || trim($sessionKey) === '') {
            $sessionKey = (string) Str::uuid();
        }

        $request->session()->put('portal_session_key', $sessionKey);
        cookie()->queue('portal_session_key', $sessionKey, 60 * 24 * 365); // 1 year

        if ($request->user() !== null) {
            // For authenticated users, find ANY experience with this session_key
            // (it could be orphaned from unauthenticated browsing)
            $experience = UserExperience::where('session_key', $sessionKey)->first();

            if ($experience) {
                // Update user_id if not already associated with this user
                if ($experience->user_id !== $request->user()->id) {
                    $experience->update(['user_id' => $request->user()->id]);
                }
            } else {
                // Create new experience for this session and user
                $experience = UserExperience::create([
                    'session_key' => $sessionKey,
                    'user_id' => $request->user()->id,
                    'active_dietitian_id' => Dietitian::query()->value('id'),
                ]);
            }
        } else {
            $experience = UserExperience::query()->firstOrCreate(
                ['session_key' => $sessionKey],
                ['active_dietitian_id' => Dietitian::query()->value('id')]
            );
        }

        if ($experience->wasRecentlyCreated) {
            $this->initializeExperience($experience);
        }

        return $experience->fresh(['activeDietitian', 'mealPlans.items', 'foodLogEntries', 'feedbackRequests.dietitian', 'plannedMealEntries']);
    }

    private function initializeExperience(UserExperience $experience): void
    {
        $templatePlan = MealPlan::query()->where('is_template', true)->with('items')->first();

        if ($templatePlan !== null) {
            $customPlan = MealPlan::query()->create([
                'user_experience_id' => $experience->id,
                'name' => $templatePlan->name.' Copy',
                'description' => $templatePlan->description,
                'daily_calories' => $templatePlan->daily_calories,
                'tags' => $templatePlan->tags,
                'rating' => $templatePlan->rating,
                'is_template' => false,
                'is_active' => true,
            ]);

            foreach ($templatePlan->items as $item) {
                $customPlan->items()->create([
                    'food_item_id' => $item->food_item_id,
                    'meal_slot' => $item->meal_slot,
                    'item_name' => $item->item_name,
                    'serving_label' => $item->serving_label,
                    'sort_order' => $item->sort_order,
                ]);
            }

            $experience->forceFill(['active_meal_plan_id' => $customPlan->id])->save();

            foreach ($customPlan->items()->whereIn('meal_slot', ['Breakfast', 'Lunch'])->get() as $item) {
                FoodLogEntry::query()->create([
                    'user_experience_id' => $experience->id,
                    'food_item_id' => $item->food_item_id,
                    'meal_slot' => $item->meal_slot,
                    'food_name' => $item->item_name,
                    'serving_label' => $item->serving_label,
                    'entry_date' => today()->toDateString(),
                    'calories' => $item->foodItem?->calories ?? 100,
                    'protein' => $item->foodItem?->protein ?? 5,
                ]);
            }

            foreach (range(0, 2) as $offset) {
                $scheduledDate = today()->addDays($offset)->toDateString();

                foreach ($customPlan->items->groupBy('meal_slot')->map->first() as $item) {
                    PlannedMealEntry::query()->firstOrCreate([
                        'user_experience_id' => $experience->id,
                        'scheduled_date' => $scheduledDate,
                        'meal_slot' => $item->meal_slot,
                    ], [
                        'food_name' => $item->item_name,
                        'grams' => (int) filter_var($item->serving_label, FILTER_SANITIZE_NUMBER_INT) ?: 100,
                        'calories' => $item->foodItem?->calories ?? 100,
                    ]);
                }
            }
        }

        $dietitian = Dietitian::query()->find($experience->active_dietitian_id);

        if ($dietitian !== null) {
            FeedbackRequest::query()->create([
                'user_experience_id' => $experience->id,
                'dietitian_id' => $dietitian->id,
                'title' => 'Welcome to your plan',
                'topic' => 'meal plan',
                'tag' => 'New',
                'tag_tone' => 'blue',
                'priority' => 'medium',
                'status' => 'in-progress',
                'message' => 'Your plan is ready. Use the meal-plan page to swap foods, add items, and make your own version.',
                'recommendations' => [
                    'Activate the plan you want to follow.',
                    'Customize meals directly from the meal-plan builder.',
                    'Use the food log each day so your stats stay current.',
                ],
                'submitted_on' => today()->toDateString(),
            ]);
        }
    }

    private function seedDemoData(): void
    {
        if (FoodItem::query()->doesntExist()) {
            app(PortalDemoSeeder::class)->run();
        }
    }

    private function bmiStatus(float $bmi): string
    {
        return match (true) {
            $bmi < 18.5 => 'Underweight',
            $bmi < 25 => 'Normal',
            $bmi < 30 => 'Overweight',
            default => 'Obese',
        };
    }
}
