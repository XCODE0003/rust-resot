<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\ServerOnline;

class InitServerOnlineData extends Command
{
    protected $signature = 'server:init-online-data';
    protected $description = 'Initialize server online data in database and clear old cache';

    public function handle()
    {
        $this->info('Initializing server online data...');
        
        foreach (getservers() as $server) {
            $this->info("Processing server {$server->id}...");
            
            ServerOnline::updateOrCreate(
                ['server_id' => $server->id],
                [
                    'online_count' => 0,
                    'online_max' => 0,
                    'online_queued' => 0,
                    'players_data' => '',
                    'updated_at' => now(),
                ]
            );
            
            Cache::forget('server' . $server->id . ':online_data');
            Cache::forget('server' . $server->id . ':online_count');
            Cache::forget('server' . $server->id . ':online_max');
            Cache::forget('server' . $server->id . ':online_queued');
            Cache::forget('server' . $server->id . ':status');
            
            $this->info("Server {$server->id} initialized and cache cleared");
        }
        
        $this->info('All servers initialized successfully!');
        
        return 0;
    }
}
