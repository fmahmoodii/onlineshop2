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
	#loading-image{
		position: fixed;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		z-index: 9999;
		background: url("<?php echo base_url()?>assets/images/loading-gif.gif") 50% 50% no-repeat rgb(15 10 10 /59%);
	}
</style>

<?php if($this->session->userdata('id')){ ?>
	<div id="loading-image" style="display: none"></div>
	<div class="container box" id="content">
		<div>
			<a href="<?php echo base_url()?>admin/insert_factor">
				<button class="btn btn-sm ml-3 btn-success" id="new"
						style="outline: unset" >فاکتور دستی</button>
			</a>
			<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" style="outline: none" >حذف همه</button>
			<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش همه</button>
			<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
			<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
		</div>

		<br>
		<br>
		<table id="ord_data" class="table table-bordered table-striped">
			<thead>
			<tr id="id_ord">
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
				</th>
				<th >کد پیگیری</th>
				<th >نام کاربر</th>
				<th >جمع پرداختی</th>
				<th >وضعیت</th>
				<th >تاریخ ایجاد</th>
				<th >آخرین ویرایش</th>
				<th >عملیات</th>
				<th >کپی</th>
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
						<form method="post" action='<?php echo base_url('admin/edit_multi_ord') ?>' >

							<input hidden id="code" name="code" value="">

					</div>

				</div>
				<div class="modal-footer">
					<button class="btn btn-success" id="submit" style="width: 150px; ">ثبت و ذخیره</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal">لغو</button>

				</div>
				</form>
			</div>
		</div>
	</div>


<?php }?>


