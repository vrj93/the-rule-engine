<?php

namespace App\Services;

use App\Jobs\FileScanStatusQueue;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\SlackAlerts\Facades\SlackAlert;

class RuleEngineService
{
    public function fileUploadReqObj ($request): array
    {
        return [
            'token' => explode(' ', $request->header('Authorization'))[1],
            'repository' => $request->input('repository') ?? '',
            'commit' => $request->input('commit') ?? '',
        ];
    }

    public function fileStorage ($files): array
    {
        $filePaths = [];

        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $fileName, 'public');
            $filePaths[] = $path;
        }

        return $filePaths;
    }

    private function uploadFilesToApi ($filePaths, $reqObj): array
    {
        $ciUploadID = null;
        $response = [];
        $result = [];
        $url = env('API_FILE_UPLOAD');

        foreach ($filePaths as $filePath) {
            $reqData = $this->prepareReqData($filePath, $reqObj, $ciUploadID);

            // Upload the file to the external API
            try {
                $response = Http::withToken($reqObj['token'])->asMultipart()->post($url, $reqData);
            } catch (Exception $ex) {
                Log::error($ex->getMessage());
                SlackAlert::message("Upload File Error: {$ex->getMessage()}");
            }

            $result[] = $this->handleFileUploadResponse($filePath, $response, $ciUploadID);
        }

        return $result;
    }

    private function scanFile ($token, $ciUploadId): void
    {
        $url = env('API_FILE_SCAN');

        try {
            Http::withToken($token)
                ->post($url, ['ciUploadId' => $ciUploadId]);
        } catch (Exception $ex) {
            SlackAlert::message("Scan File Error: {$ex->getMessage()}");
        }
    }

    public function handleFileUploadProcess ($reqObj, $filePaths): array
    {
        $ciUploadId = null;
        $result = [];

        if (!empty($filePaths)) {
            $result = $this->uploadFilesToApi($filePaths, $reqObj);

            foreach ($result as $apiResponse) {
                if(isset($apiResponse['data'])) {
                    $ciUploadId = $apiResponse['data']['ciUploadId'];
                    break;
                }
            }
        }

        if ($ciUploadId) {
            $this->scanFile($reqObj['token'], $ciUploadId);

            //Queue: Email & Slack Alert
            FileScanStatusQueue::dispatch([
                'token' => $reqObj['token'],
                'ciUploadId' => $ciUploadId,
            ]);
        }

        return $result;
    }

    private function prepareReqData ($filePath, $reqObj, $ciUploadID): array
    {
        $fullPath = storage_path('app/public/' . $filePath);

        $formData = [
            [
                'name' => 'fileData',
                'contents' => file_get_contents($fullPath),
                'filename' => basename($fullPath)
            ],
            [
                'name' => 'repositoryName',
                'contents' => $reqObj['repository'],
            ],
            [
                'name' => 'commitName',
                'contents' => $reqObj['commit'],
            ]
        ];

        if ($ciUploadID) {
            $reqData = [
                ...$formData,
                [
                    'name' => 'ciUploadId',
                    'contents' => $ciUploadID
                ],
            ];
        } else {
            $reqData = [
                ...$formData
            ];
        }

        return $reqData;
    }

    private function handleFileUploadResponse ($fullPath, $response, &$ciUploadID): array
    {
        $status = $response->status();
        if ($status == 200) {
            $ciUploadID = $response['ciUploadId'];
            $result = ['file' => basename($fullPath), 'data' => $response->json()];
        } else if ($status == 400) {
            $result = ['file' => basename($fullPath), 'msg' => $response['message']];
        } else {
            $result = ['msg' => $response['message']];
        }

        return $result;
    }
}
