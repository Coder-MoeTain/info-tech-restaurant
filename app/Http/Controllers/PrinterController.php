<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    public function index()
    {
        return Printer::all();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        return Printer::create($data);
    }

    public function update(Request $request, Printer $printer)
    {
        $data = $this->validateData($request);
        $printer->update($data);
        return $printer;
    }

    public function destroy(Printer $printer)
    {
        $printer->delete();
        return response()->json(['message' => 'deleted']);
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string',
            'connection' => 'required|in:usb,lan',
            'ip' => 'nullable|ip',
            'port' => 'nullable|integer',
            'is_kitchen' => 'boolean',
            'is_cashier' => 'boolean',
        ]);
    }
}
