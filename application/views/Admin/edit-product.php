<style>
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

	<div class="container-fluid" id="content">

		<?php foreach ($products as $p) {?>

			<input id="id" type="hidden" value="<?php echo $p->id?>">
<!--			<input id="code" type="hidden" value="--><?php //echo $p->code?><!--">-->

		<div class="row" style="margin: 60px">

				<div id="loading-image" style="display: none"></div>

				<div style="border: 1px solid #ccc; padding: 20px ; border-radius: 10px; ">



				<input autocomplete="off" class="form-control" disabled id="code" value="<?php echo $p->code?>"><br>

				<label>دسته اول:</label>
<!--				<span style="color: red">مقدار فعلی:(--><?php //foreach ($category1 as $cat1){if($p->id_cat1==$cat1->id){echo $cat1->name_cat1;}}?><!--)</span>-->
<!--				<br>-->
				<select class="form-control" id="cat1_show">
					<option value="<?php foreach ($category1 as $cat1){if ($p->id_cat1 == $cat1->id){
						$a=$p->id_cat1;
						$b=$cat1->id;
						$c=$cat1->name_cat1;
						}}?>">
						<?php echo 'انتخاب کنید..'; ?>
					</option>
					<?php foreach ($category1 as $cat1){ ?>
						<option value="<?php echo $cat1->id;?>" <?php if(isset($b[0]) && $cat1->id==$b){echo 'selected';} ?> >
							<?php echo $cat1->name_cat1?>
						</option>
					<?php }?>

				</select>
				<br>

				<label>دسته دوم:</label>
				<span id="d" style="display: none"><?php foreach ($category2 as $cat2){if($p->id_cat2==$cat2->id){echo $cat2->id;}}?></span>
				<br>
				<select class="form-control" id="cat2_show">
					<option value="<?php foreach ($category2 as $cat2){if ($p->id_cat2 == $cat2->id){
						$a2=$p->id_cat2;
						$b2=$cat2->id;
						$c2=$cat2->name_cat2;
						}}?>"
						<?php echo 'انتخاب کنید..'; ?>>
					</option>
				</select>
				<br>

				<label>نام محصول:</label>
				<input id="name" autocomplete="off" type="text" class="form-control" value="<?php echo $p->name?>" >
				<br>
				<br>
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

				<div class="row" id="show_item">

					<button id="add_row" class="btn btn-success add_item_btn" type="button">
						<i class="fa fa-plus"></i>
					</button>
					<select class="form-control model" id="model" style="width: 12%; display: inline">
						<option value="0" disabled selected hidden>مدل محصول</option>
						<option value="">خالی</option>
						<?php foreach ($model as $mo){ ?>
							<option value="<?php echo $mo->id;?>"><?php echo $mo->name?></option>
						<?php }?>

					</select>
					<select class="form-control jens" id="jens" style="width: 12%; display: inline">
						<option value="0" disabled selected hidden>جنس محصول</option>
						<option value="">خالی</option>
						<?php foreach ($jens as $je){ ?>
							<option value="<?php echo $je->id;?>"><?php echo $je->name?></option>
						<?php }?>

					</select>
					<select class="form-control size" id="size" style="width: 12%; display: inline">
						<option value="0" disabled selected hidden>سایز محصول</option>
						<option value="">خالی</option>
						<?php foreach ($size as $si){ ?>
							<option value="<?php echo $si->id;?>"><?php echo $si->name?></option>
						<?php }?>

					</select>
					<select class="form-control brand" id="brand" style="width: 12%; display: inline">
						<option value="0" disabled selected hidden>برند محصول</option>
						<option value="">خالی</option>
						<?php foreach ($brand as $br){ ?>
							<option value="<?php echo $br->id;?>"><?php echo $br->name?></option>
						<?php }?>

					</select>
					<select class="form-control color" id="color" style="width: 12%; display: inline">
						<option value="0" disabled selected hidden>رنگ محصول</option>
						<option value="">خالی</option>
						<?php foreach ($color as $co){ ?>
							<option value="<?php echo $co->id;?>" color_code="<?php echo $co->color_code ?>" style="background-color: <?php echo $co->color_code ?>"><?php echo $co->name?></option>
						<?php }?>

					</select>

					<label class="required"></label>
					<input style="width: 12%; display: inline" autocomplete="off" id="price" min="0" type="number" class="form-control positive" placeholder="قیمت">

					<input style="width: 8%; display: inline" autocomplete="off" id="off_percent" min="0" type="number" class="form-control positive" placeholder="درصد تخفیف">
					<input style="width: 12%; display: inline" autocomplete="off" id="supply" min="0" type="number" class="form-control positive" placeholder="موجودی">
					<!--								<input id="default_row" type="radio">-->

				</div>
				<br>
				<div class="row" id="results" style="display: <?php if(isset($y[0])) { echo 'block';}else{echo 'none';} ?>">
					<table class="table table-bordered table-striped" id="attr-tb">
						<thead id="t-head">

						<tr id="h-tr">
							<th class="hide_div"></th>
							<th class="model">model</th>
							<th class="jens">jens</th>
							<th class="size">size</th>
							<th class="brand">brand</th>
							<th class="color">color</th>
							<th>price</th>
							<th>off_percent</th>
							<th>supply</th>

							<th>isActive</th>

							<th class="hide_div">default_row</th>
						</tr>

						</thead>
						<tbody id="t-body" >
						<?php foreach ($x as $row) {if ($row->id!=''){ ?>
							<tr id="row_<?php echo $row->id ?>" id_row="<?php echo $row->id ?>">
								<td>
									<button id="del_row" id_row="<?php echo $row->id ?>" class="btn btn-danger" type="button">
										<i class="fa fa-minus"></i>
									</button>
									<button id="edit_row" id_row="<?php echo $row->id ?>" class="btn btn-primary" type="button">
										<i class="fa fa-edit"></i>
									</button>
								</td>
								<td class="model model_value" id="model_<?php echo $row->id ?>"option_id="<?php foreach($model as $mo){if($mo->id==$row->model) echo $mo->id;}?>"><?php foreach($model as $mo){if($mo->id==$row->model) echo $mo->name;}?></td>
								<td class="jens jens_value" id="jens_<?php echo $row->id ?>" option_id="<?php foreach($jens as $je){if($je->id==$row->jens) echo $je->id;}?>"><?php foreach($jens as $je){if($je->id==$row->jens) echo $je->name;}?></td>
								<td class="size size_value" id="size_<?php echo $row->id ?>" option_id="<?php foreach($size as $si){if($si->id==$row->size) echo $si->id;}?>"><?php foreach($size as $si){if($si->id==$row->size) echo $si->name;}?></td>
								<td class="brand brand_value" id="brand_<?php echo $row->id ?>" option_id="<?php foreach($brand as $br){if($br->id==$row->brand) echo $br->id;}?>"><?php foreach($brand as $br){if($br->id==$row->brand) echo $br->name;}?></td>
<!--								<td class="color color_value" style="background-color: --><?php //foreach($color as $co){if($co->id==$row->color) echo $co->color_code;}?><!--" id="color_--><?php //echo $row->id ?><!--" option_id="--><?php //foreach($color as $co){if($co->id==$row->color) echo $co->id;}?><!--">--><?php //foreach($color as $co){if($co->id==$row->color) echo $co->name;}?><!--</td>-->
								<td class="color color_value" id="color_<?php echo $row->id ?>" option_id="<?php foreach($color as $co){if($co->id==$row->color) echo $co->id;}?>">
									<span class="color_value2" style="height: 16px;width: 16px;border:1px solid black;border-radius: 50%;display: <?php if($row->color=='0') {echo 'none';}else{echo 'inline-block';}?> ;background:<?php foreach($color as $co){if($co->id==$row->color) echo $co->color_code;}?>;"></span>
									<span class="color_value2"><?php foreach($color as $co){if($co->id==$row->color) echo $co->name;}?></span>
								</td>
								<td id="price_<?php echo $row->id ?>" ><?php echo number_format($row->price);?></td>
								<td id="off_percent_<?php echo $row->id ?>" ><?php if($row->off_percent!=0){echo $row->off_percent; echo '%';}?></td>
								<td id="supply_<?php echo $row->id ?>" ><?php echo $row->supply;?></td>
								<td>
									<input name="isActive" id="isActive" id_row="<?php echo $row->id ?>" type="checkbox" <?php if($row->isActive==1){echo 'checked';} ?> >
								</td>
								<td>
									<input name="default" id="default" id_row="<?php echo $row->id ?>" type="radio" <?php if($row->default==1){echo 'checked';} ?> >
								</td>
							</tr>
						<?php }} ?>
						</tbody>
					</table>


				</div>


