
<div class="container-fluid">
	<div class="row" style="height: 130px"></div>
</div>

<!--tabs-->
<div class="container">
    <div class="row">
        <ul class="nav nav-tabs" style="margin-bottom: 30px">
            <li class="active col-md-2"><a style="color: #414141" data-toggle="tab" href="#profile"><b>اطلاعات کاربری</b></a></li>
            <li class="col-md-2"><a style="color: #414141"  href="<?php echo site_url('home/bookmarks'); ?>"><b>علاقه مندی ها</b></a></li>
            <li class="col-md-2"><a style="color: #414141"  href="<?php echo site_url('home/factors'); ?>"><b>خریدهای شما</b></a></li>
            <li class="col-md-2"><a style="color: #414141" data-toggle="tab" href="#pass"><b>تغییر رمز عبور</b></a></li>
        </ul>
        <div class="tab-content" style="padding: 20px;">

            <div id="profile" class="tab-pane fade in active">
                <div class="container-fluid">
                    <div class="row" style="margin: 0 auto;width: 60%; height: auto; border-radius:11px; padding: 20px;box-shadow: 5px 5px 5px #c9ccc7;
						  border: 1px solid #ccc;">
                       <!-- <img src="" width="100px" height="150px" style="margin-bottom: 10px">
                        <input autocomplete="off" type="file"><br>-->

