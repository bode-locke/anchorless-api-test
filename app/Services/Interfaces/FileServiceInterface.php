<?php

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

/**
 * Interface FileServiceInterface
 *
 * Defines business logic operations for file management.
 */
interface FileServiceInterface
{
    /**
     * Upload a file and save its metadata.
     *
     * @param int $userId User ID.
     * @param UploadedFile $file The uploaded file instance.
     * @param string|null $category Optional file category.
     * @return array Operation result containing message, file model, and HTTP status.
     */
    public function uploadFile(int $userId, UploadedFile $file, ?string $category = null): array;

    /**
     * Retrieve all files for a user grouped by category.
     *
     * @param int $userId User ID.
     * @return Collection Files grouped by category.
     */
    public function getFilesGroupedByCategory(int $userId): Collection;

    /**
     * Delete a user's file by ID.
     *
     * @param int $userId User ID.
     * @param int $fileId File ID to delete.
     * @return array Operation result containing message and HTTP status.
     */
    public function deleteFile(int $userId, int $fileId): array;
}
