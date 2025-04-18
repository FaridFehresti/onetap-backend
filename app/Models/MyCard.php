<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'company',
        'company_number',
        'postal_code',
        'color',
        'avatar',
        'status',
        'user_id',
        'uuid',
    ];


    public function links()
    {
        return $this->hasMany(MyCardLink::class, 'card_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
