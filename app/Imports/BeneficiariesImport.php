<?php

namespace App\Imports;

use App\Models\Beneficiary;
use App\Models\ImportFailure;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BeneficiariesImport implements ToModel, WithHeadingRow
{
    private $distributionItemId;
    private $distributionId;

    public function __construct(int $distributionItemId, int $distributionId)
    {
        $this->distributionItemId = $distributionItemId;
        $this->distributionId = $distributionId;
    }

    /**
     * Map each row to a database model
     */
    public function model(array $row)
    {


        DB::beginTransaction();

        try {
            $validColumns = ['name', 'contact', 'email','address'];

            $normalizedRow = [];
            foreach ($row as $header => $value) {
                $normalizedRow[strtolower($header)] = $value;
            }

            $filteredData = [];
            foreach ($validColumns as $column) {
                $filteredData[$column] = $normalizedRow[strtolower($column)] ?? null;
            }

            $filteredData['distribution_item_id'] = $this->distributionItemId;

            $existingBeneficiary = Beneficiary::where('name', $filteredData['name'])
                ->where('distribution_item_id', $this->distributionItemId)
                ->first();

            if ($existingBeneficiary) {
                $existingBeneficiary->update([
                    'contact' => $filteredData['contact'] ?? $existingBeneficiary->contact,
                    'email' => $filteredData['email'] ?? $existingBeneficiary->email,
                    'address' => $filteredData['address'] ?? $existingBeneficiary->email,
                ]);
            } else {
                Beneficiary::create($filteredData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            // Ensure distributionId is valid
            $distributionId = $this->distributionId ?? null;

            ImportFailure::create([
                'distribution_id' => $distributionId,
                'row_data' => json_encode($row),
                'error_message' => $e->getMessage(),
            ]);

            throw $e; // Optional: Re-throw exception for debugging
        }
    }
}
