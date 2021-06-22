<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyUser extends Command
{
    protected $signature = 'user:verify {email}';
    protected $description = 'Verify user account';

    public function handle()
    {
        $email = $this->argument('email');
        $success = User::whereEmail($email)->update(['is_valid' => 1]);

        if ($success) {
            $this->info('User verified successfully.');
        } else {
            $this->error('User could not be found.');
        }

        return 0;
    }
}
