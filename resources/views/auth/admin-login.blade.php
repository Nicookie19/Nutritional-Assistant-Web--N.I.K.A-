@extends('layouts.app')

@section('title', 'Admin Sign in - NutriAssist')

@section('body_class', '')

@section('content')
<div class="app-particles-background app-particles-background--admin">
    <div class="app-particles-background__layer app-particles-background__layer--one" style="background-image: radial-gradient(circle, rgba(249, 115, 22, 0.58) 0 2.5px, transparent 2.9px), radial-gradient(circle, rgba(251, 146, 60, 0.48) 0 2px, transparent 2.4px);"></div>
    <div class="app-particles-background__layer app-particles-background__layer--two" style="background-image: radial-gradient(circle, rgba(234, 88, 12, 0.5) 0 2.9px, transparent 3.3px), radial-gradient(circle, rgba(251, 146, 60, 0.4) 0 1.7px, transparent 2.1px);"></div>
    <div class="app-particles-background__layer app-particles-background__layer--three" style="background-image: radial-gradient(circle, rgba(249, 115, 22, 0.36) 0 2.1px, transparent 2.5px), radial-gradient(circle, rgba(251, 146, 60, 0.34) 0 2.7px, transparent 3.1px);"></div>
</div>

<header class="bg-orange-600 border-b border-orange-700 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between gap-8">
        <div class="flex items-center gap-3">
            <div class="rounded-full bg-white/20 p-2 text-white shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C9.65 2 8 3.4 8 5s1.65 3 4 3 4-1.4 4-3-1.65-3-4-3z"/><path d="M12 8c-4.4 0-8 2.2-8 4.9 0 5.8 7.1 9.2 7.5 9.4a1.5 1.5 0 0 0 1 0c.4-.2 7.5-3.6 7.5-9.4C20 10.2 16.4 8 12 8zm0 12.2C9.2 18.5 4 15.7 4 12.9 4 11 7.6 9.7 12 9.7s8 1.3 8 3.2c0 2.8-5.2 5.6-8 6.6z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">NutriAssist Admin</h1>
                <p class="text-xs text-orange-100">Management Dashboard</p>
            </div>
        </div>
    </div>
</header>

<main class="relative z-0 flex-1 px-4 py-16 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <div class="max-w-2xl mx-auto space-y-8">
        <div class="rounded-3xl bg-white p-12 shadow-2xl border border-slate-100">
            <h2 class="text-3xl font-bold text-slate-900 mb-2 text-center">
                Admin Sign In
            </h2>
            <p class="text-slate-600 text-lg text-center mb-10 max-w-md mx-auto">
                Access the NutriAssist management dashboard
            </p>
            <form method="POST" action="{{ route('admin.login') }}" class="space-y-8">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-900 mb-3">
                            Email Address <span class="text-orange-600">*</span>
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" 
                               class="w-full px-5 py-4 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm transition-all duration-200 text-lg @error('email') border-red-400 ring-1 ring-red-400 @enderror" 
                               placeholder="admin@example.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_code" class="block text-sm font-semibold text-slate-900 mb-3">
                            Admin Code <span class="text-orange-600">*</span>
                        </label>
                        <input id="admin_code" name="admin_code" type="text" required value="{{ old('admin_code') }}" 
                               class="w-full px-5 py-4 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm transition-all duration-200 text-lg @error('admin_code') border-red-400 ring-1 ring-red-400 @enderror" 
                               placeholder="1000808790">
                        @error('admin_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-900 mb-3">
                        Password <span class="text-orange-600">*</span>
                    </label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="w-full pr-12 pl-5 py-4 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm transition-all duration-200 text-lg @error('password') border-red-400 ring-1 ring-red-400 @enderror pr-12" 
                               placeholder="••••••••">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password')">
                            <svg id="eye-icon" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg id="eye-slash-icon" class="h-5 w-5 text-slate-400 hover:text-slate-600 transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-5 w-5 text-orange-600 border-slate-300 rounded focus:ring-orange-500">
                        <span class="ml-3 block text-sm font-medium text-slate-700">Remember me</span>
                    </label>
                    <div class="flex justify-end">
                        <a href="#" class="text-sm font-medium text-orange-600 hover:text-orange-500">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <button type="submit" class="w-full bg-orange-600 border border-transparent rounded-2xl px-6 py-5 text-xl font-bold text-white shadow-[0_10px_20px_-5px_rgba(234,88,12,0.4)] hover:bg-orange-700 focus:outline-none focus:ring-4 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-300 hover:shadow-[0_15px_30px_-5px_rgba(234,88,12,0.5)] hover:-translate-y-1">
                    Sign In as Admin
                </button>

                <div class="pt-4 mt-4 border-t border-slate-200">
                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full text-center bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 hover:bg-slate-100 transition-all">
                        <span>👤</span> Back to User Portal
                    </a>
                </div>
            </form>
        </div>
        <div class="text-center space-y-2">
            <p class="text-sm text-slate-600">
                Don't have admin access? <a href="{{ route('register') }}" class="font-semibold text-orange-600 hover:text-orange-500 transition-colors duration-200">Contact support</a>
            </p>
        </div>
    </div>
</main>

<script>
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const eyeIcon = document.getElementById('eye-icon');
    const eyeSlashIcon = document.getElementById('eye-slash-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
    } else {
        passwordField.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeSlashIcon.classList.add('hidden');
    }
}
</script>
@endsection
