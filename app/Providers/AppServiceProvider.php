<?php

namespace App\Providers;

use App\Events\ErpDataChanged;
use App\Models\CashEntry;
use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\Image;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\ExceptionResponse;
use Inertia\Inertia;

use function in_array;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerErpUpdates();

        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            if (in_array($response->statusCode(), [403, 404, 500, 503])) {
                return $response->render('error-page', [
                    'status' => $response->statusCode(),
                ])->withSharedData();
            }
        });
    }

    private function registerErpUpdates(): void
    {
        $channels = [
            'inventory' => [Product::class, ProductBatch::class, StockMovement::class, Image::class, Category::class, UnitOfMeasure::class],
            'movements' => [Product::class, StockMovement::class, StorageLocation::class],
            'cash-flow' => [CashEntry::class, FinancialAccount::class, ExpenseCategory::class, PaymentMethod::class],
        ];

        foreach ($channels as $channel => $models) {
            foreach ($models as $model) {
                Event::listen("eloquent.saved: $model", fn () => ErpDataChanged::dispatch($channel));
                Event::listen("eloquent.deleted: $model", fn () => ErpDataChanged::dispatch($channel));
                Event::listen("eloquent.restored: $model", fn () => ErpDataChanged::dispatch($channel));
            }
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        Model::shouldBeStrict();
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
        Model::preventLazyLoading(! app()->isProduction());
        DB::prohibitDestructiveCommands(app()->isProduction());
        Vite::prefetch(concurrency: 3);
        JsonResource::withoutWrapping();
        Date::use(CarbonImmutable::class);

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );

        Gate::before(fn ($user, $ability) => $user->hasRole('super-admin') ? true : null);
    }
}
