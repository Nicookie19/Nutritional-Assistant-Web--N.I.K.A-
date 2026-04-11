@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    $mealSlots = ['Breakfast', 'Lunch', 'Dinner', 'Snacks'];
    $editablePlan = $portalData['editablePlan'];
    $foodLogMeals = $portalData['foodLogMeals'];
    $activePlan = $portalData['activePlan'];
    $insights = $portalData['insights'];
    $profile = $portalData['profile'];
    $calendar = $portalData['calendar'];
    $feedbackItems = $portalData['feedbackItems'];
    $dietitian = $portalData['dietitian'];

    $dashboardMetrics = [
        [
            'label' => 'Calories',
            'value' => (int) ($foodLogMeals['Breakfast']['items']->sum('calories') + $foodLogMeals['Lunch']['items']->sum('calories') + $foodLogMeals['Dinner']['items']->sum('calories') + $foodLogMeals['Snacks']['items']->sum('calories')),
            'goal' => 2000,
            'suffix' => '',
            'tone' => 'portal-tone-orange',
            'remaining_class' => 'is-orange',
        ],
        [
            'label' => 'Protein',
            'value' => round($foodLogMeals['Breakfast']['items']->sum('protein') + $foodLogMeals['Lunch']['items']->sum('protein') + $foodLogMeals['Dinner']['items']->sum('protein') + $foodLogMeals['Snacks']['items']->sum('protein')),
            'goal' => 150,
            'suffix' => 'g',
            'tone' => 'portal-tone-green',
            'remaining_class' => 'is-orange',
        ],
        [
            'label' => 'Carbs',
            'value' => (int) round(($foodLogMeals['Breakfast']['items']->sum('calories') + $foodLogMeals['Lunch']['items']->sum('calories') + $foodLogMeals['Dinner']['items']->sum('calories') + $foodLogMeals['Snacks']['items']->sum('calories')) * 0.11),
            'goal' => 225,
            'suffix' => 'g',
            'tone' => 'portal-tone-yellow',
            'remaining_class' => 'is-orange',
        ],
        [
            'label' => 'Fat',
            'value' => max((int) round(($foodLogMeals['Breakfast']['items']->sum('calories') + $foodLogMeals['Lunch']['items']->sum('calories') + $foodLogMeals['Dinner']['items']->sum('calories') + $foodLogMeals['Snacks']['items']->sum('calories')) * 0.03), 12),
            'goal' => 67,
            'suffix' => 'g',
            'tone' => 'portal-tone-red',
            'remaining_class' => 'is-orange',
        ],
    ];

    $macroMap = [
        'Protein' => max((float) $dashboardMetrics[1]['value'], 1),
        'Carbs' => max((float) $dashboardMetrics[2]['value'], 1),
        'Fat' => max((float) $dashboardMetrics[3]['value'], 1),
    ];
    $macroTotal = array_sum($macroMap);
    $proteinAngle = round(($macroMap['Protein'] / $macroTotal) * 360, 2);
    $carbAngle = round(($macroMap['Carbs'] / $macroTotal) * 360, 2);
    $fatAngle = 360 - $proteinAngle - $carbAngle;
    $pieStyle = 'conic-gradient(#59b985 0deg '.$proteinAngle.'deg, #f4aa34 '.$proteinAngle.'deg '.($proteinAngle + $carbAngle).'deg, #e1554d '.($proteinAngle + $carbAngle).'deg 360deg)';

    $weeklyCalories = [1850, 2060, 1940, 2025, 2140, 2230, max($dashboardMetrics[0]['value'], 820)];
    $weeklyLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Today'];
    $adminPreviewBars = [920, 980, 1100, 1180, 1210, 1240, 1270];

    $dashboardMeals = $foodLogMeals->flatMap(fn (array $meal) => $meal['items'])->take(6);

    $micronutrients = [
        ['label' => 'Vitamin D', 'value' => '12 mcg', 'meta' => '80% of daily goal', 'tone' => 'portal-tone-blue'],
        ['label' => 'Vitamin C', 'value' => '85 mg', 'meta' => '94% of daily goal', 'tone' => 'portal-tone-green'],
        ['label' => 'Iron', 'value' => '14 mg', 'meta' => '78% of daily goal', 'tone' => 'portal-tone-orange'],
        ['label' => 'Calcium', 'value' => '950 mg', 'meta' => '95% of daily goal', 'tone' => 'portal-tone-purple'],
        ['label' => 'Vitamin A', 'value' => '720 mcg', 'meta' => '80% of daily goal', 'tone' => 'portal-tone-yellow'],
        ['label' => 'Vitamin B12', 'value' => '2.1 mcg', 'meta' => '88% of daily goal', 'tone' => 'portal-tone-red'],
        ['label' => 'Magnesium', 'value' => '340 mg', 'meta' => '85% of daily goal', 'tone' => 'portal-tone-indigo'],
        ['label' => 'Zinc', 'value' => '9 mg', 'meta' => '82% of daily goal', 'tone' => 'portal-tone-pink'],
    ];

    $calendarStats = [
        ['label' => 'Meals Planned', 'value' => $activePlan?->items->count() ? $activePlan->items->count() * 3 : 21, 'tone' => 'portal-tone-green'],
        ['label' => 'Days Scheduled', 'value' => 7, 'tone' => 'portal-tone-blue'],
        ['label' => 'Meal Prep Rate', 'value' => '95%', 'tone' => 'portal-tone-orange'],
        ['label' => 'Avg Daily Calories', 'value' => number_format($activePlan?->daily_calories ?? 2050), 'tone' => 'portal-tone-purple'],
    ];

    $insightHighlights = $insights['insightHighlights'] ?? [
        ['title' => 'Great protein intake!', 'copy' => "You've consistently met your protein goals this week.", 'tone' => 'portal-tone-green'],
        ['title' => 'Low fiber intake', 'copy' => 'Consider adding more vegetables and whole grains to your meals.', 'tone' => 'portal-tone-yellow'],
        ['title' => 'Hydration reminder', 'copy' => 'Don\'t forget to drink 8 glasses of water daily.', 'tone' => 'portal-tone-blue'],
        ['title' => 'Calorie balance', 'copy' => "You're maintaining a healthy caloric intake pattern.", 'tone' => 'portal-tone-green'],
    ];

    $performanceCards = $insights['performanceCards'] ?? [
        ['title' => 'Average Calories', 'value' => number_format(array_sum($weeklyCalories) / count($weeklyCalories), 0), 'suffix' => '', 'target' => 'Target: 2000', 'progress' => 102.5],
        ['title' => 'Protein Goal Achievement', 'value' => '85', 'suffix' => '%', 'target' => 'Target: 100%', 'progress' => 85],
        ['title' => 'Meal Logging Consistency', 'value' => '6', 'suffix' => ' /7 days', 'target' => 'Target: 7/7 days', 'progress' => 86],
    ];

    $achievements = $insights['achievements'] ?? [
        ['title' => '7-Day Streak', 'copy' => 'Logged meals for 7 consecutive days', 'tone' => 'portal-tone-green', 'unlocked' => true],
        ['title' => 'Protein Master', 'copy' => 'Met protein goals for 5 days in a row', 'tone' => 'portal-tone-green', 'unlocked' => true],
        ['title' => 'Balanced Diet', 'copy' => 'Maintained balanced macros for a week', 'tone' => 'portal-tone-blue', 'unlocked' => false],
        ['title' => 'Hydration Hero', 'copy' => 'Drink 8 glasses of water for 3 days', 'tone' => 'portal-tone-cyan', 'unlocked' => false],
    ];

    $recommendations = $insights['personalizedRecommendations'] ?? [
        [
            'title' => 'Increase Fiber Intake',
            'priority' => 'high priority',
            'priority_tone' => 'portal-badge--red',
            'copy' => 'Add more vegetables, fruits, and whole grains to reach your daily fiber goal of 30g.',
            'tips' => ['Add berries to your breakfast', 'Include a salad with lunch and dinner', 'Snack on raw vegetables'],
        ],
        [
            'title' => 'Optimize Protein Distribution',
            'priority' => 'medium priority',
            'priority_tone' => 'portal-badge--yellow',
            'copy' => 'Distribute protein evenly across meals for better muscle synthesis.',
            'tips' => ['Aim for 25-30g protein per meal', 'Include protein in breakfast', 'Consider a post-workout protein source'],
        ],
        [
            'title' => 'Reduce Added Sugars',
            'priority' => 'medium priority',
            'priority_tone' => 'portal-badge--yellow',
            'copy' => 'Your sugar intake is slightly above recommendations. Try these swaps:',
            'tips' => ['Choose whole fruits over fruit juice', 'Use natural sweeteners like dates', 'Read nutrition labels carefully'],
        ],
    ];

    $mealPlanStats = [
        ['label' => 'Available Plans', 'value' => $portalData['mealPlans']->count(), 'tone' => 'portal-tone-blue'],
        ['label' => 'Average Rating', 'value' => number_format($portalData['mealPlans']->avg('rating') ?? 4.8, 1), 'tone' => 'portal-tone-green'],
        ['label' => 'Active Users', 'value' => '2.5k', 'tone' => 'portal-tone-purple'],
    ];

    $feedbackStats = [
        ['label' => 'New Messages', 'value' => $feedbackItems->where('is_read', false)->count(), 'tone' => 'portal-tone-blue'],
        ['label' => 'Total Consultations', 'value' => $portalData['consultationCount'], 'tone' => 'portal-tone-green'],
        ['label' => 'Goal Adherence', 'value' => '95%', 'tone' => 'portal-tone-purple'],
        ['label' => 'Weeks Active', 'value' => '8', 'tone' => 'portal-tone-orange'],
    ];

    $intakeCards = [
        ['label' => 'Calories', 'value' => '2000', 'tone' => 'portal-tone-orange'],
        ['label' => 'Protein', 'value' => '150g', 'tone' => 'portal-tone-green'],
        ['label' => 'Carbs', 'value' => '225g', 'tone' => 'portal-tone-yellow'],
        ['label' => 'Fat', 'value' => '67g', 'tone' => 'portal-tone-red'],
    ];

    $bodyMassRange = $profile['bmi'] < 18.5 ? 'Underweight' : ($profile['bmi'] < 25 ? 'Normal' : ($profile['bmi'] < 30 ? 'Overweight' : 'Obese'));
