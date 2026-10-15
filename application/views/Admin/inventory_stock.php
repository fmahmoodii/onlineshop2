<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content" style="padding: 0 0 100px 0">

		<div id="loading-image" style="display: none"></div>

		<!-- breadcrumb -->
		<div style="margin-bottom: 15px;">
			<a href="<?= base_url('admin/inventory') ?>">لیست انبارها</a>
			<span> / </span>
			<span><?= htmlspecialchars($inventory->name) ?></span>
		</div>

		<div>
			<?php if (!empty($action_permissions['add']) && $action_permissions['add']): ?>
				<button id="new_stock" class="btn btn-success btn-sm" style="outline:unset">
					<i class="fa fa-plus"></i> افزودن کالا به انبار
				</button>
			<?php endif; ?>
		</div>

		<br><br>

		<table id="stock_data" class="table table-bordered table-striped" style="width:100%">
			<thead>
			<tr>
				<th>
					<label for="check_all">همه</label>
					<input type="checkbox" id="check_all">
				</th>
				<th width="10%">
					کد کالا<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="35%">
					نام کالا<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="8%">موجودی</th>
				<th width="12%">آخرین تغییر</th>
				<th width="8%">ورود</th>
				<th width="8%">خروج</th>
				<th width="5%">تاریخچه</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar"     class="snackbar">عملیات با موفقیت انجام شد</div>
		<div id="snackbar_err" class="snackbar">خطا در انجام عملیات</div>

	</div>

	<!-- ✅ مودال افزودن کالا جدید به انبار -->
	<div class="modal fade" id="new_stock_modal" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="float:right"><i class="fa fa-close"></i></button>
					<h4 class="modal-title">افزودن کالا به انبار</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="required">انتخاب محصول:</label>
						<select id="new_attr_id" class="form-control">
							<option value="">انتخاب کنید...</option>
							<?php
							// ✅ lookup maps بدون N+1
							$model_map = $jens_map = $size_map = $brand_map = $color_map = [];
							foreach ($model as $r)  { $model_map[$r->id] = $r->name; }
							foreach ($jens  as $r)  { $jens_map[$r->id]  = $r->name; }
							foreach ($size  as $r)  { $size_map[$r->id]  = $r->name; }
							foreach ($brand as $r)  { $brand_map[$r->id] = $r->name; }
							foreach ($color as $r)  { $color_map[$r->id] = ['name' => $r->name, 'code' => $r->color_code]; }

							$prod_map = [];
							foreach ($products as $p) { $prod_map[$p->code] = $p->name; }

							foreach ($attributes as $pa):
								$prod_name = $prod_map[$pa->code_p] ?? '—';
								$attrs = [];
								if (!empty($pa->model) && isset($model_map[$pa->model])) $attrs[] = 'مدل: ' . $model_map[$pa->model];
								if (!empty($pa->jens)  && isset($jens_map[$pa->jens]))   $attrs[] = 'جنس: ' . $jens_map[$pa->jens];
								if (!empty($pa->size)  && isset($size_map[$pa->size]))   $attrs[] = 'سایز: ' . $size_map[$pa->size];
								if (!empty($pa->brand) && isset($brand_map[$pa->brand])) $attrs[] = 'برند: ' . $brand_map[$pa->brand];
								if (!empty($pa->color) && isset($color_map[$pa->color])) $attrs[] = 'رنگ: ' . $color_map[$pa->color]['name'];
								$label = $prod_name . ' - ' . number_format($pa->price) . ' تومان';
								if (!empty($attrs)) $label .= ' (' . implode(' - ', $attrs) . ')';
								?>
								<option value="<?= $pa->id ?>">
									<?= htmlspecialchars($label) ?>
								</option>
							<?php endforeach; ?>
						</select>
						<span style="color:red" id="new_attr_err"></span>
					</div>
					<div class="form-group">
						<label class="required">تعداد:</label>
						<input type="number" id="new_qty" class="form-control" min="1" placeholder="تعداد">
						<span style="color:red" id="new_qty_err"></span>
					</div>
					<div class="form-group">
						<label class="required">نوع ورودی:</label>
						<select id="new_reference" class="form-control">
							<option value="purchase">خرید از تامین‌کننده</option>
							<option value="return">مرجوعی از مشتری</option>
							<option value="transfer">جابجایی از انبار دیگر</option>
							<option value="adjustment">تعدیل موجودی</option>
							<option value="initial">موجودی اولیه</option>
						</select>
					</div>
					<div class="form-group">
						<label>توضیحات:</label>
						<input type="text" id="new_desc" class="form-control" placeholder="توضیحات (اختیاری)">
					</div>
				</div>
				<div class="modal-footer">
					<button id="save_new_stock" class="btn btn-success" style="float:right;">ذخیره</button>
					<button class="btn btn-danger" data-dismiss="modal" style="float:right;">لغو</button>
				</div>
			</div>
		</div>
	</div>

	<!-- ✅ مودال ورود کالا -->
	<div class="modal fade" id="stock_in_modal" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="float:right"><i class="fa fa-close"></i></button>
					<h4 class="modal-title"><i class="fa fa-plus text-success"></i> ورود کالا</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="in_attr_id">
					<div class="form-group">
						<label class="required">تعداد ورودی:</label>
						<input type="number" id="in_qty" class="form-control" min="1" placeholder="تعداد">
						<span style="color:red" id="in_qty_err"></span>
					</div>
					<div class="form-group">
						<label class="required">نوع ورودی:</label>
						<select id="in_reference" class="form-control">
							<option value="purchase">خرید از تامین‌کننده</option>
							<option value="return">مرجوعی از مشتری</option>
							<option value="transfer">جابجایی از انبار دیگر</option>
							<option value="adjustment">تعدیل موجودی</option>
						</select>
					</div>
					<div class="form-group">
						<label>توضیحات:</label>
						<input type="text" id="in_desc" class="form-control" placeholder="اختیاری">
					</div>
				</div>
				<div class="modal-footer">
					<button id="save_stock_in" class="btn btn-success" style="float:right;">ثبت ورود</button>
					<button class="btn btn-danger" data-dismiss="modal" style="float:right;">لغو</button>
				</div>
			</div>
		</div>
	</div>

	<!-- ✅ مودال خروج کالا -->
	<div class="modal fade" id="stock_out_modal" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="float:right"><i class="fa fa-close"></i></button>
					<h4 class="modal-title"><i class="fa fa-minus text-warning"></i> خروج کالا</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="out_attr_id">
					<div class="form-group">
						<label class="required">تعداد خروجی:</label>
						<input type="number" id="out_qty" class="form-control" min="1" placeholder="تعداد">
						<span style="color:red" id="out_qty_err"></span>
					</div>
					<div class="form-group">
						<label class="required">نوع خروجی:</label>
						<select id="out_reference" class="form-control">
							<option value="sale">فروش به مشتری</option>
							<option value="transfer">جابجایی به انبار دیگر</option>
							<option value="return">مرجوعی به تامین‌کننده</option>
							<option value="adjustment">تعدیل موجودی</option>
						</select>
					</div>
					<div class="form-group">
						<label>توضیحات:</label>
						<input type="text" id="out_desc" class="form-control" placeholder="اختیاری">
					</div>
				</div>
				<div class="modal-footer">
					<button id="save_stock_out" class="btn btn-warning" style="float:right;">ثبت خروج</button>
					<button class="btn btn-danger" data-dismiss="modal" style="float:right;">لغو</button>
				</div>
			</div>
		</div>
	</div>

