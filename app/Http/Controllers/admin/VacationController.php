<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vacations = Vacation::select(
            'vacations.id',
            'ce.name as contract_name',
            'te.name as type_name',
            \DB::raw('CONCAT(em.names, " ", em.lastnames) AS employee_name'),
            'em.dni as employee_dni',
            'vacations.status',
            'vacations.date_start',
            'vacations.date_end',
            'vacations.created_at',
            'vacations.updated_at'
        )
            ->join('employees as em', 'vacations.employee_id', '=', 'em.id')
            ->join('contract_types as ce', 'em.contract_id', '=', 'ce.id')
            ->join('employeetypes as te', 'em.type_id', '=', 'te.id')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($vacations)
                ->addColumn('options', function ($vacation) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $vacation->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.vacations.destroy', $vacation->id) . '" method="POST" class="d-inline frmDelete">
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
            return view('admin.vacations.index', compact('vacations'));
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
