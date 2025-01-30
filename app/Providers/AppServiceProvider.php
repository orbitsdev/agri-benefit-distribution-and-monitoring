<?php

namespace App\Providers;

use Filament\Panel;
use App\Models\Support;
use App\Models\Distribution;
use App\Policies\SupportPolicy;
use App\Models\DistributionItem;

use Filament\Support\Colors\Color;
use App\Policies\DistributionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Policies\DistributionItemPolicy;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use App\Http\Middleware\EnsureDistributionIsUnlocked;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use App\Filament\Barangay\Resources\DistributionResource\Pages\EditDistribution;

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

            if ($request->is('barangay/*')) {
                return redirect('/barangay/dashboard'); // Redirect to farmer dashboard
            }

            // Default Jetstream redirection
            return redirect('/dashboard');
        });


        Gate::policy(Distribution::class, DistributionPolicy::class);
        Gate::policy(DistributionItem::class, DistributionItemPolicy::class);
        Gate::policy(Support::class, SupportPolicy::class);

      

    }
}
