<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseRequisitionController extends Controller
{
    // Fetch all PRs
    public function index()
    {
        $purchaseRequisitions = PurchaseRequisition::with('products')->get();

        if ($purchaseRequisitions->isEmpty()) {
            return response()->json(['message' => 'No records found'], 404);
        }

        return response()->json($purchaseRequisitions, 200);
    }

    // Fetch a specific PR
    public function show($id)
    {
        $purchaseRequisition = PurchaseRequisition::with('products')->find($id);

        if (!$purchaseRequisition) {
            return response()->json(['message' => 'Purchase Requisition not found'], 404);
        }

        return response()->json($purchaseRequisition, 200);
    }

    // Create a new PR
    public function store(Request $request)
    {
        $request->validate([
            'serial_no' => 'required|unique:purchase_requisitions,serial_no',
            'date' => 'required|date',
            'supplier_name' => 'required|string',
            'supplier_contact' => 'required|string',
            'customer_company' => 'required|string',
            'customer_name' => 'required|string',
            'customer_po' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.buying_price' => 'required|numeric|min:0',
            'products.*.selling_price' => 'required|numeric|min:0',
        ]);

        $purchaseRequisition = PurchaseRequisition::create($request->only([
            'serial_no', 'date', 'supplier_name', 'supplier_contact',
            'customer_company', 'customer_name', 'customer_po', 'note'
        ]));

        foreach ($request->products as $product) {
            $purchaseRequisition->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'buying_price' => $product['buying_price'],
                'selling_price' => $product['selling_price']
            ]);
        }

        return response()->json(['message' => 'Purchase Requisition created successfully'], 201);
    }

    // Update a PR
    public function update(Request $request, $id)
    {
        $purchaseRequisition = PurchaseRequisition::find($id);

        if (!$purchaseRequisition) {
            return response()->json(['message' => 'Purchase Requisition not found'], 404);
        }

        $request->validate([
            'serial_no' => "required|unique:purchase_requisitions,serial_no,{$id}",
            'date' => 'required|date',
            'supplier_name' => 'required|string',
            'supplier_contact' => 'required|string',
            'customer_company' => 'required|string',
            'customer_name' => 'required|string',
            'customer_po' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.buying_price' => 'required|numeric|min:0',
            'products.*.selling_price' => 'required|numeric|min:0',
        ]);

        $purchaseRequisition->update($request->only([
            'serial_no', 'date', 'supplier_name', 'supplier_contact',
            'customer_company', 'customer_name', 'customer_po', 'note'
        ]));

        $purchaseRequisition->products()->detach();
        foreach ($request->products as $product) {
            $purchaseRequisition->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'buying_price' => $product['buying_price'],
                'selling_price' => $product['selling_price']
            ]);
        }

        return response()->json(['message' => 'Purchase Requisition updated successfully'], 200);
    }

    // Delete a PR
    public function destroy($id)
    {
        $purchaseRequisition = PurchaseRequisition::find($id);

        if (!$purchaseRequisition) {
            return response()->json(['message' => 'Purchase Requisition not found'], 404);
        }

        $purchaseRequisition->products()->detach();
        $purchaseRequisition->delete();

        return response()->json(['message' => 'Purchase Requisition deleted successfully'], 200);
    }
}
