<?php

namespace Database\Seeders;

use App\Models\EmployeeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Conductor',
            ],
            [
                'name' => 'Asistente',
            ],
        ];

        foreach ($types as $type) {
            EmployeeType::create($type);
        }
    }
}
