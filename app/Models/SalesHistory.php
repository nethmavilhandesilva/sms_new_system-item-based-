<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesHistory extends Model
{
    use HasFactory;

    protected $table = 'sales_histories';

    protected $fillable = [
        'customer_name',
        'customer_code',
        'supplier_code',
        'code',
        'item_code',
        'item_name',
        'weight',
        'price_per_kg',
        'total',
        'packs',
        'bill_printed',
        'Processed',
        'bill_no',
        'updated',
        'is_printed',
        'CustomerBillEnteredOn',
        'FirstTimeBillPrintedOn',
        'BillChangedOn',
        'UniqueCode',
        'created_at',
        'updated_at',
        'Date',
    ];
}
