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


    //barangay details
    $data['barangay_id'] = $barangay->id;
    $data['barangay_name'] = $barangay->name;
    $data['barangay_location'] = $barangay->location;


    //distribution details
    $data['distribution_title'] = $distribution->title;
    $data['distribution_location'] = $distribution->location;
    $data['distribution_date'] = $distribution->distribution_date;
    $data['distribution_code'] = $distribution->code;

    // distribution item details
    $data['distribution_item_name'] = $distributionItem->item->name;


    //beneficiary details
    $data['beneficiary_name'] = $beneficiary->name;
    $data['beneficiary_contact'] = $beneficiary->contact;
    $data['beneficiary_email'] = $beneficiary->email;
    $data['beneficiary_code'] = $beneficiary->code;

    //support details
    $data['support_name'] = $support->personnel->user->name;
    $data['support_type'] = $support->type;
    $data['support_unique_code'] = $support->unique_code;

    // action details
    $data['action'] = 'Claimed';
    $data['performed_at'] = now();



    // dd($data);
    return $data;
}
}
