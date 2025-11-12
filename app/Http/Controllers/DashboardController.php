<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\GrnEntry;
use App\Models\Sale;
use App\Models\Item;

class DashboardController extends Controller
{
   public function index()
{
    // Customers list
    $customers = Customer::select('short_name', 'name')->get();

    // GRN entries (latest first)
   $entries = GrnEntry::where('is_hidden', 0)
    ->orderBy('txn_date', 'desc')
    ->get();

    // Fetch items for pack_due values
    $items = Item::select('no', 'type','pack_due','pack_cost')->get(); // Add this line

    // 1. Fetch all sales with basic validation (must have ID and weight)
    $allSales = Sale::whereNotNull('id')
        ->whereNotNull('weight')
        ->get();

    // 2. Split into groups

    // "New" sales: not processed and not bill_printed
    $sales = $allSales->filter(function ($sale) {
        return ($sale->processed === 'N' || is_null($sale->processed))
            && is_null($sale->bill_printed);
    })->values();

    // Printed & unprinted groups
    $printedSales   = $allSales->where('bill_printed', 'Y')->values();
    $unprintedSales = $allSales->where('bill_printed', 'N')->values();

    // Total sum for "new" sales group
    $totalSum = $sales->sum(function ($s) {
        return $s->weight * $s->price_per_kg;
    });

    // Send data to the view
    return view(
        'reactdashboard.sales.entry',
        compact(
            'customers',
            'entries',
            'sales',
            'totalSum',
            'printedSales',
            'unprintedSales',
            'items' // Add this line
        )
    );
}
 public function getAllSalesData()
    {
        // 1. Fetch all sales from the database
        // It's best practice to select only the columns the frontend needs (id, bill_printed, total, customer_code, etc.)
        $allSales = Sale::select([
            'id', 
            'bill_printed', 
            'customer_code', 
            'customer_name', 
            'supplier_code', 
            'code', 
            'item_code', 
            'item_name', 
            'weight', 
            'price_per_kg', 
            'pack_due', 
            'total', 
            'packs', 
            'grn_entry_code', 
            'original_weight', 
            'original_packs', 
            'given_amount', 
            'bill_no'
            // Add any other required fields
        ])->get();

        // 2. Separate them into the three required arrays based on 'bill_printed' status
        $response = [
            'sales' => [], // Corresponds to new/initial sales (bill_printed is NULL or not 'Y'/'N')
            'printed' => [], // Corresponds to printed sales ('Y')
            'unprinted' => [], // Corresponds to unprinted sales ('N')
        ];

        foreach ($allSales as $sale) {
            // Note: In your SalesEntry.jsx, 'newSales' is defined as: 
            // allSales.filter(s => s.id && s.bill_printed !== 'Y' && s.bill_printed !== 'N')
            
            if ($sale->bill_printed === 'Y') {
                $response['printed'][] = $sale;
            } elseif ($sale->bill_printed === 'N') {
                $response['unprinted'][] = $sale;
            } else {
                // This captures records where bill_printed is NULL or any other value
                $response['sales'][] = $sale; 
            }
        }

        // 3. Return the data structure that the frontend's fetchAllSales function expects
        return response()->json($response);
    }
}
