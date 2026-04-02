# COMPLETE NUTRIASSIST PROJECT TODO / ROADMAP **(Easy Explanations)**

## 🎯 **WHAT IS THIS APP?** *(Simple Summary)*
NutriAssist helps people track food, weight, BMI, and get diet tips. Users log meals, plan ahead, get AI insights. Admins manage content. Works out-of-box with demo data.

## 🏗️ **BACKEND** *(Server Code - What Does What?)*

### Controllers *(Traffic Cops - Handle Requests)*
```
PortalController.php *(Main User Pages)*
├── user() → Shows dashboard/food-log/etc. pages to logged-in users
│   **Explanation:** One method loads ANY user page by name, gathers all data needed
├── admin() → Admin dashboard/users/dietitians pages
├── portalData() → Magic function: Combines food logs + plans + feedback into page data
│   **Explanation:** Counts calories/protein, builds charts, finds active dietitian
└── currentExperience() → Finds/creates user "session profile" (even guests get one)

PortalActionController.php *(Save Buttons)*
├── updateProfile() → Saves height/weight → Auto-calcs BMI, saves history
│   **Explanation:** User types 175cm/72kg → BMI=23.5 stored
├── storeFoodLogEntry() → "Add Chicken 150g" to Breakfast log
└── More: Save plans, calendar, messages

AdminActionController.php *(Admin Save/Delete)*
├── saveFoodItem() → Admin adds "Broccoli: 35cal/3g protein"
└── completeFeedback() → Mark dietitian note as "done"

Auth Controllers
├── Login: Extra checkbox "Login as admin" (checks is_admin flag)
└── Register: "Admin signup?" checkbox + secret code `1000808790`
```

### Models *(Database Tables)*
```
User → Real Laravel users (email/password/is_admin)
UserExperience → Each browser tab gets profile (weight/BMI/age/plans) via session_key cookie
FoodLogEntry → "2026-04-02 Breakfast: Chicken 248cal 30g protein"
MealPlan → "Low Carb Week: 1800cal" with Breakfast/Lunch items
Dietitian → "Dr. Smith - Weight loss expert"
FeedbackRequest → Dietitian notes: "Add more veggies!"
```

### Routes *(URL Map)*
```
 /login → LoginController
 /portal/dashboard → PortalController.user('dashboard')
 /admin/users → PortalController.admin('users') *(admins only)*
 POST /profile → PortalActionController.updateProfile
```

### Database *(17 Tables)*
```
Users + is_admin column
UserExperience (profile per session)
FoodLogEntry, FoodItem, MealPlan/MealPlanItem, PlannedMealEntry
Dietitian, FeedbackRequest, ConsultationRequest
```

## 🎨 **FRONTEND** *(What Users See)*

### Main Pages *(portal.blade.php)*
```
Dashboard → Color cards (2000cal/150g protein), pie charts, weekly bars, today's meals list
Food Log → 4 cards (Breakfast/Lunch/Dinner/Snacks) + "Add" modal
Calendar → Month grid (click day → plan 4 meals)
Profile → **BMI Calculator** (live JS: height/weight → BMI score + scale), goal tracker bar
Insights → "Great protein!" cards, progress %, achievements badges, tips list
Feedback → Dietitian profile + message list + "Schedule consult" modals
```

### Admin Pages *(admin-portal.blade.php)*
```
Dashboard → User count pie (BMI normal/overweight), revenue charts, dietitian bars
Users table → Name/email/BMI/goal
Forms → Add food/meal-plan/dietitian
```

### Design
- **TailwindCSS** + custom particles background
- **CSS Charts** (conic-gradient pies, bar heights %)
- **JS Live** BMI calc (input change → update score/status)

## 🔧 **HOW IT WORKS** *(User Journey)*
```
1. Login (admin@example.com/password) → Demo seeds MealPlan/Food/Dietitian
2. Profile → Enter height/weight → BMI shows, goal bar fills
3. Food Log → Add "Eggs 70g 100cal" → Dashboard updates totals/charts
4. Meal Plans → Copy template → Edit items → Activate
5. Insights → AI reads your logs → "Good protein, add fiber"
6. Admin → /admin → Manage all users/food
```

## ⚠️ **CURRENT PROBLEMS** *(Fix These)*
```
Controllers missing `use Illuminate\Support\Facades\Auth;`
→ VSCode errors: auth()->check() unknown
**Fix:** Add to top of PortalController.php, PortalActionController.php
```

## 🚀 **IMPROVEMENTS** *(Future Features - Easy Explanations)*

### Backend *(Server)*
- [ ] **Fix Auth errors** → Add `use Auth;` line (5min)
- [ ] **Real AI** → Send logs to OpenAI: "Analyze this week's protein" ($5/mo)
- [ ] **Emails** → "New feedback from Dr. Smith" (setup Mailgun)
- [ ] **Tests** → `php artisan test` → Auto-check login/BMI calc
- [ ] **Mobile API** → JSON endpoints for React Native app

### Frontend *(Looks/Feel)*
- [ ] **Mobile fix** → Make calendar stack on phones (Tailwind `sm:` classes)
- [ ] **Real charts** → Chart.js instead of CSS bars (prettier zoom)
- [ ] **Dark mode** → Toggle button → CSS `dark:` classes
- [ ] **Food search** → Type "apple" → Autocomplete list

### Admin *(Power Tools)*
- [ ] **Edit users** → Click name → Change email/BMI
- [ ] **Analytics** → "Users grew 20% this week" graphs
- [ ] **Export** → Download users CSV for Excel

### Deploy *(Go Live)*
- [ ] **Vercel** → `npm run build` → Free hosting
- [ ] **Production DB** → MySQL + `APP_DEBUG=false`

**Everything explained! Green=done, add checkboxes as you work. Questions? Ask!**
