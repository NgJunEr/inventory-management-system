<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequisition;
use App\Models\Product;

class PurchaseRequisitionSeeder extends Seeder
{
    public function run()
    {
        // Create a product (if it doesn't exist) for testing
        $product = Product::firstOrCreate([
            'name' => 'Product A',
        ], [
            'description' => 'This is a test product for PR seeding.',
        ]);

        // Create a purchase requisition record
        $pr = PurchaseRequisition::create([
            'serial_no' => 'PR1234',
            'date' => '2025-01-01',
            'supplier_name' => 'Supplier A',
            'supplier_contact' => '1234567890',
            'customer_company' => 'Customer Corp',
            'customer_name' => 'John Doe',
            'customer_po' => 'PO12345',
            'note' => 'Test requisition for seeding purposes',
        ]);

        // Attach the product to the purchase requisition (add the relationship)
        $pr->products()->attach($product->id, [
            'quantity' => 10,
            'buying_price' => 50.00,
            'selling_price' => 75.00,
        ]);

        // Repeat for more records if needed, for example:
        $product2 = Product::firstOrCreate([
            'name' => 'Product B',
        ], [
            'description' => 'This is another test product.',
        ]);

        $pr2 = PurchaseRequisition::create([
            'serial_no' => 'PR1235',
            'date' => '2025-01-02',
            'supplier_name' => 'Supplier B',
            'supplier_contact' => '0987654321',
            'customer_company' => 'Client Corp',
            'customer_name' => 'Jane Doe',
            'customer_po' => 'PO12346',
            'note' => 'Another test requisition',
        ]);

        $pr2->products()->attach($product2->id, [
            'quantity' => 5,
            'buying_price' => 60.00,
            'selling_price' => 90.00,
        ]);
    }
}

