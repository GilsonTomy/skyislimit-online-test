<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected  $table = 'bills';

    public function billed_products(){
        return $this->hasMany(BilledProduct::class,'bill_id');
    }
}
