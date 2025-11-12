<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IncomeExpenses extends Model
{
    protected $table = 'income_expenses';

    protected $fillable = [
        'customer_id',
        'loan_id', // Added this field to link records
        'loan_type',
        'settling_way',
        'bill_no',
        'description',
        'amount',
        'cheque_no',
        'bank',
        'cheque_date',
        'customer_short_name',
        'unique_code',
        'user_id',
        'Date',
        'GRN_Code',
        'Item_Code',
        'Bill_no',
        'weight ',
        'packs',
        'Reason',
        'ip_address',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function loan()
    {
        return $this->belongsTo(CustomersLoan::class, 'loan_id');
    }
    public static function generateReport()
    {
        // Get all records with loan_type 'today'
        $todayRecords = self::where('loan_type', 'today')->get();

        $report = [];

        foreach ($todayRecords as $todayRecord) {
            $customerShortName = $todayRecord->customer_short_name;
            $todayAmount = $todayRecord->amount;

            // Get all old loan records for this customer
            $oldRecords = self::where('customer_short_name', $customerShortName)
                ->where('loan_type', 'old')
                ->get();
            $totalOldAmount = self::where('customer_short_name', $customerShortName)
                ->where('loan_type', 'old')
                ->sum('amount');
            $totaltodayAmount = self::where('customer_short_name', $customerShortName)
                ->where('loan_type', 'today')
                ->select(DB::raw('SUM(ABS(amount)) as total'))
                ->value('total');
            // Calculate amount difference (today amount minus sum of all old amounts)

            $amountDifference = $totaltodayAmount - $totalOldAmount;

            // Get Last Loan Taken (latest date for loan_type 'old')
            $lastLoanTaken = self::where('customer_short_name', $customerShortName)
                ->where('loan_type', 'old')
                ->orderBy('date', 'desc')
                ->value('date');

            // Get Last Loan Settled (latest date for loan_type 'today')
            $lastLoanSettled = self::where('customer_short_name', $customerShortName)
                ->where('loan_type', 'today')
                ->orderBy('date', 'desc')
                ->value('date');

            // Calculate Days Not Settled
            $daysNotSettled = 0;
            if ($lastLoanTaken && $lastLoanSettled) {
                $takenDate = \Carbon\Carbon::parse($lastLoanTaken);
                $settledDate = \Carbon\Carbon::parse($lastLoanSettled);
                $daysNotSettled = $takenDate->diffInDays($settledDate);
            }

            // Get customer details from Customer model
            $customerDetails = Customer::where('short_name', $customerShortName)->first();

            $report[] = [
                'customer_short_name' => $customerShortName,
                'customer_name' => $customerDetails ? $customerDetails->name : 'N/A',
                'customer_telephone' => $customerDetails ? $customerDetails->telephone_no : 'N/A',
                'amount_difference' => $amountDifference,
                'last_loan_taken' => $lastLoanTaken,
                'last_loan_settled' => $lastLoanSettled,
                'days_not_settled' => $daysNotSettled,
                'today_amount' => $todayAmount,
                'total_old_amount' => $totalOldAmount,
                'total_today_amount' => $totaltodayAmount
            ];
        }

        return $report;
    }


    /**
     * Get summary statistics
     */
    public static function getReportSummary()
    {
        $report = self::generateReport();

        $summary = [
            'total_customers' => count($report),
            'total_amount_difference' => collect($report)->sum('amount_difference'),
            'total_today_amount' => collect($report)->sum('today_amount'),
            'total_old_amount' => collect($report)->sum('total_old_amount'),
            'avg_days_not_settled' => collect($report)->avg('days_not_settled'),
        ];

        return $summary;
    }
}
