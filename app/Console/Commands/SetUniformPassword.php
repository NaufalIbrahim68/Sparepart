<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SetUniformPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:set-password {password : The new password for all users} {--force : Force change without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the same password for all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $newPassword = $this->argument('password');

        // Get all users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->error('No users found in database.');
            return 1;
        }

        $this->info('Found ' . $users->count() . ' users in database.');
        $this->newLine();

        // Show users
        $this->table(
            ['ID', 'Name', 'Email', 'Role'],
            $users->map(fn($user) => [
                $user->id,
                $user->name,
                $user->email,
                $user->roles ?? 'N/A'
            ])
        );

        $this->newLine();
        $this->warn("New password will be: {$newPassword}");
        $this->newLine();

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to set this password for ALL users?')) {
                $this->warn('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Updating passwords...');
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;
        $hashedPassword = Hash::make($newPassword);

        foreach ($users as $user) {
            try {
                $user->password = $hashedPassword;
                $user->save();
                $successCount++;
                $bar->advance();
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Failed to update password for user {$user->email}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('Password update completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Successfully updated', $successCount],
                ['Errors', $errorCount],
                ['Total processed', $users->count()]
            ]
        );

        if ($successCount > 0) {
            $this->newLine();
            $this->info("✓ All users can now login with password: {$newPassword}");
        }

        return 0;
    }
}
