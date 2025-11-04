
<div class="container-fluid" style="padding: 0">
	<div class="row" style="padding: 50px;">
		<?php if($this->session->flashdata('err')){ ?>
			<div class="alert alert-warning" style="text-align: center;">
				رمز عبور و نام کاربری شما صحیح نمی باشد
			</div>
		<?php }?>
		<?php if($this->session->flashdata('err2')){ ?>
			<div class="alert alert-warning" style="text-align: center;">
				کاربر غیرفعال می باشد! با ادمین تماس بگیرید.
			</div>
		<?php }?>
		<br>
		<br>
		<!--login button-->
		<div style="text-align: center; padding: 20px 0">
			<button id="login_btn" class="btn btn-default" style="width: 200px;
    margin-left: 10px;
    height: auto;
    box-shadow: 1px 1px 12px 1px #e3e3e3;
    border-radius: 11px;outline: unset">ورود</button>
			<button id="register_btn" class="btn btn-default" style="width: 200px;
    margin-left: 10px;
    height: auto;
    box-shadow: 1px 1px 12px 1px #e3e3e3;
    border-radius: 11px;outline: unset">ثبت نام</button>
		</div>

		<!--login-form-->
		<div id="login" style=" margin: 0 auto;padding: 20px; width: 500px; height: auto;box-shadow: 0px 2px 15px 2px #ccc; border-radius: 11px;">

			<h2 style="text-align: center; color: #7f7f7f; ">
				ورود به سیستم
			</h2>

			<form action="<?php echo base_url()?>home/login"  method="post" style=" margin-top: 50px;  ">
				<label style="color: #4f4f4f ">شماره موبایل:</label>
				<input type="tel" min="11" max="11" name="phone" id="phone" class="form-control  no-zoom"
					   autocomplete="off" maxlength="11"   minlength="11" required="required"
					   pattern="[0-9]{11,11}"  oninvalid="setCustomValidity('شماره موبایل خود را وارد کنید')"
					   onchange="try{setCustomValidity('')}catch(e){}" title='شماره موبایل '
					   oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
					   placeholder="شماره موبایل"><br>
				<label style="color: #4f4f4f ">رمز عبور:</label>
				<input type="password"  name="pass" id="pass" class="form-control  no-zoom" autocomplete="off"
					   required="required" oninvalid="setCustomValidity('رمز عبور')"
					   onchange="try{setCustomValidity('')}catch(e){}" title='رمز عبور '
					   placeholder="رمز عبور">

				<br><br><br>
				<div  id="cap_div" class="noselect" style="display:inline-block;
				user-select: none;height: 50px;width: 180px;
				border-radius: 15px;padding: 5px;margin-bottom: 10px;margin-left: 20px">
					<b  id="capcha" style="font-size: 30px;"></b>
				</div>
				<i id="refresh" title="بازآوری" class="fa fa-refresh" style="color: #636262; cursor: pointer;font-size: 22px"></i>
				<input type="text" name="capcha" id="insert_capcha" class="form-control" autocomplete="off"
					   required="required" title="" placeholder="متن بالا را صحیح وارد کنید" style="margin-top:12px">
				<div style="text-align: center;
    margin-top: 54px;">

					<button id="btn1" class="btn btn-primary form-control" type="submit"
							style="outline: unset; border: none; width: 453px;
                        ">ورود</button>
					<button id="btn" class="btn btn-primary form-control" type="button"
							style="outline: unset; border: none; width: 453px;
                        ">ورود</button>
				</div>
				<br>
					<br>
				<a id="forget-pass" style=" cursor: pointer">رمز عبور خود را فراموش کرده اید؟</a>
			</form>


		</div>
		<!--sign_up-form-->
		<div id="register2" style=" margin: 0 auto;padding: 20px; width: 500px; height: auto;box-shadow: 0px 2px 15px 2px #ccc; border-radius: 11px;">

			<h2 style="text-align: center; color: #7f7f7f; ">
				ثبت نام
			</h2>
			<form action="<?php echo base_url()?>home/register"   method="post" style=" margin-top: 50px; ">
				<label style="color: #4f4f4f ">شماره موبایل:</label>
				<input type="tel" min="11" max="11" name="phone_number" id="phone_number" class="form-control  no-zoom"
					   autocomplete="off" maxlength="11"   minlength="11" required="required"
					   pattern="[0-9]{11,11}" oninvalid="setCustomValidity('شماره موبایل خود را وارد کنید')"
					   onchange="try{setCustomValidity('')}catch(e){}" title='شماره موبایل '
					   oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
					   placeholder="شماره موبایل"><br>
				<label style="color: #4f4f4f ">رمز عبور:</label><br>
				<span id="error_1" style="color: red;display: none;">حداقل رمز عبور 6 رقم باشد</span>
				<span id="accept_1" style="color: green;display: none;">رمز عبور مورد تایید است</span>

				<input type="password"  name="password" id="password" class="form-control  no-zoom" autocomplete="off"
					   required="required"
					   oninvalid="setCustomValidity('رمز عبور')" onchange="try{setCustomValidity('')}catch(e){}"
					   title='رمز عبور '
					   placeholder="رمز عبور"><br>
				<label style="color: #4f4f4f ">تکرار رمز عبور</label><br>
				<span id="error_2" style="color: red;display: none;">رمز عبور مطابقت نداره</span>
				<span id="accept_2" style="color: green;display: none;">تطابق رمز عبور</span>

				<input type="password"  name="re_password" id="re_password" class="form-control  no-zoom" autocomplete="off"
					   required="required"
					   oninvalid="setCustomValidity('رمز عبور')" onchange="try{setCustomValidity('')}catch(e){}"
					   title='تکرار رمز عبور '
					   placeholder="تکرار رمز عبور">

				<span id='message'></span>

				<div style="text-align: center;
    margin-top: 54px;">
					<button class="btn btn-primary form-control" id="btn_accept_reg" type="submit"
							style="outline: unset; border: none; width: 453px;display: none ">ثبت نام</button>

				</div>
				<br><br>
			</form>




		</div>
		<!--forget form-->
		<div id="forget" style=" margin: 0 auto;padding: 20px; width: 500px; height: auto;box-shadow: 0px 2px 15px 2px #ccc; border-radius: 11px;">

			<h2 style="text-align: center; color: #7f7f7f; ">
				فراموشی رمز عبور
			</h2>
			<form action=""   method="post" style=" margin-top: 50px;  ">
				<label style="color: #4f4f4f ">رمز عبور قبلی</label>
				<input type="password"  name="ex-pass" id="ex-pass" class="form-control  no-zoom"
					   autocomplete="off"
					   required="required"
					   oninvalid="setCustomValidity('رمز عبور')" onchange="try{setCustomValidity('')}catch(e){}"
					   title='رمز عبور قبلی'
					   placeholder="رمز عبور قبلی"><br>
				<label style="color: #4f4f4f ">رمز عبور جدید</label>
				<input type="password"  name="new-pass" id="new-pass" class="form-control  no-zoom"
					   autocomplete="off"
					   required="required"
					   oninvalid="setCustomValidity('رمز عبور')" onchange="try{setCustomValidity('')}catch(e){}"
					   title='رمز عبور جدید'
					   placeholder="رمز عبور جدید"><br>
				<label style="color: #4f4f4f "> تکرار رمز عبور</label>
				<input type="password"  name="password" id="re-new-pass" class="form-control  no-zoom" autocomplete="off"
					   required="required"
					   oninvalid="setCustomValidity('رمز عبور')" onchange="try{setCustomValidity('')}catch(e){}"
					   title='تکرار رمز عبور'
					   placeholder="تکرار رمز عبور">

				<br><br><br>

				<button class="btn btn-primary form-control" type="submit"
						style="outline: unset; border: none; width: 453px;
                        ">تغییر رمز عبور</button>
				<br><br>

			</form>


		</div>

	</div>
