<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'action_type',
        'status',
        'link',
        'primary_color',
        'secondary_color',
        'tertiary_color',
        'text_color',
        'description',
        'header_text',
        'footer_text',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'company_name',
        'postal_code',
        'position',
        'person_title',
        'contact_link',
        'maximum_participants',
        'minimum_participants',
        'duration',
        'start_time',
        'end_time',
        'price',
        'currency',
        'booking_link',
        'avatar',
        'card_id',
        'scan_count',
    ];

    protected $guarded = ['scan_count'];

    public function card()
    {
        return $this->belongsTo(MyCard::class);
    }

    public function images()
    {
        return $this->hasMany(ActionImage::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function awards()
    {
        return $this->hasMany(Award::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }
}
