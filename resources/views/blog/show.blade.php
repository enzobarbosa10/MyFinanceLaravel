@extends('layouts.landing')

@section('title', $post->title . ' — Blog MyFinance')
@section('meta_description', Str::limit(strip_tags($post->content), 160))

@section('content')
<article class="pt-28 pb-20 lg:pt-36 lg:pb-28 bg-white">
    <div class="max-w-3xl mx-auto px-5 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="mb-8">
            <ol class="flex items-center gap-2 text-sm text-slate-400">
                <li><a href="{{ route('home') }}" class="hover:text-emerald-600 transition">Início</a></li>
                <li><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-emerald-600 transition">Blog</a></li>
                <li><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg></li>
                <li class="text-slate-600 font-medium">{{ Str::limit($post->title, 40) }}</li>
            </ol>
        </nav>

        {{-- Header --}}
        <header class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Artigo</span>
                <time class="text-sm text-slate-400" datetime="{{ $post->created_at->toISOString() }}">{{ $post->created_at->format('d \d\e F \d\e Y') }}</time>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">{{ $post->title }}</h1>
        </header>

        {{-- Content --}}
        <div class="prose prose-lg prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-p:text-slate-600 prose-p:leading-relaxed prose-a:text-emerald-600 prose-a:no-underline hover:prose-a:underline prose-strong:text-slate-800">
            {!! nl2br(e($post->content)) !!}
        </div>

        {{-- Back --}}
        <div class="mt-12 pt-8 border-t border-slate-200">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Voltar para o blog
            </a>
        </div>
    </div>
</article>

{{-- CTA --}}
<section class="py-16 lg:py-20 px-5 lg:px-8 bg-slate-50">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Gostou das dicas? Coloque em prática.</h2>
        <p class="mt-3 text-lg text-slate-500">Crie sua conta grátis no MyFinance e comece a controlar seu dinheiro hoje mesmo.</p>
        <div class="mt-6">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white font-bold px-8 py-3.5 rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-lg shadow-emerald-600/20">
                    Começar grátis
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            @endif
        </div>
    </div>
</section>
@endsection
