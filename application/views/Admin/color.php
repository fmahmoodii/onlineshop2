<style>/* The snackbar - position it at the bottom and in the middle of the screen */
	#snackbar {
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
	#snackbar.show {
		visibility: visible; /* Show the snackbar */
		/* Add animation: Take 0.5 seconds to fade in and out the snackbar.
		However, delay the fade out process for 2.5 seconds */
		-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
		animation: fadein 0.5s, fadeout 0.5s 2.5s;
	}

	/* Animations to fade the snackbar in and out */
	@-webkit-keyframes fadein {
		from {
			bottom: 0;
			opacity: 0;
		}
		to {
			bottom: 30px;
			opacity: 1;
		}
	}

	@keyframes fadein {
		from {
			bottom: 0;
			opacity: 0;
		}
		to {
			bottom: 30px;
			opacity: 1;
		}
	}

	@-webkit-keyframes fadeout {
		from {
			bottom: 30px;
			opacity: 1;
		}
		to {
			bottom: 0;
			opacity: 0;
		}
	}

	@keyframes fadeout {
		from {
			bottom: 30px;
			opacity: 1;
		}
		to {
			bottom: 0;
			opacity: 0;
		}
	}
</style>

<?php if ($this->session->userdata('id')) { ?>

	<div class="container box" id="content">

		<button class="btn btn-success" data-toggle="modal" data-target="#new-modal"
				style="outline: unset">جدید
		</button>
		<br>
		<br>
		<br>
		<table id="color_data" class="table table-bordered table-striped">
			<thead>
			<tr>
				<th >icon</th>
				<th >نام رنگ</th>
				<th >کد رنگ</th>
				<th >توضیحات</th>
				<th >تاریخ ایجاد</th>
				<th >آخرین ویرایش</th>
				<th >عملیات</th>
				<th >ویرایش</th>
				<th >حذف</th>
			</tr>
			</thead>
		</table>

		<div id="snackbar">حذف شد</div>
	</div>

	<div class="modal fade" id="new-modal" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header text-center">
					<h4 style="display: inline">جدید</h4>
					<button type="button" class="close" data-dismiss="modal" style="float: right">
						<i class="fa fa-close"></i>
					</button>
				</div>
				<div class="modal-body">
					<label class="required">انتخاب رنگ:</label><br>
					<input required autocomplete="off" id="code" class="form-control col-sm-3" type="color">
					<span id="error"></span><br>
					<br>
					<label class="required">نام رنگ:</label><br>
					<input required autocomplete="off" id="name" class="form-control" type="text" placeholder="نام رنگ">
				</div>
				<div class="modal-footer">
					<button id="insert" class="btn btn-success">ذخیره</button>
				</div>
			</div>

		</div>
	</div>

	<div class="modal fade" id="show_edit" role="dialog">
		<div class="modal-dialog modal-sm">
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
						<label class="required">انتخاب رنگ:</label><br>
						<input required autocomplete="off" id="code_modal" class="form-control col-sm-3" type="color">
						<span id="error2"></span><br>
						<br>
						<label class="required">نام رنگ:</label><br>
						<input required autocomplete="off" id="name_modal" type="text" class="form-control" placeholder="نام رنگ">
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="btn_edit" class="btn btn-success">ثبت
						ویرایش
					</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal">لغو</button>

				</div>
			</div>
		</div>
	</div>

<?php } ?>


