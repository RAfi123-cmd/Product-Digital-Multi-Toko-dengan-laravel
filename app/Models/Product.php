<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    
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

    public function transactionDetails(){
        return $this->hasMany(TransactionDetail::class);
    }
}
