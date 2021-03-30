<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Encryption\DecryptException;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $data['productLists'] = Product::all();
        return view('products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();


        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function search(Request $request)
    {

        $query = Product::query();


        if (!empty($request->title)){
            $query->where('title',$request->title);
        }

        if (!empty($request->date)){
            $query->whereDate('products.created_at',$request->date);
        }

        if (!empty($request->price_from)){
            $query->where('price','>=',$request->price_from);
        }

        if (!empty($request->price_to)){
            $query->where('price','<=',$request->price_to);
        }

        if (!empty($request->product_color)){
            $query->where('product_variant_one', 'like binary', '%' .$request->product_color . '%');
        }


        $data['productLists'] = $query->leftJoin('product_variant_prices','product_variant_prices.product_id','products.id')
            ->get(['*','products.id as products','products.created_at']);

        return view('products.index', $data);

    }

    public function store(Request $request)
    {
       //dd($request->all());
        try{

        $product_name = $request->product_name;
        $product_title = $request->product_title;
        $description = $request->description;
        $product_color = $request->product_color;
        $product_size = $request->product_size;

        $len_pcolor=0;
        if(!empty($product_color)){
            $len_pcolor = sizeof($product_color);
        }

        $len_psize=0;
        if(!empty($product_size)){
            $len_psize = sizeof($product_size);
        }


        $price = $request->price;
        $stock = $request->stock;

        $product_image  = $request->file('product_image');
            $image_name='';
            $image_path='';
        if(!empty($product_image)){
            $image_name = $product_image->getClientOriginalName();
            $image_path = public_path() .'/uploads/'.$image_name;
            $product_image->move(public_path() .'/uploads/', $image_name);
        }


        $tb_product = new Product();
        $tb_product_image = new ProductImage();
        $tb_variant = new Variant();
        $tb_product_variant = new ProductVariant();


        $tb_product->sku  = $product_name;
        $tb_product->title = $product_title;
        $tb_product->description = $description;
        $tb_product->save();

        $tb_product_image->product_id = $tb_product->id;
        $tb_product_image->thumbnail = $image_name;
        $tb_product_image->file_path = $image_path;
        $tb_product_image->save();



        if($len_pcolor==0 && $len_psize==0){

        }elseif ($len_psize==0){

            foreach ($price as $color_c =>$value1){

                $tb_product_variant_price = new ProductVariantPrice();
                $tb_product_variant_price->product_id  = $tb_product->id;
                $tb_product_variant_price->product_variant_one = $color_c;
                $tb_product_variant_price->price  = $price[$color_c];
                $tb_product_variant_price->stock = $stock[$color_c];
                $tb_product_variant_price->save();

            }
        }
        elseif ($len_pcolor==0){

            foreach ($price as $color_c =>$value1){
                $tb_product_variant_price = new ProductVariantPrice();
                $tb_product_variant_price->product_id  = $tb_product->id;
                $tb_product_variant_price->product_variant_one = $color_c;
                $tb_product_variant_price->price  = $price[$color_c];
                $tb_product_variant_price->stock = $stock[$color_c];
                $tb_product_variant_price->save();
            }
        }
        else{
            foreach ($price as $color_c =>$value1){

                foreach ($value1 as $color_s =>$value2 ){
                    $tb_product_variant_price = new ProductVariantPrice();
                    $tb_product_variant_price->product_id  = $tb_product->id;
                    $tb_product_variant_price->product_variant_one = $color_c.'/'.$color_s;

                    $tb_product_variant_price->price  = $price[$color_c][$color_s];
                   $tb_product_variant_price->stock = $stock[$color_c][$color_s];
                    $tb_product_variant_price->save();
                }
            }
        }




         return redirect('product/create')->with('success_message', 'Successfully Saved');


            } catch(\FatalError $e){

        return redirect('product/create')->with('error_message', $e->getMessage());

        } catch(\Exception $e){

            return redirect('product/create')->with('error_message', $e->getMessage());

        } catch (\Throwable  $e) {

            return redirect('product/create')->with('error_message', $e->getMessage());
        }

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $variants = Variant::all();

        try {
            $id= Crypt::decrypt($id);

        }catch (DecryptException $e){
            return  redirect(URL::previous());
        }

        $data['productList']= Product::where('products.id', $id)
            ->leftjoin('product_images', 'product_images.product_id', 'products.id')
            ->first(['*', 'products.id as id']);

        return view('products.edit', $data);
    }


    public function edit_store(Request $request)
    {

        try {
            $id= Crypt::decrypt($request->id);
        }catch (DecryptException $e){
            return  redirect(URL::previous());
        }

        try{


            $product_name = $request->product_name;
            $product_title = $request->product_title;
            $description = $request->description;



            $price = $request->price;
            $stock = $request->stock;




            $tb_product =  Product::where('id', $id)->first();
            $tb_product_image =  ProductImage::where('product_id', $id)->first();
            $tb_variant = new Variant();
            $tb_product_variant = new ProductVariant();


            $tb_product->sku  = $product_name;
            $tb_product->title = $product_title;
            $tb_product->description = $description;
            $tb_product->save();

            $product_image  = $request->file('product_image');
            $image_name='';
            $image_path='';
            if(!empty($product_image)){
                $image_name = $product_image->getClientOriginalName();
                $image_path = public_path() .'/uploads/'.$image_name;
                $product_image->move(public_path() .'/uploads/', $image_name);
                $tb_product_image->thumbnail = $image_name;
                $tb_product_image->file_path = $image_path;
                $tb_product_image->save();
            }

            if(!empty($price)){

                foreach ($price as $color_c =>$value1){
                    $color_ccc = str_replace("'", "", $color_c);
                    $tb_product_variant_price =  ProductVariantPrice::where([['product_id', $id], ['product_variant_one', $color_ccc]])->first();
                        if(!empty($tb_product_variant_price)){
                            $tb_product_variant_price->price  = $price[$color_c];
                            $tb_product_variant_price->stock = $stock[$color_c];

                            $tb_product_variant_price->save();
                        }
                }
            }else{
                foreach ($stock as $color_c =>$value1){
                    $color_ccc = str_replace("'", "", $color_c);
                    if(!empty($tb_product_variant_price)) {
                        $tb_product_variant_price = ProductVariantPrice::where([['product_id', $id], ['product_variant_one', $color_ccc]])->first();
                        $tb_product_variant_price->price = $price[$color_c];
                        $tb_product_variant_price->stock = $stock[$color_c];
                        $tb_product_variant_price->save();
                    }
                }
            }






            return redirect('product')->with('success_message', 'Successfully Updated');


        } catch(\FatalError $e){

            return redirect('product')->with('error_message', $e->getMessage());

        } catch(\Exception $e){

            return redirect('product')->with('error_message', $e->getMessage());

        } catch (\Throwable  $e) {

            return redirect('product')->with('error_message', $e->getMessage());
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
