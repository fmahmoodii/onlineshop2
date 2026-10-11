<?php if ($this->session->userdata('id')) { ?>

	<div class="container-fluid" id="content" >
		<div class="row" style="margin-top: 50px; margin-bottom: 60px">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div style="padding: 20px;border-radius: 10px; border: 1px solid #ccc">
					<form method="post" action='<?php echo base_url('admin/insert_discount') ?>'>

						<div class="form-group">
							<label for="code" class="required">کد تخفیف:</label><br>
							<input autocomplete="off" id="code" name="code"
								   class="form-control" type="text" placeholder="کد تخفیف"
								   value="<?php echo set_value('code'); ?>">
							<div hidden id="dp_code" class="form-control" style="width: 100%;
						 height: 50px;
						 border: 1px solid #cccccc;
						 display: block;
						 overflow-y: scroll;"></div>
							<span id="email_result"></span><br>
							<?php echo form_error('code','<span id="code_error" style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="mablagh" class="required">مبلغ تخفیف:</label><br>
							<input autocomplete="off" id="mablagh" name="mablagh"
								   class="form-control positive" type="number" min="0" placeholder="مبلغ تخفیف"
								   value="<?php echo set_value('mablagh'); ?>">
							<?php echo form_error('mablagh','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group start_date" style="display: inline-block; width: 270px">
							<label for="start_date" class="required">تاریخ شروع:</label>
							<?php echo form_error('start_date','<span style="color: red">','</span>') ?><br>
							<input readonly id="start_date" name="start_date"
								   class="form-control" placeholder="تاریخ شروع"
								   value="<?php echo set_value('start_date'); ?>"><br>
							<!-- ✅ تاریخ میلادی برای ذخیره در DB -->
							<input hidden id="datetime1" name="datetime1">
						</div>

						<div class="form-group end_date" style="display: inline-block; width: 270px">
							<label for="end_date" class="required">تاریخ پایان:</label>
							<?php echo form_error('end_date','<span style="color: red">','</span>') ?><br>
							<input readonly id="end_date" name="end_date"
								   class="form-control" placeholder="تاریخ پایان"
								   value="<?php echo set_value('end_date'); ?>"><br>
							<!-- ✅ تاریخ میلادی برای ذخیره در DB -->
							<input hidden id="datetime2" name="datetime2">
						</div>
						<br><br>

						<div class="form-group">
							<label for="min_price" class="required">حداقل خرید:</label><br>
							<input autocomplete="off" id="min_price" name="min_price"
								   class="form-control positive" type="number" min="0" placeholder="حداقل خرید"
								   value="<?php echo set_value('min_price'); ?>">
							<?php echo form_error('min_price','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="uses" class="required">تعداد دفعات استفاده:</label><br>
							<input autocomplete="off" id="uses" name="uses"
								   class="form-control positive" type="number" min="1"
								   placeholder="تعداد دفعات استفاده"
								   value="<?= set_value('uses', '1') ?>">
							<?php echo form_error('uses','<span style="color: red">','</span>') ?>
						</div>

						<br>

						<div class="form-group p_div" style="border: 3px solid #ccc; padding: 20px; background-color: #efe41b">
							<i class="fa fa-info-circle"
							   style="font-size: 26px; color: white"
							   title="برای انتخاب همه محصولات این فیلد را خالی بگذارید"
							></i>
							<label style="color: white; line-height: 26px; position: absolute; margin-right: 10px;">محصولات:</label>
							<br>
							<select multiple class="form-control products" id="products" name="products[]">
								<?php foreach ($products as $p):
									$label = $p->name . ' - ' . number_format($p->price ?? 0) . ' تومان';?>
									<option value="<?php echo $p->id; ?>" <?= set_select('products', $p->id, false) ?>>
										<?php echo htmlspecialchars($label); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<?php echo form_error('products','<span style="color: red">','</span>') ?>
						</div>

						<br>

						<div class="form-group u_div" style="border: 3px solid #ccc; padding: 20px; background-color: #1fcbe7">
							<i class="fa fa-info-circle"
							   style="font-size: 26px; color: white"
							   title="برای انتخاب همه کاربران این فیلد را خالی بگذارید"
							></i>
							<label style="color: white; line-height: 26px; position: absolute; margin-right: 10px;">کاربران:</label>
							<br>
							<select multiple class="form-control users" id="users" name="users[]">
								<?php
								// ✅ lookup map پروفایل برای جلوگیری از N+1
								$profile_map = [];
								foreach ($profiles as $prof) {
									$profile_map[$prof->user_id] = $prof;
								}
								foreach ($users as $u):
									$prof = $profile_map[$u->id] ?? null;
									$label = $prof
										? $prof->name . ' ' . $prof->family . ' - ' . $u->phone_number
										: $u->phone_number;
									?>
									<option value="<?php echo $u->id; ?>" <?= set_select('users', $u->id, false) ?>>
										<?php echo htmlspecialchars($label); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<?php echo form_error('users','<span style="color: red">','</span>') ?>
						</div>

						<br>

						<button class="btn btn-success" id="submit" style="width: 150px;">ثبت و ذخیره</button>
						<button type="button" id="limit_p" class="btn btn-default pull-left" style="margin-left: 5px; background-color: #efe41b; outline: unset;">انتخاب محصولات</button>
						<button type="button" id="limit_u" class="btn btn-default pull-left" style="margin-left: 5px; background-color: #1fcbe7; outline: unset;">انتخاب کاربران</button>

					</form>
				</div>
			</div>
			<div class="col-md-2"></div>
		</div>
	</div>

<?php } ?>

<script type="text/javascript">

	var to, from;

	to = $(".end_date").persianDatepicker({
		inline: true,
		altField: '#end_date',
		altFormat: 'LLLL',
		initialValue: false,
		timePicker: {
			enabled: true,
			meridiem: { enabled: true }
		},
		navigator: {
			scroll: { enabled: false },
			text: { btnNextText: 'بعدی', btnPrevText: 'قبلی' }
		},
		onSelect: function (unix) {
			const state = to.getState();
			var dt = new Date(state.view.unixDate);

			// ✅ فرمت Y-m-d H:i:s با zero-padding برای ذخیره صحیح در DB
			var year   = dt.getUTCFullYear();
			var month  = String(dt.getUTCMonth() + 1).padStart(2, '0');
			var day    = String(dt.getUTCDate()).padStart(2, '0');
			var hour   = String(dt.getHours()).padStart(2, '0');
			var minute = String(dt.getMinutes()).padStart(2, '0');
			var second = String(dt.getSeconds()).padStart(2, '0');

			$('#datetime2').val(year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second);

			to.touched = true;
			if (from && from.options && from.options.maxDate != unix) {
				var cachedValue = from.getState().selected.unixDate;
				from.options = {maxDate: unix};
				if (from.touched) { from.setDate(cachedValue); }
			}
		}
	});

	from = $(".start_date").persianDatepicker({
		inline: true,
		altField: '#start_date',
		altFormat: 'LLLL',
		initialValue: false,
		timePicker: {
			enabled: true,
			meridiem: { enabled: true }
		},
		navigator: {
			scroll: { enabled: false },
			text: { btnNextText: 'بعدی', btnPrevText: 'قبلی' }
		},
		onSelect: function (unix) {
			const state = from.getState();
			var dt = new Date(state.view.unixDate);

			// ✅ فرمت Y-m-d H:i:s با zero-padding
			var year   = dt.getUTCFullYear();
			var month  = String(dt.getUTCMonth() + 1).padStart(2, '0');
			var day    = String(dt.getUTCDate()).padStart(2, '0');
			var hour   = String(dt.getHours()).padStart(2, '0');
			var minute = String(dt.getMinutes()).padStart(2, '0');
			var second = String(dt.getSeconds()).padStart(2, '0');

			$('#datetime1').val(year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second);

			from.touched = true;
			if (to && to.options && to.options.minDate != unix) {
				var cachedValue = to.getState().selected.unixDate;
				to.options = {minDate: unix};
				if (to.touched) { to.setDate(cachedValue); }
			}
		}
	});

	$(document).on('click', '.tag', function (e) {
		let id = $(this).attr("id_c");
		let x  = $("#tag_" + id).text();
		$("#code").val(x);
	});

	$('#limit_p').click(function () {
		$(".p_div").toggle();
	});

	$('#limit_u').click(function () {
		$(".u_div").toggle();
	});

	$(document).ready(function () {

		$('.p_div').hide();
		$('.u_div').hide();

		$('.products, .users').select2({
			placeholder: 'انتخاب کنید...',
			language: { noResults: function () { return "موردی یافت نشد"; } },
			closeOnSelect: false,
		});

		// ✅ تولید خودکار کد تخفیف
		function rand_code(length) {
			var result     = '';
			var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			for (var i = 0; i < length; i++) {
				result += characters.charAt(Math.floor(Math.random() * characters.length));
			}
			return result;
		}
		$('#code').val('CODE-' + rand_code(5));

		$('#dp_code').hide();
		window.addEventListener('click', function (e) {
			if (!document.getElementById('dp_code').contains(e.target)) {
				$('#dp_code').hide();
			}
		});

		// ✅ چک تکراری بودن کد (real-time)
		$('#code').keyup(function () {
			if ($("#code_error").is(':visible')) { $("#code_error").hide(); }

			var code = $(this).val();
			if (code !== '') {
				// جستجو در dropdown
				$.ajax({
					url: "<?php echo base_url(); ?>admin/search_code",
					method: "POST",
					data: { query: code },
					success: function (data) {
						if (data == 0) { $('#dp_code').hide(); }
						else { $('#dp_code').show().html(data); }
					}
				});

				// ✅ چک تکراری با endpoint جدید
				$.ajax({
					url: "<?php echo base_url(); ?>admin/check_discount_code",
					method: "POST",
					data: { code: code },
					success: function (data) {
						$('#email_result').html(data);
					}
				});
			} else {
				$('#dp_code').hide();
				$('#email_result').html('');
			}
		});

	});

	$('.positive').keydown(function (e) {
		if (!((e.keyCode > 95 && e.keyCode < 106) ||
			(e.keyCode > 47 && e.keyCode < 58) ||
			e.keyCode == 8)) {
			return false;
		}
	});

	$('#code').on("input", function () {
		this.value = this.value.replace(/\s*/g, "").toUpperCase();
	});

</script>
