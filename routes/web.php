 <?php

use App\Http\Controllers\AdminActionController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\ComponentsController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\LaracastsController;
use App\Http\Controllers\PortalActionController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // User auth
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Admin auth (separate)
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login']);
    Route::get('/admin/register', [AdminAuthController::class, 'showRegister'])->name('admin.register');
    Route::post('/admin/register', [AdminAuthController::class, 'register']);
});

Route::post('/logout', function () {
    Auth::logout();

    return redirect('/');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [PortalController::class, 'user'])->name('portal.dashboard');
    Route::get('/food-log', fn () => app(PortalController::class)->user(request(), 'food-log'))->name('portal.food-log');
    Route::get('/calendar', fn () => app(PortalController::class)->user(request(), 'calendar'))->name('portal.calendar');
    Route::get('/insights', fn () => app(PortalController::class)->user(request(), 'insights'))->name('portal.insights');
    Route::get('/meal-plans', fn () => app(PortalController::class)->user(request(), 'meal-plans'))->name('portal.meal-plans');
    Route::get('/feedback', fn () => app(PortalController::class)->user(request(), 'feedback'))->name('portal.feedback');
    Route::get('/profile', fn () => app(PortalController::class)->user(request(), 'profile'))->name('portal.profile');

    Route::post('/profile', [PortalActionController::class, 'updateProfile'])->name('portal.profile.update');
    Route::post('/food-log', [PortalActionController::class, 'storeFoodLogEntry'])->name('portal.food-log.store');
    Route::delete('/food-log/{foodLogEntry}', [PortalActionController::class, 'destroyFoodLogEntry'])->name('portal.food-log.destroy');
    Route::delete('/calendar/planned-meals', [PortalActionController::class, 'destroyPlannedMealEntryFromRequest'])->name('portal.calendar.destroy-fallback');
    Route::post('/calendar/planned-meals', [PortalActionController::class, 'storePlannedMealEntries'])->name('portal.calendar.store');
    Route::delete('/calendar/planned-meals/{plannedMealEntry}', [PortalActionController::class, 'destroyPlannedMealEntry'])->name('portal.calendar.destroy');
    Route::post('/meal-plans', [PortalActionController::class, 'saveMealPlan'])->name('portal.meal-plans.store');
    Route::put('/meal-plans/{mealPlan}', [PortalActionController::class, 'saveMealPlan'])->name('portal.meal-plans.update');
    Route::post('/meal-plans/{mealPlan}/items', [PortalActionController::class, 'addMealPlanItem'])->name('portal.meal-plans.items.store');
    Route::delete('/meal-plans/{mealPlan}/items/{mealPlanItem}', [PortalActionController::class, 'destroyMealPlanItem'])->name('portal.meal-plans.items.destroy');
    Route::post('/meal-plans/{mealPlan}/activate', [PortalActionController::class, 'activateMealPlan'])->name('portal.meal-plans.activate');
    Route::post('/feedback/reply', [PortalActionController::class, 'storeFeedbackReply'])->name('portal.feedback.reply');
    Route::post('/feedback/{feedbackRequest}/read', [PortalActionController::class, 'markFeedbackAsRead'])->name('portal.feedback.read');
    Route::post('/consultations', [PortalActionController::class, 'storeConsultation'])->name('portal.consultations.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', fn () => app(PortalController::class)->admin(request(), 'dashboard'))->name('admin.dashboard');
    Route::get('/users', fn () => app(PortalController::class)->admin(request(), 'users'))->name('admin.users');
    Route::get('/dietitians', fn () => app(PortalController::class)->admin(request(), 'dietitians'))->name('admin.dietitians');
    Route::get('/feedback', fn () => app(PortalController::class)->admin(request(), 'feedback'))->name('admin.feedback');
    Route::get('/content', fn () => app(PortalController::class)->admin(request(), 'content'))->name('admin.content');
    Route::get('/analytics', fn () => app(PortalController::class)->admin(request(), 'analytics'))->name('admin.analytics');

    Route::put('/users/{userExperience}', [AdminActionController::class, 'updateUserExperience'])->name('admin.users.update');
    Route::post('/dietitians', [AdminActionController::class, 'storeDietitian'])->name('admin.dietitians.store');
    Route::put('/dietitians/{dietitian}', [AdminActionController::class, 'updateDietitian'])->name('admin.dietitians.update');
    Route::delete('/dietitians/{dietitian}', [AdminActionController::class, 'destroyDietitian'])->name('admin.dietitians.destroy');
    Route::post('/food-items', [AdminActionController::class, 'saveFoodItem'])->name('admin.food-items.store');
    Route::put('/food-items/{foodItem}', [AdminActionController::class, 'saveFoodItem'])->name('admin.food-items.update');
    Route::delete('/food-items/{foodItem}', [AdminActionController::class, 'destroyFoodItem'])->name('admin.food-items.destroy');
    Route::post('/meal-plans', [AdminActionController::class, 'saveMealPlan'])->name('admin.meal-plans.store');
    Route::put('/meal-plans/{mealPlan}', [AdminActionController::class, 'saveMealPlan'])->name('admin.meal-plans.update');
    Route::delete('/meal-plans/{mealPlan}', [AdminActionController::class, 'destroyMealPlan'])->name('admin.meal-plans.destroy');
    Route::post('/meal-plans/{mealPlan}/items', [AdminActionController::class, 'addMealPlanItem'])->name('admin.meal-plans.items.store');
    Route::delete('/meal-plans/{mealPlan}/items/{mealPlanItem}', [AdminActionController::class, 'destroyMealPlanItem'])->name('admin.meal-plans.items.destroy');
    Route::post('/feedback/{feedbackRequest}/complete', [AdminActionController::class, 'completeFeedback'])->name('admin.feedback.complete');
    Route::post('/consultations/{consultationRequest}/status', [AdminActionController::class, 'updateConsultationStatus'])->name('admin.consultations.status');
});

Route::get('/docs', [DocsController::class, 'index'])->name('docs');
Route::get('/components', [ComponentsController::class, 'index'])->name('components');
Route::get('/laracasts', [LaracastsController::class, 'index'])->name('laracasts');
Route::get('/deploy', [DeployController::class, 'index'])->name('deploy');
Route::get('/changelog', [ChangelogController::class, 'index'])->name('changelog');
