@extends('layouts.app')

@section('title', 'Changelog')

@section('content')
<div class="container mx-auto px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8 text-[#1b1b18] dark:text-white">Changelog</h1>
    <div class="prose dark:prose-invert max-w-none">
        <!-- Changelog entries -->
        <article class="mb-12">
            <h2>v12.0.0</h2>
            <ul>
                <li>New Tailwind v4 integration</li>
            </ul>
        </article>
    </div>
</div>
@endsection

