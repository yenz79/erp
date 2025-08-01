<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class);
    }
}
