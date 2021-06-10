$(document).ready(function(){
	// $("#sort").on('change',function(){
	// 	this.form.submit();
	// });
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	});

	$("#sort").on('change',function(){
		var sort = $(this).val();
		var fabric = get_filter("fabric");
		var sleeve = get_filter("sleeve");
		var pattern = get_filter("pattern");
		var fit = get_filter("fit");
		var occasion = get_filter("occasion");
		var url = $("#url").val();
		$.ajax({
			url:url,
			method:"post",
			data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
			success:function(data){
				$('.filter_products').html(data);
			}
		})
	});

	$(".fabric").on('click',function(){
		var fabric = get_filter("fabric");
		var sleeve = get_filter("sleeve");
		var pattern = get_filter("pattern");
		var fit = get_filter("fit");
		var occasion = get_filter("occasion");
		var sort = $("#sort option:selected").val();
		var url = $("#url").val();
		$.ajax({
			url:url,
			method:"post",
			data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
			success:function(data){
				$('.filter_products').html(data);
			}
		})
	});

	$(".sleeve").on('click',function(){
		var fabric = get_filter("fabric");
		var sleeve = get_filter("sleeve");
		var pattern = get_filter("pattern");
		var fit = get_filter("fit");
		var occasion = get_filter("occasion");
		var sort = $("#sort option:selected").val();
		var url = $("#url").val();
		$.ajax({
			url:url,
			method:"post",
			data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
			success:function(data){
				$('.filter_products').html(data);
			}
		})
	});

	$(".pattern").on('click',function(){
		var fabric = get_filter("fabric");
		var sleeve = get_filter("sleeve");
		var pattern = get_filter("pattern");
		var fit = get_filter("fit");
		var occasion = get_filter("occasion");
		var sort = $("#sort option:selected").val();
		var url = $("#url").val();
		$.ajax({
			url:url,
			method:"post",
			data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
			success:function(data){
				$('.filter_products').html(data);
			}
		})
	});

	$(".fit").on('click',function(){
		var fabric = get_filter("fabric");
		var sleeve = get_filter("sleeve");
		var pattern = get_filter("pattern");
		var fit = get_filter("fit");
		var occasion = get_filter("occasion");
		var sort = $("#sort option:selected").val();
		var url = $("#url").val();
		$.ajax({
			url:url,
			method:"post",
			data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
			success:function(data){
				$('.filter_products').html(data);
			}
		})
	});

	$(".occasion").on('click',function(){
		var fabric = get_filter("fabric");
		var sleeve = get_filter("sleeve");
		var pattern = get_filter("pattern");
		var fit = get_filter("fit");
		var occasion = get_filter("occasion");
		var sort = $("#sort option:selected").val();
		var url = $("#url").val();
		$.ajax({
			url:url,
			method:"post",
			data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
			success:function(data){
				$('.filter_products').html(data);
			}
		})
	});

	function get_filter(class_name){
		var filter = [];
		$('.'+class_name+':checked').each(function(){
			filter.push($(this).val());
		});
		return filter;
	}

	$("#getPrice").change(function(){
		var size = $(this).val();
		var product_id = $(this).attr("product-id");
		// alert(product_id);
		$.ajax({
			url:'/ecom/public/get-product-price',
			data:{size:size,product_id:product_id},
			type:'post',
			success:function(resp){
				$(".getAttrPrice").html("Tk. "+resp);
			},error:function(){
				alert("Error");
			}
		});
	});

	// active class on listview or blockview
    $(".switcher").on('click',function(){
      var theid = $(this).attr("id");
      // var theproducts = $("ul#products");
      var classNames = $(this).attr('class').split(' ');
      // console.log(classNames);
      if ($(this).hasClass("active btn-primary")) {
        return false;
      } else {
        if (theid == "gridview") {
          $(this).addClass("active btn-primary");
          $("#listview").removeClass("active btn-primary");
          // $("#listview").children("img").attr("src", "images/list-view.png");
          // var theimg = $(this).children("img");
          // theimg.attr("src", "images/grid-view-active.png");
          // theproducts.removeClass("list");
          // theproducts.addClass("grid");
        } else if (theid == "listview") {
          $(this).addClass("active btn-primary");
          $("#gridview").removeClass("active btn-primary");
          // $("#gridview").children("img").attr("src", "images/grid-view.png");
          // var theimg = $(this).children("img");
          // theimg.attr("src", "images/list-view-active.png");
          // theproducts.removeClass("grid")
          // theproducts.addClass("list");
        }
      }
    });
});