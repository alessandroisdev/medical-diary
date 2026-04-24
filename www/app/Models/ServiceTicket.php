<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTicket extends Model
{
    use UsesUuid;

    protected $guarded = [];

    protected $casts = [
        'called_at' => 'datetime',
        'attended_at' => 'datetime',
    ];

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class);
    }
}
