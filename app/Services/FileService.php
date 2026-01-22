<?php

namespace App\Services;

use App\Repositories\Interfaces\FileRepositoryInterface;
use App\Services\Interfaces\FileServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FileService
 *
 * Implements business logic related to file operations.
 */
class FileService implements FileServiceInterface
{
    protected FileRepositoryInterface $repository;

    /**
     * Constructor.
     *
     * @param FileRepositoryInterface $repository
     */
    public function __construct(FileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function uploadFile(int $userId, UploadedFile $file, ?string $category = null): array
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->extension());
        $type = strtoupper($extension === 'jpeg' ? 'jpg' : $extension);

        $path = $file->store("files/{$userId}");

        $storedFile = $this->repository->create([
            'user_id' => $userId,
            'filename' => $originalName,
            'filepath' => $path,
            'type' => $type,
            'category' => $category,
        ]);

        return [
            'message' => 'File uploaded successfully.',
            'file' => $storedFile,
            'status' => Response::HTTP_CREATED,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesGroupedByCategory(int $userId): Collection
    {
        return $this->repository->getAllByUserGroupedByCategory($userId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(int $userId, int $fileId): array
    {
        $file = $this->repository->findByIdAndUser($fileId, $userId);

        if (!$file) {
            return [
                'message' => 'File not found or unauthorized.',
                'status' => Response::HTTP_NOT_FOUND,
            ];
        }

        Storage::delete($file->filepath);
        $this->repository->delete($file);

        return [
            'message' => 'File deleted successfully.',
            'status' => Response::HTTP_OK,
        ];
    }
}
