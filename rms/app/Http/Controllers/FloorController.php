<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index()
    {
        $floors = Floor::latest()->paginate(10);
        return view('floors.index', compact('floors'));
    }

    public function create()
    {
        return view('floors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        Floor::create($data);
        return redirect()->route('floors.index')->with('status', 'Floor created');
    }

    public function edit(Floor $floor)
    {
        return view('floors.edit', compact('floor'));
    }

    public function update(Request $request, Floor $floor)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $floor->update($data);
        return redirect()->route('floors.index')->with('status', 'Floor updated');
    }

    public function destroy(Floor $floor)
    {
        $floor->delete();
        return redirect()->route('floors.index')->with('status', 'Floor deleted');
    }
}
