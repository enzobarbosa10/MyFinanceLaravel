# 💰 MyFinanceLaravel

![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)
![Laravel Version](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-38B2AC?style=for-the-badge&logo=tailwind-css)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

> **Plataforma completa de gestão financeira pessoal construída com Laravel e Tailwind CSS.**
> Controle contas, transações, orçamentos, metas, dívidas, investimentos e parcelas de forma inteligente — contando com inteligência artificial, Open Finance e um sistema robusto de assinaturas (SaaS).

---

## 📌 Índice

- [Funcionalidades](#-funcionalidades)
- [Tech Stack](#-tech-stack)
- [Arquitetura](#-arquitetura)
- [Instalação e Configuração](#-instalação-e-configuração)
- [Desenvolvimento](#-desenvolvimento)
- [Testes](#-testes)
- [Documentação da API](#-api-endpoints)
- [Banco de Dados](#-banco-de-dados)
- [Comandos e Seeders](#-comandos-e-seeders)
- [Licença](#-licença)

---

## 🚀 Funcionalidades

### 🏦 Núcleo Financeiro
- **Contas** — Gerencie múltiplas contas bancárias com saldo atualizado automaticamente.
- **Transações** — Registre receitas e despesas com categorização inteligente (regras do sistema, do usuário e via IA) e suporte a recorrência.
- **Orçamentos** — Defina limites mensais por categoria com alertas configuráveis de aproximação do teto.
- **Metas** — Estabeleça objetivos financeiros com prazos, acompanhamento de progresso e cálculo de aporte mensal.
- **Dívidas** — Controle taxas de juros, histórico de pagamentos e saldo devedor.
- **Investimentos** — Acompanhe ações, FIIs, renda fixa e criptomoedas (ativos brasileiros pré-cadastrados).
- **Parcelas** — Gestão detalhada de compras parceladas com status individual (pendente/pago/vencido).

### 🧠 Inteligência & Insights
- **Assistente IA (OpenAI)** — Faça perguntas sobre suas finanças e receba respostas contextualizadas (uso controlado por plano).
- **Motor de Insights** — Detecção automática de aumento de gastos, dominância de categorias e projeção de saldo negativo.
- **Projeções Financeiras** — Previsão de saldo para 1, 3 e 6 meses com base em recorrências, assinaturas e parcelas.

### 💳 Sistema SaaS (Planos & Assinaturas)
- **3 Tiers** — Planos Free, Pro e Premium com limites e funcionalidades configuráveis.
- **Gateways** — Integração com Stripe e PagSeguro via interface abstraída.
- **Metering & Billing** — Controle de uso de features (ex: limite de prompts IA) e ciclo de vida completo (trial, ativação, upgrade/downgrade, churn).
- **Webhooks Seguros** — Verificação de assinatura, chaves de idempotência e proteção contra *replay attacks*.

### 🔌 Integrações & Notificações
- **Open Finance (Pluggy API)** — Sincronização automática de contas e transações.
- **Alertas Inteligentes** — Saldo baixo, gasto incomum, meta atrasada e ciclo de assinatura (upsell/recuperação).
- **Painel Admin** — Métricas cruciais de SaaS (MRR, ARR, Churn, LTV, conversão).

---

## 💻 Tech Stack

| Camada         | Tecnologia                                  |
|----------------|---------------------------------------------|
| **Backend** | PHP 8.2+ / Laravel 11                       |
| **Frontend** | Tailwind CSS 4 / Vite 7                     |
| **Auth** | Laravel Sanctum (SPA + API Bearer tokens)   |
| **Banco** | SQLite (padrão, configurável para MySQL/PG) |
| **Pagamentos** | Stripe / PagSeguro                          |
| **Open Finance**| Pluggy API                                  |
| **IA** | OpenAI (GPT-4o mini)                        |
| **Testes** | PHPUnit 11                                  |

---

## 🏗 Arquitetura

O projeto foi desenhado focando em manutenibilidade, separação de responsabilidades e escalabilidade:

```text
app/
├── Console/Commands/     # 7 comandos agendáveis (churn, trial, upsell...)
├── Contracts/            # Ex: PaymentGatewayInterface
├── Enums/                # Ex: TransactionType, GoalStatus
├── Events/               # Eventos orientados ao domínio financeiro
├── Exceptions/           # Exceções customizadas de negócio
├── Http/
│   ├── Controllers/      # 13 web + 6 API controllers finos
│   ├── Middleware/        # Paywall, feature gate, webhook verify...
│   └── Requests/         # Form requests com validação estrita
├── Jobs/                 # SyncOpenFinanceData (retry e prevenção de overlap)
├── Listeners/            # Notificações, analytics e auditoria
├── Models/               # 34 models Eloquent
├── Services/             # 20+ services contendo a lógica de negócio real
│   ├── Gateways/         # Implementações Stripe e PagSeguro
│   └── InsightEngine/    # Motor de regras empilháveis
└── Support/              # Helpers e Mappers
```

**Destaques:**
- **Service Layer** — Lógica de negócio em services, controllers finos
- **Event-Driven** — Eventos financeiros disparam listeners em fila
- **Gateway Abstraction** — Interface `PaymentGatewayInterface` para múltiplos provedores
- **Feature Gating** — Middlewares controlam acesso por plano e uso
- **Webhook Security** — Verificação de assinatura + idempotência + proteção contra replay
- **Cache Strategy** — Dashboard cacheado 2min, métricas de billing 10min

---

## ⚙ Instalação e Configuração

### Requisitos

- PHP 8.2+
- Composer
- Node.js & npm

### Setup Rápido

```bash
git clone <repo-url> MyFinanceLaravel
cd MyFinanceLaravel
composer setup
```

O comando `composer setup` executa automaticamente:

1. `composer install`
2. Copia `.env.example` → `.env`
3. Gera a chave da aplicação
4. Executa as migrations
5. Instala dependências npm e faz o build

### Variáveis de Ambiente Opcionais

```env
# Pagamentos
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
PAGSEGURO_EMAIL=
PAGSEGURO_TOKEN=

# Open Finance
PLUGGY_CLIENT_ID=
PLUGGY_CLIENT_SECRET=

# IA
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini

# Notificações operacionais
SLACK_BOT_TOKEN=
OPERATIONS_ALERT_EMAIL=
```

---

## 🔧 Desenvolvimento

```bash
composer dev
```

Inicia simultaneamente o servidor Laravel, fila de jobs, logs e Vite em modo de desenvolvimento.

---

## 🧪 Testes

```bash
composer test
```

**14 arquivos de teste** cobrindo funcionalidades core e módulo de assinaturas:

| Tipo | Testes |
|------|--------|
| **Unit** | Budget, Debt, Goal, Transaction |
| **Feature** | Subscription API, Billing Metrics, Product Insights, Feature Usage Limits, Intelligent Paywall, Upsell Campaign, Subscription Financial Grade, Subscription Load Concept |

---

## 📡 API Endpoints

### Públicos

| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/api/plans` | Lista planos ativos com features |
| `POST` | `/api/webhooks/pluggy` | Webhook Open Finance |
| `POST` | `/api/webhooks/payments/{gateway}` | Webhook Stripe/PagSeguro |

### Autenticados (Bearer Token)

| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/api/plans/current` | Plano atual do usuário |
| `POST` | `/api/plans/subscribe` | Assinar plano |
| `PUT` | `/api/plans/change` | Upgrade/downgrade |
| `POST` | `/api/plans/cancel` | Cancelar assinatura |
| `GET` | `/api/dashboard` | Resumo financeiro completo |
| `GET` | `/api/dashboard/saldo` | Saldo total |
| `GET` | `/api/dashboard/gastos-mes` | Gastos do mês |
| `GET` | `/api/dashboard/gastos-categoria` | Gastos por categoria |
| `GET` | `/api/dashboard/projecao` | Projeção de saldo |
| `GET` | `/api/dashboard/insights` | Insights gerados por IA |
| `POST` | `/api/assistant/ask` | Assistente IA (usage-limited) |

### Admin

| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/api/admin/billing/metrics` | MRR, ARR, churn, LTV |
| `GET` | `/api/admin/product/insights` | Segmentos e uso |

---

## 🗄 Banco de Dados

**34 models Eloquent** organizados por domínio:

| Domínio | Modelos |
|---------|---------|
| **Core Financeiro** | User, Account, Transaction, Category |
| **Orçamentos** | Budget, BudgetAlert |
| **Metas** | Goal, GoalContribution |
| **Dívidas** | Debt, DebtPayment |
| **Investimentos** | Investment, InvestmentAsset, InvestmentType |
| **Parcelas** | Installment, InstallmentItem |
| **Assinaturas** | Plan, PlanFeature, UserSubscription, Payment |
| **Uso & Métricas** | FeatureUsage, UsageRecord, UsageAggregate |
| **Auditoria** | SubscriptionLog, IdempotencyKey, ProcessedEvent, LoginAttempt |
| **Insights & Notificações** | Insight, Notification, NotificationPreference |
| **Categorização** | CategorizationRule |
| **Importação** | Import |
| **Conteúdo** | Post, Contact |

---

## 🔁 Comandos e Seeders

### Comandos Agendáveis

| Comando | Descrição |
|---------|-----------|
| `financial:check-notifications` | Verifica saldo baixo e gastos incomuns |
| `subscriptions:expire` | Marca assinaturas expiradas |
| `subscriptions:mark-past-due` | Sinaliza pagamentos em atraso |
| `subscriptions:optimize-trial` | Envia nudges para usuários em trial |
| `subscriptions:process-usage` | Agrega uso e aplica limites |
| `subscriptions:recover-churn` | Tenta recuperar pagamentos em atraso |
| `subscriptions:run-upsell` | Envia notificações de upsell |

### Seeders

| Seeder | Conteúdo |
|--------|----------|
| `PlanSeeder` | Planos Free, Pro e Premium com features |
| `InvestmentSeeder` | Ativos brasileiros pré-cadastrados |
| `CategorizationRuleSeeder` | Regras de categorização do sistema |
| `PostSeeder` | Posts do blog |

<details>
<summary><strong>Ativos Pré-cadastrados</strong></summary>

- **Ações**: PETR4, VALE3, ITUB4, BBDC4, BBAS3, ABEV3, MGLU3, WEGE3
- **FIIs**: HGLG11, XPML11, MXRF11, KNRI11, VISC11
- **Renda Fixa**: SELIC, IPCA+2029, IPCA+2035, PRE27, CDB, LCI
- **Criptomoedas**: BTC, ETH, SOL

</details>

---

## 📄 Licença

Este projeto é open-source sob a licença [MIT](https://opensource.org/licenses/MIT).

```text
app/
├── Console/Commands/     # 7 comandos agendáveis (churn, trial, upsell...)
├── Contracts/            # Ex: PaymentGatewayInterface
├── Enums/                # Ex: TransactionType, GoalStatus
├── Events/               # Eventos orientados ao domínio financeiro
├── Exceptions/           # Exceções customizadas de negócio
├── Http/
│   ├── Controllers/      # 13 web + 6 API controllers finos
│   ├── Middleware/       # Paywall, feature gate, webhook verify...
│   └── Requests/         # Form requests com validação estrita
├── Jobs/                 # SyncOpenFinanceData (retry e prevenção de overlap)
├── Listeners/            # Notificações, analytics e auditoria
├── Models/               # 34 models Eloquent
├── Services/             # 20+ services contendo a lógica de negócio real
│   ├── Gateways/         # Implementações Stripe e PagSeguro
│   └── InsightEngine/    # Motor de regras empilháveis
└── Support/              # Helpers e Mappers