</div>
<script src="<?php echo base_url()?>assets/js/login.js?v=1.1.1"></script>


<script>
	$(document).ready(function () {
		$("#register2").hide();
		$("#forget").hide();
		$('#btn1').hide();
	});
	$('#password').keyup(function () {

		var password= $('#password').val();

		if (password.length>=6){
			$('#error_1').hide();
			$('#accept_1').show();
		}else {
			$('#error_1').show();
			$('#accept_1').hide();
		}
	});
	$('#re_password').keyup(function () {

		var password= $('#password').val();
		var re_password= $('#re_password').val();
		if (password.length>=6){
			if(re_password==password){

				$('#error_2').hide();
				$('#accept_2').show();

				$('#btn_accept_reg').show();

			}else {

				$('#error_2').show();
				$('#accept_2').hide();

				$('#btn_accept_reg').hide();
			}}

	});

	$('#register_btn').click(function(){
		$('#login').hide();
		$("#register2").show();
		$("#forget").hide();
	});
	$('#login_btn').click(function(){
		$('#login').show();
		$("#register2").hide();
		$("#forget").hide();
	});
	$('#forget-pass').click(function(){
		$('#login').hide();
		$("#register2").hide();
		$("#forget").show();
	});



	$("#btn").click(function () {
		alert("متن درون تصویر را صحیح وارد کنید");
	});

	$('#insert_capcha').keyup(function(event) {
		var text = $("#insert_capcha").val();
		var pass = $("#capcha").text();

		if(text==pass){
			$("#btn1").show();
			$("#btn").hide();
		}else {
			$("#btn").show();
			$("#btn1").hide();
		}
	});
	$('#refresh').mouseover(function () {
		$(this).css('color','#2c9ed4');
		$(this).css('font-size','25px');
		$(this).css('transform','rotate(5deg)');
		$(this).mouseleave(function () {
			$(this).css('color','#636262');
			$(this).css('font-size','20px');
			$(this).css('transform','rotate(-5deg)');

		});
	});
	$(document).on('click', '#refresh', function(e){
		var r=Math.random().toString(36).slice(7);
		$("#capcha").text(r);
		var myArray = ['#4287f5' ,'#9ec3ff','#f2abff','#b8ffbf','#ffd4b8','#ffada8','#f59ff3','#ff4fa4','#e0e0ff','#defcff'];
		var rand = myArray[Math.floor(Math.random() * myArray.length)];
		document.getElementById("cap_div").style.backgroundColor = rand;

		var element = $("#cap_div"); // global variable
		var getCanvas; // global variable
		html2canvas(element, {
			onrendered: function (canvas) {
				$("#cap_div").hide();
				getCanvas = canvas;
			}
		});
	});
	//-------------------
	var r=Math.random().toString(36).slice(7);
	$("#capcha").text(r);
	var myArray = ['#4287f5' ,'#9ec3ff','#f2abff','#b8ffbf','#ffd4b8','#ffada8','#f59ff3','#ff4fa4','#e0e0ff','#defcff'];
	var rand = myArray[Math.floor(Math.random() * myArray.length)];
	document.getElementById("cap_div").style.backgroundColor = rand;

	var element = $("#cap_div"); // global variable
	var getCanvas; // global variable
	html2canvas(element, {
		onrendered: function (canvas) {
			$("#cap_div").hide();
			getCanvas = canvas;
		}
	});



</script>
<script src="<?php echo base_url()?>assets/js/login.js"></script>
