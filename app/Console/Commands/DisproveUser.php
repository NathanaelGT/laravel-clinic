<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DisproveUser extends Command
{
    protected $signature = 'user:disprove {email}';
    protected $description = 'Disprove user account';

    public function handle()
    {
        $email = $this->argument('email');
        $success = User::whereEmail($email)->update(['is_valid' => 0]);

        if ($success) {
            $this->info('User disproved successfully.');
        } else {
            $this->error('User could not be found.');
        }

        return 0;
    }
}
