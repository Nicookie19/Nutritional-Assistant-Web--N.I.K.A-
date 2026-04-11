@extends('layouts.app')

@section('title', 'Sign in - NutriAssist')

@section('body_class', '')

@section('content')
<div class="app-particles-background">
    <div class="app-particles-background__layer app-particles-background__layer--one"></div>
    <div class="app-particles-background__layer app-particles-background__layer--two"></div>
    <div class="app-particles-background__layer app-particles-background__layer--three"></div>
</div>

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
    </div>
</header>

<main class="relative z-0 flex-1 px-4 py-16 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <div class="max-w-2xl mx-auto space-y-8">
        <div class="rounded-2xl bg-white p-10 shadow-xl border border-slate-200">
            <h2 class="text-3xl font-bold text-slate-900 mb-2 text-center">
                Welcome back
            </h2>
            <p class="text-slate-600 text-lg text-center mb-10 max-w-md mx-auto">
                Sign in to your NutriAssist account
            </p>
            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-900 mb-3">
                        Email Address <span class="text-emerald-600">*</span>
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" 
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm transition-all duration-200 text-lg @error('email') border-red-400 ring-1 ring-red-400 @enderror" 
                           placeholder="john@example.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-900 mb-3">
                        Password <span class="text-emerald-600">*</span>
                    </label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="w-full pr-12 pl-5 py-4 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm transition-all duration-200 text-lg @error('password') border-red-400 ring-1 ring-red-400 @enderror pr-12" 
                               placeholder="••••••••">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password')">
                            <svg id="eye-icon" class="h-5 w-5 text-slate-400 hover:text-slate-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg id="eye-slash-icon" class="h-5 w-5 text-slate-400 hover:text-slate-600 transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-4">
                <label class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-5 w-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                        <span class="ml-3 block text-sm font-medium text-slate-700">Remember me</span>
                    </label>
                    {{-- Admin checkbox removed --}}
                    <div class="flex justify-end">
                        <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-500">
                            Forgot your password?
                        </a>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-200">
                        <a href="{{ route('admin.login') }}" class="block w-full text-center bg-orange-50 border border-orange-200 rounded-2xl px-6 py-3 text-sm font-semibold text-orange-700 hover:bg-orange-100 transition-colors">
                            👨‍💼 Admin Login
                        </a>
                    </div>
                </div>
                <button type="submit" class="w-full bg-emerald-600 border border-transparent rounded-2xl px-6 py-4 text-lg font-semibold text-white shadow-xl hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-100 transition-all duration-200 hover:shadow-2xl hover:-translate-y-0.5">
                    Sign In
                </button>
            </form>
        </div>
        <div class="text-center space-y-2">
            <p class="text-sm text-slate-600">
                Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-emerald-600 hover:text-emerald-500 transition-colors duration-200">Create one now</a>
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
