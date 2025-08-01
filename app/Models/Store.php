<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
