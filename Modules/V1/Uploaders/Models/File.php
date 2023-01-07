<?php

namespace Modules\V1\Uploaders\Models;

use App\Http\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use Uuids;

    protected $fillable = [
        'filename',
        'extension',
        'mimetype',
        'path',
        'size',
    ];
}
