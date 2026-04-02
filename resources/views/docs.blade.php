@extends('layouts.app')

@section('title', 'Documentation')

@section('content')
<div class="container mx-auto px-6 lg:px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold mb-8 text-[#1b1b18] dark:text-white">Laravel Documentation</h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">Comprehensive guides and API reference for Laravel framework.</p>
        <div class="grid lg:grid-cols-4 gap-8">
            <aside class="lg:col-span-1">
                <nav class="sticky top-20">
                    <ul class="space-y-2">
                        <li><a href="#installation" class="block py-2 px-4 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">Installation</a></li>
                        <li><a href="#routing" class="block py-2 px-4 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">Routing</a></li>
                        <li><a href="#middleware" class="block py-2 px-4 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">Middleware</a></li>
                        <li><a href="#controllers" class="block py-2 px-4 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">Controllers</a></li>
                    </ul>
                </nav>
            </aside>
            <main class="lg:col-span-3 prose dark:prose-invert max-w-none">
                <section id="installation">
                    <h2 class="text-2xl font-semibold mb-4">Installation</h2>
                    <p>Install Laravel via Composer...</p>
                </section>
                <!-- More sections matching design screenshots -->
            </main>
        </div>
    </div>
</div>
@endsection

