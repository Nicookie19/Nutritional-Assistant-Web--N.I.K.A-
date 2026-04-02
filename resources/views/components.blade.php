@extends('layouts.app')

@section('title', 'Components')

@section('content')
<div class="container mx-auto px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8 text-[#1b1b18] dark:text-white">UI Components</h1>
    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">Beautiful, reusable UI components powered by Tailwind CSS.</p>
    <!-- Grid of components matching design -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="font-semibold mb-2">Buttons</h3>
            <p>Primary, secondary, danger variants.</p>
        </div>
        <!-- More components -->
    </div>
</div>
@endsection

