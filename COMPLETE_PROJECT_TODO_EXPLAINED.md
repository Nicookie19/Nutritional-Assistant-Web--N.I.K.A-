# COMPLETE NUTRIASSIST PROJECT TODO / ROADMAP **(Easy Explanations)**

## 🎯 **WHAT IS THIS APP?** *(Simple Summary)*
NutriAssist (N.I.K.A) helps people track food, weight, BMI, and get diet tips. Users log meals, plan ahead, get AI insights. Admins manage content. Works out-of-box with demo data.


## 🏗️ **BACKEND** *(Server Code - What Does What?)*

### Controllers *(Traffic Cops - Handle Requests)*
```
PortalController.php *(Main User Pages)*
├── user() → Shows dashboard/food-log/etc. pages to logged-in users
├── admin() → Admin dashboard/users/dietitians pages
├── portalData() → Combines food logs + plans + feedback into page data
│   Counts calories/protein, builds charts, finds active dietitian
└── currentExperience() → Finds/creates user "session profile"

PortalActionController.php *(Save Buttons)*
├── updateProfile() → Saves height/weight → Auto-calcs BMI, saves history
├── storeFoodLogEntry() → "Add Chicken 150g" to Breakfast log
├── storePlannedMealEntries() → Save meals to calendar day
├── saveMealPlan() / addMealPlanItem() → Build/edit meal plans
├── storeFeedbackReply() → Send message to dietitian
├── storeConsultation() → Request a consultation
└── destroy*() → Delete food log entries, planned meals, plan items

AdminActionController.php *(Admin Save/Delete)*
├── saveFoodItem() → Admin adds "Broccoli: 35cal/3g protein"
├── storeDietitian() / updateDietitian() / destroyDietitian()
├── saveMealPlan() / addMealPlanItem() → Template plan management
├── updateUserExperience() → Admin edits a user's profile
├── completeFeedback() → Mark dietitian note as "done"
└── updateConsultationStatus() → Change consult to in-progress/completed

Auth Controllers
├── LoginController → Blocks admins from user login (throws ValidationException)
├── RegisterController → Admin checkbox + secret code `1000808790`
└── AdminAuthController → Separate /admin/login and /admin/register routes
```

### Models *(Database Tables)*
```
User → Real Laravel users (email/password/is_admin)
UserExperience → Per-user profile (weight/BMI/age/plans) linked by user_id + session_key cookie
FoodLogEntry → "2026-04-02 Breakfast: Chicken 248cal 30g protein"
MealPlan → "Low Carb Week: 1800cal" (is_template=true for admin templates)
MealPlanItem → Individual items inside a plan (meal_slot, item_name, serving_label)
PlannedMealEntry → Calendar entries (scheduled_date, meal_slot, food_name, grams)
Dietitian → "Dr. Smith - Weight loss expert" (name, specialization, rating, status)
FeedbackRequest → Dietitian notes/messages (status: pending/in-progress/completed, is_read)
ConsultationRequest → Scheduled consult requests (preferred_date, note, status)
FoodItem → Admin-managed food database (name, calories, protein, carbs, fat, is_active)
```

### Routes *(URL Map)*
```
Guest:  /login, /register, /admin/login, /admin/register
Auth:   / (dashboard), /food-log, /calendar, /insights, /meal-plans, /feedback, /profile
        POST /profile, POST /food-log, DELETE /food-log/{id}
        POST/DELETE /calendar/planned-meals, POST /meal-plans, PUT /meal-plans/{id}
        POST /feedback/reply, POST /consultations
Admin:  /admin (dashboard), /admin/users, /admin/dietitians, /admin/feedback
        /admin/content, /admin/analytics
        PUT /admin/users/{id}, POST/PUT/DELETE /admin/dietitians, /admin/food-items, /admin/meal-plans
        POST /admin/feedback/{id}/complete, POST /admin/consultations/{id}/status
Public: /docs, /components, /laracasts, /deploy, /changelog
```

### Database *(17 Tables)*
```
users (+ is_admin, address, age, gender, date_of_birth)
user_experiences (session_key, user_id, active_dietitian_id, active_meal_plan_id, bmi_history JSON)
food_log_entries, food_items
meal_plans (is_template, is_active, daily_calories, tags JSON)
meal_plan_items (meal_slot, item_name, serving_label, sort_order)
planned_meal_entries (scheduled_date, meal_slot, grams, calories, protein)
dietitians (specialization, experience_years, rating, status)
feedback_requests (status, is_read, recommendations JSON, submitted_on)
consultation_requests (preferred_date, note, status)
```


