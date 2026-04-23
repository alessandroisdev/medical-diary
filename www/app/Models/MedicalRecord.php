<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use OwenIt\Auditing\Contracts\Auditable;

class MedicalRecord extends Model implements Auditable
{
    use SoftDeletes, UsesUuid, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
