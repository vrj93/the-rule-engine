<?php

namespace App\Services;

use App\Models\CiUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;

class DatabaseService
{
    public function fileUpload ($fileName, Response $fileUploadResponse): void
    {
        $ciUpload = CiUpload::updateOrCreate(
            ['ciUploadId' => $fileUploadResponse['ciUploadId']],
            $fileUploadResponse->json(),
        );

        $this->fileStorage($fileName, $ciUpload);
    }

    private function fileStorage($fileName, $ciUpload): void
    {
        $ciUpload->fileUpload()->create([
            'name' => $fileName
        ]);
    }

    public function fileScanStatus ($ciUploadId, $fileScanResponse): void
    {
        $ciUpload = $this->ciUpload($ciUploadId);
        $fileScan = $this->fileScan($ciUpload, $fileScanResponse);

        if (isset($fileScanResponse['automationRules'])) {
            $rules = $fileScanResponse['automationRules'];
            foreach ($rules as $rule) {
                if ($rule['triggered']) {
                    $ruleTrigger = $this->ruleTrigger($fileScan, $rule);

                    $events = $rule['triggerEvents'];
                    foreach ($events as $event) {
                        $this->eventTrigger($ruleTrigger, $event);
                    }
                }
            }
        }
    }

    private function ciUpload($ciUploadId): Model
    {
        return CiUpload::select('id')->where('ciUploadId', $ciUploadId)->first();
    }

    private function fileScan($ciUpload, $fileScanResponse): Model
    {
        return $ciUpload->fileScan()->create([
            'progress' => $fileScanResponse['progress'],
            'vulnerabilitiesFound' => $fileScanResponse['vulnerabilitiesFound'],
            'unaffectedVulnerabilitiesFound' => $fileScanResponse['unaffectedVulnerabilitiesFound'],
            'automationsAction' => $fileScanResponse['automationsAction'],
            'policyEngineAction' => $fileScanResponse['policyEngineAction'],
        ]);
    }

    private function ruleTrigger($fileScan, $rule): Model
    {
        return $fileScan->ruleTrigger()->create([
            'ruleActions' => json_encode($rule['ruleActions']),
            'ruleLink' => $rule['ruleLink'],
        ]);
    }

    private function eventTrigger($ruleTrigger, $event): void
    {
        $ruleTrigger->eventTrigger()->create([
            'dependency' => $event['dependency'],
            'dependencyLink' => $event['dependencyLink'],
            'licenses' => json_encode($event['licenses']),
            'cve' => $event['cve'],
            'cveLink' => $event['cveLink'],
            'cvss2' => $event['cvss2'],
            'cvss3' => $event['cvss3'],
        ]);
    }
}
