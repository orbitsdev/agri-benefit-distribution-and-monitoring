<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
        Schema::defaultStringLength(191);
        Model::unguard();

        RedirectIfAuthenticated::redirectUsing(function($request){
            if ($request->is('admin/*')) {
                return redirect('/admin/dashboard'); // Redirect to admin dashboard
            }

            // if ($request->is('farmer/*')) {
            //     return redirect('/farmer/dashboard'); // Redirect to farmer dashboard
            // }

            // Default Jetstream redirection
            return redirect('/dashboard');
        });
    }
}
