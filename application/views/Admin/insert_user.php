<style>/* The snackbar - position it at the bottom and in the middle of the screen */
	#snackbar {
		visibility: hidden; /* Hidden by default. Visible on click */
		min-width: 250px; /* Set a default minimum width */
		margin-left: -125px; /* Divide value of min-width by 2 */
		background-color: #333; /* Black background color */
		color: #fff; /* White text color */
		text-align: center; /* Centered text */
		border-radius: 2px; /* Rounded borders */
		padding: 16px; /* Padding */
		position: fixed; /* Sit on top of the screen */
		z-index: 1; /* Add a z-index if needed */
		left: 50%; /* Center the snackbar */
		bottom: 30px; /* 30px from the bottom */
	}

	/* Show the snackbar when clicking on a button (class added with JavaScript) */
	#snackbar.show {
		visibility: visible; /* Show the snackbar */
		/* Add animation: Take 0.5 seconds to fade in and out the snackbar.
		However, delay the fade out process for 2.5 seconds */
		-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
		animation: fadein 0.5s, fadeout 0.5s 2.5s;
	}

	/* Animations to fade the snackbar in and out */
	@-webkit-keyframes fadein {
		from {bottom: 0; opacity: 0;}
		to {bottom: 30px; opacity: 1;}
	}

	@keyframes fadein {
		from {bottom: 0; opacity: 0;}
		to {bottom: 30px; opacity: 1;}
	}

	@-webkit-keyframes fadeout {
		from {bottom: 30px; opacity: 1;}
		to {bottom: 0; opacity: 0;}
	}

	@keyframes fadeout {
		from {bottom: 30px; opacity: 1;}
		to {bottom: 0; opacity: 0;}
	}
</style>

