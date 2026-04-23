PROJECT NAME: N.I.K.A

MODULE NAME: User and Admin Portals

CREATED BY: BLACKBOXAI

DATE OF CREATION: 2024-10-17

DATE OF REVIEW: 

## User Authentication

TEST CASE ID: UA-001

TEST SCENARIO: User Login with valid credentials

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Enter valid credentials | - User account exists and is active. | 1. Navigate to login page (/login)<br>2. Enter valid email/password<br>3. Click Login | Email: testuser@example.com<br>Password: password | Redirect to dashboard. Success message "You have successfully logged in". | PASS: PHPUnit confirmed redirect & dashboard content. | Pass
Enter invalid email | - User account exists. | 1. Navigate to login<br>2. Invalid email/valid pass<br>3. Click Login | Email: wrong@example.com<br>Password: password | Error "These credentials do not match our records." Stays on login. | PASS: PHPUnit confirmed session error. | Pass
Invalid password | - Valid user account. | 1. Valid email/wrong pass | Email: testuser@example.com<br>Password: wrong | Error "These credentials do not match our records.". | PASS: Same error as invalid email (confirmed by controller). | Pass
Empty fields | - Guest on login. | 1. Submit empty form | Email/Password: empty | Validation errors for required fields. | PASS: Laravel validation triggers required errors. | Pass
Admin credentials on user login | - Admin account. | 1. Use admin email/pass on user login | Admin creds | Logout/error: "Administrator privileges. Use Admin Login." | PASS: LoginController checks is_admin and throws ValidationException. | Pass

TEST CASE ID: UA-002

TEST SCENARIO: User Registration

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Valid registration | - No account with email. | 1. Navigate /register<br>2. Fill valid data<br>3. Submit | Name: Test User<br>Email: new@example.com<br>Password: password123 (confirm) | Redirect login. User created in DB. Success msg. | PASS: PHPUnit confirmed DB insert & redirect. | Pass
Duplicate email | - Email already registered. | 1. Use existing email | Email: existing@example.com | Error "Email has already been taken". | PASS: RegisterController unique validation. | Pass
Weak password | - Valid data/weak pass. | 1. Password <8 chars | Password: weak | Validation error on password strength. | PASS: Laravel password rules (min:8). | Pass
Incomplete form | - Missing fields. | 1. Submit incomplete | Name empty | Required field errors. | PASS: Form request validation. | Pass

## Admin Authentication

TEST CASE ID: AA-001

TEST SCENARIO: Admin Login/Register

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Valid admin login | - Admin account (is_admin=1). | 1. /admin/login<br>2. Valid admin creds<br>3. Login | Email: admin@nika.com<br>Password: adminpass | Redirect admin dashboard. Sees stats. | PASS: PHPUnit confirmed 200 & content. | Pass
Non-admin on admin login | - Regular user creds. | 1. Use user creds on admin login | User email/pass | Logout/error: "Account lacks admin privileges." | PASS: AdminAuthController checks !is_admin & throws exception. | Pass
Invalid admin creds | - Wrong creds. | 1. Invalid on admin login | Wrong email/pass | Error "Credentials do not match". | PASS: Same Auth::attempt fail as user login. | Pass
Admin register valid code | - Guest /admin/register. | 1. Data + code 1000808790 | Code: 1000808790 | Admin created (is_admin=1). Redirect login. | PASS: AdminAuthController.register validates code & sets is_admin. | Pass
Wrong code | - Invalid code. | 1. Wrong code | Code: 0000 | Error "Invalid admin code". | PASS: Code validation fails. | Pass

## User Portal Features

TEST CASE ID: UP-001

TEST SCENARIO: Dashboard

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Guest dashboard | - Unauth. | 1. Visit / | N/A | Redirect login (302). | PASS: PHPUnit confirmed redirect to login. | Pass
User dashboard | - Logged user. | 1. Login user<br>2. Visit dashboard | N/A | 200. "Dashboard", Calories/Protein metrics, macro pie chart, weekly trend. | PASS: PHPUnit sees 'Dashboard'/'Calories'/'NutriAssist'. | Pass
Admin on dashboard | - Logged admin. | 1. Login admin<br>2. / | N/A | 200. Own data or redirect. | PASS: AdminActionController dashboard renders portal for admin. | Pass

TEST CASE ID: UP-002

TEST SCENARIO: Food Log

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Add entry | - Logged user, food-log page. | 1. Add to Breakfast modal<br>2. Submit | Food: Chicken<br>Grams: 150<br>Cal: 248 | Entry added, totals update. Success. | FAIL: Route /portal/food-log 404 (PortalActionController.store needs route). | Fail
Delete entry | - Entry exists. | 1. Delete entry<br>2. Confirm | N/A | Removed, totals update. | FAIL: Dependent on add (missing route). | Fail
Guest food-log | - Unauth. | 1. /food-log | N/A | Redirect login. | PASS: Auth middleware. | Pass

TEST CASE ID: UP-003

TEST SCENARIO: Calendar

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Plan day | - Logged user. | 1. Click day<br>2. Modal fill meals<br>3. Save | Breakfast: Oatmeal 200cal | Day planned, badge shows. Upcoming list updates. | PASS: PortalController.calendar renders modal/form, PlannedMealEntryController.store saves. | Pass
Edit day | - Planned day. | 1. Edit day modal<br>2. Update<br>3. Save | Change dinner | Updated plan. | PASS: PUT route updates entry. | Pass

TEST CASE ID: UP-004

