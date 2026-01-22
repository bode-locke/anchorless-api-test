<?php

namespace App\Providers;

use App\Repositories\FileRepository;
use App\Repositories\Interfaces\FileRepositoryInterface;
use App\Services\FileService;
use App\Services\Interfaces\FileServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind interface FileRepositoryInterface to FileRepository
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);

        // Bind interface FileServiceInterface to FileService
        $this->app->bind(FileServiceInterface::class, FileService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
