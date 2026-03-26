@extends('layouts.landing')

@section('title', 'Política de Privacidade — MyFinance')
@section('meta_description', 'Política de privacidade do MyFinance. Entenda como coletamos, usamos e protegemos seus dados pessoais.')

@section('content')
<section class="pt-28 pb-20 lg:pt-36 lg:pb-28 bg-white">
    <div class="max-w-3xl mx-auto px-5 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-2">Política de Privacidade</h1>
        <p class="text-sm text-slate-400 mb-12">Última atualização: 25 de março de 2026</p>

        <div class="prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-p:text-slate-600 prose-p:leading-relaxed prose-li:text-slate-600">

            <h2>1. Introdução</h2>
            <p>O MyFinance ("nós", "nosso" ou "Plataforma") está comprometido com a proteção da privacidade dos seus usuários. Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e protegemos suas informações pessoais, em conformidade com a Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018).</p>

            <h2>2. Dados que Coletamos</h2>
            <p>Coletamos os seguintes tipos de informação:</p>

            <h3>2.1 Dados fornecidos por você</h3>
            <ul>
                <li><strong>Dados de cadastro:</strong> nome, e-mail e senha;</li>
                <li><strong>Dados financeiros:</strong> receitas, despesas, contas bancárias, orçamentos, metas, dívidas e investimentos que você registrar na plataforma;</li>
                <li><strong>Dados de contato:</strong> informações fornecidas ao nos enviar mensagens através do formulário de contato.</li>
            </ul>

            <h3>2.2 Dados coletados automaticamente</h3>
            <ul>
                <li><strong>Dados de uso:</strong> páginas acessadas, funcionalidades utilizadas, horários de acesso e frequência de uso;</li>
                <li><strong>Dados técnicos:</strong> endereço IP, tipo de navegador, sistema operacional e dispositivo utilizado;</li>
                <li><strong>Cookies:</strong> utilizamos cookies essenciais para o funcionamento da plataforma e cookies analíticos para melhorar a experiência.</li>
            </ul>

            <h2>3. Como Usamos Seus Dados</h2>
            <p>Utilizamos suas informações para:</p>
            <ul>
                <li>Fornecer, operar e manter o Serviço;</li>
                <li>Processar e exibir seus dados financeiros na plataforma;</li>
                <li>Enviar notificações relacionadas ao Serviço (alertas de orçamento, metas etc.);</li>
                <li>Responder a solicitações de suporte e comunicações;</li>
                <li>Melhorar e personalizar a experiência do usuário;</li>
                <li>Cumprir obrigações legais e regulatórias.</li>
            </ul>

            <h2>4. Compartilhamento de Dados</h2>
            <p><strong>Não vendemos, alugamos ou compartilhamos seus dados pessoais com terceiros para fins comerciais.</strong> Seus dados podem ser compartilhados apenas nas seguintes situações:</p>
            <ul>
                <li><strong>Provedores de serviço:</strong> empresas que nos auxiliam na operação da plataforma (hospedagem, e-mail), sob contratos que garantem a proteção dos seus dados;</li>
                <li><strong>Obrigação legal:</strong> quando exigido por lei, ordem judicial ou autoridade regulatória;</li>
                <li><strong>Proteção de direitos:</strong> para proteger os direitos, propriedade ou segurança do MyFinance e seus usuários.</li>
            </ul>

            <h2>5. Segurança dos Dados</h2>
            <p>Implementamos medidas técnicas e organizacionais para proteger seus dados, incluindo:</p>
            <ul>
                <li>Criptografia de dados em trânsito (HTTPS/TLS) e em repouso;</li>
                <li>Senhas armazenadas com hash bcrypt;</li>
                <li>Controle de acesso baseado em roles;</li>
                <li>Monitoramento contínuo de segurança;</li>
                <li>Backups regulares e criptografados.</li>
            </ul>

            <h2>6. Retenção de Dados</h2>
            <p>Mantemos seus dados enquanto sua conta estiver ativa. Ao solicitar a exclusão da conta, seus dados pessoais e financeiros serão removidos no prazo de 30 dias, exceto quando houver obrigação legal de retenção.</p>

            <h2>7. Seus Direitos (LGPD)</h2>
            <p>De acordo com a LGPD, você tem direito a:</p>
            <ul>
                <li><strong>Confirmação e acesso:</strong> saber se tratamos seus dados e acessá-los;</li>
                <li><strong>Correção:</strong> corrigir dados incompletos, inexatos ou desatualizados;</li>
                <li><strong>Anonimização ou eliminação:</strong> solicitar a anonimização ou eliminação de dados desnecessários;</li>
                <li><strong>Portabilidade:</strong> solicitar a transferência dos seus dados a outro fornecedor;</li>
                <li><strong>Revogação do consentimento:</strong> revogar o consentimento a qualquer momento;</li>
                <li><strong>Oposição:</strong> opor-se ao tratamento quando realizado com base em legítimo interesse.</li>
            </ul>
            <p>Para exercer qualquer um desses direitos, entre em contato pelo e-mail <strong>privacidade@myfinance.app</strong>.</p>

            <h2>8. Cookies</h2>
            <p>Utilizamos cookies para:</p>
            <ul>
                <li><strong>Cookies essenciais:</strong> necessários para o funcionamento básico da plataforma (autenticação, sessão);</li>
                <li><strong>Cookies analíticos:</strong> para entender como os usuários interagem com a plataforma e melhorar a experiência.</li>
            </ul>
            <p>Você pode configurar seu navegador para recusar cookies, mas isso pode afetar a funcionalidade da plataforma.</p>

            <h2>9. Menores de Idade</h2>
            <p>O MyFinance não é destinado a menores de 18 anos. Não coletamos intencionalmente dados de menores. Caso tome conhecimento de que um menor forneceu dados pessoais, entre em contato para que possamos tomar as medidas necessárias.</p>

            <h2>10. Alterações nesta Política</h2>
            <p>Podemos atualizar esta Política periodicamente. Alterações significativas serão comunicadas por e-mail ou notificação na plataforma. Recomendamos a revisão periódica desta página.</p>

            <h2>11. Contato</h2>
            <p>Para dúvidas sobre esta Política de Privacidade ou sobre o tratamento dos seus dados pessoais, entre em contato:</p>
            <ul>
                <li><strong>E-mail:</strong> privacidade@myfinance.app</li>
                <li><strong>Encarregado de Dados (DPO):</strong> dpo@myfinance.app</li>
            </ul>

        </div>
    </div>
</section>
@endsection
