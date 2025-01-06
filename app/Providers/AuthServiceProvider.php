<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider; e in conflict cu cea de mai jos deci nu o sa o folosim
use App\Models\Job;
use App\Policies\JobPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [Job::class => JobPolicy::class];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
