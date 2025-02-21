<?php

namespace App\Filament\Barangay\Resources\TransactionResource\Pages;

use Filament\Actions;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Barangay\Resources\TransactionResource;
use App\Models\Distribution;
use App\Models\DistributionItem;
use App\Models\Support;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
{
    $barangay = Auth::user()->barangay;
    $distribution = Distribution::findOrFail($data['distribution_id']);
    $distributionItem = DistributionItem::findOrFail($data['distribution_item_id']);
    $beneficiary = Beneficiary::findOrFail($data['beneficiary_id']);
    $support = Support::findOrFail($data['support_id']);

    // Assign Foreign Keys
    $data['barangay_id'] = $barangay->id;
    $data['distribution_id'] = $distribution->id;
    $data['distribution_item_id'] = $distributionItem->id;
    $data['beneficiary_id'] = $beneficiary->id;
    $data['support_id'] = $support->id;

    // JSON Snapshots (storing full details)
    $data['barangay_details'] = [
        'id' => $barangay->id,
        'name' => $barangay->name,
        'location' => $barangay->location,
    ];

    $data['distribution_details'] = [
        'id' => $distribution->id,
        'title' => $distribution->title,
        'location' => $distribution->location,
        'date' => $distribution->distribution_date,
        'code' => $distribution->code,
    ];

    $data['distribution_item_details'] = [
        'id' => $distributionItem->id,
        'name' => $distributionItem->item->name,
    ];

    $data['beneficiary_details'] = [
        'id' => $beneficiary->id,
        'name' => $beneficiary->name,
        'contact' => $beneficiary->contact,
        'address' => $beneficiary->address,
        'email' => $beneficiary->email,
        'code' => $beneficiary->code,
    ];

    $data['support_details'] = [
        'id' => $support->id,
        'name' => $support->personnel->user->name,
        'type' => $support->type,
        'unique_code' => $support->unique_code,
    ];

    // Action Details
    $data['action'] = 'Claimed';
    $data['performed_at'] = now();

    return $data;
}
}