<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		var dataTable = $('#ord_data').DataTable({
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
				url:"<?php echo base_url() . 'admin/sales_list'; ?>",
				type:"POST"
			},
			"columnDefs":[
				{
					"targets":[0,8,9,10],
					"orderable":false,
				},
				// {
				// 	target: 5,
				// 	visible: false,
				// 	searchable: true
				// },
			],
			"rowCallback": function( row, data, index ) {
				if ( $('td:eq(4)', row).text() == "در انتظار ارسال" ) {
					$('td', row).css('background-color', 'rgba(255,0,0,0.1)');
				}
			}



		});


	});


	$(document).on('click', '#active', function (e) {
		$("#loading-image").show();
		var id = $(this).attr("code_ord");
		var col = 'order_code';
		var table = 'shopping_cart_order';
		$.post('<?php echo base_url()?>admin/row_active_by_col', {
				'val': id,
				'col': col,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#ord_data').DataTable().ajax.reload( null, false );
					$("#loading-image").hide();
				}
			});
	});
	$(document).on('click', '#deactive', function (e) {
		$("#loading-image").show();
		var id = $(this).attr("code_ord");
		var col = 'order_code';
		var table = 'shopping_cart_order';
		$.post('<?php echo base_url()?>admin/row_deactive_by_col', {
				'val': id,
				'col': col,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#ord_data').DataTable().ajax.reload( null, false );
					$("#loading-image").hide();
				}
			});
	});

	$(document).on('click', '#copy', function (e) {
		$("#loading-image").show();
		var code = $(this).attr("code_ord");

		$.post('<?php echo base_url()?>admin/copy_order', {
				'code': code,
			},
			function (data) {
				if(data){
					$('#ord_data').DataTable().ajax.reload( null, false );
				}
				else {
					alert('کپی انجام نشد..')
				}
				$("#loading-image").hide();
			});
	});

	$(document).on('click', '#delete', function(){
		$("#loading-image").show();
		var code = $(this).attr("code_ord");
		var col = 'order_code';
		var table = 'shopping_cart_order';

		var m = confirm('آیا از حذف این کد اطمینان دارید؟');
		if (m == true) {
			$.ajax({
				url: "<?php echo base_url(); ?>admin/delete_ord",
				method: "POST",
				data: {
					'val': code,
					'col': col,
					'table': table,
				},
				success: function (data) {
					$('#ord_data').DataTable().ajax.reload(null, false);
					$("#loading-image").hide();

					// Get the snackbar DIV
					var x = document.getElementById("snackbar");

					// Add the "show" class to DIV
					x.className = "show";

					// After 3 seconds, remove the show class from DIV
					setTimeout(function () {
						x.className = x.className.replace("show", "");
					}, 3000);

				}
			});
		}else {
			$("#loading-image").hide();
			return false;
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

	$(document).on('click', '#active_selected', function (e) {
		$("#loading-image").show();
		var col = 'order_code';
		var table = 'shopping_cart_order';
		var ids = '';
		var comma = '';
		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		if(ids.length > 0) {
			$.ajax({
				type: "POST",
				url:"<?php echo base_url(); ?>admin/rows_active_by_col",
				data: {
					'val': ids,
					'col': col,
					'table': table,
				},
				dataType: "html",
				cache: false,
				success: function(msg) {
					$("#msg").html(msg);
					$('#ord_data').DataTable().ajax.reload( null, false );
					$("#loading-image").hide();
					// Get the snackbar DIV
					var x = document.getElementById("snackbar3");

					// Add the "show" class to DIV
					x.className = "show";

					// After 3 seconds, remove the show class from DIV
					setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#msg").html("<span style='color:red;'>" + textStatus + " " + errorThrown + "</span>");
					$("#loading-image").hide();
				}

			});
		} else {
			alert('هیچ ردیفی انتخاب نشده');
			$("#loading-image").hide();
			$("#msg").html('<span style="color:red;">You must select at least one row for deletion</span>');
		}

	});
	$(document).on('click', '#deactive_selected', function (e) {
		$("#loading-image").show();
		var col = 'order_code';
		var table = 'shopping_cart_order';
		var ids = '';
		var comma = '';
		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		if(ids.length > 0) {
			$.ajax({
				type: "POST",
				url:"<?php echo base_url(); ?>admin/rows_deactive_by_col",
				data: {
					'val': ids,
					'table': table,
					'col': col,
				},
				dataType: "html",
				cache: false,
				success: function(msg) {
					$("#msg").html(msg);
					$('#ord_data').DataTable().ajax.reload( null, false );
					$("#loading-image").hide();
					// Get the snackbar DIV
					var x = document.getElementById("snackbar3");

					// Add the "show" class to DIV
					x.className = "show";

					// After 3 seconds, remove the show class from DIV
					setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#msg").html("<span style='color:red;'>" + textStatus + " " + errorThrown + "</span>");
					$("#loading-image").hide();
				}
			});
		} else {
			$("#loading-image").hide();
			alert('هیچ ردیفی انتخاب نشده');
		}

	});

	$("#delete_selected").on("click", function () {
		$("#loading-image").show();
		var col = 'order_code';
		var table = 'shopping_cart_order';
		var ids = '';
		var comma = '';
		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		if(ids.length > 0) {
			var m = confirm('آیا از حذف اطمینان دارید؟');
			if (m == true) {
				$.ajax({
					type: "POST",
					url: "<?php echo base_url(); ?>admin/delete_orders",
					data: {
						'val': ids,
						'col': col,
						'table': table,
					},
					dataType: "html",
					cache: false,
					success: function (data) {
						$('#ord_data').DataTable().ajax.reload(null, false);
						$("#loading-image").hide();
						// Get the snackbar DIV
						var x = document.getElementById("snackbar");

						// Add the "show" class to DIV
						x.className = "show";

						// After 3 seconds, remove the show class from DIV
						setTimeout(function () {
							x.className = x.className.replace("show", "");
						}, 3000);
					}
				});
			} else {
				return false;
				$("#loading-image").hide();
			}
		}else {
			$("#loading-image").hide();
			alert('هیچ ردیفی انتخاب نشده');
		}
	});

	$("#update_selected").on("click", function () {
		$("#loading-image").show();
		var ids = '';
		var comma = '';

		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
			$('#code').val(ids);
		});

		if(ids.length > 0) {
			$("#show_edit").modal();
			$("#loading-image").hide();

		} else {
			$("#loading-image").hide();
			alert('هیچ ردیفی انتخاب نشده');
		}
	});

	$(document).ready(function(){

	});

	$('.positive').keydown( function(e) {
		if(!((e.keyCode > 95 && e.keyCode < 106)
			|| (e.keyCode > 47 && e.keyCode < 58)
			|| e.keyCode == 8)) {
			return false;
		}
	});


</script>

