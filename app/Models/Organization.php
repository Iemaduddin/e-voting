<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'shorten_name',
        'vision',
        'mision',
        'description',
        'organization_type',
        'whatsapp_number',
        'logo',
        'link_media_social',
    ];

    protected $casts = [
        'mision' => 'array',
        'link_media_social' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