<!--				<button id="add_btn" class="btn btn-primary w-25" type="button"-->
<!--						data-toggle="tooltip" data-placement="left" title=""-->
<!--						data-original-title="ثبت ویژگی های محصول">add</button>-->





			</div>

				<br><br><br>

				<label>توضیحات:</label>

				<textarea id="text1" class="ckeditor" rows="30" cols="70" name="text1">
					<?php echo $p->text ?>
				</textarea>
				<br>

				<div style="width: 100%">
					<!------upload_img--------->
					<input class="form-control" autocomplete="off" id="files_image" type="file" multiple><br>

					<div style="flex-wrap: wrap;padding: 20px 20px 10px 20px;" id="image_prd"><?php foreach ($images as $img){if($img->user_id==$p->code){?><div id="show_image_<?php echo $img->id?>" style="border: 1px solid rebeccapurple;border-radius: 10px;padding: 5px;width: 113px;height: 140px;text-align: center;margin: 0 0 10px 10px;background-color: #fff;"><span id="delete_image" id_img="<?php echo $img->id?>" style="color: red;cursor: pointer;margin-bottom: 5px;">حذف</span><img src="<?php echo base_url().$img->direction; ?>" style="width: 100px;height: 100px;border: 1px solid #000;"></div><?php }}?></div>

				</div>
				<br><br>
				<button id="accept11" class="btn btn-success"
						type="button"
						data-toggle="tooltip" data-placement="left" title=""
						data-original-title="ثبت محصول">ثبت محصول</button>
			</div>
		<?php }?>


		<div id="snackbar">انجام شد</div>
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
						<lable for="model" class="model">مدل</lable>
						<select class="form-control model" id="model_modal" >
							<option value="0" disabled selected hidden>مدل محصول</option>
							<option value="">خالی</option>
							<?php foreach ($model as $mo){ ?>
								<option value="<?php echo $mo->id;?>"><?php echo $mo->name?></option>
							<?php } ?>

						</select>

						<lable for="jens" class="jens">جنس</lable>
						<select class="form-control jens" id="jens_modal" >
							<option value="0" disabled selected hidden>جنس محصول</option>
							<option value="">خالی</option>
							<?php foreach ($jens as $je){ ?>
								<option value="<?php echo $je->id;?>"><?php echo $je->name?></option>
							<?php }?>

						</select>

						<lable for="size" class="size">سایز</lable>
						<select class="form-control size" id="size_modal">
							<option value="0" disabled selected hidden>سایز محصول</option>
							<option value="">خالی</option>
							<?php foreach ($size as $si){ ?>
								<option value="<?php echo $si->id;?>"><?php echo $si->name?></option>
							<?php }?>

						</select>

						<lable for="brand" class="brand">برند</lable>
						<select class="form-control brand" id="brand_modal" >
							<option value="0" disabled selected hidden>برند محصول</option>
							<option value="">خالی</option>
							<?php foreach ($brand as $br){ ?>
								<option value="<?php echo $br->id;?>"><?php echo $br->name?></option>
							<?php }?>

						</select>

						<lable for="color" class="color">رنگ</lable>
						<select class="form-control color" id="color_modal" name="color">
							<option value="0" disabled selected hidden>رنگ محصول</option>
							<option value="">خالی</option>
							<?php foreach ($color as $co){ ?>
								<option value="<?php echo $co->id;?>" color_code="<?php echo $co->color_code ?>" style="background-color: <?php echo $co->color_code ?>"><?php echo $co->name?></option>
							<?php }?>

						</select>
						<label class="required" for="price">قیمت</label>
						<input  autocomplete="off" id="price_modal" min="0" type="number" class="form-control positive" placeholder="قیمت">
						<label for="off_percent">درصد تخفیف</label>
						<input  autocomplete="off" id="off_percent_modal" min="0" type="number" class="form-control positive" placeholder="درصد تخفیف">
						<label for="supply">موجودی</label>
						<input  autocomplete="off" id="supply_modal" min="0" type="number" class="form-control positive" placeholder="موجودی">
						<br>


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

