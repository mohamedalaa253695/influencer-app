<?php
namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensCan([
            'admin' => 'Admin access',
            'influencer' => 'Influencer access'
        ]);

        Gate::define('view', function ($user, $model) {
            return $user->permissions()->contains("view_{$model}") || $user->permissions()->contains("edit_{$model}");
        });

        Gate::define('edit', function ($user, $model) {
            return $user->permissions()->contains("edit_{$model}");
        });
    }
}
