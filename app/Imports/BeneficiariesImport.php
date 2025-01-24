<?php

namespace App\Imports;

use App\Models\Beneficiary;
use App\Models\ImportFailure;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BeneficiariesImport implements ToModel, WithHeadingRow
{
    private $distributionId;

    public function __construct(int $distributionId)
    {
        $this->distributionId = $distributionId;
    }

    /**
     * Map each row to a database model
     */
    public function model(array $row)
    {
        DB::beginTransaction();

        try {
            // Define valid columns for the Beneficiary model
            $validColumns = ['name', 'contact'];

            // Normalize the headers to lowercase for case-insensitivity
            $normalizedRow = [];
            foreach ($row as $header => $value) {
                $normalizedRow[strtolower($header)] = $value;
            }

            // Prepare the data by filtering only valid columns
            $filteredData = [];
            foreach ($validColumns as $column) {
                // Check if the column exists and is not empty
                $filteredData[$column] = $normalizedRow[strtolower($column)] ?? null;
            }

            // Add the distribution_item_id to the filtered data
            $filteredData['distribution_item_id'] = $this->distributionId;

            // Check for an existing beneficiary record
            $existingBeneficiary = Beneficiary::where('name', $filteredData['name'])
                ->where('distribution_item_id', $this->distributionId)
                ->first();

            if ($existingBeneficiary) {
                // Update the existing record with new details
                $existingBeneficiary->update([
                    'contact' => $filteredData['contact'] ?? $existingBeneficiary->contact,
                ]);
            } else {
                // Insert the new beneficiary
                Beneficiary::create($filteredData);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            ImportFailure::create([
                'distribution_id' => $this->distributionId,
                'row_data' => json_encode($row),
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
