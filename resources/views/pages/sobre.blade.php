@extends('layouts.landing')

@section('title', 'Sobre — MyFinance')
@section('meta_description', 'Conheça a história do MyFinance: nascemos da frustração com planilhas e apps confusos. Nossa missão é democratizar o controle financeiro no Brasil.')

@section('content')
{{-- Hero --}}
<section class="relative pt-28 pb-16 lg:pt-36 lg:pb-20 bg-gradient-to-b from-emerald-50/80 via-white to-white overflow-hidden">
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-teal-100/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-5 lg:px-8 text-center">
        <span class="text-sm font-bold uppercase tracking-wider text-emerald-600">Sobre nós</span>
        <h1 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">Nascemos de uma <span class="text-emerald-600">frustração real</span></h1>
        <p class="mt-5 text-lg text-slate-500 max-w-2xl mx-auto">O MyFinance não nasceu de uma planilha de investidores. Nasceu da raiva de não saber pra onde ia o salário todo mês.</p>
    </div>
</section>

{{-- História --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-3xl mx-auto px-5 lg:px-8">
        <div class="prose prose-lg prose-slate max-w-none">
            <h2 class="text-2xl font-extrabold text-slate-900">Como tudo começou</h2>
            <p>Em 2024, nosso fundador estava em uma situação que milhões de brasileiros conhecem: ganhava um salário razoável, mas todo mês o dinheiro simplesmente evaporava. Planilhas eram abandonadas na segunda semana. Apps de banco mostravam números, mas não respostas. E os aplicativos de controle financeiro pareciam feitos para quem já era organizado.</p>

            <p>A pergunta era simples: <strong>"Por que não existe uma ferramenta que me mostre em 5 segundos pra onde meu dinheiro está indo, me ajude a sair das dívidas e me motive a guardar?"</strong></p>

            <p>Não encontramos essa ferramenta. Então construímos.</p>

            <h2 class="text-2xl font-extrabold text-slate-900 mt-12">Nossa missão</h2>
            <p>Democratizar o controle financeiro no Brasil. Acreditamos que <strong>qualquer pessoa merece saber exatamente onde está cada centavo do seu dinheiro</strong> — sem precisar de diploma em contabilidade, sem planilhas complexas e sem pagar caro por isso.</p>

            <p>Nosso compromisso é fazer isso com:</p>

            <div class="not-prose grid sm:grid-cols-3 gap-6 my-8">
                <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Simplicidade</h3>
                    <p class="text-sm text-slate-500">Se não for simples, ninguém usa. Cada tela é pensada para resolver em poucos toques.</p>
                </div>
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-blue-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Privacidade</h3>
                    <p class="text-sm text-slate-500">Seus dados são seus. Criptografia de ponta, sem venda de informações, sem terceiros.</p>
                </div>
                <div class="bg-violet-50 rounded-2xl p-6 border border-violet-100 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-violet-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Acessibilidade</h3>
                    <p class="text-sm text-slate-500">Plano gratuito para sempre. Porque controle financeiro não deveria ser luxo.</p>
                </div>
            </div>

            <h2 class="text-2xl font-extrabold text-slate-900 mt-12">Nossos números</h2>
            <p>Desde o lançamento, temos orgulho de compartilhar:</p>
        </div>

        <div class="grid grid-cols-2 gap-6 mt-8">
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 text-center">
                <div class="text-3xl font-black text-emerald-600">+15 mil</div>
                <div class="mt-1 text-sm text-slate-500">Usuários ativos</div>
            </div>
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 text-center">
                <div class="text-3xl font-black text-emerald-600">R$ 2,4M</div>
                <div class="mt-1 text-sm text-slate-500">Economizados por mês</div>
            </div>
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 text-center">
                <div class="text-3xl font-black text-emerald-600">4.9/5</div>
                <div class="mt-1 text-sm text-slate-500">Avaliação dos usuários</div>
            </div>
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 text-center">
                <div class="text-3xl font-black text-emerald-600">24/7</div>
                <div class="mt-1 text-sm text-slate-500">Disponibilidade</div>
            </div>
        </div>

        <div class="prose prose-lg prose-slate max-w-none mt-12">
            <h2 class="text-2xl font-extrabold text-slate-900">O futuro</h2>
            <p>Estamos apenas começando. Nosso roadmap inclui integração com bancos via Open Finance, inteligência artificial para sugestões personalizadas de economia, e muito mais. Mas uma coisa não muda: <strong>o MyFinance sempre será a ferramenta mais simples e eficiente para você controlar seu dinheiro.</strong></p>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 lg:py-20 px-5 lg:px-8">
    <div class="max-w-4xl mx-auto relative overflow-hidden bg-slate-900 rounded-3xl px-8 py-16 sm:px-16 sm:py-20 text-center">
        <div class="absolute -top-20 -right-20 w-60 h-60 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Faça parte dessa história.</h2>
            <p class="mt-4 text-lg text-slate-400 max-w-lg mx-auto">Junte-se a milhares de brasileiros que decidiram tomar o controle da própria vida financeira.</p>
            <div class="mt-8">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-500 text-white font-bold text-lg px-8 py-4 rounded-xl hover:bg-emerald-400 transition-all duration-200 shadow-xl shadow-emerald-500/30">
                        Começar grátis agora
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
