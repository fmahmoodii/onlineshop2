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

		<?php if(form_error('name_modal')){ ?>
			<div class="alert alert-warning" style="text-align: center;" id="test">
<!--				--><?php //echo form_error('name_modal');?>
				<p> خطا!...ویرایش انجام نشد</p>
			</div>

		<?php }?>

		<div id="loading-image" style="display: none"></div>
		<div>

			<button id="new" class="btn btn-sm ml-3 btn-success" style="outline: unset" >انبار جدید</button>
			<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" >حذف همه</button>
			<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش همه</button>
			<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
			<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
		</div>

		<br>
		<br>
		<table id="inv_data" class="table table-bordered table-striped" style="width: 100%">
			<thead>
			<tr id="id_inv">
				<!--					<th width="5%">icon</th>-->
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
				</th>
				<th> نام انبار</th>
				<th>توضیحات</th>
				<th>سوابق</th>
				<th>تاریخ ایجاد</th>
				<th>آخرین ویرایش</th>
				<th>عملیات</th>
				<th>گزارش</th>
				<th>موجودی</th>
				<th>ویرایش</th>
				<th>حذف</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar">حذف شد</div>
		<div id="snackbar2">انجام شد</div>


	</div>

	<div class="modal fade" id="new-modal" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header text-center">
					<h4 style="display: inline">جدید</h4>
					<button type="button" class="close" data-dismiss="modal" style="float: right">
						<i class="fa fa-close"></i>
					</button>
				</div>
				<div class="modal-body">
					<form id="xxx" method="post" action='<?php echo base_url('admin/insert_inv') ?>' onsubmit="return validateForm()" >

						<div class="form-group">
							<label for="name" class="required">نام انبار:</label><br>
							<input required autocomplete="off" id="name" name="name"
								   class="form-control" type="text"
								   placeholder="نام انبار">
							<div hidden id="dp_name" class="form-control" style="width: 100%;
						 height: 50px;
						 border: 1px solid #cccccc;
						 display: block;
						 overflow-y: scroll;"></div>
							<span id="email_result"></span>
							<?php echo form_error('name','<span id="vvv" style="color: red">','</span>') ?>
						</div>
						<div class="form-group">
							<label for="details">توضیحات:</label><br>
							<input autocomplete="off" id="details" name="details"
								   class="form-control" type="text"
								   placeholder="توضیحات">
						</div>
						<button id="submit" class="btn btn-success">ذخیره</button>
					</form>
				</div>
<!--				<div class="modal-footer">-->
<!--					<button id="submit" class="btn btn-success">ذخیره</button>-->
<!--				</div>-->
			</div>

		</div>
	</div>

	<div class="modal fade" id="edit-modal" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header text-center">
					<h4 style="display: inline">ویرایش</h4>
					<button type="button" class="close" data-dismiss="modal" style="float: right">
						<i class="fa fa-close"></i>
					</button>
				</div>
				<div class="modal-body">
					<form method="post" action='<?php echo base_url('admin/edit_inv') ?>' onsubmit="return validateForm()" >
						<input autocomplete="off" type="hidden" id="id_modal" name="id_modal">
						<div class="form-group">
							<label for="name" class="required">نام انبار:</label><br>
							<input required autocomplete="off" id="name_modal" name="name_modal"
								   class="form-control" type="text"
								   value=""
								   placeholder="نام انبار">
							<div hidden id="dp_name2" class="form-control" style="width: 100%;
						 height: 50px;
						 border: 1px solid #cccccc;
						 display: block;
						 overflow-y: scroll;"></div>
							<span id="email_result2"></span>
<!--							--><?php //echo form_error('name_modal','<span id="vvv2" style="color: red">','</span>') ?>
						</div>
						<div class="form-group">
							<label for="details">توضیحات:</label><br>
							<input autocomplete="off" id="details_modal" name="details_modal"
								   class="form-control" type="text"
								   value=""
								   placeholder="توضیحات">
						</div>
						<button id="submit" class="btn btn-success">ذخیره</button>
					</form>
				</div>
				<!--				<div class="modal-footer">-->
				<!--					<button id="submit" class="btn btn-success">ذخیره</button>-->
				<!--				</div>-->
			</div>

		</div>
	</div>


<?php }?>


