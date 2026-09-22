<?php if($this->session->userdata('id')){ ?>

	<div class="container-fluid" id="content">
		<div class="row" style="margin: 60px">
			<div style="border: 1px solid #ccc; padding: 20px; border-radius: 10px;">

				<!-- ✅ کد یکتا محصول -->
				<input type="hidden" id="code" value="<?php echo $code; ?>">

				<!-- ✅ دسته‌بندی (جدید - n سطحی) -->
				<div class="form-group">
					<label>انتخاب دسته:</label>
					<select id="id_cat" class="form-control">
						<option value="">— انتخاب کنید —</option>
						<?php foreach ($categories as $cat): ?>
							<option value="<?php echo $cat->id; ?>"
									data-depth="<?php echo $cat->parentId == '0' ? 0 : 1; ?>">
								<?php
								// نمایش تورفتگی ساده بر اساس parentId
								echo ($cat->parentId != '0' ? '&nbsp;&nbsp;&nbsp;&nbsp;└─ ' : '') . htmlspecialchars($cat->name_cat);
								?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<!-- نام محصول -->
				<div class="form-group">
					<label class="required">نام محصول:</label>
					<input autocomplete="off" id="name" type="text" class="form-control" placeholder="نام محصول">
					<span style="color:red" id="name_err"></span>
				</div>

				<hr>

				<!-- ✅ بخش attributes -->
				<div class="row" id="show_item">
					<div class="col-md-12">
						<label>ویژگی‌های محصول:</label>
						<div style="margin-bottom: 10px;">
							<label><input type="checkbox" name="model_checkbox" id="model_checkbox"> مدل</label>&nbsp;&nbsp;
							<label><input type="checkbox" name="jens_checkbox" id="jens_checkbox"> جنس</label>&nbsp;&nbsp;
							<label><input type="checkbox" name="size_checkbox" id="size_checkbox"> سایز</label>&nbsp;&nbsp;
							<label><input type="checkbox" name="brand_checkbox" id="brand_checkbox"> برند</label>&nbsp;&nbsp;
							<label><input type="checkbox" name="color_checkbox" id="color_checkbox"> رنگ</label>
						</div>

						<div style="display: flex; flex-wrap: wrap; gap: 5px; align-items: center;">
							<select class="form-control" id="model" style="width: 12%; display: none;">
								<option value="">مدل</option>
								<?php foreach ($model as $mo): ?>
									<option value="<?php echo $mo->id; ?>"><?php echo htmlspecialchars($mo->name); ?></option>
								<?php endforeach; ?>
							</select>

							<select class="form-control" id="jens" style="width: 12%; display: none;">
								<option value="">جنس</option>
								<?php foreach ($jens as $je): ?>
									<option value="<?php echo $je->id; ?>"><?php echo htmlspecialchars($je->name); ?></option>
								<?php endforeach; ?>
							</select>

							<select class="form-control" id="size" style="width: 12%; display: none;">
								<option value="">سایز</option>
								<?php foreach ($size as $si): ?>
									<option value="<?php echo $si->id; ?>"><?php echo htmlspecialchars($si->name); ?></option>
								<?php endforeach; ?>
							</select>

							<select class="form-control" id="brand" style="width: 12%; display: none;">
								<option value="">برند</option>
								<?php foreach ($brand as $br): ?>
									<option value="<?php echo $br->id; ?>"><?php echo htmlspecialchars($br->name); ?></option>
								<?php endforeach; ?>
							</select>

							<select class="form-control" id="color" style="width: 12%; display: none;">
								<option value="">رنگ</option>
								<?php foreach ($color as $co): ?>
									<option value="<?php echo $co->id; ?>" style="background-color: <?php echo $co->color_code ?>">
										<?php echo htmlspecialchars($co->name); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<input autocomplete="off" id="buy_price" type="number" min="0" class="form-control positive" placeholder="قیمت خرید" style="width: 12%;">
							<input autocomplete="off" id="price" type="number" min="0" class="form-control positive" placeholder="قیمت *" style="width: 12%;">
							<input autocomplete="off" id="off_percent" type="number" min="0" max="100" class="form-control positive" placeholder="% تخفیف" style="width: 8%;">
							<input autocomplete="off" id="supply" type="number" min="0" class="form-control positive" placeholder="موجودی" style="width: 10%;">

							<button id="add_row" class="btn btn-success" type="button">
								<i class="fa fa-plus"></i> افزودن ردیف
							</button>
						</div>
					</div>
				</div>

				<br>

				<!-- ✅ جدول نمایش attributes -->
				<div class="row" id="results">
					<div class="col-md-12">
						<table class="table table-bordered table-striped" id="attr-tb">
							<thead>
							<tr>
								<th></th>
								<th class="model_cells" style="display:none;">مدل</th>
								<th class="jens_cells" style="display:none;">جنس</th>
								<th class="size_cells" style="display:none;">سایز</th>
								<th class="brand_cells" style="display:none;">برند</th>
								<th class="color_cells" style="display:none;">رنگ</th>
								<th>قیمت خرید</th>
								<th>قیمت</th>
								<th>تخفیف</th>
								<th>موجودی</th>
								<th>فعال</th>
								<th>پیش‌فرض</th>
							</tr>
							</thead>
							<tbody id="t-body"></tbody>
						</table>
						<span style="color:red" id="attr_err"></span>
					</div>
				</div>

				<hr>

				<!-- توضیحات -->
				<div class="form-group">
					<label>توضیحات:</label>
					<textarea id="text1" class="ckeditor" rows="10" cols="70" name="text1"></textarea>
				</div>

				<!-- ✅ آپلود عکس -->
				<div class="form-group">
					<label>تصاویر محصول:</label>
					<input id="files_image" type="file" multiple accept="image/*"><br>
					<small class="text-muted">حداکثر ۵ تصویر — فرمت‌های مجاز: jpg, jpeg, png, gif, webp</small>
					<div id="image_prd" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;"></div>
				</div>

				<br>

				<!-- دکمه ثبت نهایی -->
				<button id="accept11" class="btn btn-success btn-lg" type="button">
					<i class="fa fa-check"></i> ثبت محصول
				</button>
				<span style="color:red; margin-right: 10px;" id="submit_err"></span>

			</div>
		</div>
	</div>

<?php }?>

