<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_product')
            ->withPivot('quantity', 'discount_percentage')
            ->withTimestamps();
    }

    public function saleProducts()
    {
        return $this->hasMany(SaleProduct::class);
    }
}
