COMPLETE NUTRIASSIST PROJECT TODO / ROADMAP

## OVERVIEW
**NutriAssist** - Laravel nutrition tracking app with AI insights, meal planning, food logging, admin dashboard.
- **Stack:** Laravel 11, Blade, TailwindCSS/Vite, SQLite (dev).
- **Auth:** Email/password + admin (is_admin flag).
- **Key Features:** User portal (dashboard/food-log/calendar/insights/meal-plans/feedback/profile), Admin dashboard.
- **Demo Mode:** Session-based (UserExperience model), seeds demo data.
- **Live:** http://127.0.0.1:8001 (auth required post-login).

## BACKEND STRUCTURE

### Controllers (app/Http/Controllers/)
```
PortalController.php (Main)
├── user($page='dashboard') → portal.blade.php (user pages)
├── admin($page='dashboard') → admin-portal.blade.php (admin pages)
├── portalData() → All user data aggregation
├── adminData() → Admin stats/charts
├── currentExperience() → Session/UserExperience resolver
├── seedDemoData() → PortalDemoSeeder
└── profileData(), insightsData(), calendarData() → Computed metrics

PortalActionController.php (CRUD)
├── updateProfile() → Save profile/BMI history
├── storeFoodLogEntry() → Food log
├── storePlannedMealEntries() → Calendar planning
├── saveMealPlan(), addMealPlanItem() → Meal plans
├── storeFeedbackReply(), storeConsultation() → Feedback
└── destroy*() → Delete actions

AdminActionController.php (Admin CRUD)
├── storeDietitian(), saveFoodItem(), saveMealPlan()
├── completeFeedback()
└── destroy*()

Auth Controllers
├── LoginController → Admin checkbox login
└── RegisterController → Admin code validation ('1000808790')

Other: HomeController, DocsController, ComponentsController, DeployController, ChangelogController, LaracastsController
```

### Models (app/Models/)
```
User → experiences() → UserExperience
├── is_admin boolean (migration 2024_10_17_120000_add_is_admin_to_users_table.php)

UserExperience (Session/Profile)
├── session_key (unique), user_id, active_dietitian_id, active_meal_plan_id
├── profile fields (full_name, age, gender, height_cm, current_weight_kg*, starting_weight_kg, target_weight_kg*, activity_level, primary_goal, bmi_history JSON)
├── relations: foodLogEntries, feedbackRequests, plannedMealEntries, mealPlans, activeDietitian

FoodLogEntry → user_experience_id, meal_slot (Breakfast/Lunch/Dinner/Snacks), food_name, calories, protein
FoodItem (Admin) → name, calories, protein, is_active

MealPlan → user_experience_id, is_template/active, daily_calories, tags[], items → MealPlanItem (food_item_id, meal_slot, item_name)
PlannedMealEntry → user_experience_id, scheduled_date, meal_slot, food_name, grams, calories

Dietitian → name, specialization
FeedbackRequest → user_experience_id/dietitian_id, title, message, recommendations[], status, is_read
ConsultationRequest → user_experience_id/dietitian_id, preferred_date, note
```

### Migrations (database/migrations/)
```
Users/Cache/Jobs (Laravel 11)
add_is_admin_to_users_table (2024_10_17)
Profile fields → users: address/age/gender/date_of_birth (2026_04_02_021721)
UserExperience table (2026_04_01_111513)
Food/Meal/* tables
add_user_id_to_user_experiences_table (2026_04_02_030533)
```

### Routes (routes/web.php)
```
Guest: login/register
Auth: /portal/* (dashboard/food-log/calendar/insights/meal-plans/feedback/profile) → PortalController.user()
Auth+Admin: /admin/* → PortalController.admin()
Actions → PortalActionController/AdminActionController
Public: /docs /components /laracasts /deploy /changelog
```

### Middleware
```
EnsureUserIsAdmin.php → $request->user()->is_admin
```

### Requests (app/Http/Requests/)
```
UpdateProfileRequest, StoreFoodLogEntryRequest, StorePlannedMealEntriesRequest, etc. (Validation)
```

### Seeders
```
DatabaseSeeder → AdminSeeder (admin@example.com/password), PortalDemoSeeder (FoodItem/Dietitian/etc.)
```

## FRONTEND STRUCTURE

