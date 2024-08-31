<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;

class SaleDetailController extends Controller
{
    public function index()
    {
        $members = Member::all();
        $products = Product::all();
        return view('sale.sale', compact('members', 'products'));
    }
    public function api()
    {
        $sale = SaleDetail::all();
        $datatables = datatables()->of($sale)->addIndexColumn();
        return $datatables->make(true);
    }
}
