<?php

namespace App\Providers;

use App\Contracts\Repositories\Accout\CompanyRepositoryInterface;
use App\Contracts\Repositories\Accout\StoreRepositoryInterface;
use App\Contracts\Repositories\Accout\UserRepositoryInterface;
use App\Contracts\Repositories\Accout\UserStoreRepositoryInterface;
use App\Contracts\Repositories\Adjustment\StockAdjustmentRepositoryInterface;
use App\Contracts\Repositories\Business\SupplierRepositoryInterface;
use App\Contracts\Repositories\Business\ProductRepositoryInterface;
use App\Contracts\Repositories\Business\ValidateProductRepositoryInterface;
use App\Contracts\Repositories\Finance\AccountPayableRepositoryInterface;
use App\Contracts\Repositories\Finance\AccountReceivableRepositoryInterface;
use App\Contracts\Repositories\Finance\CashFlowRepositoryInterface;
use App\Contracts\Repositories\ListSuspended\ListSuspendedRepositoryInterface;
use App\Contracts\Repositories\Purchases\PurchaseRepositoryInterface;
use App\Contracts\Repositories\Sales\SaleRepositoryInterface;
use App\Contracts\Repositories\Stock\StockBalanceRepositoryInterface;
use App\Contracts\Repositories\Stock\StockRepositoryInterface;
use App\Contracts\Services\Accout\CompanyServiceInterface;
use App\Contracts\Services\Accout\StoreServiceInterface;
use App\Contracts\Services\Accout\UserServiceInterface;
use App\Contracts\Services\Accout\UserStoreServiceInterface;
use App\Contracts\Services\Adjustment\StockAdjustmentServiceInterface;
use App\Contracts\Services\Auth\AuthServiceInterface;
use App\Contracts\Services\Business\SupplierServiceInterface;
use App\Contracts\Services\Business\ProductServiceInterface;
use App\Contracts\Services\Business\ValidateProductServiceInterface;
use App\Contracts\Services\Finance\AccountPayableServiceInterface;
use App\Contracts\Services\Finance\AccountReceivableServiceInterface;
use App\Contracts\Services\Finance\CashFlowServiceInterface;
use App\Contracts\Services\ListSuspended\ListSuspendedServiceInterface;
use App\Contracts\Services\Purchases\PurchaseServiceInterface;
use App\Contracts\Services\Sales\SaleServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\Interfaces\PdfExporterInterface;
use App\Models\Stock\Stock;
use App\Observers\StockObserver;
use App\Repositories\Accout\CompanyRepository;
use App\Repositories\Accout\StoreRepository;
use App\Repositories\Accout\UserRepository;
use App\Repositories\Accout\UserStoreRepository;
use App\Repositories\Adjustment\StockAdjustmentRepository;
use App\Repositories\Business\SupplierRepository;
use App\Repositories\Business\ProductRepository;
use App\Repositories\Business\ValidateProductRepository;
use App\Repositories\Finance\AccountPayableRepository;
use App\Repositories\Finance\AccountReceivableRepository;
use App\Repositories\Finance\CashFlowRepository;
use App\Repositories\ListSuspended\ListSuspendedRepository;
use App\Repositories\Purchases\PurchaseRepository;
use App\Repositories\Sales\SaleRepository;
use App\Repositories\Stock\StockBalanceRepository;
use App\Repositories\Stock\StockRepository;
use App\Services\Accout\CompanyService;
use App\Services\Accout\StoreService;
use App\Services\Accout\UserService;
use App\Services\Accout\UserStoreService;
use App\Services\Adjustment\StockAdjustmentService;
use App\Services\Auth\AuthService;
use App\Services\Business\SupplierService;
use App\Services\Business\ProductService;
use App\Services\Business\ValidateProductService;
use App\Services\DomPdfService;
use App\Services\Finance\AccountPayableService;
use App\Services\Finance\AccountReceivableService;
use App\Services\Finance\CashFlowService;
use App\Services\ListSuspended\ListSuspendedService;
use App\Services\Purchases\PurchaseService;
use App\Services\Sales\SaleService;
use App\Services\Stock\StockService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->bind(SanitizerInterface::class, XssCleanService::class);
        // $this->app->bind(LdapInterface::class, LdapService::class);
        $this->app->bind(PdfExporterInterface::class, DomPdfService::class);
        // $this->app->bind(SocialAuthInterface::class, GoogleAuthService::class);
        // $this->app->register(L5SwaggerServiceProvider::class);
        $this->app->bind(
            UserServiceInterface::class,
            UserService::class
        );
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            StoreServiceInterface::class,
            StoreService::class
        );
        $this->app->bind(
            StoreRepositoryInterface::class,
            StoreRepository::class
        );
        $this->app->bind(
            UserStoreServiceInterface::class,
            UserStoreService::class
        );
        $this->app->bind(
            UserStoreRepositoryInterface::class,
            UserStoreRepository::class
        );
        $this->app->bind(
            CompanyServiceInterface::class,
            CompanyService::class
        );
        $this->app->bind(
            CompanyRepositoryInterface::class,
            CompanyRepository::class
        );
        $this->app->bind(
            ListSuspendedServiceInterface::class,
            ListSuspendedService::class
        );
        $this->app->bind(
            ListSuspendedRepositoryInterface::class,
            ListSuspendedRepository::class
        );
        $this->app->bind(
            ProductServiceInterface::class,
            ProductService::class
        );
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
        $this->app->bind(
            SupplierServiceInterface::class,
            SupplierService::class
        );
        $this->app->bind(
            SupplierRepositoryInterface::class,
            SupplierRepository::class
        );
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(
            SaleServiceInterface::class,
            SaleService::class
        );
        $this->app->bind(
            SaleRepositoryInterface::class,
            SaleRepository::class
        );
        $this->app->bind(
            PurchaseServiceInterface::class,
            PurchaseService::class
        );
        $this->app->bind(
            PurchaseRepositoryInterface::class,
            PurchaseRepository::class
        );
        $this->app->bind(
            StockServiceInterface::class,
            StockService::class
        );
        $this->app->bind(
            StockRepositoryInterface::class,
            StockRepository::class
        );
        $this->app->bind(
            StockBalanceRepositoryInterface::class,
            StockBalanceRepository::class
        );
        $this->app->bind(
            StockAdjustmentServiceInterface::class,
            StockAdjustmentService::class
        );
        $this->app->bind(
            StockAdjustmentRepositoryInterface::class,
            StockAdjustmentRepository::class
        );
        $this->app->bind(
            AccountPayableServiceInterface::class,
            AccountPayableService::class
        );
        $this->app->bind(
            AccountPayableRepositoryInterface::class,
            AccountPayableRepository::class
        );
        $this->app->bind(
            AccountReceivableServiceInterface::class,
            AccountReceivableService::class
        );
        $this->app->bind(
            AccountReceivableRepositoryInterface::class,
            AccountReceivableRepository::class
        );
        $this->app->bind(
            CashFlowServiceInterface::class,
            CashFlowService::class
        );
        $this->app->bind(
            CashFlowRepositoryInterface::class,
            CashFlowRepository::class
        );
        $this->app->bind(
            ValidateProductServiceInterface::class,
            ValidateProductService::class
        );
        $this->app->bind(
            ValidateProductRepositoryInterface::class,
            ValidateProductRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::pattern('id', '[0-9]+');

        // Mantém o saldo materializado (stock_balances) sincronizado a cada movimentação.
        Stock::observe(StockObserver::class);

        // Gate para verificar se o usuário é um Administrador
        Gate::define('Gate-Admin', function ($user) {
            // return $user->hasRole(Roles::ADMIN->label());
        });
    }
}
