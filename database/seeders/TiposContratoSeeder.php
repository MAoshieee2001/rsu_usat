<?php

namespace Database\Seeders;

use App\Models\ContractType;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposContratoSeeder extends Seeder
{
    public function run(): void
    {
        $contract_types = [
            ['name' => 'Nombrado'],
            ['name' => 'Permanente'],
            ['name' => 'Temporal'],
        ];

        DB::table('contract_types')->insert($contract_types);
    }
}