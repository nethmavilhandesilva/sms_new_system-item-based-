<?php

namespace App\Http\Controllers;

use App\Models\GrnEntry;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\SalesHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GrnEntry2;
use Mpdf\Mpdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GrnEntriesExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Models\Supplier2;
use App\Models\Setting;

class GrnEntryController extends Controller
{
    public function index()
    {
        $entries = GrnEntry::latest()->get();
        return view('dashboard.grn.index', compact('entries'));
    }

    public function create()
    {
        $items = Item::all();
        $suppliers = Supplier::all();
        $entries = GrnEntry::orderBy('id', 'asc')->get();
        return view('dashboard.grn.create', compact('items', 'suppliers','entries'));
    }
 public function store(Request $request)
{
    try {
        // 1. Validate the incoming request (all fields optional)
        $request->validate([
            'item_code' => 'nullable|string',
            'supplier_name' => 'nullable|string|max:255',
            'packs' => 'nullable|integer|min:1',
            'weight' => 'nullable|numeric|min:0.01',
            'txn_date' => 'nullable|date',
            'grn_no' => 'nullable|string',
            'warehouse_no' => 'nullable|string',
            'total_grn' => 'nullable|numeric|min:0',
            'per_kg_price' => 'nullable|numeric|min:0',
            'wasted_weight' => 'nullable|numeric|min:0',
            'wasted_packs' => 'nullable|numeric|min:0',
            'Real_Supplier_code' => 'nullable|string|max:255',
        ]);

        // 2. Fetch item if provided
        $item = null;
        $itemName = null;
        $packCost = null;

        if ($request->filled('item_code')) {
            $item = Item::where('no', $request->item_code)->first();
            if (!$item) {
                return back()->withErrors(['item_code' => 'Invalid item selected.']);
            }
            $itemName = $item->type;
            $packCost = $item->pack_cost;
        }

        // 3. Find or create supplier
        $supplierName = $request->input('supplier_name', '');
        $supplier = Supplier::firstOrCreate(
            ['code' => $supplierName],
            ['name' => '']
        );
        $supplierCode = $supplier->code;

        // 4. Auto generate auto_purchase_no
        $last = GrnEntry::latest()->first();
        $autoNo = $last ? $last->id + 1 : 1;
        $autoPurchaseNo = str_pad($autoNo, 4, '0', STR_PAD_LEFT);

        // 5. Sequential number logic
        $lastGrnEntry = GrnEntry::orderBy('sequence_no', 'desc')->first();
        $nextSequentialNumber = (!$lastGrnEntry || $lastGrnEntry->sequence_no < 1125)
            ? 1126
            : $lastGrnEntry->sequence_no + 1;

        // 6. Build code string
        $itemCode = $item ? $item->no : 'ITEM';
        $code = strtoupper($itemCode . '-' . $supplierCode . '-' . $nextSequentialNumber);

        // 7. Calculate total wasted weight
        $wastedWeight = $request->input('wasted_weight', 0);
        $perKgPrice = $request->input('per_kg_price', 0);
        $totalWastedWeightValue = $wastedWeight * $perKgPrice;

        // 8. Determine grn_status
        $showstatus = 1;
        if ($itemName && (str_contains($itemName, 'අල') || str_contains($itemName, 'ලුණු'))) {
            $showstatus = 0;
        }

        // 9. Create GRN entry
        $grnEntry = GrnEntry::create([
            'auto_purchase_no' => $autoPurchaseNo,
            'code' => $code,
            'supplier_code' => strtoupper($supplierCode),
            'item_code' => $request->input('item_code', null),
            'item_name' => $itemName,
            'packs' => $request->input('packs', null),
            'weight' => $request->input('weight', null),
            'txn_date' => $request->input('txn_date', null),
            'grn_no' => $request->input('grn_no', null),
            'warehouse_no' => $request->input('warehouse_no', null),
            'original_packs' => $request->input('packs', null),
            'original_weight' => $request->input('weight', null),
            'sequence_no' => $nextSequentialNumber,
            'total_grn' => $request->input('total_grn', null),
            'PerKGPrice' => $perKgPrice,
            'wasted_packs' => $request->input('wasted_packs', 0),
            'wasted_weight' => $wastedWeight,
            'total_wasted_weight' => $totalWastedWeightValue,
            'show_status' => $showstatus,
            'BP' => $perKgPrice,
            'Real_Supplier_code' => $request->input('Real_Supplier_code', null),
        ]);

        // 10. Create/Update Supplier2 record if Real_Supplier_code is provided
        if ($request->filled('Real_Supplier_code')) {
            Supplier2::updateOrCreate(
                ['grn_id' => $grnEntry->id],
                [
                    'supplier_code' => $request->Real_Supplier_code,
                    'total_amount' => $request->total_grn,
                    'description' => 'Purchase from Supplier',
                    'date' => Setting::value('value') ?? $request->input('txn_date', date('Y-m-d'))
                ]
            );
        }

        // 11. Redirect with success
        return redirect()->route('grn.create')->with('success', 'GRN Entry added successfully.');

    } catch (\Exception $e) {
        // Log error with date
        Log::error('GRN store failed on ' . now()->format('Y-m-d H:i:s') . ': ' . $e->getMessage(), [
            'stack' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);

        return back()->with('error', 'Failed to add GRN Entry. Please check the logs.');
    }
}

 public function store2(Request $request)
    {
        Log::info('store2 method triggered. Request data:', $request->all());

        try {
            // Validate input
            $validated = $request->validate([
                'code' => 'required|exists:grn_entries,code',
                'packs' => 'nullable|numeric',
                'weight' => 'nullable|numeric',
                'per_kg_price' => 'nullable|numeric',
            ]);
            Log::info('Validation passed.');

            // Find GRN entry
            $grn = GrnEntry::where('code', $request->code)->first();
            if (!$grn) {
                Log::warning('GRN entry not found for code: ' . $request->code);
                return response()->json(['success' => false, 'message' => 'GRN entry not found.'], 404);
            }
            Log::info('Found GRN entry:', $grn->toArray());

            // Save old values
            $oldPacks = $grn->packs;
            $oldWeight = $grn->weight;
            Log::info("Old values - Packs: {$oldPacks}, Weight: {$oldWeight}");

            // ✅ Calculate remaining for NEW additions only
            $newPacks = (float) $request->packs;
            $newWeight = (float) $request->weight;

            // Get sales and damages for this code
            $totalSoldPacks = Sale::where('code', $request->code)->sum('packs') +
                SalesHistory::where('code', $request->code)->sum('packs');
            $totalSoldWeight = Sale::where('code', $request->code)->sum('weight') +
                SalesHistory::where('code', $request->code)->sum('weight');

            $totalWastedPacks = $grn->wasted_packs ?? 0;
            $totalWastedWeight = $grn->wasted_weight ?? 0;

            // Calculate remaining for new stock only
            $remainingNewPacks = $newPacks - ($totalSoldPacks + $totalWastedPacks);
            $remainingNewWeight = $newWeight - ($totalSoldWeight + $totalWastedWeight);

            Log::info("New additions calculation:");
            Log::info("New Packs: {$newPacks}, New Weight: {$newWeight}");
            Log::info("Sold Packs: {$totalSoldPacks}, Sold Weight: {$totalSoldWeight}");
            Log::info("Wasted Packs: {$totalWastedPacks}, Wasted Weight: {$totalWastedWeight}");
            Log::info("Remaining New Packs: {$remainingNewPacks}, Remaining New Weight: {$remainingNewWeight}");

            // ✅ Update GRN entry with the calculated remaining values
            $grn->packs += abs($remainingNewPacks);
            $grn->weight +=abs($remainingNewWeight);
            $grn->original_packs += abs($remainingNewPacks);
            $grn->original_weight += abs($remainingNewWeight);
            $grn->PerKGPrice = (float) $request->per_kg_price;
            $grn->SalesKGPrice = (float) $request->per_kg_price;

            if ($grn->save()) {
                Log::info('GRN entry updated successfully with calculated remaining values.', $grn->toArray());
            } else {
                Log::error('GRN entry save failed.');
                return response()->json(['success' => false, 'message' => 'Failed to update GRN entry.'], 500);
            }

            // Fetch date from setting
            $settingDate = \App\Models\Setting::value('value');
            $formattedDate = \Carbon\Carbon::parse($settingDate)->format('Y-m-d');
            Log::info("Using Setting date: {$formattedDate}");

            // Insert backup record
            try {
                $backup = GrnEntry2::create([
                    'code' => $grn->code,
                    'supplier_code' => $grn->supplier_code,
                    'item_code' => $grn->item_code,
                    'item_name' => $grn->item_name,
                    'packs' => $newPacks,
                    'weight' => $newWeight,
                    'per_kg_price' => (float) $request->per_kg_price,
                    'txn_date' => $formattedDate,
                    'grn_no' => $grn->grn_no,
                    'type' => 'added',
                ]);
                Log::info('Backup record inserted successfully.', $backup->toArray());
            } catch (QueryException $e) {
                Log::error('Failed to insert backup record into GrnEntry2: ' . $e->getMessage());
            }

            // Return JSON response
            return response()->json([
                'success' => true,
                'entry' => [
                    'id' => $backup->id ?? null,
                    'code' => $grn->code,
                    'supplier_code' => $grn->supplier_code,
                    'item_code' => $grn->item_code,
                    'item_name' => $grn->item_name,
                    'packs' => $newPacks,
                    'weight' => $newWeight,
                    'per_kg_price' => (float) $request->per_kg_price,
                    'txn_date' => $formattedDate,
                    'grn_no' => $grn->grn_no,
                    'remaining_packs_added' => max($remainingNewPacks, 0),
                    'remaining_weight_added' => max($remainingNewWeight, 0),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('store2 method failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong. Check logs.'], 500);
        }
    }


    public function edit($id)
    {
        $entry = GrnEntry::findOrFail($id);
        $items = Item::all();
        $suppliers = Supplier::all();

        return view('dashboard.grn.edit', compact('entry', 'items', 'suppliers'));
    }
    

 public function update(Request $request, $id)
{
    $request->validate([
        'item_code' => 'required',
        'item_name' => 'nullable|string',
        'supplier_code' => 'nullable',
        'packs' => 'nullable|integer',
        'weight' => 'nullable|numeric',
        'txn_date' => 'nullable|date',
        'grn_no' => 'nullable|string',
        'warehouse_no' => 'nullable|string',
        'total_grn' => 'nullable|numeric',
        'per_kg_price' => 'nullable|numeric',
        'BP' => 'nullable|numeric', // ✅ Added BP validation
        'code' => 'nullable|string|max:255',
    ]);

    $entry = GrnEntry::findOrFail($id);

    $updateData = [
        'item_code' => $request->item_code,
        'item_name' => $request->item_name,
        'supplier_code' => $request->supplier_code,
        'packs' => $request->packs,
        'weight' => $request->weight,
        'original_packs' => $request->packs,
        'original_weight' => $request->weight,
        'sequence_no' => $request->sequence_no,
        'txn_date' => $request->txn_date,
        'grn_no' => $request->grn_no,
        'warehouse_no' => $request->warehouse_no,
    ];

    // ✅ Allow editing GRN code
    if ($request->filled('code')) {
        $updateData['code'] = strtoupper($request->code);
    }

    if ($request->filled('total_grn')) {
        $updateData['total_grn'] = $request->total_grn;
    }

    if ($request->filled('per_kg_price')) {
        $updateData['PerKGPrice'] = $request->per_kg_price;
    }

    // ✅ Add BP update
    if ($request->filled('BP')) {
        $updateData['BP'] = $request->BP;
    }

    $entry->update($updateData);

    // ✅ Update related Sale rows when code changes
    if ($request->filled('code') && $entry->wasChanged('code')) {
        Sale::where('code', $entry->getOriginal('code'))
            ->update(['code' => strtoupper($request->code)]);
    }

    // ✅ Update sale price if changed
    if ($request->filled('per_kg_price')) {
        $newPerKgPrice = $request->per_kg_price;
        Sale::where('code', $entry->code)->get()->each(function ($sale) use ($newPerKgPrice) {
            $sale->PerKGPrice = $newPerKgPrice;
            $sale->PerKGTotal = $sale->weight * $newPerKgPrice;
            $sale->save();
        });
    }

    // ✅ Recalculate stock
    $this->updateGrnRemainingStock();

    return redirect()->route('grn.create')->with('success', 'Entry updated successfully.');
}




    public function destroy($id)
    {
        $entry = GrnEntry::findOrFail($id);
        $entry->delete();

        return redirect()->route('grn.create')->with('success', 'Entry deleted.');
    }
    public function getGrnEntryByCode($code)
    {
        $grnEntry = GrnEntry::where('code', $code)->first();

        if ($grnEntry) {
            return response()->json($grnEntry);
        }

        return response()->json(['error' => 'GRN Entry not found.'], 404);
    }
    public function getUsedData($code)
    {
        $usedWeight = DB::table('grn_entries')
            ->where('code', $code)
            ->sum('weight');

        $usedPacks = DB::table('grn_entries')
            ->where('code', $code)
            ->sum('packs');

        return response()->json([
            'used_weight' => $usedWeight,
            'used_packs' => $usedPacks
        ]);
    }
    public function hide($id)
    {
        $entry = GrnEntry::findOrFail($id);
        $entry->is_hidden = true;
        $entry->save();

        return response()->json(['status' => 'hidden']);
    }

    public function unhide($id)
    {
        $entry = GrnEntry::findOrFail($id);
        $entry->is_hidden = false;
        $entry->save();

        return response()->json(['status' => 'unhidden']);
    }
    public function Damagestore(Request $request)
    {
        // 1. Validate the incoming request data
        $validatedData = $request->validate([
            'wasted_code' => 'required|string',
            'wasted_packs' => 'required|numeric|min:0',
            'wasted_weight' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 2. Find the corresponding GrnEntry record using the `wasted_code`
            $grnEntry = GrnEntry::where('code', $validatedData['wasted_code'])->first();

            if (!$grnEntry) {
                DB::rollBack();
                return redirect()->back()->with('error', 'GRN entry with the provided code not found!');
            }

            // 3. Deduct the wasted packs and weight
            $grnEntry->packs -= $validatedData['wasted_packs'];
            $grnEntry->weight -= $validatedData['wasted_weight'];

            // 4. Add wasted values to the wasted_packs and wasted_weight columns
            $grnEntry->wasted_packs = ($grnEntry->wasted_packs ?? 0) + $validatedData['wasted_packs'];
            $grnEntry->wasted_weight = ($grnEntry->wasted_weight ?? 0) + $validatedData['wasted_weight'];

            // Prevent negative values
            if ($grnEntry->packs < 0 || $grnEntry->weight < 0) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Deduction would result in a negative value. Please check the amounts.');
            }

            // Save the updated GrnEntry record
            $grnEntry->save();

            DB::commit();

            return redirect()->back()->with('success', 'Wasted stock recorded and deducted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while processing the request.');
        }
    }
  public function showSalesBillSummary(Request $request)
    {
        Log::info('Sales Report generation started.');

        // 1. Fetch sales data based on your criteria
        // Add your filtering logic here if needed, e.g., for a specific date range.
        $salesData = SalesBill::with('items')->get();

        if ($salesData->isEmpty()) {
            Log::warning('No sales data found for the report.');
            return view('reports.sales_bill_summary', [
                'salesByBill' => collect(),
                'grnPrices' => collect(),
            ]);
        }

        // Group sales items by bill number
        $salesByBill = $salesData->groupBy('bill_no');

        // 2. Get all unique item codes from the sales data
        $itemCodes = $salesByBill->flatten()->pluck('code')->unique()->map(function ($code) {
            return trim($code);
        })->toArray();

        Log::debug('Unique Item Codes found in sales:', $itemCodes);

        // 3. Fetch the latest GRN price for each item code.
        $grnPrices = DB::table('grn_entries as t1')
            ->select('t1.code', 't1.PerKGPrice')
            ->join(DB::raw('(SELECT code, MAX(created_at) as max_date FROM grn_entries GROUP BY code) as t2'), function($join) {
                $join->on('t1.code', '=', 't2.code')
                     ->on('t1.created_at', '=', 't2.max_date');
            })
            ->whereIn(DB::raw('TRIM(t1.code)'), $itemCodes)
            ->pluck('PerKGPrice', 'code')
            ->toArray();

        // 4. Clean and log the fetched GRN prices
        $grnPrices = array_change_key_case(array_map('trim', $grnPrices));
        
        Log::info('GRN Prices fetched for comparison:', $grnPrices);

        // **Debugging a specific case:**
        // Let's inspect the data for the item 'ALA-NET1-1000' and its prices.
        $specificItemCode = 'ALA-NET1-1000';
        $grnPriceForDebug = $grnPrices[$specificItemCode] ?? null;
        
        Log::debug('Debugging specific item:', [
            'item_code' => $specificItemCode,
            'grn_price_from_db' => $grnPriceForDebug
        ]);
        
        // Use `dd()` to halt execution and inspect data
        // For example, if you want to see the exact GRN prices array before the view is rendered:
        // dd($grnPrices); 

        // 5. Pass the data to the view
        return view('reports.sales_bill_summary', [
            'salesByBill' => $salesByBill,
            'grnPrices' => $grnPrices,
        ]);
    }
    public function updateStatus(Request $request, $id)
{
    $entry = GrnEntry::findOrFail($id);

    if ($request->has('is_hidden')) {
        $entry->is_hidden = $request->is_hidden;
    }

    if ($request->has('show_status')) {
        $entry->show_status = $request->show_status;
    }

    $entry->save();

    return response()->json(['success' => true]);
}
public function getGrnEntry($code)
{
    $entry = GrnEntry::where('code', $code)->where('show_status', 1)->first();

    if ($entry) {
        return response()->json([
            'per_kg_price' => $entry->PerKGPrice,
        ]);
    }

    return response()->json(['per_kg_price' => null]);
}
  public function getRemaining($code)
    {
        // Find the GRN entry by code
        $grn = GrnEntry::where('code', $code)->first();

        if (!$grn) {
            return response()->json([
                'error' => 'GRN not found'
            ], 404);
        }

        // Calculate remaining packs and weight
        // Assuming your table has 'total_packs', 'sold_packs', 'total_weight', 'sold_weight' columns
        $remainingPacks = $grn->packs;
        $remainingWeight = $grn->weight;

        return response()->json([
            'packs' => $remainingPacks,
            'weight' => $remainingWeight
        ]);
    }
     public function updateGrnRemainingStock(): void
    {
        // Fetch all GRN entries and group them by their unique 'code'
        $grnEntriesByCode = GrnEntry::all()->groupBy('code');

        // Fetch all sales and sales history entries
        $currentSales = Sale::all()->groupBy('code');
        $historicalSales = SalesHistory::all()->groupBy('code');

        foreach ($grnEntriesByCode as $grnCode => $entries) {
            // Calculate the total original packs and weight for the current GRN code
            $totalOriginalPacks = $entries->sum('original_packs');
            $totalOriginalWeight = $entries->sum('original_weight');
            $totalWastedPacks = $entries->sum('wasted_packs');
            $totalWastedWeight = $entries->sum('wasted_weight');

            // Sum up packs and weight from sales for this specific GRN code
            $totalSoldPacks = 0;
            if (isset($currentSales[$grnCode])) {
                $totalSoldPacks += $currentSales[$grnCode]->sum('packs');
            }
            if (isset($historicalSales[$grnCode])) {
                $totalSoldPacks += $historicalSales[$grnCode]->sum('packs');
            }

            $totalSoldWeight = 0;
            if (isset($currentSales[$grnCode])) {
                $totalSoldWeight += $currentSales[$grnCode]->sum('weight');
            }
            if (isset($historicalSales[$grnCode])) {
                $totalSoldWeight += $historicalSales[$grnCode]->sum('weight');
            }

            // Calculate remaining stock based on all original, sold, and wasted amounts
            $remainingPacks = $totalOriginalPacks - $totalSoldPacks - $totalWastedPacks;
            $remainingWeight = $totalOriginalWeight - $totalSoldWeight - $totalWastedWeight;

            // Update each individual GRN entry with the new remaining values
            foreach ($entries as $grnEntry) {
                $grnEntry->packs = max($remainingPacks, 0);
                $grnEntry->weight = max($remainingWeight, 0);
                $grnEntry->save();
            }
        }
    }
   public function showupdateform(Request $request) {
    $notChangingGRNs = GrnEntry::all();
    $grnEntries = GrnEntry2::all(); // This is for the table display

    return view('dashboard.grn.updateform', compact('notChangingGRNs', 'grnEntries'));
}
public function getGrnDetails(Request $request) {
    $code = $request->input('code');
    $grnData = GrnEntry::where('code', $code)->first();

    if ($grnData) {
        return response()->json([
            'success' => true,
            'packs' => $grnData->packs,
            'weight' => $grnData->weight,
            'original_packs' => $grnData->original_packs,
            'original_weight' => $grnData->original_weight
        ]);
    } else {
        return response()->json(['success' => false, 'message' => 'GRN not found'], 404);
    }
}
public function exportUPDATEExcel(Request $request)
    {
        $entries = json_decode($request->entries, true);

        return Excel::download(new GrnEntriesExport($entries), 'grn_entries.xlsx');
    }

    public function exportUPDATEPdf(Request $request)
    {
        $entries = json_decode($request->entries, true);

        // Configure mPDF with Sinhala font
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $fontData = (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'];

        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
            'fontdata' => $fontData + [
                'notosanssinhala' => [
                    'R' => 'NotoSansSinhala-Regular.ttf',
                    'B' => 'NotoSansSinhala-Bold.ttf',
                ]
            ],
            'default_font' => 'notosanssinhala',
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        $html = view('dashboard.grn.pdf', compact('entries'))->render();
        $mpdf->WriteHTML($html);

        return $mpdf->Output('grn_entries.pdf', 'D');
    }
public function destroyupdate(Request $request)
{
    try {
        // Get the ID from request
        $id = $request->input('id');

        if (!$id) {
            return response()->json(['success' => false, 'message' => 'ID is required']);
        }

        // Find the specific update entry by ID
        $updateEntry = GrnEntry2::find($id);

        if (!$updateEntry) {
            return response()->json(['success' => false, 'message' => 'Update entry not found']);
        }

        // Get the code, packs, and weight from this record
        $code = $updateEntry->code;
        $packsToSubtract = $updateEntry->packs;
        $weightToSubtract = $updateEntry->weight;

        // Find the original GRN entry by code
        $originalGrn = GrnEntry::where('code', $code)->first();

        if ($originalGrn) {
            $originalGrn->packs -= $packsToSubtract;
            $originalGrn->weight -= $weightToSubtract;
            $originalGrn->original_packs -= $packsToSubtract;
            $originalGrn->original_weight -= $weightToSubtract;

            // Ensure values don't go negative
            $originalGrn->packs = max($originalGrn->packs, 0);
            $originalGrn->weight = max($originalGrn->weight, 0);
            $originalGrn->original_packs = max($originalGrn->original_packs, 0);
            $originalGrn->original_weight = max($originalGrn->original_weight, 0);

            $originalGrn->save();
        }

        // Delete the specific update entry
        $updateEntry->delete();

        return response()->json(['success' => true, 'message' => 'Entry deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error deleting entry: ' . $e->getMessage()]);
    }
}
public function getBalance($code)
{
    $totals = DB::table('grn_entries')
        ->where('code', $code)
        ->selectRaw('SUM(packs) as total_packs, SUM(weight) as total_weight')
        ->first();

    return response()->json([
        'total_packs' => $totals->total_packs ?? 0,
        'total_weight' => $totals->total_weight ?? 0,
    ]);
}
public function getLatestEntries(Request $request)
    {
        try {
            // Get all GRN entries with latest data, ordered by date
            $entries = GrnEntry::where('is_hidden', 0)
            ->orderBy('txn_date', 'desc')
                ->get()
                ->map(function ($entry) {
                    return [
                        'code' => $entry->code,
                        'item_name' => $entry->item_name,
                        'supplier_code' => $entry->supplier_code,
                        'item_code' => $entry->item_code,
                        'price_per_kg' => $entry->price_per_kg,
                        'PerKGPrice' => $entry->PerKGPrice,
                        'SalesKGPrice' => $entry->SalesKGPrice,
                        'weight' => $entry->weight, // Real-time weight
                        'packs' => $entry->packs,   // Real-time packs
                        'original_weight' => $entry->original_weight,
                        'original_packs' => $entry->original_packs,
                    ];
                });

            return response()->json([
                'success' => true,
                'entries' => $entries
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch GRN entries: ' . $e->getMessage()
            ], 500);
        }
    }
     public function showGrnReport(Request $request)
{
    $query = GrnEntry::where('is_hidden', 0);

    // Apply filters dynamically
    if ($request->filled('supplier_code')) {
        $query->where('supplier_code', $request->supplier_code);
    }

    if ($request->filled('item_code')) {
        $query->where('item_code', $request->item_code);
    }

    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('txn_date', [$request->start_date, $request->end_date]);
    }

    // Filter by GRN code (from modal or URL param)
    if ($request->filled('code')) {
        $query->where('code', $request->code);
    }

    // Fetch GrnEntry2 data for the existing sub-table (if you still want it)
    $grnEntry2Data = collect();
    if ($request->filled('code')) {
        $grnEntry2Data = GrnEntry2::where('code', $request->code)
            ->get()
            ->groupBy('code');
    }

    $grnEntries = $query->orderBy('txn_date', 'desc')->get();

    // For filters
    $supplierCodes = GrnEntry::whereIn('supplier_code', ['L', 'A'])
        ->select('supplier_code')
        ->distinct()
        ->pluck('supplier_code');

    $itemCodes = GrnEntry::select('item_code', 'item_name')
        ->distinct()
        ->get();

    // For modal autocomplete (kept for context)
    $allCodes = GrnEntry::select('code', 'item_code', 'item_name', 'txn_date')
        ->distinct()
        ->get();

    return view('dashboard.reports.grn_report', compact(
        'grnEntries',
        'grnEntry2Data', // Still passed for the original sub-table logic
        'allCodes',
        'supplierCodes',
        'itemCodes'
    ));
}

// ✅ NEW METHOD TO FETCH MODAL DETAILS VIA AJAX
public function fetchGrnDetails(Request $request)
{
    // Validate the required 'code' parameter
    $request->validate(['code' => 'required|string']);

    $code = $request->input('code');

    // 1. Fetch related GrnEntry2 data
    $grnEntry2Data = GrnEntry2::where('code', $code)
        ->select('item_code', 'item_name', 'packs', 'weight', 'per_kg_price', 'type')
        ->get();

    // 2. Fetch related Sale data
    $saleData = Sale::where('code', $code)
        ->select(
            'Date', // Ensure case matches model property
            'customer_code',
            'item_code',
            'item_name',
            'weight',
            'price_per_kg',
            'total',
            'packs'
        )
        ->get();

    // Return the data as a JSON response
    return response()->json([
        'grnEntry2' => $grnEntry2Data,
        'sales' => $saleData,
    ]);
}
public function markAsRead($id)
{
    $entry = GrnEntry::find($id);

    if ($entry) {
        $entry->is_read = 1;
        $entry->save();
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Record not found'], 404);
}



}

