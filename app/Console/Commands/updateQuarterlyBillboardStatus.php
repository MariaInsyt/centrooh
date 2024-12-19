<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Billboard;

class updateQuarterlyBillboardStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-quarterly-billboard-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update quarterly billboard status to notupdated.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        Billboard::where('update_interval', 'quarterly')->update(['status' => 'notupdated']);
    }
}
