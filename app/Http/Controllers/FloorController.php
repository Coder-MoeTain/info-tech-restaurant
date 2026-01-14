<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index()
    {
        return Floor::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string']);
        return Floor::create($data);
    }

    public function show(Floor $floor)
    {
        return $floor->load('tables');
    }

    public function update(Request $request, Floor $floor)
    {
        $data = $request->validate(['name' => 'required|string']);
        $floor->update($data);
        return $floor;
    }

    public function destroy(Floor $floor)
    {
        $floor->delete();
        return response()->json(['message' => 'deleted']);
    }
}
