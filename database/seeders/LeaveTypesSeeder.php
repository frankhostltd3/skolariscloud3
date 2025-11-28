<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveTypesSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'AL',
                'default_days' => 25,
                'requires_approval' => true,
                'description' => 'Paid leave given once a year to all staff',
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'default_days' => 10,
                'requires_approval' => true,
                'description' => 'Leave for illness or medical reasons',
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'ML',
                'default_days' => 90,
                'requires_approval' => false,
                'description' => 'Leave for new mothers after childbirth',
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PL',
                'default_days' => 5,
                'requires_approval' => false,
                'description' => 'Leave for new fathers after childbirth',
            ],
            [
                'name' => 'Compassionate Leave',
                'code' => 'CL',
                'default_days' => 5,
                'requires_approval' => false,
                'description' => 'Leave for family emergencies or bereavement',
            ],
            [
                'name' => 'Study Leave',
                'code' => 'STL',
                'default_days' => 15,
                'requires_approval' => true,
                'description' => 'Leave for further education or training',
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UL',
                'default_days' => 0,
                'requires_approval' => true,
                'description' => 'Leave taken without pay',
            ],
            [
                'name' => 'Personal Leave',
                'code' => 'PSL',
                'default_days' => 3,
                'requires_approval' => true,
                'description' => 'Leave for personal matters',
            ],
            [
                'name' => 'Public Holiday',
                'code' => 'PH',
                'default_days' => 1,
                'requires_approval' => false,
                'description' => 'Leave for official public holidays',
            ],
            [
                'name' => 'Emergency Leave',
                'code' => 'EL',
                'default_days' => 2,
                'requires_approval' => true,
                'description' => 'Leave for urgent, unforeseen circumstances',
            ],
        ];

        foreach ($leaveTypes as $type) {
            DB::table('leave_types')->updateOrInsert(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'default_days' => $type['default_days'],
                    'requires_approval' => $type['requires_approval'],
                    'description' => $type['description'],
                ]
            );
        }
    }
}
