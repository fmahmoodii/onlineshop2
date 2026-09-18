<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content" >

		<div>
			<?php if (!empty($action_permissions['add']) && $action_permissions['add']): ?>
				<button class="btn btn-success" id="new_cat" style="outline: unset" >
					دسته جدید
				</button>
			<?php else: ?>
				<button class="btn btn-success" id="new_cat" style="outline: unset"
						onclick="showError('شما دسترسی ایجاد ندارید')">
					دسته جدید
				</button>
			<?php endif; ?>

			<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" style="outline: none">حذف همه</button>
			<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
			<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
		</div>
		<br>
		<br>
		<br>
		<table id="cat_data" class="table table-bordered table-striped">
			<thead>
			<tr id="id_cat">
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
				</th>
				<th width="20%">نام دسته</th>
				<th width="15%">دسته والد</th>
				<th width="15%">توضیحات</th>
				<th width="8%">تعداد محصولات</th>
				<th width="10%">وضعیت</th>
				<th width="7%">زیردسته</th>
				<th width="7%">ویرایش</th>
				<th width="7%">حذف</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar_del" class="snackbar">حذف با موفقیت انجام شد</div>
		<div id="snackbar_ins" class="snackbar">درج با موفقیت انجام شد</div>
		<div id="snackbar_upd" class="snackbar">ویرایش با موفقیت انجام شد</div>
		<div id="snackbar_err" class="snackbar">عدم دسترسی</div>

	</div>

	<!-- ✅ مودال افزودن/ویرایش دسته -->
	<div class="modal fade" id="category_form_modal" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content" style="background-color: #fff">
				<div class="modal-header text-center">
					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
					<h4 class="modal-title" id="cat_form_title">افزودن دسته جدید</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="cf_id">

					<div class="form-group">
						<label class="required">نام دسته:</label>
						<span style="color:red" id="cf_name_err"></span>
						<input type="text" id="cf_name" class="form-control" placeholder="مثلاً: لپتاپ">
					</div>

					<div class="form-group">
						<label>دسته والد:</label>
						<select id="cf_parent" class="form-control">
							<option value="0">— سطح اول —</option>
						</select>
						<input type="hidden" id="cf_parent_hidden">
					</div>

					<div class="form-group">
						<label>توضیحات:</label>
						<input type="text" id="cf_details" class="form-control" placeholder="توضیحات اختیاری">
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="save_category_form" class="btn btn-success">ذخیره</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal">لغو</button>
				</div>
			</div>
		</div>
	</div>

<?php }?>


