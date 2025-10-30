<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Event;
use App\Models\Application;
use App\Models\Certificate;
use App\Models\Organization;

class SystemMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:monitor';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Monitor system health and display statistics';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("\n╔══════════════════════════════════════════════════════════════╗");
        $this->info("║           SwaedUAE System Health Monitor                    ║");
        $this->info("║              " . date('Y-m-d H:i:s') . "                     ║");
        $this->info("╚══════════════════════════════════════════════════════════════╝\n");

        // Database Status
        $this->info("📊 DATABASE STATUS");
        $this->info("─────────────────────────────────────────────────────────────");
        
        try {
            $tables = DB::select('SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?', ['public']);
            $tableCount = $tables[0]->count ?? 0;
            
            $dbSize = DB::select("SELECT pg_size_pretty(pg_database_size(?)) as size", [env('DB_DATABASE')])[0]->size ?? 'N/A';
            
            $connections = DB::select("SELECT count(*) as count FROM pg_stat_activity")[0]->count ?? 0;
            
            $this->line("✓ Connection Status: Connected");
            $this->line("✓ Tables: " . $tableCount);
            $this->line("✓ Database Size: " . $dbSize);
            $this->line("✓ Active Connections: " . $connections);
        } catch (\Exception $e) {
            $this->error("✗ Database Error: " . $e->getMessage());
        }

        // User Statistics
        $this->info("\n👥 USER STATISTICS");
        $this->info("─────────────────────────────────────────────────────────────");
        $this->line("✓ Total Users: " . User::count());
        $this->line("✓ Volunteers: " . User::role('volunteer')->count());
        $this->line("✓ Organizations: " . User::role('organization-manager')->count());
        $this->line("✓ Admins: " . User::role('admin')->count());
        $this->line("✓ Verified Users: " . User::whereNotNull('email_verified_at')->count());
        $this->line("✓ Unverified Users: " . User::whereNull('email_verified_at')->count());

        // Event Statistics
        $this->info("\n📅 EVENT STATISTICS");
        $this->info("─────────────────────────────────────────────────────────────");
        $this->line("✓ Total Events: " . Event::count());
        $this->line("✓ Published Events: " . Event::where('status', 'published')->count());
        $this->line("✓ Pending Approval: " . Event::where('status', 'pending')->count());
        $this->line("✓ Draft Events: " . Event::where('status', 'draft')->count());
        $this->line("✓ Active Organizations: " . Organization::where('status', 'approved')->count());

        // Application Statistics
        $this->info("\n📋 APPLICATION STATISTICS");
        $this->info("─────────────────────────────────────────────────────────────");
        $this->line("✓ Total Applications: " . Application::count());
        $this->line("✓ Approved: " . Application::where('status', 'approved')->count());
        $this->line("✓ Pending: " . Application::where('status', 'pending')->count());
        $this->line("✓ Rejected: " . Application::where('status', 'rejected')->count());

        // Certificate Statistics
        $this->info("\n📜 CERTIFICATE STATISTICS");
        $this->info("─────────────────────────────────────────────────────────────");
        $this->line("✓ Total Certificates: " . Certificate::count());
        $this->line("✓ Verified: " . Certificate::where('is_verified', true)->count());
        $this->line("✓ Public: " . Certificate::where('is_public', true)->count());
        $this->line("✓ Pending Verification: " . Certificate::where('is_verified', false)->count());

        // Queue Status
        $this->info("\n⚙️  QUEUE STATUS");
        $this->info("─────────────────────────────────────────────────────────────");
        
        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            
            $this->line("✓ Queue Driver: " . env('QUEUE_CONNECTION'));
            $this->line("✓ Pending Jobs: " . $pendingJobs);
            $this->line("✓ Failed Jobs: " . $failedJobs);
            
            if ($failedJobs > 0) {
                $this->warn("⚠ Warning: " . $failedJobs . " failed jobs detected!");
            }
        } catch (\Exception $e) {
            $this->error("✗ Queue Error: " . $e->getMessage());
        }

        // Cache Status
        $this->info("\n💾 CACHE STATUS");
        $this->info("─────────────────────────────────────────────────────────────");
        $this->line("✓ Cache Driver: " . env('CACHE_STORE'));
        $this->line("✓ Cache Prefix: " . env('CACHE_PREFIX', 'default'));
        
        try {
            // Test Redis connection
            \Illuminate\Support\Facades\Redis::ping();
            $this->line("✓ Redis Connection: OK");
            $this->line("✓ Redis Host: " . env('REDIS_HOST'));
            $this->line("✓ Redis Port: " . env('REDIS_PORT'));
        } catch (\Exception $e) {
            $this->error("✗ Redis Error: " . $e->getMessage());
        }

        // Email Configuration
        $this->info("\n📧 EMAIL CONFIGURATION");
        $this->info("─────────────────────────────────────────────────────────────");
        $this->line("✓ Mail Driver: " . env('MAIL_MAILER'));
        $this->line("✓ From Address: " . env('MAIL_FROM_ADDRESS'));
        $this->line("✓ From Name: " . env('MAIL_FROM_NAME'));
        $this->line("✓ SMTP Host: " . env('MAIL_HOST'));
        $this->line("✓ SMTP Port: " . env('MAIL_PORT'));

        // System Information
        $this->info("\n🖥️  SYSTEM INFORMATION");
        $this->info("─────────────────────────────────────────────────────────────");
        $this->line("✓ Laravel Version: " . \Illuminate\Foundation\Application::VERSION);
        $this->line("✓ PHP Version: " . phpversion());
        $this->line("✓ Environment: " . env('APP_ENV'));
        $this->line("✓ Debug Mode: " . (env('APP_DEBUG') ? 'ENABLED ⚠️ ' : 'DISABLED ✓'));
        $this->line("✓ URL: " . env('APP_URL'));

        // Recommendations
        $this->info("\n💡 RECOMMENDATIONS");
        $this->info("─────────────────────────────────────────────────────────────");
        
        if (env('APP_DEBUG')) {
            $this->warn("⚠ Debug mode is ENABLED. Disable for production!");
        }
        
        if ($failedJobs > 0) {
            $this->warn("⚠ Clear failed jobs with: php artisan queue:failed");
        }

        $this->info("\n✓ Monitor completed at " . date('Y-m-d H:i:s'));
        $this->info("═══════════════════════════════════════════════════════════════\n");

        return 0;
    }
}
