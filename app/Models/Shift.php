<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
