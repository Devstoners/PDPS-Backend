<?php

namespace App\Console\Commands;

use App\Services\TaxNotificationService;
use Illuminate\Console\Command;

class SendTaxOverdueNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tax:send-overdue-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for overdue tax assessments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue tax assessments...');
        
        $notificationService = new TaxNotificationService();
        $notificationService->sendOverdueNotifications();
        
        $this->info('Overdue notifications sent successfully.');
    }
}
