<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['postal_code', 'city', 'state', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
