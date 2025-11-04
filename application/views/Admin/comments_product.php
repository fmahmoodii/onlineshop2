<?php if($this->session->userdata('id')){ ?>

	<div class="container-fluid" id="content" style="padding: 20px 0 70px 0">
		<div class="row">
			<div class="col-md-1"></div>

			<div class="col-md-10">
				<br>

				<?php foreach ($comments as $c){if ($c->parent_id==0){ ?>
					<div style="padding: 0px 30px 0px 30px;">
						<div style=" background-color: #fff;">
							<div style="box-shadow: 5px 5px 5px #c9ccc7; border: 1px solid #ccc;
                                        border-radius:11px; padding: 10px;">
								<span style="color: #a1a1a1">نام:</span>
								<span><?php if($c->user_id==''){
										if($c->name!=''){echo $c->name;}
										else{echo 'مهمان';}
									}else{
										if($c->name!=''){echo $c->name;}
										else{echo 'کاربر';}
									} ?>
								</span>
								<span class="pull-left" style="color: #a1a1a1"> <?php echo $c->created;?> </span>
								<span style="margin-left: 10px;background-color: #f2f1f1;font-size: 11px; padding: 6px"
									  class="pull-left">
											<?php echo $c->cond;?>
										</span>
								<br><br>
								<!--								<span style="color: #a1a1a1">نظر:</span>-->
								<span><?php echo $c->text;?></span>

								<button style="margin-left: 10px;outline: unset;"
										id="ans"
										id_cmnt="<?php echo $c->id?>"
										class="pull-left btn btn-sm ml-3 btn-info ">پاسخ</button>
								<br>
								<br>

								<?php foreach($comments2 as $c2){if ($c2->parent_id!=0){
									if($c2->parent_id==$c->id){?>
										<div style="padding: 0 20px 20px 20px;margin-right: 30px; background-color: #fdfdde;border-radius:11px; ">
											<hr>
											<span style="color: #a1a1a1">کاربر:</span>
											<span><?php if($c2->user_id==''){
													if($c2->name!=''){echo $c2->name;}
													else{echo 'مهمان';}
												}else{
													if($c2->name!=''){echo $c2->name;}
													else{echo 'کاربر';}
												} ?>
											</span>
											<span class="pull-left" style="color: #a1a1a1"> <?php echo $c2->created;?> </span>
											<span style="margin-left: 10px;background-color: #f2f1f1;font-size: 11px; padding: 6px"
												  class="pull-left">
											<?php echo $c2->cond;?>
										</span>
											<br><br>
											<!--									<span style="color: #a1a1a1">نظر:</span>-->
											<span><?php echo $c2->text;?></span>
											<br>
										</div>
									<?php }
								}}?>
								<br>
							</div>

							<br>
						</div>
					</div>
				<?php }}?>


				<br>
			</div>

			<div class="col-md-1"></div>

		</div>

	</div>

	<div class="modal fade" id="ans_modal" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content " >
				<div class="modal-header text-center" style="background-color: #fff" >
					<button style="float: right;" type="button" class="close" data-dismiss="modal">
						<i class="fa fa-close"></i>
					</button>
					<h4 class="modal-title">پاسخ</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="code2"  value="<?php echo $code ?>">
					<input type="hidden" id="parent_modal"  value="">
					<label>نام و نام خانوادگی</label>
					<input id="name2" class="form-control" type="text" placeholder="نام و نام خانوادگی"
						   user_id="<?php if($this->session->userdata('user_id')){
							   foreach ($profile as $prof){if($prof->user_id==$this->session->userdata('user_id')){
								   echo $prof->user_id; }}} ?>"
						   value="<?php if($this->session->userdata('user_id')){
							   foreach ($profile as $prof){if($prof->user_id==$this->session->userdata('user_id')){
								   echo $prof->name.' '.$prof->family; }else{echo '';}}}?>"
					>
					<br>
					<label>متن پاسخ</label>
					<textarea id="text2" class="form-control" type="text" rows="10"  name="text2"></textarea><br><br>
				</div>
				<div class="modal-footer">
					<button style="float: right;outline: unset" id="accept2" class="btn btn-default" data-dismiss="modal"  >تایید و ارسال</button>
				</div>
			</div>
		</div>
	</div>

<?php }?>

<script>

	//------نمایش مودال---------
	$(document).on('click', '#ans', function(e){
		var id=$(this).attr("id_cmnt");
		$("#parent_modal").val(id);
		$("#ans_modal").modal();
	});
	//---------عملیات ویرایش-----------
	$(document).on('click', '#accept2', function(e) {
		var code=$('#code2').val();
		var user_id=$('#name2').attr('user_id');
		var parent_id=$('#parent_modal').val();
		console.log(parent_id);
		var name=$('#name2').val();
		var text=$('#text2').val();
		$.post('<?php  echo base_url();?>home/insert_comment', {'code':code,'name': name,'text': text,'user_id': user_id,'parent_id': parent_id,},
			function (data) {
				if(data==1){
					console.log(data);
					$('#name2').val('');
					$('#text2').val('');
					window.location.reload();
				}
			}
		);
	});

	//------نمایش مودال---------
	$(document).on('click', '#edit', function(e){
		var id=$(this).attr("id_cmnt");
		var name=$(this).attr("name_value");
		var text=$(this).attr("text_value");
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
					$("#name_"+id).text(name);
					$("#text_"+id).text(text);
					window.location.reload();
				}
			}
		);
	});
</script>
