<style>
	/* The snackbar - position it at the bottom and in the middle of the screen */
	#snackbar, #snackbar2 {
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
	#snackbar.show ,  #snackbar2.show{
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


	<div class="container-fluid" id="content">
		<div class="row" style="margin-top: 30px; margin-bottom: 60px">
			<div class="col-md-3"></div>
			<div class="col-md-6" style="border: 1px solid #ccc; padding: 20px ; border-radius: 10px">


				<input autocomplete="off" class="form-control" disabled type="hidden" id="code" value="<?php echo microtime(true).rand(1111,9999); ?>"><br>

				<label>دسته اول:</label>
				<br>
				<select class="form-control" id="cat1_show">
					<?php foreach ($category1 as $cat1){ ?>
							<option value="">انتخاب کنید</option>
						<option id="cat1_option_1" value="<?php echo $cat1->id;?>"><?php echo $cat1->name_cat1?></option>

					<?php }?>
					<!--					<option id="show_cat_option_1"></option>-->

				</select>
				<br>
				<label>دسته دوم:</label>
				<br>
				<select class="form-control" id="cat2_show">
					<option value="">انتخاب کنید</option>
				</select>
				<br>
				<label>نام محصول:</label>
				<input autocomplete="off" id="name" type="text" class="form-control" placeholder="نام محصول">
				<br>
				<label for="model">مدل محصول:</label>
				<select id="model" name="model" multiple="multiple" class="model form-control">
					<?php foreach ($size as $si){ ?>
						<option value="<?php echo $si->id;?>"><?php echo $si->name?></option>
					<?php }?>

				</select>
				<br>
				<br>
				<button class="btn btn-default" type='button' id='selectAll_model'>Select All</button>
				<button class="btn btn-default" type='button' id='deselectAll_model'>Deselect All</button>
				<br>
				<br>
				<label for="jens">جنس:</label>
				<select id="jens" name="jens" multiple="multiple" class="jens form-control">
					<?php foreach ($size as $si){ ?>
						<option value="<?php echo $si->id;?>"><?php echo $si->name?></option>
					<?php }?>

				</select>
				<br>
				<br>
				<button class="btn btn-default" type='button' id='selectAll_jens'>Select All</button>
				<button class="btn btn-default" type='button' id='deselectAll_jens'>Deselect All</button>
				<br>
				<br>
				<label for="size">سایز:</label>
				<select id="size" name="size" multiple="multiple" class="size form-control">
					<?php foreach ($size as $si){ ?>
						<option value="<?php echo $si->id;?>"><?php echo $si->name?></option>
					<?php }?>

				</select>
				<br>
				<br>
				<button class="btn btn-default" type='button' id='selectAll_size'>Select All</button>
				<button class="btn btn-default" type='button' id='deselectAll_size'>Deselect All</button>
				<button class="btn btn-success" data-toggle="modal" data-target="#new-modal"
						style="outline: unset">جدید
				</button>
				<br>
				<br>
				<br>
				<label for="brand">برند:</label>
				<select id="brand" name="brand" multiple="multiple" class="brand form-control">
					<?php foreach ($size as $si){ ?>
						<option value="<?php echo $si->id;?>"><?php echo $si->name?></option>
					<?php }?>

				</select>
				<br>
				<br>
				<button class="btn btn-default" type='button' id='selectAll_brand'>Select All</button>
				<button class="btn btn-default" type='button' id='deselectAll_brand'>Deselect All</button>
				<br>
				<br>
				<br>
				<label for="color">رنگ:</label>
				<select id="color" name="color" multiple="multiple" class="color form-control">
					<?php foreach ($size as $si){ ?>
						<option value="<?php echo $si->id;?>"><?php echo $si->name?></option>
					<?php }?>

				</select>
				<br>
				<br>
				<button class="btn btn-default" type='button' id='selectAll_color'>Select All</button>
				<button class="btn btn-default" type='button' id='deselectAll_color'>Deselect All</button>
				<button class="btn btn-default" type='button' id='add_color'>add_color</button>
				<br>
				<br>
				<br>
				<label>قیمت:</label>
				<input autocomplete="off" id="price" type="number" class="form-control" placeholder="قیمت">
				<br>
				<label>درصد تخفیف:</label>
				<input autocomplete="off" id="off_percent" type="number" class="form-control" placeholder="درصد تخفیف">
				<br>
				<label>توضیحات:</label>
				<textarea id="text1" class="ckeditor" rows="30" cols="70" name="text1"></textarea>
				<br>

				<div style="width: 100%">

					<!------upload_img--------->
					<input autocomplete="off" id="files_image" type="file" multiple><br>
					<div id="image_prd"></div>

				</div>

				<br>
				<br>
				<button id="accept11" class="btn btn-success">ثبت محصول</button>

			</div>
			<div class="col-md-2"></div>
		</div>
		<div id="snackbar"></div>
		<div id="snackbar2"></div>
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
					<input required autocomplete="off" id="name_size" class="form-control" type="text" placeholder="سایز">
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

