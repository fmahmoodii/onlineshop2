
<style>/* The snackbar - position it at the bottom and in the middle of the screen */
	#snackbar,#snackbar2 {
		visibility: hidden; /* Hidden by default. Visible on click */
		min-width: 250px; /* Set a default minimum width */
		margin-left: -125px; /* Divide value of min-width by 2 */
		background-color: #333; /* Black background color */
		color: #fff; /* White text color */
		text-align: center; /* Centered text */
		border-radius: 2px; /* Rounded borders */
		padding: 16px; /* Padding */
		position: fixed; /* Sit on top of the screen */
		z-index: 1; /* Add a z-index if needed */
		left: 50%; /* Center the snackbar */
		bottom: 30px; /* 30px from the bottom */
	}

	/* Show the snackbar when clicking on a button (class added with JavaScript) */
	#snackbar.show,#snackbar2.show {
		visibility: visible; /* Show the snackbar */
		/* Add animation: Take 0.5 seconds to fade in and out the snackbar.
		However, delay the fade out process for 2.5 seconds */
		-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
		animation: fadein 0.5s, fadeout 0.5s 2.5s;
	}

	/* Animations to fade the snackbar in and out */
	@-webkit-keyframes fadein {
		from {bottom: 0; opacity: 0;}
		to {bottom: 30px; opacity: 1;}
	}

	@keyframes fadein {
		from {bottom: 0; opacity: 0;}
		to {bottom: 30px; opacity: 1;}
	}

	@-webkit-keyframes fadeout {
		from {bottom: 30px; opacity: 1;}
		to {bottom: 0; opacity: 0;}
	}

	@keyframes fadeout {
		from {bottom: 30px; opacity: 1;}
		to {bottom: 0; opacity: 0;}
	}
</style>

<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content" >
<!--		<a href="--><?php //echo base_url('admin/edit_off_code/').$p->id.'/'.$p->code ?><!--">-->
		<a href="<?php echo base_url('admin/insert_user') ?>">
			<button class="btn btn-success" id="new_user"
				style="outline: unset" >کاربر جدید
			</button>
		</a>
		<br>
		<br>
		<br>
		<table id="usr_data" class="table table-bordered table-striped">
			<thead>
			<tr id="id_prof">
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
					<br>
					<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" >حذف</button>
					<br>
					<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش</button>
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

		<div id="snackbar">حذف شد</div>
		<div id="snackbar2">با موفقیت انجام شد</div>
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


	$(document).on('click', '#delete', function(){
		id=$(this).attr("id_prof");
		user_id=$(this).attr("user_id");
		var m = confirm('آیا از حذف محصول اطمینان دارید؟')
		if (m == true) {
			$.ajax({
				url:"<?php echo base_url(); ?>admin/delete_user",
				method:"POST",
				data:{'user_id':user_id,},
				success:function(data)
				{
					$('#usr_data').DataTable().ajax.reload( null, false );

					// Get the snackbar DIV
					var x = document.getElementById("snackbar");

					// Add the "show" class to DIV
					x.className = "show";

					// After 3 seconds, remove the show class from DIV
					setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);


				}
			});
		}
		else
		{
			return false;
		}
	});


	$(document).on('click', '#active', function (e) {
		var id = $(this).attr("user_id");
		var table = 'register';
		$.post('<?php echo base_url()?>admin/row_active', {
				'id': id,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#usr_data').DataTable().ajax.reload( null, false );
				}
			});
	});
	$(document).on('click', '#deactive', function (e) {
		var id = $(this).attr("user_id");
		var table = 'register';
		$.post('<?php echo base_url()?>admin/row_deactive', {
				'id': id,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#usr_data').DataTable().ajax.reload( null, false );
				}
			});
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

	$("#delete_selected").on("click", function () {
		var ids1 = '';
		var ids2 = '';
		var comma = '';
		$("input:checkbox[name='row-check']:checked").each(function() {
			ids1 = ids1 + comma + $(this).attr("user_id");
			ids2 = ids2 + comma + $(this).attr("id_prof");
			comma = ',';
		});

		if(ids1.length > 0) {
			$.ajax({
				type: "POST",
				url:"<?php echo base_url(); ?>admin/delete_users_checked",
				data: {'ids1': ids1,'ids2': ids2,},
				dataType: "html",
				cache: false,
				success: function(msg) {
					$("#msg").html(msg);
					$('#usr_data').DataTable().ajax.reload( null, false );
					// Get the snackbar DIV
					var x = document.getElementById("snackbar");

					// Add the "show" class to DIV
					x.className = "show";

					// After 3 seconds, remove the show class from DIV
					setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#msg").html("<span style='color:red;'>" + textStatus + " " + errorThrown + "</span>");
				}
			});
		} else {
			$("#msg").html('<span style="color:red;">You must select at least one row for deletion</span>');
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
		var id=$("#id_modal").val();
		var new_pass=$("#new_pass_modal").val();
		var re_new_pass=$("#re_new_pass_modal").val();

		$.post('<?php echo base_url();?>admin/reset_pass',
			{'id':id,'new_pass':new_pass , 're_new_pass':re_new_pass,},
			function (data) {
				if (data==1){
					$('#reset_pass').modal('toggle');
					$("#new_pass_modal").val('');
					$("#re_new_pass_modal").val('');
					$('#usr_data').DataTable().ajax.reload( null, false );
					// Get the snackbar DIV
					var x = document.getElementById("snackbar2");

					// Add the "show" class to DIV
					x.className = "show";

					// After 3 seconds, remove the show class from DIV
					setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
				}
				else {
					alert('خطا');
				}
			}
		);
	});


</script>
