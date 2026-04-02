@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    $editingFoodItem = $adminData['editingFoodItem'];
    $editingMealPlan = $adminData['editingMealPlan'];
    $editingUser = $adminData['editingUser'];
    $mealSlots = ['Breakfast', 'Lunch', 'Dinner', 'Snacks'];
    $users = $adminData['users'];
    $dietitians = $adminData['dietitians'];
    $foodItems = $adminData['foodItems'];
    $mealPlans = $adminData['mealPlans'];
    $feedbackRequests = $adminData['feedbackRequests'];

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

    $dietitianNames = ['Dr. Jennifer Adams', 'Dr. Marcus Thompson', 'Dr. Lisa Park', 'Dr. Ahmed Hassan', 'Dr. Rachel Green'];
    $dietitianSpecialties = ['Sports Nutrition', 'Weight Management', 'Clinical Nutrition', 'Pediatric Nutrition', 'Plant-Based Nutrition'];
    $dietitianPatients = [42, 58, 35, 47, 28];
    $dietitianRatings = [4.8, 4.9, 4.7, 4.9, 4.6];

    $goalPieStyle = 'conic-gradient(#557fe8 0deg 136deg, #e5534d 136deg 272deg, #59ba87 272deg 319deg, #f3a433 319deg 360deg)';
    $bmiPieStyle = 'conic-gradient(#59ba87 0deg 227deg, #ef7f2f 227deg 364deg, #e5534d 364deg 360deg)';
@endphp

@section('title', 'NutriAssist Admin')
@section('body_class', 'admin-app')

