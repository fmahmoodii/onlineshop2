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


	#classname:hover{
		overflow: visible;
		white-space: normal;
		width: auto;
		position: absolute;
		background-color:#FFF;
		color:blue ;

	}



	}
</style>

<?php if($this->session->userdata('id')){ ?>

<div class="container box" id="content" >

		<br>
		<table id="cmnt_data"  class="table table-bordered table-striped" style="width: 100%">
			<thead>
			<tr id="tr">
				<th >icon</th>
				<th > نام کالا</th>
				<th >نام کاربر</th>
				<th >نظر</th>
				<th >تاریخ ایجاد</th>
				<th >آخرین ویرایش</th>
				<th >وضعیت</th>
				<th style="width: 10%;">عملیات</th>
				<th style="width: 5%;">نمایش</th>
				<th style="width: 5%;">حذف</th>
			</tr>
			</thead>
		</table>

	<div id="snackbar">حذف شد</div>

	<div class="modal fade" id="show_edit" role="dialog">
		<div class="modal-dialog modal-md" >
			<!-- Modal content-->
			<div class="modal-content " style="background-color: #fff">
				<div class="modal-header text-center" >
					<button style="float: right;" type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
					<h4 class="modal-title">ویرایش نظرات</h4>
				</div>
				<div class="modal-body">
					<div>
						<input autocomplete="off" type="hidden" id="id_modal">
						<label for="name_modal">نام کاربر</label>
						<input autocomplete="off" id="name_modal" type="text" class="form-control"><br>
						<label for="name_modal">نظر</label>
						<textarea class="form-control" id="text_modal" name="text_modal" cols="75" rows="10" style="padding: 10px"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button style="float: right;" id="btn_edit" class="btn btn-success" data-dismiss="modal" >ثبت ویرایش</button>
					<button style="float: right;" class="btn btn-danger" data-dismiss="modal" >لغو</button>

				</div>
			</div>
		</div>
	</div>


</div>

<?php }?>


<script >
	$(document).ready(function(){

		// $('#modified').change(function(){
		// 	var year = $(this).val();
		// 	if(year != '')
		// 	{
		// 		load_monthwise_data(year, 'Month Wise Profit Data For');
		// 	}
		// });

		 table = $('#cmnt_data').DataTable({
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
			pageLength: 10,
			processing: true,
			serverSide: true,
			order: [],
			ajax: {
				url: "<?php echo base_url() . 'admin/comments_list'; ?>",
				type: "POST",
			},

			columnDefs: [
				{
					targets: [0, 8, 9 ],
					orderable: false,
					checkboxes: {
						selectRow: true
					}
				},
			],
			 "rowCallback": function( row, data, index ) {
				console.log($('td:eq(6)', row).text());
				 if ( $('td:eq(6)', row).text() == "در انتظار بررسی" ) {
					 console.log('hi');
					 $('td', row).css('background-color', 'rgba(255,0,0,0.1)');
				 }
			 }


		});
	});

