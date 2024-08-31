<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.user');
    }
    public function api()
    {
        $products = User::all();
        $datatables = datatables()->of($products)->addIndexColumn();
        return $datatables->make(true);
    }

    public function store(NewUserRequest $request)
    {
        $expenses = new User();
        $expenses->fill($request->all());
        $expenses->save();
        session(['purchase_id' => $expenses->id]);
        return redirect('purchases-details');
    }

    public function update(NewUserRequest $request, $id)
    {
        $product = User::findOrFail($id);
        $product->fill($request->all());
        $product->save();

        return redirect('products');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return redirect('');
    }
}
