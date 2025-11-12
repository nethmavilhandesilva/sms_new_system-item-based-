<?php

namespace App\Models; // <-- ADDED: Correct namespace for Laravel models

use Illuminate\Database\Eloquent\Model; // <-- ADDED: Import the base Model class

class GrnEntry extends Model
{
    protected $fillable = [
        'auto_purchase_no',
        'code',
        'supplier_code',
        'item_code',
        'item_name',
        'packs',
        'weight',
        'txn_date',
        'grn_no',
        'warehouse_no',
        'original_packs',
        'original_weight',
        'sequence_no',
        'is_hidden',
        'total_grn',
        'PerKGPrice',
        'wasted_packs',
        'wasted_weight',
        'total_wasted_weight',
        'show_status',
        'grn_status',
        'SalesKGPrice',
        'BP',
        'Real_Supplier_code',
    ];

    // Optional: If you don't want timestamps (created_at, updated_at)
    // public $timestamps = false;
}