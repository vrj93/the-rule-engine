<?php

namespace App\Jobs;

use App\Services\RuleEngineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FileScanStatusQueue implements ShouldQueue
{
    use Queueable;

    public int $tries = 10;
    public int $timeout = 360;
    public array $fileScanReq;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->delay(30);
        $this->fileScanReq = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $progress = RuleEngineService::fileScanStatus($this->fileScanReq);
        if ($progress >= 0 && $progress < 100) $this->release(30);
    }

    public function failed($ex): void
    {
        Log::error($ex->getMessage());
    }
}
