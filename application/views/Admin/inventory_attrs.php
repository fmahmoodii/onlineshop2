<style>/* The snackbar - position it at the bottom and in the middle of the screen */
	#snackbar ,#snackbar2 {
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
	#snackbar.show ,#snackbar2.show {
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

	<div class="container box" id="content" style="padding: 0 0 100px 0">
		<div id="loading-image" style="display: none"></div>
		<input id="xxx" type="hidden" value="<?php echo $id;?>">
		<div>

			<a href="<?php echo base_url()?>admin/insert_inventory_attrs">
				<button class="btn btn-sm ml-3 btn-success" id="new"
						style="outline: unset" >حواله انبار به انبار</button>
			</a>
			<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" >حذف همه</button>
			<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش همه</button>
			<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
			<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
		</div>

		<br>
		<br>
		<table id="inv_attr_data" class="table table-bordered table-striped" style="width: 100%">
			<thead>
			<tr id="id_inv">
				<!--					<th width="5%">icon</th>-->
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
				</th>
				<th width="5%">کد کالا</th>
				<th>نام کالا</th>
<!--				<th>قیمت خرید</th>-->
<!--				<th>قیمت فروش</th>-->
				<th>توضیحات</th>
				<th>تعداد</th>
				<th>واحد</th>
				<th>ورود/خروج</th>
				<th>مبداء</th>
				<th>مقصد</th>
				<th>تاریخ</th>
				<th>تاریخ ایجاد</th>
				<th>آخرین ویرایش</th>
				<th>عملیات</th>
				<th>ویرایش</th>
				<th>حذف</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar">حذف شد</div>
		<div id="snackbar2">انجام شد</div>

	</div>

<?php }?>


<script type="text/javascript" language="javascript" >

	$(document).ready(function(){
		var dataTable = $('#inv_attr_data').DataTable({
			language: {
				lengthMenu: "نمایش _MENU_ رکورد هر صفحه",
				zeroRecords: "متاسفانه موردی یافت نشد",
				info: "صفحه _PAGE_ از _PAGES_",
				infoEmpty: "موردی یافت نشد",
				infoFiltered: "(فیلتر _TOTAL_ رکورد)",
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
				url:'<?php echo base_url();?>admin/inventory_attrs_list/'+$('#xxx').val(),
				type:"POST"
			},
			"columnDefs":[
				{
					"targets":[0, 13, 14],
					"orderable":false,
				},
			],
		});




	});

	$(document).on('click', '#active', function (e) {
		$("#loading-image").show();
		var id = $(this).attr("id_row");
		var table = 'inventory_attrs';
		$.post('<?php echo base_url()?>admin/row_active', {
				'id': id,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#inv_data').DataTable().ajax.reload( null, false);
					$("#loading-image").hide();
				}
			});
	});
	$(document).on('click', '#deactive', function (e) {
		$("#loading-image").show();
		var id = $(this).attr("id_row");
		var table = 'inventory_attrs';
		$.post('<?php echo base_url()?>admin/row_deactive', {
				'id': id,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#inv_data').DataTable().ajax.reload( null, false );
					$("#loading-image").hide();
				}
			});
	});

	$(document).on('click', '#active_selected', function (e) {
		$("#loading-image").show();
		var table = 'inventory_attrs';
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
					$("#loading-image").hide();
					$("#msg").html(msg);
					$('#inv_data').DataTable().ajax.reload( null, false );
					// Get the snackbar DIV
					var x = document.getElementById("snackbar2");

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
			$("#loading-image").hide();
			$("#msg").html('<span style="color:red;">You must select at least one row for deletion</span>');
		}

	});
	$(document).on('click', '#deactive_selected', function (e) {
		$("#loading-image").show();
		var table = 'inventory_attrs';
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
					$("#loading-image").hide();
					$("#msg").html(msg);
					$('#inv_data').DataTable().ajax.reload( null, false );
					// Get the snackbar DIV
					var x = document.getElementById("snackbar2");

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
			$("#loading-image").hide();
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


	$("#update_selected").on("click", function () {
		$("#loading-image").show();
		var ids = '';
		var comma = '';

		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		if(ids.length > 0) {
			$("#loading-image").hide();
			$("#add_supply").modal();

		} else {
			$("#loading-image").hide();
			$("#msg").html('<span style="color:red;">You must select at least one product for deletion</span>');
		}
	});

	//---------عملیات ویرایش-----------
	$(document).on('click', '#btn_edit', function (e) {
		$("#loading-image").show();

		var id_cat1=$("#cat1_show").val();
		var id_cat2=$("#cat2_show").val();
		var name=$("#name").val();
		var text=CKEDITOR.instances['text1'].getData();

		var ids = '';
		var comma = '';



		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		if(ids.length > 0) {

			// var m = confirm('آیا از ویرایش محصول اطمینان دارید؟');
			// if (m == true) {

			$.post('<?php echo base_url()?>admin/update_product_attributes_checkall', {
					'id': ids, 'id_cat1': id_cat1, 'id_cat2': id_cat2,
					'name': name, 'text': text
				},
				function (data) {
					if (data.includes(1)) {
						$("#loading-image").hide();
						$("#cat1_show").val('');
						$("#cat2_show").val('');
						$("#name").val('');
						CKEDITOR.instances['text1'].setData('');
						alert('ویرایش محصول انجام شد');
						$('#inv_data').DataTable().ajax.reload( null, false );
					}
					$("#loading-image").hide();
				});

			// }
			// if (m == false) {
			// 	return
			// }

		}

	});






</script>
