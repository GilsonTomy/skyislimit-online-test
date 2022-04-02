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
            <i class="fas fa-table me-1"></i> Create Bill
        </div>
        <div class="card-body">
            <form action="{{ route('bills.store') }}" autocomplete="off" id="addForm" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-md-12">
                        <label>Bill Number<span style="color: red">*</span></label>
                        <input class="form-control" required id="billnumber" name="billnumber" placeholder="Enter a bill number" type="text" value="{{ old('billnumber') }}">
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
                        <input class="form-control" required id="customer" name="customer" placeholder="Enter a customer name" type="text" value="{{ old('customer') }}">
                        @if ($errors->has('customer'))
                            <div class="tags mt-1">
                                <span class="tag alert-danger">{{ $errors->first('customer') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row mt-3" id="options-grid" >
                    <div class="col-md-12">
                        <div class="form-group">
                            <fieldset style="border: 1px solid #e5e5e5;padding: 0px 10px 15px 10px;background: #f5f5f5;margin-bottom: 5px;">
                                <legend style="font-size: 20px;margin-bottom: 5px;border-bottom:none;">Add Products
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
                                    <div class="row mb-2">
                                        <div class="col-md-4 padd-top">
                                            <label>Product<span style="color: red">*</span></label>
                                            @php
                                                $options = \App\Models\Product::where(['status'=>1])->get();
                                            @endphp
                                            <select class="form-control option-required" onchange="loadDetails(this.value,1);" name="products[]" id="products1">
                                                <option value="">Select Product</option>
                                                @if(!$options->isEmpty())
                                                    @foreach($options as $opt)
                                                        <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-2 padd-top">
                                            <label for="rate1">Rate</label>
                                            <input readonly required class="form-control" id="rate1" name="rate[]" placeholder="rate" type="text" value="0.00">
                                        </div>
                                        <div class="col-md-2 padd-top">
                                            <label for="qty1">Quantity<span style="color: red">*</span></label>
                                            <input onchange="loadPrice(this.value,1);" required class="form-control" id="qty1" name="qty[]" placeholder="Enter qty" type="number" min="1" value="1">
                                        </div>
                                        <div class="col-md-2 padd-top">
                                            <label for="total1">Total</label>
                                            <input required class="form-control" id="total1" name="total[]" placeholder="Total" type="text" value="0">
                                        </div>
                                        <div class="col-md-2"></div>
                                        <div class="row mt-2 dynamic-div" id="dynamic-div-1"></div>
                                        <input type="hidden" name="counter[]" id="counter1" value="1"/>
                                    </div>
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
        var x = 1;

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