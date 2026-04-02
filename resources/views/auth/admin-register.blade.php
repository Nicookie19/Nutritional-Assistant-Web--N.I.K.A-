@extends('layouts.app')

@section('title', 'Admin Register - NutriAssist')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 space-y-8">
        <div>
            <div class="mx-auto h-16 w-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold text-slate-900">Admin Registration</h2>
            <p class="mt-2 text-center text-sm text-slate-600">Create admin account (Admin Code: 1000808790)</p>
        </div>

        <form method="POST" action="{{ route('admin.register') }}" class="space-y-6">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900 mb-2">Full Name</label>
                <input id="name" name="name" type="text" required class="w-full px-4 py-3 border border-slate-300 rounded-2xl focus:ring-4 focus:ring-emerald-500 focus:border-transparent transition-all duration-200" placeholder="Admin User">
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-900 mb-2">Email</label>
                <input id="email" name="email" type="email" required autocomplete="email" class="w-full px-4 py-3 border border-slate-300 rounded-2xl focus:ring-4 focus:ring-emerald-500 focus:border-transparent transition-all duration-200" placeholder="admin@company.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-900 mb-2">Password</label>
                <input id="password" name="password" type="password" required class="w-full px-4 py-3 border border-slate-300 rounded-2xl focus:ring-4 focus:ring-emerald-500 focus:border-transparent transition-all duration-200" placeholder="Secure password">
            </div>

            <div class="flex items-center">
                <input id="is_admin" name="is_admin" type="checkbox" checked class="h-5 w-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 mr-3">
                <label for="is_admin" class="text-sm font-semibold text-slate-900">Admin Account</label>
            </div>

            <div>
                <label for="admin_code" class="block text-sm font-semibold text-slate-900 mb-2">Admin Code *(Required)*</label>
                <input id="admin_code" name="admin_code" type="text" required class="w-full px-4 py-3 border border-slate-300 rounded-2xl focus:ring-4 focus:ring-orange-500 focus:border-transparent transition-all duration-200" placeholder="1000808790">
                <p class="text-xs text-slate-500 mt-1">Default code: <code>1000808790</code></p>
            </div>

            @error('admin_code')
                <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
            @enderror

            <button type="submit" class="w-full bg-emerald-600 border border-transparent rounded-2xl px-6 py-4 text-lg font-semibold text-white shadow-xl hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-100 transition-all duration-200 hover:shadow-2xl hover:-translate-y-0.5">
                Create Admin Account
            </button>
        </form>

        <div class="text-center space-y-2">
            <a href="{{ route('admin.login') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold transition-colors block">← Admin Login</a>
            <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-700 text-sm font-semibold transition-colors block">User Login →</a>
            <a href="{{ route('register') }}" class="text-slate-600 hover:text-slate-700 text-sm font-semibold transition-colors block">User Register →</a>
        </div>
    </div>
</div>
@endsection
