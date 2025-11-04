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
		<table id="size_data" class="table table-bordered table-striped">
			<thead>
			<tr>
				<th >icon</th>
				<th >سایز</th>
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
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header text-center">
					<h4 style="display: inline">جدید</h4>
					<button type="button" class="close" data-dismiss="modal" style="float: right">
						<i class="fa fa-close"></i>
					</button>
				</div>
				<div class="modal-body">
					<label class="required">سایز:</label><br>
					<input required autocomplete="off" id="name" class="form-control" type="text" placeholder="سایز">
					<div hidden id="dp_name" class="form-control" style="width: 100%;
						 height: 50px;
						 border: 1px solid #cccccc;
						 display: block;
						 overflow-y: scroll;"></div>
					<span id="error"></span><br>
				</div>
				<div class="modal-footer">
					<button id="insert" class="btn btn-success">ذخیره</button>
				</div>
			</div>

		</div>
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
						<label class="required">سایز:</label><br>
						<input required autocomplete="off" id="name_modal" type="text" class="form-control" placeholder="سایز">
						<div hidden id="dp_name_modal" class="form-control" style="width: 100%;
						 height: 50px;
						 border: 1px solid #cccccc;
						 display: block;
						 overflow-y: scroll;"></div>
						<span id="error2"></span><br>
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

<?php } ?>


<script type="text/javascript" language="javascript">
	$(document).ready(function () {
		var dataTable = $('#size_data').DataTable({
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
				url: "<?php echo base_url() . 'admin/size_list'; ?>",
				type: "POST"
			},
			"columnDefs": [
				{
					"targets": [0, 6, 7],
					"orderable": false,
				},
			],
		});

		$('#dp_name').hide();
		window.addEventListener('click', function (e) {
			if (document.getElementById('dp_name').contains(e.target)) {
			} else {
				$('#dp_name').hide();
			}
		});

		$('#name').keyup(function () {

			function load_data(query) {
				$.ajax({
					url: "<?php echo base_url(); ?>admin/search_size",
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

			if (name != '') {
				load_data(name);
				$.ajax({
					url: "<?php echo base_url(); ?>admin/check_size",
					method: "POST",
					data: {name: name},
					success: function (data) {
						$('#error').html(data);
					}
				});
			} else {
				load_data();
				$('#error').html('');
			}
		});


		$('#dp_name_modal').hide();
		window.addEventListener('click', function (e) {
			if (document.getElementById('dp_name_modal').contains(e.target)) {
			} else {
				$('#dp_name_modal').hide();
			}
		});

		$('#name_modal').keyup(function () {

			function load_data(query) {
				$.ajax({
					url: "<?php echo base_url(); ?>admin/search_size",
					method: "POST",
					data: {query: query,},
					success: function (data) {
						if (data == 0) {
							$('#dp_name_modal').hide();
						} else {
							$('#dp_name_modal').show();
							$('#dp_name_modal').html(data);


						}

					}
				});
			}

			var name = $('#name_modal').val();

			if (name != '') {
				load_data(name);
				$.ajax({
					url: "<?php echo base_url(); ?>admin/check_size",
					method: "POST",
					data: {name: name},
					success: function (data) {
						$('#error2').html(data);
					}
				});
			} else {
				load_data();
				$('#error2').html('');
			}
		});

	});

	$(document).on('click', '#delete', function () {
		var id = $(this).attr("id_size");
		$.ajax({
			url: "<?php echo base_url(); ?>admin/check_size_in_tables",
			method: "POST",
			data: {id: id},
			success: function (data) {
				var m2 = confirm("آیا از حذف اطمینان دارید؟\n"+data);
				if (m2 == true) {
					$.ajax({
						url: "<?php echo base_url(); ?>admin/delete_size",
						method: "POST",
						data: {id: id},
						success: function (data) {
							if(data.includes(1)){
								$('#size_data').DataTable().ajax.reload( null, false );

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
		var id = $(this).attr("id_size");
		$.post('<?php echo base_url()?>admin/row_active', {
				'id': id,
				'table': 'size',
			},
			function (data) {
				if (data == 1) {
					$('#size_data').DataTable().ajax.reload( null, false );
				}
			});
	});
	$(document).on('click', '#deactive', function (e) {
		var id = $(this).attr("id_size");
		$.post('<?php echo base_url()?>admin/row_deactive', {
				'id': id,
				'table': 'size',
			},
			function (data) {
				if (data == 1) {
					$('#size_data').DataTable().ajax.reload( null, false );
				}
			});
	});
	$(document).on('click', '#insert', function (e) {
		var name = $("#name").val();

		$.post('<?php echo base_url()?>admin/size_validation', {
				'name': name,
			},
			function (data) {
				if (data.includes(1)) {
					$.post('<?php echo base_url()?>admin/insert_size', {
							'name': name,

						},
						function (data) {
							if (data.includes(1)) {
								$('#size_data').DataTable().ajax.reload( null, false );
								$('#name').val('');
								$('#error').html('');
								$('#new-modal').modal('hide');
								//window.location.href = "<?php echo base_url(); ?>admin/size";

							}
						});

				} else {
					if (data.includes(2)) {
						var na = $('#name');
						na.css({'border-color': 'red'});
						na.keyup(function () {
							na.css({'border-color': ''});
						});

					}


				}
			});

	});

	$(document).on('click', '#copy', function (e) {
		var id = $(this).attr("id_size");
		$.post('<?php echo base_url()?>admin/size_copy', {
				'id': id,
			},
			function () {
				$('#size_data').DataTable().ajax.reload( null, false );
			});

	});

	// نمونه کد اینزرت با دکمه اینتر
	// $("#pass").keypress(function(event) {
	// 	if (event.keyCode == 13) {
	// 		$("#GFG_Button").click();
	// 	}
	// });


	$(document).on('click', '.tag', function (e) {
		let id = $(this).attr("id_c");
		let x = $("#tag_" + id).text();
		console.log(x);
		$("#name").val(x);
	});


	$('.positive').keydown(function (e) {
		if (!((e.keyCode > 95 && e.keyCode < 106)
			|| (e.keyCode > 47 && e.keyCode < 58)
			|| e.keyCode == 8)) {
			return false;
		}
	});


	//------نمایش مودال---------
	$(document).on('click', '#edit', function (e) {
		var id = $(this).attr("id_size");
		var name = $("#name_" + id).text();
		$("#id_modal").val(id);
		$("#name_modal").val(name);
		$("#show_edit").modal();
	});
	//---------عملیات ویرایش-----------
	$(document).on('click', '#btn_edit', function (e) {
		var id = $("#id_modal").val();
		var name = $("#name_modal").val();

		$.post('<?php echo base_url()?>admin/size_validation', {
				'id': id,
				'name': name,
			},
			function (data) {
				if (data.includes(1)) {
					$.post('<?php echo base_url()?>admin/edit_si', {
							'id': id,
							'name': name,
						},
						function (data) {
							if (data.includes(1)) {
								//alert('ویرایش با موفقیت انجام شد')
								$('#size_data').DataTable().ajax.reload( null, false );
								$('#error2').html('');

							}
						});

				} else {
					if (data.includes(2)) {
						$('#name').css({'border-color': 'red'});
						$('#name').keyup(function () {
							$('#name').css({'border-color': ''});
						});

					}


				}
			});


		$.post('<?php echo base_url();?>admin/edit_si', {'id': id, 'name': name},
			function (data) {
				if (data == 1) {
					$("#name_" + id).text(name);
					$('#cmnt_data').DataTable().ajax.reload( null, false );
				}
			}
		);
	});


</script>

