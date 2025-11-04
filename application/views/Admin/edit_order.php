<?php if($this->session->userdata('id')){ ?>

	<div class="container-fluid" id="content" style="padding: 20px 0 70px 0">
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-9">
				<br>
				<label for="status2">وضعیت ارسال:</label>
				<?php foreach ($shopping_cart_order as $ord){?>
				<select id="status2" name="status2" class="form-control">
					<option <?php if($ord->status=='در انتظار ارسال'){echo 'selected';}?> >در انتظار ارسال</option>
					<option <?php if($ord->status=='ارسال شده'){echo 'selected';}?>>ارسال شده</option>
					<option <?php if($ord->status=='مرجوع شده'){echo 'selected';}?>>مرجوع شده</option>
				</select>
				<?php break;}?>
				<br>
				<label for="user">کاربر:</label>
				<select class="form-control" id="user" name="user">
					<?php foreach ($register as $reg){ foreach ($profile as $prof){if($reg->id == $prof->user_id){ ?>
						<option
								<?php  foreach ($shopping_cart_order as $ord){if($reg->id ==$ord->user_id){ echo 'selected'; break;}}?>
								value="<?php echo $reg->id?>" ><?php $x=$prof->name.' '.$prof->family; echo $x;?>
						</option>
					<?php }}}?>
				</select>

				<br>
				<br>
				<br>

				<table id="factor" class="table table-bordered table-striped" >
					<thead>

					<tr>
						<th ></th>
						<th >نام محصول</th>
						<th width="10%">تعداد</th>
						<th >قیمت واحد</th>
						<th >مجموع</th>
						<th width="10%">درصد تخفیف</th>
						<th >مبلغ تخفیف</th>
						<th >قیمت نهایی</th>
					</tr>
					</thead>

					<tr id="new_row" id_attr="" id_p="" code_p="" style="background-color: #d7eddd">
						<td style="width: 100px">
							<button id="add_row" class="btn btn-success"><i class="fa fa-plus"></i></button>
						</td>
						<td>
							<select id="new_products" class="form-control">
								<option selected value=""></option>
								<?php foreach ($product_attributes as $pa){foreach ($products as $p){if($p->code == $pa->code_p){?>

								<option value="<?php echo $pa->id;?>"
										price="<?php echo $pa->price;?>"
										id_attr="<?php echo $pa->id;?>"
										id_p="<?php echo $p->id;?>"
										name="<?php echo $p->name;?>"
										off_percent="<?php echo $pa->off_percent;?>"
										code_p="<?php echo $pa->code_p;?>"
										order_code="<?php echo $ord->order_code;?>"
								>
									<?php  echo $p->name,' - ',$pa->price,' ';
									echo '(';
									if($pa->model!=0){foreach ($model as $mo){if($pa->model==$mo->id){ echo 'مدل:',$mo->name,' ';}}}
									if($pa->jens!=0){foreach ($jens as $je){if($pa->jens==$je->id){ echo 'مدل:',$je->name,' ';}}}
									if($pa->size!=0){foreach ($size as $si){if($pa->size==$si->id){ echo 'مدل:',$si->name,' ';}}}
									if($pa->brand!=0){foreach ($brand as $br){if($pa->brand==$br->id){ echo 'مدل:',$br->name,' ';}}}
									if($pa->color!=0){foreach ($color as $co){if($pa->color==$co->id){ echo 'مدل:',$co->name,' ';}}}
									echo ')';
									?>
										</option>
								<?php }}}?>
							</select>

						</td>
						<td><input id="new_qty" class="form-control" type="number" min="0" value=""></td>
						<td><input id="new_price" class="form-control" type="number" min="0" value=""></td>
						<td><span id="new_total_price1"></span></td>
						<td><input id="new_off_percent" class="form-control" type="number" min="0" value=""></td>
						<td><span id="new_off_price"></span></td>
						<td><span id="new_total_price"></span></td>
					</tr>
					<tbody id="first">
					<?php foreach ($shopping_cart_order as $ord){  ?>
						<input type="hidden" id="order_code" value="<?php echo $ord->order_code?>">
						<input type="hidden" id="user_id" value="<?php echo $ord->user_id?>">
						<input type="hidden" id="status" value="<?php echo $ord->status?>">

						<tr id="row_<?php echo $ord->id?>" id_ord="<?php echo $ord->id?>">
							<td style="width: 100px">
								<button id="del_row" id_ord="<?php echo $ord->id?>" class="btn btn-danger"><i class="fa fa-minus"></i></button>
							</td>
							<td>
								<select id="products" id_attr="<?php echo $ord->id_attr;?>" id_ord="<?php echo $ord->id;?>" class="form-control">
									<option  value="<?php echo $ord->id_attr;?>"
											price="<?php echo $ord->price;?>"
											id_attr="<?php echo $ord->id_attr;?>"
											id_p="<?php echo $ord->id_p;?>"
											name="<?php echo $ord->name;?>"
											off_percent="<?php echo $ord->off_percent;?>"
											 code_p="<?php echo $ord->code_p;?>"
											 order_code="<?php echo $order_code;?>"
											 user_id="<?php echo $ord->user_id;?>"
											 status="<?php echo $ord->status?>"
									>
										<?php  echo $ord->name,' - ',$ord->price,' ';
										echo '(';
										foreach ($product_attributes as $pa){if($ord->code_p == $pa->code_p){
										if($pa->model!=0){foreach ($model as $mo){if($pa->model==$mo->id){ echo 'مدل:',$mo->name,' ';}}}
										if($pa->jens!=0){foreach ($jens as $je){if($pa->jens==$je->id){ echo 'مدل:',$je->name,' ';}}}
										if($pa->size!=0){foreach ($size as $si){if($pa->size==$si->id){ echo 'مدل:',$si->name,' ';}}}
										if($pa->brand!=0){foreach ($brand as $br){if($pa->brand==$br->id){ echo 'مدل:',$br->name,' ';}}}
										if($pa->color!=0){foreach ($color as $co){if($pa->color==$co->id){ echo 'مدل:',$co->name,' ';}}}
										}}
										echo ')';
										?>
									</option>
									<?php foreach ($product_attributes as $pa){foreach ($products as $p){if($p->code == $pa->code_p){?>
										<option value="<?php echo $pa->id;?>"
												price="<?php echo $pa->price;?>"
												id_attr="<?php echo $pa->id;?>"
												id_p="<?php echo $p->id;?>"
												name="<?php echo $p->name;?>"
												off_percent="<?php echo $pa->off_percent;?>"
												code_p="<?php echo $p->code;?>"
												order_code="<?php echo $order_code;?>"
										>
											<?php  echo $p->name,' - ',$pa->price,' ';
											echo '(';
											if($pa->model!=0){foreach ($model as $mo){if($pa->model==$mo->id){ echo 'مدل:',$mo->name,' ';}}}
											if($pa->jens!=0){foreach ($jens as $je){if($pa->jens==$je->id){ echo 'مدل:',$je->name,' ';}}}
											if($pa->size!=0){foreach ($size as $si){if($pa->size==$si->id){ echo 'مدل:',$si->name,' ';}}}
											if($pa->brand!=0){foreach ($brand as $br){if($pa->brand==$br->id){ echo 'مدل:',$br->name,' ';}}}
											if($pa->color!=0){foreach ($color as $co){if($pa->color==$co->id){ echo 'مدل:',$co->name,' ';}}}
											echo ')';
											?>
										</option>
									<?php }}}?>
								</select>

							</td>
							<td><input id="qty_<?php echo $ord->id?>" id_ord="<?php echo $ord->id?>" class="qty form-control" type="number" min="0" value="<?php echo $ord->qty?>"></td>
							<td><input id="price_<?php echo $ord->id?>" id_ord="<?php echo $ord->id?>" class="price form-control" type="number" min="0" value="<?php echo $ord->price?>"></td>
							<td><span id="total_price1_<?php echo $ord->id?>" id_ord="<?php echo $ord->id?>"><?php echo number_format($ord->total_price1)?></span></td>
							<td><input id="off_percent_<?php echo $ord->id?>" id_ord="<?php echo $ord->id?>" class="off_percent form-control" type="number" min="0" value="<?php echo $ord->off_percent?>"></td>
							<td><span id="off_price_<?php echo $ord->id?>" id_ord="<?php echo $ord->id?>"><?php echo number_format($ord->off_price)?></span></td>
							<td><span class="total_price" id="total_price_<?php echo $ord->id?>" id_ord="<?php echo $ord->id?>"><?php echo number_format($ord->total_price)?></span></td>
						</tr>
					<?php }?>

					<tr>
						<td></td>
						<td colspan="6" >جمع کل</td>
						<td><span id="final_price"><?php echo number_format($ord->final_price)?></span></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="6" >کد تخفیف
							<span id="off_price"></span>
						</td>
						<td>
							<select id="test" class="form-control">
								<option value="<?php echo $ord->off_code;?>" off_code_mablagh="<?php echo $ord->off_code_mablagh;?>">
									<?php echo $ord->off_code;?>
								</option>
								<option value="" off_code_mablagh="">
									خالی
								</option>
								<?php foreach ($off_code as $off){?>
								<option value="<?php echo $off->id;?>" off_code_mablagh="<?php echo $off->mablagh;?>">
									<?php echo $off->code;?>
								</option>
								<?php }?>
							</select>

						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="6" >قابل پرداخت</td>
						<td><span id="payment"><?php echo number_format($ord->payment)?></span></td>
					</tr>
					</tbody>
				</table>
				<br>
				<div class="div_info" style="border-radius: 10px; border: 1px solid #e0dfdf; padding: 20px;">

						<label class="required">گیرنده:</label>
						<input id="name" class="form-control" value="<?php if(isset($info_fact[0])){echo $info_fact[0]->name;}?>"><br>
						<label class="required">آدرس:</label>
						<input id="address" class="form-control" value="<?php if(isset($info_fact[0])){echo $info_fact[0]->address;}?>"><br>
						<label class="required">کدپستی:</label>
						<input id="postal_code" class="form-control" value="<?php if(isset($info_fact[0])){echo $info_fact[0]->postal_code;}?>"><br>
						<label class="required">تلفن:</label>
						<input id="phone_number" class="form-control" value="<?php if(isset($info_fact[0])){echo $info_fact[0]->phone_number;}?>"><br>


				</div>
				<br>
				<br>


<!--				<button onclick="window.print();return false" class="btn btn-info">چاپ</button>-->
				<button id="accept" class="btn btn-success">تایید و ذخیره</button>
				<a href="<?php echo base_url()?>admin/sales" ><button class="btn btn-danger">بازگشت</button></a>
			</div>
			<div class="col-md-1"></div>

		</div>

	</div>
<?php }?>
<script>
	$(document).ready(function(){
		var off_code_mablagh = $('#test').find(':selected').attr('off_code_mablagh');
		if(off_code_mablagh>0){
			$('#off_price').text('('+off_code_mablagh.replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' تومان)');}
		else{
			$('#off_price').text('');
		}

		//var user_id = $('#user').val();
		//$.ajax({
		//	url: "<?php //echo base_url(); ?>//admin/get_profile",
		//	method: "POST",
		//	data: {
		//		'user_id':user_id,
		//	},
		//	success:
		//		function(data) {
		//			var obj = JSON.parse(data);
		//			$('#name').val(obj.name);
		//			$('#address').val(obj.address);
		//			$('#postal_code').val(obj.postal_code);
		//			$('#phone_number').val(obj.phone_number);
		//		}
		//});
	});

	$(document).on('change', '#user', function(e) {
		var user_id = $('#user').val();
		$.ajax({
			url: "<?php echo base_url(); ?>admin/get_profile",
			method: "POST",
			data: {
				'user_id':user_id,
			},
			success:
				function(data) {
					var obj = JSON.parse(data);
					$('#name').val(obj.name);
					$('#address').val(obj.address);
					$('#postal_code').val(obj.postal_code);
					$('#phone_number').val(obj.phone_number);
				}
		});
	});

	$(document).on('change', '#new_products', function(e) {

		console.log($('#new_products option:selected').val());
		if($('#new_products option:selected').val()!='') {
			var price = $(this).find(':selected').attr('price');
			var off_percent = $(this).find(':selected').attr('off_percent');
			console.log(price);

			$('#new_qty').val(1);
			$('#new_price').val(price);
			$('#new_off_percent').val(off_percent);

			var new_qty = $('#new_qty').val();
			var ttlprc = new_qty * price;
			var offprc = ttlprc * (off_percent / 100);

			$('#new_total_price1').text(ttlprc);
			$('#new_off_price').text(offprc);
			$('#new_total_price').text(ttlprc - offprc);
		}else {
			$('#new_total_price1').text('');
			$('#new_off_price').text('');
			$('#new_total_price').text('');
		}

	});

	$(document).on('input', '#new_qty,#new_price,#new_off_percent', function(e) {

		var new_qty = $('#new_qty').val();
		var new_price = $('#new_price').val();
		var new_off_percent = $('#new_off_percent').val();

		if($('#new_products option:selected').val()!=''){
			var ttlprc = new_qty * new_price;
			var offprc = ttlprc * (new_off_percent/100);

			$('#new_total_price1').text(ttlprc);
			$('#new_off_price').text(offprc);
			$('#new_total_price').text(ttlprc-offprc);
		} else{
			$('#new_total_price1').text('');
			$('#new_off_price').text('');
			$('#new_total_price').text('');
		}


	});



	$(document).on('change', '#products', function(e) {


		var id_ord = $(this).attr('id_ord');
		var price = $(this).find(':selected').attr('price');
		var off_percent = $(this).find(':selected').attr('off_percent');

		var off_code_mablagh = $('#test').find(':selected').attr('off_code_mablagh');

			$("#qty_" + id_ord).val(1);
			$('#price_' + id_ord).val(price);
			$('#off_percent_' + id_ord).val(off_percent);

			var qty = $('#qty_' + id_ord).val();
			console.log( $("#qty_" + id_ord).val());
			var ttlprc = qty * price;
			var offprc = ttlprc * (off_percent / 100);

			$('#total_price1_' + id_ord).text(ttlprc.toLocaleString());
			$('#off_price_' + id_ord).text(offprc.toLocaleString());
			$('#total_price_' + id_ord).text((ttlprc - offprc).toLocaleString());

			var x;
			var n;
			var t =0

			var inp = $('.total_price');
			for(var i=0; i<inp.length; i++){
				x = $(inp[i]).text();
				n = x.replace(/\,/g,'');
				t = t + Number(n);
			}
			console.log(t);

			var payment = t - off_code_mablagh;
		if(payment<=0){payment=0}
			$('#final_price').text(t.toLocaleString());
			$('#payment').text(payment.toLocaleString());


	});


	$(document).on('input', '.qty,.price,.off_percent', function(e) {
		var id_ord = $(this).attr('id_ord');
		var off_code_mablagh = $('#test').find(':selected').attr('off_code_mablagh');

		var qty = $('#qty_'+ id_ord).val();
		var price = $('#price_'+ id_ord).val();
		var off_percent = $('#off_percent_'+ id_ord).val();
		console.log(off_percent)

		var ttlprc = qty * price;
		var offprc = ttlprc * (off_percent/100);

		$('#total_price1_' + id_ord).text(ttlprc.toLocaleString());
		$('#off_price_' + id_ord).text(offprc.toLocaleString());
		$('#total_price_' + id_ord).text((ttlprc - offprc).toLocaleString());

		var x;
		var n;
		var t =0

		var inp = $('.total_price');
		for(var i=0; i<inp.length; i++){
			x = $(inp[i]).text();
			n = x.replace(/\,/g,'');
			t = t + Number(n);
		}
		console.log(t);

		var payment = t - off_code_mablagh;
		if(payment<=0){payment=0}
		$('#final_price').text(t.toLocaleString());
		$('#payment').text(payment.toLocaleString());


	});

	$(document).on('change', '#test', function(e) {
		var off_code_mablagh = $(this).find(':selected').attr('off_code_mablagh');
		if(off_code_mablagh>0){
		$('#off_price').text('('+off_code_mablagh.replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' تومان)');}
		else{
			$('#off_price').text('');
		}
		var x;
		var n;
		var t =0

		var inp = $('.total_price');
		for(var i=0; i<inp.length; i++){
			x = $(inp[i]).text();
			n = x.replace(/\,/g,'');
			t = t + Number(n);
		}
		console.log(t);

		var payment = t - off_code_mablagh;
		if(payment<=0){payment=0}
		$('#final_price').text(t.toLocaleString());
		$('#payment').text(payment.toLocaleString());
	});

	$(document).on('click', '#add_row', function(e) {

		var id_attr = $('#new_products').find(':selected').attr('id_attr');
		var new_ttlp = $('#new_total_price').text();
		var new_total_price = new_ttlp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		console.log(new_total_price);
		console.log(new_ttlp);
		var off_code_mablagh = $('#test').find(':selected').attr('off_code_mablagh');

		var order_code = $('#order_code').val();
		var user_id = $('#user_id').val();
		var status = $('#status').val();
		var code_p = $('#new_products').find(':selected').attr('code_p');
		var id_p = $('#new_products').find(':selected').attr('id_p');
		console.log(order_code);
		console.log(user_id);
		console.log(status);
		console.log(code_p);
		console.log(id_p);

		var id_ord = 'e'+Math.floor(Math.random()*(9999 - 1000) + 1000);

		$.ajax({
			url: "<?php echo base_url(); ?>admin/get_product",
			method: "POST",
			data: {
				'id_attr':id_attr,
				'order_code':order_code,
				'user_id':user_id,
				'status':status,
				'code_p':code_p,
				'id_p':id_p,
			},
			success:
				function(data) {
					var new_qty = $('#new_qty').val();
					var new_price = $('#new_price').val();
					var new_ttlp1 = $('#new_total_price1').text();
					var new_total_price1 = new_ttlp1.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
					var new_off_percent = $('#new_off_percent').val();
					var new_offp = $('#new_off_price').text();
					var new_off_price = new_offp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
					var new_ttlp = $('#new_total_price').text();
					var new_total_price = new_ttlp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
					$('#first').prepend(
						'<tr id="row_'+id_ord+'"><td>' +
						'<button id="del_row" id_ord="'+id_ord+'" class="btn btn-danger"><i class="fa fa-minus"></i></button>' +
						'</td><td>' +
						'<select id="products" class="form-control" id_attr="" id_ord="'+id_ord+'">' +
						' ' + data + ' ' +
						'</select>' +
						'</td>' +
						'<td><input id="qty_'+id_ord+'" id_ord="'+id_ord+'" class="qty form-control" type="number" min="0" value="'+new_qty+'"></td>' +
						'<td><input id="price_'+id_ord+'" id_ord="'+id_ord+'" class="price form-control" type="number" min="0" value="'+new_price+'"></td>' +
						'<td><span id="total_price1_'+id_ord+'" id_ord="'+id_ord+'">'+new_total_price1+'</span></td>' +
						'<td><input id="off_percent_'+id_ord+'" id_ord="'+id_ord+'" class="off_percent form-control" type="number" min="0" value="'+new_off_percent+'"></td>' +
						'<td><span id="off_price_'+id_ord+'" id_ord="'+id_ord+'">'+new_off_price+'</span></td>' +
						'<td><span class="total_price" id="total_price_'+id_ord+'" id_ord="'+id_ord+'">'+new_total_price+'</span></td>' +
						'</tr>'
					);

					$('#new_products').val('');
					$('#new_qty').val('');
					$('#new_price').val('');
					$('#new_total_price1').text('');
					$('#new_off_percent').val('');
					$('#new_off_price').text('');
					$('#new_total_price').text('');

					var x;
					var n;
					var t =0

					var inp = $('.total_price');
					for(var i=0; i<inp.length; i++){
						x = $(inp[i]).text();
						n = x.replace(/\,/g,'');
						t = t + Number(n);
					}
					console.log(t);
					var payment = t - off_code_mablagh;
					if(payment<=0){payment=0}
					$('#final_price').text(t.toLocaleString());
					$('#payment').text(payment.toLocaleString());
				}

		})
		;



	});


	$(document).on('click', '#del_row', function(e) {
		var id_ord = $(this).attr('id_ord');
		console.log(id_ord);
		$('#row_'+id_ord).remove();
		var off_code_mablagh = $('#test').find(':selected').attr('off_code_mablagh');
		var x;
		var n;
		var t =0

		var inp = $('.total_price');
		for(var i=0; i<inp.length; i++){
			x = $(inp[i]).text();
			n = x.replace(/\,/g,'');
			t = t + Number(n);
		}
		var payment = t - off_code_mablagh;
		if(payment<=0){payment=0}
		$('#final_price').text(t.toLocaleString());
		$('#payment').text(payment.toLocaleString());
	});

	$(document).on('click', '#accept', function(e) {
		var ids='';
		var comma='';
		var order_code = $('#order_code').val();
		var user_id = $('#user').val();
		var name = $('#name').val();
		var address = $('#address').val();
		var postal_code = $('#postal_code').val();
		var phone_number = $('#phone_number').val();

		var row = $('[id^=row_]').each(function(){
			var id = (this).id;
			var id_ord = $(this).attr('id_ord');
			console.log(id_ord);

			var off_code=$('#test option:selected').val();
			var off_code_mablagh=$('#test option:selected').attr('off_code_mablagh');
			console.log(off_code);
			console.log(off_code_mablagh);

			if(id.includes('row_e')){
				var id_ord = id.substring(4);
				var id_attr;
				var id_p;
				var name;
				var code_p;
				var order_code;

				$(this).find('#products').each(function(){if($(this).attr('id_ord')==id_ord){
					id_attr = $(this).val();
					id_p = $(this).find(':selected').attr('id_p');
					name = $(this).find(':selected').attr('name');
					code_p = $(this).find(':selected').attr('code_p');
					order_code = $(this).find(':selected').attr('order_code');
					// status = $(this).find(':selected').attr('status');
					// user_id = $(this).find(':selected').attr('user_id');
					console.log(id_attr);
					console.log(id_p);
					console.log(name);
					console.log(order_code);
				}});
				var status = $('#status2').val();
				var user_id = $('#user').val();
				console.log(user_id);
				var qty = $('#qty_'+id_ord).val();
				var price = $('#price_'+id_ord).val();
				var z = $('#total_price1_'+id_ord).text();
				var total_price1 = z.replace(/\,/g,'');
				var off_percent = $('#off_percent_'+id_ord).val();
				var x= $('#off_price_'+id_ord).text();
				var off_price = x.replace(/\,/g,'');
				var y = $('#total_price_'+id_ord).text();
				var total_price = y.replace(/\,/g,'');
				var w = $('#final_price').text();
				var final_price = w.replace(/\,/g,'');
				var q = $('#payment').text();
				var payment = q.replace(/\,/g,'');
				console.log(qty)
				console.log(price)
				console.log(total_price1)
				console.log(off_percent)
				console.log(off_price)
				console.log(total_price)

				$.ajax({
					url: "<?php echo base_url(); ?>admin/add_ord",
					method: "POST",
					data: {
						'id_attr':id_attr,
						'qty':qty,
						'price':price,
						'total_price1':total_price1,
						'off_percent':off_percent,
						'off_price':off_price,
						'total_price':total_price,
						'id_p':id_p,
						'name':name,
						'code_p':code_p,
						'order_code':order_code,
						'status':status,
						'user_id':user_id,
						'final_price':final_price,
						'payment':payment,
						'off_code':off_code,
						'off_code_mablagh':off_code_mablagh,
					},
					success:
						function(data) {
						}
				});

			}
			else{
				console.log(id+' hi');
				ids = ids + comma + id_ord;
				comma=',';

				var id_attr;
				var id_p;
				var name;
				var code_p;
				var order_code;


				$(this).find('#products').each(function(){if($(this).attr('id_ord')==id_ord){
					id_attr = $(this).val();
					id_p = $(this).find(':selected').attr('id_p');
					name = $(this).find(':selected').attr('name');
					code_p = $(this).find(':selected').attr('code_p');
					order_code = $(this).find(':selected').attr('order_code');
					// status = $(this).find(':selected').attr('status');
					// user_id = $(this).find(':selected').attr('user_id');

				}});
				var status = $('#status2').val();
				var user_id = $('#user').val();
				console.log(user_id);
				var qty = $('#qty_'+id_ord).val();
				var price = $('#price_'+id_ord).val();
				var z = $('#total_price1_'+id_ord).text();
				var total_price1 = z.replace(/\,/g,'');
				var off_percent = $('#off_percent_'+id_ord).val();
				var x= $('#off_price_'+id_ord).text();
				var off_price = x.replace(/\,/g,'');
				var y = $('#total_price_'+id_ord).text();
				var total_price = y.replace(/\,/g,'');
				var w = $('#final_price').text();
				var final_price = w.replace(/\,/g,'');
				var q = $('#payment').text();
				var payment = q.replace(/\,/g,'');


				$.ajax({
					url: "<?php echo base_url(); ?>admin/edit_ord",
					method: "POST",
					data: {
						'id':id_ord,
						'user_id':user_id,
						'id_p':id_p,
						'code_p':code_p,
						'id_attr':id_attr,
						'name':name,
						'price':price,
						'qty':qty,
						'off_percent':off_percent,
						'off_price':off_price,
						'total_price':total_price,
						'total_price1':total_price1,
						'final_price':final_price,
						'off_code':off_code,
						'payment':payment,
						'off_code_mablagh':off_code_mablagh,
						'order_code':order_code,
						'status':status,
					},
					success:
						function(data) {
					}
				});
			}

		});

		$.ajax({
			url: "<?php echo base_url(); ?>admin/edit_insert_info",
			method: "POST",
			data: {
				'order_code': order_code,
				'user_id': user_id,
				'name': name,
				'postal_code': postal_code,
				'address': address,
				'phone_number': phone_number,
			},
			success:
				function(data) {
				console.log(postal_code)
				}
		});

		console.log(ids);
		var order_code = $('#order_code').val();
		console.log(order_code);
		$.ajax({
			url: "<?php echo base_url(); ?>admin/deleted_ord",
			method: "POST",
			data: {
				'id':ids,
				'order_code':order_code,
			},
			success:
				function(data) {
				}
		});

		alert('ثبت شد');
		// window.location.reload();

	});

</script>
