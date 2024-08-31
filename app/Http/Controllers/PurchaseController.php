<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchase.purchase', compact('suppliers', 'products'));
    }
    public function api()
    {
        $purchase = Purchase::all();
        $datatables = datatables()->of($purchase)->addIndexColumn();
        return $datatables->make(true);
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchase.create', compact('suppliers', 'products'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|integer|min:0',
        ]);

        $purchase = Purchase::create([
            'supplier_id' => $request->supplier_id,
            'total_item' => 0, // Akan dihitung nanti
            'total_price' => 0, // Akan dihitung nanti
            'discount' => $request->discount ?? 0,
            'pay' =>  0,
        ]);

        $totalItem = 0;
        $totalPrice = 0;
        $pay = 0;

        foreach ($request->products as $productData) {
            $subtotal = $productData['quantity'] * $productData['purchase_price'];
            $totalItem += $productData['quantity'];
            $totalPrice += $subtotal;
            $pay = $totalPrice - $request->discount;

            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id' => $productData['product_id'],
                'purchase_price' => $productData['purchase_price'],
                'total' => $productData['quantity'],
                'subtotal' => $subtotal,
            ]);

            // Update stok produk
            $product = Product::find($productData['product_id']);
            $product->increment('stock', $productData['quantity']);
        }

        // Update total item dan total price di tabel purchases
        $purchase->update([
            'total_item' => $totalItem,
            'total_price' => $totalPrice,
            'pay' => $pay,
        ]);

        return response()->json(['message' => 'Pembelian berhasil disimpan']);
    }

    // Show the edit form
    public function edit($id)
    {
        $purchase = Purchase::with('details.product')->findOrFail($id);
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('purchase.edit', compact('purchase', 'suppliers', 'products'));
    }

    // Handle the update request
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
        ]);

        $purchase = Purchase::findOrFail($id);
        $purchase->update([
            'supplier_id' => $validated['supplier_id']
        ]);

        // Delete existing details and create new ones
        $purchase->details()->delete();
        $totalItem = 0;
        $totalPrice = 0;
        $pay = 0;
        foreach ($validated['products'] as $product) {
            $subtotal = $product['quantity'] * $product['purchase_price'];
            $totalItem += $product['quantity'];
            $totalPrice += $subtotal;
            $pay = $totalPrice - $request->discount;
            $purchase->details()->create([
                'product_id' => $product['product_id'],
                'total' => $product['quantity'],
                'purchase_price' => $product['purchase_price'],
                'subtotal' => $subtotal // Make sure subtotal is included
            ]);
        }
        $purchase->update([
            'total_item' => $totalItem,
            'total_price' => $totalPrice,
            'pay' => $pay,
        ]);
        return response()->json(['message' => 'Purchase updated successfully']);
    }

    public function destroy($id)
    {
        PurchaseDetail::where('purchase_id', $id)->delete();
        Purchase::destroy($id);
        return redirect('');
    }
}
