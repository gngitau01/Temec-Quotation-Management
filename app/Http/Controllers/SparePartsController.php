<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SparePartsImport;

class SparePartsController extends Controller
{
    public function index()
    {
        $spareParts = SparePart::all();
        return view('spare-parts.index', compact('spareParts'));
    }

    public function create()
    {
        return view('spare-parts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'part_number' => 'required|string|unique:spare_parts',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'amount_per_unit' => 'required|numeric|min:0',
        ]);

        SparePart::create($request->all());

        return redirect()->route('spare-parts.index')->with('success', 'Spare part created successfully');
    }

    public function edit(SparePart $sparePart)
    {
        return view('spare-parts.edit', compact('sparePart'));
    }

    public function update(Request $request, SparePart $sparePart)
    {
        $request->validate([
            'part_number' => 'required|string|unique:spare_parts,part_number,' . $sparePart->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'amount_per_unit' => 'required|numeric|min:0',
        ]);

        $sparePart->update($request->all());

        return redirect()->route('spare-parts.index')->with('success', 'Spare part updated successfully');
    }

    public function destroy(SparePart $sparePart)
    {
        $sparePart->delete();
        return redirect()->route('spare-parts.index')->with('success', 'Spare part deleted successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new SparePartsImport, $request->file('file'));

        return redirect()->route('spare-parts.index')->with('success', 'Spare parts imported successfully');
    }

    public function apiList()
    {
        $spareParts = SparePart::select('id', 'part_number', 'name', 'description', 'amount_per_unit', 'discount')->get();
        return response()->json([
            'status' => 'success',
            'data' => $spareParts
        ]);
    }
}
