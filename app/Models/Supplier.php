<?php

// app/Models/Supplier.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'address','phone','email','account_no'];
    
    public function transactions()
    {
        return $this->hasMany(Supplier2::class, 'supplier_code', 'code');
    }
}

