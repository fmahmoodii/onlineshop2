<?php if($this->session->userdata('id')){ ?>

<div class="container-fluid" id="content">
    <div class="row" style="margin-top: 30px; margin-bottom: 60px">
        <div class="col-md-3"></div>
		<?php foreach ($products as $p) {?>

			<input id="id" type="hidden" value="<?php echo $p->id?>">

			<div class="col-md-6" style="border: 1px solid #ccc; padding: 20px ; border-radius: 10px">
				<label>دسته اول:</label>
					<span style="color: red">مقدار فعلی:(<?php foreach ($category1 as $cat1){if($p->id_cat1==$cat1->id){echo $cat1->name_cat1;}}?>)</span>
				<br>
				<select class="form-control" id="cat1_show">
					<option value="<?php foreach ($category1 as $cat1){if ($p->id_cat1 == $cat1->id){ echo $cat1->id;}}?>">انتخاب کنید..</option>
					<?php foreach ($category1 as $cat1){ ?>
						<option id="cat1_option_1" value="<?php echo $cat1->id;?>"><?php echo $cat1->name_cat1?></option>
					<?php }?>

				</select>
				<br>

				<label>دسته دوم:</label>
					<span style="color: red">مقدار فعلی:(<?php foreach ($category2 as $cat2){if($p->id_cat2==$cat2->id){echo $cat2->name_cat2;}}?>)</span>
					<br>
				<select class="form-control" id="cat2_show">
					<option value="<?php foreach ($category2 as $cat2){if ($p->id_cat2 == $cat2->id){ echo $cat2->id;}}?>">انتخاب کنید..</option>
				</select>
				<br>


				<input disabled type="hidden" class="form-control" value="<?php echo $p->code?>" id="code">
				<br>
				<label>نام محصول:</label>
				<input autocomplete="off" type="text" class="form-control" value="<?php echo $p->name?>" id="name">
				<br>
				<label for="model">مدل محصول:</label>
				<?php if (is_array($aaa))
				{foreach ($aaa as $a){
				  echo $a->idd ;}} ?>
				<select id="model" name="model" multiple="multiple" class="model form-control">
					<?php foreach ($products as $p){foreach ($size as $si){if($p->model==$si->id){
						$arr1[$p->model]=$si->name; $selected_id=$si->id; ?>
					<option value="<?php echo $p->model;?>" selected="selected"><?php echo $arr1[$p->model]?></option>
					<?php }}} ?>
					<?php foreach ($size as $si){if(!($si->id==$selected_id)){ ?>
						<option value="<?php echo $si->id;?>"><?php echo $si->name?></option>
					<?php }}?>


				</select>
				<br>
				<br>
				<button class="btn btn-default" type='button' id='selectAll_model'>Select All</button>
				<button class="btn btn-default" type='button' id='deselectAll_model'>Deselect All</button>
				<br>
				<br>
				<label for="jens">جنس:</label>
				<?php $i=0; foreach ($products as $p){foreach ($size as $si){if($p->jens==$si->id){
					$arr2[$p->jens]=$si->name; $selected_id[$i]=$si->id; $i++; }}
				echo 'arr2['.$p->jens.']='.$arr2[$p->jens] ;} ?>
				<select id="jens" name="jens" multiple="multiple" class="jens form-control">
					<?php foreach ($products as $p){foreach ($size as $si){if($p->jens==$si->id){
						$arr2[$p->jens]=$si->name; $selected_id=$si->id; }}?>
					<option value="<?php echo $p->jens;?>" selected="selected"><?php echo $arr2[$p->jens]?></option>
					<?php } ?>
					<?php foreach ($size as $si){if(!($si->id==$selected_id[$i])){ ?>
						<option value="<?php echo $si->id;?>"><?php echo $si->name?></option>
					<?php }}?>

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
				<input autocomplete="off" type="number" class="form-control" value="<?php echo $p->price?>" id="price">
				<br>
				<label>درصد تخفیف:</label>
				<input autocomplete="off" type="number" class="form-control" value="<?php echo $p->off_percent?>" id="off_percent">
				<br>
				<label>توضیحات:</label>
				<textarea autocomplete="off" class="ckeditor" rows="30" cols="70" name="text" id="text1"><?php echo $p->text?></textarea>
				<br>
					<input autocomplete="off" id="files_image" type="file"><br>
					<div id="image_prd"></div>

					<div style="margin: 20px 0 30px 0">
						<?php foreach ($images as $img){if($img->user_id==$p->code){?>
							<div id="<?php echo $img->id?>" style="border: 1px solid rebeccapurple;border-radius: 10px;padding: 5px;width: 113px;height: 140px;text-align: center">
								<span id="delete_image" id_img="<?php echo $img->id?>" style="color: red;cursor: pointer;margin-bottom: 5px;">حذف</span>
								<img src="<?php echo base_url().$img->direction; ?>" style="width: 100px;height: 100px;">
							</div>
						<?php }}?>
					</div>
				<br>
				<br>
                <button id="edit" class="btn btn-success">ثبت ویرایش</button>
                <a href="<?php echo base_url('admin/products')?>">
                    <button class="btn btn-danger">بستن</button>
                </a>
        </div>
		<?php break; }?>

		<div class="col-md-3"></div>
    </div>

</div>
<?php }?>

<script>
	$(document).on('click', '#edit', function(e){
		var id = $("#id").val();
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
		var m = confirm('آیا از ویرایش محصول اطمینان دارید؟')
		if (m == true) {
			for ($i=0;$i<size.length;$i++) {
				$.post('<?php echo base_url()?>admin/edit_prd', {
						'id': id, 'code': code, 'id_cat1': id_cat1, 'id_cat2': id_cat2,
						'name': name, 'model': model, 'jens': jens, 'size': size[$i], 'brand': brand,
						'price': price, 'off_percent': off_percent, 'text': text
					},
					function (data) {
						if (data.includes(1)) {
							alert('ویرایش محصول انجام شد')
						}
						//window.location.href = "<?php //echo base_url('admin/products/'); ?>//";
						window.location.href = "<?php echo base_url('admin/edit_product/'); ?>" + id + "/" + code;
						//location.reload();
					});
			}
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
					//			$('#image_pf').html("<label class='text-success'>لطفا صبر کنید...</label>");
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

	$(document).ready(function(){


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
