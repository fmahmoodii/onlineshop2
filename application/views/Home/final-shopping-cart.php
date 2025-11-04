
<style>
	@media only screen and (max-device-width: 750px) {
		/* موبایل  */
		.div_p{height: 70px;}
		.pay_spn{width: 30%;}
		.msg_h5{width: 100%; text-align: center;
			background-color: #d7e9d7; padding: 20px;}
		.div_info{border-radius: 10px; border: 1px solid #e0dfdf; padding: 20px;}
		.mdl_btn{float: right;outline: unset;}
	}
	@media only screen and (min-device-width: 750px ) {
		/* pc  */
		.div_p{padding-top: 70px;}
		.pay_spn{width: 30%;}
		.msg_h5{width: 100%; text-align: center;
			background-color: #d7e9d7; padding: 20px;}
		.div_info{border-radius: 10px; border: 1px solid #e0dfdf; padding: 20px;}
		.mdl_btn{float: right;outline: unset;}
	}

</style>

<?php if ($this->session->userdata('user_id')){ ?>
<div class="container-fluid div_p" >
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<table class="table table-bordered table-striped">
				<thead>
				<tr>
					<th >نام محصول</th>
					<th >تعداد</th>
					<th >قیمت واحد</th>
					<th >مجموع</th>
					<th >درصد تخفیف</th>
					<th >مبلغ تخفیف</th>
					<th >قیمت نهایی</th>
				</tr>
				</thead>
				<tbody>

				<?php $t=0;foreach ($shopping_cart1 as $cart){
					if ($cart->user_id == $this->session->userdata('user_id')){ ?>
				<tbody>
				<tr>
					<td ><?php echo $cart->name;?></td>
					<td ><?php echo $cart->qty;?></td>
					<td ><?php echo number_format($cart->price);?></td>
					<td ><?php echo number_format($cart->total_price1);?></td>
					<td ><?php echo number_format($cart->off_percent);?>%</td>
					<td ><?php echo number_format($cart->off_price);?></td>
					<td id="total_<?php echo $cart->id;?>"><?php echo number_format($cart->total_price);?></td>
					<input type="hidden" id="sum" value="<?php echo $t += $cart->total_price; ?>">

				</tr>

				</tbody>


				</tbody>

				<?php }
				} ?>
				<tr>
					<td colspan="6" align="right">جمع کل</td>
					<td>
						<span id="sum2"><?php echo number_format($t);?></span>&nbsp;<span>تومان</span>
					</td>

				</tr>
			</table>
			<br>
			<label>قابل پرداخت:</label>
			<span id="pay" class="pay_spn">
<?php foreach ($shopping_cart1 as $cart){
if ($cart->user_id == $this->session->userdata('user_id')){echo $cart->final_price;}
break;} ?>
			</span>

			<br>
			<br>
			<input type="hidden" id="c2">


			<h5 id="msg" class="msg_h5" hidden>کد تخفیف ثبت شد</h5>


			<button id="btn_off" class="btn btn-success" data-toggle="modal"
			data-target="#off_code_modal">ثبت کد تخفیف</button>


			<hr>
			<br>
			<br>

			<div class="div_info">
				<?php foreach ($profile as $p){?>
					<label class="required">گیرنده:</label>
					<input id="name" class="form-control" value="<?php echo $p->name?>"><br>
					<label class="required">آدرس:</label>
					<input id="address" class="form-control" value="<?php echo $p->address?>"><br>
					<label class="required">کدپستی:</label>
					<input id="postal_code" class="form-control" value="<?php echo $p->postal_code?>"><br>
					<label class="required">تلفن:</label>
					<input id="phone_number" class="form-control" value="<?php echo $p->reciever_phone_number?>"><br>
				<?php }?>

			</div>
			<br>
			<br>

					<button id="next1" class="btn btn-success">اتصال به درگاه پرداخت </button>
		</div>
		<div class="col-md-2"></div>
	</div>
</div>


<div class="modal fade" id="off_code_modal" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close mdl_btn" data-dismiss="modal">
					<i class="fa fa-close"></i>
				</button>
			</div>
			<div class="modal-body">
				<input autocomplete="off" id="off_code" type="text" class="form-control">
			</div>
			<div class="modal-footer">
				<button id="accept_off" class="btn btn-success mdl_btn" data-dismiss="modal">تایید </button>

			</div>

		</div>
	</div>
</div>

<?php } ?>
<script>
	$(document).ready(function(e){
		$('#msg').hide();
		$('#btn_off').show();
		var final_price = $('#sum2').text().replace(/,/g,'');
		var final_price2 = $('#sum2').text();
		$('#pay').text(final_price2);
		console.log(final_price)
		$.post('<?php echo base_url()?>home/totalprice',{'final_price':final_price,},);
		$.post('<?php echo base_url()?>home/delete_code');

	});
	$(document).on('click', '#accept_off', function(e) {
		var code = $('#off_code').val();
		var final_price = $('#sum2').text().replace(/,/g,'');
		console.log(code);
		console.log(final_price);
		$.ajax({
			url: "<?php echo base_url(); ?>home/check_code",
			method: "POST",
			data: {'code':code,'final_price':final_price,},
			success:
					function(data) {
						if (data!=2 && data!=1){
							$('#msg').show();
							$('#btn_off').hide();
							$('#c2').val('code');
							$('#pay').html(data);

							console.log(data);
						}
						if(data==2){
							alert('کد تخفیف نادرست است و یا تاریخ انقضای این کد به پایان رسیده');
							$('#off_code').val('')
						}
						if(data==1){
							alert('مبلغ کد تخفیف بیشتر از خرید شما می باشد');
							$('#off_code').val('')
						}

					}

		});

	});

	$(document).on('click', '#next1', function(e) {
		var name=$('#name').val();
		var address=$('#address').val();
		var postal_code=$('#postal_code').val();
		var phone_number=$('#phone_number').val();
		var payment=$('#pay').text().replace(/,/g,'');
		$.ajax({
			url: "<?php echo base_url(); ?>home/info_factor",
			method:"POST",
			data:{'name':name,'address':address,'postal_code':postal_code, 'phone_number':phone_number,},
			success:
					function(data) {
						var r = data;
						$.ajax({
							url: "<?php echo base_url(); ?>home/add_payment",
							method:"POST",
							data:{'payment':payment,},
							success:
								function(data) {

									$.post('<?php echo base_url();?>home/get_order_code',{'r':r,},
											function (data) {
												window.location.href = "<?php echo base_url(); ?>home/order_code";
											});

								}
						});
					}
		});

	});

	$(document).on('click', '#next2', function(e) {
		var name=$('#name').val();
		var address=$('#address').val();
		var postal_code=$('#postal_code').val();
		var phone_number=$('#reciever_phone_number').val();
		var payment=$('#pay').text();
		$.ajax({
			url: "<?php echo base_url(); ?>home/edit_info",
			method:"POST",
			data:{'name':name,'address':address,'postal_code':postal_code, 'phone_number':phone_number,},
			success:
					function(data) {
						$.ajax({
							url: "<?php echo base_url(); ?>home/add_payment",
							method:"POST",
							data:{'payment':payment,},
							success:
									function(data) {

											$.post('<?php echo base_url();?>home/get_order_code',{},
													function (data) {
														window.location.href = "<?php echo base_url(); ?>home/order_code";
													});

									}
						});

					}
		});

	});

</script>