@endphp

@section('title', 'NutriAssist')
@section('body_class', 'portal-app')

@section('content')
<div class="app-particles-background">
    <div class="app-particles-background__layer app-particles-background__layer--one"></div>
    <div class="app-particles-background__layer app-particles-background__layer--two"></div>
    <div class="app-particles-background__layer app-particles-background__layer--three"></div>
</div>

<div class="portal-shell">
    <header class="portal-header">
        <div class="portal-header__inner">
            <a href="{{ route('portal.dashboard') }}" class="portal-brand">
                <span class="portal-brand__mark">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-2.4 0-4 1.5-4 3.6 0 .8.3 1.6.8 2.2C6 9.6 4 12 4 15.1 4 19 7.1 22 11 22c2.7 0 4.4-1.1 5.8-2.6 2.2-2.3 3.2-4.8 3.2-7.4 0-3.6-2.6-6.3-6-6.3-.4 0-.9 0-1.3.1.2-.4.3-.9.3-1.4C13 3.6 12.7 3 12 3Z"/></svg>
                </span>
                <span>
                    <strong>NutriAssist</strong>
                    <small>Nutritional Intelligence</small>
                </span>
            </a>

            <nav class="portal-nav">
                @foreach ($pages as $key => $item)
                    <a href="{{ route($item['route']) }}" class="portal-nav__link {{ $page === $key ? 'is-active' : '' }}">
                        <span>{{ $item['name'] }}</span>
                    </a>
                @endforeach
