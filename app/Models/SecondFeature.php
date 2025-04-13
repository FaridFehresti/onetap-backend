<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecondFeature extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'first_text',
        'second_text',
    ];
}
