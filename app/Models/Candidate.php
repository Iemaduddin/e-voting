<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'election_id',
        'ketua_id',
        'wakil_id',
        'visi',
        'misi',
        'cv',
        'photo',
        'link',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_id');
    }

    public function wakil()
    {
        return $this->belongsTo(User::class, 'wakil_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function voteCount()
    {
        return $this->votes()->count();
    }
}
