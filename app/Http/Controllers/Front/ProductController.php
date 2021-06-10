<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\ProductsAttribute;
use App\Product;

class ProductController extends Controller
{
	public function listing(Request $request){
		Paginator::useBootstrap();
		if($request->ajax()){
			// dd('ok');
			$data = $request->all();
			$url = $data['url'];
			// echo "<pre>"; print_r($data); die;
			$categoryCount = Category::where(['url'=>$url,'status'=>1])->count();
			if($categoryCount > 0){
				$categoryDetails = Category::catDetails($url);
				$categoryProducts = Product::with('brand')->whereIn('category_id',$categoryDetails['catIds'])->where('status',1);
				// If Fabric filter is selected
				if(isset($data['fabric']) && !empty($data['fabric'])){
					$categoryProducts->whereIn('products.fabric',$data['fabric']);
				}
				if(isset($data['sleeve']) && !empty($data['sleeve'])){
					$categoryProducts->whereIn('products.sleeve',$data['sleeve']);
				}
				if(isset($data['pattern']) && !empty($data['pattern'])){
					$categoryProducts->whereIn('products.pattern',$data['pattern']);
				}
				if(isset($data['fit']) && !empty($data['fit'])){
					$categoryProducts->whereIn('products.fit',$data['fit']);
				}
				if(isset($data['occasion']) && !empty($data['occasion'])){
					$categoryProducts->whereIn('products.occasion',$data['occasion']);
				}
    		// If sort option selected by user
				if(isset($data['sort']) && !empty($data['sort'])){
					if($data['sort']=="product_latest"){
						$categoryProducts->orderBy('id','Desc');
					}
					else if($data['sort']=="product_name_a_z"){
						$categoryProducts->orderBy('product_name','Asc');
					}
					else if($data['sort']=="product_name_z_a"){
						$categoryProducts->orderBy('product_name','Desc');
					}
					else if($data['sort']=="price_lowest"){
						$categoryProducts->orderBy('product_price','Asc');
					}
					else if($data['sort']=="price_height"){
						$categoryProducts->orderBy('product_price','Desc');
					}
				}else{
					$categoryProducts->orderBy('id','Desc');
				}
				
				$categoryProducts = $categoryProducts->paginate(6);
    		// echo "<pre>"; print_r($categoryProducts); die;
				return view('front.products.ajax_products_listing')->with(compact('categoryDetails','categoryProducts','url'));
			}else{
				abort(404);
			}
		}else{
			// dd('okkk');
			$url = Route::getFacadeRoot()->current()->uri();
			$categoryCount = Category::where(['url'=>$url,'status'=>1])->count();
			if($categoryCount > 0){
				$categoryDetails = Category::catDetails($url);
				$categoryProducts = Product::with('brand')->whereIn('category_id',$categoryDetails['catIds'])->where('status',1);

				$categoryProducts = $categoryProducts->paginate(6);
				// product Filters
				$productFilters = Product::productFilters();
				$fabricArray = $productFilters['fabricArray'];
				$sleeveArray = $productFilters['sleeveArray'];
				$patternArray = $productFilters['patternArray'];
				$fitArray = $productFilters['fitArray'];
				$occasionArray = $productFilters['occasionArray'];
    		// echo "<pre>"; print_r($categoryProducts); die;
				$page_name = "listing";
				return view('front.products.listing')->with(compact('categoryDetails','categoryProducts','url','fabricArray','sleeveArray','patternArray','fitArray','occasionArray','page_name'));
			}else{
				abort(404);
			}
		}

	}

	public function detail($id){
		$productDetails = Product::with('category','brand','attributes','images')->find($id)->toArray();
		$total_stock = ProductsAttribute::where('product_id',$id)->sum('stock');
		$relatedProducts = Product::where('category_id',$productDetails['category']['id'])->where('id','!=',$id)->limit(3)->inRandomOrder()->get()->toArray();

		return view('front.products.detail')->with(compact('productDetails','total_stock','relatedProducts'));
	}

	public function getProductPrice(Request $request){
		if($request->ajax()){
			$data = $request->all();
			// echo "<pre>"; print_r($data); die;
			$getProductPrice = ProductsAttribute::where(['product_id'=>$data['product_id'],'size'=>$data['size']])->first();
			return $getProductPrice->price;
		}
	}
}