<script>

	// ✅ آرایه‌ای که attribute ها رو توی JS نگه می‌داره (تا موقع ثبت نهایی)
	var attrRows = [];

	$(document).ready(function () {
		// نمایش/مخفی کردن ستون‌های attribute
		$('.model_cells, #model').hide();
		$('.jens_cells, #jens').hide();
		$('.size_cells, #size').hide();
		$('.brand_cells, #brand').hide();
		$('.color_cells, #color').hide();
	});

	// ================== نمایش/مخفی چک‌باکس ویژگی‌ها ==================

	$(document).on('change', '#model_checkbox,#jens_checkbox,#size_checkbox,#brand_checkbox,#color_checkbox', function () {
		var key = $(this).attr('id').replace('_checkbox', '');
		var isChecked = $(this).prop('checked');
		$('#' + key).toggle(isChecked);
		$('.' + key + '_cells').toggle(isChecked);
	});

	// ================== افزودن ردیف attribute ==================

	$('#add_row').on('click', function () {
		var price = $('#price').val();
		if (!price || parseFloat(price) <= 0) {
			alert('قیمت محصول را وارد کنید');
			return;
		}

		var model_id    = $('#model').val() || '';
		var jens_id     = $('#jens').val() || '';
		var size_id     = $('#size').val() || '';
		var brand_id    = $('#brand').val() || '';
		var color_id    = $('#color').val() || '';
		var buy_price   = $('#buy_price').val() || '';
		var off_percent = $('#off_percent').val() || '';
		var supply      = $('#supply').val() || '0';

		// ✅ گرفتن نام از سرور (یه کوئری برای همه)
		$.ajax({
			url: "<?= base_url('admin/get_attr_labels') ?>",
			method: "POST",
			data: {
				model: model_id, jens: jens_id, size: size_id,
				brand: brand_id, color: color_id
			},
			success: function (response) {
				var res = JSON.parse(response);
				if (res.status != 1) { alert('خطا در دریافت اطلاعات'); return; }

				// ✅ تعیین default: اگه هنوز ردیفی نیست، این ردیف default میشه
				var isDefault = attrRows.length === 0 ? '1' : '0';

				var row = {
					model:       model_id,
					jens:        jens_id,
					size:        size_id,
					brand:       brand_id,
					color:       color_id,
					buy_price:   buy_price,
					price:       price,
					off_percent: off_percent,
					supply:      supply,
					isActive:    '1',
					'default':   isDefault,
					// برای نمایش:
					model_name:  res.model,
					jens_name:   res.jens,
					size_name:   res.size,
					brand_name:  res.brand,
					color_name:  res.color_name,
					color_code:  res.color_code
				};

				attrRows.push(row);
				renderAttrTable();
				clearAttrInputs();
			}
		});
	});

	// ✅ رندر جدول از آرایه attrRows
	function renderAttrTable() {
		$('#t-body').empty();
		$('#attr_err').text('');

		attrRows.forEach(function (row, index) {
			var colorCell = '';
			if (row.color_code) {
				colorCell = '<span style="height:16px;width:16px;border:1px solid #000;border-radius:50%;display:inline-block;background:' + row.color_code + ';"></span> ' + row.color_name;
			} else {
				colorCell = row.color_name;
			}

			var tr = '<tr>' +
				'<td><button class="btn btn-danger btn-xs del_row" data-index="' + index + '"><i class="fa fa-minus"></i></button></td>' +
				'<td class="model_cells" style="' + ($('#model_checkbox').prop('checked') ? '' : 'display:none;') + '">' + (row.model_name || '') + '</td>' +
				'<td class="jens_cells" style="' + ($('#jens_checkbox').prop('checked') ? '' : 'display:none;') + '">' + (row.jens_name || '') + '</td>' +
				'<td class="size_cells" style="' + ($('#size_checkbox').prop('checked') ? '' : 'display:none;') + '">' + (row.size_name || '') + '</td>' +
				'<td class="brand_cells" style="' + ($('#brand_checkbox').prop('checked') ? '' : 'display:none;') + '">' + (row.brand_name || '') + '</td>' +
				'<td class="color_cells" style="' + ($('#color_checkbox').prop('checked') ? '' : 'display:none;') + '">' + colorCell + '</td>' +
				'<td>' + (row.buy_price || '—') + '</td>' +
				'<td>' + row.price + '</td>' +
				'<td>' + (row.off_percent ? row.off_percent + '%' : '—') + '</td>' +
				'<td>' + row.supply + '</td>' +
				'<td><input type="checkbox" class="isActive_cb" data-index="' + index + '" ' + (row.isActive == '1' ? 'checked' : '') + '></td>' +
				'<td><input type="radio" name="default_radio" class="default_rb" data-index="' + index + '" ' + (row['default'] == '1' ? 'checked' : '') + '></td>' +
				'</tr>';

			$('#t-body').append(tr);
		});
	}

	function clearAttrInputs() {
		$('#model, #jens, #size, #brand, #color').val('');
		$('#buy_price, #price, #off_percent, #supply').val('');
	}

	// ✅ حذف ردیف
	$(document).on('click', '.del_row', function () {
		var index = parseInt($(this).data('index'));

		// اگه ردیف default بود و ردیف دیگه‌ای هست، اجازه حذف نیست
		if (attrRows[index]['default'] == '1' && attrRows.length > 1) {
			alert('ردیف پیش‌فرض قابل حذف نیست. ابتدا ردیف دیگری را پیش‌فرض کنید.');
			return;
		}

		attrRows.splice(index, 1);

		// اگه هیچ ردیفی default نیست، اولی رو default کن
		if (attrRows.length > 0 && !attrRows.some(function (r) { return r['default'] == '1'; })) {
			attrRows[0]['default'] = '1';
			attrRows[0]['isActive'] = '1';
		}

		renderAttrTable();
	});

	// ✅ تغییر default
	$(document).on('change', '.default_rb', function () {
		var index = parseInt($(this).data('index'));
		attrRows.forEach(function (r) { r['default'] = '0'; });
		attrRows[index]['default'] = '1';
		attrRows[index]['isActive'] = '1'; // default همیشه فعاله
		renderAttrTable();
	});

	// ✅ تغییر isActive
	$(document).on('change', '.isActive_cb', function () {
		var index = parseInt($(this).data('index'));
		if (attrRows[index]['default'] == '1') {
			$(this).prop('checked', true); // default نمی‌تونه غیرفعال بشه
			return;
		}
		attrRows[index]['isActive'] = $(this).prop('checked') ? '1' : '0';
	});

	// ================== آپلود عکس ==================

	$('#files_image').on('change', function () {
		var code  = $('#code').val();
		var files = this.files;

		// چک تعداد کل
		var currentCount = $('#image_prd .img-preview').length;
		if (currentCount + files.length > 5) {
			alert('حداکثر ۵ تصویر مجاز است');
			$(this).val('');
			return;
		}

		var formData = new FormData();
		var hasError = false;
		var allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

		for (var i = 0; i < files.length; i++) {
			var ext = files[i].name.split('.').pop().toLowerCase();
			if (allowed.indexOf(ext) === -1) {
				alert('فرمت فایل ' + files[i].name + ' مجاز نیست');
				hasError = true;
				break;
			}
			formData.append('files[]', files[i]);
		}

		if (hasError) { $(this).val(''); return; }

		formData.append('code', code);

		$.ajax({
			url: "<?= base_url('admin/upload_img_temp') ?>",
			method: "POST",
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				var res = JSON.parse(response);
				if (res.status == 1) {
					res.files.forEach(function (file) {
						$('#image_prd').append(
							'<div class="img-preview" id="img_' + file.filename + '" style="position:relative; display:inline-block;">' +
							'<img src="' + file.url + '" style="width:100px;height:100px;object-fit:cover;border:1px solid #ccc;border-radius:5px;">' +
							'<button class="btn btn-danger btn-xs del_img" style="position:absolute;top:2px;right:2px;" data-filename="' + file.filename + '">' +
							'<i class="fa fa-times"></i>' +
							'</button>' +
							'</div>'
						);
					});
				} else {
					alert(res.message || 'خطا در آپلود');
				}
				$('#files_image').val('');
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
				$('#files_image').val('');
			}
		});
	});

	// حذف عکس موقت
	$(document).on('click', '.del_img', function () {
		var filename = $(this).data('filename');
		var code     = $('#code').val();
		var $el      = $(this).closest('.img-preview');

		$.ajax({
			url: "<?= base_url('admin/delete_img_temp') ?>",
			method: "POST",
			data: { code: code, filename: filename },
			success: function (response) {
				var res = JSON.parse(response);
				if (res.status == 1) {
					$el.remove();
				} else {
					alert(res.message || 'خطا در حذف');
				}
			}
		});
	});

	// ================== ثبت نهایی ==================

	$('#accept11').on('click', function () {
		$('#name_err, #attr_err, #submit_err').text('');

		var name = $('#name').val().trim();
		if (!name) {
			$('#name_err').text('نام محصول الزامی است');
			$('html, body').animate({ scrollTop: $('#name').offset().top - 100 }, 400);
			return;
		}

		if (attrRows.length === 0) {
			$('#attr_err').text('حداقل یک ردیف ویژگی (با قیمت) اضافه کنید');
			$('html, body').animate({ scrollTop: $('#attr_err').offset().top - 100 }, 400);
			return;
		}

		var hasDefault = attrRows.some(function (r) { return r['default'] == '1'; });
		if (!hasDefault) {
			$('#attr_err').text('یک ردیف باید به عنوان پیش‌فرض انتخاب شود');
			$('html, body').animate({ scrollTop: $('#attr_err').offset().top - 100 }, 400);
			return;
		}

		var code   = $('#code').val();
		var id_cat = $('#id_cat').val();
		var text   = (typeof CKEDITOR !== 'undefined') ? CKEDITOR.instances['text1'].getData() : $('#text1').val();

		// ✅ فقط فیلدهای لازم برای ارسال (بدون نام‌های نمایشی)
		var sendAttrs = attrRows.map(function (r) {
			return {
				model:       r.model,
				jens:        r.jens,
				size:        r.size,
				brand:       r.brand,
				color:       r.color,
				buy_price:   r.buy_price,
				price:       r.price,
				off_percent: r.off_percent,
				supply:      r.supply,
				isActive:    r.isActive,
				'default':   r['default']
			};
		});

		$('#accept11').prop('disabled', true).text('در حال ثبت...');

		$.ajax({
			url: "<?= base_url('admin/insert_prd') ?>",
			method: "POST",
			data: {
				code:       code,
				id_cat:     id_cat,
				name:       name,
				text:       text,
				attributes: JSON.stringify(sendAttrs)
			},
			success: function (response) {
				var res = JSON.parse(response);
				if (res.status == 1) {
					alert('محصول با موفقیت ثبت شد');
					window.location.href = "<?= base_url('admin/insert_product') ?>";
				} else if (res.message === 'session_expired') {
					alert('نشست شما منقضی شده. لطفاً دوباره وارد شوید.');
					window.location.href = "<?= base_url('admin/login_page') ?>";
				} else {
					$('#submit_err').text(res.message || 'خطا در ثبت');
					$('#accept11').prop('disabled', false).html('<i class="fa fa-check"></i> ثبت محصول');
				}
			},
			error: function () {
				$('#submit_err').text('خطا در ارتباط با سرور');
				$('#accept11').prop('disabled', false).html('<i class="fa fa-check"></i> ثبت محصول');
			}
		});
	});

	// ================== فیلتر اعداد مثبت ==================

	$('.positive').on('keydown', function (e) {
		if (!((e.keyCode > 95 && e.keyCode < 106) ||
			(e.keyCode > 47 && e.keyCode < 58) ||
			e.keyCode == 8)) {
			return false;
		}
	});

	$(document).on('change', '#buy_price, #price, #off_percent, #supply', function () {
		if (parseFloat($(this).val()) < 0) $(this).val(0);
	});

</script>