### Views (resources/views/)
```
layouts/app.blade.php → TailwindCSS particles bg
portal.blade.php (Main user page)
├── @if($page==='profile') → BMI calculator (JS live calc), Goal progress, Profile form
├── dashboard → Stats cards (macros), Charts (pie/macro, bar/calories), Micronutrients
├── food-log → Meal cards (Breakfast/etc.), Modal add food
├── calendar → Month grid, Upcoming cards, Day modals (meal inputs)
├── insights → AI cards, Performance, Achievements, Recommendations
├── meal-plans → Stats, Template cards, Custom builder
├── feedback → Dietitian card, Feedback list, Modals (consult/message)
admin-portal.blade.php → Admin dashboard (users/BMI pie, charts, forms)
auth/login.blade.php → Admin checkbox
auth/register.blade.php → Admin checkbox/code input
welcome.blade.php, docs/components/deploy/changelog/laracasts.blade.php
```

### JS/CSS
```
resources/js/app.js → BMI calc (data-profile-height/weight/target → data-bmi-score/status)
resources/css/app.css → Tailwind
vite.config.js → Build
```

## WORKING FLOW
1. Login/register → UserExperience (session_key UUID/cookie 1yr)
2. Demo seed → Template MealPlan → Copy to user + log/plan entries + welcome feedback
3. Portal data → Aggregates (macros from logs, BMI=weight/(height/100)^2, insights/AI gen)
4. Admin → Full CRUD (dietitians/food/meal-plans/feedback)

## ✅ COMPLETED FEATURES
- [x] Full user portal with 7 pages (dashboard, profile, food-log, calendar, insights, meal-plans, feedback)
- [x] Admin dashboard with stats, CRUD for dietitians/food/meal-plans/feedback
- [x] Session-based profiles (UserExperience model) with BMI history/tracking
- [x] Food logging, meal planning, calendar scheduling
- [x] Demo data seeding, auth (admin/user separation)
- [x] Responsive Tailwind UI, JS BMI calculator, CSS charts
- [x] 17 DB tables, factories, validation requests, tests (basic)

## ⚠️ ISSUES / INTELEPHENSE ERRORS (from env_details)
```
PortalActionController.php lines 365/377/384+: auth()->check(), auth()->user(), auth()->id()
PortalController.php lines 624/636/643+: same
```
→ Add `use Illuminate\Support\Facades\Auth;` to affected controllers.

## 🚀 NEXT STEPS / IMPROVEMENTS
### Backend (High Priority)
- [ ] Fix Intelephense: Add `use Illuminate\Support\Facades\Auth;` to controllers
- [ ] Tests: Expand Feature/PortalExperienceTest.php, PortalPagesTest.php
- [ ] Real AI: OpenAI nutrition analysis (insights)

### Backend (Medium)
- [ ] Email: Consultations, feedback replies (config/mail.php)
- [ ] Pagination: Feedback, logs, plans
- [ ] Validation: Age range, BMI sanity checks
- [ ] Auth: Password reset, email verify
- [ ] API: Mobile app endpoints

### Frontend
- [ ] JS: BMI calc show NaN→0, add kg/lbs toggle
- [ ] Mobile: Responsive calendar/meals (Tailwind sm/md)
- [ ] Charts: Real data → Chart.js (replace CSS bars/pies)
- [ ] Profile: Photo upload, DOB calc age
- [ ] Search: Food items autocomplete
- [ ] Dark mode toggle

### Admin
- [ ] Users: Profile edit, delete, impersonate
- [ ] Analytics: Real charts (user growth, engagement)
- [ ] Content: Rich editor (MealPlan description)
- [ ] Export: CSV users/logs

### Deploy/Infra
- [ ] Real DB: MySQL/Postgres
- [ ] Cache: Redis for aggregates

### Security/Perf
- [ ] Rate limit forms
- [ ] Env: APP_DEBUG=false production

## 📊 STATS (Updated)
- Controllers: 15+, Models: 10+, Views: 20+
- Migrations: 17 tables
- Live data: Seeded admin/test users, demo foods/plans/dietitians
- Files: 200+ total in project

## Issues Found
- so i have a problem i created a user account then i logged in the user page then i observed the data from my other account is the same with the new account i made instead of different accounts having different data. make sure this doesnt happen again. at the same time make sure when a new user logs in the websites data is clean meaning its all 0 since its a new account. because earlier as i said when i looged in a new account the data from my own account is the same on the new account even though everything in the website is clean