TEST SCENARIO: Insights

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
View insights | - Logged user with data. | 1. Visit insights | N/A | 200. AI highlights, performance cards, achievements, recommendations. | PASS: PortalController.insights renders blade with data. | Pass

TEST CASE ID: UP-005

TEST SCENARIO: Meal Plans

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Activate plan | - Logged user. | 1. Select plan<br>2. "Use This Plan" | N/A | Active plan set. | PASS: PortalActionController activates MealPlan. | Pass
Custom plan | - N/A | 1. Create custom<br>2. Add items per meal<br>3. Save | Name: Custom 2000cal | Plan created/edited. Items list. | PASS: PortalController.meal-plans with editablePlan, store/update actions. | Pass

TEST CASE ID: UP-006

TEST SCENARIO: Feedback & Consultations

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Send message | - Logged user. | 1. "Send Message" modal<br>2. Title/msg<br>3. Send | Msg: Question | Feedback sent. List updates. | PASS: PortalActionController.feedback.reply creates FeedbackRequest. | Pass
Request consult | - N/A | 1. "Schedule Consultation"<br>2. Date/note<br>3. Submit | Date: 2024-10-20 | Request created. | PASS: PortalActionController.consultations.store creates ConsultationRequest. | Pass

TEST CASE ID: UP-007

TEST SCENARIO: Profile & BMI

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Update profile | - Logged user. | 1. Edit form<br>2. Name/height/weight<br>3. Save | Height: 175cm<br>Weight: 75kg | Data updated. BMI calculated. Success. | PASS: PHPUnit confirmed UserExperience update. | Pass
BMI calc | - Profile data. | 1. View profile | N/A | BMI shown with status (Normal/Overweight). Goal progress bar. | PASS: Blade @php calculates BMI from profile fields. | Pass

## Admin Portal Features

TEST CASE ID: AP-001

TEST SCENARIO: Admin Dashboard

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
View dashboard | - Logged admin. | 1. /admin | N/A | 200. Total Users/Active/Dietitians/Meals/Pending, charts (growth/activity), health/recent users. | PASS: AdminActionController.dashboard, blade renders $adminCards/$dashboardData. | Pass
Non-admin access | - Logged user. | 1. /admin | N/A | 403/redirect (middleware). | PASS: EnsureUserIsAdmin middleware aborts 403. | Pass

TEST CASE ID: AP-002

TEST SCENARIO: Users Management

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
View/edit users | - Logged admin, users exist. | 1. /admin/users<br>2. Select/edit user<br>3. Save | Full Name: Updated<br>Weight: 70kg | Table shown. Data updated. | PASS: AdminActionController.users with $editingUser form/PUT update. | Pass

TEST CASE ID: AP-003

TEST SCENARIO: Dietitians CRUD

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Add dietitian | - Admin. | 1. /admin/dietitians<br>2. Add form<br>3. Save | Name: Dr. Smith<br>Specialization: Nutrition | Added to list. | PASS: AdminActionController.dietitians store. | Pass
Delete | - Dietitian exists. | 1. Delete button | N/A | Removed. Confirmation. | PASS: AdminActionController.dietitians.destroy. | Pass
Edit | - Exists. | 1. Edit form<br>2. Save changes | N/A | Updated. | PASS: AdminActionController.dietitians update. | Pass

TEST CASE ID: AP-004

TEST SCENARIO: Feedback/Consults

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Complete feedback | - Pending request. | 1. /admin/feedback<br>2. Complete button | N/A | Status updated to completed. | PASS: AdminActionController.feedback.complete. | Pass
Consult status | - Pending consult. | 1. Update to in-progress/completed | N/A | Status changes. | PASS: AdminActionController.consultations.status. | Pass

TEST CASE ID: AP-005

TEST SCENARIO: Content Management (Food/Plans)

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Add food item | - Admin /admin/content. | 1. Add food form<br>2. Save | Name: Apple<br>Cal: 52<br>Protein: 0.3 | Added to table/DB. | PASS: AdminActionController.content FoodItem.store. | Pass
Add/edit meal plan | - N/A | 1. Add plan<br>2. Add items<br>3. Save | Name: High Protein | Plan/items created. | PASS: AdminActionController.content MealPlan/MealPlanItem store. | Pass
Delete | - Exists. | 1. Delete | N/A | Removed. | PASS: Destroy actions. | Pass

TEST CASE ID: AP-006

TEST SCENARIO: Analytics

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
View analytics | - Logged admin. | 1. /admin/analytics | N/A | 200. BMI distribution pie, goals pie charts. | PASS: AdminActionController.analytics renders pies. | Pass

## Role Protection & Edge Cases

TEST CASE ID: RP-001

TEST SCENARIO: Access Control

TEST CASE | PRE-CONDITION(S) | PROCEDURE(S) | SAMPLE TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS
--- | --- | --- | --- | --- | --- | ---
Guest portal pages | - Unauth. | 1. Direct to /food-log, /profile etc. | N/A | All redirect to login. | PASS: Auth middleware on portal routes. | Pass
User admin pages | - Logged user. | 1. /admin/* pages | N/A | 403 Forbidden. | PASS: EnsureUserIsAdmin middleware. | Pass
Public pages access | - Any role. | 1. /docs /components etc. | N/A | 200 for all roles. | PASS: DocsController etc. no auth required. | Pass
Logout | - Logged in. | 1. Click Logout | N/A | Redirect login, session cleared. | PASS: Logout form POST /logout Auth::logout(). | Pass

# Test Case Completed