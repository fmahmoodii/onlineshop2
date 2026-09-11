<?php if($this->session->userdata('id')){ ?>

	<!-- ✅ Snackbar برای پیام‌های موفقیت/خطا -->
	<div id="snackbar_success" class="snackbar">عملیات با موفقیت انجام شد</div>
	<div id="snackbar_error" class="snackbar">خرابی رخ داد</div>

	<div class="container-fluid" id="content">
		<div class="row" style="margin-top: 50px">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div style="padding: 20px;border-radius: 10px; border: 1px solid #ccc">
					<?php if (isset($profile[0])){foreach ($profile as $p){?>

						<?php echo form_open('admin/edit_u/' . $p->user_id); ?>

						<input hidden id="id" name="id" value="<?php echo $p->id?>">

						<div class="form-group">
							<label for="role" class="required">نوع کاربر:</label>

							<select class="form-control" id="role" name="role">
								<?php foreach ($roles as $ro) { ?>
									<option value="<?php echo $ro->id; ?>"
										<?php echo (isset($user_data[0]) && $ro->id == $user_data[0]->role_id) ? 'selected' : ''; ?>>
										<?= htmlspecialchars($ro->role_name) ?>
									</option>
								<?php } ?>
							</select>

							<?php echo form_error('role','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="name" class="">نام:</label>
							<span style="color: red" id="name_err"></span>
							<input autocomplete="off" id="name" name="name" type="text" class="form-control" value="<?php echo form_error('name') ? set_value('name') : htmlspecialchars($p->name); ?>" placeholder="نام">
							<?php echo form_error('name','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="family" class="">نام خانوادگی:</label>
							<span style="color: red" id="family_err"></span>
							<input autocomplete="off" id="family" name="family" type="text" class="form-control" value="<?php echo form_error('family') ? set_value('family') : htmlspecialchars($p->family); ?>" placeholder="نام خانوادگی">
							<?php echo form_error('family','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="phone_number" class="required">شماره موبایل:</label>
							<input readonly autocomplete="off" id="phone_number" name="phone_number" type="text" class="form-control positive" value="<?php echo htmlspecialchars($users[0]->phone_number); ?>" placeholder="شماره موبایل">
						</div>

						<div class="form-group">
							<label for="phone_number1" class="">شماره موبایل ضروری:</label>
							<span style="color: red" id="ph1_err"></span>
							<input maxlength="11" autocomplete="off" id="phone_number1" name="phone_number1" type="text"
								   class="form-control positive" value="<?php echo form_error('phone_number1') ? set_value('phone_number1') : htmlspecialchars($p->reciever_phone_number); ?>" placeholder="شماره گیرنده">
							<?php echo form_error('phone_number1','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="ostan" class="">استان:</label>
							<select class="form-control" id="ostan" name="ostan">
								<option value="">انتخاب کنید..</option>
								<?php foreach ($province as $pro): ?>
									<option value="<?php echo $pro->id; ?>" <?php echo ($pro->id == $p->ostan) ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($pro->name); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<?php echo form_error('ostan','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="city" class="">شهر:</label>
							<select class="form-control" id="city" name="city">
								<option value="">انتخاب کنید</option>
								<?php foreach ($city as $ci): ?>
									<?php if ($ci->province_id == $p->ostan): ?>
										<option value="<?php echo $ci->id; ?>" <?php echo ($ci->id == $p->city) ? 'selected' : ''; ?>>
											<?php echo htmlspecialchars($ci->name); ?>
										</option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
							<?php echo form_error('city','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="address" class="">آدرس:</label>
							<span style="color: red" id="add_err"></span>
							<input autocomplete="off" id="address" name="address" type="text" class="form-control" value="<?php echo form_error('address') ? set_value('address') : htmlspecialchars($p->address); ?>" placeholder="آدرس">
							<?php echo form_error('address','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="postal_code" class="">کدپستی:</label>
							<span style="color: red" id="pcode_err"></span>
							<input autocomplete="off" id="postal_code" name="postal_code" type="text" class="form-control" value="<?php echo form_error('postal_code') ? set_value('postal_code') : htmlspecialchars($p->postal_code); ?>" placeholder="کدپستی">
							<?php echo form_error('postal_code','<span style="color: red">','</span>') ?><br>
						</div>

						<button type="submit" class="btn btn-success" id="submit" style="width: 150px; ">ثبت و ذخیره</button>

						<?php echo form_close(); ?>

					<?php }}?>
				</div>
			</div>
			<div class="col-md-2"></div>
		</div>
	</div>
<?php }?>

<script>

	$(document).ready(function () {
		// ✅ نمایش پیام موفقیت/خطا فقط اگه واقعاً از سرور اومده باشه
		<?php if ($this->session->flashdata('success')): ?>
		showSnackbar('success');
		<?php endif; ?>

		<?php if ($this->session->flashdata('error')): ?>
		showSnackbar('error');
		<?php endif; ?>
	});

	function showSnackbar(type) {
		var el = document.getElementById('snackbar_' + type);
		if (!el) return;
		el.className = 'snackbar show';
		setTimeout(function () {
			el.className = el.className.replace('show', '');
		}, 3000);
	}

	//---------get_city-----------
	$('#ostan').change(function(){
		var province_id = $("#ostan").val();

		if (!province_id) {
			$('#city').html('<option value="">انتخاب کنید</option>');
			return;
		}

		$.post('<?php echo base_url();?>admin/get_city',{'province_id':province_id},
			function(data){
				$('#city').html(data);
			});
	});

	$('#name, #family').on("input",function() {
		//چک کردن حروف فارسی. جلوگیری از اسپیس اول. جلوگیری از اسپیس های چنتایی
		this.value = this.value.replace(/[^\u0600-\u06FF0-9 ]/g,'').replace(/\s\s+/g, ' ').replace(/^\s+/g, '').replace(/^[\d .-]+/g,'');
	});

	$('#name, #family').on("keypress",function(event) {

		var errorEl = $(this).attr('id') === 'name' ? $('#name_err') : $('#family_err');

		if ((event.keyCode >= 48 && event.keyCode <= 57) ||
			(event.keyCode >= 65 && event.keyCode <= 90) ||
			(event.keyCode >= 97 && event.keyCode <= 122)) {

			errorEl.show();
			if (event.keyCode >= 48 && event.keyCode <= 57) {
				errorEl.html('فقط از حروف فارسی استفاده کنید');
			} else {
				errorEl.html('زبان کیبورد را فارسی کنید');
			}
		} else {
			errorEl.hide();
		}
	});

	// ✅ بررسی شماره موبایل ضروری (یکبار، بدون تکرار keyup/on-input)
	$('#phone_number1').on('input', function(){
		var pn = $(this).val();
		var regex = /^(0)?9\d{9}$/;
		var error = $('#ph1_err');

		error.html('').css('color', '');

		if (pn.length === 0) {
			return; // اختیاری است
		}

		if (pn.length === 10 || pn.length === 11) {
			if (regex.test(pn)) {
				error.html('شماره معتبر است').css('color', 'green');
			} else {
				error.html('شماره وارد شده نادرست است').css('color', 'red');
			}
		} else if (pn.length > 11) {
			error.html('تعداد ارقام بیشتر از حد مجاز است').css('color', 'red');
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
