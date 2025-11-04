<style>/* The snackbar - position it at the bottom and in the middle of the screen */
	#snackbar ,#snackbar2 ,#snackbar3 {
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
	#snackbar.show ,#snackbar2.show ,#snackbar3.show {
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

	<div class="container box" id="content">
		<div>
			<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" >حذف همه</button>
			<!--<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش</button>-->
			<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
			<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
		</div>
		<br>
		<br>
		<table id="category2_data" class="table table-bordered table-striped">
			<thead>
			<tr>
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
				</th>
				<th >سطح دوم</th>
				<th >وضعیت</th>
				<th >تاریخ ایجاد</th>
				<th >آخرین ویرایش</th>
				<th >عملیات</th>
				<th >ویرایش</th>
				<th >حذف</th>
			</tr>
			</thead>
		</table>
		<div id="snackbar">حذف شد</div>
		<div id="snackbar2">ثبت شد</div>
		<div id="snackbar3">انجام شد</div>

	</div>

	<div class="modal fade" id="show_edit" role="dialog">
		<div class="modal-dialog modal-md">
			<!-- Modal content-->
			<div class="modal-content " style="background-color: #fff">
				<div class="modal-header text-center">
					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i
								class="fa fa-close"></i></button>
					<h4 class="modal-title">ویرایش</h4>
				</div>
				<div class="modal-body">
					<div>
						<input autocomplete="off" type="hidden" id="id_modal">
						<label class="required">نام دسته:</label><br>
						<input required autocomplete="off" id="name_modal" type="text" class="form-control" placeholder="نام دسته">
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="btn_edit" class="btn btn-success" data-dismiss="modal">ثبت
						ویرایش
					</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal">لغو</button>

				</div>
			</div>
		</div>
	</div>

	<!--	<div class="modal fade" id="show_edit2" role="dialog">-->
	<!--		<div class="modal-dialog modal-md">-->
	<!--			 Modal content-->
	<!--			<div class="modal-content " style="background-color: #fff">-->
	<!--				<div class="modal-header text-center">-->
	<!--					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i-->
	<!--								class="fa fa-close"></i></button>-->
	<!--					<h4 class="modal-title">ویرایش</h4>-->
	<!--				</div>-->
	<!--				<div class="modal-body">-->
	<!--					<div>-->
	<!--						<input hidden id="id2" name="id2" value="">-->
	<!--						<label class="required">نام دسته:</label><br>-->
	<!--						<input required autocomplete="off" id="name_modal2" type="text" class="form-control" placeholder="نام دسته">-->
	<!--					</div>-->
	<!--				</div>-->
	<!--				<div class="modal-footer">-->
	<!--					<button style="float: right;" id="btn_edit2" class="btn btn-success" data-dismiss="modal">ثبت-->
	<!--						ویرایش-->
	<!--					</button>-->
	<!--					<button style="float: right;" class="btn btn-danger" data-dismiss="modal">لغو</button>-->
	<!---->
	<!--				</div>-->
	<!--			</div>-->
	<!--		</div>-->
	<!--	</div>-->
<?php } ?>



