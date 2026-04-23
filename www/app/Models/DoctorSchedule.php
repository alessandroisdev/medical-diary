<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['doctor_id', 'date', 'status', 'reason'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