## 🎨 **FRONTEND** *(What Users See)*

### Main Pages *(portal.blade.php)*
```
Dashboard → Color stat cards (calories/protein/meals/unread notes), pie charts, weekly bars, today's meals
Food Log → 4 cards (Breakfast/Lunch/Dinner/Snacks) + "Add" modal (food picker or manual entry)
Calendar → Month grid (click day → plan 4 meals with grams/calories/protein)
Profile → BMI Calculator (live JS: height/weight → BMI score + scale), goal tracker bar, history table
Insights → Dynamic highlights (calorie/protein/logging analysis), performance cards, achievements badges
Feedback → Dietitian profile card + message list + "Schedule consult" + "Send message" modals
Meal Plans → Template cards + custom plan builder (add items per meal slot)
```

### Admin Pages *(admin-portal.blade.php)*
```
Dashboard → User count, active users this week, feedback turnaround, meal plan adoption
Users table → Name/email/BMI/goal + inline edit form
Dietitians → CRUD table + add/edit form
Feedback → Feedback requests + consultation requests with status controls
Content → Food items table + meal plan template builder
Analytics → BMI distribution pie, goals distribution pie (conic-gradient CSS)
```

### Design
- TailwindCSS + custom particles background
- CSS Charts (conic-gradient pies, bar heights %)
- JS Live BMI calc (input change → update score/status)
- Vite for asset bundling


## 🔧 **HOW IT WORKS** *(User Journey)*
```
1. Register/Login → UserExperience created (linked to user_id + session_key cookie)
2. Demo seed runs → FoodItems, Dietitian, template MealPlan seeded if DB is empty
3. Profile → Enter height/weight → BMI calculated, stored in bmi_history JSON array (last 4 entries)
4. Food Log → Add "Eggs 70g 100cal" → Dashboard updates totals/charts
5. Meal Plans → Copy template → Edit items → Activate (sets active_meal_plan_id)
6. Calendar → Click day → Plan 4 meals → Saved as PlannedMealEntry rows
7. Insights → Reads last 7 days of logs → Generates calorie/protein/consistency highlights
8. Admin → /admin → Manage all users/food/dietitians/feedback
```


