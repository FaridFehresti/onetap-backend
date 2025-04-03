<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

   

    public function payments()
    {
        return $this->belongsTo(Payment::class,'payment_id');
    }
}
