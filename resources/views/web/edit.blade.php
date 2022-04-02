{{-- Load app layout --}}
@extends('web.layouts.app')

{{-- Exetrnal CSS section for the page --}}
@section('externalstyles')
<style>
    /* .dynamic-div{display: none;} */
</style>
@endsection

{{-- Main content of the pages --}}
@section('main-content')
<div class="container-fluid px-4">
    <h1 class="mt-4"><a href="{{ route('home') }}">Bill</a></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Enter the bill details</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i> Edit Bill
        </div>
        <div class="card-body">
            @if (session()->get('errorMsg'))
                <div class="alert bg-danger alert-danger text-white mg-b-0" role="alert">
                    {{-- <button aria-label="Close" class="close" data-bs-dismiss="alert" type="button">
                        <span aria-hidden="true">&times;</span></button> --}}
                    {{ session()->get('errorMsg') }}
                </div>
            @endif
            @if (session()->get('successMsg'))
                <div class="alert bg-success alert-success text-white mg-b-0" role="alert">
                    {{-- <button aria-label="Close" class="close" data-bs-dismiss="alert" type="button">
                        <span aria-hidden="true">&times;</span></button> --}}
                    {{ session()->get('successMsg') }}
                </div>
            @endif
            <form action="{{ route('bills.edit',['id'=>$resultData->id]) }}" autocomplete="off" id="editForm" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-md-12">
                        <label>Bill Number<span style="color: red">*</span></label>
                        <input class="form-control" required id="billnumber" name="billnumber" placeholder="Enter a bill number" type="text" value="{{ $resultData->reference_number ? $resultData->reference_number : old('billnumber') }}">
                        @if ($errors->has('billnumber'))
                            <div class="tags mt-1">
                                <span class="tag alert-danger">{{ $errors->first('billnumber') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label>Customer Name<span style="color: red">*</span></label>
                        <input class="form-control" required id="customer" name="customer" placeholder="Enter a customer name" type="text" value="{{ $resultData->customer_name ? $resultData->customer_name : old('customer') }}">
                        @if ($errors->has('customer'))
                            <div class="tags mt-1">
                                <span class="tag alert-danger">{{ $errors->first('customer') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row mt-2" id="options-grid" >
                    <div class="col-md-12">
                        <div class="form-group">
                            <fieldset style="border: 1px solid #e5e5e5;padding: 0px 10px 15px 10px;background: #f5f5f5;margin-bottom: 5px;">
                                <legend style="font-size: 20px;margin-bottom: 5px;border-bottom:none;">Add Fields
                                    <button type="button" value="Delete" class="btn btn-success ml-2 mb-2 mt-2 add-new">Add<i class="fa fa-plus"></i></button>
                                </legend>
                                @if ($errors->has('products.*')||$errors->has('qty.*')||$errors->has('total.*'))
                                    <div class="alert alert-danger" role="alert">
                                        @if($errors->has('products.*'))<p>{{ $errors->first('products.*') }}</p>@endif
                                        @if($errors->has('qty.*'))<p>{{ $errors->first('qty.*') }}</p>@endif
                                        @if($errors->has('total.*'))<p>{{ $errors->first('total.*') }}</p>@endif
                                    </div>
                                @endif
                                <div id="items">
                                    @php
                                        $formFields = $resultData->billed_products()->orderBy('id','asc')->get();
                                    @endphp
                                    @if($formFields)
                                        @php $inrg = 1; @endphp
                                        @foreach($formFields as $vals)
                                            <div class="row mb-2">
                                                <div class="col-md-4 padd-top">
                                                    <label>Product<span style="color: red">*</span></label>
                                                    @php
                                                        $options = \App\Models\Product::where(['status'=>1])->get();
                                                    @endphp
                                                    <select required class="form-control option-required" onchange="loadDetails(this.value,{{ $inrg }});" name="products[]" id="products{{ $inrg }}">
                                                        <option value="">Select Product</option>
                                                        @if(!$options->isEmpty())
                                                            @foreach($options as $opt)
                                                                <option @if($vals->product_id==$opt->id){{ 'selected="selected"' }}@endif value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-2 padd-top">
                                                    <label for="rate{{ $inrg }}">Rate</label>
                                                    <input readonly required class="form-control" id="rate{{ $inrg }}" name="rate[]" placeholder="rate" type="text" value="{{ $vals->product->rate ? $vals->product->rate : '0.00' }}">
                                                </div>
                                                <div class="col-md-2 padd-top">
                                                    <label for="qty{{ $inrg }}">Quantity<span style="color: red">*</span></label>
                                                    <input onchange="loadPrice(this.value,{{ $inrg }});" required class="form-control" id="qty{{ $inrg }}" name="qty[]" placeholder="Enter qty" type="number" min="1" value="{{ $vals->quantity ? $vals->quantity : '1' }}">
                                                </div>
                                                <div class="col-md-2 padd-top">
                                                    <label for="total{{ $inrg }}">Total</label>
                                                    <input required class="form-control" id="total{{ $inrg }}" name="total[]" placeholder="Total" type="text" value="{{ $vals->total_price ? $vals->total_price : '0.00' }}">
                                                </div>
                                                @if($inrg!=1)
                                                    <div class="col-md-2 form-group padd-top" style="margin-bottom: 0px;margin-top: 25px;"><button type="button" value="Delete" class="btn btn-danger btn-sm icon-btn ml-2 mb-2 remove-item"><i class="fas fa-trash"></i></button></div>
                                                @endif
                                                <div class="row mt-2 dynamic-div" id="dynamic-div-{{ $inrg }}"></div>
                                                <input type="hidden" name="counter[]" id="counter{{ $inrg }}" value="{{ $inrg }}"/>
                                            </div>
                                            @php $inrg++; @endphp
                                        @endforeach
                                    @endif
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <button class="btn btn-success" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Exetrnal scripts for the page --}}
@section('externalscripts')
<script type="text/javascript">

function loadDetails(value,id)
    {
        var quantity = $('#qty'+id).val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{ route('bills.loadoptionvalues') }}",
            data: {'product_id':value,'incr_id':id,'qty':quantity},
            timeout: 3000,
            success: function (response) {
                console.log(response);
                $('#rate'+id).val(response.data.rate);
                $('#total'+id).val(response.data.total);
            }
        });

    }
    function loadPrice(value,id)
    {
        var quantity = value;
        var product = $('#products'+id).val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{ route('bills.loadoptionvalues') }}",
            data: {'product_id':product,'incr_id':id,'qty':quantity},
            timeout: 3000,
            success: function (response) {
                console.log(response);
                $('#rate'+id).val(response.data.rate);
                $('#total'+id).val(response.data.total);
            }
        });

    }
