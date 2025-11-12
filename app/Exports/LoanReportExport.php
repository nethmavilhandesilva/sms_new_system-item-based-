<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LoanReportExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $loans = app(\App\Http\Controllers\CustomersLoanController::class)->prepareLoans();

        // Map the collection for Excel
        return $loans->map(function($loan) {
            return [
                'කෙටි නම' => $loan->customer_short_name,
                'සම්පූර්ණ නම' => $loan->customer_name,
                'දුරකථන අංකය' => $loan->telephone_no,
                'ණය සීමාව (Rs.)' => $loan->credit_limit,
                'මුදල' => $loan->total_amount,
            ];
        });
    }

    public function headings(): array
    {
        return ['කෙටි නම', 'සම්පූර්ණ නම', 'දුරකථන අංකය', 'ණය සීමාව (Rs.)', 'මුදල'];
    }
}

