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
	<input id="xxx" type="hidden" value="<?php echo $id;?>">
	<div>

		<button id="new" class="btn btn-sm ml-3 btn-success" style="outline: unset" >افزودن کالای جدید به انبار</button>
		<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" >حذف همه</button>
		<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش همه</button>
		<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
		<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
	</div>

	<br>
	<br>
	<table id="inv_sup_data" class="table table-bordered table-striped" style="width: 100%">
		<thead>
		<tr id="id_inv">
			<!--					<th width="5%">icon</th>-->
			<th>
				<label for="checkbox">همه</label>
				<input type="checkbox" id='check_all'>
			</th>
			<th>کد کالا</th>
			<th> نام کالا</th>
			<th>موجودی</th>
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
					<form id="xxx" method="post" action='<?php echo base_url('admin/insert_inv_sup') ?>' onsubmit="return validateForm()" >
						<input id="id_inv" name="id_inv" hidden
							   value="<?php echo $id;?>">
						<div class="form-group">
							<label>محصول</label>
							<?php echo form_error('prd','<span style="color: red">','</span>') ?>
							<select onfocus='this.size=3;' onblur='this.size=1;' onchange='this.size=1; this.blur();'
									id="aaa" name="aaa" class="form-control">
								<option selected value="0">انتخاب کنید..</option>
								<?php foreach ($products as $p){foreach ($product_attributes as $pa){if($p->code==$pa->code_p){?>
									<option value="<?php echo $pa->id;?>"
											price="<?php echo $pa->price;?>"
											id_attr="<?php echo $pa->id;?>"
											id_p="<?php echo $p->id;?>"
											name="<?php echo $p->name;?>"
											code_p="<?php echo $pa->code_p;?>"

									>
										<?php  echo $p->name,' - ',$pa->price,' ';
										if($pa->model!=0 || $pa->jens!=0 || $pa->size!=0 || $pa->brand!=0 || $pa->color!=0){echo '(';}
										if($pa->model!=0){foreach ($model as $mo){if($pa->model==$mo->id){ echo 'مدل:',$mo->name,' ';}}}
										if($pa->jens!=0){foreach ($jens as $je){if($pa->jens==$je->id){ echo 'جنس:',$je->name,' ';}}}
										if($pa->size!=0){foreach ($size as $si){if($pa->size==$si->id){ echo 'سایز:',$si->name,' ';}}}
										if($pa->brand!=0){foreach ($brand as $br){if($pa->brand==$br->id){ echo 'برند:',$br->name,' ';}}}
										if($pa->color!=0){foreach ($color as $co){if($pa->color==$co->id){ echo 'رنگ:',$co->name,' ';}}}
										if($pa->model!=0 || $pa->jens!=0 || $pa->size!=0 || $pa->brand!=0 || $pa->color!=0){echo ')';}
										?>
									</option>
								<?php }}}?>
							</select>
						</div>
						<div class="form-group">
							<label for="supply">توضیحات:</label><br>
							<input autocomplete="off" id="supply" name="supply"
								   class="form-control" type="text"
								   placeholder="تعداد">
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

			</div>

		</div>
	</div>

<?php }?>

<script type="text/javascript" language="javascript" >



	$(document).ready(function(){

		var dataTable = $('#inv_sup_data').DataTable({
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
				url:'<?php echo base_url();?>admin/inventory_supply_list/'+$('#xxx').val(),
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


	//------نمایش مودال---------
	$('#new').on("click",function (){
		$("#new-modal").modal();
	});

</script>
