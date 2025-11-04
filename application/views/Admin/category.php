
<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content">


		<div>
			<a href="<?php echo base_url('admin/insert_category')?>">
				<button class="btn btn-sm ml-3 btn-success"
						style="outline: unset">دسته بندی جدید
				</button>
			</a>
		</div>

		<br>
		<br>
		<table id="category_data" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>
						<label for="checkbox">همه</label>
						<input type="checkbox" id='check_all'>
						<br>
						<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" >حذف</button>
						<br>
						<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش</button>
					</th>
					<th >دسته اول</th>
					<th >دسته دوم</th>
					<th >کپی</th>
					<th >ویرایش</th>
					<th >حذف</th>
				</tr>
			</thead>
		</table>


	</div>

<?php } ?>



<script>

	$(document).ready(function () {
		var dataTable = $('#category_data').DataTable({
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
				url: "<?php echo base_url() . 'admin/category_list'; ?>",
				type: "POST"
			},
			"columnDefs": [
				{
					"targets": [0, 3, 4, 5],
					"orderable": false,
				},
			],
		});
	});


	// -------insert_cat1---------
	$('#btn_insert1').click(function(){
		var name_cat1=$('#insert_cat1').val();
		if(name_cat1 != "") {
			$.post('<?php echo base_url();?>admin/insert_cat1', {'name_cat1': name_cat1,},
					function (data) {
						var blocked = [];
						var i = 0;
						$.each($.parseJSON(data), function (idx, obj) {
							blocked.push([obj.id, obj.name_cat1,]);
							i++;
						});

						new_cat1 = '<option id="cat1_option" value="' + blocked[0][0] + '">' + blocked[0][1] + '</option>';

						$('#insert_cat1').val('');
						$('#cat1_show').append(new_cat1);
						$('#cat1_show_1').append(new_cat1);
						alert("دسته ی جدید اضافه شد")
					}
			);
		}else{
			alert("برای دسته ی اول یک نام وارد کنید")
		}

	});
	//----------insert_cat2----------
	$('#btn_insert2').click(function(){
		var id_cat1=$('#cat1_show_1').val();
		var name_cat2=$('#insert_cat2').val();
		if(id_cat1 != "" ) {
			if(name_cat2 != ""){
			$.post('<?php echo base_url();?>admin/insert_cat2', {'id_cat1': id_cat1, 'name_cat2': name_cat2,},
					function (data) {
						var blocked = [];
						var i = 0;
						$.each($.parseJSON(data), function (idx, obj) {
							blocked.push([obj.id, obj.name_cat2,]);
							i++;
						});

						new_cat1 = '<option id="cat2_option" value="' + blocked[0][0] + '">' + blocked[0][1] + '</option>';

						$('#insert_cat2').val('');
						$('#cat2_show').append(new_cat1);
						$('#cat2_show_1').append(new_cat1);
						alert("دسته ی جدید اضافه شد")
					}
			);
		}else {
			alert('برای دسته دوم یک نام وارد کنید')
		}}else {
			alert('دسته اول را انتخاب کنید')
		}
	});

	$(document).on('click', '#delete_cat1', function(e){
		var id_cat1=$('#cat1_show').val();
		if (id_cat1 != "") {
			var m = confirm('آیا از حذف دسته و زیر دسته ها و محصولات مرتبط مطمئن هستید؟')
			if (m == true) {
				$.post('<?php echo base_url();?>admin/delete_cat1', {'id': id_cat1,},
						function (data) {
							window.location.href="<?php echo base_url(); ?>admin/category";
					});
			}
			if (m == false){
				return
			}
		}else {
			alert('دسته را انتخاب کنید')
		}
	});
	$(document).on('click', '#delete_cat2', function(e){
		var id_cat2=$('#cat2_show').val();
		if (id_cat2 != "") {
			var m = confirm('آیا از حذف زیر دسته و محصولات مرتبط مطمئن هستید؟')
			if (m == true) {
				$.post('<?php echo base_url();?>admin/delete_cat2', {'id': id_cat2,},
						function (data) {
							window.location.href="<?php echo base_url(); ?>admin/category";
						}
				);
			}
			if (m == false){
				return
			}
		}
		else {
			alert('دسته دوم را انتخاب کنید')

		}
	});
	$('#cat1_show_1').change(function(){
		id_cat1=$(this).val();
		$.post('<?php echo base_url();?>admin/get_category2',{'id_cat1':id_cat1},
				function(data){
					$('#cat2_show').html(data);
				});
	});
	$(document).on('click', '#edit_c1', function(e){
		var id=$("#cat1_show").val();
		var name=$("#insert_cat1").val();
		if (id != ""){
			if(name != "") {
				var m = confirm('آیا از ویرایش نام دسته مطمئن هستید؟')
				if (m == true) {
					$.post('<?php echo base_url()?>admin/edit_cat1',{'id':id,'name_cat1':name,},
							function (data) {
								if (data==1){
									window.location.href="<?php echo base_url(); ?>admin/category";
								}
							});
				}else if (m == false){
					return
				}

			}else {
				alert('مقدار جدید را وارد کنید')
			}
		}
		else {
			alert('دسته ای که میخواهید ویرایش کنید را انتخاب کنید')
		}

	});
	$(document).on('click', '#edit_c2', function(e){
		var id1=$("#cat1_show_1").val();
		var id=$("#cat2_show").val();
		var name=$("#insert_cat2").val();
		if(id1 != "" ) {
			if(id != "" ) {
				if(name != "") {
					var m = confirm('آیا از ویرایش نام دسته مطمئن هستید؟')
					if (m == true) {
						$.post('<?php echo base_url()?>admin/edit_cat2', {'id': id, 'name_cat2': name,},
								function (data) {
									if (data == 1) {
										window.location.href = "<?php echo base_url(); ?>admin/category";
									}

								});
					}else if (m == false){
							return
					}

				}else {
					alert('مقدار جدید را وارد کنید')
				}
			}else {
				alert('دسته دوم را انتخاب کنید')
			}
		}else {
			alert('دسته اول را انتخاب کنید')
		}
	});



</script>
