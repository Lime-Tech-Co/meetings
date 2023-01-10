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

    /**
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return \Storage::disk(config('filesystems.default'))->url($this->path);
    }
}
