<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\GrnEntry;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
         $items = Item::query()
            ->orderBy('no', 'asc') // alphabetical ascending
            ->get();
        return view('dashboard.items.index', compact('items'));
    }

    public function create()
    {
        return view('dashboard.items.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'no'        => 'required',
        'type'      => 'required',
        'pack_cost' => 'required|numeric',
        'pack_due'  => 'required|numeric',
    ]);

    // Force 'no' to uppercase
    $data = $request->all();
    $data['no'] = strtoupper($data['no']);

    Item::create($data);

    return redirect()->route('items.index')->with('success', 'Item added successfully!');
}

    public function edit(Item $item)
    {
        return view('dashboard.items.edit', compact('item'));
    }

     public function update(Request $request, Item $item)
{
    // 1. Validate inputs
    $request->validate([
        'no' => 'required',
        'type' => 'required',
        'pack_cost' => 'required|numeric',
        'pack_due' => 'required|numeric',
    ]);

    // 2. Update the item record
    $item->update($request->all());

    // 3. Update all related GRN entries with new pack_cost value
    GrnEntry::where('item_code', $item->no)->update([
        'BP' => $item->pack_cost,
    ]);

    // 4. Redirect with success message
    return redirect()->route('items.index')->with('success', 'Item and related GRN entries updated successfully!');
}

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully!');
    }
}
