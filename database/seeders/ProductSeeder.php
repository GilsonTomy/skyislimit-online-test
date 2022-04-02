<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Admin Seeder */
        DB::table('products')->insert([
            [
                'id'=>1,
                'name'=>'Pen',
                'product_code'=>'PR001',
                'rate'=>10.00,
                'display_order'=>1,
                'status'=>1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id'=>2,
                'name'=>'Pencil',
                'product_code'=>'PR002',
                'rate'=>5.00,
                'display_order'=>2,
                'status'=>1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id'=>3,
                'name'=>'Classic Note Book',
                'product_code'=>'PR003',
                'rate'=>35.50,
                'display_order'=>3,
                'status'=>1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id'=>4,
                'name'=>'Mug',
                'product_code'=>'PR004',
                'rate'=>100.00,
                'display_order'=>4,
                'status'=>1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id'=>5,
                'name'=>'Keyboard',
                'product_code'=>'PR005',
                'rate'=>1250.00,
                'display_order'=>5,
                'status'=>1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id'=>6,
                'name'=>'Mouse',
                'product_code'=>'PR006',
                'rate'=>600.50,
                'display_order'=>6,
                'status'=>1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
