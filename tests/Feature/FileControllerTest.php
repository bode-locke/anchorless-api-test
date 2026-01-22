<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Interfaces\FileServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        Storage::fake('local');
    }

    #[Test]
    public function test_upload_calls_service_and_returns_json()
    {
        Storage::fake();

        $mockFile = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $mockService = Mockery::mock(FileServiceInterface::class);
        $mockService->shouldReceive('uploadFile')
                    ->once()
                    ->andReturn([
                        'status' => 201,
                        'message' => 'Uploaded.',
                        'file' => ['id' => 1, 'filename' => 'test.pdf']
                    ]);

        $this->instance(FileServiceInterface::class, $mockService);

        $response = $this->postJson('/api/files/upload', [
            'file' => $mockFile,
            'category' => 'identity',
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Uploaded.',
                    'file' => ['filename' => 'test.pdf']
                ]);
    }

    #[Test]
    public function test_index_returns_grouped_files()
    {
        $mockFiles = collect([
            'identity' => collect([
                ['id' => 1, 'filename' => 'file1.pdf'],
            ]),
            'financial' => collect(),
            'supporting' => collect(),
        ]);

        $mockService = Mockery::mock(FileServiceInterface::class);
        $mockService->shouldReceive('getFilesGroupedByCategory')
                    ->once()
                    ->with($this->user->id)
                    ->andReturn($mockFiles);

        $this->instance(FileServiceInterface::class, $mockService);

        $response = $this->getJson('/api/files');

        $response->assertStatus(200)
                ->assertJson([
                    'identity' => [
                        ['id' => 1, 'filename' => 'file1.pdf']
                    ],
                    'financial' => [],
                    'supporting' => []
                ]);
    }

    #[Test]
    public function test_destroy_calls_service_and_returns_message()
    {
        $mockService = Mockery::mock(FileServiceInterface::class);
        $mockService->shouldReceive('deleteFile')
                    ->once()
                    ->with($this->user->id, 123)
                    ->andReturn([
                        'status' => 200,
                        'message' => 'Deleted.'
                    ]);

        $this->instance(FileServiceInterface::class, $mockService);

        $response = $this->deleteJson('/api/files/123');

        $response->assertStatus(200)
                ->assertJson(['message' => 'Deleted.']);
    }
}
