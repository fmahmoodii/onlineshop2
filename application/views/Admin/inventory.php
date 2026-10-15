<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content" style="padding: 0 0 100px 0">

		<div id="loading-image" style="display: none"></div>

		<div>
			<?php if (!empty($action_permissions['add']) && $action_permissions['add']): ?>
				<button id="new_inv" class="btn btn-success btn-sm" style="outline:unset">
					<i class="fa fa-plus"></i> انبار جدید
				</button>
			<?php else: ?>
				<button class="btn btn-success btn-sm" onclick="showError('شما دسترسی ایجاد ندارید')">
					<i class="fa fa-plus"></i> انبار جدید
				</button>
			<?php endif; ?>

			<button id="active_selected"   class="btn btn-primary btn-sm ml-3">فعال سازی همه</button>
			<button id="deactive_selected" class="btn btn-secondary btn-sm ml-3">غیرفعال سازی همه</button>
			<button id="delete_selected"   class="btn btn-danger btn-sm ml-3">حذف همه</button>
		</div>

		<br><br>

		<table id="inv_data" class="table table-bordered table-striped" style="width:100%">
			<thead>
			<tr>
				<th>
					<label for="check_all">همه</label>
					<input type="checkbox" id="check_all">
				</th>
				<th>
					نام انبار<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th>
					توضیحات<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="8%">کل موجودی</th>
				<th width="10%">تاریخ ایجاد</th>
				<th width="10%">آخرین ویرایش</th>
				<th width="9%">وضعیت</th>
				<th width="5%">گزارش</th>
				<th width="5%">موجودی</th>
				<th width="5%">ویرایش</th>
				<th width="5%">حذف</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar"     class="snackbar">عملیات با موفقیت انجام شد</div>
		<div id="snackbar_err" class="snackbar">خطا در انجام عملیات</div>

	</div>

	<!-- ✅ مودال افزودن انبار -->
	<div class="modal fade" id="new_modal" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="float:right"><i class="fa fa-close"></i></button>
					<h4 class="modal-title">انبار جدید</h4>
				</div>
				<div class="modal-body">
					<form method="post" action="<?php echo base_url('admin/insert_inv'); ?>">
						<div class="form-group">
							<label class="required">نام انبار:</label>
							<input autocomplete="off" id="name" name="name" class="form-control" type="text" placeholder="نام انبار">
							<span id="name_result"></span>
							<?php echo form_error('name', '<span style="color:red">', '</span>'); ?>
						</div>
						<div class="form-group">
							<label>توضیحات:</label>
							<input autocomplete="off" id="details" name="details" class="form-control" type="text" placeholder="توضیحات">
						</div>
						<button class="btn btn-success">ذخیره</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- ✅ مودال ویرایش انبار -->
	<div class="modal fade" id="edit_modal" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="float:right"><i class="fa fa-close"></i></button>
					<h4 class="modal-title">ویرایش انبار</h4>
				</div>
				<div class="modal-body">
					<form method="post" action="<?php echo base_url('admin/edit_inv'); ?>">
						<input type="hidden" id="id_modal" name="id_modal">
						<div class="form-group">
							<label class="required">نام انبار:</label>
							<input autocomplete="off" id="name_modal" name="name_modal" class="form-control" type="text" placeholder="نام انبار">
							<span id="name_modal_result"></span>
							<?php echo form_error('name_modal', '<span style="color:red">', '</span>'); ?>
						</div>
						<div class="form-group">
							<label>توضیحات:</label>
							<input autocomplete="off" id="details_modal" name="details_modal" class="form-control" type="text" placeholder="توضیحات">
						</div>
						<button class="btn btn-success">ذخیره</button>
					</form>
				</div>
			</div>
		</div>
	</div>

<?php } ?>

