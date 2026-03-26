{{-- Depoimentos Section --}}
<section id="depoimentos" class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-sm font-bold uppercase tracking-wider text-emerald-600">Histórias reais</span>
            <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight">Eles estavam no vermelho. Hoje dormem tranquilos.</h2>
            <p class="mt-4 text-lg text-slate-500">Mais de 15 mil brasileiros já transformaram sua relação com o dinheiro. Estas são algumas das histórias.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Depoimento 1 --}}
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
            {{-- Depoimento 2 --}}
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
            {{-- Depoimento 3 --}}
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
        </div>

        {{-- Números sociais --}}
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
