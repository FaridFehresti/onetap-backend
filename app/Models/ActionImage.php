<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'action_id',
    ];

    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
