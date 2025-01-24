<?php

namespace App\Imports;

use App\Models\Beneficiary;
use App\Models\ImportFailure;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BeneficiariesImport implements ToModel, WithHeadingRow, WithValidation
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
            // Check for duplicate based on name and distribution_item_id
            if (Beneficiary::where('name', $row['name'])
                ->where('distribution_item_id', $this->distributionId)
                ->exists()) {
                ImportFailure::create([
                    'distribution_id' => $this->distributionId,
                    'row_data' => json_encode($row),
                    'error_message' => 'Duplicate name found.',
                ]);

                DB::rollBack();
                return null;
            }

            // Insert the beneficiary
            $beneficiary = Beneficiary::create([
                'name' => $row['name'],
                'distribution_item_id' => $this->distributionId,
            ]);

            DB::commit();

            return $beneficiary;
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            ImportFailure::create([
                'distribution_id' => $this->distributionId,
                'row_data' => json_encode($row),
                'error_message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Define the validation rules for each row
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
