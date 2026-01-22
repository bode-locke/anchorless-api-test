<?php

namespace App\Repositories\Interfaces;

use App\Models\File;
use Illuminate\Support\Collection;

interface FileRepositoryInterface
{
    /**
     * Create a new file record.
     *
     * @param array $data File data (user_id, filename, filepath, type, category).
     * @return File The created File model instance.
     */
    public function create(array $data): File;

    /**
     * Find a file by its ID and the owner's user ID.
     *
     * @param int $id File ID.
     * @param int $userId User ID.
     * @return File|null The found File model or null if not found.
     */
    public function findByIdAndUser(int $id, int $userId): ?File;

    /**
     * Delete a file record.
     *
     * @param File $file File model instance to delete.
     * @return void
     */
    public function delete(File $file): void;

    /**
     * Get all files for a given user grouped by category.
     *
     * @param int $userId User ID.
     * @return Collection Files grouped by category.
     */
    public function getAllByUserGroupedByCategory(int $userId): Collection;
}
