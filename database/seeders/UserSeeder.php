<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Modules\V1\Users\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        try {
            /**
             * Table will be truncated
             */
            User::truncate();

            $sampleUsers = [
                [
                    'id'               => Str::uuid()->toString(),
                    'external_user_id' => '57646786307395936680161735716561753784',
                    'full_name'        => 'farshid Boroomand',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ],
                [
                    'id'               => Str::uuid()->toString(),
                    'external_user_id' => '57646786307395936680161735716561753741',
                    'full_name'        => 'Tomas freeman',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ],
            ];

            User::insert($sampleUsers);
        } catch (\Exception $ex) {
            \Log::info("cannot run user seeder !" . $ex->getMessage());
        }
    }
}
