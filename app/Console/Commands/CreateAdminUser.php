<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'create:admin {--force : Force create even if admin exists}';
    protected $description = 'Create an admin user for the application';

    public function handle(): int
    {
        $email = 'admin@cozastore.com';

        if (!$this->option('force') && User::where('email', $email)->exists()) {
            $this->warn('Admin user already exists! Use --force to recreate.');
            return 1;
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin User',
                'password' => 'admin123',
                'phone' => '+963991234567',
                'address' => 'Admin Address',
                'gender' => 'male',
                'role' => 'admin'
            ]
        );

        $this->info('✅ Admin user created successfully!');
        $this->table(['Field', 'Value'], [
            ['Email', $admin->email],
            ['Name', $admin->name],
            ['Role', $admin->role],
            ['Phone', $admin->phone]
        ]);

        return 0;
    }
}