## ✅ **COMPLETED FEATURES**
- [x] Full user portal with 7 pages (dashboard, food-log, calendar, insights, meal-plans, feedback, profile)
- [x] Admin dashboard with 6 pages (dashboard, users, dietitians, feedback, content, analytics)
- [x] Session-based profiles (UserExperience) with BMI history tracking
- [x] Food logging with meal slots, calorie/protein/carbs/fat tracking
- [x] Meal plan templates + user custom plans + activation flow
- [x] Calendar meal planning (PlannedMealEntry per day/slot)
- [x] Dietitian feedback + consultation request system
- [x] Demo data seeding (PortalDemoSeeder)
- [x] Separate admin auth flow (/admin/login, /admin/register with secret code)
- [x] EnsureUserIsAdmin middleware protecting /admin/* routes
- [x] Responsive Tailwind UI, JS BMI calculator, CSS conic-gradient charts
- [x] 17 DB tables, factories for all models, form request validation
- [x] Admin CRUD for food items, meal plans, dietitians, user profiles
- [x] Feedback complete + consultation status update from admin


## ⚠️ **KNOWN BUGS / ISSUES**

### 🔴 Critical
- [ ] **Data isolation bug** — New user accounts see data from other sessions.
  **Root cause:** `currentExperience()` uses `session_key` cookie which can persist across accounts.
  When a new user logs in on the same browser, the old cookie may match a different UserExperience.
  **Fix:** On login, clear the `portal_session_key` session/cookie and always resolve UserExperience
  by `user_id` first for authenticated users. Never fall back to session_key for auth'd users.

- [ ] **BMI history keeps growing** — `bmi_history` is sliced to last 4 entries for users but last 6
  for admin edits. Should be consistent (pick one limit and apply everywhere).

### 🟡 Medium
- [ ] **Debug `Log::info()` left in production code** — `destroyPlannedMealEntry()` and
  `destroyPlannedMealEntryFromRequest()` both have `Log::info()` calls that should be removed.

- [ ] **`destroyPlannedMealEntryFromRequest()` is a workaround** — The fallback DELETE route
  (`DELETE /calendar/planned-meals`) exists because the named route delete wasn't working.
  Should be cleaned up once the primary route is confirmed working.

- [ ] **Food log entries always use `today()`** — `storeFoodLogEntry()` hardcodes `entry_date`
  to today. Users can't log food for a past date.

- [ ] **Macro goals are hardcoded** — Calorie goal (2000) and protein goal (150g) are hardcoded
  in `portalData()` and `insightsData()`. Should come from the user's profile/goal settings.

- [ ] **`activateMealPlan()` copies template with timestamp in name** — Creates names like
  "Low Carb Copy 1423". Not user-friendly; should prompt for a name or use a cleaner default.

### 🟢 Minor
- [ ] **Admin register code is hardcoded** — `'1000808790'` is in `RegisterController.php`.
  Should be in `.env` as `ADMIN_REGISTER_CODE`.

- [ ] **`User` model missing `hasOne` for UserExperience** — Has `experiences()` (hasMany) but
  most users only have one. A `experience()` hasOne would be cleaner.

- [ ] **`UserExperience` has no `consultationRequests()` relation** — ConsultationRequest queries
  are done manually with `where('user_experience_id', ...)` instead of using a relation.


## 🚀 **IMPROVEMENTS** *(Future Features)*

### Backend *(High Priority)*
- [ ] **Fix data isolation** → On login, regenerate session and clear portal_session_key cookie
- [ ] **Move admin code to .env** → `ADMIN_REGISTER_CODE=1000808790`
- [ ] **Remove debug Log::info calls** → Clean up PortalActionController delete methods
- [ ] **User-specific macro goals** → Store calorie/protein targets on UserExperience, use in portalData
- [ ] **Tests** → Expand feature tests for food log CRUD, meal plan activation, calendar planning
  (UP-002 food log add/delete is currently marked FAIL in test coverage doc)

### Backend *(Medium Priority)*
- [ ] **Email notifications** → Send email when dietitian posts feedback or consult is confirmed
- [ ] **Pagination** → Feedback list, food log history, admin users table (all load everything now)
- [ ] **Password reset** → No forgot-password flow exists yet
- [ ] **Email verification** → `MustVerifyEmail` is commented out on User model
- [ ] **Past-date food logging** → Allow users to log entries for previous days
- [ ] **Policies** → Replace manual `abort(403)` checks with Laravel Policies for FoodLogEntry,
  MealPlan, PlannedMealEntry, FeedbackRequest
- [ ] **API endpoints** → JSON routes for a potential mobile app (versioned under /api/v1)

### Frontend *(Looks/Feel)*
- [ ] **Mobile calendar** → Calendar month grid breaks on small screens (needs Tailwind `sm:` rework)
- [ ] **Real charts** → Replace CSS conic-gradient pies and bar heights with Chart.js
- [ ] **Food search autocomplete** → Type "apple" in food log modal → filter food items live
- [ ] **Dark mode** → Toggle button → CSS `dark:` classes
- [ ] **BMI NaN guard** → JS BMI calc shows NaN when fields are empty; show 0 or "—" instead
- [ ] **kg/lbs toggle** → Profile form currently only accepts kg/cm
- [ ] **Profile photo upload** → Store avatar on UserExperience

### Admin *(Power Tools)*
- [ ] **Delete users** → No user delete exists; admin can only edit
- [ ] **Impersonate users** → Admin clicks user → logs in as them to debug issues
- [ ] **Export CSV** → Download users/food logs as CSV for Excel
- [ ] **Rich text editor** → Meal plan description field is plain text input
- [ ] **Bulk food import** → Upload CSV of food items instead of adding one by one

### Deploy *(Go Live)*
- [ ] **Switch to MySQL/Postgres** → Currently SQLite (fine for dev, not production)
- [ ] **Set APP_DEBUG=false** → Currently true in .env
- [ ] **Redis cache** → Cache `portalData()` aggregates (currently recalculated on every page load)
- [ ] **Rate limiting** → Add throttle middleware to login, register, and form POST routes
- [ ] **Queue jobs** → Move email sending to queued jobs (currently would be synchronous)


## 📊 **PROJECT STATS**
- Controllers: 15 (including auth)
- Models: 10
- Views: ~20 Blade files
- Migrations: 17 tables
- Form Requests: 9 validation classes
- Factories: 10 (all models covered)
- Routes: ~40 named routes
- Test coverage: Basic auth + portal pages passing; food log CRUD tests failing (UP-002)
