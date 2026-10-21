<?php if($this->session->userdata('id')){ ?>

	<div class="container-fluid" id="content" style="padding: 20px 0 70px 0">
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">

				<br>
				<label>کاربر: <small class="text-muted">(اختیاری — برای فروش حضوری/مهمان خالی بگذارید)</small></label>
				<select class="form-control" id="user_id">
					<option value="">— بدون کاربر (مهمان) —</option>
					<?php
					$profile_map = [];
					foreach ($profiles as $p) { $profile_map[$p->user_id] = $p; }
					foreach ($users as $u):
						$prof = $profile_map[$u->id] ?? null;
						$label = $prof ? $prof->name . ' ' . $prof->family . ' - ' . $u->phone_number : $u->phone_number;
						?>
						<option value="<?= $u->id ?>"><?= htmlspecialchars($label) ?></option>
					<?php endforeach; ?>
				</select>
				<span style="color:red" id="user_err"></span>

				<br><br><br>

				<table id="factor" class="table table-bordered table-striped">
					<thead>
					<tr>
						<th></th>
						<th>نام محصول</th>
						<th width="8%">تعداد</th>
						<th width="10%">قیمت واحد</th>
						<th>مجموع</th>
						<th width="8%">% تخفیف</th>
						<th>مبلغ تخفیف</th>
						<th>قیمت نهایی</th>
						<th width="12%">انبار (اختیاری)</th>
					</tr>
					</thead>

					<tr style="background-color:#d7eddd">
						<td style="width:60px">
							<button id="add_row" type="button" class="btn btn-success"><i class="fa fa-plus"></i></button>
						</td>
						<td>
							<select id="new_products" class="form-control">
								<option selected value=""></option>
								<?php
								$prod_map = $prod_active_map = [];
								foreach ($products as $p) {
									$prod_map[$p->code] = $p->name;
									$prod_active_map[$p->code] = $p->isActive;
								}

								$model_map = $jens_map = $size_map = $brand_map = $color_map = [];
								foreach ($model as $r) { $model_map[$r->id] = $r->name; }
								foreach ($jens  as $r) { $jens_map[$r->id]  = $r->name; }
								foreach ($size  as $r) { $size_map[$r->id]  = $r->name; }
								foreach ($brand as $r) { $brand_map[$r->id] = $r->name; }
								foreach ($color as $r) { $color_map[$r->id] = $r->name; }

								foreach ($attributes as $pa):
									$prod_name = $prod_map[$pa->code_p] ?? '—';
									$attrs = [];
									if (!empty($pa->model) && isset($model_map[$pa->model])) $attrs[] = 'مدل:' . $model_map[$pa->model];
									if (!empty($pa->jens)  && isset($jens_map[$pa->jens]))   $attrs[] = 'جنس:' . $jens_map[$pa->jens];
									if (!empty($pa->size)  && isset($size_map[$pa->size]))   $attrs[] = 'سایز:' . $size_map[$pa->size];
									if (!empty($pa->brand) && isset($brand_map[$pa->brand])) $attrs[] = 'برند:' . $brand_map[$pa->brand];
									if (!empty($pa->color) && isset($color_map[$pa->color])) $attrs[] = 'رنگ:' . $color_map[$pa->color];
									$label = $prod_name . ' - ' . number_format($pa->price);
									if (!empty($attrs)) $label .= ' (' . implode(' ', $attrs) . ')';
									$is_inactive = empty($prod_active_map[$pa->code_p]) || $pa->isActive == 0;
									if ($is_inactive) $label .= ' — غیرفعال';
									?>
									<option value="<?= $pa->id ?>"
											price="<?= $pa->price ?>"
											off_percent="<?= $pa->off_percent ?>"
											name="<?= htmlspecialchars($prod_name) ?>"
										<?= $is_inactive ? 'style="color:#999;"' : '' ?>>
										<?= htmlspecialchars($label) ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
						<td><input id="new_qty" class="form-control" type="number" min="1" value=""></td>
						<td><input id="new_price" class="form-control" type="number" min="0" value="" readonly title="قیمت از سیستم خوانده می‌شود"></td>
						<td><span id="new_total_price1"></span></td>
						<td><input id="new_off_percent" class="form-control" type="number" min="0" max="100" value=""></td>
						<td><span id="new_off_price"></span></td>
						<td><span id="new_total_price"></span></td>
						<td>
							<select id="new_inventory" class="form-control">
								<option value="">— بدون کسر —</option>
							</select>
						</td>
					</tr>

					<tbody id="rows_body"></tbody>

					<tbody>
					<tr>
						<td></td>
						<td colspan="7">جمع کل</td>
						<td><span id="final_price">0</span></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="7">
							کد تخفیف
							<span id="off_price_label"></span>
						</td>
						<td>
							<select id="discount_id" class="form-control">
								<option value="">خالی</option>
								<?php
								$today = date('Y-m-d');
								foreach ($discounts as $d):
									$expired = !empty($d->end_date) && $d->end_date < $today;
									if ($expired) continue;
									?>
									<option value="<?= $d->id ?>" data-mablagh="<?= $d->mablagh ?>" data-min="<?= $d->min_price ?>">
										<?= htmlspecialchars($d->code) ?> (<?= number_format($d->mablagh) ?> تومان)
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="7">قابل پرداخت</td>
						<td><span id="payment">0</span></td>
					</tr>
					</tbody>
				</table>

				<span style="color:red" id="items_err"></span>

				<br>
				<div style="border-radius:10px; border:1px solid #e0dfdf; padding:20px;">
					<label class="required">گیرنده:</label>
					<input id="name" class="form-control"><br>
					<label>آدرس:</label>
					<input id="address" class="form-control"><br>
					<label>کدپستی:</label>
					<input id="postal_code" class="form-control"><br>
					<label>تلفن:</label>
					<input id="phone_number" class="form-control"><br>
				</div>
				<span style="color:red" id="shipping_err"></span>

				<br><br>

				<button id="accept" class="btn btn-success">تایید و ذخیره</button>
				<a href="<?php echo base_url(); ?>admin/sales"><button type="button" class="btn btn-danger">بازگشت</button></a>

			</div>
			<div class="col-md-1"></div>
		</div>
	</div>

<?php } ?>

