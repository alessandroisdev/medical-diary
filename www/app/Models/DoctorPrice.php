<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class DoctorPrice extends Model
{
    use UsesUuid;

    protected $guarded = [];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function healthInsurance()
    {
        return $this->belongsTo(HealthInsurance::class);
    }
}
