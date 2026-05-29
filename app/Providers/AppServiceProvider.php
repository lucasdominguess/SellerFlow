<?php

namespace App\Providers;

use App\Contracts\Repositories\Accout\CompanyRepositoryInterface;
use App\Contracts\Repositories\Accout\StoreRepositoryInterface;
use App\Contracts\Repositories\Accout\UserRepositoryInterface;
use App\Contracts\Repositories\Accout\UserStoreRepositoryInterface;
use App\Contracts\Repositories\Business\FornecedorRepositoryInterface;
use App\Contracts\Repositories\Business\ProductRepositoryInterface;
use App\Contracts\Repositories\ListSuspended\ListSuspendedRepositoryInterface;
use App\Contracts\Services\Accout\CompanyServiceInterface;
use App\Contracts\Services\Accout\StoreServiceInterface;
use App\Contracts\Services\Accout\UserServiceInterface;
use App\Contracts\Services\Accout\UserStoreServiceInterface;
use App\Contracts\Services\Auth\AuthServiceInterface;
use App\Contracts\Services\Business\FornecedorServiceInterface;
use App\Contracts\Services\Business\ProductServiceInterface;
use App\Contracts\Services\ListSuspended\ListSuspendedServiceInterface;
use App\Interfaces\PdfExporterInterface;
use App\Repositories\Accout\CompanyRepository;
use App\Repositories\Accout\StoreRepository;
use App\Repositories\Accout\UserRepository;
use App\Repositories\Accout\UserStoreRepository;
use App\Repositories\Business\FornecedorRepository;
use App\Repositories\Business\ProductRepository;
use App\Repositories\ListSuspended\ListSuspendedRepository;
use App\Services\Accout\CompanyService;
use App\Services\Accout\StoreService;
use App\Services\Accout\UserService;
use App\Services\Accout\UserStoreService;
use App\Services\Auth\AuthService;
use App\Services\Business\FornecedorService;
use App\Services\Business\ProductService;
use App\Services\DomPdfService;
use App\Services\ListSuspended\ListSuspendedService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

use App\Contracts\Services\Sales\VendasServiceInterface;
use App\Services\Sales\VendasService;
use App\Contracts\Repositories\Sales\VendasRepositoryInterface;
use App\Repositories\Sales\VendasRepository;
use App\Contracts\Services\Sales\VendaItemServiceInterface;
use App\Services\Sales\VendaItemService;
use App\Contracts\Repositories\Sales\VendaItemRepositoryInterface;
use App\Repositories\Sales\VendaItemRepository;
use App\Contracts\Services\Purchases\ComprasServiceInterface;
use App\Services\Purchases\ComprasService;
use App\Contracts\Repositories\Purchases\ComprasRepositoryInterface;
use App\Repositories\Purchases\ComprasRepository;
use App\Contracts\Services\Purchases\CompraServiceInterface;
use App\Services\Purchases\CompraService;
use App\Contracts\Repositories\Purchases\CompraRepositoryInterface;
use App\Repositories\Purchases\CompraRepository;
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
            FornecedorServiceInterface::class,
            FornecedorService::class
        );
        $this->app->bind(
            FornecedorRepositoryInterface::class,
            FornecedorRepository::class
        );
        $this->app->bind( AuthServiceInterface::class, AuthService::class);
        $this->app->bind(
            VendasServiceInterface::class,
            VendasService::class
        );
        $this->app->bind(
            VendasRepositoryInterface::class,
            VendasRepository::class
        );
        $this->app->bind(
            VendaItemServiceInterface::class,
            VendaItemService::class
        );
        $this->app->bind(
            VendaItemRepositoryInterface::class,
            VendaItemRepository::class
        );
        $this->app->bind(
            ComprasServiceInterface::class,
            ComprasService::class
        );
        $this->app->bind(
            ComprasRepositoryInterface::class,
            ComprasRepository::class
        );
        $this->app->bind(
            CompraServiceInterface::class,
            CompraService::class
        );
        $this->app->bind(
            CompraRepositoryInterface::class,
            CompraRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::pattern('id', '[0-9]+');
        // Gate para verificar se o usuário é um Administrador
        Gate::define('Gate-Admin', function ($user) {
            // return $user->hasRole(Roles::ADMIN->label());
        });
    }
}
