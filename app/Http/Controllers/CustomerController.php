<?php

// app/Http/Controllers/CustomerController.php
namespace App\Http\Controllers;
use Mpdf\Mpdf;
use App\Models\Customer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use App\Exports\CustomersExport;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('dashboard.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('dashboard.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'short_name' => 'nullable',
            'ID_NO' => 'nullable',
            'name' => 'nullable',
            'telephone_no' => 'nullable',
            'credit_limit' => 'nullable',
        ]);

        // Transform short_name to uppercase before saving
        $data = $request->all();
        if (!empty($data['short_name'])) {
            $data['short_name'] = strtoupper($data['short_name']);
        }

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }


    public function edit(Customer $customer)
    {
        return view('dashboard.customers.edit', compact('customer'));
    }

   public function update(Request $request, Customer $customer)
{
    $validated = $request->validate([
        'short_name' => 'required',
        'name' => 'required',
        'telephone_no' => 'nullable',
        'ID_NO' => 'nullable',
        'credit_limit' => 'nullable|numeric',
        'address' => 'nullable',
    ]);

    // If credit limit not sent, retain old value
    $validated['credit_limit'] = $validated['credit_limit'] ?? $customer->credit_limit;

    $customer->update($validated);

    return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
}

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
     public function exportPdf()
{
    $customers = Customer::orderBy('short_name','asc')->get();

    // --- mPDF Setup for Sinhala ---
    $fontDirs = (new ConfigVariables())->getDefaults()['fontDir'];
    $fontData = (new FontVariables())->getDefaults()['fontdata'];

    $mpdf = new Mpdf([
        'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
        'fontdata' => $fontData + [
            'notosanssinhala' => [
                'R' => 'NotoSansSinhala-Regular.ttf',
                'B' => 'NotoSansSinhala-Bold.ttf',
            ],
        ],
        'default_font' => 'notosanssinhala',
        'mode' => 'utf-8',
        'format' => 'A4-P',
        'margin_top' => 15,
        'margin_bottom' => 15,
        'margin_left' => 10,
        'margin_right' => 10,
    ]);

    $html = view('dashboard.reports.customers_pdf', compact('customers'))->render();

    $mpdf->WriteHTML($html);
    $fileName = 'Customer_List_' . date('Ymd_His') . '.pdf';

    return response($mpdf->Output($fileName, 'S'), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
}
public function exportExcel()
{
    return Excel::download(new CustomersExport, 'Customer_List_'.date('Ymd_His').'.xlsx');
}
}
