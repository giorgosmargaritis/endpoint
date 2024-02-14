<?php

namespace App\Console\Commands;

use App\Models\Connection;
use App\Models\ConnectionLog;
use Illuminate\Console\Command;
use App\Models\ConnectionLogAttempt;
use Illuminate\Support\Facades\Http;

class SendConnectionlogData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-connectionlog-data';

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
        $connections = Connection::get();
        foreach($connections as $connection)
        {
            $connectionLogs = $connection->connectionslogs->where('status', ConnectionLog::STATUS_FAIL)->orWhere('status', ConnectionLog::STATUS_PENDING);

            $headerUsername = $connection->receiver->auth_data['Username'];
            $headerPassword = $connection->receiver->auth_data['Password'];

            foreach($connectionLogs as $connectionLog)
            {
                $response = Http::withHeaders([
                    'Username' => $headerUsername,
                    'Password' => $headerPassword,
                ])->post($connection->receiver->url,
                    json_decode($connectionLog->transformed_data, true)
                );

                $connectionLogAttempt = ConnectionLogAttempt::create([
                    'connections_logs_id' => $connectionLog->id,
                    'status_code' => $response->status(),
                    'response' => $response
                ]);
                
                if(in_array($response->status(), ConnectionLogAttempt::STATUS_SUCCESS))
                {
                    $connectionLog->status = ConnectionLog::STATUS_SUCCESS;
                }
                else
                {
                    $connectionLog->status = ConnectionLog::STATUS_FAIL;
                }

                $connectionLog->saveQuietly();
            }
        }
        
    }
}
