<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

		$this->app->bind(
			\App\Interfaces\Admin\UserServiceInterface::class,
			\App\Services\Admin\UserService::class
		);

		$this->app->bind(
			\App\Interfaces\Settings\SessionServiceInterface::class,
			\App\Services\Settings\SessionService::class
		);

		$this->app->bind(
			\App\Interfaces\Admin\RoleServiceInterface::class,
			\App\Services\Admin\RoleService::class
		);

		$this->app->bind(
			\App\Interfaces\Product\ProductCategoryServiceInterface::class,
			\App\Services\Product\ProductCategoryService::class
		);

		$this->app->bind(
			\App\Interfaces\Product\ProductServiceInterface::class,
			\App\Services\Product\ProductService::class
		);

		$this->app->bind(
			\App\Interfaces\Common\DashboardServiceInterface::class,
			\App\Services\Common\DashboardService::class
		);

		$this->app->bind(
			\App\Interfaces\Management\ClientServiceInterface::class,
			\App\Services\Management\ClientService::class
		);

		$this->app->bind(
			\App\Interfaces\Management\PaymentConditionServiceInterface::class,
			\App\Services\Management\PaymentConditionService::class
		);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Gate::before(fn ($user, $ability) => $user->hasRole(RoleEnum::SUPER_ADMIN->value) ? true : null);
    }
}