<script>

	$(document).ready(function () {
		var dataTable = $('#category2_data').DataTable({
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
			"processing": true,
			"serverSide": true,
			"order": [],
			"ajax": {
				url: "<?php echo base_url() . 'admin/category2_list'; ?>",
				type: "POST"
			},
			"columnDefs": [
				{
					"targets": [0, 6, 7],
					"orderable": false,
				},
			],
		});
	});


	$(document).on('click', '#delete', function(e){
		var id = $(this).attr("id_cat2");

		var m = confirm('آیا از حذف دسته و زیر دسته ها مطمئن هستید؟')

		if (m == true) {
			$.ajax({
				url:"<?php echo base_url(); ?>admin/delete_cat2",
				method:"POST",
				data:{id:id},
				success:function(data)
				{
					$('#category2_data').DataTable().ajax.reload( null, false );

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

	//------نمایش مودال---------
	$(document).on('click', '#edit', function (e) {
		var id = $(this).attr("id_cat2");
		var name = $("#name_" + id).text();
		$("#id_modal").val(id);
		$("#name_modal").val(name);
		$("#show_edit").modal();
	});
	//---------عملیات ویرایش-----------
	$(document).on('click', '#btn_edit', function (e) {
		var id = $("#id_modal").val();
		var name = $("#name_modal").val();


		$.post('<?php echo base_url()?>admin/edit_cat2', {
				'id': id,
				'name_cat2': name,
			},
			function (data) {
				if (data == 1) {
					//alert('ویرایش با موفقیت انجام شد')
					// $('#category2_data').DataTable().ajax.reload( null, false );

					//reload in current page (for edit and delete and activation)
					$('#category2_data').DataTable().ajax.reload( null, false );
				}
			});
	});

	$(document).on('click', '#active', function (e) {
		var id = $(this).attr("id_cat2");
		var table = 'category2';
		$.post('<?php echo base_url()?>admin/row_active', {
				'id': id,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#category2_data').DataTable().ajax.reload( null, false );
				}
			});
	});
	$(document).on('click', '#deactive', function (e) {
		var id = $(this).attr("id_cat2");
		var table = 'category2';
		$.post('<?php echo base_url()?>admin/row_deactive', {
				'id': id,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#category2_data').DataTable().ajax.reload( null, false );
				}
			});
	});

	$(document).on('click', '#active_selected', function (e) {
		var table = 'category2';
		var ids = '';
		var comma = '';
		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		if(ids.length > 0) {
			$.ajax({
				type: "POST",
				url:"<?php echo base_url(); ?>admin/rows_active",
				data: {
					'id': ids,
					'table': table,
				},
				dataType: "html",
				cache: false,
				success: function(msg) {
					$("#msg").html(msg);
					$('#category2_data').DataTable().ajax.reload( null, false );
					// Get the snackbar DIV
					var x = document.getElementById("snackbar3");

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
	$(document).on('click', '#deactive_selected', function (e) {

		var table = 'category2';
		var ids = '';
		var comma = '';
		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		if(ids.length > 0) {
			$.ajax({
				type: "POST",
				url:"<?php echo base_url(); ?>admin/rows_deactive",
				data: {
					'id': ids,
					'table': table,
				},
				dataType: "html",
				cache: false,
				success: function(msg) {
					$("#msg").html(msg);
					$('#category2_data').DataTable().ajax.reload( null, false );
					// Get the snackbar DIV
					var x = document.getElementById("snackbar3");

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
		var ids = '';
		var comma = '';
		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		if(ids.length > 0) {
			$.ajax({
				type: "POST",
				url:"<?php echo base_url(); ?>admin/delete_cats2_checked",
				data: {'ids': ids,},
				dataType: "html",
				cache: false,
				success: function(msg) {
					$("#msg").html(msg);
					$('#category2_data').DataTable().ajax.reload( null, false );
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


	//------نمایش مودال2---------
	$(document).on('click', '#update_selected', function (e) {

		var ids = '';
		var comma = '';

		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		$('#id2').val(ids);
		$("#show_edit2").modal();

	});
	//---------عملیات ویرایش2-----------
	$("#btn_edit2").on("click", function () {
		var ids = $('#id2').val();
		var name = $("#name_modal2").val();

		if(ids.length > 0) {

			$.ajax({
				url:"<?php echo base_url(); ?>admin/edit_cats2_checked",
				method:"POST",
				data:{name:name, id:ids},
				success:function(data)
				{
					$('#category2_data').DataTable().ajax.reload( null, false );

					// Get the snackbar DIV
					var x = document.getElementById("snackbar2");

					// Add the "show" class to DIV
					x.className = "show";

					// After 3 seconds, remove the show class from DIV
					setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

				}
			});


		} else {
			$("#msg").html('<span style="color:red;">You must select at least one row for deletion</span>');
		}
	});


</script>
