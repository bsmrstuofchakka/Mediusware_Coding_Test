<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */

    public $product_color_code = ['red','green'];
    public $product_color_size = ['xl','sm'];

    public function index()
    {
        return view('products.index');
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
    public function store(Request $request)
    {
        dd($request->all());

        $product_name = $request->product_name;
        $product_title = $request->product_title;
        $description = $request->description;
        $product_color = $request->product_color;
        $product_size = $request->product_size;
        $len_pcolor = sizeof($product_color);
        $len_psize = sizeof($product_size);
        $price = $request->price;
        $stock = $request->stock;

        $product_image  = $request->file('product_image');
        $image_name = $product_image->getClientOriginalName();
        $image_path = public_path() .'/uploads/'.$image_name;
        $product_image->move(public_path() .'/uploads/', $image_name);

        $tb_product = new Product();
        $tb_product_image = new ProductImage();
        $tb_variant = new Variant();
        $tb_product_variant = new ProductVariant();
        $tb_product_variant_price = new ProductVariantPrice();

        $tb_product->sku  = $product_name;
        $tb_product->title = $product_title;
        $tb_product->description = $description;
        $tb_product->save();

        $tb_product_image->product_id = $tb_product->id;
        $tb_product_image->thumbnail = $image_name;
        $tb_product_image->file_path = $image_path;
        $tb_product_image->save();

        $tb_product_variant_price->product_id  = $tb_product->id;

        if($len_pcolor==0 && $len_psize==0){

        }elseif ($len_psize==0){
            foreach ($price as $color_c){
                $tb_product_variant_price->product_variant_one = $color_c;
                $tb_product_variant_price->price  = $price[$color_c];
                $tb_product_variant_price->stock = $stock[$color_c];
                $tb_product_variant_price->save();

            }
        }
        elseif ($len_pcolor==0){
            foreach ($price as $color_c){

                $tb_product_variant_price->product_variant_one = $color_c;
                $tb_product_variant_price->price  = $price[$color_c];
                $tb_product_variant_price->stock = $stock[$color_c];
                $tb_product_variant_price->save();
            }
        }
        else{
            foreach ($price as $color_c){
                foreach ($color_c as $color_s){
                    $tb_product_variant_price->product_variant_one = $color_c.'/'.$color_s;
                    $tb_product_variant_price->price  = $price[$color_c][$color_s];
                    $tb_product_variant_price->stock = $stock[$color_c][$color_s];
                    $tb_product_variant_price->save();
                }
            }
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
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
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
