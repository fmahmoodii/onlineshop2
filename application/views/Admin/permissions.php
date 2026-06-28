


	<div class="container box" id="content" >
		<!--		<a href="--><?php //echo base_url('admin/edit_off_code/').$p->id.'/'.$p->code ?><!--">-->

		<div>
			<a href="<?php echo base_url('admin/insert_per') ?>">
				<button class="btn btn-success" id="new_per"
						style="outline: unset" >کاربر جدید
				</button>
			</a>
			<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" style="outline: none" >حذف همه</button>
			<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش همه</button>
			<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
			<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
		</div>
		<br>
		<br>
		<br>
		<table id="per_data" class="table table-bordered table-striped">
			<thead>
			<tr id="id_per">
				<!--					<th width="5%">icon</th>-->
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
				</th>

				<th>نام دسترسی</th>
				<th>نوع دسترسی</th>
				<th>ماژول</th>
				<th>عملیات</th>
				<th>حذف</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar_del" class="snackbar">حذف با موفقیت انجام شد</div>
		<div id="snackbar_ins" class="snackbar">درج با موفقیت انجام شد</div>
		<div id="snackbar_upd" class="snackbar">ویرایش با موفقیت انجام شد</div>
		<div id="snackbar_err" class="snackbar">عدم دسترسی</div>


	</div>




<script type="text/javascript" language="javascript" >

	$(document).ready(function(){
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
					"targets":[0, 4],
					"orderable":false,
				},
			],
		});
	});


	$(document).on('click', '#active, #deactive', function(){
		var prm_id = $(this).attr('prm_id');
		var status = $(this).attr('id') === 'active' ? 1 : 0;

		$.ajax({
			url: "<?= base_url('admin/toggle_user_status') ?>",
			method: "POST",
			data: { prm_id: prm_id, status: status },
			success: function() {
				$('#per_data').DataTable().ajax.reload(null, false);
				showSnackbar('upd'); // پیام فعال یا غیرفعال
			}
		});
	});

	$(document).on('click', '#active_selected, #deactive_selected', function(){
		var prm_ids = [];
		$("input[name='row-check']:checked").each(function() {
			prm_ids.push($(this).attr('prm_id'));
		});

		if(prm_ids.length > 0){
			// تعیین وضعیت بر اساس دکمه کلیک شده
			var status = $(this).attr('id') === 'active_selected' ? 1 : 0;

			$.ajax({
				url: "<?= base_url('admin/toggle_user_status') ?>",
				method: "POST",
				data: { prm_ids: prm_ids, status: status },
				success: function() {
					$('#per_data').DataTable().ajax.reload(null, false);
					showSnackbar('upd'); // پیام گروهی
				}
			});
		} else {
			alert('حداقل یک رکورد انتخاب کنید.');
		}
	});


	//If check_all checked then check all table rows
	$("#check_all").on("click", function () {
		if ($("input:checkbox").prop("checked")) {
			$("input:checkbox[name='row-check']").prop("checked", true);

		} else {
			$("input:checkbox[name='row-check']").prop("checked", false);

		}
	});

	// Check each table row checkbox
	$("input:checkbox[name='row-check']").on("change", function () {
		var total_check_boxes = $("input:checkbox[name='row-check']").length;
		var total_checked_boxes = $("input:checkbox[name='row-check']:checked").length;

		// If all checked manually then check check_all checkbox
		if (total_check_boxes === total_checked_boxes) {
			$("#check_all").prop("checked", true);
		}
		else {
			$("#check_all").prop("checked", false);
		}
	});


</script>
