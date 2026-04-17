<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Usuário Demo',
                'password' => 'password',
                'token_version' => 0,
            ]
        );

        $catalog = [
            [
                'name' => 'Proteínas',
                'description' => 'Suplementos proteicos para rotina esportiva e recuperação.',
                'products' => [
                    [
                        'name' => 'Whey Protein Concentrado',
                        'description' => 'Proteína concentrada para consumo diário.',
                        'price' => 149.90,
                        'available' => true,
                    ],
                    [
                        'name' => 'Whey Protein Isolado',
                        'description' => 'Versão com maior pureza proteica.',
                        'price' => 189.90,
                        'available' => true,
                    ],
                ],
            ],
            [
                'name' => 'Performance',
                'description' => 'Itens voltados para energia, força e performance.',
                'products' => [
                    [
                        'name' => 'Creatina Monohidratada',
                        'description' => 'Creatina para ganho de força e recuperação.',
                        'price' => 89.90,
                        'available' => true,
                    ],
                    [
                        'name' => 'Beta Alanina',
                        'description' => 'Auxilia na resistência durante treinos intensos.',
                        'price' => 74.90,
                        'available' => false,
                    ],
                ],
            ],
            [
                'name' => 'Bem-estar',
                'description' => 'Produtos para suporte diário, saúde e qualidade de vida.',
                'products' => [
                    [
                        'name' => 'Colágeno Hidrolisado',
                        'description' => 'Suporte para pele, unhas e articulações.',
                        'price' => 69.90,
                        'available' => true,
                    ],
                    [
                        'name' => 'Magnésio Quelato',
                        'description' => 'Suplemento para suporte metabólico e muscular.',
                        'price' => 59.90,
                        'available' => true,
                    ],
                ],
            ],
        ];

        foreach ($catalog as $categoryData) {
            $category = Category::query()->updateOrCreate(
                ['name' => $categoryData['name']],
                ['description' => $categoryData['description']]
            );

            foreach ($categoryData['products'] as $productData) {
                Product::query()->updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $productData['name'],
                    ],
                    [
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'available' => $productData['available'],
                    ]
                );
            }
        }

        $this->command?->info('Dados de demonstração carregados com sucesso.');
        $this->command?->line('Usuário: '.$user->email);
        $this->command?->line('Senha: password');
    }
}
