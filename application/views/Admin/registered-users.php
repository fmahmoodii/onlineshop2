


<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content" >
<!--		<a href="--><?php //echo base_url('admin/edit_off_code/').$p->id.'/'.$p->code ?><!--">-->

		<div>
			<a href="<?php echo base_url('admin/insert_user') ?>">
				<button class="btn btn-success" id="new_user"
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
		<table id="usr_data" class="table table-bordered table-striped">
			<thead>
			<tr id="id_prof">
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
				</th>
				<th width="15%">نوع کاربر</th>
				<th width="15%">نام</th>
				<th width="10%">نام خانوادگی</th>
				<th width="10%">شماره موبایل</th>
				<th width="10%">تاریخ ایجاد</th>
				<th width="10%">آخرین ویرایش</th>
				<th width="10%">عملیات</th>
				<th width="10%">رمز عبور</th>
				<th width="10%">ویرایش</th>
				<th width="10%">حذف</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar_del" class="snackbar">حذف با موفقیت انجام شد</div>
		<div id="snackbar_ins" class="snackbar">درج با موفقیت انجام شد</div>
		<div id="snackbar_upd" class="snackbar">ویرایش با موفقیت انجام شد</div>

	</div>

	<div class="modal fade" id="reset_pass" role="dialog">
		<div class="modal-dialog modal-md">
			<!-- Modal content-->
			<div class="modal-content " style="background-color: #fff">
				<div class="modal-header text-center">
					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i
								class="fa fa-close"></i></button>
					<h4 class="modal-title">بازنشانی رمز عبور</h4>
				</div>
				<div class="modal-body">

					<input autocomplete="off" type="hidden" id="id_modal">

					<label class="required">رمزعبور جدید:</label><br>
					<input autocomplete="off" id="new_pass_modal" class="form-control positive password" type="text" placeholder="رمزعبور جدید"><br>
					<label class="required"> تکرار رمزعبور جدید:</label><br>
					<input autocomplete="off" id="re_new_pass_modal" class="form-control positive password" type="text" placeholder="تکرار رمزعبور جدید">




				</div>
				<div class="modal-footer">
					<button style="float: right;" id="accept" class="btn btn-success" >ثبت
					</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal">لغو</button>

				</div>
			</div>
		</div>
	</div>
<?php }?>


<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		var dataTable = $('#usr_data').DataTable({
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
				url:"<?php echo base_url() . 'admin/users_list'; ?>",
				type:"POST"
			},
			"columnDefs":[
				{
					"targets":[0, 8, 9, 10],
					"orderable":false,
				},
			],
		});
	});

	function showSnackbar(type) {
		// type: 'del', 'ins', 'upd'
		var id = 'snackbar_' + type;
		var x = document.getElementById(id);
		x.className = "snackbar show";

		setTimeout(function() {
			x.className = x.className.replace("show", "");
		}, 3000);
	}


	// حذف تکی
	$(document).on('click', '#delete', function(){
		var user_id = $(this).attr("user_id");
		if(confirm('آیا از حذف محصول اطمینان دارید؟')) {
			$.ajax({
				url:"<?php echo base_url(); ?>admin/delete_user",
				method:"POST",
				data:{ user_ids: [user_id] }, // ارسال به صورت آرایه
				success:function(){
					$('#usr_data').DataTable().ajax.reload(null, false);
					showSnackbar('del');
				}
			});
		}
	});

	// حذف چندتایی (checkbox)
	$("#delete_selected").on("click", function () {
		var user_ids = [];
		$("input:checkbox[name='row-check']:checked").each(function() {
			user_ids.push($(this).attr("user_id"));
		});

		if(user_ids.length > 0) {
			if(confirm('آیا از حذف محصولات انتخاب شده اطمینان دارید؟')) {
				$.ajax({
					type: "POST",
					url:"<?php echo base_url(); ?>admin/delete_user",
					data: { user_ids: user_ids }, // ارسال همان آرایه
					success: function(){
						$('#usr_data').DataTable().ajax.reload(null, false);
						showSnackbar('del');
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#msg").html("<span style='color:red;'>" + textStatus + " " + errorThrown + "</span>");
					}
				});
			}
		} else {
			alert('حداقل یک رکورد انتخاب کنید.');
		}
	});

	$(document).on('click', '#active, #deactive', function(){
		var user_id = $(this).attr('user_id');
		var status = $(this).attr('id') === 'active' ? 1 : 0;

		$.ajax({
			url: "<?= base_url('admin/toggle_user_status') ?>",
			method: "POST",
			data: { user_id: user_id, status: status },
			success: function() {
				$('#usr_data').DataTable().ajax.reload(null, false);
				showSnackbar('upd'); // پیام فعال یا غیرفعال
			}
		});
	});

	$(document).on('click', '#active_selected, #deactive_selected', function(){
		var user_ids = [];
		$("input[name='row-check']:checked").each(function() {
			user_ids.push($(this).attr('user_id'));
		});

		if(user_ids.length > 0){
			// تعیین وضعیت بر اساس دکمه کلیک شده
			var status = $(this).attr('id') === 'active_selected' ? 1 : 0;

			$.ajax({
				url: "<?= base_url('admin/toggle_user_status') ?>",
				method: "POST",
				data: { user_ids: user_ids, status: status },
				success: function() {
					$('#usr_data').DataTable().ajax.reload(null, false);
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



	//------نمایش مودال---------
	$(document).on('click', '#reset', function(e){
		var id=$(this).attr("user_id");
		$("#id_modal").val(id);
		$("#reset_pass").modal();
	});
	//---------عملیات ویرایش-----------
	$(document).on('click', '#accept', function(e){
		var id = $("#id_modal").val();
		var new_pass = $("#new_pass_modal").val();
		var re_new_pass = $("#re_new_pass_modal").val();

		$.post('<?= base_url("admin/reset_pass") ?>',
			{ 'id': id, 'new_pass': new_pass, 're_new_pass': re_new_pass },
			function (data) {
				if (data == 1){
					$('#reset_pass').modal('toggle');
					$("#new_pass_modal, #re_new_pass_modal").val('');
					$('#usr_data').DataTable().ajax.reload(null, false);

					// نمایش اسنکبار موفقیت
					showSnackbar('upd', 'رمز با موفقیت بازنشانی شد');
				} else {
					showSnackbar('del', 'خطا در بازنشانی رمز');
				}
			}
		);
	});



</script>
