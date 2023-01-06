<?php

namespace Modules\V1\Users\Tests\Unit;

use Tests\TestCase;
use Modules\V1\Users\Models\User;
use Modules\V1\Users\Models\Constants\UserStatus;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_if_user_can_be_created(): void
    {
        User::create([
            'external_user_id' => '170378154979885419149243073079764064027',
            'first_name'       => 'Colin',
            'last_name'        => 'Gomez',
            'status'           => UserStatus::ENABLED->value,
        ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertTrue(true);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_if_external_user_id_is_required(): void
    {
        try {
            User::create([
                'first_name'       => 'Colin',
                'last_name'        => 'Gomez',
                'status'           => UserStatus::ENABLED->value,
            ]);
        } catch (\Exception $e) {
            $this->assertStringContainsString('Integrity constraint violation', $e->getMessage());
        }
    }
}
