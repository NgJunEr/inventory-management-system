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
        return view('purchase_requisitions.index', compact('purchaseRequisitions'));
    }

    // Fetch a specific PR
    public function show($id)
    {
        $purchaseRequisition = PurchaseRequisition::with('products')->findOrFail($id);
        return view('purchase_requisitions.show', compact('purchaseRequisition'));
    }

    // Show form for creating a new PR
    public function create()
    {
        $products = Product::all();
        return view('purchase_requisitions.create', compact('products'));
    }

    // Store a new PR
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
            'note' => 'nullable|string',
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

        return redirect()->route('purchase-requisitions.index')->with('success', 'Purchase Requisition created successfully');
    }

    // Show form to edit an existing PR
    public function edit($id)
    {
        $purchaseRequisition = PurchaseRequisition::with('products')->findOrFail($id);
        $products = Product::all();
        return view('purchase_requisitions.edit', compact('purchaseRequisition', 'products'));
    }

    // Update an existing PR
    public function update(Request $request, $id)
    {
        $purchaseRequisition = PurchaseRequisition::findOrFail($id);

        $request->validate([
            'serial_no' => "required|unique:purchase_requisitions,serial_no,{$id}",
            'date' => 'required|date',
            'supplier_name' => 'required|string',
            'supplier_contact' => 'required|string',
            'customer_company' => 'required|string',
            'customer_name' => 'required|string',
            'customer_po' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.buying_price' => 'required|numeric|min:0',
            'products.*.selling_price' => 'required|numeric|min:0',
        ]);

        $purchaseRequisition->update($request->only([
            'serial_no', 'date', 'supplier_name', 'supplier_contact',
            'customer_company', 'customer_name', 'customer_po', 'note'
        ]));

        $productData = [];
        foreach ($request->products as $product) {
            $productData[$product['id']] = [
                'quantity' => $product['quantity'],
                'buying_price' => $product['buying_price'],
                'selling_price' => $product['selling_price']
            ];
        }

        $purchaseRequisition->products()->sync($productData);

        return redirect()->route('purchase-requisitions.index')->with('success', 'Purchase Requisition updated successfully');
    }

    // Delete a PR
    public function destroy($id)
    {
        $purchaseRequisition = PurchaseRequisition::findOrFail($id);
        $purchaseRequisition->products()->detach();
        $purchaseRequisition->delete();

        return redirect()->route('purchase-requisitions.index')->with('success', 'Purchase Requisition deleted successfully');
    }
}
