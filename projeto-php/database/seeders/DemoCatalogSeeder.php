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
                'description' => 'Fórmulas proteicas com foco em performance, recuperação e sabor.',
                'products' => [
                    [
                        'name' => 'Vanilla Whey',
                        'description' => 'Whey hidrolisado e isolado com colágeno em peptídeos e notas de baunilha.',
                        'image_url' => 'https://d3eomlzmsu8e9b.cloudfront.net/media/catalog/product/cache/31c30eac1a1b5c34a29d797d955fb1c3/v/a/vanilla_whey_latao_1308x1636px_1.jpg',
                        'price' => 494.00,
                        'available' => true,
                    ],
                    [
                        'name' => 'Açaí Whey',
                        'description' => 'Blend proteico com açaí orgânico do Pará e banana.',
                        'image_url' => 'https://d3eomlzmsu8e9b.cloudfront.net/media/catalog/product/cache/26856556595e8df4b5035801b01cd909/a/c/acai_whey_lata_media_1308x1636px.jpg',
                        'price' => 370.00,
                        'available' => true,
                    ],
                    [
                        'name' => 'H.I. Whey',
                        'description' => 'Whey hidrolisado e isolado sem aromas e sem adoçantes.',
                        'image_url' => 'https://d3eomlzmsu8e9b.cloudfront.net/media/catalog/product/cache/26856556595e8df4b5035801b01cd909/h/i/hi_whey_1308x1636px_1.jpg',
                        'price' => 330.00,
                        'available' => true,
                    ],
                ],
            ],
            [
                'name' => 'Treino',
                'description' => 'Produtos voltados para força, energia e performance física.',
                'products' => [
                    [
                        'name' => 'Crealift',
                        'description' => 'Creatina mono-hidratada com Creapure para alta performance.',
                        'image_url' => 'https://d3eomlzmsu8e9b.cloudfront.net/media/catalog/product/cache/31c30eac1a1b5c34a29d797d955fb1c3/c/r/crealift_lata_pequena_01.jpg',
                        'price' => 357.00,
                        'available' => true,
                    ],
                    [
                        'name' => 'Beta Alanina',
                        'description' => 'Suporte para resistência e treinos de alta intensidade.',
                        'image_url' => null,
                        'price' => 119.00,
                        'available' => false,
                    ],
                ],
            ],
            [
                'name' => 'Ômega-3',
                'description' => 'Itens voltados para pureza lipídica e cuidado diário.',
                'products' => [
                    [
                        'name' => 'Super Omega-3 TG',
                        'description' => 'Ômega-3 com alta concentração de EPA e DHA na forma TG.',
                        'image_url' => 'https://d3eomlzmsu8e9b.cloudfront.net/media/wysiwyg/produtos/omega/mobile/omega-3-essential-vistas.jpg',
                        'price' => 175.00,
                        'available' => true,
                    ],
                    [
                        'name' => 'CoQ10',
                        'description' => 'Coenzima Q10 com ômega-3 TG e vitamina E.',
                        'image_url' => 'https://d3eomlzmsu8e9b.cloudfront.net/media/wysiwyg/produtos/omega/mobile/omega-3-essential-vistas.jpg',
                        'price' => 298.00,
                        'available' => true,
                    ],
                ],
            ],
            [
                'name' => 'Vitaminas e Minerais',
                'description' => 'Soluções para rotina, equilíbrio de nutrientes e bem-estar.',
                'products' => [
                    [
                        'name' => 'Vitalift',
                        'description' => 'Multivitamínico com vitaminas e minerais de alta absorção.',
                        'image_url' => 'https://d3eomlzmsu8e9b.cloudfront.net/media/wysiwyg/home/linha-clinica.jpg',
                        'price' => 235.00,
                        'available' => true,
                    ],
                    [
                        'name' => 'Mg Complex',
                        'description' => 'Blend em cápsulas com quatro fontes de magnésio.',
                        'image_url' => 'https://d3eomlzmsu8e9b.cloudfront.net/media/wysiwyg/home/linha-clinica.jpg',
                        'price' => 112.00,
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
                        'image_url' => $productData['image_url'] ?? null,
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
