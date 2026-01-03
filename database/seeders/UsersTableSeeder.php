<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed baseline users for cost/deposit access.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $users = [
            [
                'id' => 1,
                'name' => 'Syful',
                'email' => 'syful.cse.bd@gmail.com',
                'cost_deposit' => 1,
                'password' => Hash::make('5683@'),
                'email_verified_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Bottle',
                'email' => 'aktershirin1992@gmail.com',
                'cost_deposit' => null,
                'password' => Hash::make('5683@'),
                'email_verified_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Mohibul Hasan',
                'email' => 'mohibulhasan483@gmail.com',
                'cost_deposit' => null,
                'password' => Hash::make('1234@'),
                'email_verified_at' => $now,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'remember_token' => Str::random(10),
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
