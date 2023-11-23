<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Role::factory()->create([
            'name' => 'superadmin',
        ]);

        \App\Models\Role::factory()->create([
            'name' => 'admin',
        ]);

        \App\Models\Role::factory()->create([
            'name' => 'marketer',
        ]);

        \App\Models\User::factory()->create([
            'first_name' => 'Giorgos',
            'last_name' => 'Margaritis',
            'email' => 'georgos.margaritis@gr.ey.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$8gXIAXAq4aY93izJ8PMJVOAxQXzqUEv7UaCtHK.HWO70s1BXYwfh2',
            'remember_token' => Str::random(10),
            'role_id' => 1,
        ]);

        \App\Models\User::factory()->create([
            'first_name' => 'Kyriakos',
            'last_name' => 'Filiagkos',
            'email' => 'kyriakos.filiagkos@gr.ey.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$8gXIAXAq4aY93izJ8PMJVOAxQXzqUEv7UaCtHK.HWO70s1BXYwfh2',
            'remember_token' => Str::random(10),
            'role_id' => 1,
        ]);

        \App\Models\User::factory()->create([
            'first_name' => 'Stelios',
            'last_name' => 'Savranakis',
            'email' => 'stelios.savranakis@gr.ey.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$8gXIAXAq4aY93izJ8PMJVOAxQXzqUEv7UaCtHK.HWO70s1BXYwfh2',
            'remember_token' => Str::random(10),
            'role_id' => 1,
        ]);

        \App\Models\User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Kosmocar',
            'email' => 'admin@kosmocar.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$8gXIAXAq4aY93izJ8PMJVOAxQXzqUEv7UaCtHK.HWO70s1BXYwfh2',
            'remember_token' => Str::random(10),
            'role_id' => 2,
        ]);

        \App\Models\User::factory()->create([
            'first_name' => 'Marketer',
            'last_name' => 'Kosmocar',
            'email' => 'marketer@kosmocar.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$8gXIAXAq4aY93izJ8PMJVOAxQXzqUEv7UaCtHK.HWO70s1BXYwfh2',
            'remember_token' => Str::random(10),
            'role_id' => 3,
        ]);

        \App\Models\AuthenticationMethod::factory()->create([
            'name' => 'No Auth',
            'type' => \App\Models\AuthenticationMethod::TYPE_NOAUTH,
        ]);

        \App\Models\AuthenticationMethod::factory()->create([
            'name' => 'Headers Auth',
            'type' => \App\Models\AuthenticationMethod::TYPE_HEADER,
        ]);
    }
}