<script>

	// ✅ آرایه‌ای که آیتم‌های فاکتور رو نگه می‌داره (مثل روش ورود محصول)
	var factorRows = [];

	// ✅ پر کردن اطلاعات گیرنده با انتخاب کاربر (یا پاک کردن موقع انتخاب مهمان)
	$(document).on('change', '#user_id', function () {
		var user_id = $(this).val();

		if (!user_id) {
			// ✅ حالت مهمان — فیلدها رو پاک کن تا ادمین دستی پر کنه
			$('#name, #address, #postal_code, #phone_number').val('');
			return;
		}

		$.ajax({
			url: "<?php echo base_url(); ?>admin/get_profile",
			method: "POST",
			data: { user_id: user_id },
			dataType: 'json',
			success: function (response) {
				if (response.status == 1) {
					$('#name').val(response.name || '');
					$('#address').val(response.address || '');
					$('#postal_code').val(response.postal_code || '');
					$('#phone_number').val(response.phone_number || '');
				} else {
					$('#name, #address, #postal_code, #phone_number').val('');
				}
			},
			error: function () {
				$('#name, #address, #postal_code, #phone_number').val('');
			}
		});
	});

	// ✅ وقتی محصول انتخاب می‌شه، قیمت رو از option می‌خونه (فقط نمایشی) + درصد تخفیف پیش‌فرض صفر
	$(document).on('change', '#new_products', function () {
		var attr_id = $(this).val();

		if (attr_id === '') {
			clearNewRow();
			$('#new_inventory').html('<option value="">— بدون کسر —</option>');
			return;
		}
		var price = $(this).find(':selected').attr('price');

		$('#new_qty').val(1);
		$('#new_price').val(price);
		$('#new_off_percent').val(0); // ✅ همیشه پیش‌فرض صفر، ادمین خودش دستی تغییر می‌ده

		calcNewRow();

		// ✅ فقط انبارهایی که این محصول توشون موجودی داره رو بیار
		$('#new_inventory').html('<option value="">در حال بارگذاری...</option>');
		$.ajax({
			url: "<?php echo base_url(); ?>admin/get_attr_inventories",
			method: "POST",
			data: { attr_id: attr_id },
			dataType: 'json',
			success: function (response) {
				var options = '<option value="">— بدون کسر —</option>';
				if (response.status == 1 && response.inventories.length > 0) {
					response.inventories.forEach(function (inv) {
						options += '<option value="' + inv.id + '">' + inv.name + ' (موجودی: ' + inv.qty.toLocaleString() + ')</option>';
					});
				} else {
					options += '<option value="" disabled>هیچ انباری موجودی ندارد</option>';
				}
				$('#new_inventory').html(options);
			},
			error: function () {
				$('#new_inventory').html('<option value="">— بدون کسر —</option>');
			}
		});
	});

	$(document).on('input', '#new_qty, #new_off_percent', calcNewRow);

	function calcNewRow() {
		var qty   = parseFloat($('#new_qty').val()) || 0;
		var price = parseFloat($('#new_price').val()) || 0;
		var off   = parseFloat($('#new_off_percent').val()) || 0;

		if ($('#new_products').val() === '') { clearNewRow(); return; }

		var total1 = qty * price;
		var offp   = total1 * (off / 100);
		var total  = total1 - offp;

		$('#new_total_price1').text(total1.toLocaleString());
		$('#new_off_price').text(offp.toLocaleString());
		$('#new_total_price').text(total.toLocaleString());
	}

	function clearNewRow() {
		$('#new_total_price1, #new_off_price, #new_total_price').text('');
	}

	// ✅ افزودن ردیف به آرایه + رندر جدول
	$('#add_row').on('click', function () {
		var attr_id = $('#new_products').val();
		if (!attr_id) { alert('یک محصول انتخاب کنید'); return; }

		var qty = parseInt($('#new_qty').val());
		if (!qty || qty <= 0) { alert('تعداد را وارد کنید'); return; }

		var row = {
			attr_id:      attr_id,
			product_name: $('#new_products').find(':selected').attr('name'),
			price:        parseFloat($('#new_price').val()),
			qty:          qty,
			off_percent:  parseFloat($('#new_off_percent').val()) || 0,
			inventory_id: $('#new_inventory').val(),
			inventory_label: $('#new_inventory').find(':selected').text()
		};

		factorRows.push(row);
		renderRows();

		$('#new_products').val('');
		$('#new_qty, #new_price, #new_off_percent').val('');
		$('#new_inventory').val('');
		clearNewRow();
	});

	function renderRows() {
		$('#rows_body').empty();
		$('#items_err').text('');

		factorRows.forEach(function (row, index) {
			var total1 = row.qty * row.price;
			var offp   = total1 * (row.off_percent / 100);
			var total  = total1 - offp;
			row._total_price = total; // ذخیره برای محاسبه جمع کل

			var tr = '<tr>' +
				'<td><button class="btn btn-danger btn-sm del_row" data-index="' + index + '"><i class="fa fa-minus"></i></button></td>' +
				'<td>' + row.product_name + '</td>' +
				'<td>' + row.qty + '</td>' +
				'<td>' + row.price.toLocaleString() + '</td>' +
				'<td>' + total1.toLocaleString() + '</td>' +
				'<td>' + row.off_percent + '%</td>' +
				'<td>' + offp.toLocaleString() + '</td>' +
				'<td>' + total.toLocaleString() + '</td>' +
				'<td>' + (row.inventory_id ? row.inventory_label : '<span class="text-muted">بدون کسر</span>') + '</td>' +
				'</tr>';

			$('#rows_body').append(tr);
		});

		calcTotals();
	}

	$(document).on('click', '.del_row', function () {
		var index = parseInt($(this).data('index'));
		factorRows.splice(index, 1);
		renderRows();
	});

	// ✅ محاسبه جمع کل + تخفیف + مبلغ قابل پرداخت
	function calcTotals() {
		var subtotal = 0;
		factorRows.forEach(function (row) { subtotal += row._total_price; });

		var discountMablagh = parseFloat($('#discount_id').find(':selected').data('mablagh')) || 0;

		if (discountMablagh > 0) {
			$('#off_price_label').text('(' + discountMablagh.toLocaleString() + ' تومان)');
		} else {
			$('#off_price_label').text('');
		}

		var payment = subtotal - discountMablagh;
		if (payment < 0) payment = 0;

		$('#final_price').text(subtotal.toLocaleString());
		$('#payment').text(payment.toLocaleString());
	}

	$(document).on('change', '#discount_id', calcTotals);

	// ================== ثبت نهایی ==================

	$('#accept').on('click', function () {
		$('#user_err, #items_err, #shipping_err').text('');

		var user_id = $('#user_id').val(); // ✅ می‌تونه خالی باشه (مهمان)

		if (factorRows.length === 0) {
			$('#items_err').text('حداقل یک آیتم به فاکتور اضافه کنید');
			return;
		}

		var name         = $('#name').val().trim();
		var address      = $('#address').val().trim();
		var postal_code  = $('#postal_code').val().trim();
		var phone_number = $('#phone_number').val().trim();

		if (!name) {
			$('#shipping_err').text('نام گیرنده الزامی است');
			return;
		}

		// ✅ فقط فیلدهای لازم برای ارسال
		var sendItems = factorRows.map(function (r) {
			return {
				attr_id:      r.attr_id,
				qty:          r.qty,
				off_percent:  r.off_percent,
				inventory_id: r.inventory_id || null
				// ✅ price ارسال نمی‌شه، سرور خودش از product_attributes می‌خونه
			};
		});

		$('#accept').prop('disabled', true).text('در حال ثبت...');

		$.ajax({
			url: "<?php echo base_url(); ?>admin/insert_order",
			method: "POST",
			data: {
				user_id:      user_id,
				discount_id:  $('#discount_id').val(),
				items:        JSON.stringify(sendItems),
				name:         name,
				address:      address,
				postal_code:  postal_code,
				phone_number: phone_number
			},
			dataType: 'json',
			success: function (response) {
				if (response.status == 1) {
					alert('فاکتور با موفقیت ثبت شد — کد سفارش: ' + response.order_code);
					window.location.href = "<?php echo base_url(); ?>admin/sales";
				} else if (response.message === 'session_expired') {
					alert('نشست منقضی شده. لطفاً دوباره وارد شوید.');
					window.location.href = "<?php echo base_url(); ?>admin/login_page";
				} else {
					alert(response.message || 'خطا در ثبت فاکتور');
					$('#accept').prop('disabled', false).text('تایید و ذخیره');
				}
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
				$('#accept').prop('disabled', false).text('تایید و ذخیره');
			}
		});
	});

</script>