@section('content')
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

            <section class="portal-grid portal-grid--two">
                <article class="portal-card portal-chart-card">
                    <div class="portal-section-title">
                        <h2>User Growth <span class="portal-badge portal-badge--blue ml-2">Monthly</span></h2>
                        <p class="text-slate-500 text-sm">New user registrations over the last 7 months</p>
                    </div>
                    <div class="portal-bar-chart portal-bar-chart--user mt-6">
                        <div class="portal-bar-chart__grid"></div>
                        <div class="portal-bar-chart__axis">
                            <span>1200</span>
                            <span>800</span>
                            <span>350</span>
                            <span>0</span>
                        </div>
                        @foreach ([840, 920, 1050, 1140, 1190, 1220, 1247] as $index => $bar)
                            <div class="portal-bar-chart__item">
                                <span class="portal-bar-chart__bar" style="height: {{ max(($bar / 1400) * 100, 12) }}%; background:#5b82ef;"></span>
                                <label>{{ ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'][$index] }}</label>
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
                            <span>4k</span>
                            <span>3k</span>
                            <span>2k</span>
                            <span>1000</span>
                            <span>0</span>
                        </div>
                        @foreach ([3250, 3480, 3190, 3600, 3770, 3920, 3490] as $index => $bar)
                            <div class="portal-bar-chart__item">
                                <span class="portal-bar-chart__bar" style="height: {{ max(($bar / 4000) * 100, 12) }}%"></span>
                                <label>{{ ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$index] }}</label>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="portal-card">
                <div class="portal-section-title">
                    <h2>System Health</h2>
                    <p>Current system status</p>
                </div>
                <div class="portal-health-list">
                    <article class="portal-health-list__item portal-tone-green">
                        <h3>All Systems Operational</h3>
                        <p>Database, API, and services running smoothly</p>
                    </article>
                    <article class="portal-health-list__item portal-tone-blue">
                        <h3>High User Engagement</h3>
                        <p>71.5% of users active in the last 7 days</p>
                    </article>
                    <article class="portal-health-list__item portal-tone-yellow">
                        <h3>Feedback Queue</h3>
                        <p>{{ $feedbackRequests->where('status', 'pending')->count() + 21 }} feedback requests pending assignment</p>
                    </article>
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

            <section class="portal-grid portal-grid--[1.15fr,0.85fr] gap-6" style="display:grid;grid-template-columns:1.15fr .85fr;gap:1.5rem;">
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
                <article class="portal-card portal-stat-card">
                    <div class="portal-stat-card__top">
                        <h2>Total Dietitians</h2>
                        <span class="portal-mini-icon portal-tone-purple"></span>
                    </div>
                    <div class="portal-stat-card__value">5</div>
                </article>
                <article class="portal-card portal-stat-card">
                    <div class="portal-stat-card__top">
                        <h2>Total Patients</h2>
                        <span class="portal-mini-icon portal-tone-blue"></span>
                    </div>
                    <div class="portal-stat-card__value">210</div>
                </article>
                <article class="portal-card portal-stat-card">
                    <div class="portal-stat-card__top">
                        <h2>Average Rating</h2>
                        <span class="portal-mini-icon portal-tone-yellow"></span>
                    </div>
                    <div class="portal-stat-card__value">4.8</div>
                </article>
                <article class="portal-card portal-stat-card">
                    <div class="portal-stat-card__top">
                        <h2>Active</h2>
                        <span class="portal-mini-icon portal-tone-green"></span>
                    </div>
                    <div class="portal-stat-card__value">5</div>
                </article>
            </section>

            <section class="portal-grid portal-grid--two">
                @foreach($dietitianNames as $index => $name)
                    <article class="portal-card flex items-center justify-between p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold border border-slate-200">
                                {{ substr($name, 4, 1) }}{{ substr(explode(' ', $name)[2], 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $name }}</h3>
                                <p class="text-sm text-slate-500">{{ $dietitianSpecialties[$index] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-slate-900">{{ $dietitianPatients[$index] }} Patients</span>
                            <span class="text-xs text-orange-500 font-semibold">★ {{ $dietitianRatings[$index] }} Rating</span>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($page === 'feedback')
            <section class="portal-hero">
                <h1>Feedback Management</h1>
                <p>Review and assign user feedback requests</p>
            </section>

            <section class="portal-card">
                <div class="portal-table-wrap">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Dietitian</th>
                                <th>Topic</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedbackRequests as $request)
                            <tr>
                                <td class="font-medium">{{ $request->userExperience?->full_name ?? 'User' }}</td>
                                <td>{{ $request->dietitian?->name ?? 'Unassigned' }}</td>
                                <td>{{ Str::title($request->topic) }}</td>
                                <td><span class="portal-badge {{ $request->priority === 'high' ? 'portal-badge--red' : 'portal-badge--slate' }}">{{ $request->priority }}</span></td>
                                <td><span class="portal-pill {{ $request->status === 'completed' ? 'portal-pill--active' : 'portal-pill--inactive' }}">{{ $request->status }}</span></td>
                                <td class="text-slate-500">{{ $request->submitted_on->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                        <div class="w-48 h-48 rounded-full border-8 border-slate-50 shadow-inner" style="background: {{ $bmiPieStyle }};"></div>
                        <div class="ml-12 space-y-3">
                            <div class="flex items-center gap-2 text-sm"><span class="w-3 h-3 bg-[#59ba87] rounded-full"></span> Normal (63%)</div>
                            <div class="flex items-center gap-2 text-sm"><span class="w-3 h-3 bg-[#ef7f2f] rounded-full"></span> Overweight (32%)</div>
                            <div class="flex items-center gap-2 text-sm"><span class="w-3 h-3 bg-[#e5534d] rounded-full"></span> Obese (5%)</div>
                        </div>
                    </div>
                </article>

                <article class="portal-card">
                    <div class="portal-section-title">
                        <h2>Primary Goals</h2>
                        <p>User-selected fitness and health objectives</p>
                    </div>
                    <div class="flex items-center justify-center p-8">
                        <div class="w-48 h-48 rounded-full border-8 border-slate-50 shadow-inner" style="background: {{ $goalPieStyle }};"></div>
                        <div class="ml-12 space-y-3">
                            <div class="flex items-center gap-2 text-sm"><span class="w-3 h-3 bg-[#557fe8] rounded-full"></span> Maintenance (38%)</div>
                            <div class="flex items-center gap-2 text-sm"><span class="w-3 h-3 bg-[#e5534d] rounded-full"></span> Muscle Gain (38%)</div>
                            <div class="flex items-center gap-2 text-sm"><span class="w-3 h-3 bg-[#59ba87] rounded-full"></span> Weight Loss (13%)</div>
                            <div class="flex items-center gap-2 text-sm"><span class="w-3 h-3 bg-[#f3a433] rounded-full"></span> Lifestyle (11%)</div>
                        </div>
                    </div>
                </article>
            </section>
        @endif
    </main>
</div>
@endsection
