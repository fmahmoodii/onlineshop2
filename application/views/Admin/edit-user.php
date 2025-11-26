<?php if($this->session->userdata('id')){ ?>


<div class="container-fluid" id="content">
	<div class="row" style="margin-top: 50px">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<div style="padding: 20px;border-radius: 10px; border: 1px solid #ccc">
				<?php if (isset($profile[0])){foreach ($profile as $p){?>
					<form method="post" action='<?php echo base_url('admin/edit_u/') . $p->user_id?>' onsubmit="alert('ویرایش ثبت شد');" >

						<input hidden id="id" name="id" value="<?php echo $p->id?>">

						<div class="form-group">
							<label for="role" class="required">نوع کاربر:</label>

							<select class="form-control" id="role" name="role">
								<?php
								// گرفتن نقش انتخاب شده فعلی کاربر از جدول user_roles
								$user_role_id = isset($user_roles[0]) ? $user_roles[0]->role_id : null;

								foreach ($role as $ro) { ?>
									<option value="<?php echo $ro->id; ?>"
											<?php echo ($ro->id == $user_role_id) ? 'selected' : ''; ?>>
										<?php echo $ro->name; ?>
									</option>
								<?php } ?>
							</select>

							<?php echo form_error('role','<span style="color: red">','</span>') ?>
						</div>



						<div class="form-group">
							<label for="name" class="">نام:</label>
							<span style="color: red" id="name_err"></span>
							<input autocomplete="off" id="name" name="name" type="text" class="form-control" value="<?php if (form_error('name')) {echo set_value('name');}else{echo $p->name;}?>" placeholder="نام">
							<?php echo form_error('name','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="family" class="">نام خانوادگی:</label>
							<span style="color: red" id="family_err"></span>
							<input autocomplete="off" id="family" name="family" type="text" class="form-control" value="<?php if (form_error('family')) {echo set_value('family');}else{echo $p->family;}?>" placeholder="نام خانوادگی">
							<?php echo form_error('family','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="phone_number" class="required">شماره موبایل:</label>
							<span style="color: red" id="ph_err"></span>
							<input readonly autocomplete="off" id="phone_number" name="phone_number" type="text" class="form-control positive" value="<?php if (form_error('phone_number')) {echo set_value('phone_number');}else{echo $register[0]->phone_number;}?>" placeholder="شماره موبایل">
							<?php echo form_error('phone_number','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="phone_number1" class="">شماره موبایل ضروری:</label>
							<span style="color: red" id="ph1_err"></span>
							<input maxlength="11" autocomplete="off" id="phone_number1" name="phone_number1" type="text"
								   class="form-control positive" value="<?php if (form_error('phone_number1')) {echo set_value('phone_number1');}else{echo $p->reciever_phone_number;}?>" placeholder="شماره گیرنده">
							<?php echo form_error('phone_number1','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="ostan" class="">استان:</label>
							<select class="form-control" id="ostan" name="ostan">
								<?php foreach ($profile as $prof){ ?>
									<option value="<?php if ($prof->city==null){echo null;}else{ foreach ($province as $pro){if($pro->id==$prof->ostan){echo $pro->id;}}} ?>" selected><?php if ($prof->ostan==null){echo "انتخاب کنید..";}else{ foreach ($province as $pro){if($pro->id==$prof->ostan){echo $pro->name;}}} ?></option>
								<?php }?>
								<?php foreach ($province as $pro){ ?>
									<option value="<?php echo $pro->id?>"><?php echo $pro->name?></option>
								<?php }?>
							</select>
							<?php echo form_error('ostan','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="city" class="">شهر:</label>
							<!--										<input autocomplete="off" id="city" name="city" type="text" class="form-control" value="--><?php //if (form_error('city')) {echo set_value('city');}else{echo $p->city;}?><!--" placeholder="شهر">-->
							<!--										--><?php //echo form_error('city','<span style="color: red">','</span>') ?>
							<select class="form-control" id="city" name="city">
								<!--											<option value="">انتخاب کنید</option>-->
								<?php foreach ($profile as $prof){ ?>
									<option value="<?php if ($prof->city==null){echo null;}else{ foreach ($city as $ci){if($ci->id==$prof->city){echo $ci->id;}}} ?>" selected><?php if ($prof->city==null){echo "انتخاب کنید..";}else{ foreach ($city as $ci){if($ci->id==$prof->city){echo $ci->name;}}} ?></option>
								<?php }?>
								<?php  foreach ($province as $pro){foreach ($profile as $prof){
										if($pro->id==$prof->ostan){
											foreach ($city as $ci){
												if($ci->province_id==$prof->ostan){?>

													<option value="<?php echo $ci->id?>"><?php echo $ci->name; ?></option>
												<?php }}}}}?>
							</select>
							<?php echo form_error('city','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="address" class="">آدرس:</label>
							<span style="color: red" id="add_err"></span>
							<input autocomplete="off" id="address" name="address" type="text" class="form-control" value="<?php if (form_error('address')) {echo set_value('address');}else{echo $p->address;}?>" placeholder="آدرس">
							<?php echo form_error('address','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="postal_code" class="">کدپستی:</label>
							<span style="color: red" id="pcode_err"></span>
							<input autocomplete="off" id="postal_code" name="postal_code" type="text" class="form-control" value="<?php if (form_error('postal_code')) {echo set_value('postal_code');}else{echo $p->postal_code;}?>" placeholder="کدپستی">
							<?php echo form_error('postal_code','<span style="color: red">','</span>') ?><br>
						</div>

						<button class="btn btn-success" id="submit" style="width: 150px; ">ثبت و ذخیره</button>

					</form>
				<?php }}?>
			</div>
		</div>
		<div class="col-md-2"></div>
	</div>
</div>
<?php }?>

<script>

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
