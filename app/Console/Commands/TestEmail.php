<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'email:test {to}';
    protected $description = 'Test sending an email';

    public function handle()
    {
        $to = $this->argument('to');
        $this->info("Sending test email to: $to");

        try {
            Mail::raw('This is a test email from your Laravel application.', function ($message) use ($to) {
                $message->to($to)
                    ->subject('Test Email');
            });

            $this->info('Email sent successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }
    }
}
