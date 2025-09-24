<?php

namespace App\Providers;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Contracts\ComplainRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\MemberRepository;
use App\Repositories\ComplainRepository;
use App\Models\User;
use App\Models\Member;
use App\Models\Complain;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind User Repository
        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new UserRepository($app->make(User::class));
        });

        // Bind Member Repository
        $this->app->bind(MemberRepositoryInterface::class, function ($app) {
            return new MemberRepository($app->make(Member::class));
        });

        // Bind Complain Repository
        $this->app->bind(ComplainRepositoryInterface::class, function ($app) {
            return new ComplainRepository($app->make(Complain::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}