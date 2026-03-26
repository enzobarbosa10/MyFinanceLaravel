# 💰 MyFinanceLaravel

Plataforma completa de gestão financeira pessoal construída com **Laravel 13** e **Tailwind CSS**. Controle contas, transações, orçamentos, metas, dívidas e investimentos em um só lugar.

## Funcionalidades

- **Contas** — Gerencie múltiplas contas bancárias com saldo atualizado automaticamente
- **Transações** — Registre receitas e despesas com categorização e suporte a recorrência
- **Orçamentos** — Defina limites mensais por categoria com alertas de proximidade do teto
- **Metas** — Estabeleça objetivos financeiros com prazo, acompanhamento de contribuições e progresso
- **Dívidas** — Controle dívidas com taxa de juros, histórico de pagamentos e saldo devedor
- **Investimentos** — Acompanhe ações, FIIs, renda fixa e criptomoedas (pré-cadastrado com ativos brasileiros)
- **Importação** — Importe transações de fontes externas
- **Planos e Assinaturas** — Sistema de planos com limites de funcionalidades por tier
- **Notificações** — Alertas de orçamento, lembretes de assinatura e avisos financeiros
- **Onboarding** — Fluxo guiado para novos usuários

## Tech Stack

| Camada   | Tecnologia                          |
|----------|-------------------------------------|
| Backend  | PHP 8.3+ / Laravel 13               |
| Frontend | Tailwind CSS 4 / Vite 7             |
| Banco    | SQLite (padrão, configurável)        |
| Testes   | PHPUnit 11.5                         |

## Requisitos

- PHP 8.3+
- Composer
- Node.js & npm

## Instalação

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

## Desenvolvimento

```bash
composer dev
```

Inicia simultaneamente o servidor Laravel, fila de jobs, logs e Vite em modo de desenvolvimento.

## Testes

```bash
composer test
```

## Estrutura do Banco de Dados

O projeto possui **16 models Eloquent** e **29+ tabelas**, incluindo:

| Modelo             | Finalidade                              |
|--------------------|-----------------------------------------|
| User               | Autenticação e relacionamento central   |
| Account            | Contas bancárias com saldo              |
| Transaction        | Receitas e despesas                     |
| Category           | Categorias (entrada/saída)              |
| Budget / BudgetAlert | Orçamentos mensais e alertas         |
| Goal / GoalContribution | Metas e contribuições              |
| Debt / DebtPayment | Dívidas e pagamentos                   |
| Investment / InvestmentAsset / InvestmentType | Portfólio de investimentos |
| Import             | Histórico de importações                |
| Plan / PlanFeature / UserSubscription | Sistema de assinaturas  |

## Ativos Pré-cadastrados (Seeder)

- **Ações**: PETR4, VALE3, ITUB4, BBDC4, BBAS3, ABEV3, MGLU3, WEGE3
- **FIIs**: HGLG11, XPML11, MXRF11, KNRI11, VISC11
- **Renda Fixa**: SELIC, IPCA+2029, IPCA+2035, PRE27, CDB, LCI
- **Criptomoedas**: BTC, ETH, SOL

## Licença

Este projeto é open-source sob a licença [MIT](https://opensource.org/licenses/MIT).
