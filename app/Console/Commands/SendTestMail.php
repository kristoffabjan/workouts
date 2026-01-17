<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMail extends Command
{
    protected $signature = 'mail:test {--to=test@example.com : Email recipient}';

    protected $description = 'Send a test email to verify mail configuration (caught by Mailpit)';

    public function handle(): int
    {
        $to = $this->option('to');

        $this->info("Sending test email to {$to}...");

        Mail::raw('This is a test email from the Workouts app. If you see this in Mailpit, mail is working!', function ($message) use ($to) {
            $message->to($to)
                ->subject('Test Email from Workouts App - '.now()->format('Y-m-d H:i:s'));
        });

        $this->info('Test email sent successfully!');
        $this->newLine();
        $this->info('Check Mailpit at: http://localhost:8025');

        return self::SUCCESS;
    }
}
