<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Billboard;

class updateWeeklyBillboardStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-weekly-billboard-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update weekly billboard status to notupdated.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Billboard::where('update_interval', 'weekly')->update(['status' => 'notupdated']);
    }
}
