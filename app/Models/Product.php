<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function purchaseRequisitions()
    {
        return $this->belongsToMany(PurchaseRequisition::class, 'purchase_requisition_products')
                    ->withPivot('quantity', 'buying_price', 'selling_price')
                    ->withTimestamps();
    }
}
