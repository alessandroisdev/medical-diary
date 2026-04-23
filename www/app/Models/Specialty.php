<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use OwenIt\Auditing\Contracts\Auditable;

class Specialty extends Model implements Auditable
{
    use UsesUuid, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class);
    }
}
