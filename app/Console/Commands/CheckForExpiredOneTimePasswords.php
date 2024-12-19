<?php

namespace App\Console\Commands;

use App\Models\OneTimePassword;
use Illuminate\Console\Command;

class CheckForExpiredOneTimePasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-for-expired-one-time-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired one-time passwords and change status to expired.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Checking for expired one-time passwords...');
        $expiredOneTimePasswords = OneTimePassword::where('expires_at', '<', now())
            ->where('status', 'pending')
            ->get();
        $expiredOneTimePasswords->each(function ($otp) {
            $otp->update(['status' => 'expired']);
        });
        $this->info('Expired one-time passwords checked and updated.');
    }
}