<script type="text/javascript" language="javascript" >

	$(document).ready(function(){
		<?php if ($this->session->flashdata('success')): ?>
		showSnackbar('upd');
		<?php endif; ?>

		<?php if ($this->session->flashdata('error')): ?>
		showSnackbar('err');
		<?php endif; ?>

		// ✅ توجه: چون ترتیب درختی رو خودمون توی PHP می‌سازیم، سمت کلاینت
		// (بدون serverSide) کار می‌کنیم تا کل داده یکجا بیاد و ترتیبش به‌هم نریزه.
		var dataTable = $('#cat_data').DataTable({
			language: {
				lengthMenu: "نمایش _MENU_ رکورد هر صفحه",
				zeroRecords: "متاسفانه موردی یافت نشد",
				info: "صفحه _PAGE_ از _PAGES_",
				infoEmpty: "موردی یافت نشد",
				infoFiltered: "(فیلتر _MAX_ رکورد)",
				search: "جستجو ",
				loadingRecords: "درحال بارگذاری",
				processing: "در حال پردازش",
				paginate: { first: "ابتدا", last: "انتها", next: "بعدی", previous: "قبلی" },
				aria: { sortAscending: ": حالت صعودی فعال", sortDescending: ": حالت نزولی فعال" }
			},
			"processing": true,
			"serverSide": false,
			"ordering": false, // ✅ ترتیب درختی نباید با sort ستون به‌هم بریزه
			"ajax": {
				url: "<?php echo base_url() . 'admin/categories_list'; ?>",
				type: "POST",
				dataSrc: "data"
			},
			"columnDefs": [
				{ "targets": [0, 4, 5, 6, 7, 8], "searchable": false }
			],
		});


	});

	function showSnackbar(type) {
		var el = document.getElementById('snackbar_' + type);
		if (!el) return;
		el.className = 'snackbar show';
		setTimeout(function () { el.className = el.className.replace('show', ''); }, 3000);
	}

	function showError(message) {
		var el = document.getElementById('snackbar_err');
		el.innerText = message || 'خطا در انجام عملیات';
		el.className = 'snackbar show';
		setTimeout(function () { el.className = el.className.replace('show', ''); }, 3000);
	}

	function handleAjaxResponse(res, onSuccess) {
		if (res.status == 1) {
			onSuccess();
		} else if (res.message === 'session_expired') {
			alert('نشست شما منقضی شده است. لطفا دوباره وارد شوید.');
			window.location.href = "<?= base_url('admin/login_page') ?>";
		} else {
			alert(res.message ?? 'خطا در انجام عملیات');
		}
	}

	// ✅ پر کردن select دسته والد (با حذف خود دسته و زیرمجموعه‌هاش موقع ویرایش)
	function loadParentOptions(excludeId, selectedId, disabled) {
		$.ajax({
			url: "<?= base_url('admin/get_categories_for_select') ?>",
			method: "POST",
			data: { exclude_id: excludeId || '' },
			success: function (response) {
				var res = JSON.parse(response);
				var select = $('#cf_parent');
				select.html('<option value="0">— سطح اول —</option>');

				if (res.status == 1) {
					res.options.forEach(function (opt) {
						var selected = (selectedId && String(opt.id) === String(selectedId)) ? 'selected' : '';
						select.append('<option value="' + opt.id + '" ' + selected + '>' + opt.label + '</option>');
					});
				}

				// ✅ ست کردن hidden و disabled
				select.prop('disabled', !!disabled);
				$('#cf_parent_hidden').val(selectedId || '0');
			}
		});
	}

	// ================== افزودن / ویرایش ==================

	// دسته جدید (سطح اول)
	$('#new_cat').on('click', function(){
		$('#cat_form_title').text('افزودن دسته جدید');
		$('#cf_id').val('');
		$('#cf_name').val('');
		$('#cf_details').val('');
		$('#cf_name_err').text('');
		loadParentOptions(null, null, false);
		$('#category_form_modal').modal();
	});

	// افزودن زیردسته (والد از قبل انتخاب شده)
	$(document).on('click', '#add_sub', function(e){
		if ($(this).data('no-permission')) {
			e.preventDefault();
			alert('شما دسترسی انجام این عملیات را ندارید!');
			return false;
		}
		var parent_id = $(this).attr('cat_id');

		$('#cat_form_title').text('افزودن زیردسته');
		$('#cf_id').val('');
		$('#cf_name').val('');
		$('#cf_details').val('');
		$('#cf_name_err').text('');
		loadParentOptions(null, parent_id, true); // ✅ قفل شده
		$('#category_form_modal').modal();
	});

	// ویرایش
	$(document).on('click', '#edit_category', function(e){
		if ($(this).data('no-permission')) {
			e.preventDefault();
			alert('شما دسترسی انجام این عملیات را ندارید!');
			return false;
		}
		var id = $(this).attr('cat_id');

		$.ajax({
			url: "<?= base_url('admin/get_category') ?>",
			method: "POST",
			data: { id: id },
			success: function (response) {
				var res = JSON.parse(response);
				if (res.status == 1) {
					$('#cat_form_title').text('ویرایش دسته');
					$('#cf_id').val(res.data.id);
					$('#cf_name').val(res.data.name_cat);
					$('#cf_details').val(res.data.details);
					$('#cf_name_err').text('');
					loadParentOptions(res.data.id, res.data.parentId, false);
					$('#category_form_modal').modal();
				} else {
					alert(res.message ?? 'خطا در دریافت اطلاعات');
				}
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	});

	// ذخیره (افزودن یا ویرایش)
	$('#save_category_form').on('click', function(){
		var id = $('#cf_id').val();
		var name = $('#cf_name').val().trim();

		// ✅ اگه select disabled بود، از hidden بخون
		var parentId = $('#cf_parent').prop('disabled')
			? $('#cf_parent_hidden').val()
			: $('#cf_parent').val();

		var details = $('#cf_details').val().trim();

		$('#cf_name_err').text('');
		if (!name) { $('#cf_name_err').text('نام دسته الزامی است'); return; }

		var url = id ? "<?= base_url('admin/update_category') ?>" : "<?= base_url('admin/insert_category') ?>";
		var data = { name_cat: name, parentId: parentId, details: details };
		if (id) data.id = id;

		$.ajax({
			url: url,
			method: "POST",
			data: data,
			success: function (response) {
				var res = JSON.parse(response);
				handleAjaxResponse(res, function() {
					$('#category_form_modal').modal('toggle');
					$('#cat_data').DataTable().ajax.reload(null, false);
					showSnackbar('upd');
				});
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	});

	// ================== حذف ==================

	function deleteCategories(cat_ids, confirmMessage) {
		if (!confirm(confirmMessage)) return;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>admin/soft_delete_category",
			data: { cat_ids: cat_ids },
			success: function (response) {
				var res = JSON.parse(response);
				handleAjaxResponse(res, function() {
					$('#cat_data').DataTable().ajax.reload(null, false);
					showSnackbar('del');
				});
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	}

	$(document).on('click', '#delete', function(e){
		if ($(this).data('no-permission')) {
			e.preventDefault();
			alert('شما دسترسی انجام این عملیات را ندارید!');
			return false;
		}
		var cat_id = $(this).attr('cat_id');
		deleteCategories([cat_id], 'آیا از حذف این دسته اطمینان دارید؟');
	});

	$('#delete_selected').on('click', function(){
		var cat_ids = [];
		$("input:checkbox[name='row-check']:checked").each(function() {
			cat_ids.push($(this).attr('cat_id'));
		});
		if (cat_ids.length === 0) { alert('حداقل یک رکورد انتخاب کنید.'); return; }
		deleteCategories(cat_ids, 'آیا از حذف دسته‌های انتخاب شده اطمینان دارید؟');
	});

	// ================== فعال/غیرفعال ==================

	function toggleCategoryStatus(cat_ids, status) {
		$.ajax({
			url: "<?= base_url('admin/toggle_category_status') ?>",
			method: "POST",
			data: { cat_ids: cat_ids, status: status },
			success: function (response) {
				var res = JSON.parse(response);
				handleAjaxResponse(res, function() {
					$('#cat_data').DataTable().ajax.reload(null, false);
					showSnackbar('upd');
				});
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	}

	$(document).on('click', '#active, #deactive', function(e){
		if ($(this).data('no-permission')) {
			e.preventDefault();
			alert('شما دسترسی انجام این عملیات را ندارید!');
			return false;
		}
		var cat_id = $(this).attr('cat_id');
		var status = $(this).attr('id') === 'active' ? 1 : 0;
		toggleCategoryStatus([cat_id], status);
	});

	$(document).on('click', '#active_selected, #deactive_selected', function(){
		var cat_ids = [];
		$("input[name='row-check']:checked").each(function() {
			cat_ids.push($(this).attr('cat_id'));
		});
		if (cat_ids.length === 0) { alert('حداقل یک رکورد انتخاب کنید.'); return; }
		var status = $(this).attr('id') === 'active_selected' ? 1 : 0;
		toggleCategoryStatus(cat_ids, status);
	});

	// ================== چک‌باکس‌ها ==================

	$("#check_all").on("click", function () {
		$("input:checkbox[name='row-check']").prop("checked", $(this).prop("checked"));
	});

	$(document).on("change", "input:checkbox[name='row-check']", function () {
		var total = $("input:checkbox[name='row-check']").length;
		var checked = $("input:checkbox[name='row-check']:checked").length;
		$("#check_all").prop("checked", total === checked);
	});

</script>
