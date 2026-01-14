<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        return Table::with('floor')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'name' => 'required|string',
            'capacity' => 'integer|min:1',
            'status' => 'in:available,occupied,reserved,cleaning',
        ]);
        return Table::create($data);
    }

    public function show(Table $table)
    {
        return $table->load('floor');
    }

    public function update(Request $request, Table $table)
    {
        $data = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'name' => 'required|string',
            'capacity' => 'integer|min:1',
            'status' => 'in:available,occupied,reserved,cleaning',
        ]);
        $table->update($data);
        return $table;
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return response()->json(['message' => 'deleted']);
    }

    // Floors
    public function floors()
    {
        return Floor::all();
    }
}
