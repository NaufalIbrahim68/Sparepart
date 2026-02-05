<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class HashUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:hash-passwords {--force : Force hash without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash all plain text passwords in users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->error('No users found in database.');
            return 1;
        }

        $this->info('Found ' . $users->count() . ' users in database.');

        // List current users
        $this->table(
            ['ID', 'Name', 'Email', 'Current Password (Plain Text)'],
            $users->map(fn($user) => [
                $user->id,
                $user->name,
                $user->email,
                $user->password
            ])
        );

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to hash all these passwords?')) {
                $this->warn('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting password hashing...');
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            try {
                // Store plain password for reference
                $plainPassword = $user->password;

                // Check if already hashed (bcrypt hashes start with $2y$)
                if (str_starts_with($plainPassword, '$2y$')) {
                    $this->newLine();
                    $this->warn("User {$user->email} already has hashed password. Skipping.");
                    $bar->advance();
                    continue;
                }

                // Hash the password
                $user->password = Hash::make($plainPassword);
                $user->save();

                $successCount++;
                $bar->advance();
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Failed to hash password for user {$user->email}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('Password hashing completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Successfully hashed', $successCount],
                ['Errors', $errorCount],
                ['Total processed', $users->count()]
            ]
        );

        if ($successCount > 0) {
            $this->newLine();
            $this->warn('IMPORTANT: Users must now use their ORIGINAL passwords to login.');
            $this->warn('The passwords have been hashed but remain the same.');
            $this->info('Example: If password was "admin12", user still logs in with "admin12"');
        }

        return 0;
    }
}
