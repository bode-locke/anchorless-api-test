<?php

namespace Tests\Unit\Repositories;

use App\Models\File;
use App\Models\User;
use App\Repositories\FileRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FileRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private FileRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FileRepository();
    }

    #[Test]
    public function it_creates_a_file(): void
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'filename' => 'test.pdf',
            'filepath' => '/files/test.pdf',
            'category' => 'documents',
            'type' => 'pdf',
        ];

        $file = $this->repository->create($data);

        $this->assertInstanceOf(File::class, $file);
        $this->assertDatabaseHas('files', [
            'filename' => 'test.pdf',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_finds_a_file_by_id_and_user(): void
    {
        $user = User::factory()->create();

        $file = File::factory()->create([
            'user_id' => $user->id,
        ]);

        $found = $this->repository->findByIdAndUser($file->id, $user->id);

        $this->assertNotNull($found);
        $this->assertEquals($file->id, $found->id);
    }

    #[Test]
    public function it_returns_null_if_file_does_not_belong_to_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $file = File::factory()->create([
            'user_id' => $owner->id,
        ]);

        $found = $this->repository->findByIdAndUser($file->id, $otherUser->id);

        $this->assertNull($found);
    }

    #[Test]
    public function it_deletes_a_file(): void
    {
        $file = File::factory()->create();

        $this->repository->delete($file);

        $this->assertDatabaseMissing('files', [
            'id' => $file->id,
        ]);
    }

    #[Test]
    public function it_gets_all_files_by_user_grouped_by_category(): void
    {
        $user = User::factory()->create();

        File::factory()->create([
            'user_id' => $user->id,
            'category' => 'images',
        ]);

        File::factory()->create([
            'user_id' => $user->id,
            'category' => 'documents',
        ]);

        File::factory()->create([
            'user_id' => $user->id,
            'category' => 'images',
        ]);

        $grouped = $this->repository->getAllByUserGroupedByCategory($user->id);

        $this->assertTrue($grouped->has('images'));
        $this->assertTrue($grouped->has('documents'));
        $this->assertCount(2, $grouped->get('images'));
        $this->assertCount(1, $grouped->get('documents'));
    }
}
