<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Services\RuleEngineService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RuleEngineController extends Controller
{
    private RuleEngineService $ruleEngineService;

    /**
     * @throws Exception
     */
    public function __construct (Request $request, RuleEngineService $ruleEngineService)
    {
        if (null === $request->header('Authorization'))
            throw new Exception('Unauthenticated', 401);

        $this->ruleEngineService = $ruleEngineService;
    }

    public function fileUpload(Request $request): JsonResponse {
        $request->validate([
            'files.*' => ['required', 'file']
        ], [
            'files.required' => 'The files field is required.'
        ]);

        $files = $request->file('files');
        $ruleEngine = $this->ruleEngineService;
        $filePaths = $ruleEngine->fileStorage($files);
        $reqObj = $ruleEngine->fileUploadReqObj($request);
        $result = $ruleEngine->handleFileUploadProcess($reqObj, $filePaths);

        return response()->json($result);
    }
}
