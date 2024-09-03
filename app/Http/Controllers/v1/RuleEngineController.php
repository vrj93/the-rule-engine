<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Jobs\FileScanStatusQueue;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RuleEngineController extends Controller
{
    public function fileUpload(Request $request): JsonResponse {
        if (null === $request->header('Authorization'))
            return response()->json(['msg' => 'Unauthenticated'], 401);

        $request->validate([
            'files.*' => ['required']
        ], [
            'files.required' => 'The files field is required.'
        ]);

        $filePaths = [];
        $result = [];
        $status = 200;

        // Loop through each file and store it
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = $file->getClientOriginalName();
                $path = $file->storeAs('uploads', $fileName, 'public');
                $filePaths[] = $path;
            }
        }

        $this->uploadFilesToApi($filePaths, $request, $result, $status);

        return response()->json($result, $status);
    }

    public function scanFile(Request $request): JsonResponse
    {
        if (null === $request->header('Authorization'))
            return response()->json(['msg' => 'Unauthenticated'], 401);

        $token = explode(' ', $request->header('Authorization'))[1];
        $url = env('API_URL').'/'.env('API_VERSION').'/open/finishes/dependencies/files/uploads';

        $request->validate([
            'ciUploadId' => ['required']
        ], [
            'ciUploadId.required' => ['The field ciUploadId is required.']
        ]);

        try {
            $response = Http::withToken($token)
                ->post($url, ['ciUploadId' => $request->input('ciUploadId')]);

            FileScanStatusQueue::dispatch([
                'token' => $token,
                'ciUploadId' => $request->input('ciUploadId')
            ]);
        } catch (Exception $ex) {
            return response()->json(['msg' => $ex->getMessage()], 500);
        }

        return response()->json([
            'msg' => 'Scanned successfully',
            'data' => $response->json()
        ], $response->status());
    }

    private function uploadFilesToApi($filePaths, $request, &$result, &$status): void
    {
        $token = explode(' ', $request->header('Authorization'))[1];
        $url = env('API_URL').'/'.env('API_VERSION').'/open/uploads/dependencies/files';

        $ciUploadID = '';
        foreach ($filePaths as $filePath) {
            $response = Http::withToken($token);
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
                    'contents' => $request->repository
                ],
                [
                    'name' => 'commitName',
                    'contents' => $request->commit
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
                response()->json(['msg' => $ex->getMessage()], 500);
                return;
            }

            $status = $response->status();
            if ($status == 200) {
                $ciUploadID = $response['ciUploadId'];
                $result[] = ['file' => basename($fullPath), 'data' => $response->json()];
            } else if ($status == 400) {
                $result[] = ['file' => basename($fullPath), 'msg' => $response['message']];
            } else {
                $result[] = ['msg' => $response['message']];
                return;
            }
        }
    }
}
