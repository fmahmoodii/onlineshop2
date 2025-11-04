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
			<a href="<?php echo base_url()?>admin/off_code_form">
				<button class="btn btn-sm ml-3 btn-success" id="new"
						style="outline: unset" >کد تخفیف جدید</button>
			</a>
			<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" style="outline: none" >حذف همه</button>
			<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش همه</button>
			<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
			<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
		</div>

		<br>
		<br>
		<table id="off_data" class="table table-bordered table-striped">
			<thead>
			<tr id="id_off">
				<th>
					<label for="checkbox">همه</label>
					<input type="checkbox" id='check_all'>
				</th>
				<th >کد تخفیف</th>
				<th >مبلغ تخفیف</th>
				<th >تاریخ شروع</th>
				<th >تاریخ پایان</th>
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
						<form method="post" action='<?php echo base_url('admin/edit_multi_off') ?>' >

							<input hidden id="id" name="id" value="">

							<div class="form-group">
								<label for="mablagh" class="">مبلغ تخفیف:</label><br>
								<input autocomplete="off" id="mablagh" name="mablagh" class="form-control positive" type="number" min="0" placeholder="مبلغ تخفیف">
								<span id="email_result2"></span><br>
								<?php echo form_error('mablagh','<span style="color: red">','</span>') ?>

							</div>

							<div class="form-group start_date" style="display: inline-block; width: 270px">
								<label for="start_date" class="">تاریخ شروع:</label><br>
								<input readonly id="start_date" name="start_date" class="form-control" placeholder="تاریخ شروع"><br>
								<input hidden id="datetime1" name="datetime1">
								<?php echo form_error('start_date','<span style="color: red">','</span>') ?>
							</div>

							<div class="form-group end_date" style="display: inline-block; width: 270px">
								<label for="end_date" class="">تاریخ پایان:</label><br>
								<input readonly id="end_date" name="end_date" class="form-control" placeholder="تاریخ پایان"><br>
								<input hidden id="datetime2" name="datetime2">
								<?php echo form_error('end_date','<span style="color: red">','</span>') ?>
							</div>
							<br>
							<br>


							<div class="form-group">
								<label for="min_price" class="">حداقل خرید:</label><br>
								<input autocomplete="off" id="min_price" name="min_price" class="form-control positive" type="number" min="0" placeholder="حداقل خرید">

								<?php echo form_error('min_price','<span style="color: red">','</span>') ?>

							</div>

							<div class="form-group">
								<label for="products" class="">محصولات:</label>
								<select multiple class="form-control products" id="products" name="products[]" >

									<!--<option selected value="">--><?php //echo "انتخاب کنید.."; ?><!--</option>-->

									<?php foreach ($products as $p){ ?>
										<option value="<?php echo $p->id?>"><?php echo $p->name?></option>
									<?php }?>
								</select>
								<?php echo form_error('products','<span style="color: red">','</span>') ?>
							</div>

							<button type="button" id="selectAll_p" class="btn btn-default">انتخاب همه</button>
							<button type="button" id="deselectAll_p" class="btn btn-default">حذف همه</button>
							<br>
							<br>



							<div class="form-group">
								<label for="users" class="">کاربران:</label>
								<select multiple class="form-control users" id="users" name="users[]" >

									<!--<option selected value="">--><?php //echo "انتخاب کنید.."; ?><!--</option>-->

									<?php foreach ($register as $reg){ foreach ($profile as $prof){if($reg->id == $prof->user_id){ ?>
										<option value="<?php echo $reg->id?>"><?php $x=$prof->name.' '.$prof->family; echo $x;?></option>
									<?php }}}?>
								</select>
								<?php echo form_error('users','<span style="color: red">','</span>') ?>
							</div>

							<button type="button" id="selectAll_u" class="btn btn-default">انتخاب همه</button>
							<button type="button" id="deselectAll_u" class="btn btn-default">حذف همه</button>
							<br>
							<br>


							<div class="form-group">
								<label for="uses" class="">تعداد دفعات استفاده:</label><br>
								<input autocomplete="off" id="uses" name="uses" class="form-control positive" type="number" min="1" placeholder="تعداد دفعات استفاده">

								<?php echo form_error('uses','<span style="color: red">','</span>') ?>

							</div>







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
		var dataTable = $('#off_data').DataTable({
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
				url:"<?php echo base_url() . 'admin/off_code_list'; ?>",
				type:"POST"
			},
			"columnDefs":[
				{
					"targets":[0,9,10,11],
					"orderable":false,
				},
				// {
				// 	target: 5,
				// 	visible: false,
				// 	searchable: true
				// },
			],
			"rowCallback": function( row, data, index ) {
				console.log($('td:eq(5)', row).text());
				if ( $('td:eq(5)', row).text() == "منقضی شده" ) {
					console.log('hi');
					$('td', row).css('background-color', 'rgba(255,0,0,0.1)');
				}
			}



		});


	});

	// $('#mablagh').keyup(function(){
	// 	var mablagh = $('#mablagh').val();
	// 	console.log(mablagh);
	// 	$('#min_price').val(mablagh);
	// 	$('#min_price').attr({"min":mablagh});
	//
	// });


	$(document).on('click', '#active', function (e) {
		var id = $(this).attr("id_off");
		var table = 'off_code';
		$.post('<?php echo base_url()?>admin/row_active', {
				'id': id,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#off_data').DataTable().ajax.reload( null, false );
				}
			});
	});
	$(document).on('click', '#deactive', function (e) {
		var id = $(this).attr("id_off");
		var table = 'off_code';
		$.post('<?php echo base_url()?>admin/row_deactive', {
				'id': id,
				'table': table,
			},
			function (data) {
				if (data == 1) {
					$('#off_data').DataTable().ajax.reload( null, false );
				}
			});
	});

	$(document).on('click', '#copy', function (e) {
		var id = $(this).attr("id_off");

		$.post('<?php echo base_url()?>admin/copy_off_code', {
				'id': id,
			},
			function (data) {
				if(data==1){
					$('#off_data').DataTable().ajax.reload( null, false );}
				else {
					alert('کپی انجام نشد..')
				}
			});
	});

	$(document).on('click', '#delete', function(){
		var id = $(this).attr("id_off");
		var column = 'off_code';
		var table = 'shopping_cart_order';
			$.ajax({
				url:"<?php echo base_url(); ?>admin/check_orders",
				method:"POST",
				data:{
					id:id,
					column:column,
					table:table,
				},
				success:function(data)
				{
					if(data==0) {
						var m = confirm('آیا از حذف این کد اطمینان دارید؟');
						$("#loading-image").show();
						if (m == true) {
							$.ajax({
								url: "<?php echo base_url(); ?>admin/delete_off",
								method: "POST",
								data: {id: id},
								success: function (data) {
									$('#off_data').DataTable().ajax.reload(null, false);

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
						}else
						{
							$("#loading-image").hide();
							return false;
						}
					}
					else if(data == 1){
						$("#loading-image").hide();
						alert('امکان حذف این کد تخفیف وجود ندارد')
					}


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

	$(document).on('click', '#active_selected', function (e) {
		var table = 'off_code';
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
					$('#off_data').DataTable().ajax.reload( null, false );
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
			alert('هیچ ردیفی انتخاب نشده');
			$("#msg").html('<span style="color:red;">You must select at least one row for deletion</span>');
		}

	});
	$(document).on('click', '#deactive_selected', function (e) {

		var table = 'off_code';
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
					$('#off_data').DataTable().ajax.reload( null, false );
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
			alert('هیچ ردیفی انتخاب نشده');
			$("#msg").html('<span style="color:red;">You must select at least one row for deletion</span>');
		}

	});



	$("#delete_selected").on("click", function () {
		var ids = '';
		var comma = '';
		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
		});

		$.ajax({
			type: "POST",
			url:"<?php echo base_url(); ?>admin/check_orders_selected",
			data: {'ids': ids,},
			dataType: "html",
			cache: false,
			success: function(data1) {
				if(ids.length > 0) {
					var obj = JSON.parse(data1);
					var i = obj.ord;
					if (obj.ord == 0) {
						var m = confirm('آیا از حذف اطمینان دارید؟');
						if (m == true) {
							$.ajax({
								type: "POST",
								url: "<?php echo base_url(); ?>admin/delete_off_codes",
								data: {'ids': ids,},
								dataType: "html",
								cache: false,
								success: function (data) {
									$('#off_data').DataTable().ajax.reload(null, false);
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
						}else
						{
							$("#loading-image").hide();
							return false;
						}
					}else if (obj.ord > 0) {
						$("#loading-image").hide();
						var alertContent = "امکان حذف کدهای زیر وجود ندارد؟";
						for(let j=1;j<=i;j++){ alertContent += '\n'+obj.off[j]}
						alert(alertContent);
					}
			}else {
					alert('هیچ ردیفی انتخاب نشده');
				}
			}
		});

	});

	$("#update_selected").on("click", function () {
		var ids = '';
		var comma = '';



		$("input:checkbox[name='row-check']:checked").each(function() {
			ids = ids + comma + this.value;
			comma = ',';
			$('#id').val(ids);
		});

		if(ids.length > 0) {
			$("#show_edit").modal();

		} else {
			alert('هیچ ردیفی انتخاب نشده');
			$("#msg").html('<span style="color:red;">You must select at least one row for deletion</span>');
		}
	});



	var to, from;
	to = $(".end_date").persianDatepicker({
		inline: true,
		altField: '#end_date',
		// altFormat: 'YYYY-MM-DD HH:mm:ss',
		altFormat: 'LLLL',
		initialValue: false,
		timePicker: {
			enabled: true,
			meridiem: {
				enabled: true
			}
		},
		navigator:{
			scroll:{
				enabled: false
			},
			text: {

				btnNextText:'بعدی',

				btnPrevText:'قبلی',
			}
		},


		onSelect: function (unix) {

			// const state = to.getState();
			// var dt = new Date(state.view.unixDate);
			// var datetime2 = dt.toLocaleString('fa-IR', {numberingSystem: 'latn'}).replace(',','').replaceAll('/', '-') ;
			// var x = datetime2.toString();
			// console.log(x);
			// console.log(typeof x);
			// $('#datetime2').val(x);

			const state = to.getState();
			var dt = new Date(state.view.unixDate);
			let month = dt.getUTCMonth() + 1; //months from 1-12
			let day = dt.getUTCDate();
			let year = dt.getUTCFullYear();
			let hour = dt.getHours();
			let minute = dt.getMinutes();
			let second = dt.getSeconds();

			newdate = year + "/" + month + "/" + day + " " + hour + ":" + minute + ":" +second;
			// console.log(newdate);
			// console.log(typeof newdate)
			$('#datetime2').val(newdate);



			to.touched = true;
			if (from && from.options && from.options.maxDate != unix) {
				var cachedValue = from.getState().selected.unixDate;
				from.options = {maxDate: unix};
				if (from.touched) {
					from.setDate(cachedValue);
				}
			}
		}
	});
	from = $(".start_date").persianDatepicker({
		inline: true,
		altField: '#start_date',
		altFormat: 'LLLL',
		initialValue: false,
		timePicker: {
			enabled: true,
			meridiem: {
				enabled: true
			}
		},
		navigator:{
			scroll:{
				enabled: false
			},
			text: {

				btnNextText:'بعدی',

				btnPrevText:'قبلی',
			}
		},
		onSelect: function (unix) {

			// const state = from.getState();
			// var dt = new Date(state.view.unixDate);
			// var datetime1 = dt.toLocaleString('fa-IR', {numberingSystem: 'latn'}).replace(',','').replaceAll('/', '-') ;
			// var x = datetime1.toString();
			// console.log(x);
			// console.log(typeof x);
			// $('#datetime1').val(x);

			const state = from.getState();
			var dt = new Date(state.view.unixDate);
			let month = dt.getUTCMonth() + 1; //months from 1-12
			let day = dt.getUTCDate();
			let year = dt.getUTCFullYear();
			let hour = dt.getHours();
			let minute = dt.getMinutes();
			let second = dt.getSeconds();

			newdate = year + "/" + month + "/" + day + " " + hour + ":" + minute + ":" +second;
			// console.log(newdate);
			// console.log(typeof newdate)
			$('#datetime1').val(newdate);


			from.touched = true;
			if (to && to.options && to.options.minDate != unix) {
				var cachedValue = to.getState().selected.unixDate;
				to.options = {minDate: unix};
				if (to.touched) {
					to.setDate(cachedValue);
				}
			}
		}
	});

	$(document).on('click', '.tag', function(e) {
		let id = $(this).attr("id_c");
		let x=$("#tag_"+id).text();
		console.log(x);
		$("#code").val(x);
	});


	$('#selectAll_p').click(function() {
		$(".products > option").prop("selected", true);
		$(".products").trigger("change");

	});
	$('#deselectAll_p').click(function() {
		$(".products").val(null).trigger("change");
	});

	$('#selectAll_u').click(function() {
		$(".users > option").prop("selected", true);
		$(".users").trigger("change");
	});
	$('#deselectAll_u').click(function() {
		$(".users").val(null).trigger("change");
	});


	$(document).ready(function(){


		$('.products,.users').select2({
			placeholder: 'انتخاب کنید...',
			// allowClear: true,
			language: {
				noResults: function () {
					return "موردی یافت نشد";
				}
			},
			closeOnSelect: false,
		});



		//$('#email_result2')
		//$('#mablagh').keyup(function(){
		//	var mablagh = $('#mablagh').val();
		//	if(mablagh != '')
		//	{
		//		$.ajax({
		//			url:"<?php //echo base_url(); ?>//admin/check_mablagh",
		//			method:"POST",
		//			data:{mablagh:mablagh},
		//			success:function(data){
		//				$('#email_result2').html(data);
		//			}
		//		});
		//	}else{
		//		$('#email_result2').html('');
		//	}
		//});

	});


	$('.positive').keydown( function(e) {
		if(!((e.keyCode > 95 && e.keyCode < 106)
			|| (e.keyCode > 47 && e.keyCode < 58)
			|| e.keyCode == 8)) {
			return false;
		}
	});



	$('#code').on("input",function() {

		//چک کردن حروف فارسی. جلوگیری از اسپیس اول. جلوگیری از اسپیس های چنتایی
		this.value = this.value.replace(/\s*/g,"");


	});







	// نمونه کد اینزرت با دکمه اینتر
	// $("#pass").keypress(function(event) {
	// 	if (event.keyCode == 13) {
	// 		$("#GFG_Button").click();
	// 	}
	// });


	//$(document).on('click', '#insert', function(e) {
	//	var code=$("#code").val();
	//	var mablagh=$("#mablagh").val();
	//	var start_date=$("#start_date").val();
	//	var end_date=$("#end_date").val();
	//			$.post('<?php //echo base_url()?>//admin/off_code_validation', {
	//					'code': code,
	//					'mablagh': mablagh,
	//					'start_date': start_date,
	//					'end_date': end_date,
	//				},
	//				function (data) {
	//					if (data.includes(1)) {
	//						$.post('<?php //echo base_url()?>//admin/insert_off_code', {
	//								'code': code,
	//								'mablagh': mablagh,
	//								'start_date': start_date,
	//								'end_date': end_date,
	//							},
	//							function (data) {
	//								if (data.includes(1)) {
	//									//$('#off_data').DataTable().ajax.reload();
	//									//$('#new-modal').modal('hide');
	//									window.location.href = "<?php //echo base_url(); ?>//admin/off_code";
	//									$('#code').val('');
	//									$('#mablagh').val('');
	//									$('#start_date').val('');
	//									$('#end_date').val('');
	//
	//								}
	//						});
	//
	//					}else {
	//						if(data.includes(2)){
	//							var co=$('#code');
	//							co.css({'border-color':'red'});
	//							co.keyup(function(){
	//								co.css({'border-color':''});
	//							});
	//
	//						}
	//						if(data.includes(3)){
	//							var mab=$('#mablagh');
	//							mab.css({'border-color':'red'});
	//							mab.keyup(function(){
	//								mab.css({'border-color':''});
	//							});
	//
	//						}
	//
	//
	//					}
	//			});
	//
	//});

	// $('#end_date').click(function(){
	// 	let end = $('#start_date').val();
	// 	$('#end_date')[0].setAttribute('min', end);
	//
	// 	if($('#end_date').val() !='') {
	// 		if ($('#start_date').val() == '' || $('#end_date').val() < $('#start_date').val()) {
	// 			$("#end_date").val('');
	// 			$("#end_date").prop('readonly', true);
	// 		} else {
	// 			console.log($('#start_date').val())
	// 			$("#end_date").prop('readonly', false);
	// 		}
	// 	}else{
	// 		if ($('#start_date').val() == '') {
	// 			$("#end_date").val('');
	// 			$("#end_date").prop('readonly', true);
	// 		} else {
	// 			console.log($('#start_date').val())
	// 			$("#end_date").prop('readonly', false);
	// 		}
	// 	}
	// });
	//
	//
	// $('#start_date').click(function(){
	// 	if ($('#start_date').val() != '') {
	// 		let x = $('#end_date').val();
	// 		$('#start_date')[0].setAttribute('max', x);
	// 	}
	// });
	//
	// let today = new Date().toISOString().split('T')[0];
	// $('#start_date')[0].setAttribute('min', today);
	//
	//
	// // $('#end_date').click(function(){
	// // 	let end = $('#start_date').val();
	// // 	$('#end_date')[0].setAttribute('min', end);
	// // });
	//
	// $('#end_date_edit').click(function(){
	// 	let end = $('#start_date_edit').val();
	// 	$('#end_date_edit')[0].setAttribute('min', end);
	//
	// 	if($('#end_date_edit').val() !='') {
	// 		if ($('#start_date_edit').val() == '' || $('#end_date_edit').val() < $('#start_date_edit').val()) {
	// 			$("#end_date_edit").val('');
	// 			$("#end_date_edit").prop('readonly', true);
	// 		} else {
	// 			console.log($('#start_date_edit').val())
	// 			$("#end_date_edit").prop('readonly', false);
	// 		}
	// 	}else{
	// 		if ($('#start_date_edit').val() == '') {
	// 			$("#end_date_edit").val('');
	// 			$("#end_date_edit").prop('readonly', true);
	// 		} else {
	// 			console.log($('#start_date_edit').val())
	// 			$("#end_date_edit").prop('readonly', false);
	// 		}
	// 	}
	// });
	//
	//
	// $('#start_date_edit').click(function(){
	// 	if ($('#start_date_edit').val() != '') {
	// 		let x = $('#end_date_edit').val();
	// 		$('#start_date_edit')[0].setAttribute('max', x);
	// 	}
	// });







	// $(document).on('click', '.tag', function(e) {
	// 	let id = $(this).attr("id_c");
	// 	let x=$("#tag_"+id).text();
	// 	console.log(x);
	// 	$("#code").val(x);
	// });
	//

	// $(document).ready(function(){


		// $("#end_date").prop('readonly', true);


	//	function rand_code(length) {
	//		var result           = '';
	//		var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	//		var charactersLength = characters.length;
	//		for ( var i = 0; i < length; i++ ) {
	//			result += characters.charAt(Math.floor(Math.random() * charactersLength));
	//		}
	//		return result;
	//	}
	//
	//	$('#code').val('CODE-'+rand_code(5));
	//
	//
	//
	//
	//	$('#dp_code').hide();
	//	window.addEventListener('click', function(e){
	//		if (document.getElementById('dp_code').contains(e.target)){
	//		} else{
	//			$('#dp_code').hide();
	//		}
	//	});
	//
	//
	//
	//
	//	$('#email_result')
	//	$('#code').keyup(function() {
	//
	//		function load_data(query) {
	//			$.ajax({
	//				url: "<?php //echo base_url(); ?>//admin/search_code",
	//				method: "POST",
	//				data: {query: query,},
	//				success: function (data) {
	//					if (data == 0) {
	//						$('#dp_code').hide();
	//					} else {
	//						$('#dp_code').show();
	//						$('#dp_code').html(data);
	//
	//
	//					}
	//
	//				}
	//			});
	//		}
	//
	//		var code = $('#code').val();
	//
	//		if(code != '')
	//		{
	//			load_data(code);
	//			$.ajax({
	//				url:"<?php //echo base_url(); ?>//admin/check_code",
	//				method:"POST",
	//				data:{code:code},
	//				success:function(data){
	//					$('#email_result').html(data);
	//				}
	//			});
	//		}else{
	//			load_data();
	//			$('#email_result').html('');
	//		}
	//	});
	//
	//
	//	$('#email_result2')
	//	$('#mablagh').keyup(function(){
	//		var mablagh = $('#mablagh').val();
	//		if(mablagh != '')
	//		{
	//			$.ajax({
	//				url:"<?php //echo base_url(); ?>//admin/check_mablagh",
	//				method:"POST",
	//				data:{mablagh:mablagh},
	//				success:function(data){
	//					$('#email_result2').html(data);
	//				}
	//			});
	//		}else{
	//			$('#email_result2').html('');
	//		}
	//	});
	//});
	//
	//
	//$('.positive').keydown( function(e) {
	//	if(!((e.keyCode > 95 && e.keyCode < 106)
	//		|| (e.keyCode > 47 && e.keyCode < 58)
	//		|| e.keyCode == 8)) {
	//		return false;
	//	}
	//});





	//---------عملیات ویرایش-----------
	//$(document).on('click', '#btn_edit', function (e) {
	//
	//
	//	var mablagh=$("#mablagh_edit").val();
	//	var start_date=$("#start_date_edit").val();
	//	var end_date=$("#end_date_edit").val();
	//
	//	var ids = '';
	//	var comma = '';
	//
	//
	//
	//	$("input:checkbox[name='row-check']:checked").each(function() {
	//		ids = ids + comma + this.value;
	//		comma = ',';
	//	});
	//
	//	if(ids.length > 0) {
	//
	//		// var m = confirm('آیا از ویرایش محصول اطمینان دارید؟');
	//		// if (m == true) {
	//
	//		$.post('<?php //echo base_url()?>//admin/update_off_checkall', {
	//				'id': ids, 'mablagh': mablagh, 'start_date': start_date,
	//				'end_date': end_date,
	//			},
	//			function (data) {
	//				if (data.includes(1)) {
	//					$("#mablagh").val('');
	//					$("#start_date").val('');
	//					$("#end_date").val('');
	//
	//					alert('ویرایش محصول انجام شد');
	//					$('#off_data').DataTable().ajax.reload();
	//				}
	//
	//			});
	//
	//		// }
	//		// if (m == false) {
	//		// 	return
	//		// }
	//
	//
	//	}
	//
	//});

</script>

