<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'email' => 'webmaster@puller.mx',
            'password' => Hash::make('12345678'),
            'firebase_uid' => 'sYrJNFrpSK1vKNRbODfS',
            'email_verified_at' => Carbon::now(),
        ]);
        $user->assignRole('Admin');

    }
}
