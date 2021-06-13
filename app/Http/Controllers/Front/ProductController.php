<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\ProductsAttribute;
use App\Product;
use Session;
use App\Cart;
use Auth;

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
		$productDetails = Product::with(['category','brand','attributes'=>function($query){
			$query->where('status',1);
		},'images'])->find($id)->toArray();
		$total_stock = ProductsAttribute::where('product_id',$id)->sum('stock');
		$relatedProducts = Product::where('category_id',$productDetails['category']['id'])->where('id','!=',$id)->limit(3)->inRandomOrder()->get()->toArray();

		return view('front.products.detail')->with(compact('productDetails','total_stock','relatedProducts'));
	}

	public function getProductPrice(Request $request){
		if($request->ajax()){
			$data = $request->all();
			// echo "<pre>"; print_r($data); die;
			$getDiscountedAttrPrice = Product::getDiscountedAttrPrice($data['product_id'],$data['size']);
			return $getDiscountedAttrPrice;
		}
	}

	public function addtocart(Request $request){
		if($request->isMethod('post')){
			$data = $request->all();
			// echo "<pre>"; print_r($data); die;
			$getProductStock = ProductsAttribute::where(['product_id'=>$data['product_id'],'size'=>$data['size']])->first()->toArray();
			// return $getProductStock['stock']; die;
			if($getProductStock['stock'] < $data['quantity']){
				$message = "Required Quantity is not available!";
				session::flash('error_message',$message);
				return redirect()->back();
			}

			// Generate Session id if not exists
			$session_id = Session::get('session_id');
			if(empty($session_id)){
				$session_id = Session::getId();
				Session::put('session_id',$session_id);
			}
			// Check Product if already exists in Cart
			if(Auth::check()){
				$countProducts = Cart::where(['product_id'=>$data['product_id'],'size'=>$data['size'],'user_id'=>Auth::user()->id])->count();
			}else{
				$countProducts = Cart::where(['product_id'=>$data['product_id'],'size'=>$data['size'],'session_id'=>Session::get('session_id')])->count();
			}
			if($countProducts>0){
				$message = "Product already exists in Cart!";
				session::flash('error_message',$message);
				return redirect()->back();
			}

			// Save Product in Cart
			// Cart::insert(['section_id'=>$session_id,'product_id'=>$data['product_id'],'size'=>$data['size'],'quantity'=>$data['quantity']]);
			$cart = new Cart;
			$cart->session_id = $session_id;
			$cart->product_id = $data['product_id'];
			$cart->size = $data['size'];
			$cart->quantity = $data['quantity'];
			$cart->save();
			$message = "Product has been added in Cart!";
			session::flash('success_message',$message);
			return redirect('cart');
		}
	}

	public function cart(){
		$userCartItems = Cart::userCartItems();
		// echo "<pre>"; print_r($userCartItems); die;
		return view('front.products.cart')->with(compact('userCartItems'));
	}

	public function updateCartItemQty(Request $request){
		if($request->ajax()){
			$data = $request->all();
			// echo "<pre>"; print_r($data); die;
			$cartDetails = Cart::find($data['cartid']);
			$availableStock = ProductsAttribute::select('stock')->where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size']])->first()->toArray();
			// echo "Available Stock: ".$availableStock['stock']; die;
			// Check Stock is available or not
			if($data['qty']>$availableStock['stock']){
				$userCartItems = Cart::userCartItems();
				return response()->json([
					'status'=>false,
					'message'=>'Product Stock is not available',
					'view'=>(String)View::make('front.products.cart_item')->with(compact('userCartItems'))
				]);
			}

			// Check size available
			// $availableSize = ProductsAttribute::where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size'],'status'=>1])->count();
			// if($availableSize==0){
			// 	$userCartItems = Cart::userCartItems();
			// 	return response()->json([
			// 		'status'=>false,
			// 		'message'=>'Product Size is not available',
			// 		'view'=>(String)View::make('front.products.cart_item')->with(compact('userCartItems'))
			// 	]);
			// }

			Cart::where('id',$data['cartid'])->update(['quantity'=>$data['qty']]);
			$userCartItems = Cart::userCartItems();
			return response()->json([
				'status'=>true,
				'view'=>(String)View::make('front.products.cart_item')->with(compact('userCartItems'))
			]);
		}
	}

	public function deleteCartItem(Request $request){
		if($request->ajax()){
			$data = $request->all();
			Cart::where('id',$data['cartid'])->delete();
			$userCartItems = Cart::userCartItems();
			return response()->json([
				'message'=>'Cart Item Deleted Successfully!',
				'view'=>(String)View::make('front.products.cart_item')->with(compact('userCartItems'))
			]);
		}
	}
}
