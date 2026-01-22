<?php

namespace Tests\Unit\Services;

use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryInterface;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    private FileService $service;

    /** @var FileRepositoryInterface&MockObject */
    private FileRepositoryInterface $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(FileRepositoryInterface::class);

        $this->service = new FileService($this->repositoryMock);

        Storage::fake();
    }

    #[Test]
    public function it_uploads_a_file(): void
    {
        $userId = 1;
        $category = 'documents';

        $uploadedFile = UploadedFile::fake()->create('example.pdf', 100);

        $expectedPath = "files/{$userId}/" . $uploadedFile->hashName();

        $this->repositoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) use ($userId, $uploadedFile, $category, $expectedPath) {
                return $data['user_id'] === $userId
                    && $data['filename'] === $uploadedFile->getClientOriginalName()
                    && $data['filepath'] === $expectedPath
                    && $data['type'] === 'PDF'
                    && $data['category'] === $category;
            }))
            ->willReturn(new File([
                'user_id' => $userId,
                'filename' => $uploadedFile->getClientOriginalName(),
                'filepath' => $expectedPath,
                'type' => 'PDF',
                'category' => $category,
            ]));

        $response = $this->service->uploadFile($userId, $uploadedFile, $category);

        Storage::assertExists($expectedPath);

        $this->assertEquals('File uploaded successfully.', $response['message']);
        $this->assertEquals(Response::HTTP_CREATED, $response['status']);
        $this->assertInstanceOf(File::class, $response['file']);
        $this->assertEquals($uploadedFile->getClientOriginalName(), $response['file']->filename);
    }

    #[Test]
    public function it_returns_files_grouped_by_category(): void
    {
        $userId = 1;

        $mockCollection = new Collection([
            'documents' => collect([new File(['category' => 'documents'])]),
            'images' => collect([new File(['category' => 'images'])]),
        ]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('getAllByUserGroupedByCategory')
            ->with($userId)
            ->willReturn($mockCollection);

        $result = $this->service->getFilesGroupedByCategory($userId);

        $this->assertSame($mockCollection, $result);
        $this->assertTrue($result->has('documents'));
        $this->assertTrue($result->has('images'));
    }

    #[Test]
    public function it_deletes_file_when_found(): void
    {
        $userId = 1;
        $fileId = 10;

        $file = new File([
            'user_id' => $userId,
            'filepath' => 'files/1/example.pdf',
        ]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('findByIdAndUser')
            ->with($fileId, $userId)
            ->willReturn($file);

        Storage::put($file->filepath, 'dummy content');

        Storage::assertExists($file->filepath);

        $this->repositoryMock
            ->expects($this->once())
            ->method('delete')
            ->with($file);

        $response = $this->service->deleteFile($userId, $fileId);

        Storage::assertMissing($file->filepath);

        $this->assertEquals('File deleted successfully.', $response['message']);
        $this->assertEquals(Response::HTTP_OK, $response['status']);
    }

    #[Test]
    public function it_returns_not_found_when_file_missing(): void
    {
        $userId = 1;
        $fileId = 999;

        $this->repositoryMock
            ->expects($this->once())
            ->method('findByIdAndUser')
            ->with($fileId, $userId)
            ->willReturn(null);

        $response = $this->service->deleteFile($userId, $fileId);

        $this->assertEquals('File not found or unauthorized.', $response['message']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response['status']);
    }
}