</script>
<script>
    $(document).ready(function () {
        var avail_ingr = '{{ $inrg-1 }}';
        if(avail_ingr>0){
            var x = avail_ingr;
        }else{
            var x = 1; //initlal text box count
        }

        $(".add-new").click(function(e){
            x++;//on add input button click
            e.preventDefault();
            $("#items").append('<div class="row mb-2"><div class="col-md-4 padd-top"><label>Product<span style="color: red">*</span></label>'+
                '<select required class="form-control option-required" onchange="loadDetails(this.value,'+x+');" name="products[]" id="products'+x+'">'+
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    })+
                    $.ajax({
                        url: '{{ route('bills.loadoptionsadd') }}',
                        method: 'post',
                        success: function (data) {
                            // alert(x);
                            $('#products' + x).append(data);
                        }
                    })+
                '</select></div>'+
                '<div class="col-md-2 padd-top"><label for="rate'+x+'">Rate</label>' +
                '<input readonly required type="text" id="rate'+x+'" class="form-control" name="rate[]" placeholder="rate" value="0.00">'+
                '</div>' +
                '<div class="col-md-2 padd-top"><label for="qty'+x+'">Quantity</label>' +
                '<input required type="number" min="1" id="qty'+x+'" onchange="loadPrice(this.value,'+x+');" class="form-control" name="qty[]" placeholder="Enter Quantity" value="1">'+
                '</div>' +
                '<div class="col-md-2 padd-top"><label for="total'+x+'">Total</label>' +
                '<input required type="text" min="1" id="total'+x+'" class="form-control" name="total[]" placeholder="Total" value="0.00">'+
                '</div>' +
                '<div class="col-md-2 form-group padd-top" style="margin-bottom: 0px;margin-top: 25px;"><button type="button" value="Delete" class="btn btn-danger btn-sm icon-btn ml-2 mb-2 remove-item"><i class="fas fa-trash"></i></button></div>' +
                '<div class="row mt-2 dynamic-div" id="dynamic-div-'+x+'"></div>'+
                '<input type="hidden" name="counter[]" id="counter'+x+'" value="'+x+'"/>'+
                '</div>'
            );
        });

        $("#items").on("click",".remove-item", function(e){ //user click on remove field
            e.preventDefault(); $(this).parent().parent('div').remove(); x--;
        });

        
    });
</script>
@endsection