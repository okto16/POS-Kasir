<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewSaleRequest;
use App\Models\Member;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function index()
    {
        $members = Member::all();
        $products = Product::all();
        return view('sale.sale', compact('members', 'products'));
    }
    public function api()
    {
        $sale = Sale::all();
        $datatables = datatables()->of($sale)->addIndexColumn();
        return $datatables->make(true);
    }

    public function create()
    {
        $members = Member::all();
        $products = Product::all();
        $users = User::all();
        return view('sale.create', compact('members', 'products', 'users'));
    }

    public function store(Request $request)
    {
        $totalItems = 0;
        $totalPrice = 0;
        $saleDetails = [];

        foreach ($request->products as $product) {
            $productData = Product::find($product['id']);
            $discountedPrice = $productData->sell_price - $productData->discount;
            $subtotal = $discountedPrice * $product['quantity'];
            $totalItems += $product['quantity'];
            $totalPrice += $subtotal;

            $saleDetails[] = new SaleDetail([
                'product_id' => $product['id'],
                'sell_price' => $productData->sell_price,
                'total' => $product['quantity'],
                'discount' => $productData->discount,
                'subtotal' => $subtotal
            ]);

            // Kurangi stok produk
            $productData->stock -= $product['quantity'];
            $productData->save();
        }

        $sale = Sale::create([
            'member_id' => $request->member_id,
            'total_item' => $totalItems,
            'total_price' => $totalPrice,
            'discount' => $request->discount,
            'pay' => $request->pay,
            'received' => $request->received,
            'user_id' => $request->user_id
        ]);

        $sale->saleDetails()->saveMany($saleDetails);

        return response()->json(['message' => 'Sale completed successfully']);
    }

    public function edit($id)
    {
        $sale = Sale::with('details.product')->findOrFail($id);
        $members = Member::all();
        $products = Product::all();

        return view('sale.edit', compact('sale', 'members', 'products'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'products' => 'required|array',
            'user_id' => 'required',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.sale_price' => 'required|numeric|min:0',
            'received' => 'required|integer|min:0',
        ]);

        $sale = Sale::findOrFail($id);
        $sale->update([
            'member_id' => $validated['member_id'],
            'received' => $validated['received'],
        ]);

        // Delete existing details and create new ones
        $sale->details()->delete();
        $totalItem = 0;
        $totalPrice = 0;
        $pay = 0;

        foreach ($validated['products'] as $product) {
            $subtotal = $product['quantity'] * $product['sale_price'] - ($product['discount'] ?? 0);
            $totalItem += $product['quantity'];
            $totalPrice += $subtotal;
            $pay = $totalPrice - $request->discount;

            $sale->details()->create([
                'product_id' => $product['product_id'],
                'total' => $product['quantity'],
                'sale_price' => $product['sale_price'],
                'discount' => $product['discount'] ?? 0,
                'subtotal' => $subtotal,
            ]);

            // Update stok produk
            $productModel = Product::find($product['product_id']);
            $productModel->decrement('stock', $product['quantity']);
        }

        $sale->update([
            'total_item' => $totalItem,
            'total_price' => $totalPrice,
            'pay' => $pay,
        ]);

        return response()->json(['message' => 'Penjualan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        SaleDetail::where('sale_id', $id)->delete();
        Sale::destroy($id);
        return redirect()->route('sales.index')->with('message', 'Penjualan berhasil dihapus');
    }
}