<?php }?>


<script>
	$(document).on('click', '#accept11', function(e){
		var id = $("#id").val();
		var code=$("#code").val();
		var id_cat1=$("#cat1_show").val();
		var id_cat2=$("#cat2_show").val();
		var name=$("#name").val();

		var text=CKEDITOR.instances['text1'].getData();
		var m = confirm('آیا از ویرایش محصول اطمینان دارید؟');
		if (m == true) {

				$.post('<?php echo base_url()?>admin/edit_prd', {
						'id': id, 'code': code, 'id_cat1': id_cat1, 'id_cat2': id_cat2,
						'name': name, 'text': text
					},
					function (data) {
						if (data.includes(1)) {
							// Get the snackbar DIV
							var x = document.getElementById("snackbar");

							// Add the "show" class to DIV
							x.className = "show";

							// After 3 seconds, remove the show class from DIV
							setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
							// alert('ویرایش محصول انجام شد')
						}
						//window.location.href = "<?php //echo base_url('admin/products/'); ?>//";
						//location.reload();

						// reload to controller function with input
						//window.location.href = "<?php //echo base_url('admin/edit_product'); ?>//"  + "/" + code;
					});

		}
		if (m == false){
			return
		}

	});


	//---------get_category-----------
	$('#cat1_show').change(function(){
		id_cat1=$(this).val();
		$.post('<?php echo base_url();?>admin/get_category2',{'id_cat1':id_cat1},
				function(data){
					$('#cat2_show').html(data);
				});
	});


	$(document).on('click', '#delete_image', function(e){
		$("#loading-image").show();
		id_img=$(this).attr("id_img");
		image_prd = $('#image_prd').text();
		console.log(id_img);
		$.post('<?php echo base_url();?>admin/delete_img',{'id':id_img,},
				function (data) {
					if (data==1){
						$("#loading-image").hide();
						$("#show_image_"+id_img).remove();
					}else{
						$("#loading-image").hide();
					}
				}
		);
	});

	$('#files_image').change(function(){
		$("#loading-image").show();
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
					//			$('#image_pf').html("<label class='text-success'>لطفا صبر کنید...</label>");
				},
				success:function(data)
				{
					$("#loading-image").hide();
					$('#image_prd').append(data);
					$('#files_image').val('');

				}
			})
		}
		else
		{
			$("#loading-image").hide();
			alert('بیش 5 عکس مجاز نیستید');
		}
	});



	$(document).on('click', '#add_row', function(e){
		$("#loading-image").show();

		var code = $('#code').val();
		var model = $('#model').val();
		var jens=$("#jens").val();
		var size=$("#size").val();
		var brand=$("#brand").val();
		var color=$("#color").val();
		var color_code=$("#color").find(':selected').attr('color_code');
		console.log(color_code);
		var supply=$("#supply").val();
		var price = $('#price').val();
		var off_percent = $('#off_percent').val();

		var default_row = '1';
		var i=0;

		$('input[id=default]').each(function () {
			if($(this).prop('checked') == true){
				default_row = '0';
				i++;
			}
		});

		if(price != '') {
			$.ajax({
				url:"<?php echo base_url()?>admin/add_attr_editProducts",
				method:"POST",
				data:{'code': code,
					'model': model,
					'jens': jens,
					'size': size,
					'brand': brand,
					'color': color,
					'price': price,
					'off_percent': off_percent,
					'supply': supply,

				},

				success:function(data)
				{
					// console.log(data);
					var obj = JSON.parse(data);


					$.ajax({
						url: "<?php echo base_url()?>admin/hhh",
						method: "POST",
						data: {
							'model': model,
							'jens': jens,
							'size': size,
							'brand': brand,
							'color': color,
							'price': price,
							'default_row': default_row,

						},
						success: function (data2) {
							// console.log(data2);
							var obj2 = JSON.parse(data2);
							$('#t-body').prepend(
								'<tr>' +
								'<td>' +
								'<button id_row=' + obj.id + ' id="del_row" class="btn btn-danger" type="button">' +
								'<i class="fa fa-minus"></i>' +
								'</button>' +
								'<button id_row=' + obj.id + ' id="edit_row" class="btn btn-primary" type="button">' +
								'<i class="fa fa-edit"></i>' +
								'</button>' +
								'</td>' +
								'<td class="model model_value" id=model_' + obj.id + ' option_id=' + obj.model + ' >' + obj2.model + '</td>' +
								'<td class="jens jens_value" id=jens_' + obj.id + ' option_id=' + obj.jens + ' >' + obj2.jens + '</td>' +
								'<td class="size size_value" id=size_' + obj.id + ' option_id=' + obj.size + ' >' + obj2.size + '</td>' +
								'<td class="brand brand_value" id=brand_' + obj.id + ' option_id=' + obj.brand + ' >' + obj2.brand + '</td>' +
								// '<td class="color color_value" style="background-color: ' + obj2.color + '" id=color_' + obj.id + ' option_id=' + ((typeof obj2.color == 'undefined') ? "" : "") + ' >' + obj2.color + '</td>' +
								'<td class="color color_value" id=color_' + obj.id + ' option_id=' + obj.color + ' >' +
								'<span class="color_value2" style="height: 16px;width: 16px;border:1px solid black;border-radius: 50%;display:'+((typeof color_code == 'undefined')?'none':'inline-block')+';background-color:' + ((typeof color_code == 'undefined') ? "" : color_code) + '; "></span>' +
								'<span class="color_value2">' + obj2.color + '</span>' +
								'</td>' +
								'<td id=price_' + obj.id + ' >' + parseInt(obj.price).toLocaleString() + '</td>' +
								'<td id=off_percent_' + obj.id + ' >' + (obj.off_percent != "" ? obj.off_percent + "%" : "") + '</td>' +
								'<td id=supply_' + obj.id + ' >' + (obj.supply == "" ? '0' : obj.supply) + '</td>' +
								'<td>' +
								'<input name="isActive" id="isActive" id_row="' + obj.id + '" type="checkbox" '+ (obj.isActive == '1' ? 'checked' : '') +' >' +
								'</td>' +
								'<td>' +
								'<input name="default" id="default" id_row="' + obj.id + '" type="radio" >' +
								'</td>' +
								'</tr>'
							)

							if($('.model').css('display')=='none') {
								if($('.model_value').css({'display':'none'}));
							}
							if($('.jens').css('display')=='none') {
								if($('.jens_value').css({'display':'none'}));
							}
							if($('.size').css('display')=='none') {
								if($('.size_value').css({'display':'none'}));
							}
							if($('.brand').css('display')=='none') {
								if($('.brand_value').css({'display':'none'}));
							}
							if($('.color').css('display')=='none') {
								if($('.color_value').css({'display':'none'}));
							}

								if(i<1){
									// $('input[id=default]').addAttribution('checked');
									$('input[id=default]').prop('checked',true);
									$.ajax({
										url: "<?php echo base_url()?>/admin/default_attr",
										method: "POST",
										data: {
											'code': code,
											'id': obj.id,
											'isActive': 1,
											'default': 1,

										},
										success: function () {

										}

									});
								}

							$('#results').css({'display':'block'});
							$('#model').val('0');
							$('#jens').val('0');
							$('#size').val('0');
							$('#brand').val('0');
							$('#color').val('0');
							$('#price').val('');
							$('#off_percent').val('');
							$('#supply').val('');

							$("#loading-image").hide();
						}
					});
				}
			});

		}
		else{
			$("#loading-image").hide();
			alert("قیمت محصول را وارد کنید");
		}
	});


	$(document).on('click', '#del_row', function(e){
		$("#loading-image").show();
		if($("#attr-tb tr").length>2 && $(this).closest('tr').find('input[id=default]').prop('checked')){
		// 	if($(this).closest('tr').find('input[id=default]').prop('checked')){
			alert('ردیف دیفالت قابل حذف نمی باشد. ردیف دیفالت را تغییر دهید');
			$("#loading-image").hide();
		}

		if($("#attr-tb tr").length<=2 || !$(this).closest('tr').find('input[id=default]').prop('checked')){
			var id = $(this).attr('id_row');
			var code = $("#code").val();
			$.post('<?php echo base_url()?>admin/del_product_attr', {
					'id': id,
					'code': code,
				},
				function (data) {
					console.log('success');
					$('#row_' + id).remove();
					$("#loading-image").hide();

					if (data < 1) {
						$("#loading-image").show();
						// $('input[id=default]').addAttribution('checked');
						$('input[id=default]').prop('checked', true);
						var id2 = $('input[id=default]:checked').attr('id_row');
						$.ajax({
							url: "<?php echo base_url()?>/admin/default_attr",
							method: "POST",
							data: {
								'code': code,
								'id': id2,
								'isActive': 1,
								'default': 1,

							},
							success: function () {
								$("#loading-image").hide();
							}

						});
					}
				});
		}
		$("#loading-image").hide();
	});

	$(document).on('change', 'input[id=default]', function(){
		$("#loading-image").show();

		var code=$("#code").val();
		var id = $(this).attr("id_row");
		$('input[id=default]').removeAttr('checked');
		$(this).closest('tr').find('input[id=isActive]').prop('checked',true);
			$.ajax({
				url: "<?php echo base_url()?>/admin/default_attr",
				method: "POST",
				data: {
					'code': code,
					'id': id,
					'isActive': 1,
					'default': 1,
				},
				success: function () {
					$("#loading-image").hide();

				}

			});

	});

	$(document).on('click', 'input[id=isActive]', function(){
		$("#loading-image").show();

		var code=$("#code").val();
		var id = $(this).attr("id_row");
		if ( $(this).attr("id_row")==id && $(this).is(':checked') ) {
			var isActive = '1';
		}
		if ( $(this).attr("id_row")==id && !$(this).is(':checked') ) {
			if( $('input[id=default]:checked').attr("id_row")==id ){
				var isActive = '1';
				$(this).prop('checked',true);
				alert('ردیف دیفالت غیرفعال نمی شود');
			}else{
				var isActive = '0';
			}
		}

		$.ajax({
			url: "<?php echo base_url()?>/admin/isActive_attr",
			method: "POST",
			data: {
				'code': code,
				'id': id,
				'isActive': isActive,
			},
			success: function (data) {
				if(data==1){
					console.log('success');
					$("#loading-image").hide();
				}
			}

		});
	});

	$(document).ready(function(){
		var id_cat1=$('#cat1_show').val();
		$.post('<?php echo base_url();?>admin/get_category2',{'id_cat1':id_cat1},
			function(data){
				$('#cat2_show').html(data);
				var d=$('#d').text();
				$("#cat2_show option[value="+d+"]").attr("selected", true);

			});

		// $('#add_btn').attr("disabled",true);
		// $('#accept11').attr("disabled",true);
		$('.model').css({'display':'none'});
		$('.jens').css({'display':'none'});
		$('.size').css({'display':'none'});
		$('.brand').css({'display':'none'});
		$('.color').css({'display':'none'});

		$('.model_value').each(function () {
			if($(this).html()!=''){
				$('#model_checkbox').prop('checked',true);
				$('.model').css({'display':'revert'});
			}

		});
		$('.jens_value').each(function () {
			if($(this).html()!=''){
				$('#jens_checkbox').prop('checked',true);
				$('.jens').css({'display':'revert'});
			}

		});
		$('.size_value').each(function () {
			if($(this).html()!=''){
				$('#size_checkbox').prop('checked',true);
				$('.size').css({'display':'revert'});
			}

		});
		$('.brand_value').each(function () {
			if($(this).html()!=''){
				$('#brand_checkbox').prop('checked',true);
				$('.brand').css({'display':'revert'});
			}

		});
		$('.color_value2').each(function () {

			if($(this).html()!=''){
				$('#color_checkbox').prop('checked',true);
				$('.color').css({'display':'revert'});
				console.log($('.color_value2').html());
			}
		});



	});

	$(document).on('change', '#price,#off_percent,#supply', function(){
		var num = $(this).val();
		if(num<0){
			$(this).val(0);
		}
	});

	$(document).on('change', '#model_checkbox,#jens_checkbox,#size_checkbox,#brand_checkbox,#color_checkbox', function(){
		var x = $(this).attr('id');
		var y = x.slice(0,-9);
		// var z = x.slice(0,-8);
		// var c = z+'cells';
		console.log(x);
		console.log(y);
		// console.log(c);
		if( $(this).prop('checked') ){
			$('.'+y).css({'display':'revert'});
			// $('.'+c).css({'display':'revert'});
		}
		if(! $(this).prop('checked') ){
			$('.'+y).css({'display':'none'});
			// $('.'+c).css({'display':'none'});
		}
	});


	//------نمایش مودال---------//
	$(document).on('click', '#edit_row', function (e) {
		$("#loading-image").show();
		var id = $(this).attr("id_row");
		$("#id_modal").val(id);


		var model_option_id = $("#model_" + id).attr('option_id');
		var jens_option_id = $("#jens_" + id).attr('option_id');
		var size_option_id = $("#size_" + id).attr('option_id');
		var brand_option_id = $("#brand_" + id).attr('option_id');
		var color_option_id = $("#color_" + id).attr('option_id');
		var price = $("#price_" + id).text().replace(/,/g,'');
		var off_percent = $("#off_percent_" + id).text().replace(/%/g,'');
		var supply = $("#supply_" + id).text().replace(/,/g,'');
		$("#price_modal").val(price);
		$("#off_percent_modal").val(off_percent);
		$("#supply_modal").val(supply);


		$("#show_edit").modal();
		$("#loading-image").hide();

		if(model_option_id != ''){
			$('#model_modal option[value=' + model_option_id + ']').prop('selected', true);
		} else if(model_option_id == ''){
			$('#model_modal option[value= 0]').prop('selected', true);
		}
		if(jens_option_id != ''){
			$('#jens_modal option[value=' + jens_option_id + ']').prop('selected', true);
		} else if(jens_option_id == ''){
			$('#jens_modal option[value= 0]').prop('selected', true);
		}
		if(size_option_id != ''){
			$('#size_modal option[value=' + size_option_id + ']').prop('selected', true);
		} else if(size_option_id == ''){
			$('#size_modal option[value= 0]').prop('selected', true);
		}
		if(brand_option_id != ''){
			$('#brand_modal option[value=' + brand_option_id + ']').prop('selected', true);
		} else if(model_option_id == ''){
			$('#brand_modal option[value= 0]').prop('selected', true);
		}
		if(color_option_id != ''){
			$('#color_modal option[value=' + color_option_id + ']').prop('selected', true);
		} else if(color_option_id == ''){
			$('#color_modal option[value= 0]').prop('selected', true);
		}




	});
	//---------عملیات ویرایش-----------
	$(document).on('click', '#btn_edit', function (e) {

		$("#loading-image").show();

		var id = $("#id_modal").val();
		var model = $("#model_modal").val();
		var jens = $("#jens_modal").val();
		var size = $("#size_modal").val();
		var brand = $("#brand_modal").val();
		var color = $("#color_modal").val();
		var color_code2 = $("#color_modal").find(':selected').attr('color_code');
		var price = $("#price_modal").val();
		var off_percent = $("#off_percent_modal").val();
		var supply = $("#supply_modal").val();

		//console.log(model+jens+size+brand+color);

		$.post('<?php echo base_url();?>admin/edit_attr_editProducts',
			{
				'id': id,
				'model': model,
				'jens': jens,
				'size': size,
				'brand': brand,
				'color': color,
				'price': price,
				'off_percent': off_percent,
				'supply': supply,
			},
			function (data) {

				var model = $("#model_modal").val();
				var jens = $("#jens_modal").val();
				var size = $("#size_modal").val();
				var brand = $("#brand_modal").val();
				var color = $("#color_modal").val();
				$.post('<?php echo base_url();?>admin/hhh',
					{
						'model': model,
						'jens': jens,
						'size': size,
						'brand': brand,
						'color': color,
						'price': price,

					},
					function (data2) {
						var obj = JSON.parse(data2);
						// console.log(obj.model,obj.jens,obj.size,obj.brand,obj.color);
						$('#model_'+id).text(obj.model);
						$("#model_" + id).attr('option_id',model);
						$('#jens_'+id).text(obj.jens);
						$("#jens_" + id).attr('option_id',jens);
						$('#size_'+id).text(obj.size);
						$("#size_" + id).attr('option_id',size);
						$('#brand_'+id).text(obj.brand);
						$("#brand_" + id).attr('option_id',brand);
						$("#color_" + id).attr('option_id',color);
						$("#color_" + id).html('<span style="height: 16px;width: 16px;border:1px solid black;border-radius: 50%;display: '+((typeof color_code2 == 'undefined')?'none':'inline-block')+';background-color:' + ((typeof color_code2 == 'undefined') ? "" : color_code2) + ';"></span>' +
							'<span>'+obj.color+'</span>');
						$('#price_'+id).text(parseInt(price).toLocaleString());
						$('#off_percent_'+id).text(off_percent+(off_percent==""?'':'%'));
						$('#supply_'+id).text(supply);

						$('#loading-image').hide();

					});



			});
	});





	//$(document).on('click', 'input[id=default]', function(){
	//	var id = $(this).attr("id_row");
	//
	//	if($('input[id=default]:checked').length>1){
	//		$(this).prop('checked',false);
	//	}else if($('input[id=default]:checked').length<=1){
	//		if($('input[id=default]').attr("id_row")==id && $(this).prop('checked',false)){
	//			$.ajax({
	//				url: "<?php //echo base_url()?>//admin/default_attr",
	//				method: "POST",
	//				data: {
	//					'id': id,
	//					'isActive': '1',
	//					'default': '1',
	//
	//				},
	//				success: function () {
	//					if($('input[id=default]').attr("id_row")==id) {
	//						$('input[id=default]').prop('checked',true);
	//					}
	//				}
	//			});
	//		}
	//		if($('input[id=default]').attr("id_row")==id &&($(this).prop('checked',true)){
	//
	//			$.ajax({
	//				url: "<?php //echo base_url()?>//admin/default_attr",
	//				method: "POST",
	//				data: {
	//					'id': id,
	//					'default': '0',
	//
	//				},
	//				success: function () {
	//					if($('input[id=default]').attr("id_row")==id) {
	//						$('input[id=default]').prop('checked',false);
	//					}
	//				}
	//			});
	//		}
	//
	//	}
	//});







	// $('#model_modal').on("change", function(){
	// 	$('option:selected', this).hide().siblings().show();
	// }).trigger('change');



</script>
