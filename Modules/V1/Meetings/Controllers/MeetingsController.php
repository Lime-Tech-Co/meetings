<?php

namespace Modules\V1\Meetings\Controllers;

use App\Http\Controllers\Controller;
use Modules\V1\Meetings\Controllers\Actions\GetAvailableEmployeesTime;

class MeetingsController extends Controller
{
    public function getAvailabilities(GetAvailableEmployeesTime $action): \Illuminate\Http\JsonResponse
    {
        if (($errors = $action->validate()) !== true) {
            return $this->returner->failedReturn(
                400,
                null,
                $errors,
                10000
            );
        }

        $result = $action->execute();

        if (!empty($result['error'])) {
            return $this->returner->failedReturn(
                400,
                null,
                $result['error'],
                10001
            );
        }

        return $this->returner->successfulReturn(null, $result);
    }
}