@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/fontawesome.min.js">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>



    <section>
        <div class="row">

            <form style="width:100%" class="form-horizontal" action="{{ url('edit_store') }}"  method="post"   enctype="multipart/form-data"   >

                <input name="id" value="@if(!empty($productList->id)){{ \Illuminate\Support\Facades\Crypt::encrypt($productList->id)  }}@endif" type="hidden">
                <input name="_token" value="{{ csrf_token() }}" type="hidden">

                <div class="col-md-10">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="">Product Name</label>
                                <input type="text" name="product_name" value="@if(!empty($productList->sku)){{$productList->sku}}@endif" placeholder="Product Name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Product Title</label>
                                <input type="text" value="@if(!empty($productList->title)){{$productList->title}}@endif" name="product_title" placeholder="Product Title" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Description</label>
                                <textarea name="description"  id="description" cols="30" rows="4" class="form-control">@if(!empty($productList->description)){{$productList->description}}@endif</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                        </div>
                        <div class="card-body border">
                            <input type="file" name="product_image" placeholder="Product Image" class="form-control">
                            @if(!empty($productList->thumbnail)){{$productList->thumbnail}}@endif
                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="card shadow mb-4">

                        @php
                            $product_variant_price = \App\Models\ProductVariantPrice::where('product_id', $productList->id)->get();
                        @endphp

                        <div class="card-header text-uppercase">Preview</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" >
                                    <thead>
                                    <tr>
                                        <td>Variant</td>
                                        <td>Price</td>
                                        <td>Stock</td>
                                    </tr>
                                    </thead>
                                    <tbody id="preview_input">
                                    @if(!empty($product_variant_price))
                                        @foreach($product_variant_price as $product_variant_p)
                                            <tr>
                                                <td>@if(!empty($product_variant_p->product_variant_one)){{$product_variant_p->product_variant_one}}@endif </td>

                                                <td>
                                                    <input type="text" class="form-control" value="@if(!empty($product_variant_p->price)){{$product_variant_p->price}}@endif" name="price['{{$product_variant_p->product_variant_one}}']">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" value="@if(!empty($product_variant_p->stock)){{$product_variant_p->stock}}@endif" name="stock['{{$product_variant_p->product_variant_one}}']">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody >
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-10">
                    <button type="submit" class="btn btn-lg btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
                </div>

            </form>
        </div>


    </section>


    <script>
        function selectTagging() {
            $(".js-example-tokenizer").select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });
            $('.selectTag').select2({
                createTag: function (params) {
                    var term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term,
                        text: term,
                        newTag: true // add additional parameters
                    }
                }
            });
            $('.selectTag').select2({
                createTag: function (params) {
                    // Don't offset to create a tag if there is no @ symbol
                    if (params.term.indexOf('@') === -1) {
                        // Return null to disable tag creation
                        return null;
                    }

                    return {
                        id: params.term,
                        text: params.term
                    }
                }
            });

            $('.selectTag').select2({
                insertTag: function (data, tag) {
                    // Insert the tag at the end of the results
                    data.push(tag);
                }
            });
        }


        $(document).ready(function(){
            var product_color_code =['red', 'green'];
            var product_size_code =['xl', 'sm'];

            selectTagging();





            $('#product_color').on('change', function(){
                var product_color = $('#product_color').val();
                var product_size = $('#product_size').val();

                var color_len = product_color.length;
                var size_len = product_size.length;


                var preview_input;


                if(color_len==0 && size_len==0){
                    preview_input ='';

                }else if(color_len==0){
                    preview_input ='';

                    product_size.forEach (function (current_size) {

                        preview_input = preview_input+ ' <tr> \n'+


                            '     <td>' + current_size  +' </td> \n'+
                            '   <td> \n'+
                            '         <input type="text" class="form-control" name="price['+current_size+']"  > \n'+
                            '    </td> \n'+
                            '    <td> \n'+
                            '        <input type="text" class="form-control" name="stock['+current_size+']"> \n'+
                            '     </td> \n'+

                            '  </tr>';
                    });

                }else if(size_len==0){
                    product_color.forEach (function (current_color) {



                        preview_input = preview_input+ ' <tr> \n'+


                            '     <td>'+ current_color  +' </td> \n'+
                            '   <td> \n'+
                            '         <input type="text" class="form-control" name="price['+current_color+']"> \n'+
                            '    </td> \n'+
                            '    <td> \n'+
                            '        <input type="text" class="form-control" name="stock['+current_color+']"> \n'+
                            '     </td> \n'+

                            '  </tr>';

                    });
                }else{
                    preview_input ='';
                    product_color.forEach (function (current_color) {

                        product_size.forEach (function (current_size) {

                            preview_input = preview_input+ ' <tr> \n'+


                                '     <td>'+ current_color +'/' + current_size  +' </td> \n'+
                                '   <td> \n'+
                                '         <input type="text" class="form-control" name="price['+current_color+']['+current_size+']"> \n'+
                                '    </td> \n'+
                                '    <td> \n'+
                                '        <input type="text" class="form-control" name="stock['+current_color+']['+current_size+']"  > \n'+
                                '     </td> \n'+

                                '  </tr>';
                        });
                    });
                }



                // console.log(product_color);
                // console.log(color_len);
                // console.log(product_size);
                // console.log(size_len);
                $('#preview_input').html(preview_input);

            });

            $('#product_size').on('change', function(){
                var product_color = $('#product_color').val();
                var product_size = $('#product_size').val();

                var color_len = product_color.length;
                var size_len = product_size.length;


                var preview_input;


                if(color_len==0 && size_len==0){
                    preview_input ='';

                }else if(color_len==0){
                    preview_input ='';

                    product_size.forEach (function (current_size) {

                        preview_input = preview_input+ ' <tr> \n'+


                            '     <td>' + current_size  +' </td> \n'+
                            '   <td> \n'+
                            '         <input type="text" class="form-control" name="price['+current_size+']"  > \n'+
                            '    </td> \n'+
                            '    <td> \n'+
                            '        <input type="text" class="form-control" name="stock['+current_size+']"> \n'+
                            '     </td> \n'+

                            '  </tr>';
                    });

                }else if(size_len==0){
                    product_color.forEach (function (current_color) {



                        preview_input = preview_input+ ' <tr> \n'+


                            '     <td>'+ current_color  +' </td> \n'+
                            '   <td> \n'+
                            '         <input type="text" class="form-control" name="price['+current_color+']"> \n'+
                            '    </td> \n'+
                            '    <td> \n'+
                            '        <input type="text" class="form-control" name="stock['+current_color+']"> \n'+
                            '     </td> \n'+

                            '  </tr>';

                    });
                }else{
                    preview_input ='';
                    product_color.forEach (function (current_color) {

                        product_size.forEach (function (current_size) {

                            preview_input = preview_input+ ' <tr> \n'+


                                '     <td>'+ current_color +'/' + current_size  +' </td> \n'+
                                '   <td> \n'+
                                '         <input type="text" class="form-control" name="price['+current_color+']['+current_size+']"> \n'+
                                '    </td> \n'+
                                '    <td> \n'+
                                '        <input type="text" class="form-control" name="stock['+current_color+']['+current_size+']"  > \n'+
                                '     </td> \n'+

                                '  </tr>';
                        });
                    });
                }



                // console.log(product_color);
                // console.log(color_len);
                // console.log(product_size);
                // console.log(size_len);
                $('#preview_input').html(preview_input);

            });



        });

    </script>


@endsection
