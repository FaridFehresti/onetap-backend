<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'colors',
        'product_category_id',
        'template_id',
        'address_id',
    ];

    protected $casts = [
        'colors' => 'array',
    ];


    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }


    public function features()
    {
        return $this->belongsToMany(Feature::class, 'product_feature');
    }


    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }


    public function images()
    {
        return $this->hasMany(Image::class);
    }


    public function address()
    {
        return $this->belongsTo(Address::class);
    }


    public function bundleProducts()
    {
        return $this->belongsToMany(Product::class, 'product_bundle', 'product_id', 'bundle_id');
    }

    public function bundledIn()
    {
        return $this->belongsToMany(Product::class, 'product_bundle', 'bundle_id', 'product_id');
    }

}
