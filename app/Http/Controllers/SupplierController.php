<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(){
        return view('supplier.supplier');
    }
    public function api()
    {
        $expenses = Supplier::all();
        $datatables = datatables()->of($expenses)->addIndexColumn();
        return $datatables->make(true);
    }

    public function store(NewSupplierRequest $request)
    {
        $expenses = new Supplier();
        $expenses->fill($request->all());
        $expenses->save();
    
        return redirect('suppliers');
    }

    public function update(NewSupplierRequest $request, $id)
    {
        $category = Supplier::findOrFail($id);
        $category->fill($request->all());
        $category->save();

        return redirect('suppliers');
    }

    public function destroy($id)
    {
        Supplier::destroy($id);
        return redirect('');
    }
}
