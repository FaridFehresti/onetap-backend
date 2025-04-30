<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'template_id',
        'total_scans',
        'qrcode_image',
        'user_id',
        'uuid',
    ];

    public function links()
    {
        return $this->hasMany(MyCardLink::class, 'card_id', 'id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class, 'card_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
