@extends('layouts.admin_layout.admin_layout');
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Catelogues</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Products</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <!-- /.card -->
        @if(Session::has('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top:10px;">
          {{ Session::get('success_message') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Products</h3>
            <a href="{{ url('admin/add-edit-product') }}" class="btn btn-success" style="float:right;">+Add Product</a>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="products" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Product Name</th>
                  <th>Product Code</th>
                  <th>Product Color</th>
                  <th>Product Image</th>
                  <th>Category</th>
                  <th>Section</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($products as $product)
                <tr>
                  <td>{{ $product->id }}</td>
                  <td>{{ $product->product_name }}</td>
                  <td>{{ $product->product_code }}</td>
                  <td>{{ $product->product_color }}</td>
                  <td>
                    <?php $product_image_path = "images/product_images/small/".$product->main_image; ?>
                    @if(!empty($product->main_image) && file_exists($product_image_path))
                    <img style="width:70px;" src="{{ asset('images/product_images/small/'.$product->main_image) }}">
                    @else
                    <img style="width:70px;" src="{{ asset('images/product_images/small/no-image.png') }}">
                    @endif
                  </td>
                  <td>{{ $product->category->category_name }}</td>
                  <td>{{ $product->section->name }}</td>
                  <td>
                  	@if($product->status==1)
                  	<a class="updateProductStatus" id="product-{{ $product->id }}" product_id="{{ $product->id }}" href="javascript:void(0)" ><i class="fas fa-toggle-on" status="Active"></i></a>
                    @else
                    <a class="updateProductStatus" id="product-{{ $product->id }}" product_id="{{ $product->id }}" href="javascript:void(0)" ><i class="fas fa-toggle-off" status="Inactive"></i></a>
                    @endif
                  </td>
                  <td>
                    <a title="Add/Edit Attributes" href="{{ url('admin/add-attributes/'.$product->id) }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    &nbsp;&nbsp;
                    <a title="Add Images" href="{{ url('admin/add-images/'.$product->id) }}"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
                    &nbsp;&nbsp;
                    <a title="Edit Product" href="{{ url('admin/add-edit-product/'.$product->id) }}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                    &nbsp;&nbsp;
                    <a title="Delete Product" href="javascript:void(0)" class="confirmDelete" record="product" recordid="{{ $product->id }}" <?php /* href="{{ url('admin/delete-category/'.$category->id) }}" */ ?>><i class="fa fa-trash" aria-hidden="true"></i></a>
                  </td>
                </tr> 
                @endforeach               
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@endsection