// 	$(document).ready(function() {
// 		var table = $('#example').DataTable({
// 			dom: 'Bfrtip',
// 			paging: true,
// 			"ajax": {
// 				"url": "ajax/objects_root_array.txt",
// 				"dataSrc": ""
// 			},
// 			"columns": [
// 				{ "data": "name" },
// 				{ "data": "position" },
// 				{ "data": "office" },
// 				{ "data": "extn" },
// 				{ "data": "start_date" },
// 				{ "data": "salary" }
// 			],
// 			"createdRow": function(row, data, index) {
// 				if (data.office == "London") {
// 					$('td:eq(2)', row).css('background-color', 'Red');
// 				} else if (data.office == "New York") {
// 					$('td:eq(2)', row).css('background-color', 'Green');
// 				}
//
// 				if (data.position == "Accountant") {
// 					$('td:eq(1)', row).css('background-color', 'Red');
// 				} else if (data.position == "Software Engineer") {
// 					$('td:eq(1)', row).css('background-color', 'Green');
// 				}
//
// 				if (data.name == "Ashton Cox") {
// 					$('td:eq(0)', row).css('background-color', 'Red');
// 				} else if (data.name == "Caesar Vance") {
// 					$('td:eq(0)', row).css('background-color', 'Green');
// 				}
//
// 			},
// 			buttons: [{
// 				extend: 'excelHtml5',
// 				customize: function ( xlsx ) {
// 					var sheet = xlsx.xl.worksheets['sheet1.xml'];
//
// 					// Map used to map column index to Excel index
// 					var excelMap = {
// 						0: 'A',
// 						1: 'B',
// 						2: 'C',
// 						3: 'D',
// 						4: 'E',
// 						5: 'F'
// 					};
//
// 					var count = 0;
// 					var skippedHeader = false;
// 					$('row', sheet).each( function () {
// 						var row = this;
// 						if (skippedHeader) {
// //             var colour = $('tbody tr:eq('+parseInt(count)+') td:eq(2)').css('background-color');
//
// 							// Output first row
// 							if (count === 0) {
// 								console.log(this);
// 							}
//
// 							for (td=0; td<6; td++) {
//
// 								// Output cell contents for first row
// 								if (count === 0) {
// 									console.log($('c[r^="' + excelMap[td] + '"]', row).text());
// 								}
// 								var colour = $(table.cell(':eq('+count+')',td).node()).css('background-color');
//
// 								if (colour === 'rgb(255, 0, 0)' || colour === 'red') {
// 									$('c[r^="' + excelMap[td] + '"]', row).attr( 's', '20' );
// 								}
// 								else if (colour === 'rgb(0, 128, 0)' || colour === 'green') {
// 									$('c[r^="' + excelMap[td] + '"]', row).attr( 's', '50' );
// 								}
// 							}
// 							count++;
// 						}
// 						else {
// 							skippedHeader = true;
// 						}
// 					});
// 				}
// 			}]
// 		});
// 	});


	$(document).on('click', '#accept', function(e){
		id=$(this).attr("id_cmnt");

			$.post('<?php echo base_url();?>admin/accept_comment',{'id':id,},
				function (data) {
					if (data==1){
						$('#cmnt_data').DataTable().ajax.reload( null, false );

					}
				}
			);


	});
	$(document).on('click', '#reject', function(e){
		id=$(this).attr("id_cmnt");

			$.post('<?php echo base_url();?>admin/reject_comment',{'id':id,},
				function (data) {
					if (data==1){
						$('#cmnt_data').DataTable().ajax.reload( null, false );
					}
				}
			);


	});
	$(document).on('click', '#delete', function(e){
		id=$(this).attr("id_cmnt");
		var m = confirm('آیا از حذف این کامنت اطمینان دارید؟')
		if (m == true) {
			$.post('<?php echo base_url();?>admin/delete_comment',{'id':id,},
				function (data) {
					if (data==1){
						$('#cmnt_data').DataTable().ajax.reload( null, false );

						// Get the snackbar DIV
						var x = document.getElementById("snackbar");

						// Add the "show" class to DIV
						x.className = "show";

						// After 3 seconds, remove the show class from DIV
						setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
					}
				}
			);
		}
		else if (m == false){
			return
		}


	});

	//------نمایش مودال---------
	$(document).on('click', '#edit_1__', function(e){
		id=$(this).attr("id_cmnt");
		var name=$("#name_"+id).text();
		var text=$("#text_"+id).text();
		$("#id_modal").val(id);
		$("#name_modal").val(name);
		$("#text_modal").val(text);
		$("#show_edit").modal();
	});
	//---------عملیات ویرایش-----------
	$(document).on('click', '#btn_edit', function(e){
		var id=$("#id_modal").val();
		var name=$("#name_modal").val();
		var text=$("#text_modal").val();

		$.post('<?php echo base_url();?>admin/edit_cm',{'id':id,'name':name , 'text':text},
			function (data) {
				if (data==1){
					/*$("#name_"+id).text(name);
					$("#text_"+id).text(text);*/
					$('#cmnt_data').DataTable().ajax.reload( null, false );
				}
			}
		);
	});





</script>

