<?php

namespace App\Console\Commands;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    protected $signature = 'user:create
                            {name : The name of the user}
                            {email : The email address}
                            {password : The password}
                            {--admin : Make the user a system admin}';

    protected $description = 'Create a new user with optional admin privileges';

    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $isAdmin = $this->option('admin');

        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists.");

            return self::FAILURE;
        }

        try {
            DB::transaction(function () use ($name, $email, $password, $isAdmin) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'is_admin' => $isAdmin,
                    'email_verified_at' => now(),
                ]);

                // Create personal team for the user
                $personalTeam = Team::create([
                    'name' => "{$name}'s Personal",
                    'slug' => str()->slug($name).'-personal-'.str()->random(4),
                    'is_personal' => true,
                    'owner_id' => $user->id,
                ]);

                $user->teams()->attach($personalTeam->id, ['role' => TeamRole::Coach]);
            });
        } catch (\Throwable $e) {
            $this->error("Failed to create user: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->info("User created successfully: {$email}");
        if ($isAdmin) {
            $this->info('User has system admin privileges.');
        }

        return self::SUCCESS;
    }
}
