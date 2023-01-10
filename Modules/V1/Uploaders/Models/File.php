<?php

namespace Modules\V1\Uploaders\Models;

use App\Http\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
     * @param $query
     *
     * @return Builder
     */
    public function scopeActive($query): Builder
    {
        return $query->where('should_delete', false);
    }

    /**
     * @param $query
     *
     * @return Builder
     */
    public function scopeReadyToDelete($query): Builder
    {
        return $query->where('should_delete', true);
    }

    /*
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return \Storage::disk(config('filesystems.default'))->url($this->path);
    }
}
