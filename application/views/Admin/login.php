<style>

	.noselect {
		-webkit-touch-callout: none; /* iOS Safari */
		-webkit-user-select: none; /* Safari */
		-khtml-user-select: none; /* Konqueror HTML */
		-moz-user-select: none; /* Old versions of Firefox */
		-ms-user-select: none; /* Internet Explorer/Edge */
		user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome, Edge, Opera and Firefox */
	}


</style>
<div class="container-fluid">
	<div class="row" style="padding: 50px;">
		<?php if($this->session->flashdata('err')){ ?>
		<div class="alert alert-warning" style="text-align: center;">
			رمز عبور و نام کاربری شما صحیح نمی باشد
		</div>
		<?php }?>
		<!--login-form-->
		<div id="login" style=" margin: 0 auto;padding: 20px; width: 500px;
		height: auto;box-shadow: 0px 2px 15px 2px #ccc; border-radius: 11px;">

			<h2 style="text-align: center; color: #7f7f7f; ">
				ورود به سیستم
			</h2>

			<form action="<?php echo base_url()?>admin/login"  method="post" style=" margin-top: 50px;  ">
				<label style="color: #4f4f4f ">شناسه کاربری:</label>
				<input type="text" min="6" maxlength="6" name="user_name" id="user_name" class="form-control  no-zoom"
					   autocomplete="off" required="required" title="شناسه کاربری"
					   oninvalid="setCustomValidity('شناسه کاربری خود را وارد کنید')"
					   onchange="try{setCustomValidity('')}catch(e){}"
					   placeholder="شناسه کاربری"><br>
				<label style="color: #4f4f4f ">رمز عبور:</label>
				<input type="password" min="6" maxlength="6" name="password" id="password" class="form-control  no-zoom" autocomplete="off"
					   required="required" oninvalid="setCustomValidity('رمز عبور')"
					   onchange="try{setCustomValidity('')}catch(e){}" title='رمز عبور '
					   placeholder="رمز عبور">
				<br>

				<br><br>
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

			</form>


		</div>

	</div>
</div>
<script src="<?php echo base_url()?>assets/js/login.js?v=1.1.1"></script>
<script>
	$(document).ready(function () {
		$('#btn').hide();
	})
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
