<?php

namespace App\Jobs;

use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmailSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $emailData;
    public $type;
    public function __construct(array $emailData, string $type = null)
    {
        $this->emailData = $emailData;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->type == 'template') {
            $email = new EmailHelper();
            $email->sendTemplateMail($this->emailData);
            Log::info('Email sent');
        } else {
            $email = new EmailHelper();
            $email->sendCustomMail($this->emailData);
        }
    }
}
