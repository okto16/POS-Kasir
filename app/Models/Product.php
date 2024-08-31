<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [''];

    public function category()
    {
        return $this->belongsTo(Categorie::class);
    }
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

}
