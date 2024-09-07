<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\SlackAlerts\Facades\SlackAlert;

class SlackAlertQueue implements ShouldQueue
{
    use Queueable;

    private array $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->onQueue('slack');
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (isset($this->data['msg'])) {
            SlackAlert::message($this->data['msg']);
        } elseif (isset($this->data['event'])) {
            $vulnerability = $this->data['event']['dependency'];
            $prettyJson = json_encode($this->data['event'], JSON_PRETTY_PRINT);
            $blocks = [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => "*$vulnerability*",
                    ],
                ],
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => "```$prettyJson```",
                    ],
                ],
            ];
            SlackAlert::blocks($blocks);
        }
    }
}
