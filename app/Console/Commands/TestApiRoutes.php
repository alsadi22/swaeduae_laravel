<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class TestApiRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:test-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test that API routes are properly registered';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = Route::getRoutes();
        $apiRoutes = [];
        
        foreach ($routes as $route) {
            if (strpos($route->uri(), 'api/') === 0) {
                $apiRoutes[] = [
                    'uri' => $route->uri(),
                    'methods' => implode('|', $route->methods()),
                    'action' => $route->getActionName(),
                ];
            }
        }
        
        $this->info('API Routes Found: ' . count($apiRoutes));
        
        foreach ($apiRoutes as $route) {
            $this->line("{$route['methods']} /{$route['uri']}");
        }
        
        return 0;
    }
}