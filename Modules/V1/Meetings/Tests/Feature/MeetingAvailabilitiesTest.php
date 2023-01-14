<?php

namespace Modules\V1\Meetings\Tests\Feature;

use Tests\TestCase;

class MeetingAvailabilitiesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfMeetingLengthCanBeLessThanDefault(): void
    {
        $response = $this->get('/api/v1/meetings/available?meeting_length=1');

        $response->assertStatus(400)->assertJsonValidationErrors('meeting_length');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfOfficeHoursCanBeIgnoredDefault(): void
    {
        $response = $this->get('/api/v1/meetings/available');
        $response->assertStatus(400);
        $response->assertSessionDoesntHaveErrors([
            'office_hours',
        ]);
    }
}
