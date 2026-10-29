<?php
namespace App\Http\Controllers;
use App\Http\Requests\ProductRequest;
use App\Services\ProductStore;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
class ProductController extends Controller {
 public function index(ProductStore $store): View { return view('products.index',['products'=>$store->all()]); }
 public function store(ProductRequest $request, ProductStore $store): JsonResponse { return response()->json(['message'=>'Product added.','product'=>$store->create($request->validated())],201); }
 public function update(ProductRequest $request,string $id,ProductStore $store): JsonResponse { $p=$store->update($id,$request->validated()); return $p?response()->json(['message'=>'Product updated.','product'=>$p]):response()->json(['message'=>'Product not found.'],404); }
}