<!--						--><?php //echo validation_errors('<div class="alert alert-danger">','</div>'); ?>

						<div>
							<?php if (isset($profile[0])){foreach ($profile as $p){?>
								<form method="post" action='<?php echo base_url('home/edit_profile/') . $_SESSION['user_id']?>' >

									<input hidden id="id" name="id" value="<?php echo $p->id?>">

									<div class="form-group">
										<label for="name" class="required">نام:</label>
										<span style="color: red" id="name_err"></span>
										<input autocomplete="off" id="name" name="name" type="text" class="form-control" value="<?php if (form_error('name')) {echo set_value('name');}else{echo $p->name;}?>" placeholder="نام">
										<?php echo form_error('name','<span style="color: red">','</span>') ?>

									</div>

									<div class="form-group">
										<label for="family" class="required">نام خانوادگی:</label>
										<span style="color: red" id="family_err"></span>
										<input autocomplete="off" id="family" name="family" type="text" class="form-control" value="<?php if (form_error('family')) {echo set_value('family');}else{echo $p->family;}?>" placeholder="نام خانوادگی">
										<?php echo form_error('family','<span style="color: red">','</span>') ?>
									</div>

									<div class="form-group">
										<label for="phone_number" class="required">شماره موبایل:</label>
										<span style="color: red" id="ph_err"></span>
										<input readonly autocomplete="off" id="phone_number" name="phone_number" type="text" class="form-control positive" value="<?php if (form_error('phone_number')) {echo set_value('phone_number');}else{echo $p->phone_number;}?>" placeholder="شماره موبایل">
										<?php echo form_error('phone_number','<span style="color: red">','</span>') ?>
									</div>

									<div class="form-group">
										<label for="phone_number1" class="required">شماره موبایل ضروری:</label>
										<span style="color: red" id="ph1_err"></span>
										<input maxlength="11" autocomplete="off" id="phone_number1" name="phone_number1" type="text"
											   class="form-control positive" value="<?php if (form_error('phone_number1')) {echo set_value('phone_number1');}else{echo $p->reciever_phone_number;}?>" placeholder="شماره گیرنده">
										<?php echo form_error('phone_number1','<span style="color: red">','</span>') ?>
									</div>

									<div class="form-group">
										<label for="ostan" class="required">استان:</label>
										<select class="form-control" id="ostan" name="ostan">
											<?php foreach ($profile as $prof){if ($prof->user_id == $this->session->userdata('user_id')){ ?>
												<option value="<?php if ($prof->city==null){echo null;}else{ foreach ($province as $pro){if($pro->id==$prof->ostan){echo $pro->id;}}} ?>" selected><?php if ($prof->ostan==null){echo "انتخاب کنید..";}else{ foreach ($province as $pro){if($pro->id==$prof->ostan){echo $pro->name;}}} ?></option>
											<?php }}?>
											<?php foreach ($province as $pro){ ?>
												<option value="<?php echo $pro->id?>"><?php echo $pro->name?></option>
											<?php }?>
										</select>
										<?php echo form_error('ostan','<span style="color: red">','</span>') ?>
									</div>

									<div class="form-group">
										<label for="city" class="required">شهر:</label>
<!--										<input autocomplete="off" id="city" name="city" type="text" class="form-control" value="--><?php //if (form_error('city')) {echo set_value('city');}else{echo $p->city;}?><!--" placeholder="شهر">-->
<!--										--><?php //echo form_error('city','<span style="color: red">','</span>') ?>
										<select class="form-control" id="city" name="city">
<!--											<option value="">انتخاب کنید</option>-->
											<?php foreach ($profile as $prof){if ($prof->user_id == $this->session->userdata('user_id')){ ?>
												<option value="<?php if ($prof->city==null){echo null;}else{ foreach ($city as $ci){if($ci->id==$prof->city){echo $ci->id;}}} ?>" selected><?php if ($prof->city==null){echo "انتخاب کنید..";}else{ foreach ($city as $ci){if($ci->id==$prof->city){echo $ci->name;}}} ?></option>
											<?php }}?>
											<?php  foreach ($province as $pro){foreach ($profile as $prof){
												if ($prof->user_id == $this->session->userdata('user_id')){
													if($pro->id==$prof->ostan){
														foreach ($city as $ci){
															if($ci->province_id==$prof->ostan){?>

												<option value="<?php echo $ci->id?>"><?php echo $ci->name; ?></option>
											<?php }}}}}}?>
										</select>
										<?php echo form_error('city','<span style="color: red">','</span>') ?>
									</div>

									<div class="form-group">
										<label for="address" class="required">آدرس:</label>
										<span style="color: red" id="add_err"></span>
										<input autocomplete="off" id="address" name="address" type="text" class="form-control" value="<?php if (form_error('address')) {echo set_value('address');}else{echo $p->address;}?>" placeholder="آدرس">
										<?php echo form_error('address','<span style="color: red">','</span>') ?>
									</div>

									<div class="form-group">
										<label for="postal_code" class="required">کدپستی:</label>
										<span style="color: red" id="pcode_err"></span>
										<input autocomplete="off" id="postal_code" name="postal_code" type="text" class="form-control" value="<?php if (form_error('postal_code')) {echo set_value('postal_code');}else{echo $p->postal_code;}?>" placeholder="کدپستی">
										<?php echo form_error('postal_code','<span style="color: red">','</span>') ?><br>
									</div>

									<button class="btn btn-success" id="submit" style="width: 150px; ">ثبت و ذخیره</button>

								</form>
							<?php }}?>
						</div>

                    </div>
                </div>
            </div>

			<div id="pass" class="tab-pane fade">
					<div class="row">
						<!--login-form-->
						<div id="change_pass" style=" margin: 0 auto;padding: 20px; width: 500px; height: auto;box-shadow: 0px 2px 15px 2px #ccc; border-radius: 11px;">

							<h2 style="text-align: center; color: #7f7f7f; ">
								تغییر رمز عبور
							</h2>

							<div style=" margin-top: 30px;  ">

								<label style="color: #4f4f4f ">رمز عبور قبلی:</label>
								<span id="pass_before_err" style="color: red;">
									رمز عبور قبلی نادرست است
								</span>
								<input type="password"  name="pass_before" id="pass_before" class="form-control  no-zoom" autocomplete="off"
									   required="required" oninvalid="setCustomValidity('رمز عبور قبلی')"
									   onchange="try{setCustomValidity('')}catch(e){}" title='رمز عبور قبلی'
									   placeholder="رمز عبور قبلی">
								<br>
								<label style="color: #4f4f4f ">رمز عبور جدید:</label>
								<input type="password"  name="pass_new" id="pass_new" class="form-control  no-zoom" autocomplete="off"
									   required="required" oninvalid="setCustomValidity('رمز عبور جدید')"
									   onchange="try{setCustomValidity('')}catch(e){}" title='رمز عبور جدید'
									   placeholder="رمز عبور جدید"><br>
								<label style="color: #4f4f4f ">تکرار رمز عبور:</label>
								<span id="pass_err" style="color: red;">
									تکرار رمز عبور مطابقت ندارد
								</span>
								<input type="password"  name="re_pass" id="re_pass" class="form-control  no-zoom" autocomplete="off"
									   required="required" oninvalid="setCustomValidity('تکرار رمز عبور')"
									   onchange="try{setCustomValidity('')}catch(e){}" title='تکرار رمز عبور'
									   placeholder="تکرار رمز عبور">


								<br><br>
								<br>


								<div style="text-align: center;">

									<button id="btn1" class="btn btn-primary form-control"
											style="outline: unset; border: none; width: 453px;
                        ">تایید</button>

								</div>
								<br>
							</div>


						</div>


					</div>
				</div>

			</div>

        </div>


    </div>
