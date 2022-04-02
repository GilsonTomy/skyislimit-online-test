<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BilledProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected  $table = 'billed_products';

    public function bill(){
        return $this->belongsTo(Bill::class,'bill_id');
    }

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }
}
