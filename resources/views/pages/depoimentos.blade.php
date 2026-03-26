@extends('layouts.landing')

@section('title', 'Depoimentos — MyFinance')
@section('meta_description', 'Veja histórias reais de brasileiros que saíram do vermelho e conquistaram o controle financeiro com o MyFinance.')

@section('content')
{{-- Hero --}}
<section class="relative pt-28 pb-16 lg:pt-36 lg:pb-20 bg-gradient-to-b from-emerald-50/80 via-white to-white overflow-hidden">
    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-emerald-200/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-5 lg:px-8 text-center">
        <span class="text-sm font-bold uppercase tracking-wider text-emerald-600">Depoimentos</span>
        <h1 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">Histórias de quem <span class="text-emerald-600">saiu do vermelho de verdade</span></h1>
        <p class="mt-5 text-lg text-slate-500 max-w-2xl mx-auto">Não são depoimentos genéricos. São pessoas comuns que estavam endividadas, perdidas ou frustadas — e hoje dormem tranquilas sabendo exatamente onde está cada centavo.</p>
    </div>
</section>

{{-- Depoimentos --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">

            {{-- 1 --}}
            <div class="bg-slate-50 rounded-2xl p-7 border border-slate-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex gap-1 mb-4">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">"Eu vivia no cheque especial e tinha vergonha de falar sobre dinheiro. No primeiro mês com o MyFinance, descobri que gastava R$ 800/mês só com delivery. <strong>Em 3 meses saí do vermelho e já tenho R$ 2.000 guardados.</strong> Pela primeira vez na vida, sobrou dinheiro."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-200 flex items-center justify-center font-bold text-emerald-700 text-sm">MC</div>
                    <div>
                        <div class="text-sm font-semibold">Mariana Costa</div>
                        <div class="text-xs text-slate-400">Professora, 28 anos — São Paulo, SP</div>
                    </div>
                </div>
            </div>

            {{-- 2 --}}
            <div class="bg-slate-50 rounded-2xl p-7 border border-slate-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex gap-1 mb-4">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">"Eu achava que controlava meus gastos de cabeça. Quando vi o dashboard pela primeira vez, levei um susto: <strong>42% do salário ia pra coisas que eu nem lembrava.</strong> Cortei o desnecessário sem sofrimento e hoje guardo 30% todo mês. Virou hábito."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center font-bold text-blue-700 text-sm">RL</div>
                    <div>
                        <div class="text-sm font-semibold">Rafael Lima</div>
                        <div class="text-xs text-slate-400">Desenvolvedor, 32 anos — Belo Horizonte, MG</div>
                    </div>
                </div>
            </div>

            {{-- 3 --}}
            <div class="bg-slate-50 rounded-2xl p-7 border border-slate-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex gap-1 mb-4">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">"Tinha 4 dívidas e a sensação de que nunca ia sair daquilo. A gestão de dívidas me mostrou que, pagando R$ 180 a mais por mês na dívida certa, eu <strong>quitava tudo em 14 meses em vez de 36.</strong> Já paguei 3 das 4. Falta uma. Tô quase lá."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-violet-200 flex items-center justify-center font-bold text-violet-700 text-sm">AS</div>
                    <div>
                        <div class="text-sm font-semibold">Ana Souza</div>
                        <div class="text-xs text-slate-400">Enfermeira, 35 anos — Curitiba, PR</div>
                    </div>
                </div>
            </div>

            {{-- 4 --}}
            <div class="bg-slate-50 rounded-2xl p-7 border border-slate-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex gap-1 mb-4">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">"Minha esposa e eu brigávamos por dinheiro toda semana. Depois que começamos a usar o MyFinance juntos, <strong>as brigas acabaram porque os números falam por si.</strong> Hoje a gente planeja junto e já temos uma reserva de emergência de R$ 8.000."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-200 flex items-center justify-center font-bold text-amber-700 text-sm">PF</div>
                    <div>
                        <div class="text-sm font-semibold">Pedro Ferreira</div>
                        <div class="text-xs text-slate-400">Contador, 40 anos — Porto Alegre, RS</div>
                    </div>
                </div>
            </div>

            {{-- 5 --}}
            <div class="bg-slate-50 rounded-2xl p-7 border border-slate-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex gap-1 mb-4">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">"Sou autônoma e minha renda varia muito. O MyFinance me ajudou a criar um orçamento flexível e <strong>pela primeira vez consegui separar dinheiro do negócio do pessoal.</strong> Já investi R$ 5.000 que antes simplesmente sumiam no mês."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-pink-200 flex items-center justify-center font-bold text-pink-700 text-sm">JO</div>
                    <div>
                        <div class="text-sm font-semibold">Juliana Oliveira</div>
                        <div class="text-xs text-slate-400">Designer freelancer, 27 anos — Recife, PE</div>
                    </div>
                </div>
            </div>

            {{-- 6 --}}
            <div class="bg-slate-50 rounded-2xl p-7 border border-slate-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex gap-1 mb-4">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">"Comecei a usar o MyFinance quando ganhava R$ 2.500. Hoje ganho R$ 4.200 e, graças ao controle, <strong>consigo guardar mais agora do que gastava antes.</strong> O app me ensinou que o problema nunca foi o salário — era a falta de visibilidade."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-teal-200 flex items-center justify-center font-bold text-teal-700 text-sm">LM</div>
                    <div>
                        <div class="text-sm font-semibold">Lucas Mendes</div>
                        <div class="text-xs text-slate-400">Vendedor, 24 anos — Goiânia, GO</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Números --}}
        <div class="mt-16 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-3xl sm:text-4xl font-black text-emerald-600">15.847</div>
                <div class="mt-1 text-sm text-slate-500">Brasileiros no controle</div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-black text-emerald-600">R$ 2,4M</div>
                <div class="mt-1 text-sm text-slate-500">Economizados só este mês</div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-black text-emerald-600">4.9/5</div>
                <div class="mt-1 text-sm text-slate-500">Avaliação média</div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-black text-emerald-600">98%</div>
                <div class="mt-1 text-sm text-slate-500">Recomendam para amigos</div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 lg:py-20 px-5 lg:px-8">
    <div class="max-w-4xl mx-auto relative overflow-hidden bg-slate-900 rounded-3xl px-8 py-16 sm:px-16 sm:py-20 text-center">
        <div class="absolute -top-20 -right-20 w-60 h-60 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Sua história pode ser a próxima.</h2>
            <p class="mt-4 text-lg text-slate-400 max-w-lg mx-auto">Comece grátis e descubra como é dormir tranquilo sabendo exatamente onde está seu dinheiro.</p>
            <div class="mt-8">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-emerald-500 text-white font-bold text-lg px-8 py-4 rounded-xl hover:bg-emerald-400 transition-all duration-200 shadow-xl shadow-emerald-500/30">
                        Quero minha transformação
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
