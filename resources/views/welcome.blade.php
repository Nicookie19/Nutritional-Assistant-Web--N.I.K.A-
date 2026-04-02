@extends('layouts.app')

@section('title', 'NutriAssist Dashboard')

@section('content')
<header class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between gap-8">
        <div class="flex items-center gap-3">
            <div class="rounded-full bg-emerald-500 p-2 text-white shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C9.65 2 8 3.4 8 5s1.65 3 4 3 4-1.4 4-3-1.65-3-4-3z"/><path d="M12 8c-4.4 0-8 2.2-8 4.9 0 5.8 7.1 9.2 7.5 9.4a1.5 1.5 0 0 0 1 0c.4-.2 7.5-3.6 7.5-9.4C20 10.2 16.4 8 12 8zm0 12.2C9.2 18.5 4 15.7 4 12.9 4 11 7.6 9.7 12 9.7s8 1.3 8 3.2c0 2.8-5.2 5.6-8 6.6z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">NutriAssist</h1>
                <p class="text-xs text-slate-500">Nutritional Intelligence</p>
            </div>
        </div>

<nav class="hidden md:flex items-center gap-4 text-sm font-medium text-slate-700">
            <a href="{{ route('login') }}" class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-xl font-semibold hover:bg-emerald-200 transition-all">👤 User Login</a>
            <a href="{{ route('admin.login') }}" class="bg-orange-100 text-orange-700 px-4 py-2 rounded-xl font-semibold hover:bg-orange-200 transition-all">👨‍💼 Admin Login</a>
        </nav>
    </div>
</header>

<main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-medium text-slate-500 mb-2">Calories</h2>
            <p class="text-3xl font-bold text-slate-900">851 <span class="text-slate-500 text-sm">/ 2000</span></p>
            <div class="h-2 mt-3 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-orange-500" style="width:42%"></div></div>
            <p class="mt-2 text-xs text-orange-500">1,149 remaining</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-medium text-slate-500 mb-2">Protein</h2>
            <p class="text-3xl font-bold text-slate-900">68 <span class="text-slate-500 text-sm">/ 150g</span></p>
            <div class="h-2 mt-3 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-emerald-500" style="width:45%"></div></div>
            <p class="mt-2 text-xs text-emerald-600">82 g remaining</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-medium text-slate-500 mb-2">Carbs</h2>
            <p class="text-3xl font-bold text-slate-900">94 <span class="text-slate-500 text-sm">/ 225g</span></p>
            <div class="h-2 mt-3 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-amber-500" style="width:42%"></div></div>
            <p class="mt-2 text-xs text-amber-600">131 g remaining</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-medium text-slate-500 mb-2">Fat</h2>
            <p class="text-3xl font-bold text-slate-900">26 <span class="text-slate-500 text-sm">/ 67g</span></p>
            <div class="h-2 mt-3 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-red-400" style="width:39%"></div></div>
            <p class="mt-2 text-xs text-red-500">41 g remaining</p>
        </article>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <article class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-semibold text-slate-900">Food Log</h2>
                <button class="px-4 py-2 text-sm text-white bg-emerald-500 rounded-lg hover:bg-emerald-600">Add Food</button>
            </div>

            <div class="space-y-3">
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <div class="flex items-center justify-between text-sm text-slate-600 mb-2"><span>Breakfast</span><span>284 cal · 10g protein</span></div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-slate-800 font-medium">Oatmeal <span>0.5 × 100g · 195 cal</span></div>
                        <div class="flex items-center justify-between text-slate-800 font-medium">Banana <span>1 × 1 medium · 89 cal</span></div>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <div class="flex items-center justify-between text-sm text-slate-600 mb-2"><span>Lunch</span><span>394 cal · 52g protein</span></div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-slate-800 font-medium">Grilled Chicken Breast <span>1.5 × 100g · 248 cal</span></div>
                        <div class="flex items-center justify-between text-slate-800 font-medium">Brown Rice <span>1 × 100g · 112 cal</span></div>
                        <div class="flex items-center justify-between text-slate-800 font-medium">Broccoli <span>1 × 100g · 34 cal</span></div>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <div class="flex items-center justify-between text-sm text-slate-600 mb-2"><span>Snacks</span><span>174 cal · 6g protein</span></div>
                    <div class="text-slate-800 font-medium">Almonds <span>0.3 × 100g · 174 cal</span></div>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center text-slate-500">No items logged yet for Dinner</div>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Micronutrients & Vitamins</h2>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-sky-100 bg-sky-50 p-3"><p class="text-xs uppercase text-sky-700">Vitamin D</p><p class="text-2xl font-bold">12 mcg</p><p class="text-xs text-slate-500">80% of daily goal</p></div>
                <div class="rounded-lg border border-emerald-100 bg-emerald-50 p-3"><p class="text-xs uppercase text-emerald-700">Vitamin C</p><p class="text-2xl font-bold">85 mg</p><p class="text-xs text-slate-500">94% of daily goal</p></div>
                <div class="rounded-lg border border-amber-100 bg-amber-50 p-3"><p class="text-xs uppercase text-amber-700">Iron</p><p class="text-2xl font-bold">14 mg</p><p class="text-xs text-slate-500">78% of daily goal</p></div>
                <div class="rounded-lg border border-violet-100 bg-violet-50 p-3"><p class="text-xs uppercase text-violet-700">Calcium</p><p class="text-2xl font-bold">950 mg</p><p class="text-xs text-slate-500">95% of daily goal</p></div>
                <div class="rounded-lg border border-amber-100 bg-amber-50 p-3"><p class="text-xs uppercase text-amber-700">Vitamin A</p><p class="text-2xl font-bold">720 mcg</p><p class="text-xs text-slate-500">80% of daily goal</p></div>
                <div class="rounded-lg border border-rose-100 bg-rose-50 p-3"><p class="text-xs uppercase text-rose-700">Vitamin B12</p><p class="text-2xl font-bold">2.1 mcg</p><p class="text-xs text-slate-500">88% of daily goal</p></div>
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-3"><p class="text-xs uppercase text-blue-700">Magnesium</p><p class="text-2xl font-bold">340 mg</p><p class="text-xs text-slate-500">85% of daily goal</p></div>
                <div class="rounded-lg border border-pink-100 bg-pink-50 p-3"><p class="text-xs uppercase text-pink-700">Zinc</p><p class="text-2xl font-bold">9 mg</p><p class="text-xs text-slate-500">82% of daily goal</p></div>
            </div>
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Insights</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <article class="rounded-lg border border-emerald-100 bg-emerald-50 p-4">
                <h3 class="font-semibold text-emerald-700">Great protein intake!</h3>
                <p class="text-sm text-slate-700">You've consistently met your protein goals this week.</p>
            </article>
            <article class="rounded-lg border border-amber-100 bg-amber-50 p-4">
                <h3 class="font-semibold text-amber-700">Low fiber intake</h3>
                <p class="text-sm text-slate-700">Consider adding more vegetables and whole grains to your meals.</p>
            </article>
            <article class="rounded-lg border border-blue-100 bg-blue-50 p-4">
                <h3 class="font-semibold text-blue-700">Hydration reminder</h3>
                <p class="text-sm text-slate-700">Don't forget to drink 8 glasses of water daily.</p>
            </article>
            <article class="rounded-lg border border-emerald-100 bg-emerald-50 p-4">
                <h3 class="font-semibold text-emerald-700">Calorie balance</h3>
                <p class="text-sm text-slate-700">You're maintaining a healthy caloric intake pattern.</p>
            </article>
        </div>
    </section>
</main>
@endsection

