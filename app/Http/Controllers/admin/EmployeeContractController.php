<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeContract;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EmployeeContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $contracts = EmployeeContract::select(
            'employee_contracts.id',
            DB::raw('CONCAT(emp.names, " ", emp.lastnames) as full_name'),
            'code',
            'b.name as brand_name',
            'brandmodels.description',
            'brandmodels.created_at',
            'brandmodels.updated_at'
        )
            ->join('employees as emp', 'employee_contracts.employee_id', '=', 'emp.id')
            ->join('contract_types as ctp', 'employee_contracts.contract_id', '=', 'contract_.id')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($contracts)
                ->addColumn('options', function ($contract) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $contract->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.contracts.destroy', $contract->id) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['options'])
                ->make(true);
        } else {
            return view('admin.contracts.index', compact('contracts'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
