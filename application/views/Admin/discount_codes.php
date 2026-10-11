<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content">

		<div>
			<?php if (!empty($action_permissions['add']) && $action_permissions['add']): ?>
				<a href="<?= base_url('admin/discount_form') ?>">
					<button class="btn btn-success" style="outline:unset">
						<i class="fa fa-plus"></i> کد تخفیف جدید
					</button>
				</a>
			<?php else: ?>
				<button class="btn btn-success" style="outline:unset"
						onclick="showError('شما دسترسی ایجاد ندارید')">
					<i class="fa fa-plus"></i> کد تخفیف جدید
				</button>
			<?php endif; ?>

			<button id="delete_selected" class="btn btn-danger btn-sm ml-3" style="outline:none">
				<i class="fa fa-trash"></i> حذف انتخاب‌شده‌ها
			</button>
		</div>

		<br><br><br>

		<table id="disc_data" class="table table-bordered table-striped">
			<thead>
			<tr>
				<th>
					<label for="check_all">همه</label>
					<input type="checkbox" id="check_all">
				</th>
				<th width="10%">
					کد تخفیف<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="9%">مبلغ تخفیف</th>
				<th width="9%">تاریخ شروع</th>
				<th width="9%">تاریخ پایان</th>
				<th width="9%">حداقل خرید</th>
				<th width="7%">تعداد استفاده</th>
				<th width="9%">وضعیت انقضا</th>
				<th width="12%">محدودیت</th>
				<th width="9%">وضعیت</th>
				<th width="5%">ویرایش</th>
				<th width="5%">حذف</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar" class="snackbar">عملیات با موفقیت انجام شد</div>
		<div id="snackbar_err" class="snackbar">خطا در انجام عملیات</div>

	</div>

<?php } ?>

<script>

	$(document).ready(function () {

		<?php if ($this->session->flashdata('success')): ?>
		showSnackbar('snackbar');
		<?php endif; ?>

		var dataTable = $('#disc_data').DataTable({
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
				url: "<?= base_url('admin/discount_list') ?>",
				type: "POST"
			},
			columnDefs: [{
				targets: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
				orderable: false,
				searchable: false
			}]
		});

		// column filter
		$('.column-filter').on('keyup change', function () {
			var columnIndex = $(this).closest('th').index();
			dataTable.column(columnIndex).search(this.value).draw();
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
			alert('نشست شما منقضی شده. لطفاً دوباره وارد شوید.');
			window.location.href = "<?= base_url('admin/login_page') ?>";
		} else {
			showError(res.message || 'خطا در انجام عملیات');
		}
	}

	// ================== فعال/غیرفعال ==================

	$(document).on('click', '.toggle_disc_status', function () {
		if ($(this).data('no-permission')) { alert('شما دسترسی این عملیات را ندارید'); return; }

		$.ajax({
			url: "<?= base_url('admin/toggle_discount_status') ?>",
			method: "POST",
			data: { id: $(this).attr('disc_id'), status: $(this).data('status') },
			dataType: 'json',
			success: function (response) {
				handleAjaxResponse(response, function () {
					$('#disc_data').DataTable().ajax.reload(null, false);
					showSnackbar('snackbar');
				});
			}
		});
	});

	// ================== حذف ==================

	function deleteDiscounts(ids) {
		if (!confirm('آیا از حذف کد(های) تخفیف اطمینان دارید؟')) return;

		$.ajax({
			url: "<?= base_url('admin/delete_discount') ?>",
			method: "POST",
			data: { 'ids[]': ids },
			dataType: 'json',
			success: function (response) {
				handleAjaxResponse(response, function () {
					$('#disc_data').DataTable().ajax.reload(null, false);
					showSnackbar('snackbar');
				});
			},
			error: function () { showError('خطا در ارتباط با سرور'); }
		});
	}

	$(document).on('click', '.delete_disc', function () {
		if ($(this).data('no-permission')) { alert('شما دسترسی حذف را ندارید'); return; }
		deleteDiscounts([$(this).attr('disc_id')]);
	});

	$('#delete_selected').on('click', function () {
		var ids = [];
		$("input[name='row-check']:checked").each(function () {
			ids.push($(this).attr('disc_id'));
		});
		if (ids.length === 0) { alert('هیچ ردیفی انتخاب نشده است'); return; }
		deleteDiscounts(ids);
	});

</script>
