<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductStoreRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //All product
        $products =Product::all();
        //return json response
        return response()->json([
            'products'=>$products
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
      try {
        $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
            //Create Product
            Product ::create([

                'name' => $request -> name,
                'image'=>$imageName,
                'description'=> $request -> description
            ]);
            //Save Image in storage folder
            Storage::disk('public')->put($imageName , file_get_contents( $request->image));
            // Return JSON Response
            return  response()->json(['message'=> "product has been added successfully"],200);
      }  catch(\Exception){
        return response()->json(['message' => "Erreur lors de l'ajout d'une image"],500);

      }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //product details 
        $product = Product::find($id);
        if(!$product){
            return response()->json(["message"=>"No product found!"],404);
        }
        // Return JSON Response
        return response()->json([
            'product' => $product
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(ProductStoreRequest $request,  $id)
    // {
    //   try {
    //     //Find product 
    //     $product = Product::find($id);
    //     //Check if product exists
    //     if (!$product) {
    //         return response()->json(["message" => "Ce produit n'existe pas."], 404);
    //   }
    //   echo "request : $request->name";
    //   echo "description : $request->description";
    //   $product->name=$request->name;
    //   $product->description=$request->description;

    // }catch(Exception $e){

    //     // Return json response
    //     return response() ->json( [
    //       'message'=>'Something went really wrong!'
    //     ],500);  
    // }}
    public function update(ProductStoreRequest $request, $id)
    {
        try {
            // Find product
            $product = Product::find($id);
            if(!$product){
              return response()->json([
                'message'=>'Product Not Found.'
              ],404);
            }
      
            //echo "request : $request->image";
            $product->name = $request->name;
            $product->description = $request->description;
      
            if($request->image) {
 
                // Public storage
                $storage = Storage::disk('public');
      
                // Old iamge delete
                if($storage->exists($product->image))
                    $storage->delete($product->image);
      
                // Image name
                $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
                $product->image = $imageName;
      
                // Image save in public folder
                $storage->put($imageName, file_get_contents($request->image));
            }
      
            // Update Product
            $product->save();
      
            // Return Json Response
            return response()->json([
                'message' => "Product successfully updated."
            ],200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Details 
        $product = Product::find($id);
        if(!$product){
            return response()->json(['message' => 'Product Not Found' ] ,404);
        }
        //Public storage 
        $storage = Storage::disk('public');
        //Image delete 
        if($storage->exists($product->image))
        $storage->delete($product->image);
        
        //Remove product data   
        $product->delete();
        return response()->json(['message'=>"Product Successfully Deleted"],200);  }
}
