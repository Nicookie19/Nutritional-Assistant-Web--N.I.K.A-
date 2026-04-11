@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    $editingDietitian = $adminData['editingDietitian'];
    $editingFoodItem = $adminData['editingFoodItem'];
    $editingMealPlan = $adminData['editingMealPlan'];
    $editingUser = $adminData['editingUser'];
    $mealSlots = ['Breakfast', 'Lunch', 'Dinner', 'Snacks'];
    $users = $adminData['users'];
    $dietitians = $adminData['dietitians'];
    $foodItems = $adminData['foodItems'];
    $mealPlans = $adminData['mealPlans'];
    $feedbackRequests = $adminData['feedbackRequests'];
    $consultationRequests = $adminData['consultationRequests'];
    $dashboardData = $adminData['dashboard'];
    $dietitiansData = $adminData['dietitiansData'];
    $feedbackData = $adminData['feedbackData'];
    $analyticsData = $adminData['analyticsData'];
    $contentData = $adminData['contentData'];

    $activeUsersCount = $users->filter(fn ($user) => $user->foodLogEntries->isNotEmpty() || $user->plannedMealEntries->isNotEmpty())->count();
    $inactiveUsersCount = max($users->count() - $activeUsersCount, 0);
    $avgCaloriesPerUser = $users->count() > 0
        ? (int) round($users->avg(fn ($user) => $user->foodLogEntries->sum('calories')) ?? 0)
        : 0;

    $adminCards = [
        ['label' => 'Total Users', 'value' => $users->count(), 'meta' => 'Tracked accounts', 'tone' => 'portal-tone-blue'],
        ['label' => 'Active Users', 'value' => $activeUsersCount, 'meta' => 'With meal or plan activity', 'tone' => 'portal-tone-green'],
        ['label' => 'Dietitians', 'value' => $dietitians->count(), 'meta' => 'All Active', 'tone' => 'portal-tone-purple'],
        ['label' => 'Meals Today', 'value' => $users->sum(fn ($user) => $user->foodLogEntries->count()), 'meta' => 'Logged entries', 'tone' => 'portal-tone-orange'],
        ['label' => 'Pending Feedback', 'value' => $feedbackRequests->where('status', '!=', 'completed')->count(), 'meta' => 'Requires Action', 'tone' => 'portal-tone-red'],
        ['label' => 'Avg Calories/User', 'value' => $avgCaloriesPerUser, 'meta' => 'From current logs', 'tone' => 'portal-tone-teal'],
    ];
@endphp

@section('title', 'NutriAssist Admin')
@section('body_class', 'admin-app')

@section('content')
<div class="app-particles-background app-particles-background--admin">
    <div class="app-particles-background__layer app-particles-background__layer--one" style="background-image: radial-gradient(circle, rgba(249, 115, 22, 0.58) 0 2.5px, transparent 2.9px), radial-gradient(circle, rgba(251, 146, 60, 0.48) 0 2px, transparent 2.4px);"></div>
    <div class="app-particles-background__layer app-particles-background__layer--two" style="background-image: radial-gradient(circle, rgba(234, 88, 12, 0.5) 0 2.9px, transparent 3.3px), radial-gradient(circle, rgba(251, 146, 60, 0.4) 0 1.7px, transparent 2.1px);"></div>
    <div class="app-particles-background__layer app-particles-background__layer--three" style="background-image: radial-gradient(circle, rgba(249, 115, 22, 0.36) 0 2.1px, transparent 2.5px), radial-gradient(circle, rgba(251, 146, 60, 0.34) 0 2.7px, transparent 3.1px);"></div>
</div>