@if (auth()->check() && auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="portal-admin-link" style="color: #111827 !important; background: #ffffff; border-color: #ea580c;">
                        <span style="color: #000000 !important;">Admin</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="ml-4">
                    @csrf
                    <button type="submit" class="portal-button">Logout</button>
                </form>

            </nav>
        </div>
    </header>

    <main class="portal-content">
        <section class="portal-hero">
            @if ($page === 'dashboard')
                <h1>Dashboard</h1>
                <p>Track your nutritional intake and progress</p>
            @elseif ($page === 'food-log')
                <h1>Food Log</h1>
                <p>Track your daily meals and nutrition</p>
            @elseif ($page === 'calendar')
                <h1>Food Prep Calendar</h1>
                <p>Plan your meals ahead of time for consistent healthy eating</p>
            @elseif ($page === 'insights')
                <h1>Insights</h1>
                <p>AI-powered nutritional analysis and recommendations</p>
            @elseif ($page === 'meal-plans')
                <h1>Meal Plans</h1>
                <p>Pre-designed meal plans tailored to your nutritional goals</p>
            @elseif ($page === 'feedback')
                <h1>Dietitian Feedback</h1>
                <p>Personalized insights and recommendations from your nutrition expert</p>
            @else
                <h1>Profile &amp; Metrics</h1>
                <p>Manage your personal information and track your health metrics</p>
            @endif
        </section>

        @if (session('status'))
            <section class="portal-card portal-tone-green portal-grid portal-grid--tight">
                <strong>{{ session('status') }}</strong>
            </section>
        @endif

        @if ($page === 'dashboard')
            <section class="portal-grid portal-grid--four">
                @foreach ($dashboardMetrics as $metric)
                    @php
                        $progress = min(($metric['value'] / max($metric['goal'], 1)) * 100, 100);
                        $remaining = max($metric['goal'] - $metric['value'], 0);
                    @endphp
                    <article class="portal-card portal-stat-card">
                        <div class="portal-stat-card__top">
                            <h2>{{ $metric['label'] }}</h2>
                            <span class="portal-mini-icon {{ $metric['tone'] }}"></span>
                        </div>
                        <div class="portal-stat-card__value">
                            {{ $metric['value'] }} <span>/ {{ $metric['goal'] }}{{ $metric['suffix'] }}</span>
                        </div>
                        <div class="portal-progress"><span style="width: {{ $progress }}%"></span></div>
                        <p class="portal-stat-card__remaining {{ $metric['remaining_class'] }}">{{ $remaining }}{{ $metric['suffix'] }} remaining</p>
                    </article>
                @endforeach
            </section>

            <section class="portal-grid portal-grid--two">
                <article class="portal-card portal-chart-card">
                    <div class="portal-section-title">
                        <h2>Macro Distribution</h2>
                        <p>Today's macronutrient breakdown</p>
                    </div>
                    <div class="portal-pie-chart" style="display:grid;place-items:center;min-height:290px;">
                        <div style="width:138px;height:138px;border-radius:999px;background: {{ $pieStyle }};"></div>
                        <div class="portal-grid portal-grid--tight" style="grid-template-columns:repeat(3,minmax(0,1fr));margin-top:26px;text-align:center;">
                            <div><strong class="is-green">Protein: {{ $macroMap['Protein'] }}g</strong></div>
                            <div><strong class="is-orange">Carbs: {{ $macroMap['Carbs'] }}g</strong></div>
                            <div><strong class="is-red">Fat: {{ $macroMap['Fat'] }}g</strong></div>
                        </div>
                    </div>
                    <div class="portal-legend">
                        <span><i style="background:#59b985;"></i>Protein</span>
                        <span><i style="background:#f4aa34;"></i>Carbs</span>
                        <span><i style="background:#e1554d;"></i>Fat</span>
                    </div>
                </article>

                <article class="portal-card portal-chart-card">
                    <div class="portal-section-title">
                        <h2>Weekly Calorie Trend</h2>
                        <p>Your calorie intake over the past week</p>
                    </div>
                    <div class="portal-bar-chart">
                        <div class="portal-bar-chart__grid"></div>
                        <div class="portal-bar-chart__axis">
                            <span>2400</span>
                            <span>1800</span>
                            <span>1200</span>
                            <span>600</span>
                            <span>0</span>
                        </div>
                        @foreach ($weeklyCalories as $index => $bar)
                            <div class="portal-bar-chart__item">
                                <span class="portal-bar-chart__bar" style="height: {{ max(($bar / 2400) * 100, 12) }}%"></span>
                                <label>{{ $weeklyLabels[$index] }}</label>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Today's Meals</h2>
                    <p>Your logged meals for today</p>
                </div>
                <div class="portal-meal-list">
                    @foreach ($dashboardMeals as $entry)
                        <article class="portal-meal-item">
                            <div class="portal-meal-item__meta">
                                <span class="portal-tag">{{ strtoupper($entry->meal_slot) }}</span>
                                <strong>{{ $entry->food_name }}</strong>
                                <small>{{ $entry->serving_label }}</small>
                            </div>
                            <div class="portal-meal-item__nutrition">
                                <strong>{{ $entry->calories }} cal</strong>
                                <small>{{ $entry->protein }}g protein</small>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Micronutrients &amp; Vitamins</h2>
                    <p>Essential vitamins and minerals tracking</p>
                </div>
                <div class="portal-micro-grid">
                    @foreach ($micronutrients as $nutrient)
                        <article class="portal-micro-card {{ $nutrient['tone'] }}">
                            <h3>{{ $nutrient['label'] }}</h3>
                            <strong>{{ $nutrient['value'] }}</strong>
                            <p>{{ $nutrient['meta'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($page === 'food-log')
            <section class="portal-grid portal-grid--two">
                @foreach ($foodLogMeals as $mealName => $meal)
                    <article class="portal-card portal-log-card">
                        <div class="portal-log-card__header">
                            <div>
                                <h2>{{ $mealName }} <span>{{ $meal['count'] }}</span></h2>
                                <p>{{ $meal['summary'] }}</p>
                            </div>
                            <button type="button" class="portal-button" data-modal-open="food-picker" data-meal="{{ $mealName }}">Add</button>
                        </div>
                        <div class="portal-log-card__items">
                            @forelse ($meal['items'] as $item)
                                <article class="portal-log-entry">
                                    <div>
                                        <strong>{{ $item->food_name }}</strong>
                                        <p>{{ $item->serving_label }} • {{ $item->calories }} cal</p>
                                    </div>
                                    <form method="POST" action="{{ route('portal.food-log.destroy', $item) }}" style="display:inline;" onsubmit="return confirm('Delete this food item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="portal-button portal-button--danger" style="padding:4px 8px;font-size:12px;">Delete</button>
                                    </form>
                                </article>
                            @empty
                                <div class="portal-log-empty">No items logged yet</div>
                            @endforelse
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="portal-modal" data-modal="food-picker" hidden>
                <div class="portal-modal__backdrop" data-modal-close></div>
                <div class="portal-modal__dialog portal-modal__dialog--narrow">
                    <div class="portal-modal__header">
                        <div>
                            <h2>Add Food to breakfast</h2>
                            <p>Type the food name, grams, and calories for a quick manual entry.</p>
                        </div>
                        <button type="button" data-modal-close>&times;</button>
                    </div>

                    <form method="POST" action="{{ route('portal.food-log.store') }}" class="portal-bmi__inputs" style="margin-bottom:16px;">
                        @csrf
                        <input type="hidden" name="meal_slot" value="{{ $portalData['selectedMeal'] }}">
                        <label>
                            <span>Food</span>
                            <input type="text" name="food_name" placeholder="Chicken breast">
                        </label>
                        <label>
                            <span>Grams</span>
                            <input type="number" name="grams" min="1" placeholder="150">
                        </label>
                        <label>
                            <span>Calories</span>
                            <input type="number" name="calories" min="0" placeholder="248">
                        </label>
                        <div class="portal-form__actions" style="grid-column:1 / -1;">
                            <button type="submit" class="portal-button portal-button--primary">Add Food</button>
                        </div>
                    </form>

                    <div class="portal-section-title" style="margin-top:10px;">
                        <h2>Manual Entry</h2>
                        <p>Type the food name, grams, and calories for a quick manual entry.</p>
                    </div>
                </div>
            </section>
        @endif

        @if ($page === 'calendar')
            <section class="portal-grid portal-grid--four">
                @foreach ($calendarStats as $card)
                    <article class="portal-summary-card {{ $card['tone'] }}">
                        <strong>{{ $card['value'] }}</strong>
                        <p>{{ $card['label'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="portal-card">
                <div class="portal-calendar__header">
                    <div class="portal-section-title">
                        <h2>{{ $calendar['month_label'] }}</h2>
                        <p>Click on a day to plan meals</p>
                    </div>
                    <div class="portal-actions">
                        <a href="{{ route('portal.calendar', ['date' => $calendar['previous_month']]) }}" class="portal-button portal-button--icon">&lsaquo;</a>
                        <a href="{{ route('portal.calendar', ['date' => $calendar['next_month']]) }}" class="portal-button portal-button--icon">&rsaquo;</a>
                    </div>
                </div>
                <div class="portal-calendar__weekdays">
                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                        <span>{{ $weekday }}</span>
                    @endforeach
                </div>
                <div class="portal-calendar">
                    @foreach ($calendar['days'] as $day)
                        <button type="button" class="portal-calendar__day {{ $day['selected'] ? 'is-selected' : '' }} {{ $day['has_plan'] ? 'is-filled' : '' }}" data-modal-open="calendar-day-{{ $day['date'] }}">
                            <strong>{{ $day['label'] }}</strong>
                            <span class="portal-calendar__plus">+</span>
                            @if ($day['has_plan'])
                                <span class="portal-calendar__badge">{{ $day['count'] }}</span>
                            @endif
                            @if ($day['has_plan'])
                                <div class="portal-calendar__items">
                                    @foreach (['Breakfast' => 'B', 'Lunch' => 'L', 'Dinner' => 'D', 'Snacks' => 'S'] as $slot => $shortLabel)
                                        @if ($day['entries']->has($slot))
                                            <small>{{ $shortLabel }}: {{ $day['entries'][$slot]->food_name }}</small>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Upcoming Meals</h2>
                    <p>Your planned meals for the next few days</p>
                </div>
                <div class="portal-upcoming">
                    @foreach ($calendar['upcoming'] as $entry)
                        <article class="portal-upcoming__card">
                            <div class="portal-upcoming__card-header">
                                <h3>{{ $entry['label'] }}</h3>
                                <button type="button" class="portal-button" data-modal-open="calendar-day-{{ $entry['date'] }}">Edit</button>
                            </div>
                            <div class="portal-upcoming__meals">
                                @foreach ($mealSlots as $mealSlot)
                                    <div>
                                        <strong class="portal-upcoming__meal-label">{{ $mealSlot }}</strong>
                                        @foreach (($entry['meals'][$mealSlot] ?? []) as $item)
                                            <p>• {{ $item }}</p>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            @foreach ($calendar['days'] as $day)
                <section class="portal-modal" data-modal="calendar-day-{{ $day['date'] }}" hidden>
                    <div class="portal-modal__backdrop" data-modal-close></div>
                    <div class="portal-modal__dialog portal-modal__dialog--wide">
                        <div class="portal-modal__header">
                            <div>
                                <h2>Plan Meals for {{ \Illuminate\Support\Carbon::parse($day['date'])->format('F j, Y') }}</h2>
                                <p>Type each meal manually with food name, grams, and calories.</p>
                            </div>
                            <button type="button" data-modal-close>&times;</button>
                        </div>

                        <form method="POST" action="{{ route('portal.calendar.store') }}" class="portal-bmi__inputs">
                            @csrf
                            <input type="hidden" name="scheduled_date" value="{{ $day['date'] }}">
                            @foreach ($mealSlots as $mealSlot)
                                @php
                                    $plannedEntry = $day['entries']->get($mealSlot);
                                @endphp
                                <div class="portal-card" style="padding:14px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                        <strong>{{ $mealSlot }}</strong>
@if($plannedEntry?->exists)
                                        <button type="button" class="portal-button portal-button--danger" style="padding:4px 8px;font-size:12px;" onclick="deleteMeal({{ $plannedEntry->id }}, '{{ $mealSlot }}')">Delete meal</button>
                                        @endif
                                    </div>
                                    <div class="portal-form" style="grid-template-columns:1.3fr .8fr .8fr;">
                                        <label>
                                            <span>Food</span>
                                            <input type="text" name="entries[{{ $mealSlot }}][food_name]" value="{{ $plannedEntry?->food_name }}">
                                        </label>
                                        <label>
                                            <span>Grams</span>
                                            <input type="number" min="1" name="entries[{{ $mealSlot }}][grams]" value="{{ $plannedEntry?->grams }}">
                                        </label>
                                        <label>
                                            <span>Calories</span>
                                            <input type="number" min="0" name="entries[{{ $mealSlot }}][calories]" value="{{ $plannedEntry?->calories }}">
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            <div class="portal-actions--end">
                                <button type="submit" class="portal-button portal-button--primary">Save Day Plan</button>
                            </div>
                        </form>

                        @foreach ($mealSlots as $mealSlot)
                            @php
                                $plannedEntry = $day['entries']->get($mealSlot);
                            @endphp
                            @if($plannedEntry?->exists)
                            <form id="delete-form-{{ $plannedEntry->id }}" method="POST" action="{{ route('portal.calendar.destroy', $plannedEntry) }}" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif

        @if ($page === 'insights')
            <section class="portal-section-title--with-icon">
                <span class="portal-icon portal-icon--purple">✦</span>
                <h2>AI Insights</h2>
            </section>
            <section class="portal-grid portal-grid--two">
                @foreach ($insightHighlights as $highlight)
                    <article class="portal-insight-card {{ $highlight['tone'] }}">
                        <div>
                            <h3>{{ $highlight['title'] }}</h3>
                            <p>{{ $highlight['copy'] }}</p>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="portal-section-title">
                <h2>Weekly Performance</h2>
            </section>
            <section class="portal-grid portal-grid--three">
                @foreach ($performanceCards as $card)
                    <article class="portal-card portal-performance-card">
                        <h3>{{ $card['title'] }}</h3>
                        <strong>{{ $card['value'] }}<span>{{ $card['suffix'] }}</span></strong>
                        <div class="portal-progress"><span style="width: {{ $card['progress'] }}%"></span></div>
                        <div class="portal-performance-card__meta">
                            <span>{{ $card['target'] }}</span>
                            <b>{{ $card['progress'] }}%</b>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="portal-section-title">
                <h2>Achievements</h2>
            </section>
            <section class="portal-grid portal-grid--four">
                @foreach ($achievements as $achievement)
                    <article class="portal-achievement {{ $achievement['unlocked'] ? 'is-unlocked' : '' }}">
                        <div class="portal-achievement__icon {{ $achievement['tone'] }}"></div>
                        <h3>{{ $achievement['title'] }}</h3>
                        <p>{{ $achievement['copy'] }}</p>
                        @if ($achievement['unlocked'])
                            <span class="portal-badge portal-badge--dark" style="margin-top:12px;">Unlocked</span>
                        @endif
                    </article>
                @endforeach
            </section>

            <section class="portal-section-title">
                <h2>Personalized Recommendations</h2>
            </section>
            <section>
                @foreach ($recommendations as $recommendation)
                    <article class="portal-card portal-recommendation">
                        <div class="portal-actions">
                            <h3>{{ $recommendation['title'] }}</h3>
                            <span class="portal-badge {{ $recommendation['priority_tone'] }}">{{ $recommendation['priority'] }}</span>
                        </div>
                        <p>{{ $recommendation['copy'] }}</p>
                        <div class="portal-recommendation__tips">
                            <strong>Quick Tips:</strong>
                            @foreach ($recommendation['tips'] as $tip)
                                <span>{{ $tip }}</span>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($page === 'meal-plans')
            <section class="portal-grid portal-grid--three">
                @foreach ($mealPlanStats as $card)
                    <article class="portal-summary-card {{ $card['tone'] }}">
                        <strong>{{ $card['value'] }}</strong>
                        <p>{{ $card['label'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="portal-grid portal-grid--two">
                @foreach ($portalData['mealPlans'] as $plan)
                    <article class="portal-card portal-plan">
                        <div class="portal-plan__hero">
                            <div>
                                <h2>{{ $plan->name }}</h2>
                                <p>{{ $plan->description }}</p>
                                <div class="portal-plan__tags">
                                    @foreach ($plan->tags ?? [] as $tag)
                                        <span class="portal-tag">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <span class="portal-rating">★ {{ number_format($plan->rating, 1) }}</span>
                        </div>
                        <div class="portal-plan__body">
                            <div class="portal-plan__summary">
                                <span>Daily Calories</span>
                                <b>{{ $plan->daily_calories }} cal</b>
                            </div>
                            @foreach ($mealSlots as $mealSlot)
                                @php
                                    $items = $plan->items->where('meal_slot', $mealSlot);
                                @endphp
                                @if ($items->isNotEmpty())
                                    <div class="portal-plan__meal">
                                        <strong>{{ $mealSlot }}</strong>
                                        @foreach ($items as $item)
                                            <p>• {{ $item->item_name }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                            <form method="POST" action="{{ route('portal.meal-plans.activate', $plan) }}">
                                @csrf
                                <button type="submit" class="portal-button portal-button--primary" style="width:100%;margin-top:8px;">Use This Plan</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="portal-card portal-cta">
                <div>
                    <h2>Need a Custom Plan?</h2>
                    <p>Our AI can create a personalized meal plan based on your specific goals, dietary restrictions, and preferences.</p>
                </div>
                <a href="#custom-plan-builder" class="portal-button portal-button--primary">Create Custom Plan</a>
            </section>

            <section class="portal-card" id="custom-plan-builder">
                <div class="portal-section-title">
                    <h2>{{ $editablePlan ? 'Customize Your Plan' : 'Create a Custom Plan' }}</h2>
                    <p>Build a plan, rename it, adjust calories, and change every meal item.</p>
                </div>
                <form method="POST" action="{{ $editablePlan ? route('portal.meal-plans.update', $editablePlan) : route('portal.meal-plans.store') }}" class="portal-form">
                    @csrf
                    @if ($editablePlan)
                        @method('PUT')
                    @endif
                    <label>
                        <span>Plan Name</span>
                        <input type="text" name="name" value="{{ $editablePlan?->name }}" required>
                    </label>
                    <label>
                        <span>Daily Calories</span>
                        <input type="number" name="daily_calories" value="{{ $editablePlan?->daily_calories ?? 2000 }}" required>
                    </label>
                    <label>
                        <span>Tags</span>
                        <input type="text" name="tags" value="{{ $editablePlan ? implode(', ', $editablePlan->tags ?? []) : '' }}">
                    </label>
                    <label style="grid-column:1 / -1;">
                        <span>Description</span>
                        <input type="text" name="description" value="{{ $editablePlan?->description }}" required>
                    </label>
                    <div class="portal-form__actions">
                        <button type="submit" class="portal-button portal-button--primary">{{ $editablePlan ? 'Save Plan' : 'Create Plan' }}</button>
                    </div>
                </form>
                @if ($editablePlan)
                    <form method="POST" action="{{ route('portal.meal-plans.activate', $editablePlan) }}" style="margin-top:12px;">
                        @csrf
                        <button type="submit" class="portal-button">Make Active</button>
                    </form>
                @endif
            </section>

            @if ($editablePlan)
                <section class="portal-grid portal-grid--two">
                    @foreach ($mealSlots as $mealSlot)
                        <article class="portal-card">
                            <div class="portal-section-title">
                                <h2>{{ $mealSlot }}</h2>
                                <p>Swap items or add new ones for this meal.</p>
                            </div>
                            <div class="portal-log-card__items">
                                @forelse ($editablePlan->items->where('meal_slot', $mealSlot) as $item)
                                    <article class="portal-log-entry">
                                        <strong>{{ $item->item_name }}</strong>
                                        <p>{{ $item->serving_label }}</p>
                                        <form method="POST" action="{{ route('portal.meal-plans.items.destroy', [$editablePlan, $item]) }}" style="margin-top:10px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="portal-button">Remove</button>
                                        </form>
                                    </article>
                                @empty
                                    <div class="portal-log-empty">No items yet</div>
                                @endforelse
                            </div>
                            <form method="POST" action="{{ route('portal.meal-plans.items.store', $editablePlan) }}" class="portal-form" style="margin-top:18px;grid-template-columns:1fr 1fr;">
                                @csrf
                                <input type="hidden" name="meal_slot" value="{{ $mealSlot }}">
                                <label>
                                    <span>Food</span>
                                    <input type="text" name="item_name" placeholder="Enter food name" required>
                                </label>
                                <label>
                                    <span>Serving label</span>
                                    <input type="text" name="serving_label" value="1 serving" required>
                                </label>
                                <div class="portal-form__actions">
                                    <button type="submit" class="portal-button portal-button--primary">Add to {{ $mealSlot }}</button>
                                </div>
                            </form>
                        </article>
                    @endforeach
                </section>
            @endif
        @endif

        @if ($page === 'feedback')
            <section class="portal-grid portal-grid--four">
                @foreach ($feedbackStats as $card)
                    <article class="portal-summary-card {{ $card['tone'] }}">
                        <strong>{{ $card['value'] }}</strong>
                        <p>{{ $card['label'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="portal-card portal-dietitian">
                <div class="portal-section-title">
                    <h2>Your Assigned Dietitian</h2>
                    <p>Your personal nutrition expert</p>
                </div>
                <div class="portal-dietitian__row">
                    <span class="portal-avatar">{{ Str::of($dietitian?->name ?? 'NA')->explode(' ')->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->take(2)->implode('') }}</span>
                    <div>
                        <h3>{{ $dietitian?->name ?? 'No dietitian assigned' }}</h3>
                        <p>{{ $dietitian?->specialization ?? 'Registered Dietitian-Nutritionist' }}</p>
                        <p>Specializes in weight management, sports nutrition, and metabolic health.</p>
                        <div class="portal-actions" style="margin-top:18px;">
                            <button type="button" class="portal-button portal-button--primary" data-modal-open="consultation-request">Schedule Consultation</button>
                            <button type="button" class="portal-button" data-modal-open="feedback-message">Send Message</button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="portal-section-title">
                <h2>Recent Feedback</h2>
            </section>
            <section>
                @foreach ($feedbackItems as $item)
                    <article class="portal-card portal-feedback-card {{ $loop->first ? 'is-highlighted' : '' }}">
                        <div class="portal-feedback-card__meta">
                            <span class="portal-badge portal-badge--blue">{{ $item->tag }}</span>
                            <span class="portal-badge {{ $item->status === 'completed' ? 'portal-badge--green' : 'portal-badge--slate' }}">{{ $item->status }}</span>
                        </div>
                        <h3>{{ $item->title }}</h3>
                        <strong>From {{ $item->dietitian?->name ?? 'Dietitian' }} • {{ $item->submitted_on->format('F j, Y') }}</strong>
                        <p>{{ $item->message }}</p>
                        @if (($item->recommendations ?? []) !== [])
                            <div class="portal-feedback-card__recommendations">
                                <h4>Recommendations</h4>
                                @foreach ($item->recommendations as $recommendation)
                                    <span>{{ $recommendation }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div class="portal-actions" style="margin-top:18px;">
                            @if (! $item->is_read)
                                <form method="POST" action="{{ route('portal.feedback.read', $item) }}">
                                    @csrf
                                    <button type="submit" class="portal-button">Mark as Read</button>
                                </form>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('portal.feedback.reply') }}" class="portal-bmi__inputs" style="margin-top:18px;">
                            @csrf
                            <input type="hidden" name="title" value="Reply: {{ $item->title }}">
                            <input type="hidden" name="dietitian_id" value="{{ $item->dietitian_id ?? $dietitian?->id }}">
                            <label style="grid-column: 1 / -1;">
                                <span>Reply to {{ $item->dietitian?->name ?? ($dietitian?->name ?? 'your dietitian') }}</span>
                                <textarea name="message" rows="3" placeholder="Type your reply here..." required></textarea>
                            </label>
                            <button type="submit" class="portal-button portal-button--primary">Send Reply</button>
                        </form>
                    </article>
                @endforeach
            </section>

            <section class="portal-modal" data-modal="consultation-request" hidden>
                <div class="portal-modal__backdrop" data-modal-close></div>
                <div class="portal-modal__dialog">
                    <div class="portal-modal__header">
                        <div>
                            <h2>Schedule Consultation</h2>
                            <p>Request time with your dietitian.</p>
                        </div>
                        <button type="button" data-modal-close>&times;</button>
                    </div>
                    <form method="POST" action="{{ route('portal.consultations.store') }}" class="portal-bmi__inputs">
                        @csrf
                        <label>
                            <span>Preferred date</span>
                            <input type="date" name="preferred_date">
                        </label>
                        <label>
                            <span>Note</span>
                            <input type="text" name="note" placeholder="What would you like to discuss?">
                        </label>
                        <button type="submit" class="portal-button portal-button--primary">Submit Request</button>
                    </form>
                </div>
            </section>

            <section class="portal-modal" data-modal="feedback-message" hidden>
                <div class="portal-modal__backdrop" data-modal-close></div>
                <div class="portal-modal__dialog">
                    <div class="portal-modal__header">
                        <div>
                            <h2>Send Message</h2>
                            <p>Share a question or update for your dietitian.</p>
                        </div>
                        <button type="button" data-modal-close>&times;</button>
                    </div>
                    <form method="POST" action="{{ route('portal.feedback.reply') }}" class="portal-bmi__inputs">
                        @csrf
                        <input type="hidden" name="dietitian_id" value="{{ $dietitian?->id }}">
                        <label>
                            <span>Title</span>
                            <input type="text" name="title" placeholder="Message title">
                        </label>
                        <label style="grid-column: 1 / -1;">
                            <span>Message</span>
                            <textarea name="message" rows="4" placeholder="Type your message"></textarea>
                        </label>
                        <button type="submit" class="portal-button portal-button--primary">Send Message</button>
                    </form>
                </div>
            </section>
        @endif

        @if ($page === 'profile')
            <section class="portal-grid portal-grid--profile">
                <article class="portal-card">
                    <div class="portal-section-title">
                        <h2>BMI Calculator</h2>
                        <p>Track your Body Mass Index and health status</p>
                    </div>
                    <div class="portal-bmi" data-profile-form>
                        <div>
                            <div class="portal-bmi__score">
                                <strong data-bmi-score>{{ $profile['bmi'] }}</strong>
                                <span data-bmi-status>{{ $bodyMassRange }}</span>
                                <small>Body Mass Index</small>
                            </div>
                            <div class="portal-bmi-scale">
                                <span>&lt;18.5</span>
                                <span>18.5-24.9</span>
                                <span>25-29.9</span>
                                <span>&ge;30</span>
                            </div>
                        </div>
                        <div class="portal-bmi__inputs">
                            <label>
                                <span>Height (cm)</span>
                                <input type="number" value="{{ $profile['height_cm'] ?? 0 }}" data-profile-height>
                            </label>
                            <label>
                                <span>Current Weight (kg)</span>
                                <input type="number" step="0.1" value="{{ $profile['current_weight_kg'] ?? 0 }}" data-profile-weight>
                            </label>
                            <label>
                                <span>Target Weight (kg)</span>
                                <input type="number" step="0.1" value="{{ $profile['target_weight_kg'] ?? 0 }}" data-profile-target>
                            </label>
                        </div>
                    </div>
                    <div class="portal-list-card">
                        <h3>BMI Progress</h3>
                        @foreach ($profile['bmi_history'] as $history)
                            <div class="portal-list-card__row">
                                <div>
                                    <strong>{{ $history['date'] }}</strong>
                                    <span>{{ $history['weight'] }}</span>
                                </div>
                                <div style="text-align:right;">
                                    <strong>{{ $history['bmi'] }}</strong>
                                    <span>BMI</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="portal-card">
                    <div class="portal-section-title">
                        <h2>Goal Progress</h2>
                    </div>
                    <div class="portal-goal-card__headline">
                        <strong data-profile-current>{{ $profile['current_weight_kg'] }} kg</strong>
                        <span>Current Weight</span>
                    </div>
                    <div class="portal-goal-card__bar-meta">
                        <span>Progress to goal</span>
                        <strong data-profile-remaining>{{ number_format($profile['remaining_weight_kg'], 1) }} kg remaining</strong>
                    </div>
                    @php
                        $startingWeight = (float) $profile['starting_weight_kg'];
                        $currentWeight = (float) $profile['current_weight_kg'];
                        $targetWeight = (float) $profile['target_weight_kg'];
                        $achieved = max(0, $startingWeight - $currentWeight);
                        $goalTotal = max(1, $startingWeight - $targetWeight);
                        $progressPercent = min(100, $goalTotal > 0 ? ($achieved / $goalTotal) * 100 : 0);
                    @endphp
                    <div class="portal-progress"><span style="width: {{ $progressPercent }}%"></span></div>
                    <div class="portal-goal-card__status">
                        <strong>On track!</strong>
                        <span>You've lost {{ number_format($achieved, 1) }} kg so far</span>
                    </div>
                    <dl class="portal-goal-card__stats">
                        <div><dt>Starting</dt><dd>{{ $profile['starting_weight_kg'] }} kg</dd></div>
                        <div><dt>Current</dt><dd data-profile-current-inline>{{ $profile['current_weight_kg'] }} kg</dd></div>
                        <div><dt>Target</dt><dd class="is-green" data-profile-target-inline>{{ $profile['target_weight_kg'] }} kg</dd></div>
                    </dl>
                </article>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Personal Information</h2>
                    <p>Update your profile and health goals</p>
                </div>

                @if (session('status'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
                        ✓ {{ session('status') }}
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                    <!-- Edit Form -->
                    <div>
                        <h3 style="font-size: 0.95rem; color: #666; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px;">Update Information</h3>
                        <form method="POST" action="{{ route('portal.profile.update') }}" class="portal-form">
                            @csrf
                            <label>
                                <span>Full Name</span>
                                <input type="text" name="full_name" placeholder="Enter your name" value="">
                            </label>
                            <label>
                                <span>Age</span>
                                <input type="number" name="age" placeholder="Enter your age" value="">
                            </label>
                            <label>
                                <span>Gender</span>
                                <select name="gender">
                                    <option value="">None</option>
                                    @foreach (['Male', 'Female', 'Non-binary'] as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                <span>Activity Level</span>
                                <select name="activity_level">
                                    <option value="">None</option>
                                    @foreach (['Lightly Active', 'Moderately Active', 'Very Active'] as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                <span>Primary Goal</span>
                                <select name="primary_goal">
                                    <option value="">None</option>
                                    @foreach (['Weight Loss', 'Weight Maintenance', 'Muscle Gain'] as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                <span>Height (cm)</span>
                                <input type="number" name="height_cm" placeholder="e.g., 175" value="">
                            </label>
                            <label>
                                <span>Current Weight (kg)</span>
                                <input type="number" step="0.1" name="current_weight_kg" placeholder="e.g., 72.5" value="">
                            </label>
                            <label>
                                <span>Starting Weight (kg)</span>
                                <input type="number" step="0.1" name="starting_weight_kg" placeholder="e.g., 80.0" value="">
                            </label>
                            <label>
                                <span>Target Weight (kg)</span>
                                <input type="number" step="0.1" name="target_weight_kg" placeholder="e.g., 70.0" value="">
                            </label>
                            <div class="portal-form__actions">
                                <button type="submit" class="portal-button portal-button--primary">Save Changes</button>
                            </div>
                        </form>
                    </div>

                    <!-- Tracked Data Display -->
                    <div>
                        <h3 style="font-size: 0.95rem; color: #666; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px;">Tracked Profile Data</h3>
                        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; border-left: 4px solid #007bff;">
                            <dl style="margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Full Name</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['full_name'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Age</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['age'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Gender</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['gender'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Activity Level</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['activity_level'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Primary Goal</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['primary_goal'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Height</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['height_cm'] ?: '—' }} cm</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Current Weight</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['current_weight_kg'] ?: '—' }} kg</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Starting Weight</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['starting_weight_kg'] ?: '—' }} kg</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Target Weight</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['target_weight_kg'] ?: '—' }} kg</dd>
                                </div>
                                @if ($profile['bmi'] > 0)
                                    <div>
                                        <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">BMI</dt>
                                        <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;">{{ $profile['bmi'] }} ({{ $profile['status'] }})</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Recommended Daily Intake</h2>
                    <p>Based on your profile and goals</p>
                </div>
                <div class="portal-grid portal-grid--four portal-grid--grow">
                    @foreach ($intakeCards as $card)
                        <article class="portal-summary-card {{ $card['tone'] }}">
                            <strong>{{ $card['value'] }}</strong>
                            <p>{{ $card['label'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>
</div>
@endsection
