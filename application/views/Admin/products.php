<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
	/* Snackbar - یک کلاس */
	.snackbar {
		visibility: hidden;
		min-width: 250px;
		background-color: #333;
		color: #fff;
		text-align: center;
		border-radius: 4px;
		padding: 16px;
		position: fixed;
		z-index: 5000;
		left: 50%;
		bottom: 30px;
		transform: translateX(-50%);
	}

	.snackbar.show {
		visibility: visible;
		animation: fadein 0.5s, fadeout 0.5s 2.5s;
	}

	@keyframes fadein {
		from {
			opacity: 0;
			transform: translateX(-50%) translateY(20px);
		}
		to {
			opacity: 1;
			transform: translateX(-50%) translateY(0);
		}
	}

	@keyframes fadeout {
		from {
			opacity: 1;
		}
		to {
			opacity: 0;
		}
	}

	/* Loading Overlay */
	.loading-overlay {
		display: none;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(15, 10, 10, 0.59);
		z-index: 4999;
		justify-content: center;
		align-items: center;
	}

	.loading-overlay.active {
		display: flex;
	}

	/* CSS Spinner */
	.spinner {
		border: 4px solid rgba(255, 255, 255, 0.3);
		border-top: 4px solid #fff;
		border-radius: 50%;
		width: 40px;
		height: 40px;
		animation: spin 1s linear infinite;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

	.content-spacing {
		margin-top: 1.5rem;
		margin-bottom: 1.5rem;
	}
</style>

<?php if ($this->session->userdata('id')): ?>

	<div class="container box" id="content">
		<div id="loading-overlay" class="loading-overlay">
			<div class="spinner"></div>
		</div>

		<!-- Toolbar -->
		<div class="mb-3">
			<?php if ($action_permissions['add']): ?>
				<a href="<?php echo base_url('admin/insert_product'); ?>" class="btn btn-sm btn-success">
					<i class="fa fa-plus"></i> ورود محصول جدید
				</a>
			<?php endif; ?>

			<?php if ($action_permissions['edit']): ?>
				<button id="active_selected" class="btn btn-sm btn-primary">
					<i class="fa fa-check"></i> فعال‌سازی همه
				</button>
				<button id="deactive_selected" class="btn btn-sm btn-secondary">
					<i class="fa fa-times"></i> غیرفعال‌سازی همه
				</button>
				<button id="update_selected" class="btn btn-sm btn-warning">
					<i class="fa fa-edit"></i> ویرایش همه
				</button>
			<?php endif; ?>

			<?php if ($action_permissions['delete']): ?>
				<button id="delete_selected" class="btn btn-sm btn-danger">
					<i class="fa fa-trash"></i> حذف همه
				</button>
			<?php endif; ?>
		</div>

		<div id="msg"></div>

		<div class="content-spacing"></div>

		<!-- DataTable -->
		<table id="prd_data" class="table table-bordered table-striped" style="width: 100%">
			<thead>
			<tr>
				<th width="5%">
					<label for="check_all">همه</label>
					<input type="checkbox" id="check_all">
				</th>
				<th>نام کالا</th>
				<th>قیمت</th>
				<th>دسته‌بندی</th>
				<th>تاریخ ایجاد</th>
				<th>آخرین ویرایش</th>
				<th>وضعیت</th>
				<th>کپی</th>
				<th>ویرایش</th>
				<th>حذف</th>
			</tr>
			</thead>
		</table>

		<!-- Snackbars -->
		<div id="snackbar" class="snackbar">محصول حذف شد</div>
		<div id="snackbar2" class="snackbar">عملیات انجام شد</div>

		<!-- Modal -->
		<div class="modal fade" id="show_edit" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">ویرایش محصولات انتخاب‌شده</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">
						<div class="form-group mb-3">
							<label for="name">نام محصول:</label>
							<input id="name" type="text" class="form-control" placeholder="نام محصول">
						</div>
						<div class="form-group">
							<label for="text1">توضیحات:</label>
							<textarea id="text1" class="ckeditor" rows="10" name="text1"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button id="btn_edit" class="btn btn-success">ثبت ویرایش</button>
						<button class="btn btn-secondary" data-bs-dismiss="modal">لغو</button>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php endif; ?>




<script type="text/javascript">
	$(document).ready(function() {
		var permissions = {
			edit: <?php echo $action_permissions['edit'] ? 'true' : 'false'; ?>,
			delete: <?php echo $action_permissions['delete'] ? 'true' : 'false'; ?>,
			add: <?php echo $action_permissions['add'] ? 'true' : 'false'; ?>
		};

		// Initialize DataTable
		var dataTable = $('#prd_data').DataTable({
			processing: true,
			serverSide: true,
			order: [],
			ajax: {
				url: "<?php echo base_url('admin/products_list'); ?>",
				type: "POST"
			},
			columnDefs: [{ targets: [0, 6, 7, 8, 9], orderable: false, searchable: false }],

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
				}
			}
		});

		// Check All / Uncheck All
		$("#check_all").on("click", function() {
			if ($("input:checkbox").prop("checked")) {
				$("input:checkbox[name='row-check']").prop("checked", true);
			} else {
				$("input:checkbox[name='row-check']").prop("checked", false);
			}
		});

		$(document).on("change", "input:checkbox[name='row-check']", function() {
			var total = $("input:checkbox[name='row-check']").length;
			var checked = $("input:checkbox[name='row-check']:checked").length;
			$("#check_all").prop("checked", total === checked);
		});

		function toggleStatus(ids, status) {
			$("#loading-image").show();
			$.ajax({
				url: "<?php echo base_url('admin/toggle_product_status'); ?>",
				method: "POST",
				data: { 'prd_ids[]': ids, 'status': status },
				dataType: 'json',
				success: function(response) {
					$("#loading-image").hide();
					if (response.status == 1) {
						dataTable.ajax.reload(null, false);
						showSnackbar('snackbar2');
					} else {
						alert(response.message || 'خطا در تغییر وضعیت');
					}
				},
				error: function() {
					$("#loading-image").hide();
					alert('خطا در ارتباط با سرور');
				}
			});
		}

		// تکی
		$(document).on('click', '#active, #deactive', function() {
			if (!permissions.edit) { alert('شما دسترسی این عملیات را ندارید'); return; }
			var status = $(this).attr('id') === 'active' ? 1 : 0;
			toggleStatus([$(this).attr('id_p')], status);
		});

		// گروهی
		$('#active_selected, #deactive_selected').on('click', function() {
			if (!permissions.edit) { alert('شما دسترسی این عملیات را ندارید'); return; }
			var ids = getSelectedIds();
			if (ids.length === 0) { alert('هیچ ردیفی انتخاب نشده است'); return; }
			var status = $(this).attr('id') === 'active_selected' ? 1 : 0;
			toggleStatus(ids, status);
		});

		// کپی محصول
		$(document).on('click', '#copy', function() {
			if (!permissions.add) {
				alert('شما دسترسی این عملیات را ندارید');
				return;
			}

			$("#loading-image").show();
			var code = $(this).attr("code_p");
			var id = $(this).attr("id_p");

			$.post('<?php echo base_url()?>admin/copy_product', {
				'code': code,
				'id': id
			}, function(data) {
				$("#loading-image").hide();
				if (data == 1) {
					dataTable.ajax.reload(null, false);
				} else {
					alert('خطا در کپی');
				}
			});
		});

		function doDelete(id_p, code_p, confirmed) {
			$("#loading-image").show();
			$.ajax({
				url: "<?php echo base_url(); ?>admin/delete_prd",
				method: "POST",
				data: { id_p: id_p, code_p: code_p, confirmed: confirmed ? 1 : 0 },
				dataType: 'json',
				success: function(response) {
					$("#loading-image").hide();
					if (response.status == 1) {
						dataTable.ajax.reload(null, false);
						showSnackbar('snackbar');
					} else if (response.needs_confirm) {
						if (confirm(response.message)) {
							doDelete(id_p, code_p, true);
						}
					} else {
						alert(response.message || 'خطا در حذف');
					}
				},
				error: function() {
					$("#loading-image").hide();
					alert('خطا در ارتباط با سرور');
				}
			});
		}

		function deleteMultiple(ids, codes) {
			$("#loading-image").show();
			$.ajax({
				url: "<?php echo base_url(); ?>admin/delete_products",
				method: "POST",
				data: { ids: ids.join(','), codes: codes.join(',') },
				dataType: 'json',
				success: function(response) {
					$("#loading-image").hide();
					if (response.status == 1) {
						dataTable.ajax.reload(null, false);
						showSnackbar('snackbar');
					} else {
						alert(response.message || 'خطا در حذف');
					}
				},
				error: function() {
					$("#loading-image").hide();
					alert('خطا در ارتباط با سرور');
				}
			});
		}

// تکی
		$(document).on('click', '#delete', function() {
			if (!permissions.delete) { alert('شما دسترسی حذف را ندارید'); return; }
			if (!confirm('آیا از حذف محصول اطمینان دارید؟')) return;
			doDelete($(this).attr('id_p'), $(this).attr('code_p'), false);
		});

// گروهی
		$('#delete_selected').on('click', function() {
			if (!permissions.delete) { alert('شما دسترسی حذف را ندارید'); return; }
			var ids = getSelectedIds();
			var codes = getSelectedCodes();
			if (ids.length === 0) { alert('هیچ ردیفی انتخاب نشده است'); return; }
			if (!confirm('آیا از حذف محصولات انتخاب‌شده اطمینان دارید؟')) return;
			deleteMultiple(ids, codes);
		});

		// ویرایش همگانی
		$("#update_selected").on("click", function() {
			if (!permissions.edit) {
				alert('شما دسترسی این عملیات را ندارید');
				return;
			}

			var ids = getSelectedIds();
			if (ids.length === 0) {
				alert('هیچ ردیفی انتخاب نشده است');
				return;
			}

			$("#show_edit").modal();
		});

		// ثبت ویرایش همگانی
		$(document).on('click', '#btn_edit', function() {
			$("#loading-image").show();

			var ids = getSelectedIds();
			var name = $("#name").val();
			var text = CKEDITOR.instances['text1'].getData();

			$.post('<?php echo base_url()?>admin/update_products_checkall', {
				'id': ids.join(','),
				'name': name,
				'text': text
			}, function(data) {
				if (data.includes(1)) {
					$("#loading-image").hide();
					$("#name").val('');
					CKEDITOR.instances['text1'].setData('');
					$("#show_edit").modal('hide');
					dataTable.ajax.reload(null, false);
					showSnackbar('snackbar2');
				}
				$("#loading-image").hide();
			});
		});



		// Helper Functions
		function getSelectedIds() {
			var ids = [];
			$("input:checkbox[name='row-check']:checked").each(function() {
				ids.push($(this).attr('id_p'));
			});
			return ids;
		}

		function getSelectedCodes() {
			var codes = [];
			$("input:checkbox[name='row-check']:checked").each(function() {
				codes.push($(this).attr('code_p'));
			});
			return codes;
		}

		function showSnackbar(id) {
			var x = document.getElementById(id);
			x.className = "show";
			setTimeout(function() {
				x.className = x.className.replace("show", "");
			}, 3000);
		}
	});
</script>
