@extends('layouts.front_layout.front_layout')
@section('content')
<div class="span9">
				<div class="well well-small">
					<h4>Talha Products <small class="pull-right">{{ $featuredItemCount }} featured products</small></h4>
					<div class="row-fluid">
						<div id="featured" class="carousel slide">
							<div class="carousel-inner">
								@foreach($featuredItemsChunk as $key => $featuredItem)
								<div class="item @if($key==0) active @endif">
									<ul class="thumbnails">
										@foreach($featuredItem as $item)
										<li class="span3">
											<div class="thumbnail">
												<i class="tag"></i>
												<a href="product_details.html">
													<?php $product_image_path = 'images/product_images/small/'.$item['main_image']; ?>
													@if(!empty($item['main_image']) && file_exists($product_image_path))
													<img src="{{ asset($product_image_path) }}" alt="">
													@else
													<img src="{{ asset('images/product_images/small/no-image.png') }}" alt="">
													@endif
												</a>
												<div class="caption">
													<h5>{{ $item['product_name'] }}</h5>
													<h4><a class="btn" href="product_details.html">VIEW</a> <span class="pull-right">${{ $item['product_price'] }}</span></h4>
												</div>
											</div>
										</li>
										@endforeach
									</ul>
								</div>
								@endforeach
							</div>
							<!-- <a class="left carousel-control" href="#featured" data-slide="prev">‹</a>
							<a class="right carousel-control" href="#featured" data-slide="next">›</a> -->
						</div>
					</div>
				</div>
				<h4>Latest Products </h4>
				<ul class="thumbnails">
					@foreach($newProducts as $product)
					<li class="span3">
						<div class="thumbnail">
							<a  href="product_details.html">
								<?php $product_image_path = 'images/product_images/small/'.$product['main_image']; ?>
								@if(!empty($product['main_image']) && file_exists($product_image_path))
									<img width="150px" src="{{ asset($product_image_path) }}" alt="">
								@else
									<img width="150px" src="{{ asset('images/product_images/small/no-image.png') }}" alt="">
								@endif
							</a>
							<div class="caption">
								<h5>{{ $product['product_name'] }}</h5>
								<p>
									{{ $product['product_code'] }} ({{ $product['product_color'] }})
								</p>
								
								<h4 style="text-align:center"><a class="btn" href="product_details.html"> <i class="fa fa-search-plus" aria-hidden="true"></i></a> <a class="btn" href="#">Add to <i class="fa fa-shopping-cart" aria-hidden="true"></i></a> <a class="btn btn-primary" href="#">Rs.1000</a></h4>
							</div>
						</div>
					</li>
					@endforeach
				</ul>
			</div>
@endsection