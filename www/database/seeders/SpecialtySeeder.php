<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            ['name' => 'Cardiologia', 'description' => 'Foco total no acompanhamento vascular e sistema cardíaco com exames sofisticados.'],
            ['name' => 'Pediatria', 'description' => 'Ambiente lúdico preparado para atender nossos pequenos com conforto psicológico e clínico.'],
            ['name' => 'Dermatologia', 'description' => 'Tratamentos estéticos e patológicos da pele, unhas e cabelos com equipamentos laser.'],
            ['name' => 'Ortopedia', 'description' => 'Especialistas focados na recuperação muscular e óssea para performance.'],
            ['name' => 'Oftalmologia', 'description' => 'Cuidados avançados com a sua visão, do óculos ao bloco cirúrgico.'],
            ['name' => 'Ginecologia', 'description' => 'Cuidado feminino completo e preventivo na saúde da mulher.'],
            ['name' => 'Neurologia', 'description' => 'Acompanhamento do sistema nervoso, oferecendo tratamentos precisos.'],
            ['name' => 'Psiquiatria', 'description' => 'Tratamento mental humanizado, lidando com os desafios contemporâneos da sociedade.'],
        ];

        foreach ($specialties as $sp) {
            Specialty::firstOrCreate(['name' => $sp['name']], $sp);
        }
    }
}
