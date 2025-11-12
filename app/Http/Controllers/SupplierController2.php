<?php

namespace App\Http\Controllers;

use App\Models\Supplier2;
use App\Models\Supplier;
use App\Models\GrnEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SupplierController2 extends Controller
{
    protected function getSupplierBalances()
    {
        $balances = Supplier2::select('supplier_code', DB::raw('SUM(total_amount) as total_balance'))
            ->groupBy('supplier_code')
            ->pluck('total_balance', 'supplier_code');

         $existingSuppliers = Supplier::select('id', 'code', 'name', 'account_no')->get();


        $suppliersWithBalance = $existingSuppliers->map(function ($supplier) use ($balances) {
            $supplier->balance = $balances->get($supplier->code, 0);
            return $supplier;
        });

        return $suppliersWithBalance;
    }

    public function index(Request $request)
    {
        $dates = \App\Models\Setting::pluck('value')->toArray();

        $query = Supplier2::with('grn')->orderBy('id', 'desc');

        if (!$request->has('search') || empty($request->search)) {
            $query->whereIn('date', $dates);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = strtoupper($request->search);
            $query->where(DB::raw('UPPER(supplier_code)'), 'like', "%{$search}%");
        }

        $suppliers = $query->get();

        $grnOptions = GrnEntry::select('id', DB::raw("CONCAT(code,' - ',item_code,' - ',item_name) as display_name"))
            ->pluck('display_name', 'id');

        $existingSuppliersWithBalance = $this->getSupplierBalances();

        return view('dashboard.suppliers2.index', compact('suppliers', 'grnOptions', 'existingSuppliersWithBalance'));
    }

    public function getSupplierTransactions(Request $request)
    {
        $request->validate(['supplier_code' => 'required|string']);
        $supplierCode = $request->supplier_code;

        $supplier = Supplier::where('code', $supplierCode)->first();

        $transactions = Supplier2::with('grn')
            ->where('supplier_code', $supplierCode)
            ->orderBy('date', 'asc')
            ->get();

        $totalPurchases = $transactions->where('total_amount', '>', 0)->sum('total_amount');
        $totalPayments  = abs($transactions->where('total_amount', '<', 0)->sum('total_amount'));
        $remainingBalance = $totalPurchases - $totalPayments;

        $runningBalance = 0;

        $history = $transactions->map(function ($txn) use (&$runningBalance) {
            $type = $txn->total_amount >= 0 ? 'Purchase' : 'Payment';
            $runningBalance += $txn->total_amount;

            $grnNo = $txn->grn->code ?? '-';

            $slipUrl = null;
            if ($txn->bank_slip_path) {
               $filename = basename($txn->bank_slip_path);
$slipUrl = url("file/bank_slips/suppliers/" . $filename);

            }

            return [
                'date' => $txn->date,
                'type' => $type,
                'description' => $txn->description ?? '-',
                'grn_no' => $grnNo,
                'amount' => number_format(abs($txn->total_amount), 2),
                'balance' => number_format($runningBalance, 2),
                'class' => $txn->total_amount >= 0 ? 'text-success text-end' : 'text-danger text-end',
                'bank_slip_path' => $slipUrl
            ];
        });

        $purchases = Supplier2::with('grn')
            ->where('supplier_code', $supplierCode)
            ->where('total_amount', '>', 0)
            ->whereNotNull('grn_id')
            ->get()
            ->keyBy('grn_id');

        $grnSummary = [];

        foreach ($purchases as $grnId => $purchaseTxn) {
            $grn = $purchaseTxn->grn;

            $totalPaid = Supplier2::where('supplier_code', $supplierCode)
                ->where('grn_id', $grnId)
                ->where('total_amount', '<', 0)
                ->sum('total_amount');

            $lastPaymentDate = Supplier2::where('supplier_code', $supplierCode)
                ->where('grn_id', $grnId)
                ->where('total_amount', '<', 0)
                ->latest('date')
                ->value('date');

            $remaining = $purchaseTxn->total_amount - abs($totalPaid);

            $grnSummary[] = [
                'grn_code' => $grn->code,
                'grn_total' => number_format($purchaseTxn->total_amount, 2),
                'total_paid' => number_format(abs($totalPaid), 2),
                'remaining' => number_format($remaining, 2),
                'last_payment_date' => $lastPaymentDate ?? 'N/A',
            ];
        }

        return response()->json([
            'supplier_code' => $supplierCode,
            'supplier_name' => $supplier->name ?? 'Unknown',
            'supplier_email' => $supplier->email ?? null,
            'supplier_phone' => $supplier->phone ?? null,
            'supplier_address' => $supplier->address ?? null,
            'total_purchases' => number_format($totalPurchases, 2),
            'total_payments' => number_format($totalPayments, 2),
            'remaining_balance' => number_format($remainingBalance, 2),
            'history' => $history,
            'grn_payment_summary' => $grnSummary,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|string',
            'grn_id' => 'nullable|exists:grn_entries,id',
            'total_amount' => 'required|numeric',
            'transaction_id' => 'nullable|exists:supplier2s,id',
        ]);

        try {
            $currentDate = \App\Models\Setting::value('value');

            if ($request->transaction_id) {
                $txn = Supplier2::find($request->transaction_id);
                $txn->update([
                    'supplier_code' => $request->supplier_code,
                    'supplier_name' => $request->supplier_name,
                    'grn_id' => $request->grn_id,
                    'total_amount' => $request->total_amount,
                    'description' => $request->description,
                    'date' => $currentDate,
                ]);

                return back()->with('success', 'Transaction updated.');
            }

            $existing = Supplier2::where('supplier_code', $request->supplier_code)
                ->where('grn_id', $request->grn_id)
                ->where('total_amount', '>', 0)
                ->first();

            if ($existing) {
                $existing->update([
                    'total_amount' => $existing->total_amount + $request->total_amount,
                    'description' => $request->description,
                    'date' => $currentDate,
                ]);

                return back()->with('success', 'Existing GRN purchase updated.');
            }

            Supplier2::create([
                'supplier_code' => $request->supplier_code,
                'supplier_name' => $request->supplier_name,
                'grn_id' => $request->grn_id,
                'total_amount' => $request->total_amount,
                'description' => $request->description ?? ('Purchase / GRN ' . $request->grn_id),
                'date' => $currentDate,
            ]);

            return back()->with('success', 'New purchase recorded.');

        } catch (\Exception $e) {
            Log::error("Store Error", ['e' => $e->getMessage()]);
            return back()->with('error', 'Error saving transaction.');
        }
    }

    public function payment(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|string',
            'payment_amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'grn_id' => 'nullable|integer',
            'payment_method' => 'required|in:cash,cheque,bank_deposit',

            'payment_cheque_no' => 'required_if:payment_method,cheque|nullable|string',
            'payment_cheque_date' => 'required_if:payment_method,cheque|nullable|date',
            'payment_bank_name' => 'required_if:payment_method,cheque|nullable|string',

            'payment_bank_name_deposit' => 'required_if:payment_method,bank_deposit|nullable|string',
            'payment_account_no' => 'required_if:payment_method,bank_deposit|nullable|string',
            'payment_bank_slip' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $supplier = Supplier::where('code', $request->supplier_code)->first();
            $supplierName = $supplier->name ?? Supplier2::where('supplier_code', $request->supplier_code)->value('supplier_name');
            $grnId = $request->grn_id ?: null;
            $currentDate = \App\Models\Setting::value('value');

            $data = [
                'supplier_code' => $request->supplier_code,
                'supplier_name' => $supplierName,
                'grn_id' => $grnId,
                'total_amount' => -abs($request->payment_amount),
                'date' => $currentDate,
                'payment_method' => $request->payment_method,
            ];

            $description = $request->description ?? 'Payment to Supplier';

            if ($request->payment_method === 'cheque') {
                $description = sprintf(
                    '%s (Cheque No: %s, Date: %s, Bank: %s)',
                    $description,
                    $request->payment_cheque_no,
                    $request->payment_cheque_date,
                    $request->payment_bank_name
                );

                $data['cheque_no'] = $request->payment_cheque_no;
                $data['cheque_date'] = $request->payment_cheque_date;
                $data['bank_name'] = $request->payment_bank_name;

            } elseif ($request->payment_method === 'bank_deposit') {

                if ($request->hasFile('payment_bank_slip')) {
                    $path = $request->file('payment_bank_slip')->store('public/bank_slips/suppliers');
                    $data['bank_slip_path'] = $path;
                }

                $description = sprintf(
                    '%s (Bank Deposit: %s, Acc: %s)',
                    $description,
                    $request->payment_bank_name_deposit,
                    $request->payment_account_no
                );

                $data['bank_name'] = $request->payment_bank_name_deposit;
                $data['account_no'] = $request->payment_account_no;
            }

            $data['description'] = $description;

            Supplier2::create($data);

            return back()->with('success', 'Payment recorded.');

        } catch (\Exception $e) {
            Log::error("Payment Error", ['e' => $e->getMessage()]);
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function getUnpaidGrns($supplier_code)
    {
        try {
            $purchases = Supplier2::where('supplier_code', $supplier_code)
                ->where('total_amount', '>', 0)
                ->whereNotNull('grn_id')
                ->with('grn')
                ->get();

            $unpaidGrns = [];

            foreach ($purchases as $purchase) {
                $totalPaid = Supplier2::where('supplier_code', $supplier_code)
                    ->where('grn_id', $purchase->grn_id)
                    ->where('total_amount', '<', 0)
                    ->sum('total_amount');

                $balance = $purchase->total_amount + $totalPaid;

                if ($balance > 0.01) {
                    $unpaidGrns[] = [
                        'grn_id' => $purchase->grn_id,
                        'grn_code' => $purchase->grn->code ?? 'N/A',
                        'date' => $purchase->date,
                        'total_amount' => (float)$purchase->total_amount,
                        'remaining_balance' => (float)$balance,
                    ];
                }
            }

            return response()->json(['success' => true, 'grns' => $unpaidGrns]);

        } catch (\Exception $e) {
            Log::error("Get Unpaid GRN Error", ['e' => $e->getMessage()]);
            return response()->json(['success' => false], 500);
        }
    }

    public function storeManyPayment(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|string|exists:suppliers,code',
            'description' => 'nullable|string|max:255',
            'many_payment_amount' => 'required|numeric|min:0.01',
            'grn_payments' => 'required|array|min:1',
            'grn_payments.*' => 'required|numeric|min:0.01',

            'many_payment_method' => 'required|in:cash,cheque,bank_deposit',

            'many_cheque_no' => 'required_if:many_payment_method,cheque|nullable|string',
            'many_cheque_date' => 'required_if:many_payment_method,cheque|nullable|date',
            'many_bank_name' => 'required_if:many_payment_method,cheque|nullable|string',

            'many_bank_name_deposit' => 'required_if:many_payment_method,bank_deposit|nullable|string',
            'many_account_no' => 'required_if:many_payment_method,bank_deposit|nullable|string',
            'many_bank_slip' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $supplier = Supplier::where('code', $request->supplier_code)->firstOrFail();
            $currentDate = \App\Models\Setting::value('value');

            $baseDescription = $request->description ?? 'Payment to Supplier';
            $bankSlipPath = null;

            $baseData = [
                'payment_method' => $request->many_payment_method,
            ];

            if ($request->many_payment_method === 'cheque') {

                $baseDescription = sprintf(
                    '%s (Cheque No: %s, Date: %s, Bank: %s)',
                    $baseDescription,
                    $request->many_cheque_no,
                    $request->many_cheque_date,
                    $request->many_bank_name
                );

                $baseData['cheque_no'] = $request->many_cheque_no;
                $baseData['cheque_date'] = $request->many_cheque_date;
                $baseData['bank_name'] = $request->many_bank_name;

            } elseif ($request->many_payment_method === 'bank_deposit') {

                if ($request->hasFile('many_bank_slip')) {
                    $bankSlipPath = $request->file('many_bank_slip')->store('public/bank_slips/suppliers');
                }

                $baseDescription = sprintf(
                    '%s (Bank Deposit: %s, Acc: %s)',
                    $baseDescription,
                    $request->many_bank_name_deposit,
                    $request->many_account_no
                );

                $baseData['bank_name'] = $request->many_bank_name_deposit;
                $baseData['account_no'] = $request->many_account_no;
                $baseData['bank_slip_path'] = $bankSlipPath;
            }

            $totalAllocated = 0;

            foreach ($request->grn_payments as $grn_id => $payment_amount) {

                if ($payment_amount <= 0) continue;

                $totalAllocated += $payment_amount;

                $grn = GrnEntry::find($grn_id);
                $grnCode = $grn ? $grn->code : $grn_id;

                Supplier2::create(array_merge($baseData, [
                    'supplier_code' => $supplier->code,
                    'supplier_name' => $supplier->name,
                    'existing_supplier_id' => $supplier->id,
                    'grn_id' => $grn_id,
                    'date' => $currentDate,
                    'description' => $baseDescription . ' (GRN: ' . $grnCode . ')',
                    'total_amount' => -1 * abs($payment_amount),
                ]));
            }

           

            DB::commit();
            return redirect()->route('suppliers2.index')->with('success', 'Payments allocated and processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($bankSlipPath) && $bankSlipPath) Storage::delete($bankSlipPath);

            Log::error("Many Payment Error", [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return redirect()->route('suppliers2.index')
                ->with('error', 'Error processing payments: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $supplier = Supplier2::findOrFail($id);

        $grnOptions = GrnEntry::select(
            'id',
            DB::raw("CONCAT(code, ' - ', item_code, ' - ', item_name) as display_name")
        )->pluck('display_name', 'id');

        return view('dashboard.suppliers2.edit', compact('supplier', 'grnOptions'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier2::findOrFail($id);

        $request->validate([
            'supplier_code' => 'required',
            'supplier_name' => 'nullable',
            'grn_id' => 'required',
            'total_amount' => 'required|numeric',
        ]);

        $supplier->update($request->only(['supplier_code', 'supplier_name', 'grn_id', 'total_amount', 'description']));

        return redirect()->route('suppliers2.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        Supplier2::destroy($id);
        return redirect()->route('suppliers2.index')->with('success', 'Supplier deleted.');
    }
}
