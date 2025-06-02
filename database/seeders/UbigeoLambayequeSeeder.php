<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UbigeoLambayequeSeeder extends Seeder
{
    public function run(): void
    {
        // Insertar departamento: Lambayeque
        $departments = [
            ['id' => 1, 'name' => 'Lambayeque', 'code' => '14']
        ];
        DB::table('departments')->insert($departments);

        // Insertar provincias de Lambayeque
        $provinces = [
            ['id' => 1, 'name' => 'Chiclayo',     'code' => '1401', 'department_id' => 1],
            ['id' => 2, 'name' => 'Lambayeque',   'code' => '1402', 'department_id' => 1],
            ['id' => 3, 'name' => 'Ferreñafe',    'code' => '1403', 'department_id' => 1],
        ];
        DB::table('provinces')->insert($provinces);

        // Insertar distritos de Lambayeque
        $districts = [
            // Chiclayo
            ['name' => 'Chiclayo',          'code' => '140101', 'department_id' => 1, 'provinces_id' => 1],
            ['name' => 'José Leonardo Ortiz','code' => '140102', 'department_id' => 1, 'provinces_id' => 1],
            ['name' => 'La Victoria',       'code' => '140103', 'department_id' => 1, 'provinces_id' => 1],
            ['name' => 'Pimentel',          'code' => '140104', 'department_id' => 1, 'provinces_id' => 1],

            // Lambayeque
            ['name' => 'Lambayeque',        'code' => '140201', 'department_id' => 1, 'provinces_id' => 2],
            ['name' => 'Mochumí',           'code' => '140202', 'department_id' => 1, 'provinces_id' => 2],
            ['name' => 'Jayanca',           'code' => '140203', 'department_id' => 1, 'provinces_id' => 2],

            // Ferreñafe
            ['name' => 'Ferreñafe',         'code' => '140301', 'department_id' => 1, 'provinces_id' => 3],
            ['name' => 'Incahuasi',         'code' => '140302', 'department_id' => 1, 'provinces_id' => 3],
            ['name' => 'Cañaris',           'code' => '140303', 'department_id' => 1, 'provinces_id' => 3],
        ];
        DB::table('districts')->insert($districts);
    }
}
