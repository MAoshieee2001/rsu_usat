<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmployeeContract;

class UpdateContractStatus extends Command
{
    protected $signature = 'contracts:update-status';
    protected $description = 'Actualiza el estado de los contratos vencidos a Inactivo';

    public function handle()
    {
        $updated = EmployeeContract::where('date_end', '<', now())
            ->where('status', 'Activo')
            ->update(['status' => 'Inactivo']);

        $this->info("Contratos actualizados: $updated");
    }
}