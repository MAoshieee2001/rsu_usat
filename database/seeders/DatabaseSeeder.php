<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'Rojo',     'code' => 'FF0000'], // rojo puro
            ['name' => 'Verde',    'code' => '00FF00'], // verde puro
            ['name' => 'Azul',     'code' => '0000FF'], // azul puro
            ['name' => 'Amarillo', 'code' => 'FFFF00'], // amarillo puro
            ['name' => 'Negro',    'code' => '000000'], // negro puro
        ];

        DB::table('colors')->insert($colors);
        $this->call([
        EmployeeTypeSeeder::class,
        TiposContratoSeeder::class,
        UbigeoLambayequeSeeder::class,
        ]);
    }
}
