@extends('layouts.landing')

@section('title', 'Blog — MyFinance')
@section('meta_description', 'Dicas práticas de finanças pessoais, economia e investimentos. Aprenda a controlar seu dinheiro com artigos do blog MyFinance.')

@section('content')
{{-- Hero --}}
<section class="relative pt-28 pb-16 lg:pt-36 lg:pb-20 bg-gradient-to-b from-emerald-50/80 via-white to-white overflow-hidden">
    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-emerald-200/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-5 lg:px-8 text-center">
        <span class="text-sm font-bold uppercase tracking-wider text-emerald-600">Blog</span>
        <h1 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">Conteúdo que <span class="text-emerald-600">faz seu dinheiro render</span></h1>
        <p class="mt-5 text-lg text-slate-500 max-w-2xl mx-auto">Dicas práticas, estratégias testadas e conteúdo sem enrolação para você dominar suas finanças.</p>
    </div>
</section>

{{-- Posts --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        @if ($posts->count())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($posts as $post)
                    <article class="bg-white rounded-2xl border border-slate-200 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-50 transition-all duration-300 overflow-hidden group">
                        <div class="p-7">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Artigo</span>
                                <span class="text-xs text-slate-400">{{ $post->created_at->format('d/m/Y') }}</span>
                            </div>
                            <h2 class="text-xl font-bold mb-3 group-hover:text-emerald-600 transition-colors">
                                <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                            </h2>
                            <p class="text-slate-500 text-sm leading-relaxed mb-4">{{ Str::limit(strip_tags($post->content), 150) }}</p>
                            <a href="{{ route('blog.show', $post) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition">
                                Ler artigo completo
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Nenhum artigo ainda</h3>
                <p class="text-slate-500 mt-1">Em breve publicaremos conteúdo incrível para ajudar você.</p>
            </div>
        @endif
    </div>
</section>
@endsection