<?php }?>

<script>

	$(document).on('click', '#insert', function (e) {
		var name = $("#name_size").val();

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
								$('#name_size').val('');
								$('#error').html('');
								$('#new-modal').modal('hide');


								var data = {
									id: 1,
									text: name
								};

								var newOption = new Option(data.text, data.id, false, false);
								$('#size').append(newOption).trigger('change');
							}
						});

				} else {
					if (data.includes(2)) {
						var na = $('#name_size');
						na.css({'border-color': 'red'});
						na.keyup(function () {
							na.css({'border-color': ''});
						});

					}


				}
			});

	});




	$('#selectAll_model').click(function() {
		$("#model > option").prop("selected", true);
		$("#model").trigger("change");
	});
	$('#deselectAll_jens').click(function() {
		$("#model").val(null).trigger("change");
	});

	$('#selectAll_jens').click(function() {
		$("#jens > option").prop("selected", true);
		$("#jens").trigger("change");
	});
	$('#deselectAll_jens').click(function() {
		$("#jens").val(null).trigger("change");
	});

	$('#selectAll_size').click(function() {
		$("#size > option").prop("selected", true);
		$("#size").trigger("change");
	});
	$('#deselectAll_size').click(function() {
		$("#size").val(null).trigger("change");
	});

	$('#selectAll_brand').click(function() {
		$("#brand > option").prop("selected", true);
		$("#brand").trigger("change");
	});
	$('#deselectAll_brand').click(function() {
		$("#brand").val(null).trigger("change");
	});
	$('#selectAll_color').click(function() {
		$("#color > option").prop("selected", true);
		$("#color").trigger("change");
	});
	$('#deselectAll_color').click(function() {
		$("#color").val(null).trigger("change");
	});
	$(document).on('click', '#accept11', function(e){
		var code=$("#code").val();
		var id_cat1=$("#cat1_show").val();
		var id_cat2=$("#cat2_show").val();
		var name=$("#name").val();
		var model=$("#model").val();
		var jens=$("#jens").val();
		var size=$("#size").val();
		var brand=$("#brand").val();
		var price=$("#price").val();
		var off_percent=$("#off_percent").val();
		var text=CKEDITOR.instances['text1'].getData();


		if(model==''){model[0]=null;}
		if(jens==''){jens[0]=null;}
		if(size==''){size[0]=null;}
		if(brand==''){brand[0]=null;}

			var j=0;
			var k=0;

			for (var i1=0; i1<model.length; i1++) {
				for (var i2=0; i2<jens.length; i2++) {
					for (var i3=0; i3<size.length; i3++) {
						for (var i4=0; i4<brand.length; i4++) {

console.log('size['+i1+']'+size[i1]);
console.log('jens['+i2+']'+jens[i2]);
console.log('size['+i3+']'+size[i3]);
console.log('brand['+i4+']'+brand[i4]);
console.log('id_cat1'+id_cat1);
console.log('id_cat2'+id_cat2);

			$.post('<?php echo base_url()?>admin/insert_prd2',{'code':code, 'id_cat1':id_cat1, 'id_cat2':id_cat2,
					'name':name, 'model':model[i1], 'jens':jens[i2], 'size':size[i3], 'brand':brand[i4], 'price':price,'off_percent':off_percent,'text':text},
				function (data) {
					if (data == 1) {
						j++;


					}else{
						k++;
					}
					if( i1 == model.length){

						//$("#text1").val('');

						//window.location.reload();
						document.getElementById("snackbar").innerHTML=j+' محصول اضافه شد';
						document.getElementById("snackbar2").innerHTML=k+' مورد خطا';
						// Get the snackbar DIV
						var x = document.getElementById("snackbar");
						var x2 = document.getElementById("snackbar2");

						// Add the "show" class to DIV
						x.className = "show";
						x2.className = "show";

						// After 3 seconds, remove the show class from DIV
						setTimeout(function(){ x.className = x.className.replace("show", ""); }, 10000);
						setTimeout(function(){ x2.className = x2.className.replace("show", ""); }, 5000);
					}

				});

			}}}}



	});
	//---------get_category-----------
	$('#cat1_show').change(function(){
		id_cat1=$(this).val();
		$.post('<?php echo base_url();?>admin/get_category2',{'id_cat1':id_cat1},
			function(data){
				$('#cat2_show').html(data);
			});
	});

	$('#files_image').change(function(){
		var code=$("#code").val();

		var files = $('#files_image')[0].files;
		var error = '';
		var form_data = new FormData();
		for(var count = 0; count<files.length; count++)
		{
			var name = files[count].name;
			var extension = name.split('.').pop().toLowerCase();
			if(jQuery.inArray(extension, ['gif','png','jpg','jpeg']) == -1)
			{
				error += "Invalid " + count + " Image File"
			}
			else
			{
				form_data.append("files[]", files[count]);
				form_data.append("code",code );
			}
		}

		if(error == '')
		{
			$.ajax({
				url:"<?php echo base_url(); ?>admin/upload_img",
				method:"POST",
				data:form_data,
				contentType:false,
				cache:false,
				processData:false,
				beforeSend:function()
				{
				},
				success:function(data)
				{
					$('#image_prd').append(data);
					$('#files_image').val('');

				}
			})
		}
		else
		{
			alert('بیش 5 عکس مجاز نیستید');
		}
	});
	$(document).on('click', '#delete_image', function(e){
		id_img=$(this).attr("id_img");
		console.log(id_img);
		$.post('<?php echo base_url();?>admin/delete_img',{'id':id_img,},
			function (data) {
				if (data==1){
					$("#show_image_"+id_img).remove();
				}
			}
		);
	});

	//این کد برای ذخیره ورودی سلک لیست در جدول دیتابیسی است. با دکمه اینتر انجام می شود
