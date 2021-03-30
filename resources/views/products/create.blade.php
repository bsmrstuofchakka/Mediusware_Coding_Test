@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/fontawesome.min.js">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Product</h1>
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

    <section>
        <div class="row">

            <form style="width:100%" class="form-horizontal" action="{{ url('store') }}"  method="post"   enctype="multipart/form-data"   >

                <input name="_token" value="{{ csrf_token() }}" type="hidden">

                <div class="col-md-10">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="">Product Name</label>
                                <input type="text" name="product_name" placeholder="Product Name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Product Title</label>
                                <input type="text" name="product_title" placeholder="Product Title" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Description</label>
                                <textarea name="description" id="description" cols="30" rows="4" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                        </div>
                        <div class="card-body border">
                            <input type="file" name="product_image" placeholder="Product Image" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Variants</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Color</label>
                                        <select class="form-control  selectTag" multiple id="product_color" name="product_color[]">
                                            <option value="red"> red </option>
                                            <option value="green"> green </option>

                                        </select>
                                    </div>
                                </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Size</label>
                                        <select class="form-control  selectTag" multiple id="product_size" name="product_size[]">
                                            <option value="xl"> xl </option>
                                            <option value="sm"> sm </option>

                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>


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
                                    <tr>
                                        <td> </td>
                                        <td>
                                            <input type="text" disabled class="form-control" name="price">
                                        </td>
                                        <td>
                                            <input type="text" disabled class="form-control" name="stock">
                                        </td>
                                    </tr>
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
