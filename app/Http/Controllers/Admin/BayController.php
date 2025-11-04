<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bay;
use App\Models\Branch;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class BayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bays = Bay::with('branch')->get();

        return Inertia::render('Admin/Bays/Index', [
            'bays' => $bays
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::select('id', 'name')->get();

        return Inertia::render('Admin/Bays/Create', [
            'branches' => $branches
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'is_active' => 'required|boolean',
        ]);

        Bay::create($validated);

        return redirect()->route('admin.bays.index')
                         ->with('message', 'Box criado com sucesso!');
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
    public function edit(Bay $bay) 
    {
        $branches = Branch::select('id', 'name')->get();

        return Inertia::render('Admin/Bays/Edit', [
            'bay' => $bay,
            'branches' => $branches
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bay $bay): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'is_active' => 'required|boolean',
        ]);

        $bay->update($validated);

        return redirect()->route('admin.bays.index')
                         ->with('message', 'Box atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bay $bay): RedirectResponse
    {
        $bay->delete();

        return redirect()->route('admin.bays.index')
                         ->with('message', 'Box excluído com sucesso!');
    }
}
