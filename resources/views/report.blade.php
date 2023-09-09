@extends('layout')
@section('title', 'REPORT')

@section('content')
<style>
    .tl-left {
        text-align: right;
    }
</style>
<div class="row mt-5">
    <h1>TRANSACTION REPORT</h1>
    <a class="mb-3" href="{{ route('top.distributor') }}">See top selling</a>
</div>

<br>
@if($error !== null)
<p>{{$error}}</p>
@endif
<div class="row">
    <form class="col-lg-7">
        @csrf
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="distributor" class="col-form-label">Distributor</label>
            </div>
            <div class="col-auto">
                <input type="text" id="distributor" name="distributor" class="typeahead form-control" placeholder="search by ID, username, first name and last name">
            </div>
        </div>
        <br>
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="startDate" class="col-form-label">Date From</label>
            </div>
            <div class="col-auto">
                <input type="date" id="startDate" name="startDate" class="form-control" aria-describedby="startDate">
            </div>
            <div class="col-auto">
                <label for="endDate" class="col-form-label">To</label>
            </div>
            <div class="col-auto">
                <input type="date" id="endDate" name="endDate" class="form-control" aria-describedby="endDate">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>
    <!-- <div class="col-lg-5 tl-left">
        <div>TOTAL COMMISSION</div>
        <p>200</p>
        <div class="row g-3 align-items-center">
            <div class="col-5"></div>
            <div class="col-auto">
                <label for="distributor" class="col-form-label">Search</label>
            </div>
            <div class="col-auto">
                <input type="text" class="form-control" aria-describedby="distributor">
            </div>
        </div>
    </div> -->
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">Invoice</th>
            <th scope="col">Purchaser</th>
            <th scope="col">Distributor</th>
            <th scope="col">Referred Distributor</th>
            <th scope="col">Order Date</th>
            <th scope="col">Order Total</th>
            <th scope="col">Percentage</th>
            <th scope="col">Commission</th>
            <th scope="col"></th>

        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        @php
        $totalReferrer = $order->user->referer !== null ? $order->user->referer->totalReferrers : 0;
        $percentage = calculateCommissionHelper($totalReferrer);

        @endphp
        <tr>
            <td>{{$order->invoice_number}}</td>
            <td>{{$order->user->full_name}}</td>
            <td>
                @if($order->user->referer !== null)
                @if($order->user->referer->is_distributor)
                {{$order->user->referer->first_name}}
                @endif
                @endif
            </td>
            <td>{{ $order->user->referer !== null ? $order->user->referer->totalReferrers : ''}}</td>
            <td>{{$order->order_date}}</td>
            <td>{{$order->orderTotal}}</td>
            <td>{{$percentage}}%</td>
            <td>{{($percentage/100) * $order->orderTotal}}</td>
            <td>
                <a class="" onclick="displayOrderItemsModal('{{ $order->id }}', '{{$order->invoice_number}}')"href="javascript:void(0)">View Items</a>
            </td>
        </tr>
        @endforeach

    </tbody>
</table>
<div class="d-flex">
    {!! $orders->links() !!}
</div>

<div class="modal fade " id="orderItemsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-4 text-dark" id="invoice_number">Order Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <table class="table  table-hover">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>

                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tbody id="ordered-items">

                    </table>


                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primaryi-outline" data-bs-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>


<script>
    function displayOrderItemsModal(orderId, invoiceNumber) {

        $('#ordered-items').empty();

        document.getElementById('invoice_number').innerHTML = 'Invoice: ' + invoiceNumber;

        $(document).ready(function() {
            $.ajax({
                url: `/api/get-order-items/${orderId}`,
                type: "GET",
                cache: true,
                success: function(response) {
                    $.each(response, function(key, value) {
                        $('#ordered-items').append(`
                <tr>
                    <td>${value.product.sku}</td>
                    <td>${value.product.name}</td>
                    <td>${value.product.price}</td>
                    <td>${value.qantity}</td>
                    <td>$${value.qantity * value.product.price} </td>
                </tr>
                `);
                    })
                }

            });
        });

        $('#orderItemsModal').modal('show')
    }

    var path = "/api/users";
    $("#distributor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: path,
                type: 'GET',
                dataType: "json",
                data: {
                    search: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            $('#distributor').val(ui.item.first_name);
            return false;
        }
    });
</script>
@endsection