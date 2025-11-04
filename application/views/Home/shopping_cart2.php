
<div class="container-fluid" style="padding: 100px 0 50px 0" >
	<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-10">
			<table class="table table-bordered">
				<thead style="background-color: #e0e0e0;">
				<th >نام محصول</th>
				<th >مشخصات</th>
				<th >تعداد</th>
				<th >قیمت واحد</th>
				<th >مجموع</th>
				<th >درصد تخفیف</th>
				<th >مبلغ تخفیف</th>
				<th >قیمت نهایی</th>
				<th  style="text-align: center">عملیات</th>
				</thead>
				<tbody id="cart_ajax"></tbody>
				<?php $t=0; foreach ($shopping_cart as $cart){
					if ($cart->user_id == $this->session->userdata('user_id')){ ?>
						<tbody>
						<tr id="romove_<?php echo $cart->id;?>" class="test" id_attr="<?php echo $cart->id_attr?>">
							<td style="text-wrap: normal;width: 15%"" id="name_<?php echo $cart->id ?>"><?php echo $cart->name;?></td>
							<td style="font-size: 10px;text-wrap: normal;width: 15%" id="attributes" ><?php
						foreach ($product_attributes as $pt) {if($pt->id==$cart->id_attr){?>
							<span style="display: <?php if($pt->model!=0){echo 'inline';}else{echo 'none';}?>">مدل:</span>
							<span id="model_<?php echo $pt->id?>" style="display: <?php if($pt->model!=0){echo 'inline';}else{echo 'none';}?>"><?php echo $pt->model ?></span>
							<span style="display: <?php if($pt->jens!=0){echo 'inline';}else{echo 'none';}?>">جنس:</span>
							<span id="jens_<?php echo $pt->id?>" style="display: <?php if($pt->jens!=0){echo 'inline';}else{echo 'none';}?>"><?php echo $pt->jens ?></span>
							<span style="display: <?php if($pt->size!=0){echo 'inline';}else{echo 'none';}?>">سایز:</span>
							<span id="size_<?php echo $pt->id?>" style="display: <?php if($pt->size!=0){echo 'inline';}else{echo 'none';}?>"><?php echo $pt->size ?></span>
							<span style="display: <?php if($pt->brand!=0){echo 'inline';}else{echo 'none';}?>">برند:</span>
							<span id="brand_<?php echo $pt->id?>" style="display: <?php if($pt->brand!=0){echo 'inline';}else{echo 'none';}?>"><?php echo $pt->brand ?></span>
							<span style="display: <?php if($pt->color!=0){echo 'inline';}else{echo 'none';}?>">رنگ:</span>
							<span id="color_<?php echo $pt->id?>" style="display: <?php if($pt->color!=0){echo 'inline';}else{echo 'none';}?>"><?php echo $pt->color ?></span>
						<?php }} ?>
							</td>
							<td id="qty_<?php echo $cart->id ?>"><?php echo $cart->qty;?></td>
							<td id="price_<?php echo $cart->id;?>"><?php echo number_format($cart->price);?></td>
							<td id="total_price1_<?php echo $cart->id;?>"><?php echo number_format($cart->total_price1);?></td>
							<td id="off_percent_<?php echo $cart->id;?>"><?php echo $cart->off_percent;?>%</td>
							<td id="off_price_<?php echo $cart->id;?>"><?php echo number_format($cart->off_price);?></td>
							<td id="total_<?php echo $cart->id;?>"><?php echo number_format($cart->total_price);?></td>
							<input type="hidden" id="sum" value="<?php echo $t += $cart->total_price; ?>">
							<td align="center">
								<button type="button" name="remove" class="btn btn-danger btn-xs "
										id_cart="<?php echo $cart->id ?>" id="remove">
									<i class="fa fa-trash" style="font-size: 30px" aria-hidden="true"></i>
								</button>
								<button type="button" name="edit_qty" class="btn btn-success btn-xs "
										id_cart="<?php echo $cart->id ?>" id="edit_qty">
									<i class="fa fa-edit" style="font-size: 30px" aria-hidden="true"></i>
								</button>

							</td>
						</tr>
						</tbody>

					<?php }} ?>
				<tr>
					<td colspan="7" align="right">جمع کل</td>
					<td>
						<span id="sum2"><?php echo number_format($t);?></span>&nbsp<span>تومان</span>
					</td>
				</tr>
			</table>
			<br>
			<a href="<?php echo base_url('home/final_shopping_cart')?>">
				<button type="button"  class="btn btn-default pull-right">ادامه خرید</button>
			</a>
		</div>
		<div class="col-md-1"></div>
	</div>
