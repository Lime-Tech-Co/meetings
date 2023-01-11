<?php

namespace Modules\V1\Users\Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\V1\Users\Models\Constants\UserStatus;
use Modules\V1\Users\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testIfUserCanBeCreated(): void
    {
        User::create([
            'external_user_id' => '170378154979885419149243073079764064027',
            'first_name' => 'Colin',
            'last_name' => 'Gomez',
            'status' => UserStatus::ENABLED->value,
        ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertTrue(true);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testIfExternalUserIdIsRequired(): void
    {
        try {
            User::create([
                'first_name' => 'Colin',
                'last_name' => 'Gomez',
                'status' => UserStatus::ENABLED->value,
            ]);
        } catch (\Exception $e) {
            $this->assertStringContainsString('Integrity constraint violation', $e->getMessage());
        }
    }
}
