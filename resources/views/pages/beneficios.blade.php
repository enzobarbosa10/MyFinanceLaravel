@extends('layouts.landing')

@section('title', 'Benefícios — MyFinance')
@section('meta_description', 'Descubra como o MyFinance transforma sua relação com o dinheiro: clareza financeira, economia real e tranquilidade para dormir à noite.')

@section('content')
{{-- Hero --}}
<section class="relative pt-28 pb-16 lg:pt-36 lg:pb-20 bg-gradient-to-b from-emerald-50/80 via-white to-white overflow-hidden">
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-teal-100/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-5 lg:px-8 text-center">
        <span class="text-sm font-bold uppercase tracking-wider text-emerald-600">Benefícios</span>
        <h1 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">O problema não é ganhar pouco.<br><span class="text-emerald-600">É não saber pra onde vai.</span></h1>
        <p class="mt-5 text-lg text-slate-500 max-w-2xl mx-auto">78% dos brasileiros chegam ao fim do mês sem saber onde gastaram. O MyFinance resolve isso em minutos — e o resultado é dinheiro sobrando de verdade.</p>
    </div>
</section>

{{-- Problema → Solução --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-5xl mx-auto px-5 lg:px-8 space-y-20">

        {{-- 1 --}}
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="bg-red-50 rounded-2xl p-8 border border-red-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h3 class="font-bold text-red-700">O problema</h3>
                </div>
                <p class="text-red-800/80 leading-relaxed">"No fim do mês o dinheiro sumiu e eu não sei onde gastei. Tenho a sensação de que trabalho pra pagar conta e nunca sobra nada."</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-8 border border-emerald-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="font-bold text-emerald-700">Com MyFinance</h3>
                </div>
                <p class="text-emerald-800/80 leading-relaxed">Você vê cada centavo categorizado automaticamente. Em 5 segundos sabe exatamente onde gastou, descobre padrões ocultos e começa a sobrar dinheiro no primeiro mês.</p>
            </div>
        </div>

        {{-- 2 --}}
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="bg-red-50 rounded-2xl p-8 border border-red-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h3 class="font-bold text-red-700">O problema</h3>
                </div>
                <p class="text-red-800/80 leading-relaxed">"Tenho dívidas acumuladas e parece que nunca vou conseguir pagar. Os juros crescem mais rápido do que eu consigo pagar."</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-8 border border-emerald-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="font-bold text-emerald-700">Com MyFinance</h3>
                </div>
                <p class="text-emerald-800/80 leading-relaxed">O módulo de dívidas calcula juros, mostra a prioridade de pagamento e cria um plano com data para quitar tudo. Nossos usuários reduzem o tempo de quitação em até 60%.</p>
            </div>
        </div>

        {{-- 3 --}}
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="bg-red-50 rounded-2xl p-8 border border-red-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h3 class="font-bold text-red-700">O problema</h3>
                </div>
                <p class="text-red-800/80 leading-relaxed">"Quero guardar dinheiro mas nunca consigo. Sempre aparece algo urgente e o dinheiro some antes de eu guardar."</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-8 border border-emerald-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="font-bold text-emerald-700">Com MyFinance</h3>
                </div>
                <p class="text-emerald-800/80 leading-relaxed">Metas visuais com prazo e barra de progresso. Você define quanto quer guardar, acompanha semana a semana e vê o progresso real. Em média, nossos usuários economizam R$ 640/mês nos primeiros 90 dias.</p>
            </div>
        </div>

    </div>
</section>

{{-- Números --}}
<section class="py-16 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-5 lg:px-8 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
        <div>
            <div class="text-3xl sm:text-4xl font-black text-emerald-600">R$ 640</div>
            <div class="mt-1 text-sm text-slate-500">Economia média mensal</div>
        </div>
        <div>
            <div class="text-3xl sm:text-4xl font-black text-emerald-600">60%</div>
            <div class="mt-1 text-sm text-slate-500">Mais rápido para quitar dívidas</div>
        </div>
        <div>
            <div class="text-3xl sm:text-4xl font-black text-emerald-600">2 min</div>
            <div class="mt-1 text-sm text-slate-500">Tempo diário de uso</div>
        </div>
        <div>
            <div class="text-3xl sm:text-4xl font-black text-emerald-600">98%</div>
            <div class="mt-1 text-sm text-slate-500">Recomendam para amigos</div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 lg:py-20 px-5 lg:px-8">
    <div class="max-w-4xl mx-auto relative overflow-hidden bg-slate-900 rounded-3xl px-8 py-16 sm:px-16 sm:py-20 text-center">
        <div class="absolute -top-20 -right-20 w-60 h-60 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Pare de perder dinheiro.<br>Comece a guardar hoje.</h2>
            <p class="mt-4 text-lg text-slate-400 max-w-lg mx-auto">Junte-se a +15 mil brasileiros que transformaram sua relação com o dinheiro.</p>
            <div class="mt-8">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-500 text-white font-bold text-lg px-8 py-4 rounded-xl hover:bg-emerald-400 transition-all duration-200 shadow-xl shadow-emerald-500/30">
                        Criar minha conta grátis
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
