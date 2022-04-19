<?php

namespace Database\Seeders;

use App\Models\Departamento;
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
            DepartamentoSeeder::class
        ]);

        $faker = Factory::create();

        $admin = User::create([
            'email' => 'administrador@puller.mx',
            'password' => Hash::make('12345678'),
            'nombre' => $faker->name(),
            'paterno' => $faker->lastName(),
            'materno' => $faker->lastName(),
            'celular' => $faker->phoneNumber(),
            'imagen' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $admin->assignRole('Admin');

        $jefe1 = User::create([
            'email' => 'jefe1@puller.mx',
            'password' => Hash::make('12345678'),
            'nombre' => $faker->name(),
            'paterno' => $faker->lastName(),
            'materno' => $faker->lastName(),
            'celular' => $faker->phoneNumber(),
            'imagen' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $departamento1 = Departamento::all()->random();
        $jefe1->departamento()->associate($departamento1);
        $jefe1->save();
        $jefe1->assignRole('Head of Department');

        $jefe2 = User::create([
            'email' => 'jefe2@puller.mx',
            'password' => Hash::make('12345678'),
            'nombre' => $faker->name(),
            'paterno' => $faker->lastName(),
            'materno' => $faker->lastName(),
            'celular' => $faker->phoneNumber(),
            'imagen' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $departamento2 = Departamento::all()->random();
        $jefe2->departamento()->associate($departamento2);
        $jefe2->save();
        $jefe2->assignRole('Head of Department');

        $analista = User::create([
            'email' => 'analista1@puller.mx',
            'password' => Hash::make('12345678'),
            'nombre' => $faker->name(),
            'paterno' => $faker->lastName(),
            'materno' => $faker->lastName(),
            'celular' => $faker->phoneNumber(),
            'imagen' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $analista->departamento()->associate($departamento1);
        $analista->save();
        $analista->assignRole('Analyst');

        $analista2 = User::create([
            'email' => 'analista1@puller.mx',
            'password' => Hash::make('12345678'),
            'nombre' => $faker->name(),
            'paterno' => $faker->lastName(),
            'materno' => $faker->lastName(),
            'celular' => $faker->phoneNumber(),
            'imagen' => null,
            'email_verified_at' => now(),
            'remember_token' => $faker->word(),
        ]);
        $analista2->departamento()->associate($departamento2);
        $analista2->save();
        $analista2->assignRole('Analyst');
    }
}
