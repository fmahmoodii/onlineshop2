<?php if($this->session->userdata('id')){ ?>

<div class="container-fluid" id="content" style="margin-top: 50px">
	<div class="row" >
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<div style="padding: 20px;border-radius: 10px; border: 1px solid #ccc">
				<?php foreach ($profile as $prof){?>
					<input id="id" type="hidden" value="<?php echo $prof->id?>">
					<label>نام:</label><br>
					<input id="name" class="form-control" type="text" value="<?php echo $prof->name?>"><br>
					<label>نام خانوادگی:</label><br>
					<input id="family" class="form-control" type="text" value="<?php echo $prof->family?>"><br>
					<label>شماره موبایل:</label><br>
					<input id="phone_number" disabled class="form-control" type="text" value="<?php echo $prof->phone_number?>"><br>
					<label>شماره موبایل گیرنده:</label><br>
					<input id="reciever_phone_number" class="form-control" type="text" value="<?php echo $prof->reciever_phone_number?>"><br>
					<label>استان:</label><br>
					<input id="ostan" class="form-control" type="text" value="<?php echo $prof->ostan?>"><br>
					<label>شهر:</label><br>
					<input id="city" class="form-control" type="text" value="<?php echo $prof->city?>"><br>
					<label>آدرس:</label><br>
					<input id="address" class="form-control" type="text" value="<?php echo $prof->address?>"><br>
					<label>کد پستی:</label><br>
					<input id="postal_code" class="form-control" type="text" value="<?php echo $prof->postal_code?>"><br>


					<button id="edit" class="btn btn-success">ثبت ویرایش</button>
					<a href="<?php echo base_url()?>admin/sales" ><button class="btn btn-danger">بازگشت</button></a>
				<?php }?>
			</div>
		</div>
		<div class="col-md-3"></div>
	</div>
</div>
<?php }?>

<script>
	$(document).on('click', '#edit', function(e){
		var id = $('#id').val();
		var name = $('#name').val();
		var family = $('#family').val();
		var reciever_phone_number = $('#reciever_phone_number').val();
		var ostan = $('#ostan').val();
		var city = $('#city').val();
		var address = $('#address').val();
		var postal_code = $('#postal_code').val();
		$.post('<?php echo base_url();?>admin/edit_u/'+id,{'id':id,'name': name,
				'family': family,'reciever_phone_number': reciever_phone_number,
				'ostan': ostan,'city': city,'address': address,'postal_code': postal_code,},
			function (data) {
				if (data==1){

					$("#name").val(name);
					$("#family").val(family);
					$("#reciever_phone_number").val(reciever_phone_number);
					$("#ostan").val(ostan);
					$("#city").val(city);
					$("#address").val(address);
					$("#postal_code").val(postal_code);
				}
				alert('ویرایش با موفقیت انجام شد')
				window.location.reload();
			}
		);
	});



</script>

