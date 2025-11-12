<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_name',
        'alias',
        'identity_number',
        'address',
        'phone_number',
        'whatsapp_number',
        'email',
        'website',
        'description_1',
        'description_2',
        'description_3',
        'logo',
        'logo_dark',
        'favicon',
        'banner',
        'login_background',
        'theme',
        'theme_color',
        'boxed_layout',
        'sidebar_type',
        'card_border',
        'direction',
        'embed_youtube',
        'embed_map',
        'keyword',
        'keyword_description'
    ];

    protected $casts = [
        'boxed_layout' => 'boolean',
        'card_border' => 'boolean'
    ];

    public function socialMedias()
    {
        return $this->hasMany(SocialMedias::class, 'profile_id');
    }
}
