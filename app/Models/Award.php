<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'logo',
        'action_id',
    ];

    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
