{{-- Load app layout --}}
@extends('web.layouts.app')

{{-- Exetrnal CSS section for the page --}}
@section('externalstyles')
@endsection

{{-- Main content of the pages --}}
@section('main-content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Bills</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">List All Bills</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            Search Section</div>
        <div class="card-body">
            <div class="no-footer sortable searchable fixed-columns">
                <form action="{{ route('home') }}" autocomplete="off" >
                    {{csrf_field()}}
                    
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Start Date</label>
                            <input class="form-control" id="start_date" name="start_date"
                                placeholder="Start Date" type="text"
                                value="@if(!empty($_GET['start_date'])){{ date('d-m-Y',strtotime($_GET['start_date'])) }}@endif">
                        </div>
                        <div class="col-md-6 mg-t-20 mg-md-t-0">
                            <label>End Date</label>
                            <input class="form-control" id="end_date" name="end_date"
                                placeholder="End Date" type="text"
                                value="@if(!empty($_GET['end_date'])){{ date('d-m-Y',strtotime($_GET['end_date'])) }}@else{{date('d-m-Y')}}@endif">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Start Date</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Select Status</option>
                                <option @if(!empty($_GET['status'])) @if($_GET['status']==1){{ 'selected' }}@endif @endif value="1">Active</option>
                                <option @if(!empty($_GET['status'])) @if($_GET['status']==2){{ 'selected' }}@endif @endif value="2">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" id="reset-btn" type="button">Reset</button>
                        <button class="btn btn-success" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i> Bills List
            </div>
            <div class="card-body">
                <div class="no-footer sortable searchable fixed-columns">
                    <div class="dataTable-top">
                        <div class="dataTable-search">
                            <a href="{{ route('bills.create') }}"><button class="btn btn-success">Create Bill</button></a>
                        </div>
                    </div>
                    <div class="table">
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
                        <table class="dataTable-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bill Info.</th>
                                    <th>Total Amount</th>
                                    <th>Display Order</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if(!$resultData->isEmpty())
                                    @php
                                        $i = ($resultData->currentpage()-1)* $resultData->perpage() + 1;

                                    @endphp
                                    @foreach($resultData as $result)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>
                                                <b>Bill Ref#: {{ $result->reference_number }}</b>
                                                <br>Customer Name: {{ $result->customer_name }}
                                                <br>Date: {{ date('d-M-Y H:i A',strtotime($result->created_at)) }}
                                            </td>
                                            <td>{{ 'Rs. '.number_format($result->grand_total,2)  }}</td>
                                            <td>
                                                <b>Order</b>: <input type="text" value="{{$result->display_order}}" class="form-control table-inputs" name="displayorder{{$i}}" id="displayorder{{$i}}"><a href="#" class="badge bg-danger" onclick="updateOrder({{ $result->id }},{{$i}})">&nbsp;Save</a><br>
                                            </td>
                                            <td>
                                                <b>Status</b>: <input @if($result->status==1){{ 'checked=checked' }}@endif type="radio" id="status{{$result->id}}" name="status{{$result->id}}" onchange="updateStatus({{$result->id}},1)">Active
                                                <input @if($result->status==0){{ 'checked=checked' }}@endif type="radio" id="status{{$result->id}}" name="status{{$result->id}}" onchange="updateStatus({{$result->id}},0)">Inactive
                                            </td>
                                            <td>
                                                {{-- <a target="_blank" href="{{ route('web.forms.public',['slug'=>$result->form_code]) }}"><i class="fas fa-eye"></i></a> --}}
                                                <a href="{{ route('bills.edit',['id'=>$result->id]) }}" data-bs-placement="bottom" data-bs-toggle="tooltip-primary" title="Edit Form"><i class="fas fa-edit"></i></a>
                                                <a href="{{ route('bills.delete',['id'=>$result->id]) }}" data-bs-placement="bottom" data-bs-toggle="tooltip-primary" title="Delete Form"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6">
                                            <div class="alert bg-danger alert-danger text-white mg-b-0" role="alert">
                                                No records available..
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="dataTable-bottom">
                        @if(!$resultData->isEmpty()){{ $resultData->appends(request()->query())->links('vendor.pagination.simple-bootstrap-4') }}@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Exetrnal scripts for the page --}}
@section('externalscripts')
<script>
    
    function updateStatus(id, status) {
        if (confirm('Are you sure you want to change status?')) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.post("{{ route('bills.status') }}", {
                id: id,
                status: status
            })
            .done(function (data) {
                console.log(data);
                location.reload();
            });
        }else{
            location.reload();
        }
    }

    function updateOrder(id, element) {
        if (confirm('Are you sure you want to update the order?')) {
            var order = $('#displayorder' + element).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: 'html',
                url: '{{route('bills.order')}}',
                data: {'id': id, 'order': order},
                success: function (data) {
                    location.reload();
                }

            });
        }else{
            location.reload();
        }

    }
    
</script>
<script>
    $('#start_date,#end_date').datepicker({
        format: "dd-mm-yyyy",
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true,
        changeMonth: true,
        changeYear: true,
        endDate: '+0d',
        todayHighlight: true,
        orientation: "bottom"
    });
    $('button#reset-btn').click(function (e){
        e.preventDefault();
        window.location.replace('{{ route('home') }}');
    });
</script>
@endsection