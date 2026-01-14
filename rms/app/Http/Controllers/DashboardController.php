<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Floor;
use App\Models\RTable;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'menuCount' => Menu::count(),
            'floorCount' => Floor::count(),
            'tableCount' => RTable::count(),
        ]);
    }
}
