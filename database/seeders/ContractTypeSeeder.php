<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Nombrado',
            ],
            [
                'name' => 'Permanente',
            ],
            [
                'name' => 'Temporal',
            ],
        ];

        foreach ($types as $type) {
            ContractType::create($type);
        }
    }
}
