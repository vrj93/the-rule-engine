<?php

namespace App\Jobs;

use App\Mail\FileUploadStatus;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\SlackAlerts\Facades\SlackAlert;

class FileScanStatusQueue implements ShouldQueue
{
    use Queueable;

    public int $tries = 10;
    public int $timeout = 360;
    private array $data;
    private array $rules = [
        'vulnerabilities' => 4,
    ];

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = [];
        $url = env('API_FILE_UPLOAD_STATUS');

        try {
            $response = Http::withToken($this->data['token'])
                ->get($url, ['ciUploadId' => $this->data['ciUploadId']]);
        } catch (Exception $ex) {
            Log::alert('msg: '. $ex->getMessage());
        }

        if ($response->successful()) {
            $mailData['progress'] = $response['progress'];

            if ($response['vulnerabilitiesFound'] > $this->rules['vulnerabilities']) {
                $mailData['vulnerabilities'] = $response['vulnerabilitiesFound'];
                SlackAlert::message("Warning: {$response['vulnerabilitiesFound']} vulnerabilities found!");
            }

            $mailData['detailsUrl'] = $response['detailsUrl'] ?? '';

            Mail::to('vrj022@gmail.com', 'Vivek Joshi')
                ->send(new FileUploadStatus($mailData));

            if ($response['progress'] < 100) {
                $this->release(30);
            }
        }
    }

    public function failed(Exception $ex): void
    {
        Log::error($ex->getMessage());
    }
}
