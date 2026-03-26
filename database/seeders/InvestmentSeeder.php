<?php

namespace Database\Seeders;

use App\Models\InvestmentType;
use App\Models\InvestmentAsset;
use Illuminate\Database\Seeder;

class InvestmentSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Ações',
                'assets' => [
                    ['name' => 'Petrobras PN', 'symbol' => 'PETR4'],
                    ['name' => 'Vale ON', 'symbol' => 'VALE3'],
                    ['name' => 'Itaú Unibanco PN', 'symbol' => 'ITUB4'],
                    ['name' => 'Bradesco PN', 'symbol' => 'BBDC4'],
                    ['name' => 'Banco do Brasil ON', 'symbol' => 'BBAS3'],
                    ['name' => 'Ambev ON', 'symbol' => 'ABEV3'],
                    ['name' => 'Magazine Luiza ON', 'symbol' => 'MGLU3'],
                    ['name' => 'WEG ON', 'symbol' => 'WEGE3'],
                ],
            ],
            [
                'name' => 'FIIs',
                'assets' => [
                    ['name' => 'HGLG11', 'symbol' => 'HGLG11'],
                    ['name' => 'XPML11', 'symbol' => 'XPML11'],
                    ['name' => 'MXRF11', 'symbol' => 'MXRF11'],
                    ['name' => 'KNRI11', 'symbol' => 'KNRI11'],
                    ['name' => 'VISC11', 'symbol' => 'VISC11'],
                ],
            ],
            [
                'name' => 'Renda Fixa',
                'assets' => [
                    ['name' => 'Tesouro Selic', 'symbol' => 'SELIC'],
                    ['name' => 'Tesouro IPCA+ 2029', 'symbol' => 'IPCA29'],
                    ['name' => 'Tesouro IPCA+ 2035', 'symbol' => 'IPCA35'],
                    ['name' => 'Tesouro Prefixado 2027', 'symbol' => 'PRE27'],
                    ['name' => 'CDB 100% CDI', 'symbol' => 'CDB100'],
                    ['name' => 'LCI 90% CDI', 'symbol' => 'LCI90'],
                ],
            ],
            [
                'name' => 'Criptomoedas',
                'assets' => [
                    ['name' => 'Bitcoin', 'symbol' => 'BTC'],
                    ['name' => 'Ethereum', 'symbol' => 'ETH'],
                    ['name' => 'Solana', 'symbol' => 'SOL'],
                ],
            ],
        ];

        foreach ($types as $typeData) {
            $type = InvestmentType::create([
                'name' => $typeData['name'],
            ]);

            foreach ($typeData['assets'] as $asset) {
                InvestmentAsset::create([
                    'type_id' => $type->id,
                    'name' => $asset['name'],
                    'symbol' => $asset['symbol'],
                ]);
            }
        }
    }
}
