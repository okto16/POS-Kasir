<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewCategorieRequest;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        return view('admin.categorie');
    }

    public function api()
    {
        $categories = Categorie::all();
        $datatables = datatables()->of($categories)->addIndexColumn();
        return $datatables->make(true);
    }

    public function store(NewCategorieRequest $request)
    {
        $categories = new Categorie();
        $categories->fill($request->all());
        $categories->save();
    
        return redirect('categories');
    }

    public function update(NewCategorieRequest $request, $id)
    {
        $category = Categorie::findOrFail($id);
        $category->fill($request->all());
        $category->save();

        return redirect('categories');
    }

    public function destroy($id)
    {
        Categorie::destroy($id);
        return redirect('categories');
    }
    
}
