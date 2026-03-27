<?php

namespace App\Services;

use App\Enums\GoalStatus;
use App\Enums\TransactionType;
use App\Models\User;
use App\Exceptions\AiAssistantException;
use Illuminate\Support\Facades\Http;

class FinancialAssistantService
{
    private const MAX_RESPONSE_TOKENS = 300;

    private string $systemPrompt = '';

    public function ask(User $user, string $question): array
    {
        $financialData = $this->gatherFinancialData($user);
        $this->systemPrompt = $this->detectSystemPrompt($question);
        $prompt = $this->buildPrompt($question, $financialData);

        $reply = $this->callOpenAi($prompt);

        return [
            'question' => $question,
            'answer'   => $reply,
            'context'  => $financialData,
        ];
    }

    private function gatherFinancialData(User $user): array
    {
        $month = now()->format('Y-m');

        $balance = round((float) $user->accounts()->sum('balance'), 2);

        $monthlyIncome = round((float) $user->transactions()
            ->forMonth($month)
            ->where('type', TransactionType::Entrada)
            ->sum('amount'), 2);

        $monthlyExpenses = round((float) $user->transactions()
            ->forMonth($month)
            ->where('type', TransactionType::Saida)
            ->sum('amount'), 2);

        $fixedExpenses = round((float) $user->transactions()
            ->forMonth($month)
            ->where('type', TransactionType::Saida)
            ->where('is_recurring', true)
            ->sum('amount'), 2);

        $variableExpenses = round($monthlyExpenses - $fixedExpenses, 2);

        $savings = round($monthlyIncome - $monthlyExpenses, 2);

        $goals = $user->goals()
            ->where('status', GoalStatus::Active)
            ->get(['name', 'target_amount', 'current_amount', 'deadline'])
            ->map(fn ($g) => [
                'nome'       => $g->name,
                'meta'       => round((float) $g->target_amount, 2),
                'atual'      => round((float) $g->current_amount, 2),
                'falta'      => round((float) $g->target_amount - (float) $g->current_amount, 2),
                'prazo'      => $g->deadline?->format('Y-m-d'),
                'progresso'  => $g->target_amount > 0
                    ? round(((float) $g->current_amount / (float) $g->target_amount) * 100, 1) . '%'
                    : '0%',
            ])
            ->values()
            ->all();

        return [
            'balance'           => $balance,
            'monthly_income'    => $monthlyIncome,
            'monthly_expenses'  => $monthlyExpenses,
            'fixed_expenses'    => $fixedExpenses,
            'variable_expenses' => $variableExpenses,
            'savings'           => $savings,
            'goals'             => $goals,
        ];
    }

