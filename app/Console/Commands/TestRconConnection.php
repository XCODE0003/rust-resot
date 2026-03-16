<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RconConnectionManager;
use App\Models\ServerOnline;

class TestRconConnection extends Command
{
    protected $signature = 'rcon:test {server_id?} {--proxy= : SOCKS5 proxy для теста (host:port:user:pass)}';
    protected $description = 'Проверка RCON подключения к серверу (через прокси если настроен)';

    public function handle()
    {
        $server_id = $this->argument('server_id');
        $proxyOption = $this->option('proxy');

        if ($proxyOption) {
            config(['rcon.proxy' => $proxyOption]);
            $this->info("Используется прокси: " . preg_replace('/:[^:]+$/', ':****', $proxyOption));
        } elseif (config('rcon.proxy')) {
            $this->info("Используется прокси из конфига: " . preg_replace('/:[^:]+$/', ':****', config('rcon.proxy')));
        } else {
            $this->line("Подключение без прокси");
        }
        $this->newLine();

        if ($server_id) {
            $servers = [getserver($server_id)];
        } else {
            $servers = getservers();
        }

        $manager = RconConnectionManager::getInstance();

        foreach ($servers as $server) {
            $this->info("Тест сервера {$server->id} ({$server->name})...");
            
            if ($manager->connect($server->id)) {
                $this->info("✓ Connected successfully");
                
                $result = $manager->sendCommand($server->id, 'status');
                
                if ($result && isset($result->Message)) {
                    $this->info("✓ Command sent successfully");
                    $this->line("Response preview: " . substr($result->Message, 0, 100) . "...");
                    
                    $serverOnline = ServerOnline::where('server_id', $server->id)->first();
                    if ($serverOnline) {
                        $this->info("Database status:");
                        $this->line("  Online: {$serverOnline->online_count}/{$serverOnline->online_max}");
                        $this->line("  Queued: {$serverOnline->online_queued}");
                        $this->line("  Last update: {$serverOnline->updated_at}");
                    }
                } else {
                    $this->error("✗ Failed to send command or no response");
                }
                
                $manager->disconnect($server->id);
                $this->info("✓ Disconnected");
            } else {
                $this->error("✗ Failed to connect");
            }
            
            $this->line("");
        }
        
        return 0;
    }
}
