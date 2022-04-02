<?php

namespace App\Http\Controllers\Web;

use App\Models\Bill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BilledProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Lang;
use Validator;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status ? $request->status : NULL;
        $whereData = [];
        $start_date1 = date('Y-m-d', strtotime($start_date)) . ' 00:00:00';
        if ($end_date) {
            $end_date1 = date('Y-m-d', strtotime($end_date)) . ' 23:59:59';
        } else {
            $end_date1 = date('Y-m-d') . ' 23:59:59';
        }

        if ($start_date) {
            $whereData = [['created_at', '<=', $end_date1], ['created_at', '>=', $start_date1]];
        }
        if ($status) {
            if ($status == 1) {
                $whereData['status'] = 1;
            } else if ($status == 2) {
                $whereData['status'] = 0;
            }
        }
        $resultData = Bill::where($whereData)->orderBy('display_order', 'asc')->paginate(10);
        $data = [
            'resultData' => $resultData,
        ];
        return view('web.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        return view('web.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'billnumber' => 'required',
            'customer' => 'required',
            // 'grandtotal' => 'required',
            'field_type.*' => 'required',
            'field_name.*' => 'required',
        ];

        $messages = [
            'billnumber.required' => Lang::get('validation.required',['attribute'=>'billnumber']),
            'customer.required' => Lang::get('validation.required',['attribute'=>'customer']),
            // 'grandtotal.required' => Lang::get('validation.required',['attribute'=>'grandtotal']),
            'products.required' => Lang::get('validation.required',['attribute'=>'products']),
            'qty.required' => Lang::get('validation.required',['attribute'=>'qty']),
            'total.required' => Lang::get('validation.required',['attribute'=>'total']),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }
        $displayOrder = Bill::where([])->count();
        $displayOrder++;
        $resultInsrt = Bill::create([
            'reference_number' => $request->billnumber,
            'customer_name' => $request->customer,
            // 'grand_total' => $request->grandtotal,
            'status' =>1,
            'display_order' => $displayOrder
        ]);

        if ($resultInsrt) {
            $total = 0.00;
            if($request->products){
                for($i=0;$i<count($request->products);$i++){
                    if($request->total[$i]){
                        $total+=$request->total[$i];
                    }
                    BilledProduct::create([
                        'bill_id'=>$resultInsrt->id,
                        'product_id' => $request->products[$i],
                        'quantity' => $request->qty[$i],
                        'total_price' => $request->total[$i],
                    ]);
                }
            }
            Bill::where(['id'=>$resultInsrt->id])->update([
                'grand_total' => $total
            ]);
            return redirect()->route('home')->with('successMsg', 'Successfully created.');
        }
        return redirect()->back()->withInput($request->all())->with('errorMsg', 'Unable to process your request. Please try again.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bill = Bill::where(['id'=>$id])->first();
        if($bill){
            $data = [
                'resultData' => $bill,
            ];
            return view('web.edit', $data);
        }else{
            abort('404');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'billnumber' => 'required',
            'customer' => 'required',
            // 'grandtotal' => 'required',
            'field_type.*' => 'required',
            'field_name.*' => 'required',
        ];

        $messages = [
            'billnumber.required' => Lang::get('validation.required',['attribute'=>'billnumber']),
            'customer.required' => Lang::get('validation.required',['attribute'=>'customer']),
            // 'grandtotal.required' => Lang::get('validation.required',['attribute'=>'grandtotal']),
            'products.required' => Lang::get('validation.required',['attribute'=>'products']),
            'qty.required' => Lang::get('validation.required',['attribute'=>'qty']),
            'total.required' => Lang::get('validation.required',['attribute'=>'total']),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        $update_array = [
            'reference_number' => $request->billnumber,
            'customer_name' => $request->customer,
        ];
        $formUpdate = Bill::where(['id'=>$id])->update($update_array);
        if ($formUpdate) {
            if($request->products){
                BilledProduct::where(['bill_id'=>$id])->delete();
                $total = 0.00;
                for($i=0;$i<count($request->products);$i++){
                    if($request->total[$i]){
                        $total+=$request->total[$i];
                    }
                    BilledProduct::create([
                        'bill_id'=>$id,
                        'product_id' => $request->products[$i],
                        'quantity' => $request->qty[$i],
                        'total_price' => $request->total[$i],
                    ]);
                }
            }
            Bill::where(['id'=>$id])->update([
                'grand_total' => $total
            ]);
            
            return back()->with('successMsg', 'Successfully updated.');
        }
        return redirect()->back()->withInput($request->all())->with('errorMsg', 'Unable to process your request. Please try again.');
    }

    public function destroy($id)
    {
        Bill::destroy($id);
        return back()->with('successMsg', 'Successfully deleted.');
    }

    function changeStatus(Request $request){
        $update_array = [
            'status' => $request->status
        ];
        $userData = Bill::where(['id'=>$request->id])->update($update_array);
        if ($userData) {
            $request->session()->flash('successMsg', 'Successfully updated.');
        }
    }

    function updateOrder(Request $request){
        $update_array = [
            'display_order' => $request->order
        ];
        $result = Bill::where(['id'=>$request->id])->update($update_array);
        if ($result) {
            $request->session()->flash('successMsg', 'Successfully updated.');
        }
    }

    public function loadOptionValues(Request $request){
        $productId = $request->product_id;
        $qty = $request->qty;
        $product = Product::find($productId);
        $total = $rate = 0.00;
        if($product){
            $rate = $product->rate;
            $total = $rate*$qty;
        }
        return response()->json([
            'success' => true,
            'data' => [
                'rate' => $rate,
                'total' => $total,
            ],
        ]);
    }

    public function loadOptionsAdd(){
        $options = Product::where(['status'=>1])->get();

        $html = '';
        if(!$options->isEmpty()){
            $html= '<option value="">Select Product</option>';
            foreach($options as $optns){
                $html .='<option value="'.$optns->id.'">'.$optns->name.'</option>';
            };
        }
        echo $html;
    }
}