<?php if($this->session->userdata('id')){ ?>


	<div class="container-fluid" id="content" >
		<div class="row" style="margin-top: 50px; margin-bottom: 60px">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div style="padding: 20px;border-radius: 10px; border: 1px solid #ccc">
						<form method="post" action='<?php echo base_url('admin/add_user') ?>' onsubmit="alert('ثبت شد');" >

							<input hidden id="id" name="id">

							<div class="form-group">
								<label for="role" class="required">نوع کاربر:</label>
								<select class="form-control" id="role" name="role"
										onfocus='this.size=6;' onblur='this.size=6;' onfocusout='this.size=null;' onchange='this.size=6; this.blur();'
								>

									<option selected value=""><?php echo "انتخاب کنید.."; ?></option>

									<?php foreach ($role as $ro){ ?>
										<option value="<?php echo $ro->id?>"><?php echo $ro->name?></option>
									<?php }?>
								</select>
								<?php echo form_error('role','<span style="color: red">','</span>') ?>
							</div>

							<div class="form-group">
								<label for="name" class="">نام:</label>
								<span style="color: red" id="name_err"></span>
								<input maxlength="25" autocomplete="off" id="name" name="name" type="text" class="form-control" value="<?php if (form_error('name')) {echo set_value('name');}?>" placeholder="نام">
								<?php echo form_error('name','<span style="color: red">','</span>') ?>

							</div>

							<div class="form-group">
								<label for="family" class="">نام خانوادگی:</label>
								<span style="color: red" id="family_err"></span>
								<input maxlength="25" autocomplete="off" id="family" name="family" type="text" class="form-control" value="<?php if (form_error('family')) {echo set_value('family');}?>" placeholder="نام خانوادگی">
								<?php echo form_error('family','<span style="color: red">','</span>') ?>
							</div>

							<div class="form-group">
								<label for="phone_number" class="required">شماره موبایل:</label>
								<span style="color: red" id="ph_err"></span>
								<input maxlength="11" autocomplete="off" id="phone_number" name="phone_number" type="text" class="form-control positive" value="<?php if (form_error('phone_number')) {echo set_value('phone_number');}?>" placeholder="شماره موبایل">
								<?php echo form_error('phone_number','<span style="color: red">','</span>') ?>
							</div>
							<div class="form-group">
								<label for="password" class="required">رمز عبور :</label>
								<span style="color: red" id="pass_err"></span>
								<input autocomplete="new-password" id="password" name="password" type="password" class="form-control positive"  placeholder="رمز عبور">
<!--									   value="--><?php //if (form_error('password')) {echo set_value('password');}?><!--" -->

								<?php echo form_error('password','<span style="color: red">','</span>') ?>
							</div>

							<div class="form-group">
								<label for="phone_number1" class="">شماره موبایل ضروری:</label>
								<span style="color: red" id="ph1_err"></span>
								<input maxlength="11" autocomplete="off" id="phone_number1" name="phone_number1" type="text"
									   class="form-control positive" value="<?php if (form_error('phone_number1')) {echo set_value('phone_number1');}?>" placeholder="شماره موبایل ضروری">
								<?php echo form_error('phone_number1','<span style="color: red">','</span>') ?>
							</div>

							<div class="form-group">
								<label for="ostan" class="">استان:</label>
								<select class="form-control" id="ostan" name="ostan">

									<option selected value=""><?php echo "انتخاب کنید.."; ?></option>

									<?php foreach ($province as $pro){ ?>
										<option value="<?php echo $pro->id?>"><?php echo $pro->name?></option>
									<?php }?>
								</select>
								<?php echo form_error('ostan','<span style="color: red">','</span>') ?>
							</div>

							<div class="form-group">
								<label for="city" class="">شهر:</label>
								<select class="form-control" id="city" name="city">
									<option value="">انتخاب کنید</option>

									<?php  foreach ($province as $pro){
										foreach ($city as $ci){
										if($ci->province_id==$pro->id){?>

										<option value="<?php echo $ci->id?>"><?php echo $ci->name; ?></option>
									<?php }}}?>
								</select>
								<?php echo form_error('city','<span style="color: red">','</span>') ?>
							</div>

							<div class="form-group">
								<label for="address" class="">آدرس:</label>
								<span style="color: red" id="add_err"></span>
								<input autocomplete="off" id="address" name="address" type="text" class="form-control" value="<?php if (form_error('address')) {echo set_value('address');}?>" placeholder="آدرس">
								<?php echo form_error('address','<span style="color: red">','</span>') ?>
							</div>

							<div class="form-group">
								<label for="postal_code" class="">کدپستی:</label>
								<span style="color: red" id="pcode_err"></span>
								<input autocomplete="off" id="postal_code" name="postal_code" type="text" class="form-control" value="<?php if (form_error('postal_code')) {echo set_value('postal_code');}?>" placeholder="کدپستی">
								<?php echo form_error('postal_code','<span style="color: red">','</span>') ?><br>
							</div>

							<button class="btn btn-success" id="submit" style="width: 150px; ">ثبت و ذخیره</button>

						</form>

				</div>

			</div>
			<div class="col-md-2"></div>
		</div>
		<div id="snackbar">ثبت شد</div>
	</div>
<?php }?>