    private function buildPrompt(string $question, array $data): string
    {
        $goalsJson = ! empty($data['goals'])
            ? json_encode($data['goals'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            : 'Nenhuma meta cadastrada.';

        $specializedInstructions = $this->detectIntent($question, $data);

        return <<<PROMPT
        Você é um assistente financeiro pessoal especialista.

        Seu objetivo é ajudar o usuário a tomar decisões financeiras inteligentes com base em dados reais.

        REGRAS:
        - Use SOMENTE os dados fornecidos
        - NÃO invente valores ou suposições
        - Seja direto, claro e prático
        - Sempre use números nas respostas
        - Sempre dê uma recomendação final clara
        - Se faltar informação, diga explicitamente

        ESTILO:
        - Resposta curta (máx. 5 linhas)
        - Linguagem simples
        - Tom de consultor financeiro

        FORMATO:
        1. Resposta direta
        2. Explicação com base nos números
        3. Sugestão prática final

        {$specializedInstructions}

        PERGUNTA DO USUÁRIO:
        {$question}

        DADOS FINANCEIROS:

        Saldo atual: R$ {$data['balance']}
        Receita mensal: R$ {$data['monthly_income']}
        Gastos mensais: R$ {$data['monthly_expenses']}
        Gastos fixos: R$ {$data['fixed_expenses']}
        Gastos variáveis: R$ {$data['variable_expenses']}
        Economias atuais: R$ {$data['savings']}

        METAS:
        {$goalsJson}

        REGRAS IMPORTANTES:
        - Baseie sua resposta nesses dados
        - Não generalize
        - Faça cálculos quando necessário

        Responda agora.
        PROMPT;
    }

    private function detectIntent(string $question, array $data): string
    {
        $intent = $this->classifyIntent($question);

        return match ($intent) {
            'spending' => $this->buildSpendingIntent($data),
            'savings'  => $this->buildSavingsIntent($data),
            'goal'     => $this->buildGoalIntent($data),
            default    => '',
        };
    }

    private function classifyIntent(string $question): string
    {
        $lower = mb_strtolower($question);

        $intents = [
            'spending' => ['posso gastar', 'posso comprar', 'dá pra gastar'],
            'savings'  => ['economizar', 'economia', 'guardar', 'poupar'],
            'goal'     => ['meta', 'objetivo', 'quando vou', 'quanto tempo'],
        ];

        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lower, $keyword)) {
                    return $intent;
                }
            }
        }

        return 'general';
    }

    private function buildSpendingIntent(array $data): string
    {
        $safeLimit = round(max($data['savings'] * 0.5, 0), 2);

        return <<<INTENT
        CONTEXTO ESPECIALIZADO — ANÁLISE DE GASTO:
        Analise se o usuário pode gastar o valor mencionado.
        Verifique:
        - Saldo atual vs valor pedido
        - Margem livre: R$ {$data['savings']} (receita - gastos)
        - Limite seguro sugerido (50% da margem): R$ {$safeLimit}
        - Impacto nos gastos do mês e nas metas ativas
        Responda:
        1. Se pode ou não gastar (SIM/NÃO)
        2. Por quê, com números
        3. Impacto financeiro no mês
        4. Valor alternativo seguro se não for recomendado
        INTENT;
    }

    private function buildSavingsIntent(array $data): string
    {
        $idealSavings = round($data['monthly_income'] * 0.20, 2);
        $savingsPercent = $data['monthly_income'] > 0
            ? round(($data['savings'] / $data['monthly_income']) * 100, 1)
            : 0;

        return <<<INTENT
        CONTEXTO ESPECIALIZADO — CAPACIDADE DE ECONOMIA:
        Calcule a capacidade real de economia do usuário.
        Dados pré-calculados:
        - Margem atual: R$ {$data['savings']} ({$savingsPercent}% da renda)
        - Meta ideal (20% da renda): R$ {$idealSavings}
        - Gastos variáveis (reduzíveis): R$ {$data['variable_expenses']}
        Responda:
        1. Quanto pode economizar por mês (valor e %)
        2. Se está acima ou abaixo do ideal de 20%
        3. Sugestão prática para melhorar (focando em gastos variáveis)
        INTENT;
    }

    private function buildGoalIntent(array $data): string
    {
        $monthlySavings = max($data['savings'], 0);

        return <<<INTENT
        CONTEXTO ESPECIALIZADO — PREVISÃO DE META:
        Calcule a previsão para atingir as metas ativas do usuário.
        Para cada meta, calcule:
        - Quanto falta = meta - atual
        - Capacidade mensal de economia: R$ {$monthlySavings}
        - Meses estimados = falta / economia mensal
        - Compare com o prazo definido (se houver)
        Responda:
        1. Tempo estimado para atingir cada meta
        2. Se o ritmo atual é suficiente para bater o prazo
        3. Sugestão para acelerar (valor mensal extra necessário)
        INTENT;
    }

    private function detectSystemPrompt(string $question): string
    {
        $prompts = [
            'spending' => 'Você é um consultor financeiro pessoal brasileiro rigoroso. Analise gastos com cautela. Proteja o patrimônio do usuário. Responda em português do Brasil.',
            'savings'  => 'Você é um consultor financeiro pessoal brasileiro focado em poupança e planejamento. Incentive hábitos de economia. Responda em português do Brasil.',
            'goal'     => 'Você é um consultor financeiro pessoal brasileiro especialista em planejamento de metas. Seja realista com prazos. Responda em português do Brasil.',
            'general'  => 'Você é um consultor financeiro pessoal brasileiro. Responda sempre em português do Brasil.',
        ];

        $intent = $this->classifyIntent($question);

        return $prompts[$intent] ?? $prompts['general'];
    }

    private function callOpenAi(string $prompt): string
    {
        $apiKey = config('services.openai.key');
        $model = config('services.openai.model', 'gpt-4.1-mini');

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model'       => $model,
            'messages'    => [
                ['role' => 'system', 'content' => $this->systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens'  => self::MAX_RESPONSE_TOKENS,
            'temperature' => 0.3,
        ]);

        if ($response->failed()) {
            throw new AiAssistantException('Falha ao consultar a IA: ' . $response->body());
        }

        return $response->json('choices.0.message.content', 'Não foi possível gerar uma resposta.');
    }
}
