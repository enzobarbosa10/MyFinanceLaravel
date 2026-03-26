@extends('layouts.landing')

@section('title', 'Funcionalidades — MyFinance')
@section('meta_description', 'Conheça todas as funcionalidades do MyFinance: controle de gastos, orçamentos inteligentes, metas financeiras, gestão de dívidas e investimentos.')

@section('content')
{{-- Hero --}}
<section class="relative pt-28 pb-16 lg:pt-36 lg:pb-20 bg-gradient-to-b from-emerald-50/80 via-white to-white overflow-hidden">
    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-emerald-200/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-5 lg:px-8 text-center">
        <span class="text-sm font-bold uppercase tracking-wider text-emerald-600">Funcionalidades</span>
        <h1 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">Tudo que você precisa para <span class="text-emerald-600">dominar seu dinheiro</span></h1>
        <p class="mt-5 text-lg text-slate-500 max-w-2xl mx-auto">Cada recurso foi pensado para resolver um problema real. Sem firula, sem funcionalidade de enfeite. Apenas o que te ajuda a sair do vermelho e guardar mais.</p>
    </div>
</section>

{{-- Feature Grid --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16">

            {{-- 1: Transações --}}
            <div class="flex gap-6">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Registro de receitas e despesas</h3>
                    <p class="text-slate-500 leading-relaxed">Adicione transações em 3 toques. Categorize automaticamente, filtre por período, conta ou categoria e descubra exatamente pra onde seu dinheiro está indo. Suporte a múltiplas contas bancárias e cartões.</p>
                </div>
            </div>

            {{-- 2: Dashboard --}}
            <div class="flex gap-6">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Dashboard visual inteligente</h3>
                    <p class="text-slate-500 leading-relaxed">Abra, olhe, entenda. Gráficos de barras, pizza e evolução temporal que mostram sua saúde financeira em 5 segundos. Sem precisar interpretar números confusos em planilhas.</p>
                </div>
            </div>

            {{-- 3: Orçamentos --}}
            <div class="flex gap-6">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-violet-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Orçamentos por categoria</h3>
                    <p class="text-slate-500 leading-relaxed">Defina limites de gastos mensais por categoria (alimentação, transporte, lazer). Receba alertas quando estiver chegando perto do limite — antes de estourar o orçamento.</p>
                </div>
            </div>

            {{-- 4: Metas --}}
            <div class="flex gap-6">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Metas financeiras com prazo</h3>
                    <p class="text-slate-500 leading-relaxed">Viagem, carro, reserva de emergência — defina quanto quer guardar e até quando. Acompanhe com barras de progresso visuais e contribua semanalmente. Sonho com plano não é mais sonho, é meta.</p>
                </div>
            </div>

            {{-- 5: Dívidas --}}
            <div class="flex gap-6">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Gestão de dívidas inteligente</h3>
                    <p class="text-slate-500 leading-relaxed">Registre cada dívida com valor, juros e parcelas. Veja quanto falta, calcule o impacto dos juros compostos e tenha uma rota clara de quitação com data definida. Sem achismo, com estratégia.</p>
                </div>
            </div>

            {{-- 6: Investimentos --}}
            <div class="flex gap-6">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-teal-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Carteira de investimentos</h3>
                    <p class="text-slate-500 leading-relaxed">Acompanhe renda fixa, ações, FIIs e cripto em um só lugar. Veja a rentabilidade real da sua carteira, a distribuição por tipo de ativo e tome decisões com dados — não com achismo do grupo do WhatsApp.</p>
                </div>
            </div>

            {{-- 7: Múltiplas contas --}}
            <div class="flex gap-6">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Múltiplas contas e cartões</h3>
                    <p class="text-slate-500 leading-relaxed">Nubank, Inter, Itaú, carteira física — cadastre todas as suas contas e cartões. O saldo total é calculado automaticamente e você vê tudo consolidado em um painel único.</p>
                </div>
            </div>

            {{-- 8: Relatórios --}}
            <div class="flex gap-6">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-pink-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Relatórios detalhados</h3>
                    <p class="text-slate-500 leading-relaxed">Relatórios por período, categoria e conta que revelam padrões de gastos que você não percebia. É aqui que o dinheiro começa a sobrar — quando você enxerga o que antes era invisível.</p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 lg:py-20 px-5 lg:px-8">
    <div class="max-w-4xl mx-auto relative overflow-hidden bg-slate-900 rounded-3xl px-8 py-16 sm:px-16 sm:py-20 text-center">
        <div class="absolute -top-20 -right-20 w-60 h-60 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Pronto para ter o controle total?</h2>
            <p class="mt-4 text-lg text-slate-400 max-w-lg mx-auto">Todas essas funcionalidades estão esperando por você. Crie sua conta grátis em 2 minutos.</p>
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
