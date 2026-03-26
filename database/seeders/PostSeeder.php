<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => '5 passos para sair das dívidas em 2026 (mesmo ganhando pouco)',
                'slug' => '5-passos-para-sair-das-dividas-em-2026',
                'content' => "Se você está endividado, saiba que não está sozinho: mais de 70 milhões de brasileiros estão na mesma situação. A boa notícia é que sair das dívidas não exige ganhar mais — exige um plano claro. Aqui estão 5 passos práticos que funcionam:\n\n1. Liste TODAS as suas dívidas\n\nPegue um papel ou abra o MyFinance e registre cada dívida: valor total, taxa de juros, parcela mensal e data de vencimento. Você vai se assustar com o total — e isso é bom. A clareza é o primeiro passo.\n\n2. Priorize pelo juro mais alto\n\nNem toda dívida é igual. Cartão de crédito cobra até 400% ao ano, enquanto um financiamento imobiliário pode ser de 8%. Concentre seus pagamentos extras na dívida com maior taxa de juros. Isso economiza milhares de reais.\n\n3. Negocie com credores\n\nMuitas empresas preferem receber com desconto do que não receber. Ligue, explique sua situação e peça desconto para pagamento à vista ou condições melhores. Feirões de negociação como o Serasa Limpa Nome são ótimas oportunidades.\n\n4. Crie um orçamento de guerra\n\nPor 3 a 6 meses, corte tudo que não é essencial. Delivery, streaming extra, compras por impulso — tudo. Redirecione esse dinheiro para as dívidas. É temporário, mas o impacto é permanente.\n\n5. Automatize com uma ferramenta\n\nPlanilha não funciona porque exige disciplina constante. Use uma ferramenta como o MyFinance que calcula automaticamente quanto falta, sugere prioridades e mostra seu progresso visual. Quando você vê a barra enchendo, a motivação vem naturalmente.\n\nO mais importante: comece hoje. Não espere o mês que vem, não espere o 13º, não espere a situação perfeita. Cada dia de juros compostos trabalhando contra você é dinheiro jogado fora.",
                'created_at' => now()->subDays(3),
            ],
            [
                'title' => 'Regra 50-30-20: o método mais simples para organizar seu salário',
                'slug' => 'regra-50-30-20-metodo-simples-organizar-salario',
                'content' => "Você recebe o salário e em duas semanas já não sabe onde foi parar? A regra 50-30-20 é o método mais simples e eficiente para distribuir sua renda — e funciona para qualquer faixa salarial.\n\nComo funciona:\n\n50% para necessidades — Aluguel, contas de luz/água/internet, alimentação, transporte, saúde. Tudo que você PRECISA para viver. Se essa fatia está passando de 50%, é sinal de que algo precisa ser reduzido ou sua renda precisa aumentar.\n\n30% para desejos — Restaurantes, streaming, roupas novas, viagens, hobbies. Sim, você pode gastar com prazer. A diferença é que agora tem um limite claro, então gasta sem culpa.\n\n20% para futuro — Reserva de emergência, investimentos, pagamento extra de dívidas, aposentadoria. Essa é a fatia que muda sua vida a longo prazo.\n\nExemplo prático:\n\nSalário de R$ 4.000:\n- R$ 2.000 para necessidades\n- R$ 1.200 para desejos\n- R$ 800 para futuro\n\nEm 12 meses, só a fatia de 20% já gerou R$ 9.600 em economia.\n\nDicas para aplicar:\n\nNo dia que receber o salário, já separe os 20% para uma conta ou investimento. Se o dinheiro ficar na conta corrente, ele vai ser gasto — é psicologia humana.\n\nUse o MyFinance para criar orçamentos por categoria e acompanhar se está dentro dos percentuais. O app avisa quando você está estourando alguma fatia.\n\nComece imperfeito. Se conseguir fazer 60-25-15 no primeiro mês, já é uma vitória enorme. O importante é o hábito, não a perfeição.\n\nA regra 50-30-20 não é mágica — é matemática simples. E é exatamente por ser simples que funciona.",
                'created_at' => now()->subDays(7),
            ],
            [
                'title' => 'Reserva de emergência: quanto guardar e onde investir',
                'slug' => 'reserva-de-emergencia-quanto-guardar-onde-investir',
                'content' => "Se você perdesse o emprego amanhã, por quantos meses conseguiria se manter? Se a resposta te deixa desconfortável, este artigo é para você.\n\nO que é reserva de emergência?\n\nÉ um dinheiro guardado exclusivamente para imprevistos: demissão, problema de saúde, conserto urgente do carro, emergência familiar. NÃO é para viagens, presentes ou oportunidades de investimento.\n\nQuanto guardar?\n\nA recomendação clássica é de 3 a 6 meses dos seus custos fixos mensais. Se você gasta R$ 3.000/mês para viver, sua reserva deve ser entre R$ 9.000 e R$ 18.000.\n\nPara CLT: 3 meses é suficiente (você tem FGTS e seguro-desemprego como backup).\nPara autônomos/PJ: 6 meses ou mais, já que a renda é variável.\n\nOnde investir?\n\nA reserva precisa ter 3 características: segurança, liquidez e rendimento acima da inflação.\n\nMelhores opções:\n\n- Tesouro Selic: título público, o mais seguro do Brasil. Rende acima da inflação e você pode resgatar a qualquer momento. É a escolha número 1.\n\n- CDB com liquidez diária: CDBs de bancos que paguem pelo menos 100% do CDI e permitam resgate imediato. Coberto pelo FGC até R$ 250 mil.\n\n- Conta remunerada: Nubank, Inter e outros oferecem rendimento automático. Não é o melhor rendimento, mas a praticidade é imbatível para o início.\n\nOnde NÃO colocar:\n\n- Poupança (rende menos que a inflação)\n- Ações (muito voláteis)\n- Criptomoedas (pode cair 50% em uma semana)\n- Qualquer investimento sem liquidez diária\n\nComo começar:\n\nNão espere ter os R$ 18 mil para começar. Comece com R$ 100/mês. No MyFinance, crie uma meta chamada \"Reserva de Emergência\", defina o valor alvo e contribua toda semana. A barra de progresso é surpreendentemente motivadora.\n\nEm 12 meses guardando R$ 500/mês, você terá R$ 6.000 + rendimentos. É dinheiro suficiente para dormir tranquilo sabendo que, se algo acontecer, você tem fôlego para reagir sem precisar se endividar.\n\nA reserva de emergência não é investimento — é seguro. E o melhor seguro é aquele que te dá tranquilidade todos os dias.",
                'created_at' => now()->subDays(14),
            ],
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }
    }
}
