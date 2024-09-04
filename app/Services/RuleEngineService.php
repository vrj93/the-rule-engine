<?php

namespace App\Services;

use App\Jobs\FileScanStatusQueue;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $url = env('API_URL').'/'.env('API_VERSION').'/open/uploads/dependencies/files';

        $ciUploadID = '';
        $result = [];

        foreach ($filePaths as $filePath) {
            $response = Http::withToken($reqObj['token']);
            // Get the full path to the file
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

            // Upload the file to the external API
            try {
                $response = $response->asMultipart()->post($url, $reqData);
            } catch (Exception $ex) {
                Log::error($ex->getMessage());
            }

            $status = $response->status();
            if ($status == 200) {
                $ciUploadID = $response['ciUploadId'];
                $result[] = ['file' => basename($fullPath), 'data' => $response->json()];
            } else if ($status == 400) {
                $result[] = ['file' => basename($fullPath), 'msg' => $response['message']];
            } else {
                $result[] = ['msg' => $response['message']];
            }
        }

        return $result;
    }

    private function scanFile ($token, $ciUploadId): void
    {
        $url = env('API_URL').'/'.env('API_VERSION').'/open/finishes/dependencies/files/uploads';

        try {
            Http::withToken($token)
                ->post($url, ['ciUploadId' => $ciUploadId]);
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
    }

    public function handleFileUploadProcess ($reqObj, $filePaths): array
    {
        $ciUploadId = null;
        $result = [];

        if (!empty($filePaths)) {
            $result = $this->uploadFilesToApi($filePaths, $reqObj);
            if(!empty($result)) $ciUploadId = $result[0]['data']['ciUploadId'];
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
}