</div>

<div class="modal fade" id="show_edit" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close mdl_btn" data-dismiss="modal">
					<i class="fa fa-close"></i>
				</button>
			</div>
			<div class="modal-body">
				<input autocomplete="off" type="hidden" id="id_modal">
				<input class="form-control positive" type="number" min="1" autocomplete="off" id="qty_modal"><br>
			</div>
			<div class="modal-footer">
				<button id="add_qty" class="btn btn-success mdl_btn" data-dismiss="modal">تایید </button>

			</div>

		</div>
	</div>
</div>

<script>
	$('.positive').keydown( function(e) {
		if(!((e.keyCode > 95 && e.keyCode < 106)
			|| (e.keyCode > 47 && e.keyCode < 58)
			|| e.keyCode == 8)) {
			return false;
		}
	});

	$(document).ready(function () {

		var final_price = $('#sum2').text().replace(/,/g, '');
		$.post('<?php echo base_url()?>home/totalprice', {'final_price': final_price,},
		);
		console.log(final_price);

		$('.test').each(function () {
			var id = $(this).attr('id_attr');


			console.log(id);

			var model = $("#model_"+id).text();
			var jens = $("#jens_"+id).text();
			var size = $("#size_"+id).text();
			var brand = $("#brand_"+id).text();
			var color = $("#color_"+id).text();
			$.post('<?php echo base_url();?>home/hhh',
				{
					'model': model,
					'jens': jens,
					'size': size,
					'brand': brand,
					'color': color,

				},
				function (data2) {
					var obj = JSON.parse(data2);
					// console.log(obj.model,obj.jens,obj.size,obj.brand,obj.color);
					$('#model_'+id).text(obj.model+'/');
					$('#jens_'+id).text(obj.jens+'/');
					$('#size_'+id).text(obj.size+'/');
					$('#brand_'+id).text(obj.brand+'/');
					$('#color_'+id).text(obj.color+'');
				});
		});
	});
	$(document).on('click', '#edit_qty', function(e){
		var id=$(this).attr("id_cart");
		var qty=$("#qty_"+id).text();

		$("#id_modal").val(id);
		$("#qty_modal").val(qty);
		$("#show_edit").modal();
	});
	$(document).on('click', '#add_qty', function(e) {
		var id=$("#id_modal").val();
		var qty=$("#qty_modal").val();
		var sum2=$('#sum2').text().replace(/,/g,'');
		var total=$("#total_"+id).text().replace(/,/g,'');
		if (qty>0){
			$.ajax({
				url: "<?php echo base_url(); ?>home/insert_shopping_cart2",
				method:"POST",
				data:{'id':id, 'qty':qty,},
				dataType: 'json',
				success:
					function(data) {
						$("#qty_"+id).html(data[0]);
						$("#price_"+id).html(parseInt(data[1]).toLocaleString());
						$("#total_price1_"+id).html(parseInt(data[2]).toLocaleString());
						$("#off_percent_"+id).html(data[3]+"%");
						$("#off_price_"+id).html(parseInt(data[4]).toLocaleString());
						$("#total_"+id).html(parseInt(data[5]).toLocaleString());
						var sum=sum2-total+data[5];
						$("#sum2").html(parseInt(sum).toLocaleString());
						console.log(total)


					}
			});
		}else{
			alert('تعداد باید بزرگتر از 0 باشد')
		}

	});

	$(document).on('click', '#remove', function(e) {
		var id_cart=$(this).attr('id_cart');
			$.ajax({
				url: "<?php echo base_url(); ?>home/delete_cart",
				method:"POST",
				data:{'id_cart': id_cart,},
				success:
						function(data) {
							a = $('#sum2').text().replace(/,/g,'');
							console.log(a);
							final_price = a - $('#total_' + id_cart).text().replace(/,/g,'');
							console.log($('#total_' + id_cart).text().replace(/,/g,''));
							$.ajax({
								url: "<?php echo base_url(); ?>home/totalprice",
								method: "POST",
								data: {'final_price': final_price,},
								success:
										function(data) {
											$('#romove_' + id_cart).remove();
											$('#sum2').text(final_price);
											console.log(final_price)
										}

						})
							;
						}
			});



	});




</script>
