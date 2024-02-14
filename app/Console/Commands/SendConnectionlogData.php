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
            $connectionLogsFail = $connection->connectionslogs->where('status', ConnectionLog::STATUS_FAIL);
            $connectionLogsPending = $connection->connectionslogs->where('status', ConnectionLog::STATUS_PENDING);

            $headerUsername = $connection->receiver->auth_data['Username'];
            $headerPassword = $connection->receiver->auth_data['Password'];

            foreach($connectionLogsFail as $connectionLogFail)
            {
                $response = Http::withHeaders([
                    'Username' => $headerUsername,
                    'Password' => $headerPassword,
                ])->post($connection->receiver->url,
                    json_decode($connectionLogFail->transformed_data, true)
                );

                $connectionLogAttempt = ConnectionLogAttempt::create([
                    'connections_logs_id' => $connectionLogFail->id,
                    'status_code' => $response->status(),
                    'response' => $response
                ]);
                
                if(in_array($response->status(), ConnectionLogAttempt::STATUS_SUCCESS))
                {
                    $connectionLogFail->status = ConnectionLog::STATUS_SUCCESS;
                }
                else
                {
                    $connectionLogFail->status = ConnectionLog::STATUS_FAIL;
                }

                $connectionLogFail->saveQuietly();
            }   
            
            foreach($connectionLogsPending as $connectionLogPending)
            {
                $response = Http::withHeaders([
                    'Username' => $headerUsername,
                    'Password' => $headerPassword,
                ])->post($connection->receiver->url,
                    json_decode($connectionLogPending->transformed_data, true)
                );

                $connectionLogAttempt = ConnectionLogAttempt::create([
                    'connections_logs_id' => $connectionLogPending->id,
                    'status_code' => $response->status(),
                    'response' => $response
                ]);
                
                if(in_array($response->status(), ConnectionLogAttempt::STATUS_SUCCESS))
                {
                    $connectionLogPending->status = ConnectionLog::STATUS_SUCCESS;
                }
                else
                {
                    $connectionLogPending->status = ConnectionLog::STATUS_FAIL;
                }

                $connectionLogPending->saveQuietly();
            }
        }
        
    }
}
