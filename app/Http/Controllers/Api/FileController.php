<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadFileRequest;
use App\Services\Interfaces\FileServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    protected FileServiceInterface $fileService;

    /**
     * @param FileServiceInterface $fileService
     */
    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Upload a file and save its info.
     *
     * @param UploadFileRequest $request
     * @return JsonResponse
     */
    public function upload(UploadFileRequest $request): JsonResponse
    {
        $user = $request->user();

        $result = $this->fileService->uploadFile(
            $user->id,
            $request->file('file'),
            $request->input('category')
        );

        return response()->json([
            'message' => $result['message'],
            'file' => $result['file'] ?? null,
        ], $result['status']);
    }

    /**
     * List all files grouped by category.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $files = $this->fileService->getFilesGroupedByCategory($request->user()->id);

        return response()->json($files, Response::HTTP_OK);
    }

    /**
     * Delete a file owned by the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $result = $this->fileService->deleteFile($request->user()->id, $id);

        return response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }
}
