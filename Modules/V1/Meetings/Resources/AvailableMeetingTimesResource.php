<?php

namespace Modules\V1\Meetings\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableMeetingTimesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'employee' => $this['employee'],
            'availabilities' => $this->when($this['availabilities'], $this['availabilities']),
        ];
    }
}
