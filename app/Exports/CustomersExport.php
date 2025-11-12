<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Customer::orderBy('short_name','asc')->get([
            'short_name',
            'name',
            'ID_NO',
            'address',
            'telephone_no',
            'credit_limit'
        ]);
    }

    public function headings(): array
    {
        return [
            'කෙටි නම',
            'සම්පූර්ණ නම',
            'ID_NO',
            'ලිපිනය',
            'දුරකථන අංකය',
            'ණය සීමාව (Rs.)'
        ];
    }
}
