<?php if($this->session->userdata('id')){ ?>


	<div class="container-fluid" id="content">
		<div class="row" style="margin: 60px">
			<div style="border: 1px solid #ccc; padding: 20px ; border-radius: 10px; ">


				<input autocomplete="off" class="form-control" disabled id="code" value="<?php echo microtime(true).rand(1111,9999); ?>"><br>

				<br>
				<div class="form-group" style="">
<!--					<i class="fa fa-info-circle"-->
<!--					   style="font-size: 26px; color: white"-->
<!--					   title="برای انتخاب همه این فیلد را خالی بگذارید"-->
<!--					></i>-->
					<label for="category[]" class="">انتخاب دسته:</label>
					<br>
					<select multiple class="form-control category" id="category" name="category[]" >

						<!--<option selected value="">--><?php //echo "انتخاب کنید.."; ?><!--</option>-->

						<?php foreach ($category_test as $cat){ ?>
							<option value="<?php echo $cat->id?>" <?= set_select('category', $cat->id, false) ?> ><?php echo $cat->name_cat?></option>
						<?php }?>
					</select>
					<?php echo form_error('category','<span style="color: red">','</span>') ?>
					<br>
					<!--							<br>-->
					<!--							<br>-->
					<!--							<button type="button" id="selectAll_p" class="btn btn-default">انتخاب همه</button>-->
					<!--							<button type="button" id="deselectAll_p" class="btn btn-default">حذف همه</button>-->

				</div>
				<br>

				<label>دسته اول:</label>
				<br>
				<select class="form-control" id="cat1_show">
					<option value="">انتخاب کنید</option>
					<?php foreach ($category1 as $cat1){ ?>
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
				<br>


				<div class="row" id="show_item">
							<label for="model_checkbox">مدل</label>
							<input type="checkbox" name="model_checkbox" id="model_checkbox">
							<label for="jens_checkbox">جنس</label>
							<input type="checkbox" name="jens_checkbox" id="jens_checkbox">
							<label for="size_checkbox">سایز</label>
							<input type="checkbox" name="size_checkbox" id="size_checkbox">
							<label for="brand_checkbox">برند</label>
							<input type="checkbox" name="brand_checkbox" id="brand_checkbox">
							<label for="color_checkbox">رنگ</label>
							<input type="checkbox" name="color_checkbox" id="color_checkbox">
							<br>

								<button id="add_row" class="btn btn-success add_item_btn" type="button">
									<i class="fa fa-plus"></i>
								</button>
								<select class="form-control" id="model" style="width: 12%; display: none" >
									<option value="" disabled selected hidden>مدل محصول</option>
									<?php foreach ($model as $mo){ ?>
										<option id="cat1_option_1" value="<?php echo $mo->id;?>"><?php echo $mo->name?></option>
									<?php }?>

								</select>
								<select class="form-control" id="jens" style="width: 12%; display: none">
									<option value="" disabled selected hidden>جنس محصول</option>
									<?php foreach ($jens as $je){ ?>
										<option id="cat1_option_1" value="<?php echo $je->id;?>"><?php echo $je->name?></option>
									<?php }?>

								</select>
								<select class="form-control" id="size" style="width: 12%; display: none">
									<option value="" disabled selected hidden>سایز محصول</option>
									<?php foreach ($size as $si){ ?>
										<option id="cat1_option_1" value="<?php echo $si->id;?>"><?php echo $si->name?></option>
									<?php }?>

								</select>
								<select class="form-control" id="brand" style="width: 12%; display: none">
									<option value="" disabled selected hidden>برند محصول</option>
									<?php foreach ($brand as $br){ ?>
										<option id="cat1_option_1" value="<?php echo $br->id;?>"><?php echo $br->name?></option>
									<?php }?>

								</select>
								<select class="form-control" id="color" style="width: 12%; display: none">
									<option value="" disabled selected hidden>رنگ محصول</option>
									<?php foreach ($color as $co){ ?>
										<option id="cat1_option_1" value="<?php echo $co->id;?>" style="background-color: <?php echo $co->color_code ?>">
											<span style="background-color: <?php echo $co->color_code ?>"><?php echo $co->name ?></span>
										</option>
									<?php }?>

								</select>

							<label class="required"></label>
								<input style="width: 12%; display: inline" autocomplete="off" id="buy_price" min="0" type="number" class="form-control positive" placeholder="قیمت خرید">
								<input style="width: 12%; display: inline" autocomplete="off" id="price" min="0" type="number" class="form-control positive" placeholder="قیمت">

								<input style="width: 8%; display: inline" autocomplete="off" id="off_percent" min="0" type="number" class="form-control positive" placeholder="درصد تخفیف">
								<input style="width: 12%; display: inline" autocomplete="off" id="supply" min="0" type="number" class="form-control positive" placeholder="موجودی">
<!--								<input id="default_row" type="radio">-->

						</div>

				<br>


				<div class="row" id="results">
							<table class="table table-bordered table-striped" id="attr-tb">
								<thead id="t-head">
								<tr id="h-tr">
									<th class="hide_div"></th>
									<th class="model_cells" id="model_cells">model</th>
									<th class="jens_cells" id="jens_cells">jens</th>
									<th class="size_cells" id="size_cells">size</th>
									<th class="brand_cells" id="brand_cells">brand</th>
									<th class="color_cells" id="color_cells">color</th>
									<th>buy_price</th>
									<th>price</th>
									<th>off_percent</th>
									<th>supply</th>

									<th style="display: none" id="isActive_hide">isActive_hide</th>
									<th>isActive</th>

									<th style="display: none" id="default_hide">default_hide</th>
									<th>default_row</th>
								</tr>

								</thead>
								<tbody id="t-body">

								</tbody>
							</table>


						</div>


				<button id="add_btn" class="btn btn-primary w-25" type="button"
						data-toggle="tooltip" data-placement="left" title=""
						data-original-title="ثبت ویژگی های محصول">add</button>





				</div>


				<br>
				<br>
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
				<button id="accept11" class="btn btn-success"
						type="button"
						data-toggle="tooltip" data-placement="left" title=""
						data-original-title="ثبت محصول">ثبت محصول</button>

			</div>

		</div>

	</div>
<?php }?>

<script>

	$(document).ready(function() {

		$('.category').select2({
			placeholder: 'انتخاب کنید...',
			// allowClear: true,
			language: {
				noResults: function () {
					return "موردی یافت نشد";
				}
			},
			closeOnSelect: false,
		});
	});

	$(document).on('click', '#accept11', function(e){
		var code=$("#code").val();
		var id_cat1=$("#cat1_show").val();
		var id_cat2=$("#cat2_show").val();
		var name=$("#name").val();
		var text=CKEDITOR.instances['text1'].getData();

			$.post('<?php echo base_url()?>admin/insert_prd',{'code':code, 'id_cat1':id_cat1, 'id_cat2':id_cat2,
					'name':name, 'text':text},
				function (data) {
					if (data == 1) {
						alert('ذخیره با موفقیت انجام شد');
						$("#cat1_show").val('');
						$("#cat2_show").val('');
						$("#name").val('');
						$("#text1").val('');
						window.location.href = "<?php echo base_url(); ?>admin/insert_product";
						// window.location.reload();
					}
				});


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


	$(document).on('click', '#add_btn', function(e) {
		var code = $('#code').val();
		var myRows = [];
		var $headers = $('#t-head tr th');
		var rows = $("#t-body #hidden_tr").each(function (index) {
			$cells = $(this).find("td");
			myRows[index] = {};
			$cells.each(function (cellIndex) {
				myRows[index][$($headers[cellIndex]).html()] = $(this).html();
			});
		});
		// var myobj = {};
		// myobj.myrows = myRows;
		// // alert(JSON.stringify(myobj));

		$.post('<?php echo base_url()?>admin/insert_prd8', {sendData: JSON.stringify(myRows), code:code}, function (data){
			console.log(data);
			if(data==1){
				$('#show_item').remove();
				$('#add_btn').remove();
				$('.hide_div').hide();
				$('input[id=default2]').attr("disabled",true);
				$('input[id=isActive2]').attr("disabled",true);
				$('input[id=default]').attr("disabled",true);
				$('input[id=isActive]').attr("disabled",true);
				$('#accept11').attr("disabled",false);
			}
		}, "json");


	});

	$(document).on('click', '#add_row', function(e){
		var model = $('#model').val();
		var jens = $("#jens").val();
		var size = $("#size").val();
		var brand = $("#brand").val();
		var color = $("#color").val();
		var supply = $("#supply").val();
		var buy_price = $('#buy_price').val();
		var price = $('#price').val();
		var off_percent = $('#off_percent').val();
		var default_row = '1';
		var isActive = '1';
		$('input[id=default2]').each(function () {
			if($(this).prop('checked') == true){
				default_row = '0';
			}
		});
		// if($('input[id=default2]').prop('checked') == false){
		// 	$('input[id=default2]').prop('checked',true);
		// 	$('#default_hide').val('1');
		// 	default_row = '1';
		// }
		// if($('input[id=default2]').prop('checked') == true){
		// 	$('#default_hide').val('0');
		// 	default_row = '0';
		// }



		if(price != '') {

			$.post('<?php echo base_url()?>admin/add_attr', {

					'model': model,
					'jens': jens,
					'size': size,
					'brand': brand,
					'color': color,
					'buy_price': buy_price,
					'price': price,
					'off_percent': off_percent,
					'supply': supply,
					'default_row': default_row,
					'isActive': isActive,

				},
				function (data) {
					$('#model').val('');
					$('#jens').val('');
					$('#size').val('');
					$('#brand').val('');
					$('#color').val('');
					$('#buy_price').val('');
					$('#price').val('');
					$('#off_percent').val('');
					$('#supply').val('');
					$('#t-body').prepend(data);
					if($('#model_checkbox').prop('checked')){
						$('.model_cells').css({'display':'revert'});
					}
					if($('#jens_checkbox').prop('checked')){
						$('.jens_cells').css({'display':'revert'});
					}
					if($('#size_checkbox').prop('checked')){
						$('.size_cells').css({'display':'revert'});
					}
					if($('#brand_checkbox').prop('checked')){
						$('.brand_cells').css({'display':'revert'});
					}
					if($('#color_checkbox').prop('checked')){
						$('.color_cells').css({'display':'revert'});
					}

				});

		}
		else{
			alert("قیمت محصول را وارد کنید");
		}
	});

	$(document).on('click', '#del_row', function(e){
		if($("#attr-tb tr").length>3 && $(this).closest('tr').next('tr').find('td:nth-child(13)').text()=='1'){
			alert('ردیف دیفالت قابل حذف نمی باشد. ردیف دیفالت را تغییر دهید');
		}
		if($("#attr-tb tr").length<=3 || $(this).closest('tr').next('tr').find('td:nth-child(13)').text()=='0'){
			$(this).closest('#t-body tr').next('tr').remove();
			$(this).closest('#t-body tr').remove();
		}
	});


	$(document).on('change', 'input[id=default2]', function(){

		$('tr').find('td:nth-child(13)').html("0");
		$(this).closest('tr').find('td:nth-child(13)').html("1");
		$(this).closest('tr').find('td:nth-child(11)').html("1");
		$(this).closest('tr').find('td:nth-child(12) input[id=isActive2]').prop('checked',true);

		$(this).closest('tr').next('tr').find('td:nth-child(13)').html("1");
		$(this).closest('tr').next('tr').find('td:nth-child(11)').html("1");
		$(this).closest('tr').next('tr').find('td:nth-child(12) input[id=isActive]').prop('checked',true);

	});

	// $(document).on('change', 'input[id=default]', function(){
	// 	if($('input[id=default]:checked').length>1){
	// 		$(this).prop('checked',false);
	// 	}
	// 	if($(this).prop('checked') == true) {
	// 		$(this).closest('tr').find('td:nth-child(12)').html("1");
	// 		$('#add_btn').attr("disabled",false);
	// 	}
	// 	if($(this).prop('checked') == false) {
	// 		$(this).closest('tr').find('td:nth-child(12)').html("0");
	// 		$('#add_btn').attr("disabled",true);
	// 	}
	// });
	$(document).on('change', 'input[id=isActive2]', function(){
		if($(this).prop('checked') == true) {
			$(this).closest('tr').next('tr').find('td:nth-child(11)').html("1");
		}
		if($(this).prop('checked') == false) {
			if($(this).closest('tr').next('tr').find('td:nth-child(13)').text()==0) {
				$(this).closest('tr').next('tr').find('td:nth-child(11)').html("0");
				$(this).closest('tr').find('td:nth-child(11)').html("0");
			}
			if($(this).closest('tr').next('tr').find('td:nth-child(13)').text()==1) {
				$(this).prop('checked', true);
			}
		}
	});

	$(document).ready(function (){
		// $('#add_btn').attr("disabled",true);
		// $('#accept11').attr("disabled",true);
		$('.model_cells').css({'display':'none'});
		$('.jens_cells').css({'display':'none'});
		$('.size_cells').css({'display':'none'});
		$('.brand_cells').css({'display':'none'});
		$('.color_cells').css({'display':'none'});

	});


	$('.positive').keydown( function(e) {
		if(!((e.keyCode > 95 && e.keyCode < 106)
			|| (e.keyCode > 47 && e.keyCode < 58)
			|| e.keyCode == 8)) {
			return false;
		}
	});

	$(document).on('change', '#buy_price,#price,#off_percent,#supply', function(){
		var num = $(this).val();
		if(num<0){
			$(this).val(0);
		}
	});


	$(document).on('change', '#model_checkbox,#jens_checkbox,#size_checkbox,#brand_checkbox,#color_checkbox', function(){
		var x = $(this).attr('id');
		var y = x.slice(0,-9);
		var z = x.slice(0,-8);
		var c = z+'cells';
		console.log(x);
		console.log(y);
		console.log(c);
		if( $(this).prop('checked') ){
			$('#'+y).css({'display':'revert'});
			$('.'+c).css({'display':'revert'});
		}
		if(! $(this).prop('checked') ){
			$('#'+y).css({'display':'none'});
			$('.'+c).css({'display':'none'});
		}
	});

	// $(function(){
	// 	$('#add_btn, #accept11').tooltip();
	// 	// $("#test").tooltip({
	// 	// 	placement: "left",
	// 	// 	title: "tooltip on left"
	// 	// });
	// });

	// $(document).on('click','input[id=default]',function () {
	// 	alert("Checkbox state (method 1) = " +  $(this).prop('checked'));
	//
	// 	if($(this).prop('checked') == true) {
	// 		alert("Actual Amount = " + $(this).closest('tr').find('td:nth-child(3)').html());
	// 	}
	// });

</script>
