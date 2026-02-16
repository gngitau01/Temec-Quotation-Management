<?php

namespace App\Imports;

use App\Models\SparePart;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SparePartsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new SparePart([
            'part_number' => $row['part_number'],
            'name' => $row['name'],
            'description' => $row['description'] ?? null,
            'amount_per_unit' => $row['amount_per_unit'],
        ]);
    }
}