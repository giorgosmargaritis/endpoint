<?php

namespace App\Console\Commands;

use App\Models\LogDataFacebook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as FacadesLog;

class RequestFacebookData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:request-facebook-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logsForRequestingData = LogDataFacebook::where('data_requested_status', 2)->get();
        FacadesLog::info($logsForRequestingData);
    }
}
