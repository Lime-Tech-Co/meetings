<?php

namespace Modules\V1\Meetings\Controllers\Actions;

use App\Http\Actions\Action;
use Illuminate\Http\Request;
use Modules\V1\Meetings\Models\Constants\MeetingDuration;
use Modules\V1\Meetings\Models\Constants\OfficeWorkingHours;
use Modules\V1\Users\Models\User;

class GetAvailableEmployeesTime extends Action
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function validate(): bool|array
    {
        if (($errors = parent::validate()) !== true) {
            return $errors;
        }

        return true;
    }

    public function execute()
    {
        $requestedMeetingDuration =
            $this->request->query('meeting_length') ?? MeetingDuration::MINIMUM_MEETING_DURATION->value;
        $participants = $this->request->query('participants');
        $fromDate = $this->request->query('from');
        $toDate = $this->request->query('to');
        $officeWorkingHours = $this->request->query('office_hours') ?? OfficeWorkingHours::WORKING_HOURS->value;

        $participants = $this->getParticipants();
    }

    protected function rules(): array
    {
        return [
            'meeting_length' => [
                'sometimes',
                'integer',
                'min:'.MeetingDuration::MINIMUM_MEETING_DURATION->value,
            ],
            'participants' => [
                'required',
                'exists:users,external_user_id',
            ],
            'office_hours' => [
                'sometimes',
            ],
            'from' => [
                'required',
                'date',
            ],
            'to' => [
                'required',
                'date',
                'after:from',
            ],
        ];
    }

    private function getParticipants(): \Illuminate\Database\Eloquent\Collection|array
    {
        return User::with('busyTimes')->get();
    }
}
