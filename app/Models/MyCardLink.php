<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyCardLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'status',
        'card_id',
    ];

    public function card()
    {
        return $this->belongsTo(MyCard::class, 'card_id');
    }
}
