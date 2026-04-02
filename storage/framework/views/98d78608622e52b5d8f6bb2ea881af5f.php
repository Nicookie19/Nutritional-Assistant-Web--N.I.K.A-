<?php
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
?>

<?php $__env->startSection('title', 'NutriAssist'); ?>
<?php $__env->startSection('body_class', 'portal-app'); ?>

<?php $__env->startSection('content'); ?>
<div class="app-particles-background">
    <div class="app-particles-background__layer app-particles-background__layer--one"></div>
    <div class="app-particles-background__layer app-particles-background__layer--two"></div>
    <div class="app-particles-background__layer app-particles-background__layer--three"></div>
</div>

<div class="portal-shell">
    <header class="portal-header">
        <div class="portal-header__inner">
            <a href="<?php echo e(route('portal.dashboard')); ?>" class="portal-brand">
                <span class="portal-brand__mark">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-2.4 0-4 1.5-4 3.6 0 .8.3 1.6.8 2.2C6 9.6 4 12 4 15.1 4 19 7.1 22 11 22c2.7 0 4.4-1.1 5.8-2.6 2.2-2.3 3.2-4.8 3.2-7.4 0-3.6-2.6-6.3-6-6.3-.4 0-.9 0-1.3.1.2-.4.3-.9.3-1.4C13 3.6 12.7 3 12 3Z"/></svg>
                </span>
                <span>
                    <strong>NutriAssist</strong>
                    <small>Nutritional Intelligence</small>
                </span>
            </a>

            <nav class="portal-nav">
                <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route($item['route'])); ?>" class="portal-nav__link <?php echo e($page === $key ? 'is-active' : ''); ?>">
                        <span><?php echo e($item['name']); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php if(auth()->check() && auth()->user()->is_admin): ?>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="portal-admin-link">Admin</a>
                <?php endif; ?>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="ml-4">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="portal-button">Logout</button>
                </form>

            </nav>
        </div>
    </header>

    <main class="portal-content">
        <section class="portal-hero">
            <?php if($page === 'dashboard'): ?>
                <h1>Dashboard</h1>
                <p>Track your nutritional intake and progress</p>
            <?php elseif($page === 'food-log'): ?>
                <h1>Food Log</h1>
                <p>Track your daily meals and nutrition</p>
            <?php elseif($page === 'calendar'): ?>
                <h1>Food Prep Calendar</h1>
                <p>Plan your meals ahead of time for consistent healthy eating</p>
            <?php elseif($page === 'insights'): ?>
                <h1>Insights</h1>
                <p>AI-powered nutritional analysis and recommendations</p>
            <?php elseif($page === 'meal-plans'): ?>
                <h1>Meal Plans</h1>
                <p>Pre-designed meal plans tailored to your nutritional goals</p>
            <?php elseif($page === 'feedback'): ?>
                <h1>Dietitian Feedback</h1>
                <p>Personalized insights and recommendations from your nutrition expert</p>
            <?php else: ?>
                <h1>Profile &amp; Metrics</h1>
                <p>Manage your personal information and track your health metrics</p>
            <?php endif; ?>
        </section>

        <?php if(session('status')): ?>
            <section class="portal-card portal-tone-green portal-grid portal-grid--tight">
                <strong><?php echo e(session('status')); ?></strong>
            </section>
        <?php endif; ?>

        <?php if($page === 'dashboard'): ?>
            <section class="portal-grid portal-grid--four">
                <?php $__currentLoopData = $dashboardMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $progress = min(($metric['value'] / max($metric['goal'], 1)) * 100, 100);
                        $remaining = max($metric['goal'] - $metric['value'], 0);
                    ?>
                    <article class="portal-card portal-stat-card">
                        <div class="portal-stat-card__top">
                            <h2><?php echo e($metric['label']); ?></h2>
                            <span class="portal-mini-icon <?php echo e($metric['tone']); ?>"></span>
                        </div>
                        <div class="portal-stat-card__value">
                            <?php echo e($metric['value']); ?> <span>/ <?php echo e($metric['goal']); ?><?php echo e($metric['suffix']); ?></span>
                        </div>
                        <div class="portal-progress"><span style="width: <?php echo e($progress); ?>%"></span></div>
                        <p class="portal-stat-card__remaining <?php echo e($metric['remaining_class']); ?>"><?php echo e($remaining); ?><?php echo e($metric['suffix']); ?> remaining</p>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>

            <section class="portal-grid portal-grid--two">
                <article class="portal-card portal-chart-card">
                    <div class="portal-section-title">
                        <h2>Macro Distribution</h2>
                        <p>Today's macronutrient breakdown</p>
                    </div>
                    <div class="portal-pie-chart" style="display:grid;place-items:center;min-height:290px;">
                        <div style="width:138px;height:138px;border-radius:999px;background: <?php echo e($pieStyle); ?>;"></div>
                        <div class="portal-grid portal-grid--tight" style="grid-template-columns:repeat(3,minmax(0,1fr));margin-top:26px;text-align:center;">
                            <div><strong class="is-green">Protein: <?php echo e($macroMap['Protein']); ?>g</strong></div>
                            <div><strong class="is-orange">Carbs: <?php echo e($macroMap['Carbs']); ?>g</strong></div>
                            <div><strong class="is-red">Fat: <?php echo e($macroMap['Fat']); ?>g</strong></div>
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
                        <?php $__currentLoopData = $weeklyCalories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $bar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="portal-bar-chart__item">
                                <span class="portal-bar-chart__bar" style="height: <?php echo e(max(($bar / 2400) * 100, 12)); ?>%"></span>
                                <label><?php echo e($weeklyLabels[$index]); ?></label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </article>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Today's Meals</h2>
                    <p>Your logged meals for today</p>
                </div>
                <div class="portal-meal-list">
                    <?php $__currentLoopData = $dashboardMeals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="portal-meal-item">
                            <div class="portal-meal-item__meta">
                                <span class="portal-tag"><?php echo e(strtoupper($entry->meal_slot)); ?></span>
                                <strong><?php echo e($entry->food_name); ?></strong>
                                <small><?php echo e($entry->serving_label); ?></small>
                            </div>
                            <div class="portal-meal-item__nutrition">
                                <strong><?php echo e($entry->calories); ?> cal</strong>
                                <small><?php echo e($entry->protein); ?>g protein</small>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Micronutrients &amp; Vitamins</h2>
                    <p>Essential vitamins and minerals tracking</p>
                </div>
                <div class="portal-micro-grid">
                    <?php $__currentLoopData = $micronutrients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nutrient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="portal-micro-card <?php echo e($nutrient['tone']); ?>">
                            <h3><?php echo e($nutrient['label']); ?></h3>
                            <strong><?php echo e($nutrient['value']); ?></strong>
                            <p><?php echo e($nutrient['meta']); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if($page === 'food-log'): ?>
            <section class="portal-grid portal-grid--two">
                <?php $__currentLoopData = $foodLogMeals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mealName => $meal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-card portal-log-card">
                        <div class="portal-log-card__header">
                            <div>
                                <h2><?php echo e($mealName); ?> <span><?php echo e($meal['count']); ?></span></h2>
                                <p><?php echo e($meal['summary']); ?></p>
                            </div>
                            <button type="button" class="portal-button" data-modal-open="food-picker" data-meal="<?php echo e($mealName); ?>">Add</button>
                        </div>
                        <div class="portal-log-card__items">
                            <?php $__empty_1 = true; $__currentLoopData = $meal['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <article class="portal-log-entry">
                                    <div>
                                        <strong><?php echo e($item->food_name); ?></strong>
                                        <p><?php echo e($item->serving_label); ?> • <?php echo e($item->calories); ?> cal</p>
                                    </div>
                                    <form method="POST" action="<?php echo e(route('portal.food-log.destroy', $item)); ?>" style="display:inline;" onsubmit="return confirm('Delete this food item?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="portal-button portal-button--danger" style="padding:4px 8px;font-size:12px;">Delete</button>
                                    </form>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="portal-log-empty">No items logged yet</div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

                    <form method="POST" action="<?php echo e(route('portal.food-log.store')); ?>" class="portal-bmi__inputs" style="margin-bottom:16px;">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="meal_slot" value="<?php echo e($portalData['selectedMeal']); ?>">
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
        <?php endif; ?>

        <?php if($page === 'calendar'): ?>
            <section class="portal-grid portal-grid--four">
                <?php $__currentLoopData = $calendarStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-summary-card <?php echo e($card['tone']); ?>">
                        <strong><?php echo e($card['value']); ?></strong>
                        <p><?php echo e($card['label']); ?></p>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>

            <section class="portal-card">
                <div class="portal-calendar__header">
                    <div class="portal-section-title">
                        <h2><?php echo e($calendar['month_label']); ?></h2>
                        <p>Click on a day to plan meals</p>
                    </div>
                    <div class="portal-actions">
                        <a href="<?php echo e(route('portal.calendar', ['date' => $calendar['previous_month']])); ?>" class="portal-button portal-button--icon">&lsaquo;</a>
                        <a href="<?php echo e(route('portal.calendar', ['date' => $calendar['next_month']])); ?>" class="portal-button portal-button--icon">&rsaquo;</a>
                    </div>
                </div>
                <div class="portal-calendar__weekdays">
                    <?php $__currentLoopData = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span><?php echo e($weekday); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="portal-calendar">
                    <?php $__currentLoopData = $calendar['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="portal-calendar__day <?php echo e($day['selected'] ? 'is-selected' : ''); ?> <?php echo e($day['has_plan'] ? 'is-filled' : ''); ?>" data-modal-open="calendar-day-<?php echo e($day['date']); ?>">
                            <strong><?php echo e($day['label']); ?></strong>
                            <span class="portal-calendar__plus">+</span>
                            <?php if($day['has_plan']): ?>
                                <span class="portal-calendar__badge"><?php echo e($day['count']); ?></span>
                            <?php endif; ?>
                            <?php if($day['has_plan']): ?>
                                <div class="portal-calendar__items">
                                    <?php $__currentLoopData = ['Breakfast' => 'B', 'Lunch' => 'L', 'Dinner' => 'D', 'Snacks' => 'S']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot => $shortLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($day['entries']->has($slot)): ?>
                                            <small><?php echo e($shortLabel); ?>: <?php echo e($day['entries'][$slot]->food_name); ?></small>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Upcoming Meals</h2>
                    <p>Your planned meals for the next few days</p>
                </div>
                <div class="portal-upcoming">
                    <?php $__currentLoopData = $calendar['upcoming']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="portal-upcoming__card">
                            <div class="portal-upcoming__card-header">
                                <h3><?php echo e($entry['label']); ?></h3>
                                <button type="button" class="portal-button" data-modal-open="calendar-day-<?php echo e($entry['date']); ?>">Edit</button>
                            </div>
                            <div class="portal-upcoming__meals">
                                <?php $__currentLoopData = $mealSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mealSlot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div>
                                        <strong class="portal-upcoming__meal-label"><?php echo e($mealSlot); ?></strong>
                                        <?php $__currentLoopData = ($entry['meals'][$mealSlot] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p>• <?php echo e($item); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            <?php $__currentLoopData = $calendar['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <section class="portal-modal" data-modal="calendar-day-<?php echo e($day['date']); ?>" hidden>
                    <div class="portal-modal__backdrop" data-modal-close></div>
                    <div class="portal-modal__dialog portal-modal__dialog--wide">
                        <div class="portal-modal__header">
                            <div>
                                <h2>Plan Meals for <?php echo e(\Illuminate\Support\Carbon::parse($day['date'])->format('F j, Y')); ?></h2>
                                <p>Type each meal manually with food name, grams, and calories.</p>
                            </div>
                            <button type="button" data-modal-close>&times;</button>
                        </div>

                        <form method="POST" action="<?php echo e(route('portal.calendar.store')); ?>" class="portal-bmi__inputs">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="scheduled_date" value="<?php echo e($day['date']); ?>">
                            <?php $__currentLoopData = $mealSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mealSlot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $plannedEntry = $day['entries']->get($mealSlot);
                                ?>
                                <div class="portal-card" style="padding:14px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                        <strong><?php echo e($mealSlot); ?></strong>
<?php if($plannedEntry?->exists): ?>
                                        <button type="button" class="portal-button portal-button--danger" style="padding:4px 8px;font-size:12px;" onclick="deleteMeal(<?php echo e($plannedEntry->id); ?>, '<?php echo e($mealSlot); ?>')">Delete meal</button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="portal-form" style="grid-template-columns:1.3fr .8fr .8fr;">
                                        <label>
                                            <span>Food</span>
                                            <input type="text" name="entries[<?php echo e($mealSlot); ?>][food_name]" value="<?php echo e($plannedEntry?->food_name); ?>">
                                        </label>
                                        <label>
                                            <span>Grams</span>
                                            <input type="number" min="1" name="entries[<?php echo e($mealSlot); ?>][grams]" value="<?php echo e($plannedEntry?->grams); ?>">
                                        </label>
                                        <label>
                                            <span>Calories</span>
                                            <input type="number" min="0" name="entries[<?php echo e($mealSlot); ?>][calories]" value="<?php echo e($plannedEntry?->calories); ?>">
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="portal-actions--end">
                                <button type="submit" class="portal-button portal-button--primary">Save Day Plan</button>
                            </div>
                        </form>

                        <?php $__currentLoopData = $mealSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mealSlot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $plannedEntry = $day['entries']->get($mealSlot);
                            ?>
                            <?php if($plannedEntry?->exists): ?>
                            <form id="delete-form-<?php echo e($plannedEntry->id); ?>" method="POST" action="<?php echo e(route('portal.calendar.destroy', $plannedEntry)); ?>" style="display:none;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                            </form>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        <?php if($page === 'insights'): ?>
            <section class="portal-section-title--with-icon">
                <span class="portal-icon portal-icon--purple">✦</span>
                <h2>AI Insights</h2>
            </section>
            <section class="portal-grid portal-grid--two">
                <?php $__currentLoopData = $insightHighlights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $highlight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-insight-card <?php echo e($highlight['tone']); ?>">
                        <div>
                            <h3><?php echo e($highlight['title']); ?></h3>
                            <p><?php echo e($highlight['copy']); ?></p>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>

            <section class="portal-section-title">
                <h2>Weekly Performance</h2>
            </section>
            <section class="portal-grid portal-grid--three">
                <?php $__currentLoopData = $performanceCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-card portal-performance-card">
                        <h3><?php echo e($card['title']); ?></h3>
                        <strong><?php echo e($card['value']); ?><span><?php echo e($card['suffix']); ?></span></strong>
                        <div class="portal-progress"><span style="width: <?php echo e($card['progress']); ?>%"></span></div>
                        <div class="portal-performance-card__meta">
                            <span><?php echo e($card['target']); ?></span>
                            <b><?php echo e($card['progress']); ?>%</b>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>

            <section class="portal-section-title">
                <h2>Achievements</h2>
            </section>
            <section class="portal-grid portal-grid--four">
                <?php $__currentLoopData = $achievements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-achievement <?php echo e($achievement['unlocked'] ? 'is-unlocked' : ''); ?>">
                        <div class="portal-achievement__icon <?php echo e($achievement['tone']); ?>"></div>
                        <h3><?php echo e($achievement['title']); ?></h3>
                        <p><?php echo e($achievement['copy']); ?></p>
                        <?php if($achievement['unlocked']): ?>
                            <span class="portal-badge portal-badge--dark" style="margin-top:12px;">Unlocked</span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>

            <section class="portal-section-title">
                <h2>Personalized Recommendations</h2>
            </section>
            <section>
                <?php $__currentLoopData = $recommendations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recommendation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-card portal-recommendation">
                        <div class="portal-actions">
                            <h3><?php echo e($recommendation['title']); ?></h3>
                            <span class="portal-badge <?php echo e($recommendation['priority_tone']); ?>"><?php echo e($recommendation['priority']); ?></span>
                        </div>
                        <p><?php echo e($recommendation['copy']); ?></p>
                        <div class="portal-recommendation__tips">
                            <strong>Quick Tips:</strong>
                            <?php $__currentLoopData = $recommendation['tips']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span><?php echo e($tip); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>
        <?php endif; ?>

        <?php if($page === 'meal-plans'): ?>
            <section class="portal-grid portal-grid--three">
                <?php $__currentLoopData = $mealPlanStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-summary-card <?php echo e($card['tone']); ?>">
                        <strong><?php echo e($card['value']); ?></strong>
                        <p><?php echo e($card['label']); ?></p>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>

            <section class="portal-grid portal-grid--two">
                <?php $__currentLoopData = $portalData['mealPlans']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-card portal-plan">
                        <div class="portal-plan__hero">
                            <div>
                                <h2><?php echo e($plan->name); ?></h2>
                                <p><?php echo e($plan->description); ?></p>
                                <div class="portal-plan__tags">
                                    <?php $__currentLoopData = $plan->tags ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="portal-tag"><?php echo e($tag); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <span class="portal-rating">★ <?php echo e(number_format($plan->rating, 1)); ?></span>
                        </div>
                        <div class="portal-plan__body">
                            <div class="portal-plan__summary">
                                <span>Daily Calories</span>
                                <b><?php echo e($plan->daily_calories); ?> cal</b>
                            </div>
                            <?php $__currentLoopData = $mealSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mealSlot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $items = $plan->items->where('meal_slot', $mealSlot);
                                ?>
                                <?php if($items->isNotEmpty()): ?>
                                    <div class="portal-plan__meal">
                                        <strong><?php echo e($mealSlot); ?></strong>
                                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p>• <?php echo e($item->item_name); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <form method="POST" action="<?php echo e(route('portal.meal-plans.activate', $plan)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="portal-button portal-button--primary" style="width:100%;margin-top:8px;">Use This Plan</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <h2><?php echo e($editablePlan ? 'Customize Your Plan' : 'Create a Custom Plan'); ?></h2>
                    <p>Build a plan, rename it, adjust calories, and change every meal item.</p>
                </div>
                <form method="POST" action="<?php echo e($editablePlan ? route('portal.meal-plans.update', $editablePlan) : route('portal.meal-plans.store')); ?>" class="portal-form">
                    <?php echo csrf_field(); ?>
                    <?php if($editablePlan): ?>
                        <?php echo method_field('PUT'); ?>
                    <?php endif; ?>
                    <label>
                        <span>Plan Name</span>
                        <input type="text" name="name" value="<?php echo e($editablePlan?->name); ?>" required>
                    </label>
                    <label>
                        <span>Daily Calories</span>
                        <input type="number" name="daily_calories" value="<?php echo e($editablePlan?->daily_calories ?? 2000); ?>" required>
                    </label>
                    <label>
                        <span>Tags</span>
                        <input type="text" name="tags" value="<?php echo e($editablePlan ? implode(', ', $editablePlan->tags ?? []) : ''); ?>">
                    </label>
                    <label style="grid-column:1 / -1;">
                        <span>Description</span>
                        <input type="text" name="description" value="<?php echo e($editablePlan?->description); ?>" required>
                    </label>
                    <div class="portal-form__actions">
                        <button type="submit" class="portal-button portal-button--primary"><?php echo e($editablePlan ? 'Save Plan' : 'Create Plan'); ?></button>
                    </div>
                </form>
                <?php if($editablePlan): ?>
                    <form method="POST" action="<?php echo e(route('portal.meal-plans.activate', $editablePlan)); ?>" style="margin-top:12px;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="portal-button">Make Active</button>
                    </form>
                <?php endif; ?>
            </section>

            <?php if($editablePlan): ?>
                <section class="portal-grid portal-grid--two">
                    <?php $__currentLoopData = $mealSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mealSlot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="portal-card">
                            <div class="portal-section-title">
                                <h2><?php echo e($mealSlot); ?></h2>
                                <p>Swap items or add new ones for this meal.</p>
                            </div>
                            <div class="portal-log-card__items">
                                <?php $__empty_1 = true; $__currentLoopData = $editablePlan->items->where('meal_slot', $mealSlot); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <article class="portal-log-entry">
                                        <strong><?php echo e($item->item_name); ?></strong>
                                        <p><?php echo e($item->serving_label); ?></p>
                                        <form method="POST" action="<?php echo e(route('portal.meal-plans.items.destroy', [$editablePlan, $item])); ?>" style="margin-top:10px;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="portal-button">Remove</button>
                                        </form>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="portal-log-empty">No items yet</div>
                                <?php endif; ?>
                            </div>
                            <form method="POST" action="<?php echo e(route('portal.meal-plans.items.store', $editablePlan)); ?>" class="portal-form" style="margin-top:18px;grid-template-columns:1fr 1fr;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="meal_slot" value="<?php echo e($mealSlot); ?>">
                                <label>
                                    <span>Food</span>
                                    <input type="text" name="item_name" placeholder="Enter food name" required>
                                </label>
                                <label>
                                    <span>Serving label</span>
                                    <input type="text" name="serving_label" value="1 serving" required>
                                </label>
                                <div class="portal-form__actions">
                                    <button type="submit" class="portal-button portal-button--primary">Add to <?php echo e($mealSlot); ?></button>
                                </div>
                            </form>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </section>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($page === 'feedback'): ?>
            <section class="portal-grid portal-grid--four">
                <?php $__currentLoopData = $feedbackStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-summary-card <?php echo e($card['tone']); ?>">
                        <strong><?php echo e($card['value']); ?></strong>
                        <p><?php echo e($card['label']); ?></p>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>

            <section class="portal-card portal-dietitian">
                <div class="portal-section-title">
                    <h2>Your Assigned Dietitian</h2>
                    <p>Your personal nutrition expert</p>
                </div>
                <div class="portal-dietitian__row">
                    <span class="portal-avatar"><?php echo e(Str::of($dietitian?->name ?? 'NA')->explode(' ')->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->take(2)->implode('')); ?></span>
                    <div>
                        <h3><?php echo e($dietitian?->name ?? 'No dietitian assigned'); ?></h3>
                        <p><?php echo e($dietitian?->specialization ?? 'Registered Dietitian-Nutritionist'); ?></p>
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
                <?php $__currentLoopData = $feedbackItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="portal-card portal-feedback-card <?php echo e($loop->first ? 'is-highlighted' : ''); ?>">
                        <div class="portal-feedback-card__meta">
                            <span class="portal-badge portal-badge--blue"><?php echo e($item->tag); ?></span>
                            <span class="portal-badge <?php echo e($item->status === 'completed' ? 'portal-badge--green' : 'portal-badge--slate'); ?>"><?php echo e($item->status); ?></span>
                        </div>
                        <h3><?php echo e($item->title); ?></h3>
                        <strong>From <?php echo e($item->dietitian?->name ?? 'Dietitian'); ?> • <?php echo e($item->submitted_on->format('F j, Y')); ?></strong>
                        <p><?php echo e($item->message); ?></p>
                        <?php if(($item->recommendations ?? []) !== []): ?>
                            <div class="portal-feedback-card__recommendations">
                                <h4>Recommendations</h4>
                                <?php $__currentLoopData = $item->recommendations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recommendation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span><?php echo e($recommendation); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                        <div class="portal-actions" style="margin-top:18px;">
                            <form method="POST" action="<?php echo e(route('portal.feedback.reply')); ?>">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="title" value="Reply: <?php echo e($item->title); ?>">
                                <input type="hidden" name="message" value="Following up on <?php echo e($item->title); ?>">
                                <button type="submit" class="portal-button">Reply</button>
                            </form>
                            <?php if(! $item->is_read): ?>
                                <form method="POST" action="<?php echo e(route('portal.feedback.read', $item)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="portal-button">Mark as Read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <form method="POST" action="<?php echo e(route('portal.consultations.store')); ?>" class="portal-bmi__inputs">
                        <?php echo csrf_field(); ?>
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
                    <form method="POST" action="<?php echo e(route('portal.feedback.reply')); ?>" class="portal-bmi__inputs">
                        <?php echo csrf_field(); ?>
                        <label>
                            <span>Title</span>
                            <input type="text" name="title" placeholder="Message title">
                        </label>
                        <label>
                            <span>Message</span>
                            <input type="text" name="message" placeholder="Type your message">
                        </label>
                        <button type="submit" class="portal-button portal-button--primary">Send Message</button>
                    </form>
                </div>
            </section>
        <?php endif; ?>

        <?php if($page === 'profile'): ?>
            <section class="portal-grid portal-grid--profile">
                <article class="portal-card">
                    <div class="portal-section-title">
                        <h2>BMI Calculator</h2>
                        <p>Track your Body Mass Index and health status</p>
                    </div>
                    <div class="portal-bmi" data-profile-form>
                        <div>
                            <div class="portal-bmi__score">
                                <strong data-bmi-score><?php echo e($profile['bmi']); ?></strong>
                                <span data-bmi-status><?php echo e($bodyMassRange); ?></span>
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
                                <input type="number" value="<?php echo e($profile['height_cm'] ?? 0); ?>" data-profile-height>
                            </label>
                            <label>
                                <span>Current Weight (kg)</span>
                                <input type="number" step="0.1" value="<?php echo e($profile['current_weight_kg'] ?? 0); ?>" data-profile-weight>
                            </label>
                            <label>
                                <span>Target Weight (kg)</span>
                                <input type="number" step="0.1" value="<?php echo e($profile['target_weight_kg'] ?? 0); ?>" data-profile-target>
                            </label>
                        </div>
                    </div>
                    <div class="portal-list-card">
                        <h3>BMI Progress</h3>
                        <?php $__currentLoopData = $profile['bmi_history']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="portal-list-card__row">
                                <div>
                                    <strong><?php echo e($history['date']); ?></strong>
                                    <span><?php echo e($history['weight']); ?></span>
                                </div>
                                <div style="text-align:right;">
                                    <strong><?php echo e($history['bmi']); ?></strong>
                                    <span>BMI</span>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </article>

                <article class="portal-card">
                    <div class="portal-section-title">
                        <h2>Goal Progress</h2>
                    </div>
                    <div class="portal-goal-card__headline">
                        <strong data-profile-current><?php echo e($profile['current_weight_kg']); ?> kg</strong>
                        <span>Current Weight</span>
                    </div>
                    <div class="portal-goal-card__bar-meta">
                        <span>Progress to goal</span>
                        <strong data-profile-remaining><?php echo e(number_format($profile['remaining_weight_kg'], 1)); ?> kg remaining</strong>
                    </div>
                    <?php
                        $startingWeight = (float) $profile['starting_weight_kg'];
                        $currentWeight = (float) $profile['current_weight_kg'];
                        $targetWeight = (float) $profile['target_weight_kg'];
                        $achieved = max(0, $startingWeight - $currentWeight);
                        $goalTotal = max(1, $startingWeight - $targetWeight);
                        $progressPercent = min(100, $goalTotal > 0 ? ($achieved / $goalTotal) * 100 : 0);
                    ?>
                    <div class="portal-progress"><span style="width: <?php echo e($progressPercent); ?>%"></span></div>
                    <div class="portal-goal-card__status">
                        <strong>On track!</strong>
                        <span>You've lost <?php echo e(number_format($achieved, 1)); ?> kg so far</span>
                    </div>
                    <dl class="portal-goal-card__stats">
                        <div><dt>Starting</dt><dd><?php echo e($profile['starting_weight_kg']); ?> kg</dd></div>
                        <div><dt>Current</dt><dd data-profile-current-inline><?php echo e($profile['current_weight_kg']); ?> kg</dd></div>
                        <div><dt>Target</dt><dd class="is-green" data-profile-target-inline><?php echo e($profile['target_weight_kg']); ?> kg</dd></div>
                    </dl>
                </article>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Personal Information</h2>
                    <p>Update your profile and health goals</p>
                </div>

                <?php if(session('status')): ?>
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
                        ✓ <?php echo e(session('status')); ?>

                    </div>
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                    <!-- Edit Form -->
                    <div>
                        <h3 style="font-size: 0.95rem; color: #666; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px;">Update Information</h3>
                        <form method="POST" action="<?php echo e(route('portal.profile.update')); ?>" class="portal-form">
                            <?php echo csrf_field(); ?>
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
                                    <?php $__currentLoopData = ['Male', 'Female', 'Non-binary']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </label>
                            <label>
                                <span>Activity Level</span>
                                <select name="activity_level">
                                    <option value="">None</option>
                                    <?php $__currentLoopData = ['Lightly Active', 'Moderately Active', 'Very Active']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </label>
                            <label>
                                <span>Primary Goal</span>
                                <select name="primary_goal">
                                    <option value="">None</option>
                                    <?php $__currentLoopData = ['Weight Loss', 'Weight Maintenance', 'Muscle Gain']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['full_name'] ?: '—'); ?></dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Age</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['age'] ?: '—'); ?></dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Gender</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['gender'] ?: '—'); ?></dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Activity Level</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['activity_level'] ?: '—'); ?></dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Primary Goal</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['primary_goal'] ?: '—'); ?></dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Height</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['height_cm'] ?: '—'); ?> cm</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Current Weight</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['current_weight_kg'] ?: '—'); ?> kg</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Starting Weight</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['starting_weight_kg'] ?: '—'); ?> kg</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">Target Weight</dt>
                                    <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['target_weight_kg'] ?: '—'); ?> kg</dd>
                                </div>
                                <?php if($profile['bmi'] > 0): ?>
                                    <div>
                                        <dt style="font-size: 0.8rem; color: #999; text-transform: uppercase; margin-bottom: 0.25rem;">BMI</dt>
                                        <dd style="font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo e($profile['bmi']); ?> (<?php echo e($profile['status']); ?>)</dd>
                                    </div>
                                <?php endif; ?>
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
                    <?php $__currentLoopData = $intakeCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="portal-summary-card <?php echo e($card['tone']); ?>">
                            <strong><?php echo e($card['value']); ?></strong>
                            <p><?php echo e($card['label']); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/nico/Herd/N.I.K.A/resources/views/portal.blade.php ENDPATH**/ ?>