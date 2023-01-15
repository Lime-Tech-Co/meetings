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
            'employee' => [
                'user_id' => $this['employee']['id'],
                'full_name' => $this->when(isset($this['employee']['full_name']), $this['employee']['full_name']),
            ],
            'availabilities' => $this->when(isset($this['availabilities']), $this['availabilities']),
        ];
    }
}