<script>

	$('#phone_number').on('input', function() {
			var pn = $(this).val();
			var errorEl = $('#ph_err');
			var regex = /^(0)?9\d{9}$/; // قبول با صفر یا بدون صفر

			// پاک کردن پیام قبلی
			errorEl.html('').css('color','');

			// فقط وقتی طول به حد نصاب رسید بررسی کن
			if (pn.length === 10 || pn.length === 11) {
				if (regex.test(pn)) {
					// شماره درست، حالا بررسی شماره تکراری با AJAX
					$.ajax({
						url: "<?= base_url('admin/check_phone') ?>", // تابع سرور برای چک کردن
						method: "POST",
						data: { phone_number: pn },
						success: function(data) {
							if (data == 'exists') {
								errorEl.html('این شماره موبایل قبلاً ثبت شده است').css('color','red');
							} else {
								errorEl.html('شماره موبایل معتبر است').css('color','green');
							}
						}
					});
				} else {
					errorEl.html('شماره وارد شده نادرست است').css('color','red');
				}
			} else if (pn.length > 11) {
				errorEl.html('تعداد ارقام بیشتر از حد مجاز است').css('color','red');
			}
		});


	//---------get_city-----------
	$('#ostan').change(function(){
		province_id=$("#ostan").val();
		$.post('<?php echo base_url();?>home/get_city',{'province_id':province_id},
			function(data){
				$('#city').html(data);
				//console.log(province_id);
			});
	});
	$('#city').change(function(){
		city=$("#city").val();
		//console.log(city);

	});

	$('#name, #family').on("input",function() {

		/*
				this.value = this.value.replace(/[^a-zA-Z0-9 ]/g,'').replace(/\s\s+/g, ' ').replace(/^\s+/g, '');
		*/

		// /[آ-ی]|([a-zA-Z])/

		//چک کردن حروف فارسی. جلوگیری از اسپیس اول. جلوگیری از اسپیس های چنتایی
		this.value = this.value.replace(/[^\u0600-\u06FF0-9 ]/g,'').replace(/\s\s+/g, ' ').replace(/^\s+/g, '').replace(/^[\d .-]+/g,'');


	});


	$('#name, #family').on("keypress",function(event) {

		if (event.keyCode >= 48 && event.keyCode <= 57) {
			// Number
			if(this.id==='name'){
				$('#name_err').show();
				$('#name_err').html('فقط از حروف فارسی استفاده کنید');
			}
			else if(this.id==='family'){
				$('#family_err').show();
				$('#family_err').html('فقط از حروف فارسی استفاده کنید');
			}
		} else if (event.keyCode >= 65 && event.keyCode <= 90) {
			// Alphabet upper case
			if(this.id==='name'){
				$('#name_err').show();
				$('#name_err').html('زبان کیبورد را فارسی کنید');
			}
			else if(this.id==='family'){
				$('#family_err').show();
				$('#family_err').html('زبان کیبورد را فارسی کنید');
			}
		} else if (event.keyCode >= 97 && event.keyCode <= 122) {
			// Alphabet lower case
			if (this.id === 'name') {
				$('#name_err').show();
				$('#name_err').html('زبان کیبورد را فارسی کنید');
			} else if (this.id === 'family') {
				$('#family_err').show();
				$('#family_err').html('زبان کیبورد را فارسی کنید');

			}
		}else {

			if (this.id === 'name') {
				$('#name_err').hide();
			} else if (this.id === 'family') {
				$('#family_err').hide();
			}
		}


	});

	$('#phone_number').keyup(function(){
		var pn=$(this).val();
		var regx = "^(\\+98|0)?9\\d{9}$";
		var err='';
		var error = $('#ph_err');

		if(pn!==''){
			if(pn.match(regx)){
				err= '';
				error.html(err);
			}
			else
			{
				if (pn.length == 11){
					err= 'شماره وارد شده نادرست است';
					error.html(err);
					error.css({'color':'red'});
				}else{
					err= 'تعداد ارقام کمتر از 11 می باشد';
					error.html(err);
					error.css({'color':'blue'});
				}

			}
		}else{
			error.html(err);
			error.css({'color':''});
		}

	});

	$('#phone_number1').keyup(function(){
		var pn=$(this).val();
		var regx = "^(\\+98|0)?9\\d{9}$";
		var err='';
		var error = $('#ph1_err');

		if(pn!==''){
			if(pn.match(regx)){
				err= '';
				error.html(err);
			}
			else
			{
				if (pn.length == 11){
					err= 'شماره وارد شده نادرست است';
					error.html(err);
					error.css({'color':'red'});
				}else{
					err= 'تعداد ارقام کمتر از 11 می باشد';
					error.html(err);
					error.css({'color':'blue'});
				}

			}
		}else{
			error.html(err);
			error.css({'color':''});
		}

	});

	$('.positive').keydown( function(e) {
		if(!((e.keyCode > 95 && e.keyCode < 106)
			|| (e.keyCode > 47 && e.keyCode < 58)
			|| e.keyCode == 8)) {
			return false;
		}
	});
</script>
