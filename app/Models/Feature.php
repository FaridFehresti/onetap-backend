<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_feature');
    }
}
