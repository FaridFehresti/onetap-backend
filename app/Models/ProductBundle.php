<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBundle extends Model
{
    use HasFactory;
    protected $table = 'product_bundle';

    protected $fillable = [
        'product_id',
        'bundle_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function bundle()
    {
        return $this->belongsTo(Product::class, 'bundle_id');
    }
}
