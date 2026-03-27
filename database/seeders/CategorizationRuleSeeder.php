<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorizationRuleSeeder extends Seeder
{
    /**
     * System-wide default rules (user_id = null).
     * These are applied to ALL users as fallback.
     *
     * pattern → substring matched (case-insensitive) against transaction description.
     * Categories reference the default names from Category::seedDefaults().
     */
    public function run(): void
    {
        $rules = [
            // ── Transporte ───────────────────────────────────
            ['pattern' => 'UBER',              'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => '99POP',             'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => '99 POP',            'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => 'CABIFY',            'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => 'SHELL',             'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => 'IPIRANGA',          'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => 'BR DISTRIBUIDORA',  'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => 'ESTAPAR',           'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => 'SEM PARAR',         'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => 'CONECTCAR',         'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],
            ['pattern' => 'METRO',             'category' => 'Transporte',    'type' => 'saida', 'priority' => 0],

            // ── Alimentação ──────────────────────────────────
            ['pattern' => 'IFOOD',             'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'RAPPI',             'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'ZDELIVERY',         'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'MCDONALD',          'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'BURGER KING',       'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'SUBWAY',            'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'STARBUCKS',         'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'PADARIA',           'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'RESTAURANTE',       'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'SUPERMERCADO',      'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'CARREFOUR',         'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'PAO DE ACUCAR',     'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'EXTRA HIPER',       'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],
            ['pattern' => 'ASSAI',             'category' => 'Alimentação',   'type' => 'saida', 'priority' => 0],

            // ── Saúde ────────────────────────────────────────
            ['pattern' => 'DROGASIL',          'category' => 'Saúde',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'DROGA RAIA',        'category' => 'Saúde',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'DROGARIA',          'category' => 'Saúde',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'FARMACIA',          'category' => 'Saúde',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'UNIMED',            'category' => 'Saúde',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'AMIL',              'category' => 'Saúde',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'SULAMERICA SAUDE',  'category' => 'Saúde',         'type' => 'saida', 'priority' => 0],

            // ── Educação ─────────────────────────────────────
            ['pattern' => 'UDEMY',             'category' => 'Educação',      'type' => 'saida', 'priority' => 0],
            ['pattern' => 'ALURA',             'category' => 'Educação',      'type' => 'saida', 'priority' => 0],
            ['pattern' => 'ROCKETSEAT',        'category' => 'Educação',      'type' => 'saida', 'priority' => 0],
            ['pattern' => 'COURSERA',          'category' => 'Educação',      'type' => 'saida', 'priority' => 0],
            ['pattern' => 'HOTMART',           'category' => 'Educação',      'type' => 'saida', 'priority' => 0],

            // ── Lazer ────────────────────────────────────────
            ['pattern' => 'NETFLIX',           'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'SPOTIFY',           'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'DISNEY',            'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'HBO MAX',           'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'PRIME VIDEO',       'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'AMAZON PRIME',      'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'STEAM',             'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'PLAYSTATION',       'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'CINEMA',            'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],
            ['pattern' => 'CINEMARK',          'category' => 'Lazer',         'type' => 'saida', 'priority' => 0],

            // ── Moradia ──────────────────────────────────────
            ['pattern' => 'ENEL',              'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],
            ['pattern' => 'CPFL',              'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],
            ['pattern' => 'SABESP',            'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],
            ['pattern' => 'COPASA',            'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],
            ['pattern' => 'COMGAS',            'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],
            ['pattern' => 'VIVO FIBRA',        'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],
            ['pattern' => 'CLARO NET',         'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],
            ['pattern' => 'ALUGUEL',           'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],
            ['pattern' => 'CONDOMINIO',        'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],
            ['pattern' => 'IPTU',              'category' => 'Moradia',       'type' => 'saida', 'priority' => 0],

            // ── Entrada (Salário / Freelance) ────────────────
            ['pattern' => 'SALARIO',           'category' => 'Salário',       'type' => 'entrada', 'priority' => 0],
            ['pattern' => 'FOLHA PGTO',        'category' => 'Salário',       'type' => 'entrada', 'priority' => 0],
            ['pattern' => 'PAGAMENTO MENSAL',  'category' => 'Salário',       'type' => 'entrada', 'priority' => 0],
            ['pattern' => 'FREELANCE',         'category' => 'Freelance',     'type' => 'entrada', 'priority' => 0],
            ['pattern' => 'RENDIMENTO',        'category' => 'Rendimentos',   'type' => 'entrada', 'priority' => 0],
            ['pattern' => 'DIVIDENDO',         'category' => 'Rendimentos',   'type' => 'entrada', 'priority' => 0],
            ['pattern' => 'JUROS S/CAPITAL',   'category' => 'Rendimentos',   'type' => 'entrada', 'priority' => 0],
        ];

        $now = now();

        foreach ($rules as $rule) {
            DB::table('categorization_rules')->insert([
                'user_id'       => null,
                'category_id'   => null,
                'category_name' => $rule['category'],
                'pattern'       => $rule['pattern'],
                'type'          => $rule['type'],
                'source'        => 'system',
                'priority'      => $rule['priority'],
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
