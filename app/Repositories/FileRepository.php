<?php

namespace App\Repositories;

use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Class FileRepository
 *
 * Implements FileRepositoryInterface for database operations on files.
 */
class FileRepository implements FileRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $data): File
    {
        return File::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdAndUser(int $id, int $userId): ?File
    {
        return File::where('id', $id)->where('user_id', $userId)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(File $file): void
    {
        $file->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserGroupedByCategory(int $userId): Collection
    {
        return File::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('category');
    }
}
