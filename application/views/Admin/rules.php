<?php if($this->session->userdata('id')){ ?>

<!--space-->
<div style="height: 50px"></div>

<!--rules-->
<div class="container-fluid" id="content" style="margin: 0 auto">
    <div class="row" >
		<div class="col-md-3"></div>
		<div class="col-md-6" style="border-radius:11px; padding: 20px;box-shadow: 5px 5px 5px #c9ccc7;
                border: 1px solid #ccc;">

			<h3 style="font-weight: bold">قوانین سایت: </h3><br><br>
			<?php if (isset($rules[0])){ foreach ($rules as $r){ ?>
				<textarea disabled class="form-control" cols="70" rows="30"><?php echo $r->text;?></textarea>
			<?php }}?>
			<br>
			<?php if (isset($rules[0])){ foreach ($rules as $r){ ?>
				<input id="id" type="hidden" value="<?php  echo $r->id;?>">
				<button data-toggle="modal" data-target="#edit-modal" class="btn btn-success">ویرایش</button>
			<?php }}else{?>
				<button data-toggle="modal" data-target="#insert-modal" class="btn btn-success">ایجاد قوانین</button>
			<?php }?>

		</div>
		<div class="col-md-3"></div>
    </div>
</div>

<div class="modal fade" id="edit-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button class="close" data-dismiss="modal" style="float: right">
                    <i class="fa fa-close"></i>
                </button>
                <h4>متن جدید</h4>
            </div>
            <div class="modal-body text-center">
                <textarea class="ckeditor" rows="10" cols="70" name="text2">
					<?php if (isset($rules[0])){ foreach ($rules as $r){ echo $r->text;}}?>
				</textarea>
            </div>
            <div class="modal-footer">
                <button id="edit_rule" class="btn btn-success" data-dismiss="modal">ذخیره</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="insert-modal" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button class="close" data-dismiss="modal" style="float: right">
					<i class="fa fa-close"></i>
				</button>
				<h4>ویرایش</h4>
			</div>
			<div class="modal-body text-center">
				<textarea class="ckeditor" rows="10" cols="70" name="text1"></textarea>
			</div>
			<div class="modal-footer">
				<button id="insert_rule" class="btn btn-success" data-dismiss="modal">ذخیره</button>
			</div>
		</div>
	</div>
</div>
<?php }?>
<script>
	$(document).on('click', '#insert_rule', function(e) {
		var text=CKEDITOR.instances['text1'].getData();
		$.post('<?php  echo base_url();?>admin/insert_rules', {'text1': text},
				function (data) {
					if(data==1){
						$('#text1').val('');
						window.location.href="<?php echo base_url(); ?>admin/rules";
					}
				}
		);
	});
	$(document).on('click', '#edit_rule', function(e){
		var id=$("#id").val();
		var text=CKEDITOR.instances['text2'].getData();
		$.post('<?php echo base_url();?>admin/edit_rules',{'id':id,'text2':text},
				function (data) {
					if (data==1){
						$("#text1").text(text);
						window.location.href="<?php echo base_url(); ?>admin/rules";
					}
				}
		);
	});

</script>
