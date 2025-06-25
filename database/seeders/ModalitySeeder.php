<?php

namespace Database\Seeders;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalities = [
            ['name' => 'Lunes a Viernes'],
            ['name' => 'Lunes, Miércoles y Viernes'],
        ];

        DB::table('modalities')->insert($modalities);
    }
}
