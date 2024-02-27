<?php

namespace App\Jobs;

use App\Models\OneTimePassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use AfricasTalking\SDK\AfricasTalking;

class SendOneTimePassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public OneTimePassword $oneTimePassword
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Log::info('OTP' .
            $this->oneTimePassword->code
            . ' sent to ' . $this->oneTimePassword->phone_number);

        if (config('app.env') === 'local') {
            return;
        }

        $apiKey = config('africastalking.api_key');
        $username = config('africastalking.username');

        $AT = new AfricasTalking($username, $apiKey);

        $sms = $AT->sms();

        $result = $sms->send([
            'to' => $this->oneTimePassword->phone_number,
            'message' => 'Your INSYTMEDIA otp is ' . $this->oneTimePassword->code
        ]);

        Log::info($result);
    }
}
