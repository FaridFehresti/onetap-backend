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

    protected $guarded = ['total_scans'];

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

    public function getTotalScansAttribute()
    {
        $actionsScans = $this->actions()->where('card_id', $this->id)->sum('scan_count');

        $cardScans = $this->attributes['total_scans'] ?? 0;

        return (int) ($cardScans + $actionsScans);
    }
}
