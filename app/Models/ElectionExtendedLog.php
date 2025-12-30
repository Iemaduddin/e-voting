<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ElectionExtendedLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'election_id',
        'old_end_at',
        'new_end_at',
        'reason',
        'extended_by',
    ];

    protected $casts = [
        'old_end_at' => 'datetime',
        'new_end_at' => 'datetime',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function extendedBy()
    {
        return $this->belongsTo(User::class, 'extended_by');
    }
}