//	/*$(document).on('keyup','.select2-search__field', function(e){
//		var b=$(this).attr('aria-describedby');
//		var bb=b.toString();
//		var id=bb.substring( bb.indexOf("-") + 1, bb.lastIndexOf("-") );
//		console.log(id);
//		if($(this).prop('aria-describedby','select2-'+id+'-container')){
//			console.log($(this).val());
//			var name=$(this).val();
//		}
//		if(e.key === 'Enter' || e.keyCode === 13)  // the enter key code
//		{
//			console.log(id);
//			$.post('<?php //echo base_url()?>//admin/'+id+'_validation', {
//					'name': name,
//				},
//				function (data) {
//					if (data.includes(1)) {
//						$.post('<?php //echo base_url()?>//admin/insert_'+id , {
//								'name': name,
//
//							},
//							function (data) {
//								if (data.includes(1)) {
//									console.log('success');
//									$("#"+id).trigger("change");
//
//								}
//							});
//
//					} else {
//						if (data.includes(2)) {
//							console.log('fail');
//
//						}
//
//
//					}
//				});
//		}
//	});
//*/

	//در کد زیر با استفاده از تریگر پارامتر از ایونت اول 'کیاپ' به ایونت 'دوم' کلیک انتقال داده می شود و بدون نیاز به کلید وارد ایونت کلیک می شود
		// $(document).on('keyup','.select2-search__field', function(e){
		//
		// 	var b=$(this).attr('aria-describedby');
		// 	var bb=b.toString();
		// 	var id=bb.substring( bb.indexOf("-") + 1, bb.lastIndexOf("-") );
		// 	if($(this).prop('aria-describedby','select2-'+id+'-container')){
		// 		var name=$(this).val();
		// 	}
		// 	$('#add_size').trigger('click', {param1: name,param2: id});
		// });
		// 	$(document).on('click','#add_size', function(e,data){
		// 		console.log("param1:"+data.param1);
		// 		console.log("param2:"+data.param2);
		// 		var id=data.param2;
		// 		var name=data.param1;
		//
		// 	});


	$(document).ready(function(){


		$('#dp_name').hide();
		window.addEventListener('click', function (e) {
			if (document.getElementById('dp_name').contains(e.target)) {
			} else {
				$('#dp_name').hide();
			}
		});

		$('#name_size').keyup(function () {

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

			var name = $('#name_size').val();

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


		$('.model').select2();
		$('.jens').select2();
		$('.size').select2({
			placeholder: 'Your NULL value caption',
			allowClear: true,
			language: {
				noResults: function () {
					return "موردی یافت نشد";
				}
			}
		});
		$('.brand').select2();
		$('.color').select2();


	});




</script>
