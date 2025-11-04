<?php if($this->session->userdata('id')){ ?>


<div class="container-fluid" id="content">
    <div class="row" style="margin-top: 30px; margin-bottom: 60px">
        <div class="col-md-3"></div>
        <div class="col-md-6" style="border: 1px solid #ccc; padding: 20px ; border-radius: 10px">


				<input autocomplete="off" class="form-control" disabled type="hidden" id="code" value="<?php echo microtime(true).rand(1111,9999); ?>"><br>

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
				<label>مدل محصول:</label>
				<input autocomplete="off" id="model" type="text" class="form-control" placeholder="مدل محصول">
				<br>
                <label>جنس:</label>
                <input autocomplete="off" id="jens" type="text" class="form-control" placeholder="جنس محصول">
                <br>
                <label>سایز:</label>
                <input autocomplete="off" id="size" type="text" class="form-control" placeholder="سایز محصول">
                <br>
                <label>برند:</label>
                <input autocomplete="off" id="brand" type="text" class="form-control" placeholder="برند محصول">
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

</div>
<?php }?>

<script>

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
		if (id_cat1 != "") {
	 	$.post('<?php echo base_url()?>admin/insert_prd',{'code':code, 'id_cat1':id_cat1, 'id_cat2':id_cat2,
		 'name':name, 'model':model, 'jens':jens, 'size':size, 'brand':brand, 'price':price,'off_percent':off_percent,'text':text},
				function (data) {
						if (data == 1) {
							alert('ذخیره با موفقیت انجام شد');
							$("#cat1_show").val('');
							$("#cat2_show").val('');
							$("#name").val('');
							$("#model").val('');
							$("#jens").val('');
							$("#size").val('');
							$("#brand").val('');
							$("#price").val('');
							$("#off_percent").val('');
							$("#text1").val('');
							window.location.href = "<?php echo base_url(); ?>admin/insert_product";
							// window.location.reload();
						}
					});
	}else {
		alert('انتخاب دسته الزامی است');
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
					$('#image_prd').html(data);
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
						$("#show_image_"+id_img).hide();
						$("#show_image_"+id_img).remove();
					}
				}
		);
	});








</script>
