<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('demo:seed-api {--fresh : Recria as tabelas antes de popular os dados}', function () {
    if ($this->option('fresh')) {
        $this->call('migrate:fresh', ['--force' => true]);
    }

    $this->call('db:seed', ['--class' => \Database\Seeders\DemoCatalogSeeder::class, '--force' => true]);

    $baseUrl = rtrim(config('app.url'), '/');

    $this->newLine();
    $this->info('Fluxo manual pronto para teste.');
    $this->line('1. Abra '.$baseUrl.'/docs');
    $this->line('2. Faça POST em /api/login com demo@example.com / password');
    $this->line('3. Copie o token Bearer retornado.');
    $this->line('4. Autorize no Swagger UI e execute os endpoints protegidos.');
    $this->newLine();
    $this->table(
        ['Item', 'Valor'],
        [
            ['Base URL', $baseUrl],
            ['Docs', $baseUrl.'/docs'],
            ['Usuário demo', 'demo@example.com'],
            ['Senha demo', 'password'],
        ]
    );
})->purpose('Popula dados de demonstração para testar a API manualmente');
