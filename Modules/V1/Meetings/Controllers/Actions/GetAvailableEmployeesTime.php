<?php

namespace Modules\V1\Meetings\Controllers\Actions;

use App\Http\Actions\Action;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Modules\V1\Meetings\Models\Constants\MeetingDuration;
use Modules\V1\Meetings\Models\Constants\OfficeWorkingHours;
use Modules\V1\Meetings\Resources\AvailableMeetingTimesResource;
use Modules\V1\Meetings\Services\MeetingGenerator;
use Modules\V1\Users\Models\User;

class GetAvailableEmployeesTime extends Action
{
    public function __construct(public MeetingGenerator $meetingGenerator, Request $request)
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
        $requestData = [
            'meeting_length' => $this->request->query('meeting_length') ??
                                MeetingDuration::MINIMUM_MEETING_DURATION->value,
            'from' => $this->dateParser($this->request->query('from')),
            'to' => $this->dateParser($this->request->query('to')),
            'office_hours' => $this->getOfficeWorkingHours(),
            'participants' => $this->getParticipantsWithTheirBusyTimes(),
        ];

        $this->meetingGeneratorSetter($requestData);

        return $this->returner(AvailableMeetingTimesResource::collection(collect($this->meetingGenerator->make())->first()));
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

    /**
     * @return void
     */
    private function meetingGeneratorSetter(array $requestData): void
    {
        $this->meetingGenerator->setMeetingLength($requestData['meeting_length']);
        $this->meetingGenerator->setRequestedDateFrom($requestData['from']);
        $this->meetingGenerator->setRequestedDateTo($requestData['to']);
        $this->meetingGenerator->setWorkingHourTimeResolution($requestData['office_hours']);
        $this->meetingGenerator->setParticipants($requestData['participants']);
    }

    /**
     * @return Collection
     */
    private function getParticipantsWithTheirBusyTimes(): Collection
    {
        return User::with('busyTimes')
                   ->getParticipants($this->request->query('participants'))
                   ->get();
    }

    private function dateParser(string $date): int
    {
        return Carbon::parse($date)->timestamp;
    }

    /**
     * @return array
     */
    private function getOfficeWorkingHours(): array
    {
        $workingHours = explode(
            '-',
            $this->request->query('office_hours') ??
            OfficeWorkingHours::WORKING_HOURS->value
        );

        return [
            'started_at' => (int) $workingHours[0],
            'finished_at' => (int) $workingHours[1],
        ];
    }
}
