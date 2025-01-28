<?php

namespace App\Filament\Barangay\Resources\DistributionResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use App\Filament\Barangay\Resources\DistributionResource;

class EditDistribution extends EditRecord
{
    use NestedPage;


    protected ?string $heading = 'Manage Distribution Details';
    protected static string $resource = DistributionResource::class;


    protected function beforeSave(): void
    {
        // Check if the record is locked
        if ($this->record->is_locked) {
            // Show a notification
            Notification::make()
                ->title('Distribution is locked')
                ->body('You cannot edit a locked distribution.')
                ->danger()
                ->send();

            // Prevent saving by halting the save process
            $this->halt();
        }
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
