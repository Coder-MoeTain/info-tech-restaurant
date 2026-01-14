<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\RTable;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = RTable::with('floor')->latest()->paginate(10);
        return view('tables.index', compact('tables'));
    }

    public function create()
    {
        $floors = Floor::pluck('name', 'id');
        return view('tables.create', compact('floors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved,cleaning',
        ]);
        RTable::create($data);
        return redirect()->route('tables.index')->with('status', 'Table created');
    }

    public function edit(RTable $table)
    {
        $floors = Floor::pluck('name', 'id');
        return view('tables.edit', compact('table', 'floors'));
    }

    public function update(Request $request, RTable $table)
    {
        $data = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved,cleaning',
        ]);
        $table->update($data);
        return redirect()->route('tables.index')->with('status', 'Table updated');
    }

    public function destroy(RTable $table)
    {
        $table->delete();
        return redirect()->route('tables.index')->with('status', 'Table deleted');
    }
}