<script type="text/javascript" language="javascript">
	$(document).ready(function () {
		var dataTable = $('#color_data').DataTable({
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
				url: "<?php echo base_url() . 'admin/color_list'; ?>",
				type: "POST"
			},
			"columnDefs": [
				{
					"targets": [0, 6, 7],
					"orderable": false,
				},
			],
		});


		$('#code').on('click change',function () {

			var code = $('#code').val();

			if (code != '') {
				$.ajax({
					url: "<?php echo base_url(); ?>admin/check_color",
					method: "POST",
					data: {code: code},
					success: function (data) {
						$('#error').html(data);
					}
				});
			} else {
				$('#error').html('');
			}
		});



		$('#code_modal').on('click change',function () {
			var code = $("#code_modal").val();
			var id = $("#id_modal").val();
			if (code != '') {
				$.ajax({
					url: "<?php echo base_url(); ?>admin/check_color2",
					method: "POST",
					data: {'code': code,'id':id},
					success: function (data) {
						$('#error2').html(data);
					}
				});
			} else {
				$('#error2').html('');
			}
		});

	});

	$(document).on('click', '#delete', function () {
		var id = $(this).attr("id_color");
		$.ajax({
			url: "<?php echo base_url(); ?>admin/check_color_in_tables",
			method: "POST",
			data: {id: id},
			success: function (data) {
				var m2 = confirm("آیا از حذف اطمینان دارید؟\n"+data);
				if (m2 == true) {
					$.ajax({
						url: "<?php echo base_url(); ?>admin/delete_color",
						method: "POST",
						data: {id: id},
						success: function (data) {
							if(data.includes(1)){
								$('#color_data').DataTable().ajax.reload( null, false );

								// Get the snackbar DIV
								var x = document.getElementById("snackbar");

								// Add the "show" class to DIV
								x.className = "show";

								// After 3 seconds, remove the show class from DIV
								setTimeout(function () {
									x.className = x.className.replace("show", "");
								}, 3000);
							}

						}
					});
				} else {
					return false;
				}
			}
		});

	});

	$(document).on('click', '#active', function (e) {
		var id = $(this).attr("id_color");
		$.post('<?php echo base_url()?>admin/row_active', {
				'id': id,
				'table': 'color',
			},
			function (data) {
				if (data == 1) {
					$('#color_data').DataTable().ajax.reload( null, false );
				}
			});
	});
	$(document).on('click', '#deactive', function (e) {
		var id = $(this).attr("id_color");
		$.post('<?php echo base_url()?>admin/row_deactive', {
				'id': id,
				'table': 'color',
			},
			function (data) {
				if (data == 1) {
					$('#color_data').DataTable().ajax.reload( null, false );
				}
			});
	});
	$(document).on('click', '#insert', function (e) {
		var code = $("#code").val();
		console.log(code);
		var name = $("#name").val();

		$.post('<?php echo base_url()?>admin/color_validation', {
				'code': code,
			},
			function (data) {
				if (data.includes(1)) {
					$.post('<?php echo base_url()?>admin/insert_color', {
							'code': code,
							'name': name,

						},
						function (data) {
							if (data.includes(1)) {
								$('#color_data').DataTable().ajax.reload( null, false );
								$('#code').val('');
								$('#name').val('');
								$('#error').html('');
								$('#new-modal').modal('hide');
								//window.location.href = "<?php echo base_url(); ?>admin/color";

							}
						});

				} else {
					if (data.includes(2)) {
						var co = $('#code');
						co.css({'border-color': 'red'});
						co.keyup(function () {
							co.css({'border-color': ''});
						});

					}


				}
			});

	});


	// نمونه کد اینزرت با دکمه اینتر
	// $("#pass").keypress(function(event) {
	// 	if (event.keyCode == 13) {
	// 		$("#GFG_Button").click();
	// 	}
	// });


	//------نمایش مودال---------
	$(document).on('click', '#edit', function (e) {
		var id = $(this).attr("id_color");
		var code = $("#code_" + id).text();
		var name = $("#name_" + id).text();
		$("#id_modal").val(id);
		$("#code_modal").val(code);
		$("#name_modal").val(name);
		$("#show_edit").modal();
	});
	//---------عملیات ویرایش-----------
	$(document).on('click', '#btn_edit', function (e) {
		var id = $("#id_modal").val();
		var code = $("#code_modal").val();
		var name = $("#name_modal").val();

		$.post('<?php echo base_url()?>admin/color_validation2', {
				'id': id,
				'code': code,
			},
			function (data) {
				if (data.includes(1)) {
					console.log(data);
					$.post('<?php echo base_url()?>admin/edit_co', {
							'id': id,
							'code': code,
							'name': name,
						},
						function (data) {
							if (data.includes(1)) {
								$('#color_data').DataTable().ajax.reload( null, false );
								$('#code_modal').val('');
								$('#name_modal').val('');
								$('#error2').html('');
								$('#show_edit').modal('hide');

							}
						});

				} else {
					if (data.includes(2)) {
						console.log(data);
						var co = $('#code_modal');
						co.css({'border-color': 'red'});
						co.keyup(function () {
							co.css({'border-color': ''});
						});

					}
				}

			});

	});


</script>