<?php } ?>

<script>
	var inv_id = <?= $inv_id ?>;

	$(document).ready(function () {

		var dataTable = $('#stock_data').DataTable({
			language: {
				lengthMenu: "نمایش _MENU_ رکورد هر صفحه",
				zeroRecords: "موردی یافت نشد",
				info: "صفحه _PAGE_ از _PAGES_",
				infoEmpty: "موردی یافت نشد",
				infoFiltered: "(فیلتر _MAX_ رکورد)",
				search: "جستجو ",
				loadingRecords: "درحال بارگذاری",
				processing: "در حال پردازش",
				paginate: { first: "ابتدا", last: "انتها", next: "بعدی", previous: "قبلی" }
			},
			processing: true,
			serverSide: true,
			order: [],
			ajax: {
				url: "<?= base_url('admin/inventory_stock_list/') ?>" + inv_id,
				type: "POST"
			},
			columnDefs: [{
				targets: [0, 3, 4, 5, 6, 7],
				orderable: false,
				searchable: false
			}]
		});

		$('.column-filter').on('keyup change', function () {
			dataTable.column($(this).closest('th').index()).search(this.value).draw();
		});
		$('.column-filter').on('click', function (e) { e.stopPropagation(); });

		$('#check_all').on('change', function () {
			$("input[name='row-check']").prop('checked', $(this).prop('checked'));
		});
	});

	function showSnackbar(id) {
		var el = document.getElementById(id);
		if (!el) return;
		el.className = 'snackbar show';
		setTimeout(function() { el.className = el.className.replace('show', ''); }, 3000);
	}

	function showError(msg) {
		var el = document.getElementById('snackbar_err');
		el.innerText = msg || 'خطا در انجام عملیات';
		el.className = 'snackbar show';
		setTimeout(function() { el.className = el.className.replace('show', ''); }, 3000);
	}

	function handleAjaxResponse(res, onSuccess) {
		if (res.status == 1) {
			onSuccess();
		} else if (res.message === 'session_expired') {
			alert('نشست منقضی شده.');
			window.location.href = "<?= base_url('admin/login_page') ?>";
		} else {
			showError(res.message || 'خطا');
		}
	}

	// ================== افزودن کالای جدید ==================

	$('#new_stock').on('click', function () {
		$('#new_attr_id, #new_reference').val('');
		$('#new_qty, #new_desc').val('');
		$('#new_attr_err, #new_qty_err').text('');
		$('#new_stock_modal').modal();
	});

	$('#save_new_stock').on('click', function () {
		var attr_id   = $('#new_attr_id').val();
		var qty       = parseInt($('#new_qty').val());
		var reference = $('#new_reference').val();
		var desc      = $('#new_desc').val().trim();

		$('#new_attr_err, #new_qty_err').text('');
		var hasError = false;
		if (!attr_id) { $('#new_attr_err').text('محصول الزامی است'); hasError = true; }
		if (!qty || qty <= 0) { $('#new_qty_err').text('تعداد باید بیشتر از صفر باشد'); hasError = true; }
		if (hasError) return;

		$(this).prop('disabled', true);
		$.ajax({
			url: "<?= base_url('admin/add_stock') ?>",
			method: "POST",
			data: { inv_id: inv_id, attr_id: attr_id, qty: qty, reference: reference, description: desc },
			dataType: 'json',
			success: function (response) {
				$('#save_new_stock').prop('disabled', false);
				handleAjaxResponse(response, function () {
					$('#new_stock_modal').modal('toggle');
					$('#stock_data').DataTable().ajax.reload(null, false);
					showSnackbar('snackbar');
				});
			},
			error: function () {
				$('#save_new_stock').prop('disabled', false);
				showError('خطا در ارتباط با سرور');
			}
		});
	});

	// ================== ورود کالا ==================

	$(document).on('click', '.stock_in', function () {
		if ($(this).data('no-permission')) { alert('شما دسترسی این عملیات را ندارید'); return; }
		$('#in_attr_id').val($(this).attr('attr_id'));
		$('#in_qty, #in_desc').val('');
		$('#in_qty_err').text('');
		$('#stock_in_modal').modal();
	});

	$('#save_stock_in').on('click', function () {
		var attr_id   = $('#in_attr_id').val();
		var qty       = parseInt($('#in_qty').val());
		var reference = $('#in_reference').val();
		var desc      = $('#in_desc').val().trim();

		$('#in_qty_err').text('');
		if (!qty || qty <= 0) { $('#in_qty_err').text('تعداد باید بیشتر از صفر باشد'); return; }

		$(this).prop('disabled', true);
		$.ajax({
			url: "<?= base_url('admin/add_stock') ?>",
			method: "POST",
			data: { inv_id: inv_id, attr_id: attr_id, qty: qty, reference: reference, description: desc },
			dataType: 'json',
			success: function (response) {
				$('#save_stock_in').prop('disabled', false);
				handleAjaxResponse(response, function () {
					$('#stock_in_modal').modal('toggle');
					$('#stock_data').DataTable().ajax.reload(null, false);
					showSnackbar('snackbar');
				});
			},
			error: function () {
				$('#save_stock_in').prop('disabled', false);
				showError('خطا در ارتباط با سرور');
			}
		});
	});

	// ================== خروج کالا ==================

	$(document).on('click', '.stock_out', function () {
		if ($(this).data('no-permission')) { alert('شما دسترسی این عملیات را ندارید'); return; }
		$('#out_attr_id').val($(this).attr('attr_id'));
		$('#out_qty, #out_desc').val('');
		$('#out_qty_err').text('');
		$('#stock_out_modal').modal();
	});

	$('#save_stock_out').on('click', function () {
		var attr_id   = $('#out_attr_id').val();
		var qty       = parseInt($('#out_qty').val());
		var reference = $('#out_reference').val();
		var desc      = $('#out_desc').val().trim();

		$('#out_qty_err').text('');
		if (!qty || qty <= 0) { $('#out_qty_err').text('تعداد باید بیشتر از صفر باشد'); return; }

		$(this).prop('disabled', true);
		$.ajax({
			url: "<?= base_url('admin/remove_stock') ?>",
			method: "POST",
			data: { inv_id: inv_id, attr_id: attr_id, qty: qty, reference: reference, description: desc },
			dataType: 'json',
			success: function (response) {
				$('#save_stock_out').prop('disabled', false);
				handleAjaxResponse(response, function () {
					$('#stock_out_modal').modal('toggle');
					$('#stock_data').DataTable().ajax.reload(null, false);
					showSnackbar('snackbar');
				});
			},
			error: function () {
				$('#save_stock_out').prop('disabled', false);
				showError('خطا در ارتباط با سرور');
			}
		});
	});
</script>
