<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'best_plan',
        'price',
        'price_period',
        'status',
    ];

    protected $casts = [
        'best_plan' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function planFeatures()
    {
        return $this->hasMany(PlanFeature::class, 'plan_id');
    }

}
