<?php if($this->session->userdata('id')){ ?>

	<div class="container-fluid" id="content">
		<div class="row" style="margin: 60px">
			<div style="border: 1px solid #ccc; padding: 20px; border-radius: 10px;">

				<input type="hidden" id="code_p" value="<?php echo $product->code; ?>">
				<input type="hidden" id="id_p" value="<?php echo $product->id; ?>">

				<!-- ================== اطلاعات اصلی محصول ================== -->
				<h4>اطلاعات اصلی محصول</h4>
				<hr>

				<div class="form-group">
					<label>دسته‌بندی:</label>
					<select id="id_cat" class="form-control">
						<option value="">— انتخاب کنید —</option>
						<?php foreach ($categories as $cat): ?>
							<option value="<?php echo $cat->id; ?>"
								<?php echo ($cat->id == $product->id_cat) ? 'selected' : ''; ?>>
								<?php echo ($cat->parentId != '0' ? '&nbsp;&nbsp;&nbsp;&nbsp;└─ ' : '') . htmlspecialchars($cat->name_cat); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="form-group">
					<label class="required">نام محصول:</label>
					<input autocomplete="off" id="name" type="text" class="form-control"
						   value="<?php echo htmlspecialchars($product->name); ?>" placeholder="نام محصول">
					<span style="color:red" id="name_err"></span>
				</div>

				<div class="form-group">
					<label>توضیحات:</label>
					<textarea id="text1" class="ckeditor" rows="10" cols="70" name="text1"><?php echo $product->text; ?></textarea>
				</div>

				<button id="save_product" class="btn btn-success">
					<i class="fa fa-save"></i> ذخیره اطلاعات محصول
				</button>
				<span style="color:red; margin-right:10px;" id="product_err"></span>
				<span style="color:green; margin-right:10px; display:none;" id="product_ok">ذخیره شد ✓</span>

				<hr>

				<!-- ================== مدیریت عکس‌ها ================== -->
				<h4>تصاویر محصول</h4>

				<div id="image_prd" style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px;">
					<?php foreach ($images as $img): ?>
						<div class="img-preview" id="img_<?php echo $img->id; ?>" style="position:relative; display:inline-block;">
							<img src="<?php echo base_url('uploads/products/' . $product->id . '/' . $img->filename); ?>"
								 style="width:100px;height:100px;object-fit:cover;border:1px solid #ccc;border-radius:5px;">
							<button class="btn btn-danger btn-xs del_img"
									style="position:absolute;top:2px;right:2px;"
									data-img-id="<?php echo $img->id; ?>"
									data-filename="<?php echo htmlspecialchars($img->filename); ?>">
								<i class="fa fa-times"></i>
							</button>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if(count($images) < 5): ?>
					<input id="files_image" type="file" multiple accept="image/*">
					<small class="text-muted">حداکثر <?php echo 5 - count($images); ?> تصویر دیگر — فرمت‌های مجاز: jpg, jpeg, png, gif, webp</small>
				<?php else: ?>
					<small class="text-muted text-danger">حداکثر تعداد تصویر (۵) رسیده است</small>
				<?php endif; ?>

				<hr>

				<!-- ================== attribute ها ================== -->
				<h4>ویژگی‌های محصول</h4>

				<div style="margin-bottom: 10px;">
					<button id="show_add_attr" class="btn btn-success btn-sm">
						<i class="fa fa-plus"></i> افزودن ویژگی جدید
					</button>
				</div>

				<!-- فرم افزودن attribute جدید -->
				<div id="add_attr_form" style="display:none; border:1px solid #ddd; padding:15px; border-radius:8px; margin-bottom:15px; background:#f9f9f9;">
					<h5>افزودن ویژگی جدید</h5>
					<div style="display:flex; flex-wrap:wrap; gap:5px; align-items:center;">
						<div>
							<label><input type="checkbox" id="edit_model_checkbox"> مدل</label>
							<select class="form-control" id="edit_model" style="display:none; width:120px; display:none;">
								<option value="">مدل</option>
								<?php foreach ($model as $mo): ?>
									<option value="<?php echo $mo->id; ?>"><?php echo htmlspecialchars($mo->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div>
							<label><input type="checkbox" id="edit_jens_checkbox"> جنس</label>
							<select class="form-control" id="edit_jens" style="display:none; width:120px;">
								<option value="">جنس</option>
								<?php foreach ($jens as $je): ?>
									<option value="<?php echo $je->id; ?>"><?php echo htmlspecialchars($je->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div>
							<label><input type="checkbox" id="edit_size_checkbox"> سایز</label>
							<select class="form-control" id="edit_size" style="display:none; width:120px;">
								<option value="">سایز</option>
								<?php foreach ($size as $si): ?>
									<option value="<?php echo $si->id; ?>"><?php echo htmlspecialchars($si->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div>
							<label><input type="checkbox" id="edit_brand_checkbox"> برند</label>
							<select class="form-control" id="edit_brand" style="display:none; width:120px;">
								<option value="">برند</option>
								<?php foreach ($brand as $br): ?>
									<option value="<?php echo $br->id; ?>"><?php echo htmlspecialchars($br->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div>
							<label><input type="checkbox" id="edit_color_checkbox"> رنگ</label>
							<select class="form-control" id="edit_color" style="display:none; width:120px;">
								<option value="">رنگ</option>
								<?php foreach ($color as $co): ?>
									<option value="<?php echo $co->id; ?>" style="background:<?php echo $co->color_code; ?>">
										<?php echo htmlspecialchars($co->name); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<input type="number" id="edit_buy_price" class="form-control positive" placeholder="قیمت خرید" style="width:100px;" min="0">
						<input type="number" id="edit_price" class="form-control positive" placeholder="قیمت *" style="width:100px;" min="0">
						<input type="number" id="edit_off_percent" class="form-control positive" placeholder="% تخفیف" style="width:80px;" min="0" max="100">
						<input type="number" id="edit_supply" class="form-control positive" placeholder="موجودی" style="width:80px;" min="0">
						<button id="save_new_attr" class="btn btn-primary btn-sm">
							<i class="fa fa-save"></i> ذخیره
						</button>
						<button id="cancel_add_attr" class="btn btn-default btn-sm">انصراف</button>
					</div>
					<span style="color:red" id="add_attr_err"></span>
				</div>

				<!-- جدول attribute ها -->
				<table id="attr_data" class="table table-bordered table-striped table-sm">
					<thead>
					<tr>
						<th><input type="checkbox" id="check_all_attr"></th>
						<th>مدل</th>
						<th>جنس</th>
						<th>سایز</th>
						<th>برند</th>
						<th>رنگ</th>
						<th>قیمت خرید</th>
						<th>قیمت</th>
						<th>تخفیف</th>
						<th>موجودی</th>
						<th>وضعیت</th>
						<th>پیش‌فرض</th>
						<th>ویرایش</th>
					</tr>
					</thead>
				</table>

			</div>
		</div>
	</div>

	<!-- ================== مودال ویرایش attribute ================== -->
	<div class="modal fade" id="edit_attr_modal" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content" style="background:#fff;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
					<h4 class="modal-title">ویرایش ویژگی</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="modal_attr_id">

					<div style="display:flex; flex-wrap:wrap; gap:10px;">
						<div class="form-group">
							<label>مدل:</label>
							<select class="form-control" id="modal_model">
								<option value="">—</option>
								<?php foreach ($model as $mo): ?>
									<option value="<?php echo $mo->id; ?>"><?php echo htmlspecialchars($mo->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label>جنس:</label>
							<select class="form-control" id="modal_jens">
								<option value="">—</option>
								<?php foreach ($jens as $je): ?>
									<option value="<?php echo $je->id; ?>"><?php echo htmlspecialchars($je->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label>سایز:</label>
							<select class="form-control" id="modal_size">
								<option value="">—</option>
								<?php foreach ($size as $si): ?>
									<option value="<?php echo $si->id; ?>"><?php echo htmlspecialchars($si->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label>برند:</label>
							<select class="form-control" id="modal_brand">
								<option value="">—</option>
								<?php foreach ($brand as $br): ?>
									<option value="<?php echo $br->id; ?>"><?php echo htmlspecialchars($br->name); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label>رنگ:</label>
							<select class="form-control" id="modal_color">
								<option value="">—</option>
								<?php foreach ($color as $co): ?>
									<option value="<?php echo $co->id; ?>" style="background:<?php echo $co->color_code; ?>">
										<?php echo htmlspecialchars($co->name); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label>قیمت خرید:</label>
							<input type="number" id="modal_buy_price" class="form-control positive" min="0">
						</div>
						<div class="form-group">
							<label class="required">قیمت:</label>
							<input type="number" id="modal_price" class="form-control positive" min="0">
						</div>
						<div class="form-group">
							<label>% تخفیف:</label>
							<input type="number" id="modal_off_percent" class="form-control positive" min="0" max="100">
						</div>
						<div class="form-group">
							<label>موجودی:</label>
							<input type="number" id="modal_supply" class="form-control positive" min="0">
						</div>
					</div>
					<span style="color:red" id="modal_attr_err"></span>
				</div>
				<div class="modal-footer">
					<button id="save_attr_modal" class="btn btn-success" style="float:right;">ذخیره</button>
					<button class="btn btn-danger" data-dismiss="modal" style="float:right;">لغو</button>
				</div>
			</div>
		</div>
	</div>

	<div id="snackbar" class="snackbar">عملیات با موفقیت انجام شد</div>
	<div id="snackbar_err" class="snackbar">خطا در عملیات</div>

<?php } ?>

<script>

	var code_p      = $('#code_p').val();
	var product_id  = $('#id_p').val();

	// ================== DataTable attribute ها ==================

	var attrTable = $('#attr_data').DataTable({
		processing: true,
		serverSide: true,
		order: [],
		ajax: {
			url: "<?= base_url('admin/attr_list') ?>",
			type: "POST",
			data: function(d) { d.code_p = code_p; }
		},
		columnDefs: [
			{ targets: [0, 10, 11, 12], orderable: false, searchable: false }
		]
	});

	// ================== چک‌باکس همه ==================

	$('#check_all_attr').on('change', function() {
		$("input[name='attr-check']").prop('checked', $(this).prop('checked'));
	});

	$(document).on('change', "input[name='attr-check']", function() {
		var total   = $("input[name='attr-check']").length;
		var checked = $("input[name='attr-check']:checked").length;
		$('#check_all_attr').prop('checked', total === checked);
	});

	// ================== فعال/غیرفعال attribute ==================

	$(document).on('click', '.toggle_attr_status', function() {
		if ($(this).data('no-permission')) { alert('شما دسترسی این عملیات را ندارید'); return; }

		var attr_id = $(this).attr('attr_id');
		var status  = $(this).data('status');

		$.ajax({
			url: "<?= base_url('admin/toggle_attr_status') ?>",
			method: "POST",
			data: { id: attr_id, status: status },
			dataType: 'json',
			success: function(response) {
				if (response.status == 1) {
					attrTable.ajax.reload(null, false);
					showSnackbar('snackbar');
				} else {
					alert(response.message || 'خطا');
				}
			}
		});
	});

	// ================== تنظیم default ==================

	$(document).on('change', '.set_default_attr', function() {
		var attr_id = $(this).attr('attr_id');

		$.ajax({
			url: "<?= base_url('admin/set_default_attr') ?>",
			method: "POST",
			data: { id: attr_id, code_p: code_p },
			dataType: 'json',
			success: function(response) {
				if (response.status == 1) {
					attrTable.ajax.reload(null, false);
					showSnackbar('snackbar');
				} else {
					alert(response.message || 'خطا');
				}
			}
		});
	});

	// ================== ویرایش attribute (مودال) ==================

	$(document).on('click', '.edit_attr', function() {
		if ($(this).data('no-permission')) { alert('شما دسترسی این عملیات را ندارید'); return; }

		var attr_id = $(this).attr('attr_id');

		$.ajax({
			url: "<?= base_url('admin/get_attr') ?>",
			method: "POST",
			data: { id: attr_id },
			dataType: 'json',
			success: function(response) {
				if (response.status == 1) {
					var d = response.data;
					$('#modal_attr_id').val(d.id);
					$('#modal_model').val(d.model || '');
					$('#modal_jens').val(d.jens || '');
					$('#modal_size').val(d.size || '');
					$('#modal_brand').val(d.brand || '');
					$('#modal_color').val(d.color || '');
					$('#modal_buy_price').val(d.buy_price || '');
					$('#modal_price').val(d.price);
					$('#modal_off_percent').val(d.off_percent || '');
					$('#modal_supply').val(d.supply || '');
					$('#modal_attr_err').text('');
					$('#edit_attr_modal').modal();
				} else {
					alert(response.message || 'خطا');
				}
			}
		});
	});

	$('#save_attr_modal').on('click', function() {
		var price = $('#modal_price').val();
		if (!price || parseFloat(price) <= 0) {
			$('#modal_attr_err').text('قیمت الزامی است');
			return;
		}
		$('#modal_attr_err').text('');

		$.ajax({
			url: "<?= base_url('admin/update_attr') ?>",
			method: "POST",
			data: {
				id:          $('#modal_attr_id').val(),
				model:       $('#modal_model').val(),
				jens:        $('#modal_jens').val(),
				size:        $('#modal_size').val(),
				brand:       $('#modal_brand').val(),
				color:       $('#modal_color').val(),
				buy_price:   $('#modal_buy_price').val(),
				price:       price,
				off_percent: $('#modal_off_percent').val(),
				supply:      $('#modal_supply').val()
			},
			dataType: 'json',
			success: function(response) {
				if (response.status == 1) {
					$('#edit_attr_modal').modal('toggle');
					attrTable.ajax.reload(null, false);
					showSnackbar('snackbar');
				} else {
					$('#modal_attr_err').text(response.message || 'خطا');
				}
			}
		});
	});

	// ================== افزودن attribute جدید ==================

	$('#show_add_attr').on('click', function() {
		$('#add_attr_form').slideDown();
		$(this).hide();
	});

	$('#cancel_add_attr').on('click', function() {
		$('#add_attr_form').slideUp();
		$('#show_add_attr').show();
		clearAddAttrForm();
	});

	$('#edit_model_checkbox').on('change', function() {
		$('#edit_model').toggle($(this).prop('checked'));
	});
	$('#edit_jens_checkbox').on('change', function() {
		$('#edit_jens').toggle($(this).prop('checked'));
	});
	$('#edit_size_checkbox').on('change', function() {
		$('#edit_size').toggle($(this).prop('checked'));
	});
	$('#edit_brand_checkbox').on('change', function() {
		$('#edit_brand').toggle($(this).prop('checked'));
	});
	$('#edit_color_checkbox').on('change', function() {
		$('#edit_color').toggle($(this).prop('checked'));
	});

	$('#save_new_attr').on('click', function() {
		var price = $('#edit_price').val();
		if (!price || parseFloat(price) <= 0) {
			$('#add_attr_err').text('قیمت الزامی است');
			return;
		}
		$('#add_attr_err').text('');

		$.ajax({
			url: "<?= base_url('admin/add_attr') ?>",
			method: "POST",
			data: {
				code_p:      code_p,
				model:       $('#edit_model').val(),
				jens:        $('#edit_jens').val(),
				size:        $('#edit_size').val(),
				brand:       $('#edit_brand').val(),
				color:       $('#edit_color').val(),
				buy_price:   $('#edit_buy_price').val(),
				price:       price,
				off_percent: $('#edit_off_percent').val(),
				supply:      $('#edit_supply').val()
			},
			dataType: 'json',
			success: function(response) {
				if (response.status == 1) {
					attrTable.ajax.reload(null, false);
					showSnackbar('snackbar');
					$('#add_attr_form').slideUp();
					$('#show_add_attr').show();
					clearAddAttrForm();
				} else {
					$('#add_attr_err').text(response.message || 'خطا');
				}
			}
		});
	});

	function clearAddAttrForm() {
		$('#edit_model, #edit_jens, #edit_size, #edit_brand, #edit_color').val('').hide();
		$('#edit_model_checkbox, #edit_jens_checkbox, #edit_size_checkbox, #edit_brand_checkbox, #edit_color_checkbox').prop('checked', false);
		$('#edit_buy_price, #edit_price, #edit_off_percent, #edit_supply').val('');
		$('#add_attr_err').text('');
	}

	// ================== ذخیره اطلاعات اصلی محصول ==================

	$('#save_product').on('click', function() {
		var name = $('#name').val().trim();
		$('#name_err').text('');
		$('#product_ok').hide();

		if (!name) {
			$('#name_err').text('نام محصول الزامی است');
			return;
		}

		var text = (typeof CKEDITOR !== 'undefined') ? CKEDITOR.instances['text1'].getData() : $('#text1').val();

		$(this).prop('disabled', true);

		$.ajax({
			url: "<?= base_url('admin/update_product') ?>",
			method: "POST",
			data: {
				id_p:   product_id,
				code:   code_p,
				name:   name,
				id_cat: $('#id_cat').val(),
				text:   text
			},
			dataType: 'json',
			success: function(response) {
				$('#save_product').prop('disabled', false);
				if (response.status == 1) {
					$('#product_ok').show();
					setTimeout(function() { $('#product_ok').hide(); }, 3000);
				} else {
					$('#product_err').text(response.message || 'خطا در ذخیره');
				}
			},
			error: function() {
				$('#save_product').prop('disabled', false);
				$('#product_err').text('خطا در ارتباط با سرور');
			}
		});
	});

	// ================== مدیریت عکس‌ها ==================

	$('#files_image').on('change', function() {
		var files = this.files;
		var currentCount = $('#image_prd .img-preview').length;

		if (currentCount + files.length > 5) {
			alert('حداکثر ۵ تصویر مجاز است');
			$(this).val('');
			return;
		}

		var formData = new FormData();
		var allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

		for (var i = 0; i < files.length; i++) {
			var ext = files[i].name.split('.').pop().toLowerCase();
			if (allowed.indexOf(ext) === -1) {
				alert('فرمت فایل ' + files[i].name + ' مجاز نیست');
				$(this).val('');
				return;
			}
			formData.append('files[]', files[i]);
		}

		formData.append('product_id', product_id);
		formData.append('code_p', code_p);

		$.ajax({
			url: "<?= base_url('admin/upload_img_product') ?>",
			method: "POST",
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			success: function(response) {
				var res = JSON.parse(response);
				if (res.status == 1) {
					res.files.forEach(function(file) {
						$('#image_prd').append(
							'<div class="img-preview" id="img_' + file.id + '" style="position:relative;display:inline-block;">' +
							'<img src="' + file.url + '" style="width:100px;height:100px;object-fit:cover;border:1px solid #ccc;border-radius:5px;">' +
							'<button class="btn btn-danger btn-xs del_img" style="position:absolute;top:2px;right:2px;" ' +
							'data-img-id="' + file.id + '" data-filename="' + file.filename + '">' +
							'<i class="fa fa-times"></i>' +
							'</button>' +
							'</div>'
						);
					});
					showSnackbar('snackbar');
				} else {
					alert(res.message || 'خطا در آپلود');
				}
				$('#files_image').val('');
			},
			error: function() {
				alert('خطا در ارتباط با سرور');
				$('#files_image').val('');
			}
		});
	});

	$(document).on('click', '.del_img', function() {
		var img_id   = $(this).data('img-id');
		var filename = $(this).data('filename');
		var $el      = $(this).closest('.img-preview');

		if (!confirm('آیا از حذف این تصویر اطمینان دارید؟')) return;

		$.ajax({
			url: "<?= base_url('admin/delete_img_product') ?>",
			method: "POST",
			data: { img_id: img_id, product_id: product_id, filename: filename },
			dataType: 'json',
			success: function(response) {
				if (response.status == 1) {
					$el.remove();
					showSnackbar('snackbar');
				} else {
					alert(response.message || 'خطا در حذف');
				}
			}
		});
	});

	// ================== توابع کمکی ==================

	function showSnackbar(id) {
		var el = document.getElementById(id);
		if (!el) return;
		el.className = 'snackbar show';
		setTimeout(function() { el.className = el.className.replace('show', ''); }, 3000);
	}

	$('.positive').on('keydown', function(e) {
		if (!((e.keyCode > 95 && e.keyCode < 106) ||
			(e.keyCode > 47 && e.keyCode < 58) ||
			e.keyCode == 8)) {
			return false;
		}
	});

</script>
