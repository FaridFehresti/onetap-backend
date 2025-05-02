<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'logo',
        'action_id',
        'percentage',
    ];


    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
