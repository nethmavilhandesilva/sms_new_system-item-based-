<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('dashboard.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('dashboard.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|unique:suppliers',
            'name'        => 'required',
            'address'     => 'required',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:100',
            'account_no'  => 'nullable|string|max:100', // <-- ADDED
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($data['code']); // force uppercase

        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully!');
    }

    public function edit(Supplier $supplier)
    {
        return view('dashboard.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'code'        => 'required|unique:suppliers,code,' . $supplier->id,
            'name'        => 'required',
            'address'     => 'required',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:100',
            'account_no'  => 'nullable|string|max:100', // <-- ADDED
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($data['code']);

        $supplier->update($data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully!');
    }
}