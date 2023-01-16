<?php

namespace Modules\V1\Meetings\Controllers;

use App\Http\Controllers\Controller;
use Modules\V1\Meetings\Controllers\Actions\GetAvailableEmployeesTime;

class MeetingsController extends Controller
{
    /**
     * @api               {get} api/v1/meetings/available Get Available Meetings
     *
     * @apiVersion        1.0.0
     *
     * @apiName           GetAvailableMeetings
     *
     * @apiGroup          Meetings
     *
     * @apiPermission     None
     *
     * @apiDescription    get available meetings times.
     *
     * @apiQuery {bool}     paginated           true or false.
     * @apiQuery {int}      meeting_length      min:30, max: 120.
     * @apiQuery {string}   office_hours        example: 08-17.
     * @apiQuery {string}   participants[]      259939411636051033617118653993975778241.
     * @apiQuery {datetime} from                2/2/2023 8:00:00 AM.
     * @apiQuery {datetime} to                  2/2/2023 07:00:00 PM.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *          {
     *              "employee": {
     *                  "user_id": "259939411636051033617118653993975778241"
     *              },
     *              "availabilities": [
     *                  {
     *                      "start_at": "2023-02-02 08:00:00",
     *                      "finished_at": "2023-02-02 08:30:00"
     *                  },
     *              ]
     *          },
     *          {
     *              "employee": {
     *                  "user_id": "57646786307395936680161735716561753784"
     *              },
     *              "availabilities": [
     *                  {
     *                      "start_at": "2023-02-02 09:00:00",
     *                      "finished_at": "2023-02-02 09:30:00"
     *                  }
     *              ]
     *          }
     *     }
     *
     * @apiError         invalid credentials
     *
     * @apiErrorExample   Error-Response:
     *     HTTP/1.1 400 invalid credentials
     *     {
     *       "errors": {
     *          "meeting_length": [
     *              "The meeting length field is required."
     *          ],
     *          "participants": [
     *              "The participants field is required."
     *          ],
     *          "from": [
     *              "The from field is required."
     *          ],
     *          "to": [
     *              "The to field is required."
     *          ],
     *       },
     *       "code": 10xxx
     *     }
     */
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
