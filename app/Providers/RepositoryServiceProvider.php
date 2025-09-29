<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repository Interfaces
use App\Contracts\RepositoryInterface;

// Repository Implementations
use App\Repositories\BaseRepository;
use App\Repositories\UserRepository;
use App\Repositories\ComplainRepository;
use App\Repositories\TaxRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\NewsRepository;
use App\Repositories\MemberRepository;
use App\Repositories\OfficerRepository;
use App\Repositories\DownloadRepository;

// Services
use App\Services\UserService;
use App\Services\ComplainService;
use App\Services\TaxService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Repository Implementations
        $this->registerRepositories();

        // Register Services
        $this->registerServices();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register repository implementations
     */
    private function registerRepositories(): void
    {
        // User Repository
        $this->app->singleton(UserRepository::class, function ($app) {
            return new UserRepository();
        });

        // Complain Repository
        $this->app->singleton(ComplainRepository::class, function ($app) {
            return new ComplainRepository();
        });

        // Tax Repository
        $this->app->singleton(TaxRepository::class, function ($app) {
            return new TaxRepository();
        });

        // Payment Repository
        $this->app->singleton(PaymentRepository::class, function ($app) {
            return new PaymentRepository();
        });

        // Project Repository
        $this->app->singleton(ProjectRepository::class, function ($app) {
            return new ProjectRepository();
        });

        // News Repository
        $this->app->singleton(NewsRepository::class, function ($app) {
            return new NewsRepository();
        });

        // Member Repository
        $this->app->singleton(MemberRepository::class, function ($app) {
            return new MemberRepository();
        });

        // Officer Repository
        $this->app->singleton(OfficerRepository::class, function ($app) {
            return new OfficerRepository();
        });

        // Download Repository
        $this->app->singleton(DownloadRepository::class, function ($app) {
            return new DownloadRepository();
        });
    }

    /**
     * Register service implementations
     */
    private function registerServices(): void
    {
        // User Service
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService(
                $app->make(UserRepository::class)
            );
        });

        // Complain Service
        $this->app->singleton(ComplainService::class, function ($app) {
            return new ComplainService(
                $app->make(ComplainRepository::class)
            );
        });

        // Tax Service
        $this->app->singleton(TaxService::class, function ($app) {
            return new TaxService(
                $app->make(TaxRepository::class),
                $app->make(PaymentRepository::class)
            );
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            // Repositories
            UserRepository::class,
            ComplainRepository::class,
            TaxRepository::class,
            PaymentRepository::class,
            ProjectRepository::class,
            NewsRepository::class,
            MemberRepository::class,
            OfficerRepository::class,
            DownloadRepository::class,

            // Services
            UserService::class,
            ComplainService::class,
            TaxService::class,
        ];
    }
}