<script type="text/javascript" language="javascript" >



	$(document).ready(function(){

		var dataTable = $('#inv_data').DataTable({
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
				url:"<?php echo base_url() . 'admin/inventory_list'; ?>",
				type:"POST"
			},
			"columnDefs":[
				{
					"targets":[0, 7, 8],
					"orderable":false,
				},
			],
		});




	});

	$(document).on('click', '#active', function (e) {
		$("#loading-image").show();
		var id = $(this).attr("id_inv");
		var table = 'inventory';
		$.post('<?php echo base_url()?>admin/row_active', {
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
	$(document).on('click', '#deactive', function (e) {
		$("#loading-image").show();
		var id = $(this).attr("id_inv");
		var table = 'inventory';
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
		var table = 'inventory';
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
		var table = 'inventory';
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

		} else {
			$("#loading-image").hide();
			$("#msg").html('<span style="color:red;">You must select at least one product for deletion</span>');
		}
	});


	$(document).on('click', '.tag', function(e) {
		let id = $(this).attr("id_c");
		let x=$("#tag_"+id).text();
		console.log(x);
		$("#name").val(x);

		var name = $('#name').val();

		if(name != '')
		{
			$.ajax({
				url:"<?php echo base_url(); ?>admin/check_name",
				method:"POST",
				data:{name:name,},
				success:function(data){
					$('#email_result').html(data);
				}
			});
		}else{
			load_data();
			$('#email_result').html('');
		}

	});


	$(document).on('click', '.tag2', function(e) {
		let id = $(this).attr("id_c2");
		let x=$("#tag2_"+id).text();
		console.log(x);
		$("#name_modal").val(x);

		var name = $('#name_modal').val();
		var id2 = $('#id_modal').val();

		if(name != '')
		{
			$.ajax({
				url:"<?php echo base_url(); ?>admin/check_name2",
				method:"POST",
				data:{name:name,id:id2,},
				success:function(data){
					$('#email_result2').html(data);
				}
			});
		}else{
			load_data();
			$('#email_result2').html('');
		}

	});




	$(document).ready(function(){

		<?php if(form_error('name_modal')){ ?>
		// alert('خطای ویرایش رکورد');
		//alert('<?php //echo form_error('name_modal'); ?>//');
		<?php form_error('name_modal','<p>hi','</p>'); } ?>



		$('#dp_name').hide();
		window.addEventListener('click', function(e){
			if (document.getElementById('dp_name').contains(e.target)){
			} else{
				$('#dp_name').hide();
			}
		});

		$('#email_result');

		$('#name').keyup(function() {
			console.log('hi');
			$("#vvv").html('');
			if ($("#name_error").is(':visible')){
				$("#name_error").hide();}
			function load_data(query) {
				$.ajax({
					url: "<?php echo base_url(); ?>admin/search_name",
					method: "POST",
					data: {query: query,},
					success: function (data) {
						if (data == 0) {
							$('#dp_name').hide();
						} else {
							$('#dp_name').show();
							$('#dp_name').html(data);


						}

					}
				});
			}

			var name = $('#name').val();


			if(name != '')
			{
				load_data(name);
				$.ajax({
					url:"<?php echo base_url(); ?>admin/check_name",
					method:"POST",
					data:{name:name},
					success:function(data){
						$('#email_result').html(data);
					}
				});
			}else{
				load_data();
				$('#email_result').html('');
			}

		});






		$('#dp_name2').hide();
		window.addEventListener('click', function(e){
			if (document.getElementById('dp_name2').contains(e.target)){
			} else{
				$('#dp_name2').hide();
			}
		});

		$('#email_result2');

		$('#name_modal').keyup(function() {
			console.log('hi');
			$("#vvv2").html('');
			if ($("#name_error2").is(':visible')){
				$("#name_error2").hide();}
			function load_data(query) {
				$.ajax({
					url: "<?php echo base_url(); ?>admin/search_name2",
					method: "POST",
					data: {query: query,},
					success: function (data) {
						if (data == 0) {
							$('#dp_name2').hide();
						} else {
							$('#dp_name2').show();
							$('#dp_name2').html(data);


						}

					}
				});
			}

			var name = $('#name_modal').val();
			var id = $('#id_modal').val();

			if(name != '')
			{
				load_data(name);
				$.ajax({
					url:"<?php echo base_url(); ?>admin/check_name2",
					method:"POST",
					data:{name:name,id:id,},
					success:function(data){
						$('#email_result2').html(data);
					}
				});
			}else{
				load_data();
				$('#email_result2').html('');
			}

		});




	});


	//------نمایش مودال---------
	$('#new').on("click",function (){
		$("#new-modal").modal();
	});


	//------نمایش مودال---------
	$(document).on('click', '#edit', function (e) {
		var id = $(this).attr("id_inv");
		var name = $("#name_" + id).text();
		var details = $("#details_" + id).text();
		$("#id_modal").val(id);
		$("#name_modal").val(name);
		$("#details_modal").val(details);
		$("#edit-modal").modal();
	});


</script>
