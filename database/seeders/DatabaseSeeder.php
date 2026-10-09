<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('=== INICIANDO DATABASE SEEDER (SISLIC) ===');

        $this->command->info('Executando FDWSeeder...');
        $this->call([
            FDWSeeder::class,
        ]);
        $this->command->info('FDWSeeder concluído.');

        // 1. Procurar o usuário admin
        $this->command->info('Procurando usuário admin@sislic.com...');
        $admUser = User::where('email', 'admin@sislic.com')->first();

        if (!$admUser) {
            $this->command->error('ERRO: Usuário admin@sislic.com NÃO FOI ENCONTRADO no banco!');
            
            // Exibir no console os e-mails dos usuários que existem atualmente no banco
            $emails = User::pluck('email')->toArray();
            $this->command->warn('E-mails encontrados no banco: ' . implode(', ', $emails));
            return;
        }

        $this->command->info("Usuário encontrado! ID: {$admUser->id}");

        // 2. Garante ou cria o registro do profissional
        $this->command->info('Criando/Buscando Profissional...');
        $professional = $admUser->professional()->firstOrCreate(
            ['user_id' => $admUser->id],
            [
                'name'         => $admUser->name ?? 'Administrador',
                'phone'        => '0000000000',
                'registration' => '000000',
            ]
        );
        $this->command->info("Profissional pronto! ID: {$professional->id}");

        // 3. Associa a lotação/tipo 'Administrador'
        $hasAdminType = $professional->types()->where('type', 'Administrador')->exists();
        
        if (!$hasAdminType) {
            $this->command->info('Vinculando tipo Administrador ao profissional...');
            $professional->types()->create([
                'type' => 'Administrador'
            ]);
            $this->command->info('Tipo Administrador associado com sucesso!');
        } else {
            $this->command->warn('Profissional já possuía o tipo Administrador.');
        }

        $this->command->info('=== DATABASE SEEDER CONCLUÍDO COM SUCESSO ===');
    }
}