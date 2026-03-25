<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MyFinance - Controle Financeiro Inteligente</title>
        <meta name="description" content="Gerencie suas finan&#231;as pessoais com intelig&#234;ncia artificial.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
        <style>
            *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
            :root{--primary:#10B981;--primary-dark:#059669;--primary-light:#D1FAE5;--dark:#0F172A;--gray:#64748B;--gray-light:#F1F5F9;--white:#FFFFFF}
            html{scroll-behavior:smooth}
            body{font-family:'Inter',sans-serif;color:var(--dark);line-height:1.6;background:var(--white)}
            .navbar{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(255,255,255,0.95);backdrop-filter:blur(10px);border-bottom:1px solid rgba(0,0,0,0.05);padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
            .navbar-brand{font-size:1.5rem;font-weight:800;color:var(--primary);text-decoration:none;display:flex;align-items:center;gap:0.5rem}
            .navbar-brand svg{width:32px;height:32px}
            .navbar-links{display:flex;align-items:center;gap:2rem;list-style:none}
            .navbar-links a{text-decoration:none;color:var(--gray);font-size:0.9rem;font-weight:500;transition:color 0.2s}
            .navbar-links a:hover{color:var(--dark)}
            .btn{display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.5rem;border-radius:8px;font-weight:600;font-size:0.95rem;text-decoration:none;transition:all 0.3s;border:none;cursor:pointer}
            .btn-primary{background:var(--primary);color:var(--white)}
            .btn-primary:hover{background:var(--primary-dark);transform:translateY(-2px);box-shadow:0 8px 25px rgba(16,185,129,0.3)}
            .btn-outline{background:transparent;color:var(--dark);border:2px solid #E2E8F0}
            .btn-outline:hover{border-color:var(--primary);color:var(--primary)}
            .btn-large{padding:1rem 2.5rem;font-size:1.1rem;border-radius:12px}
            .hero{padding:8rem 2rem 4rem;text-align:center;background:linear-gradient(180deg,#F0FDF4 0%,var(--white) 100%);min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center}
            .hero-badge{display:inline-flex;align-items:center;gap:0.5rem;background:var(--primary-light);color:var(--primary-dark);padding:0.4rem 1rem;border-radius:100px;font-size:0.85rem;font-weight:600;margin-bottom:1.5rem}
            .hero h1{font-size:clamp(2.5rem,5vw,4rem);font-weight:800;line-height:1.1;margin-bottom:1.5rem;max-width:700px}
            .hero h1 span{color:var(--primary)}
            .hero p{font-size:1.2rem;color:var(--gray);max-width:550px;margin-bottom:2.5rem}
            .hero-buttons{display:flex;gap:1rem;flex-wrap:wrap;justify-content:center}
            .hero-stats{display:flex;gap:3rem;margin-top:4rem;padding-top:3rem;border-top:1px solid #E2E8F0}
            .hero-stat{text-align:center}
            .hero-stat .number{font-size:2rem;font-weight:800;color:var(--primary)}
            .hero-stat .label{font-size:0.85rem;color:var(--gray);margin-top:0.25rem}
            .section{padding:6rem 2rem}
            .section-gray{background:var(--gray-light)}
            .container{max-width:1100px;margin:0 auto}
            .section-header{text-align:center;margin-bottom:4rem}
            .section-header h2{font-size:2.2rem;font-weight:800;margin-bottom:1rem}
            .section-header p{font-size:1.1rem;color:var(--gray);max-width:500px;margin:0 auto}
            .features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:2rem}
            .feature-card{background:var(--white);border-radius:16px;padding:2rem;border:1px solid #E2E8F0;transition:all 0.3s}
            .feature-card:hover{transform:translateY(-4px);box-shadow:0 12px 40px rgba(0,0,0,0.08);border-color:var(--primary-light)}
            .feature-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:1.2rem;font-size:1.5rem}
            .feature-icon.green{background:#D1FAE5}.feature-icon.blue{background:#DBEAFE}.feature-icon.purple{background:#EDE9FE}
            .feature-icon.orange{background:#FED7AA}.feature-icon.red{background:#FEE2E2}.feature-icon.cyan{background:#CFFAFE}
            .feature-card h3{font-size:1.15rem;font-weight:700;margin-bottom:0.6rem}
            .feature-card p{font-size:0.9rem;color:var(--gray);line-height:1.7}
            .steps{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:2rem;counter-reset:step}
            .step{text-align:center;padding:2rem 1.5rem}
            .step::before{counter-increment:step;content:counter(step);display:flex;align-items:center;justify-content:center;width:48px;height:48px;background:var(--primary);color:var(--white);border-radius:50%;font-weight:800;font-size:1.2rem;margin:0 auto 1.2rem}
            .step h3{font-size:1.1rem;font-weight:700;margin-bottom:0.5rem}
            .step p{font-size:0.9rem;color:var(--gray)}
            .plans-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:2rem;align-items:start}
            .plan-card{background:var(--white);border-radius:16px;padding:2.5rem 2rem;border:2px solid #E2E8F0;text-align:center;transition:all 0.3s}
            .plan-card.featured{border-color:var(--primary);position:relative;transform:scale(1.05)}
            .plan-card.featured::before{content:'Mais Popular';position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:var(--primary);color:var(--white);padding:0.25rem 1rem;border-radius:100px;font-size:0.8rem;font-weight:600}
            .plan-card:hover{box-shadow:0 12px 40px rgba(0,0,0,0.08)}
            .plan-name{font-size:1.2rem;font-weight:700;color:var(--gray);margin-bottom:0.5rem}
            .plan-price{font-size:3rem;font-weight:800;margin-bottom:0.25rem}
            .plan-price span{font-size:1rem;font-weight:400;color:var(--gray)}
            .plan-desc{font-size:0.9rem;color:var(--gray);margin-bottom:1.5rem}
            .plan-features{list-style:none;text-align:left;margin-bottom:2rem}
            .plan-features li{padding:0.5rem 0;font-size:0.9rem;color:var(--dark);display:flex;align-items:center;gap:0.5rem}
            .plan-features li::before{content:'\2713';color:var(--primary);font-weight:700}
            .cta{background:var(--dark);color:var(--white);padding:5rem 2rem;text-align:center;border-radius:24px;margin:0 2rem}
            .cta h2{font-size:2.2rem;font-weight:800;margin-bottom:1rem}
            .cta p{color:#94A3B8;font-size:1.1rem;max-width:500px;margin:0 auto 2rem}
            .footer{padding:3rem 2rem;text-align:center;color:var(--gray);font-size:0.85rem}
            .footer a{color:var(--primary);text-decoration:none}
            .menu-toggle{display:none;background:none;border:none;cursor:pointer;padding:0.5rem}
            .menu-toggle span{display:block;width:24px;height:2px;background:var(--dark);margin:5px 0;transition:0.3s}
            @media(max-width:768px){.menu-toggle{display:block}.navbar-links{display:none;position:absolute;top:100%;left:0;right:0;flex-direction:column;background:var(--white);padding:1.5rem;border-bottom:1px solid #E2E8F0;gap:1rem}.navbar-links.active{display:flex}.hero-stats{flex-direction:column;gap:1.5rem}.plan-card.featured{transform:none}.cta{margin:0;border-radius:0}}
        </style>
    </head>
    <body>
        <nav class="navbar">
            <a href="#" class="navbar-brand">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                MyFinance
            </a>
            <button class="menu-toggle" onclick="document.querySelector('.navbar-links').classList.toggle('active')"><span></span><span></span><span></span></button>
            <ul class="navbar-links">
                <li><a href="#features">Recursos</a></li>
                <li><a href="#how-it-works">Como Funciona</a></li>
                <li><a href="#plans">Planos</a></li>
                @if (Route::has('login'))
                    @auth
                        <li><a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Entrar</a></li>
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="btn btn-primary">Criar Conta</a></li>
                        @endif
                    @endauth
                @endif
            </ul>
        </nav>

        <section class="hero">
            <div class="hero-badge">&#x1F680; Potencializado por Intelig&#234;ncia Artificial</div>
            <h1>Suas finan&#231;as no <span>controle total</span></h1>
            <p>Gerencie gastos, investimentos, d&#237;vidas e metas financeiras em um s&#243; lugar, com insights inteligentes que transformam sua vida financeira.</p>
            <div class="hero-buttons">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary btn-large">Come&#231;ar Gratuitamente</a>
                @endif
                <a href="#features" class="btn btn-outline btn-large">Conhecer Recursos</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><div class="number">100%</div><div class="label">Gratuito para come&#231;ar</div></div>
                <div class="hero-stat"><div class="number">IA</div><div class="label">Consultor financeiro</div></div>
                <div class="hero-stat"><div class="number">360&#176;</div><div class="label">Vis&#227;o financeira completa</div></div>
            </div>
        </section>

        <section class="section section-gray" id="features">
            <div class="container">
                <div class="section-header">
                    <h2>Tudo que voc&#234; precisa para suas finan&#231;as</h2>
                    <p>Ferramentas poderosas e intuitivas para cada aspecto da sua vida financeira.</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card"><div class="feature-icon green">&#x1F4CA;</div><h3>Controle de Transa&#231;&#245;es</h3><p>Registre receitas e despesas, categorize automaticamente com IA e tenha visibilidade total do seu fluxo de caixa.</p></div>
                    <div class="feature-card"><div class="feature-icon blue">&#x1F4B0;</div><h3>Or&#231;amentos Inteligentes</h3><p>Crie or&#231;amentos por categoria e receba alertas quando estiver pr&#243;ximo do limite.</p></div>
                    <div class="feature-card"><div class="feature-icon purple">&#x1F4C8;</div><h3>Investimentos</h3><p>Acompanhe sua carteira de investimentos, veja o hist&#243;rico de pre&#231;os e receba not&#237;cias dos seus ativos.</p></div>
                    <div class="feature-card"><div class="feature-icon orange">&#x1F3AF;</div><h3>Metas Financeiras</h3><p>Defina metas como viagens, compras ou reserva de emerg&#234;ncia e acompanhe seu progresso.</p></div>
                    <div class="feature-card"><div class="feature-icon red">&#x1F4B3;</div><h3>Gest&#227;o de D&#237;vidas</h3><p>Controle empr&#233;stimos e d&#237;vidas, registre pagamentos e visualize o caminho at&#233; a quita&#231;&#227;o.</p></div>
                    <div class="feature-card"><div class="feature-icon cyan">&#x1F916;</div><h3>Consultor IA</h3><p>Receba dicas personalizadas, proje&#231;&#245;es de fluxo de caixa e an&#225;lises inteligentes baseadas nos seus dados.</p></div>
                </div>
            </div>
        </section>

        <section class="section" id="how-it-works">
            <div class="container">
                <div class="section-header">
                    <h2>Comece em minutos</h2>
                    <p>Tr&#234;s passos simples para transformar sua vida financeira.</p>
                </div>
                <div class="steps">
                    <div class="step"><h3>Crie sua conta</h3><p>Cadastro r&#225;pido e gratuito. Em menos de 1 minuto voc&#234; j&#225; est&#225; pronto.</p></div>
                    <div class="step"><h3>Conecte suas contas</h3><p>Adicione suas contas banc&#225;rias, cart&#245;es e investimentos. Importe extratos facilmente.</p></div>
                    <div class="step"><h3>Assuma o controle</h3><p>Visualize dashboards, receba insights da IA e tome decis&#245;es financeiras mais inteligentes.</p></div>
                </div>
            </div>
        </section>

        <section class="section section-gray" id="plans">
            <div class="container">
                <div class="section-header">
                    <h2>Planos para cada necessidade</h2>
                    <p>Escolha o plano ideal para o seu momento financeiro.</p>
                </div>
                <div class="plans-grid">
                    <div class="plan-card">
                        <div class="plan-name">Free</div>
                        <div class="plan-price">R$0 <span>/m&#234;s</span></div>
                        <div class="plan-desc">Perfeito para come&#231;ar</div>
                        <ul class="plan-features"><li>2 contas banc&#225;rias</li><li>Controle de transa&#231;&#245;es</li><li>Categorias b&#225;sicas</li><li>1 meta financeira</li><li>Relat&#243;rios simples</li></ul>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline" style="width:100%;justify-content:center;">Come&#231;ar Gr&#225;tis</a>
                        @endif
                    </div>
                    <div class="plan-card featured">
                        <div class="plan-name">Pro</div>
                        <div class="plan-price">R$19 <span>/m&#234;s</span></div>
                        <div class="plan-desc">Para quem quer crescer</div>
                        <ul class="plan-features"><li>Contas ilimitadas</li><li>Or&#231;amentos inteligentes</li><li>Carteira de investimentos</li><li>Metas ilimitadas</li><li>Consultor IA (50 consultas/m&#234;s)</li><li>Importa&#231;&#227;o de extratos</li><li>Alertas personalizados</li></ul>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary" style="width:100%;justify-content:center;">Assinar Pro</a>
                        @endif
                    </div>
                    <div class="plan-card">
                        <div class="plan-name">Premium</div>
                        <div class="plan-price">R$39 <span>/m&#234;s</span></div>
                        <div class="plan-desc">Controle total</div>
                        <ul class="plan-features"><li>Tudo do Pro</li><li>Consultor IA ilimitado</li><li>Proje&#231;&#245;es de fluxo de caixa</li><li>Gest&#227;o de d&#237;vidas avan&#231;ada</li><li>Relat&#243;rios export&#225;veis</li><li>Renda vari&#225;vel</li><li>Suporte priorit&#225;rio</li></ul>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline" style="width:100%;justify-content:center;">Assinar Premium</a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="cta">
                <h2>Pronto para transformar suas finan&#231;as?</h2>
                <p>Junte-se a milhares de pessoas que j&#225; assumiram o controle da sua vida financeira com o MyFinance.</p>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary btn-large">Criar Minha Conta Gr&#225;tis</a>
                @endif
            </div>
        </section>

        <footer class="footer">
            <p>&copy; {{ date('Y') }} <a href="#">MyFinance</a>. Todos os direitos reservados.</p>
        </footer>
    </body>
</html>

        <footer class="footer">
            <p>&copy; {{ date('Y') }} <a href="#">MyFinance</a>. Todos os direitos reservados.</p>
        </footer>
    </body>
</html>
