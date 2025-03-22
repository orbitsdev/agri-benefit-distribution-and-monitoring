<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\EnsureIsAdmin;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Barangay\Pages\EditProfile;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Barangay\Widgets\LatestDistributions;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class BarangayPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('barangay')
            ->path('barangay')
            ->login()
            ->colors([
                'primary' => '#1e40af',
            ])
            ->brandName(function(){
                if(Auth::user()->barangay){
                    return 'Barangay '. Auth::user()->barangay->name. ' Agri Distribution System';
                }
                    return 'Agriculture Benefit Distribution and Monitoring';

            })

            ->discoverResources(in: app_path('Filament/Barangay/Resources'), for: 'App\\Filament\\Barangay\\Resources')
            ->discoverPages(in: app_path('Filament/Barangay/Pages'), for: 'App\\Filament\\Barangay\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Barangay/Widgets'), for: 'App\\Filament\\Barangay\\Widgets')
            ->widgets([
                LatestDistributions::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureIsAdmin::class
            ])
            ->profile(EditProfile::class,isSimple:false)
            // ->userMenuItems([
            //     'My Profile' => MenuItem::make()->url(fn (): string => EditProfile::getUrl())
            // ])
            ->navigationItems([


                // NavigationItem::make('Chat')
                //     ->url(fn(): string => url('/chats'), shouldOpenInNewTab: true) // Link to the chat page
                //     ->icon('heroicon-o-chat-bubble-left-right') // Chat icon
                //     ->group('Communication') // Optional: Group

                //     ->sort(1),


            ])->viteTheme('resources/css/filament/barangay/theme.css')
            ->darkMode(false)

        ;
    }
}
