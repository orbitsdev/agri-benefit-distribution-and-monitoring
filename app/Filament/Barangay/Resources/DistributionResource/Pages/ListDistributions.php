<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use App\Filament\Barangay\Resources\DistributionResource;
use App\Models\Distribution;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
class ListDistributions extends ListRecords
{
    use NestedPage;
    protected static string $resource = DistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array{
      return [
        'all' => Tab::make('All customers'),
        'Planned'=> Tab::make('Planned')->modifyQueryUsing(fn (Builder $query) => $query->planned()),
        'Ongoing'=> Tab::make('Ongoing')->modifyQueryUsing(fn (Builder $query) => $query->ongoing()),
        'Completed'=> Tab::make('Completed')->modifyQueryUsing(fn (Builder $query) => $query->completed()),
        'Canceled'=> Tab::make('Canceled')->modifyQueryUsing(fn (Builder $query) => $query->canceled()),
      ];
    }
}