<div class="portal-shell">
    <header class="admin-header">
        <div class="admin-header__top">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <span class="admin-brand__mark">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C9.65 2 8 3.4 8 5s1.65 3 4 3 4-1.4 4-3-1.65-3-4-3z"/>
                        <path d="M12 8c-4.4 0-8 2.2-8 4.9 0 5.8 7.1 9.2 7.5 9.4a1.5 1.5 0 0 0 1 0c.4-.2 7.5-3.6 7.5-9.4C20 10.2 16.4 8 12 8zm0 12.2C9.2 18.5 4 15.7 4 12.9 4 11 7.6 9.7 12 9.7s8 1.3 8 3.2c0 2.8-5.2 5.6-8 6.6z"/>
                    </svg>
                </span>
                <div>
                    <strong>NutriAssist Admin</strong>
                    <small>System Management</small>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="admin-header__actions">
                @csrf
                <button type="submit" class="admin-exit">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.75 3a.75.75 0 0 1 .75.75v2.5a.75.75 0 0 1-1.5 0V4.5h-8v15h8v-1.75a.75.75 0 0 1 1.5 0v2.5a.75.75 0 0 1-.75.75h-9.5a.75.75 0 0 1-.75-.75V3.75A.75.75 0 0 1 6.25 3h9.5Z"/>
                        <path d="M13.72 8.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 1 1-1.06-1.06l1.97-1.97H9.75a.75.75 0 0 1 0-1.5h5.94l-1.97-1.97a.75.75 0 0 1 0-1.06Z"/>
                    </svg>
                    <span>Exit Admin</span>
                </button>
            </form>
        </div>
        <nav class="admin-nav">
            @foreach ($pages as $key => $item)
                <a href="{{ route($item['route']) }}" class="admin-nav__link {{ $page === $key ? 'is-active' : '' }}">
                    @if ($key === 'dashboard')
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11.25 3.75a.75.75 0 0 1 1.5 0v1.533a7.502 7.502 0 0 1 5.967 5.967h1.533a.75.75 0 0 1 0 1.5h-1.533a7.502 7.502 0 0 1-5.967 5.967v1.533a.75.75 0 0 1-1.5 0v-1.533a7.502 7.502 0 0 1-5.967-5.967H3.75a.75.75 0 0 1 0-1.5h1.533a7.502 7.502 0 0 1 5.967-5.967V3.75Zm.75 3a6 6 0 1 0 0 12 6 6 0 0 0 0-12Z"/></svg>
                    @elseif ($key === 'users')
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5ZM6.37 20.25a5.625 5.625 0 0 1 11.26 0 .75.75 0 0 1-.74.75H7.11a.75.75 0 0 1-.74-.75Z"/><path d="M18.75 8.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5ZM20.25 20.25a.75.75 0 0 0 .75-.75 4.5 4.5 0 0 0-4.07-4.476 5.205 5.205 0 0 1 1.573 4.145.75.75 0 0 0 .747 1.08h1.002ZM5.25 8.25a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5ZM3.75 20.25a.75.75 0 0 1-.75-.75 4.5 4.5 0 0 1 4.07-4.476 5.205 5.205 0 0 0-1.573 4.145.75.75 0 0 1-.747 1.08H3.75Z"/></svg>
                    @elseif ($key === 'dietitians')
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11.25 4.5a.75.75 0 0 1 1.5 0v1.19a3.75 3.75 0 0 1 2.56 2.56h1.19a.75.75 0 0 1 0 1.5h-1.19a3.75 3.75 0 0 1-2.56 2.56v1.19a.75.75 0 0 1-1.5 0v-1.19a3.75 3.75 0 0 1-2.56-2.56H7.5a.75.75 0 0 1 0-1.5h1.19a3.75 3.75 0 0 1 2.56-2.56V4.5Z"/><path d="M5.625 13.5A2.625 2.625 0 0 0 3 16.125v.375A3.75 3.75 0 0 0 6.75 20.25h1.61a5.227 5.227 0 0 1-.485-2.25 5.227 5.227 0 0 1 .485-2.25H5.625ZM18.375 13.5h-2.735a5.227 5.227 0 0 1 .485 2.25 5.227 5.227 0 0 1-.485 2.25h1.61A3.75 3.75 0 0 0 21 16.5v-.375a2.625 2.625 0 0 0-2.625-2.625ZM12 14.25A3.75 3.75 0 0 0 8.25 18v.375c0 1.036.84 1.875 1.875 1.875h3.75c1.036 0 1.875-.84 1.875-1.875V18A3.75 3.75 0 0 0 12 14.25Z"/></svg>
                    @elseif ($key === 'feedback')
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M4.804 4.804A6.75 6.75 0 0 1 9.58 2.25h4.84a6.75 6.75 0 0 1 6.75 6.75v3a6.75 6.75 0 0 1-6.75 6.75H9.58a6.724 6.724 0 0 1-3.584-1.028l-2.94.98a.75.75 0 0 1-.949-.949l.98-2.94A6.724 6.724 0 0 1 2.25 12V9.58a6.75 6.75 0 0 1 2.554-4.776Z"/></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 3a.75.75 0 0 1 .75.75V20.25a.75.75 0 0 1-1.5 0V3.75A.75.75 0 0 1 3.75 3Zm16.5 18a.75.75 0 0 0 .75-.75V8.25a.75.75 0 0 0-1.5 0v12a.75.75 0 0 0 .75.75Zm-5.25 0a.75.75 0 0 0 .75-.75V3.75a.75.75 0 0 0-1.5 0V20.25a.75.75 0 0 0 .75.75Zm-5.25 0a.75.75 0 0 0 .75-.75v-9a.75.75 0 0 0-1.5 0v9a.75.75 0 0 0 .75.75Z"/></svg>
                    @endif
                    <span>{{ $item['name'] }}</span>
                </a>
            @endforeach
        </nav>
    </header>

    <main class="portal-content portal-content--admin">
        @if (session('status'))
            <section class="portal-card portal-tone-green">
                <strong>{{ session('status') }}</strong>
            </section>
        @endif

        @if ($page === 'dashboard')
            <section class="portal-hero">
                <h1>Admin Dashboard</h1>
                <p>System overview and key metrics</p>
            </section>

            <section class="portal-grid portal-grid--three">
                @foreach ($adminCards as $card)
                    <article class="portal-card portal-stat-card">
                        <div class="portal-stat-card__top">
                            <h2>{{ $card['label'] }}</h2>
                            <span class="portal-mini-icon {{ $card['tone'] }}"></span>
                        </div>
                        <div class="portal-stat-card__value">{{ $card['value'] }}</div>
                        <p class="portal-stat-card__admin-meta">{{ $card['meta'] }}</p>
                    </article>
                @endforeach
            </section>

            @php
                $userGrowthMax = max(collect($dashboardData['user_growth'])->max('value') ?: 0, 1);
                $loggingActivityMax = max(collect($dashboardData['logging_activity'])->max('value') ?: 0, 1);
            @endphp

            <section class="portal-grid portal-grid--two">
                <article class="portal-card portal-chart-card">
                    <div class="portal-section-title">
                        <h2>User Growth <span class="portal-badge portal-badge--blue ml-2">Monthly</span></h2>
                        <p class="text-slate-500 text-sm">New user registrations over the last 7 months</p>
                    </div>
                    <div class="portal-bar-chart portal-bar-chart--user mt-6">
                        <div class="portal-bar-chart__grid"></div>
                        <div class="portal-bar-chart__axis">
                            <span>{{ $userGrowthMax }}</span>
                            <span>{{ (int) ceil($userGrowthMax * 0.66) }}</span>
                            <span>{{ (int) ceil($userGrowthMax * 0.33) }}</span>
                            <span>0</span>
                        </div>
                        @foreach ($dashboardData['user_growth'] as $point)
                            <div class="portal-bar-chart__item">
                                <span class="portal-bar-chart__bar" style="height: {{ max(($point['value'] / $userGrowthMax) * 100, 12) }}%; background:#5b82ef;"></span>
                                <label>{{ $point['label'] }}</label>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="portal-card portal-chart-card">
                    <div class="portal-section-title">
                        <h2>Logging Activity <span class="portal-badge portal-badge--green ml-2">Daily</span></h2>
                        <p class="text-slate-500 text-sm">Daily meal entries recorded across the system</p>
                    </div>
                    <div class="portal-bar-chart mt-6">
                        <div class="portal-bar-chart__grid"></div>
                        <div class="portal-bar-chart__axis">
                            <span>{{ $loggingActivityMax }}</span>
                            <span>{{ (int) ceil($loggingActivityMax * 0.75) }}</span>
                            <span>{{ (int) ceil($loggingActivityMax * 0.5) }}</span>
                            <span>{{ (int) ceil($loggingActivityMax * 0.25) }}</span>
                            <span>0</span>
                        </div>
                        @foreach ($dashboardData['logging_activity'] as $point)
                            <div class="portal-bar-chart__item">
                                <span class="portal-bar-chart__bar" style="height: {{ max(($point['value'] / $loggingActivityMax) * 100, 12) }}%"></span>
                                <label>{{ $point['label'] }}</label>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="portal-grid portal-grid--two">
                <section class="portal-card">
                    <div class="portal-section-title">
                        <h2>System Health</h2>
                        <p>Current system status</p>
                    </div>
                    <div class="portal-health-list">
                        @foreach ($dashboardData['health'] as $healthItem)
                            <article class="portal-health-list__item {{ $healthItem['tone'] }}">
                                <h3>{{ $healthItem['title'] }}</h3>
                                <p>{{ $healthItem['copy'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="portal-card">
                    <div class="portal-section-title">
                        <h2>Recent Users</h2>
                        <p>Latest user records reflected from the database</p>
                    </div>
                    <div class="portal-table-wrap">
                        <table class="portal-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Goal</th>
                                    <th>Dietitian</th>
                                    <th>BMI</th>
                                    <th>Meals</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dashboardData['recent_users'] as $recentUser)
                                    <tr>
                                        <td>
                                            <div class="flex flex-col">
                                                <span class="font-medium text-slate-900">{{ $recentUser['name'] }}</span>
                                                <span class="text-xs text-slate-500">{{ $recentUser['joined_at'] }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $recentUser['goal'] }}</td>
                                        <td>{{ $recentUser['dietitian'] }}</td>
                                        <td>{{ $recentUser['bmi'] }}</td>
                                        <td>{{ $recentUser['meal_count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Live Totals</h2>
                    <p>Current database-backed totals from admin and user records</p>
                </div>
                <div class="portal-grid portal-grid--three portal-grid--tight">
                    <article class="portal-card portal-admin-number"><h2>Registered Accounts</h2><strong>{{ $dashboardData['totals']['registered_users'] }}</strong></article>
                    <article class="portal-card portal-admin-number"><h2>User Profiles</h2><strong>{{ $dashboardData['totals']['experiences'] }}</strong></article>
                    <article class="portal-card portal-admin-number"><h2>Pending Feedback</h2><strong class="is-red">{{ $dashboardData['totals']['feedback_pending'] }}</strong></article>
                </div>
            </section>
        @endif

        @if ($page === 'users')
            <section class="portal-hero">
                <h1>User Management Hub</h1>
                <p>See live profile, nutrition, plan, and feedback data from the user portal and edit it from one admin page.</p>
            </section>

            <section class="portal-grid portal-grid--four">
                <article class="portal-card portal-admin-number"><h2>Total Users</h2><strong>{{ $users->count() }}</strong></article>
                <article class="portal-card portal-admin-number"><h2>Active</h2><strong class="is-green">{{ $activeUsersCount }}</strong></article>
                <article class="portal-card portal-admin-number"><h2>Inactive</h2><strong>{{ $inactiveUsersCount }}</strong></article>
                <article class="portal-card portal-admin-number"><h2>With Pending Feedback</h2><strong class="is-red">{{ $users->filter(fn ($user) => $user->feedbackRequests->where('status', '!=', 'completed')->isNotEmpty())->count() }}</strong></article>
            </section>

            <section class="portal-grid gap-6" style="display:grid;grid-template-columns:minmax(0,1.02fr) minmax(420px,0.98fr);gap:1.5rem;align-items:start;">
                <section class="portal-card">
                    <div class="portal-table-header">
                        <div class="portal-section-title">
                            <h2>All Users</h2>
                            <p>Live user portal records reflected in the admin hub</p>
                        </div>
                        <div class="portal-search" style="max-width:240px;">
                            <input type="text" placeholder="Search users..." data-table-search="users-table">
                        </div>
                    </div>
                    <div class="portal-table-wrap">
                        <table class="portal-table" id="users-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Dietitian</th>
                                    <th>BMI</th>
                                    <th>Goal</th>
                                    <th>Meals</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @php
                                        $heightInMeters = $user->height_cm ? $user->height_cm / 100 : 0;
                                        $bmi = $heightInMeters > 0 && $user->current_weight_kg ? round(((float) $user->current_weight_kg) / ($heightInMeters * $heightInMeters), 1) : null;
                                        $status = ($user->foodLogEntries->isNotEmpty() || $user->plannedMealEntries->isNotEmpty()) ? 'active' : 'inactive';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="flex flex-col">
                                                <span class="font-medium text-slate-900">{{ $user->full_name ?: 'Unnamed user' }}</span>
                                                <span class="text-xs text-slate-500">{{ optional($user->created_at)->format('M d, Y') }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $user->activeDietitian?->name ?? 'Unassigned' }}</td>
                                        <td>
                                            <div class="flex flex-col">
                                                <span class="font-medium text-slate-900">{{ $bmi ? number_format($bmi, 1) : '—' }}</span>
                                                <span class="text-xs {{ $bmi !== null && $bmi >= 25 ? 'text-orange-600' : 'text-emerald-600' }}">
                                                    @if ($bmi === null)
                                                        No data
                                                    @elseif ($bmi >= 30)
                                                        Obese
                                                    @elseif ($bmi >= 25)
                                                        Overweight
                                                    @else
                                                        Normal
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-slate-600">{{ $user->primary_goal ?: 'Not set' }}</td>
                                        <td class="text-center font-semibold text-slate-700">{{ $user->foodLogEntries->count() }}</td>
                                        <td><span class="portal-pill {{ $status === 'active' ? 'portal-pill--active' : 'portal-pill--inactive' }}">{{ $status }}</span></td>
                                        <td>
                                            <a href="{{ route('admin.users', ['edit_user' => $user->id]) }}" class="text-orange-600 font-semibold">Open Hub</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="space-y-6" style="display:flex;flex-direction:column;gap:1.5rem;">
                    <article class="portal-card">
                        <div class="portal-section-title">
                            <h2>User Detail Hub</h2>
                            <p>Selected user data mirrors the user-facing portal information.</p>
                        </div>

                        @if ($editingUser)
                            @php
                                $editingHeightInMeters = $editingUser->height_cm ? $editingUser->height_cm / 100 : 0;
                                $editingBmi = $editingHeightInMeters > 0 && $editingUser->current_weight_kg ? round(((float) $editingUser->current_weight_kg) / ($editingHeightInMeters * $editingHeightInMeters), 1) : null;
                                $pendingFeedbackCount = $editingUser->feedbackRequests->where('status', '!=', 'completed')->count();
                            @endphp

                            <div class="grid gap-3 sm:grid-cols-2 mb-6">
                                <div class="portal-card portal-tone-blue">
                                    <strong>{{ $editingUser->full_name ?: 'Unnamed user' }}</strong>
                                    <p class="text-sm text-slate-600 mt-1">Goal: {{ $editingUser->primary_goal ?: 'Not set' }}</p>
                                </div>
                                <div class="portal-card portal-tone-green">
                                    <strong>{{ $editingUser->activeDietitian?->name ?? 'Unassigned dietitian' }}</strong>
                                    <p class="text-sm text-slate-600 mt-1">Current support owner</p>
                                </div>
                                <div class="portal-card">
                                    <strong>{{ $editingUser->foodLogEntries->count() }}</strong>
                                    <p class="text-sm text-slate-500 mt-1">Food log entries</p>
                                </div>
                                <div class="portal-card">
                                    <strong>{{ $editingUser->plannedMealEntries->count() }}</strong>
                                    <p class="text-sm text-slate-500 mt-1">Planned meals</p>
                                </div>
                                <div class="portal-card">
                                    <strong>{{ $editingUser->activeMealPlan?->name ?? 'No active plan' }}</strong>
                                    <p class="text-sm text-slate-500 mt-1">Current meal plan</p>
                                </div>
                                <div class="portal-card">
                                    <strong>{{ $pendingFeedbackCount }}</strong>
                                    <p class="text-sm text-slate-500 mt-1">Pending feedback requests</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.users.update', $editingUser) }}" class="space-y-4">
                                @csrf
                                @method('PUT')

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="portal-field">
                                        <span>Full name</span>
                                        <input type="text" name="full_name" value="{{ old('full_name', $editingUser->full_name) }}">
                                    </label>
                                    <label class="portal-field">
                                        <span>Age</span>
                                        <input type="number" name="age" value="{{ old('age', $editingUser->age) }}">
                                    </label>
                                    <label class="portal-field">
                                        <span>Gender</span>
                                        <input type="text" name="gender" value="{{ old('gender', $editingUser->gender) }}">
                                    </label>
                                    <label class="portal-field">
                                        <span>Activity level</span>
                                        <input type="text" name="activity_level" value="{{ old('activity_level', $editingUser->activity_level) }}">
                                    </label>
                                    <label class="portal-field">
                                        <span>Primary goal</span>
                                        <input type="text" name="primary_goal" value="{{ old('primary_goal', $editingUser->primary_goal) }}">
                                    </label>
                                    <label class="portal-field">
                                        <span>Assigned dietitian</span>
                                        <select name="active_dietitian_id">
                                            <option value="">Unassigned</option>
                                            @foreach ($dietitians as $dietitian)
                                                <option value="{{ $dietitian->id }}" @selected((string) old('active_dietitian_id', $editingUser->active_dietitian_id) === (string) $dietitian->id)>
                                                    {{ $dietitian->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="portal-field">
                                        <span>Height (cm)</span>
                                        <input type="number" step="0.1" name="height_cm" value="{{ old('height_cm', $editingUser->height_cm) }}">
                                    </label>
                                    <label class="portal-field">
                                        <span>Current weight (kg)</span>
                                        <input type="number" step="0.1" name="current_weight_kg" value="{{ old('current_weight_kg', $editingUser->current_weight_kg) }}">
                                    </label>
                                    <label class="portal-field">
                                        <span>Target weight (kg)</span>
                                        <input type="number" step="0.1" name="target_weight_kg" value="{{ old('target_weight_kg', $editingUser->target_weight_kg) }}">
                                    </label>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-3">
                                    <article class="portal-card">
                                        <strong>{{ $editingBmi ? number_format($editingBmi, 1) : '—' }}</strong>
                                        <p class="text-sm text-slate-500 mt-1">Current BMI</p>
                                    </article>
                                    <article class="portal-card">
                                        <strong>{{ $editingUser->starting_weight_kg ? number_format((float) $editingUser->starting_weight_kg, 1) . ' kg' : '—' }}</strong>
                                        <p class="text-sm text-slate-500 mt-1">Starting weight</p>
                                    </article>
                                    <article class="portal-card">
                                        <strong>{{ $editingUser->target_weight_kg ? number_format(max((float) $editingUser->current_weight_kg - (float) $editingUser->target_weight_kg, 0), 1) . ' kg' : '—' }}</strong>
                                        <p class="text-sm text-slate-500 mt-1">Remaining to target</p>
                                    </article>
                                </div>

                                <button type="submit" class="portal-button portal-button--primary">Save user changes</button>
                            </form>
                        @else
                            <p class="text-slate-500">No user records available yet.</p>
                        @endif
                    </article>

                    @if ($editingUser)
                        <article class="portal-card">
                            <div class="portal-section-title">
                                <h2>Latest Activity</h2>
                                <p>Recent data from the selected user portal.</p>
                            </div>
                            <div class="space-y-3">
                                @forelse ($editingUser->foodLogEntries->sortByDesc('entry_date')->take(4) as $entry)
                                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 px-4 py-3">
                                        <div>
                                            <strong class="text-slate-900">{{ $entry->food_name }}</strong>
                                            <p class="text-sm text-slate-500">{{ $entry->meal_slot }} • {{ optional($entry->entry_date)->format('M d, Y') }}</p>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700">{{ $entry->calories }} cal</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">No food log activity yet.</p>
                                @endforelse
                            </div>
                        </article>
                    @endif
                </section>
            </section>
        @endif

        @if ($page === 'dietitians')
            <section class="portal-hero">
                <h1>Dietitian Management</h1>
                <p>Manage your team of nutrition professionals</p>
            </section>

            <section class="portal-grid portal-grid--four">
                @foreach ($dietitiansData['cards'] as $card)
                    <article class="portal-card portal-stat-card">
                        <div class="portal-stat-card__top">
                            <h2>{{ $card['label'] }}</h2>
                            <span class="portal-mini-icon {{ $card['tone'] }}"></span>
                        </div>
                        <div class="portal-stat-card__value">{{ $card['value'] }}</div>
                    </article>
                @endforeach
            </section>

            <section class="portal-grid gap-6" style="display:grid;grid-template-columns:minmax(0,1.2fr) minmax(360px,0.8fr);gap:1.5rem;align-items:start;">
                <section class="portal-card">
                    <div class="portal-table-header">
                        <div class="portal-section-title">
                            <h2>Dietitian Team</h2>
                            <p>Open a record to edit it or remove it from the admin roster.</p>
                        </div>
                    </div>
                    <div class="space-y-4" style="display:grid;gap:1rem;">
                        @forelse ($dietitiansData['list'] as $dietitian)
                            <article class="portal-card" style="padding:1rem 1.1rem;">
                                <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;">
                                    <div style="display:flex;gap:1rem;min-width:0;">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold border border-slate-200">
                                            {{ $dietitian['initials'] }}
                                        </div>
                                        <div style="min-width:0;">
                                            <h3 class="font-bold text-slate-900">{{ $dietitian['name'] }}</h3>
                                            <p class="text-sm text-slate-500">{{ $dietitian['specialization'] }}</p>
                                            <p class="text-xs text-slate-400">{{ $dietitian['email'] }}</p>
                                            <p class="text-xs text-slate-500 mt-2">{{ $dietitian['patient_count'] }} assigned users • {{ $dietitian['feedback_count'] }} feedback threads</p>
                                        </div>
                                    </div>
                                    <div class="text-right" style="min-width:120px;">
                                        <span class="block font-bold text-slate-900">{{ $dietitian['experience_years'] }} yrs</span>
                                        <span class="text-xs text-orange-500 font-semibold">★ {{ $dietitian['rating'] }} Rating</span>
                                        <span class="block mt-2 portal-pill {{ $dietitian['status'] === 'active' ? 'portal-pill--active' : 'portal-pill--inactive' }}">{{ $dietitian['status'] }}</span>
                                    </div>
                                </div>
                                <div class="portal-table__actions" style="margin-top:1rem;">
                                    <a href="{{ route('admin.dietitians', ['edit_dietitian' => $dietitian['id']]) }}" class="portal-button portal-button--ghost">Edit</a>
                                    <form method="POST" action="{{ route('admin.dietitians.destroy', $dietitian['id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="portal-button portal-button--danger">Delete</button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <p class="text-slate-500">No dietitians available yet.</p>
                        @endforelse
                    </div>
                </section>

                <section class="portal-card">
                    <div class="portal-section-title">
                        <h2>{{ $editingDietitian ? 'Edit Dietitian' : 'Add Dietitian' }}</h2>
                        <p>{{ $editingDietitian ? 'Update the selected dietitian profile.' : 'Create a new active dietitian profile for the admin side.' }}</p>
                    </div>
                    <form method="POST" action="{{ $editingDietitian ? route('admin.dietitians.update', $editingDietitian) : route('admin.dietitians.store') }}" class="portal-form" style="grid-template-columns:1fr;">
                        @csrf
                        @if ($editingDietitian)
                            @method('PUT')
                        @endif
                        <label>
                            <span>Name</span>
                            <input type="text" name="name" value="{{ old('name', $editingDietitian?->name) }}" required>
                        </label>
                        <label>
                            <span>Email</span>
                            <input type="email" name="email" value="{{ old('email', $editingDietitian?->email) }}" required>
                        </label>
                        <label>
                            <span>Specialization</span>
                            <input type="text" name="specialization" value="{{ old('specialization', $editingDietitian?->specialization) }}" required>
                        </label>
                        <label>
                            <span>Experience Years</span>
                            <input type="number" name="experience_years" value="{{ old('experience_years', $editingDietitian?->experience_years) }}" min="0" max="60" required>
                        </label>
                        <div class="portal-form__actions">
                            <button type="submit" class="portal-button portal-button--primary">{{ $editingDietitian ? 'Save Dietitian' : 'Add Dietitian' }}</button>
                            @if ($editingDietitian)
                                <a href="{{ route('admin.dietitians') }}" class="portal-button portal-button--ghost">Clear Selection</a>
                            @endif
                        </div>
                    </form>
                </section>
            </section>
        @endif

        @if ($page === 'feedback')
            <section class="portal-hero">
                <h1>Feedback Management</h1>
                <p>Review user messages, dietitian notes, and consultation requests</p>
            </section>

            <section class="portal-grid portal-grid--four">
                @foreach ($feedbackData['cards'] as $card)
                    <article class="portal-card portal-stat-card">
                        <div class="portal-stat-card__top">
                            <h2>{{ $card['label'] }}</h2>
                            <span class="portal-mini-icon {{ $card['tone'] }}"></span>
                        </div>
                        <div class="portal-stat-card__value">{{ $card['value'] }}</div>
                    </article>
                @endforeach
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>User Messages and Feedback</h2>
                    <p>Includes user replies sent from the feedback tab and dietitian responses.</p>
                </div>
                <div class="portal-table-wrap">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Dietitian</th>
                                <th>Topic</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($feedbackRequests as $request)
                            <tr>
                                <td class="font-medium">{{ $request->userExperience?->full_name ?? 'User' }}</td>
                                <td>{{ $request->dietitian?->name ?? 'Unassigned' }}</td>
                                <td>{{ Str::title($request->topic) }}</td>
                                <td class="text-slate-900 font-medium">{{ $request->title }}</td>
                                <td class="text-slate-600">{{ Str::limit($request->message, 90) }}</td>
                                <td><span class="portal-badge {{ $request->priority === 'high' ? 'portal-badge--red' : 'portal-badge--slate' }}">{{ $request->priority }}</span></td>
                                <td><span class="portal-pill {{ $request->status === 'completed' ? 'portal-pill--completed' : ($request->status === 'in-progress' ? 'portal-pill--in-progress' : 'portal-pill--pending') }}">{{ $request->status }}</span></td>
                                <td class="text-slate-500">{{ $request->submitted_on->format('M d, Y') }}</td>
                                <td>
                                    @if ($request->status !== 'completed')
                                        <form method="POST" action="{{ route('admin.feedback.complete', $request) }}">
                                            @csrf
                                            <button type="submit" class="portal-button portal-button--primary">Complete</button>
                                        </form>
                                    @else
                                        <span class="text-sm font-semibold text-emerald-600">Done</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>Consultation Requests</h2>
                    <p>{{ $feedbackData['consultations']['total'] }} total requests, {{ $feedbackData['consultations']['pending'] }} pending, {{ $feedbackData['consultations']['in_progress'] }} in progress.</p>
                </div>
                <div class="portal-table-wrap">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Dietitian</th>
                                <th>Preferred Date</th>
                                <th>Note</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($consultationRequests as $consultation)
                                <tr>
                                    <td class="font-medium">{{ $consultation->userExperience?->full_name ?? 'User' }}</td>
                                    <td>{{ $consultation->dietitian?->name ?? 'Unassigned' }}</td>
                                    <td class="text-slate-500">{{ $consultation->preferred_date?->format('M d, Y') ?? 'Not specified' }}</td>
                                    <td class="text-slate-600">{{ $consultation->note !== '' ? Str::limit($consultation->note, 100) : 'No note provided' }}</td>
                                    <td><span class="portal-pill {{ $consultation->status === 'completed' ? 'portal-pill--completed' : ($consultation->status === 'in-progress' ? 'portal-pill--in-progress' : 'portal-pill--pending') }}">{{ $consultation->status }}</span></td>
                                    <td>
                                        @if ($consultation->status !== 'completed')
                                            <div class="portal-table__actions">
                                                @if ($consultation->status === 'pending')
                                                    <form method="POST" action="{{ route('admin.consultations.status', $consultation) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="in-progress">
                                                        <button type="submit" class="portal-button portal-button--ghost">In Progress</button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.consultations.status', $consultation) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="portal-button portal-button--primary">Complete</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-sm font-semibold text-emerald-600">Done</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-slate-500">No consultation requests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($page === 'content')
            <section class="portal-hero">
                <h1>Content Management</h1>
                <p>Maintain food items and admin meal plan templates from live database records.</p>
            </section>

            <section class="portal-grid portal-grid--four">
                @foreach ($contentData['cards'] as $card)
                    <article class="portal-card portal-stat-card">
                        <div class="portal-stat-card__top">
                            <h2>{{ $card['label'] }}</h2>
                            <span class="portal-mini-icon {{ $card['tone'] }}"></span>
                        </div>
                        <div class="portal-stat-card__value">{{ $card['value'] }}</div>
                    </article>
                @endforeach
            </section>

            <section class="portal-grid portal-grid--two">
                <section class="portal-card">
                    <div class="portal-section-title">
                        <h2>{{ $editingFoodItem ? 'Edit Food Item' : 'Add Food Item' }}</h2>
                        <p>Changes here update the shared food database used across the experience.</p>
                    </div>
                    <form method="POST" action="{{ $editingFoodItem ? route('admin.food-items.update', $editingFoodItem) : route('admin.food-items.store') }}" class="portal-form">
                        @csrf
                        @if ($editingFoodItem)
                            @method('PUT')
                        @endif
                        <label>
                            <span>Name</span>
                            <input type="text" name="name" value="{{ old('name', $editingFoodItem?->name) }}" required>
                        </label>
                        <label>
                            <span>Category</span>
                            <input type="text" name="category" value="{{ old('category', $editingFoodItem?->category) }}" required>
                        </label>
                        <label>
                            <span>Serving Size</span>
                            <input type="text" name="serving_size" value="{{ old('serving_size', $editingFoodItem?->serving_size) }}" required>
                        </label>
                        <label>
                            <span>Calories</span>
                            <input type="number" name="calories" value="{{ old('calories', $editingFoodItem?->calories) }}" min="0" required>
                        </label>
                        <label>
                            <span>Protein</span>
                            <input type="number" step="0.1" name="protein" value="{{ old('protein', $editingFoodItem?->protein) }}" min="0" required>
                        </label>
                        <label>
                            <span>Carbs</span>
                            <input type="number" step="0.1" name="carbs" value="{{ old('carbs', $editingFoodItem?->carbs) }}" min="0" required>
                        </label>
                        <label>
                            <span>Fat</span>
                            <input type="number" step="0.1" name="fat" value="{{ old('fat', $editingFoodItem?->fat) }}" min="0" required>
                        </label>
                        <label>
                            <span>Fiber</span>
                            <input type="number" step="0.1" name="fiber" value="{{ old('fiber', $editingFoodItem?->fiber) }}" min="0" required>
                        </label>
                        <div class="portal-form__actions">
                            <button type="submit" class="portal-button portal-button--primary">{{ $editingFoodItem ? 'Save Food Item' : 'Add Food Item' }}</button>
                            @if ($editingFoodItem)
                                <a href="{{ route('admin.content') }}" class="portal-button portal-button--ghost">Clear Selection</a>
                            @endif
                        </div>
                    </form>
                </section>

                <section class="portal-card">
                    <div class="portal-section-title">
                        <h2>{{ $editingMealPlan ? 'Edit Template Plan' : 'Add Template Plan' }}</h2>
                        <p>Template meal plans feed the user portal starter plans.</p>
                    </div>
                    <form method="POST" action="{{ $editingMealPlan ? route('admin.meal-plans.update', $editingMealPlan) : route('admin.meal-plans.store') }}" class="portal-form">
                        @csrf
                        @if ($editingMealPlan)
                            @method('PUT')
                        @endif
                        <label>
                            <span>Name</span>
                            <input type="text" name="name" value="{{ old('name', $editingMealPlan?->name) }}" required>
                        </label>
                        <label>
                            <span>Daily Calories</span>
                            <input type="number" name="daily_calories" value="{{ old('daily_calories', $editingMealPlan?->daily_calories) }}" min="1200" max="6000" required>
                        </label>
                        <label style="grid-column: 1 / -1;">
                            <span>Description</span>
                            <input type="text" name="description" value="{{ old('description', $editingMealPlan?->description) }}" required>
                        </label>
                        <label style="grid-column: 1 / -1;">
                            <span>Tags</span>
                            <input type="text" name="tags" value="{{ old('tags', $editingMealPlan ? implode(', ', $editingMealPlan->tags ?? []) : '') }}" placeholder="High Protein, Balanced">
                        </label>
                        <div class="portal-form__actions">
                            <button type="submit" class="portal-button portal-button--primary">{{ $editingMealPlan ? 'Save Meal Plan' : 'Add Meal Plan' }}</button>
                            @if ($editingMealPlan)
                                <a href="{{ route('admin.content') }}" class="portal-button portal-button--ghost">Clear Selection</a>
                            @endif
                        </div>
                    </form>
                </section>
            </section>

            @if ($editingMealPlan)
                <section class="portal-grid gap-6" style="display:grid;grid-template-columns:minmax(320px,0.8fr) minmax(0,1.2fr);gap:1.5rem;align-items:start;">
                    <section class="portal-card">
                        <div class="portal-section-title">
                            <h2>Add Template Item</h2>
                            <p>Add foods directly into the selected template plan.</p>
                        </div>
                        <form method="POST" action="{{ route('admin.meal-plans.items.store', $editingMealPlan) }}" class="portal-form" style="grid-template-columns:1fr;">
                            @csrf
                            <label>
                                <span>Meal Slot</span>
                                <select name="meal_slot" required>
                                    @foreach ($mealSlots as $mealSlot)
                                        <option value="{{ $mealSlot }}">{{ $mealSlot }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                <span>Food Item</span>
                                <select name="food_item_id" required>
                                    <option value="">Select a food item</option>
                                    @foreach ($foodItems as $foodItem)
                                        <option value="{{ $foodItem->id }}">{{ $foodItem->name }} • {{ $foodItem->serving_size }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                <span>Serving Label</span>
                                <input type="text" name="serving_label" placeholder="1 bowl" required>
                            </label>
                            <div class="portal-form__actions">
                                <button type="submit" class="portal-button portal-button--primary">Add Item</button>
                            </div>
                        </form>
                    </section>

                    <section class="portal-card">
                        <div class="portal-section-title">
                            <h2>Template Items</h2>
                            <p>{{ $editingMealPlan->name }} currently has {{ $editingMealPlan->items->count() }} saved items.</p>
                        </div>
                        <div class="portal-table-wrap">
                            <table class="portal-table">
                                <thead>
                                    <tr>
                                        <th>Meal Slot</th>
                                        <th>Item</th>
                                        <th>Serving</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($editingMealPlan->items as $item)
                                        <tr>
                                            <td>{{ $item->meal_slot }}</td>
                                            <td>{{ $item->item_name }}</td>
                                            <td>{{ $item->serving_label }}</td>
                                            <td class="portal-table__actions">
                                                <form method="POST" action="{{ route('admin.meal-plans.items.destroy', [$editingMealPlan, $item]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="portal-button portal-button--danger">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-slate-500">No template items saved yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </section>
            @endif

            <section class="portal-grid portal-grid--two">
                <section class="portal-card">
                    <div class="portal-table-header">
                        <div class="portal-section-title">
                            <h2>Food Items</h2>
                            <p>Live admin-managed food content</p>
                        </div>
                    </div>
                    <div class="portal-table-wrap">
                        <table class="portal-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Calories</th>
                                    <th>Protein</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($foodItems as $foodItem)
                                    <tr>
                                        <td>{{ $foodItem->name }}</td>
                                        <td>{{ $foodItem->category }}</td>
                                        <td>{{ $foodItem->calories }}</td>
                                        <td>{{ number_format((float) $foodItem->protein, 1) }}g</td>
                                        <td class="portal-table__actions">
                                            <a href="{{ route('admin.content', ['edit_food' => $foodItem->id]) }}" class="text-orange-600 font-semibold">Edit</a>
                                            <form method="POST" action="{{ route('admin.food-items.destroy', $foodItem) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="portal-button portal-button--danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="portal-card">
                    <div class="portal-table-header">
                        <div class="portal-section-title">
                            <h2>Template Meal Plans</h2>
                            <p>Live plan templates available to user profiles</p>
                        </div>
                    </div>
                    <div class="portal-table-wrap">
                        <table class="portal-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Calories</th>
                                    <th>Rating</th>
                                    <th>Items</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mealPlans as $mealPlan)
                                    <tr>
                                        <td>{{ $mealPlan->name }}</td>
                                        <td>{{ $mealPlan->daily_calories }}</td>
                                        <td>{{ number_format((float) $mealPlan->rating, 1) }}</td>
                                        <td>{{ $mealPlan->items->count() }}</td>
                                        <td class="portal-table__actions">
                                            <a href="{{ route('admin.content', ['edit_meal_plan' => $mealPlan->id]) }}" class="text-orange-600 font-semibold">Edit</a>
                                            <form method="POST" action="{{ route('admin.meal-plans.destroy', $mealPlan) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="portal-button portal-button--danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>
        @endif

        @if ($page === 'analytics')
            <section class="portal-hero">
                <h1>System Analytics</h1>
                <p>Detailed performance and engagement data</p>
            </section>

            <section class="portal-grid portal-grid--two">
                <article class="portal-card">
                    <div class="portal-section-title">
                        <h2>BMI Distribution</h2>
                        <p>Breakdown of user population by BMI category</p>
                    </div>
                    <div class="flex items-center justify-center p-8">
                        <div class="w-48 h-48 rounded-full border-8 border-slate-50 shadow-inner" style="background: {{ $analyticsData['bmi_distribution']['style'] }};"></div>
                        <div class="ml-12 space-y-3">
                            @foreach ($analyticsData['bmi_distribution']['legend'] as $item)
                                <div class="flex items-center gap-2 text-sm"><span class="w-3 h-3 rounded-full" style="background: {{ $item['color'] }}"></span> {{ $item['label'] }} ({{ $item['percent'] }}%)</div>
                            @endforeach
                        </div>
                    </div>
                </article>

                <article class="portal-card">
                    <div class="portal-section-title">
                        <h2>Primary Goals</h2>
                        <p>User-selected fitness and health objectives</p>
                    </div>
                    <div class="flex items-center justify-center p-8">
                        <div class="w-48 h-48 rounded-full border-8 border-slate-50 shadow-inner" style="background: {{ $analyticsData['goals']['style'] }};"></div>
                        <div class="ml-12 space-y-3">
                            @foreach ($analyticsData['goals']['legend'] as $item)
                                <div class="flex items-center gap-2 text-sm"><span class="w-3 h-3 rounded-full" style="background: {{ $item['color'] }}"></span> {{ $item['label'] }} ({{ $item['percent'] }}%)</div>
                            @endforeach
                        </div>
                    </div>
                </article>
            </section>
        @endif
    </main>
</div>
@endsection
