<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content" >

		<div>
			<?php if (!empty($action_permissions['add']) && $action_permissions['add']): ?>
				<button class="btn btn-success" id="new_per" style="outline: unset" >
					دسترسی جدید
				</button>
			<?php else: ?>
				<button class="btn btn-success" id="new_per" style="outline: unset"
						onclick="showError('شما دسترسی ایجاد ندارید')">
					دسترسی جدید
				</button>
			<?php endif; ?>

			<?php if (!empty($action_permissions['edit']) && $action_permissions['edit']): ?>
				<button id='bulk_assign_btn' class="btn btn-sm ml-3" style="background-color:#5bc0de;color:#fff;border-color:#46b8da;">
					<i class="fa fa-users"></i> افزودن نقش به انتخاب‌شده‌ها
				</button>
			<?php endif; ?>

			<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" style="outline: none">حذف همه</button>
			<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
			<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
		</div>
		<br>
		<br>
		<br>
		<table id="per_data" class="table table-bordered table-striped">
			<thead>
			<tr id="id_per">
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
				</th>
				<th width="15%">
					نام دسترسی<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="15%">
					کلید<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="15%">
					ماژول<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="15%">نقش‌های متصل</th>
				<th width="8%">مدیریت نقش‌ها</th>
				<th width="8%">ویرایش</th>
				<th width="8%">وضعیت</th>
				<th width="8%">حذف</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar_del" class="snackbar">حذف با موفقیت انجام شد</div>
		<div id="snackbar_ins" class="snackbar">درج با موفقیت انجام شد</div>
		<div id="snackbar_upd" class="snackbar">ویرایش با موفقیت انجام شد</div>
		<div id="snackbar_err" class="snackbar">عدم دسترسی</div>

	</div>

	<!-- ✅ مودال مدیریت نقش‌های یک دسترسی (تکی) -->
	<div class="modal fade" id="roles_modal" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content" style="background-color: #fff">
				<div class="modal-header text-center">
					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
					<h4 class="modal-title">مدیریت نقش‌های این دسترسی</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="modal_permission_id">

					<input type="text" class="form-control role-search" data-target="#roles_checkbox_list" placeholder="جستجوی نقش..." style="margin-bottom: 10px;">

					<div id="roles_checkbox_list" style="max-height: 300px; overflow-y: auto;">
						<?php foreach ($roles as $role): ?>
							<div class="form-check role-item" data-name="<?php echo htmlspecialchars(mb_strtolower($role->role_name)); ?>">
								<input type="checkbox" class="role-checkbox" value="<?php echo $role->id; ?>" id="role_<?php echo $role->id; ?>">
								<label for="role_<?php echo $role->id; ?>"><?php echo htmlspecialchars($role->role_name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="save_roles" class="btn btn-success">ذخیره</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal">لغو</button>
				</div>
			</div>
		</div>
	</div>

	<!-- ✅ مودال افزودن گروهی نقش به چند دسترسی انتخاب‌شده -->
	<div class="modal fade" id="bulk_roles_modal" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content" style="background-color: #fff">
				<div class="modal-header text-center">
					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
					<h4 class="modal-title">افزودن نقش به دسترسی‌های انتخاب‌شده</h4>
				</div>
				<div class="modal-body">
					<p class="text-muted">نقش‌هایی که می‌خوای به <b id="bulk_count_label">0</b> دسترسی انتخاب‌شده اضافه بشه رو انتخاب کن. (نقش‌های فعلی هر دسترسی حذف نمی‌شن، فقط موارد جدید اضافه می‌شن)</p>

					<input type="text" class="form-control role-search" data-target="#bulk_roles_checkbox_list" placeholder="جستجوی نقش..." style="margin-bottom: 10px;">

					<div id="bulk_roles_checkbox_list" style="max-height: 300px; overflow-y: auto;">
						<?php foreach ($roles as $role): ?>
							<div class="form-check role-item" data-name="<?php echo htmlspecialchars(mb_strtolower($role->role_name)); ?>">
								<input type="checkbox" class="bulk-role-checkbox" value="<?php echo $role->id; ?>" id="bulk_role_<?php echo $role->id; ?>">
								<label for="bulk_role_<?php echo $role->id; ?>"><?php echo htmlspecialchars($role->role_name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="save_bulk_roles" class="btn btn-success">افزودن</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal">لغو</button>
				</div>
			</div>
		</div>
	</div>

	<!-- ✅ مودال افزودن/ویرایش دسترسی -->
	<div class="modal fade" id="permission_form_modal" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content" style="background-color: #fff">
				<div class="modal-header text-center">
					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
					<h4 class="modal-title" id="permission_form_title">افزودن دسترسی جدید</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="permission_form_id">

					<div class="form-group">
						<label class="required">نام دسترسی:</label>
						<span style="color:red" id="pf_name_err"></span>
						<input type="text" id="pf_name" class="form-control" placeholder="مثلاً: مدیریت کاربران">
					</div>

					<div class="form-group">
						<label class="required">کلید (key_name):</label>
						<span style="color:red" id="pf_key_err"></span>
						<input type="text" id="pf_key_name" class="form-control" placeholder="مثلاً: view / edit / insert / delete / full">
					</div>

					<div class="form-group">
						<label>ماژول (table_name) - اختیاری:</label>
						<input type="text" id="pf_table_name" class="form-control" placeholder="مثلاً: users (خالی = سراسری)">
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="save_permission_form" class="btn btn-success">ذخیره</button>
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

		var dataTable = $('#per_data').DataTable({
			language: {
				lengthMenu: "نمایش _MENU_ رکورد هر صفحه",
				zeroRecords: "متاسفانه موردی یافت نشد",
				info: "صفحه _PAGE_ از _PAGES_",
				infoEmpty: "موردی یافت نشد",
				infoFiltered: "(فیلتر _MAX_ رکورد)",
				search: "جستجو ",
				loadingRecords: "درحال بارگذاری",
				processing: "در حال پردازش",
				paginate: {
					first: "ابتدا",
					last: "انتها",
					next: "بعدی",
					previous: "قبلی"
				},
				aria: {
					sortAscending: ": حالت صعودی فعال",
					sortDescending: ": حالت نزولی فعال"
				}
			},
			"processing":true,
			"serverSide":true,
			"order":[],
			"ajax":{
				url:"<?php echo base_url() . 'admin/permissions_list'; ?>",
				type:"POST"
			},
			"columnDefs":[
				{
					"targets":[0, 4, 5, 6, 7, 8],
					"orderable":false,
					"searchable":false,
				},
			],
		});

		$('.column-filter').on('keyup change', function () {
			var columnIndex = $(this).closest('th').index();
			dataTable.column(columnIndex).search(this.value).draw();
		});
		$('.column-filter').on('click', function (e) {
			e.stopPropagation();
		});

		// ✅ جستجو داخل لیست چک‌باکس نقش‌ها (هم مودال تکی، هم مودال گروهی)
		$('.role-search').on('keyup', function () {
			var query = $(this).val().trim().toLowerCase();
			var target = $($(this).data('target'));

			target.find('.role-item').each(function () {
				var name = $(this).data('name').toString();
				$(this).toggle(name.indexOf(query) !== -1);
			});
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

	// ================== مدیریت نقش تکی ==================

	$(document).on('click', '#manage_roles', function(e){
		if ($(this).data('no-permission')) {
			e.preventDefault();
			alert('شما دسترسی انجام این عملیات را ندارید!');
			return false;
		}

		var permission_id = $(this).attr('prm_id');
		$('#modal_permission_id').val(permission_id);
		$('.role-checkbox').prop('checked', false);
		$('#roles_modal .role-search').val('').trigger('keyup');

		$.ajax({
			url: "<?= base_url('admin/get_permission_roles') ?>",
			method: "POST",
			data: { permission_id: permission_id },
			success: function (response) {
				var res = JSON.parse(response);
				if (res.status == 1) {
					res.role_ids.forEach(function(id){
						$('#role_' + id).prop('checked', true);
					});
					$('#roles_modal').modal();
				} else {
					alert(res.message ?? 'خطا در دریافت اطلاعات');
				}
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	});

	$(document).on('click', '#save_roles', function(){
		var permission_id = $('#modal_permission_id').val();
		var role_ids = [];

		$('.role-checkbox:checked').each(function(){
			role_ids.push($(this).val());
		});

		$.ajax({
			url: "<?= base_url('admin/save_permission_roles') ?>",
			method: "POST",
			data: { permission_id: permission_id, role_ids: role_ids },
			success: function (response) {
				var res = JSON.parse(response);
				handleAjaxResponse(res, function() {
					$('#roles_modal').modal('toggle');
					$('#per_data').DataTable().ajax.reload(null, false);
					showSnackbar('upd');
				});
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	});

	// ================== افزودن گروهی نقش ==================

	$('#bulk_assign_btn').on('click', function(){
		var prm_ids = [];
		$("input:checkbox[name='row-check']:checked").each(function() {
			prm_ids.push($(this).attr('prm_id'));
		});

		if (prm_ids.length === 0) {
			alert('حداقل یک دسترسی انتخاب کنید.');
			return;
		}

		$('#bulk_count_label').text(prm_ids.length);
		$('.bulk-role-checkbox').prop('checked', false);
		$('#bulk_roles_modal .role-search').val('').trigger('keyup');
		$('#bulk_roles_modal').data('prm_ids', prm_ids);
		$('#bulk_roles_modal').modal();
	});

	$('#save_bulk_roles').on('click', function(){
		var prm_ids = $('#bulk_roles_modal').data('prm_ids') || [];
		var role_ids = [];

		$('.bulk-role-checkbox:checked').each(function(){
			role_ids.push($(this).val());
		});

		if (role_ids.length === 0) {
			alert('حداقل یک نقش انتخاب کنید.');
			return;
		}

		$.ajax({
			url: "<?= base_url('admin/bulk_assign_roles') ?>",
			method: "POST",
			data: { prm_ids: prm_ids, role_ids: role_ids },
			success: function (response) {
				var res = JSON.parse(response);
				handleAjaxResponse(res, function() {
					$('#bulk_roles_modal').modal('toggle');
					$('#per_data').DataTable().ajax.reload(null, false);
					showSnackbar('upd');
				});
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	});

	// ================== افزودن / ویرایش دسترسی ==================

	// باز کردن مودال برای افزودن جدید
	$('#new_per').on('click', function(){
		$('#permission_form_title').text('افزودن دسترسی جدید');
		$('#permission_form_id').val('');
		$('#pf_name').val('');
		$('#pf_key_name').val('');
		$('#pf_table_name').val('');
		$('#pf_name_err, #pf_key_err').text('');
		$('#permission_form_modal').modal();
	});

	// باز کردن مودال برای ویرایش
	$(document).on('click', '#edit_permission', function(e){
		if ($(this).data('no-permission')) {
			e.preventDefault();
			alert('شما دسترسی انجام این عملیات را ندارید!');
			return false;
		}

		var id = $(this).attr('prm_id');

		$.ajax({
			url: "<?= base_url('admin/get_permission') ?>",
			method: "POST",
			data: { id: id },
			success: function (response) {
				var res = JSON.parse(response);
				if (res.status == 1) {
					$('#permission_form_title').text('ویرایش دسترسی');
					$('#permission_form_id').val(res.data.id);
					$('#pf_name').val(res.data.name);
					$('#pf_key_name').val(res.data.key_name);
					$('#pf_table_name').val(res.data.table_name);
					$('#pf_name_err, #pf_key_err').text('');
					$('#permission_form_modal').modal();
				} else {
					alert(res.message ?? 'خطا در دریافت اطلاعات');
				}
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	});

	// ذخیره (افزودن یا ویرایش، بسته به وجود id)
	$('#save_permission_form').on('click', function(){
		var id = $('#permission_form_id').val();
		var name = $('#pf_name').val().trim();
		var key_name = $('#pf_key_name').val().trim();
		var table_name = $('#pf_table_name').val().trim();

		$('#pf_name_err, #pf_key_err').text('');

		var hasError = false;
		if (!name) { $('#pf_name_err').text('نام دسترسی الزامی است'); hasError = true; }
		if (!key_name) { $('#pf_key_err').text('کلید دسترسی الزامی است'); hasError = true; }
		if (hasError) return;

		var url = id ? "<?= base_url('admin/update_permission') ?>" : "<?= base_url('admin/insert_permission') ?>";
		var data = { name: name, key_name: key_name, table_name: table_name };
		if (id) data.id = id;

		$.ajax({
			url: url,
			method: "POST",
			data: data,
			success: function (response) {
				var res = JSON.parse(response);
				handleAjaxResponse(res, function() {
					$('#permission_form_modal').modal('toggle');
					$('#per_data').DataTable().ajax.reload(null, false);
					showSnackbar('upd');
				});
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	});

	// ================== حذف ==================

	function deletePermissions(prm_ids, confirmMessage) {
		if (!confirm(confirmMessage)) {
			return;
		}

		$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>admin/soft_delete_permission",
			data: { prm_ids: prm_ids },
			success: function (response) {
				var res = JSON.parse(response);
				handleAjaxResponse(res, function() {
					$('#per_data').DataTable().ajax.reload(null, false);
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
		var prm_id = $(this).attr('prm_id');
		deletePermissions([prm_id], 'آیا از حذف این دسترسی اطمینان دارید؟');
	});

	$('#delete_selected').on('click', function(){
		var prm_ids = [];
		$("input:checkbox[name='row-check']:checked").each(function() {
			prm_ids.push($(this).attr('prm_id'));
		});

		if (prm_ids.length === 0) {
			alert('حداقل یک رکورد انتخاب کنید.');
			return;
		}

		deletePermissions(prm_ids, 'آیا از حذف دسترسی‌های انتخاب شده اطمینان دارید؟');
	});

	// ================== فعال/غیرفعال ==================

	$(document).on('click', '#active, #deactive', function(e){
		if ($(this).data('no-permission')) {
			e.preventDefault();
			alert('شما دسترسی انجام این عملیات را ندارید!');
			return false;
		}

		var prm_id = $(this).attr('prm_id');
		var status = $(this).attr('id') === 'active' ? 1 : 0;

		$.ajax({
			url: "<?= base_url('admin/toggle_permission_status') ?>",
			method: "POST",
			data: { prm_ids: [prm_id], status: status },
			success: function (response) {
				var res = JSON.parse(response);
				handleAjaxResponse(res, function() {
					$('#per_data').DataTable().ajax.reload(null, false);
					showSnackbar('upd');
				});
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	});

	$(document).on('click', '#active_selected, #deactive_selected', function(){
		var prm_ids = [];
		$("input[name='row-check']:checked").each(function() {
			prm_ids.push($(this).attr('prm_id'));
		});

		if(prm_ids.length === 0){
			alert('حداقل یک رکورد انتخاب کنید.');
			return;
		}

		var status = $(this).attr('id') === 'active_selected' ? 1 : 0;

		$.ajax({
			url: "<?= base_url('admin/toggle_permission_status') ?>",
			method: "POST",
			data: { prm_ids: prm_ids, status: status },
			success: function (response) {
				var res = JSON.parse(response);
				handleAjaxResponse(res, function() {
					$('#per_data').DataTable().ajax.reload(null, false);
					showSnackbar('upd');
				});
			},
			error: function () {
				alert('خطا در ارتباط با سرور');
			}
		});
	});

	// ================== چک‌باکس‌ها ==================

	$("#check_all").on("click", function () {
		if ($("input:checkbox").prop("checked")) {
			$("input:checkbox[name='row-check']").prop("checked", true);
		} else {
			$("input:checkbox[name='row-check']").prop("checked", false);
		}
	});

	$(document).on("change", "input:checkbox[name='row-check']", function () {
		var total_check_boxes = $("input:checkbox[name='row-check']").length;
		var total_checked_boxes = $("input:checkbox[name='row-check']:checked").length;

		if (total_check_boxes === total_checked_boxes) {
			$("#check_all").prop("checked", true);
		} else {
			$("#check_all").prop("checked", false);
		}
	});

</script>
