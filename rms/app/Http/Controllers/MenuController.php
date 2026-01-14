<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Modifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    // Web index for menus, API returns categories
    public function index(Request $request)
    {
        if ($request->is('api/*')) {
            return Category::all();
        }
        $menus = MenuItem::latest()->paginate(10);
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        return view('menus.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);
        $data['is_available'] = $request->boolean('is_available');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }
        MenuItem::create($data);
        return redirect()->route('menus.index')->with('status', 'Menu created');
    }

    public function edit(MenuItem $menu)
    {
        return view('menus.edit', compact('menu'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);
        $data['is_available'] = $request->boolean('is_available');
        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menu', 'public');
        }
        $menu->update($data);
        return redirect()->route('menus.index')->with('status', 'Menu updated');
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('status', 'Menu deleted');
    }

    // API: menu items
    public function items()
    {
        return MenuItem::with('category')->get();
    }

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
            'image' => 'nullable|image|max:2048',
            'stock' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }
        return MenuItem::create($data);
    }

    public function showItem(MenuItem $item)
    {
        return $item;
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
            'image' => 'nullable|image|max:2048',
            'stock' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);
        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('menu', 'public');
        }
        $item->update($data);
        return $item;
    }

    public function destroyItem(MenuItem $item)
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();
        return response()->json(['message' => 'deleted']);
    }

    // API: modifiers
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

    public function updateCategoryMapping(Request $request, Category $category)
    {
        $data = $request->validate([
            'station' => 'nullable|string|max:50',
            'printer_id' => 'nullable|exists:printers,id',
        ]);
        $category->update($data);
        return $category;
    }
}
