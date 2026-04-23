<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use OwenIt\Auditing\Contracts\Auditable;

class Doctor extends Authenticatable implements Auditable
{
    use SoftDeletes, UsesUuid, \OwenIt\Auditing\Auditable;

    protected $guarded = [];
}
