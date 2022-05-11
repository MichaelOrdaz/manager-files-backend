<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DepartmentSeeder::class
        ]);

        $departaments = Department::all();

        $faker = Factory::create();

        $admin = User::create([
            'email' => 'administrador@puller.mx',
            'password' => Hash::make('12345678'),
            'name' => $faker->name(),
            'lastname' => $faker->lastName(),
            'second_lastname' => $faker->lastName(),
            'phone' => $faker->phoneNumber(),
            'image' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $admin->assignRole('Admin');

        $jefe1 = User::create([
            'email' => 'jefe1@puller.mx',
            'password' => Hash::make('12345678'),
            'name' => $faker->name(),
            'lastname' => $faker->lastName(),
            'second_lastname' => $faker->lastName(),
            'phone' => $faker->phoneNumber(),
            'image' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $departamento1 = $departaments->random();
        $jefe1->department()->associate($departamento1);
        $jefe1->save();
        $jefe1->assignRole('Head of Department');

        $jefe2 = User::create([
            'email' => 'jefe2@puller.mx',
            'password' => Hash::make('12345678'),
            'name' => $faker->name(),
            'lastname' => $faker->lastName(),
            'second_lastname' => $faker->lastName(),
            'phone' => $faker->phoneNumber(),
            'image' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $departamento2 = $departaments->where('name', '!=', $departamento1->name)->random();
        $jefe2->department()->associate($departamento2);
        $jefe2->save();
        $jefe2->assignRole('Head of Department');

        $analista = User::create([
            'email' => 'analista1@puller.mx',
            'password' => Hash::make('12345678'),
            'name' => $faker->name(),
            'lastname' => $faker->lastName(),
            'second_lastname' => $faker->lastName(),
            'phone' => $faker->phoneNumber(),
            'image' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $analista->department()->associate($departamento1);
        $analista->save();
        $analista->assignRole('Analyst');

        $analista2 = User::create([
            'email' => 'analista2@puller.mx',
            'password' => Hash::make('12345678'),
            'name' => $faker->name(),
            'lastname' => $faker->lastName(),
            'second_lastname' => $faker->lastName(),
            'phone' => $faker->phoneNumber(),
            'image' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $analista2->department()->associate($departamento2);
        $analista2->save();
        $analista2->assignRole('Analyst');
    }
}