</div>

<script>
	$(document).ready(function (){
		$('#pass_err').hide();
		$('#pass_before_err').hide();
	});

	$(document).on('click', '#submit2', function(e) {
		var name=$('#name').val;
		var family=$('#family').val;
		var phone_number1=$('#phone_number1').val;
		var ostan=$('#ostan').val;
		var city=$('#city').val;
		var address=$('#address').val;
		var postal_code=$('#postal_code').val;
		$.post('<?php echo base_url()?>home/prof_validation', {
				'name': name,
				'family': family,
				'phone_number1': phone_number1,
				'ostan': ostan,
				'city': city,
				'address': address,
				'postal_code': postal_code,
			},
			function (data) {
				if (data.includes(1)) {
					$.post('<?php echo base_url('home/edit_profile/') . $_SESSION['user_id']?>', {
							'name': name,
							'family': family,
							'phone_number1': phone_number1,
							'ostan': ostan,
							'city': city,
							'address': address,
							'postal_code': postal_code,
						},
						function (data) {
							if (data.includes(1)) {
								window.location.reload();

							}
						});
				} else {



				}
			});
	});

	$('#btn1').click(function (){
		var pass_before=$('#pass_before').val();
		var pass_new=$('#pass_new').val();
		var re_pass=$('#re_pass').val();

		if(pass_new==re_pass){
		$.post('<?php echo base_url('home/change_pass')?>',{'pass_before':pass_before,'pass_new':pass_new, },
			function (data){
			if(data==1){
				$('#pass_before').val('');
				$('#pass_new').val('');
				$('#re_pass').val('');
				alert("رمز عبور جدید ثبت شد");
				$('#pass_before_err').hide();
			}else{
				$('#pass_before_err').show();
			}
		});
		}else if(!(pass_new==re_pass)){
			$('#pass_err').show();
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

	$(document).on('click', '#submit2', function(e) {
		var id = $("#id").val();
		var name = $("#name").val();
		var family = $("#family").val();
		var phone_number1 = $("#phone_number1").val();
		var ostan = $("#ostan").val();
		var city = $("#city").val();
		var address = $("#address").val();
		var postal_code = $("#postal_code").val();
		$.post('<?php echo base_url()?>home/prof_validation', {
				'id': id,
				'name': name,
				'family': family,
				'phone_number1': phone_number1,
				'ostan': ostan,
				'city': city,
				'address': address,
				'postal_code': postal_code,
			},
			function (data) {
				if (data.includes(1)) {
					$.post('<?php echo base_url('home/edit_profile/') . $_SESSION['user_id']?>', {
							'id': id,
							'name': name,
							'family': family,
							'phone_number1': phone_number1,
							'ostan': ostan,
							'city': city,
							'address': address,
							'postal_code': postal_code,
						},
						function (data) {
							if (data.includes(1)) {
								window.location.reload();

							}
						});
				} else {



				}
			});
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
