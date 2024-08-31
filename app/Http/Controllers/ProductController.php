<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewProductRequest;
use App\Models\Categorie;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Categorie::all();
        return view('product.product', compact('categories'));
    }
    public function api()
    {
        $products = Product::all();
        $datatables = datatables()->of($products)->addIndexColumn();
        return $datatables->make(true);
    }

    public function store(NewProductRequest $request)
    {
        $product = Product::latest()->first() ?? new Product();
        // Generate kode produk
        $productCode = 'P'. tambah_nol_didepan((int)$product->id +1, 6);

        // Tambahkan kode produk ke dalam request data
        $request->merge(['product_code' => $productCode]);

        // Lanjutkan dengan menyimpan produk
        $product = Product::create($request->all());


        return redirect('products');
    }

    public function update(NewProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->fill($request->all());
        $product->save();

        return redirect('products');
    }

    public function destroy($id)
    {
        Product::destroy($id);
        return redirect('');
    }
    public function cetakBarcode(Request $request)
    {
        $ids = $request->input('ids', []);

        // Validasi jika tidak ada ID yang dipilih
        if (empty($ids)) {
            return redirect()->back()->withErrors('Tidak ada produk yang dipilih.');
        }

        // Dapatkan data produk berdasarkan ID yang diberikan
        $products = Product::whereIn('id', $ids)->get();

        // Buat instance DNS1D untuk generate barcode
        $barcodeGenerator = new DNS1D();

        // Generate barcode untuk setiap produk
        $barcodes = $products->map(function ($product) use ($barcodeGenerator) {
            return [
                'product' => $product,
                'barcode' => $barcodeGenerator->getBarcodeSVG($product->product_code, 'c39',1,58),
            ];
        });

        $pdf = Pdf::loadView('product.barcodes', compact('barcodes'));

        // Set ukuran kertas dan orientasi
        $pdf->setPaper('A4', 'portrait'); 
        return view('product.barcodes', compact('barcodes'));
    }
}
