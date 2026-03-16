<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServerOnline;

class MonitorRconConnections extends Command
{
    protected $signature = 'rcon:monitor';
    protected $description = 'Monitor RCON connections and server online status';

    public function handle()
    {
        $this->info('Server Online Status:');
        $this->line('');
        
        $servers = ServerOnline::all();
        
        if ($servers->isEmpty()) {
            $this->warn('No server data found. Run: php artisan server:init-online-data');
            return 1;
        }
        
        $headers = ['Server ID', 'Online', 'Max', 'Queue', 'Status', 'Last Update', 'Minutes Ago'];
        $rows = [];
        
        foreach ($servers as $server) {
            $lastUpdate = \Carbon\Carbon::parse($server->updated_at);
            $minutesAgo = $lastUpdate->diffInMinutes(now());
            $status = $minutesAgo <= 2 ? '✓ Online' : '✗ Offline';
            
            $rows[] = [
                $server->server_id,
                $server->online_count,
                $server->online_max,
                $server->online_queued,
                $status,
                $server->updated_at,
                $minutesAgo,
            ];
        }
        
        $this->table($headers, $rows);
        
        $this->line('');
        $this->info('Legend:');
        $this->line('  ✓ Online  - Data updated within last 2 minutes');
        $this->line('  ✗ Offline - Data older than 2 minutes');
        
        return 0;
    }
}
