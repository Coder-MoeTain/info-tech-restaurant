<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Modifier;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return Category::with('items')->get();
    }

    public function items()
    {
        return MenuItem::with('category')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string']);
        return Category::create($data);
    }

    public function show(Category $category)
    {
        return $category->load('items');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate(['name' => 'required|string']);
        $category->update($data);
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'deleted']);
    }

    // Menu items
    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'cost' => 'nullable|numeric',
            'is_available' => 'boolean',
            'variations' => 'array',
            'modifiers' => 'array',
        ]);
        return MenuItem::create($data);
    }

    public function updateItem(Request $request, MenuItem $item)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'cost' => 'nullable|numeric',
            'is_available' => 'boolean',
            'variations' => 'array',
            'modifiers' => 'array',
        ]);
        $item->update($data);
        return $item;
    }

    public function showItem(MenuItem $item)
    {
        return $item;
    }

    public function destroyItem(MenuItem $item)
    {
        $item->delete();
        return response()->json(['message' => 'deleted']);
    }

    // Modifiers
    public function modifiers()
    {
        return Modifier::all();
    }

    public function storeModifier(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'nullable|numeric',
        ]);
        return Modifier::create($data);
    }

    public function updateModifier(Request $request, Modifier $modifier)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'nullable|numeric',
        ]);
        $modifier->update($data);
        return $modifier;
    }

    public function destroyModifier(Modifier $modifier)
    {
        $modifier->delete();
        return response()->json(['message' => 'deleted']);
    }
}
