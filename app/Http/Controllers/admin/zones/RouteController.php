<?php

namespace App\Http\Controllers\admin\zones;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RouteController extends Controller
{

    public function index(Request $request)
    {
        $routes = Route::all();
        if ($request->ajax()) {
            return DataTables::of($routes)
                ->addColumn('options', function ($route) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $route->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.types.destroy', $route->id) . '" method="POST" class="d-inline frmDelete">
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
            return view('admin.types.index', compact('routes'));
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
