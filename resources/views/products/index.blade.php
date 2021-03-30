@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/fontawesome.min.js">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>

    @if (Session::has('success_message'))
        <div class="alert alert-success m-t-sm">{{ Session::get('success_message') }}</div>
    @endif
    @if (Session::has('error_message'))
        <div class="alert alert-danger m-t-sm"><?php echo html_entity_decode(Session::get('error_message')); ?></div>
    @endif
    @if (Session::has('warning_message'))
        <div class="alert alert-warning m-t-sm">{{ Session::get('warning_message') }}</div>
    @endif

    <div class="card">
        <form  action="{{ url('search') }}" method="get" class="card-header">
            <input name="_token" value="{{ csrf_token() }}" type="hidden">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select class="form-control  selectTag"  id="product_color" name="product_color">
                        <option value="red"> red </option>
                        <option value="green"> green </option>

                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response" >
                <table class="table table-striped table-bordered table-hover" id="sample_1">
                    <thead class="bordered-darkorange">
                        <tr>
                            <th>No</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Variant</th>
                            <th width="150px">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                      <?php $i=0; ?>
                      @if(!empty($productLists))
                          @foreach($productLists as $productList)
                            <tr>
                                <td>{{++ $i}}</td>
                                <td>{{$productList->title}} <br> Created at : {{\Carbon\Carbon::parse($productList->created_at)->diffForhumans()}} </td>
                                <td>{{$productList->description}}</td>

                                @php
                                    $product_variant_prices = \App\Models\ProductVariantPrice::where('product_id', $productList->id)->get();

                                @endphp

                                <td>
                                    @if(!empty($product_variant_prices))
                                        @foreach($product_variant_prices as $product_variant_price)
                                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                                <dt class="col-sm-3 pb-0">
                                                    {{$product_variant_price->product_variant_one}}
                                                </dt>
                                                <dd class="col-sm-9">
                                                    <dl class="row mb-0">
                                                        <dt class="col-sm-4 pb-0">Price : {{ number_format($product_variant_price->price,  2) }}</dt>
                                                        <dd class="col-sm-8 pb-0">InStock : {{ number_format($product_variant_price->stock, 2) }}</dd>
                                                    </dl>
                                                </dd>
                                            </dl>
                                            {{--                                <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>--}}

                                        @endforeach
                                    @endif

                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ url('edit', \Illuminate\Support\Facades\Crypt::encrypt($productList->id) ) }}" class="btn btn-success">Edit</a>
                                    </div>
                                </td>
                            </tr>

                          @endforeach
                      @endif

                    </tbody>

                </table>
            </div>

        </div>
    </div>

    <link href="{{asset('datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('datatables/plugins/bootstrap/datatables.bootstrap.css')}}" rel="stylesheet" type="text/css" />
    <script src="{{asset('datatables/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('datatables/plugins/bootstrap/datatables.bootstrap.js')}}" type="text/javascript"></script>

    <script>
        $('#sample_1').DataTable({
            "iDisplayLength": 10,
            "aLengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "all"]
            ]
        });
    </script>

@endsection
