<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;

class ErpReferenceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['EA', 'Unit', 0],
            ['PC', 'Piece', 0],
            ['L', 'Liter', 3],
            ['KG', 'Kilogram', 3],
            ['M', 'Meter', 3],
        ] as [$code, $name, $decimals]) {
            UnitOfMeasure::withTrashed()->firstOrCreate(
                ['code' => $code],
                ['name' => $name, 'quantity_decimals' => $decimals],
            );
        }

        foreach (['Cash', 'Pix', 'Debit Card', 'Credit Card', 'Bank Transfer'] as $name) {
            PaymentMethod::withTrashed()->firstOrCreate(['name' => $name]);
        }

        foreach (['Water', 'Electricity', 'Rent', 'Suppliers', 'Taxes', 'Maintenance', 'Other'] as $name) {
            ExpenseCategory::withTrashed()->firstOrCreate(['name' => $name], ['is_system' => true]);
        }
    }
}
