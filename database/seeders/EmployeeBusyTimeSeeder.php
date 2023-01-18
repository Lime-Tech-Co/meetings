<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Modules\V1\Meetings\Models\EmployeeBusyTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeBusyTimeSeeder extends Seeder
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
            EmployeeBusyTime::truncate();

            $sampleBusyTimes = [
                [
                    'id'                 => Str::uuid()->toString(),
                    'external_user_id'   => '57646786307395936680161735716561753784',
                    'busy_at'            => '2023-02-02 08:00:00',
                    'external_unique_id' => 'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7E..',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'id'                 => Str::uuid()->toString(),
                    'external_user_id'   => '57646786307395936680161735716561753784',
                    'busy_at'            => '2023-02-02 08:30:00',
                    'external_unique_id' => 'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7C..',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'id'                 => Str::uuid()->toString(),
                    'external_user_id'   => '57646786307395936680161735716561753741',
                    'busy_at'            => '2023-02-02 08:00:00',
                    'external_unique_id' => 'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7B..',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'id'                 => Str::uuid()->toString(),
                    'external_user_id'   => '57646786307395936680161735716561753741',
                    'busy_at'            => '2023-02-02 08:00:00',
                    'external_unique_id' => 'C5CAACCED1B9F361761853A7F995A1D4F16C8BCD0A5001A2DF3EC0D7CD539A09AA7A..',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
            ];

            EmployeeBusyTime::insert($sampleBusyTimes);
        } catch (\Exception $ex) {
            \Log::info("cannot run employee busy time seeder !" . $ex->getMessage());
        }
    }
}
