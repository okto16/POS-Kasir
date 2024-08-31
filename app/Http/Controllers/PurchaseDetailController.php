<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPurchaseDetailRequest;
use App\Models\Product;
use App\Models\PurchaseDetail;
use Illuminate\Http\Request;

class PurchaseDetailController extends Controller
{
    public function index()
    {
        // Ambil `purchase_id` dari session
        $purchaseId = session('purchase_id');

        // Pastikan `purchase_id` valid
        if (!$purchaseId) {
            return redirect()->route('purchases.create')->with('error', 'Please create a purchase first.');
        }

        // Ambil data yang diperlukan untuk form, seperti daftar produk
        $products = Product::all();

        return view('purchase.details', compact('products'));
    }
    public function api()
    {
        $purchaseDetails = PurchaseDetail::all();
        $datatables = datatables()->of($purchaseDetails)->addIndexColumn();
        return $datatables->make(true);
    }

    public function store(NewPurchaseDetailRequest $request,$productId)
    {
        $purchaseId = session('purchase_id');
        if (!$purchaseId) {
            return redirect()->route('purchases.create')->with('error', 'Please create a purchase first.');
        }
        $product = Product::find($productId);
        $subtotal = $request->totals * $product->purchase_price;
        PurchaseDetail::create([
            'purchase_id' => $purchaseId,
            'product_id' => $request->productId,
            'purchase_price' => $product->purchase_price,
            'total' => $request->totals,
            'subtotal' => $subtotal,
        ]);
        // Hapus session `purchase_id` setelah selesai
        session()->forget('purchase_id');

        // Redirect kembali ke halaman utama atau halaman lain yang diperlukan
        return redirect()->route('purchases.index')->with('success', 'Purchase details added successfully.');
    }

    public function update(NewPurchaseDetailRequest $request, $id)
    {
        $category = PurchaseDetail::findOrFail($id);
        $category->fill($request->all());
        $category->save();

        return redirect('');
    }

    public function destroy($id)
    {
        PurchaseDetail::destroy($id);
        return redirect('');
    }
}