<script>

	$(document).ready(function () {

		<?php if ($this->session->flashdata('success')): ?>
		showSnackbar('snackbar');
		<?php endif; ?>

		var dataTable = $('#inv_data').DataTable({
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
				url: "<?= base_url('admin/inventory_list') ?>",
				type: "POST"
			},
			columnDefs: [{
				targets: [0, 3, 6, 7, 8, 9, 10],
				orderable: false,
				searchable: false
			}]
		});

		// column filter
		$('.column-filter').on('keyup change', function () {
			var idx = $(this).closest('th').index();
			dataTable.column(idx).search(this.value).draw();
		});
		$('.column-filter').on('click', function (e) { e.stopPropagation(); });

		// check all
		$('#check_all').on('change', function () {
			$("input[name='row-check']").prop('checked', $(this).prop('checked'));
		});
		$(document).on('change', "input[name='row-check']", function () {
			var total   = $("input[name='row-check']").length;
			var checked = $("input[name='row-check']:checked").length;
			$('#check_all').prop('checked', total === checked);
		});

		// ✅ real-time چک نام انبار جدید
		$('#name').on('keyup', function () {
			var name = $(this).val().trim();
			if (!name) { $('#name_result').html(''); return; }
			$.post("<?= base_url('admin/check_inv_name') ?>", { name: name }, function (data) {
				$('#name_result').html(data);
			});
		});

		// ✅ real-time چک نام انبار ویرایش
		$('#name_modal').on('keyup', function () {
			var name = $(this).val().trim();
			var id   = $('#id_modal').val();
			if (!name) { $('#name_modal_result').html(''); return; }
			$.post("<?= base_url('admin/check_inv_name_edit') ?>", { name: name, id: id }, function (data) {
				$('#name_modal_result').html(data);
			});
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
			alert('نشست منقضی شده. لطفاً دوباره وارد شوید.');
			window.location.href = "<?= base_url('admin/login_page') ?>";
		} else {
			showError(res.message || 'خطا در انجام عملیات');
		}
	}

	// ================== مودال‌ها ==================

	$('#new_inv').on('click', function () {
		$('#name, #details').val('');
		$('#name_result').html('');
		$('#new_modal').modal();
	});

	$(document).on('click', '.edit_inv', function () {
		if ($(this).data('no-permission')) { alert('شما دسترسی ویرایش ندارید'); return; }

		var id      = $(this).attr('inv_id');
		var name    = $('#name_' + id).text().trim();
		var details = $('#details_' + id).text().trim();

		$('#id_modal').val(id);
		$('#name_modal').val(name);
		$('#details_modal').val(details === '—' ? '' : details);
		$('#name_modal_result').html('');
		$('#edit_modal').modal();
	});

	// ================== فعال/غیرفعال ==================

	function toggleStatus(ids, status) {
		$("#loading-image").show();
		$.ajax({
			url: "<?= base_url('admin/toggle_inventory_status') ?>",
			method: "POST",
			data: { 'inv_ids[]': ids, status: status },
			dataType: 'json',
			success: function (response) {
				$("#loading-image").hide();
				handleAjaxResponse(response, function () {
					$('#inv_data').DataTable().ajax.reload(null, false);
					showSnackbar('snackbar');
				});
			},
			error: function () {
				$("#loading-image").hide();
				showError('خطا در ارتباط با سرور');
			}
		});
	}

	$(document).on('click', '.toggle_inv_status', function () {
		if ($(this).data('no-permission')) { alert('شما دسترسی این عملیات را ندارید'); return; }
		toggleStatus([$(this).attr('inv_id')], $(this).data('status'));
	});

	$('#active_selected, #deactive_selected').on('click', function () {
		var ids = [];
		$("input[name='row-check']:checked").each(function () { ids.push($(this).attr('inv_id')); });
		if (ids.length === 0) { alert('حداقل یک رکورد انتخاب کنید'); return; }
		var status = $(this).attr('id') === 'active_selected' ? 1 : 0;
		toggleStatus(ids, status);
	});

	// ================== حذف ==================

	function deleteInventories(ids) {
		if (!confirm('آیا از حذف انبار(های) انتخاب‌شده اطمینان دارید؟')) return;

		$("#loading-image").show();
		$.ajax({
			url: "<?= base_url('admin/delete_inv') ?>",
			method: "POST",
			data: { 'inv_ids[]': ids },
			dataType: 'json',
			success: function (response) {
				$("#loading-image").hide();
				handleAjaxResponse(response, function () {
					$('#inv_data').DataTable().ajax.reload(null, false);
					showSnackbar('snackbar');
				});
			},
			error: function () {
				$("#loading-image").hide();
				showError('خطا در ارتباط با سرور');
			}
		});
	}

	$(document).on('click', '.delete_inv', function () {
		if ($(this).data('no-permission')) { alert('شما دسترسی حذف را ندارید'); return; }
		deleteInventories([$(this).attr('inv_id')]);
	});

	$('#delete_selected').on('click', function () {
		var ids = [];
		$("input[name='row-check']:checked").each(function () { ids.push($(this).attr('inv_id')); });
		if (ids.length === 0) { alert('حداقل یک رکورد انتخاب کنید'); return; }
		deleteInventories(ids);
	});

</script>
