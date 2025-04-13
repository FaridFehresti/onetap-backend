<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoGreen extends Model
{
    use HasFactory;

    protected $fillable = [
        'header_text',
        'header_paragraph',
        'planted_trees_number'
    ];
}
