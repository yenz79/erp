<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }
    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
