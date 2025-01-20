<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_no', 'date', 'supplier_name', 'supplier_contact', 
        'customer_company', 'customer_name', 'customer_po', 'note'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_requisition_products')
                    ->withPivot('quantity', 'buying_price', 'selling_price')
                    ->withTimestamps();
    }
}
