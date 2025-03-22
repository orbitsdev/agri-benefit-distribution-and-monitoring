<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PSGCService
{
    protected $baseUrl = 'https://psgc.gitlab.io/api/';

    public function getRegions()
    {
        return Http::withoutVerifying()->get($this->baseUrl . 'regions')->json();
    }

    public function getProvinces($regionCode = null)
    {
        $endpoint = $regionCode ? "regions/$regionCode/provinces" : 'provinces';
        return Http::withoutVerifying()->get($this->baseUrl . $endpoint)->json();
    }

    public function getCitiesMunicipalities($provinceCode)
    {
        return Http::withoutVerifying()->get($this->baseUrl . "provinces/$provinceCode/cities-municipalities")->json();
    }

    public function getBarangays($cityCode)
    {
        return Http::withoutVerifying()->get($this->baseUrl . "cities-municipalities/$cityCode/barangays")->json();
    }
}
