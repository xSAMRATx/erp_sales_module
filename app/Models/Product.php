<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $guarded = [];

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_product')
            ->withPivot('quantity', 'discount_percentage')
            ->withTimestamps();
    }

    public function saleProducts()
    {
        return $this->hasMany(SaleProduct::class);
    }
}
