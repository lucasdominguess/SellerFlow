<?php

namespace App\Providers;

use App\Services\DomPdfService;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\PdfExporterInterface;


use App\Contracts\Services\Accout\UserServiceInterface;
use App\Services\Accout\UserService;
use App\Contracts\Repositories\Accout\UserRepositoryInterface;
use App\Repositories\Accout\UserRepository;
use App\Contracts\Services\Accout\StoreServiceInterface;
use App\Services\Accout\StoreService;
use App\Contracts\Repositories\Accout\StoreRepositoryInterface;
use App\Repositories\Accout\StoreRepository;
use App\Contracts\Services\Accout\UserStoreServiceInterface;
use App\Services\Accout\UserStoreService;
use App\Contracts\Repositories\Accout\UserStoreRepositoryInterface;
use App\Repositories\Accout\UserStoreRepository;
use App\Contracts\Services\Accout\CompanyServiceInterface;
use App\Services\Accout\CompanyService;
use App\Contracts\Repositories\Accout\CompanyRepositoryInterface;
use App\Repositories\Accout\CompanyRepository;
use App\Contracts\Services\ListSuspended\ListSuspendedServiceInterface;
use App\Services\ListSuspended\ListSuspendedService;
use App\Contracts\Repositories\ListSuspended\ListSuspendedRepositoryInterface;
use App\Repositories\ListSuspended\ListSuspendedRepository;
use App\Contracts\Services\Business\ProductServiceInterface;
use App\Services\Business\ProductService;
use App\Contracts\Repositories\Business\ProductRepositoryInterface;
use App\Repositories\Business\ProductRepository;
use App\Contracts\Services\Business\FornecedorServiceInterface;
use App\Services\Business\FornecedorService;
use App\Contracts\Repositories\Business\FornecedorRepositoryInterface;
use App\Repositories\Business\FornecedorRepository;
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
