<?php

namespace Modules\V1\Meetings\Models;

use App\Http\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class EmployeeBusyTime extends model
{
    use Uuids;

    protected $fillable = [
        'external_user_id',
        'busy_at',
        'external_unique_id',
    ];
}
