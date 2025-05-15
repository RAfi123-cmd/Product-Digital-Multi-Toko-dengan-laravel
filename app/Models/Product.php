<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'product_category_id',
        'image',
        'name',
        'description',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function productCategory(){
        return $this->belongsTo(ProductCategory::class);
    }
}
