<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChangeAdminCredentials extends Command
{
    protected $signature = 'app:change-admin-credentials';
    protected $description = 'Change admin credentials daily at midnight';

    public function handle()
    {
        $adminNumbers = [
            '01700000000', // Super Admin
            '01700000001', // Store Manager
            '01700000002', // Staff Member
        ];

        foreach ($adminNumbers as $number) {
            $user = \App\Models\User::where('phone', $number)->first();

            if ($user) {
                $user->password = \Illuminate\Support\Facades\Hash::make(date('mdy') . '#');
                $user->save();
                $this->info("Password for {$user->name} ({$user->email}) has been reset.");
            } else {
                $this->warn("User with phone number {$number} not found.");
            }
        }
    }
}
