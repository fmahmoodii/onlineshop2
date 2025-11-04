<?php if($this->session->userdata('id')){ ?>

	<div class="container-fluid" id="content" style="padding: 20px 0 70px 0">
		<div class="row">
			<div class="col-md-1"></div>

			<div class="col-md-10">
				<br>
				<?php if(isset($comments1[0])){foreach ($comments1 as $c1){if($c1->user_id==$uid && $c1->parent_id==0){  ?>
					<div style="padding: 0px 30px 0px 30px;">
						<div style=" background-color: #fff;">
							<div style="box-shadow: 5px 5px 5px #c9ccc7; border: 1px solid #ccc;
								border-radius:11px; padding: 10px;">
								<span style="color: #a1a1a1">نام:</span>
								<span><?php if($c1->user_id==''){
										if($c1->name!=''){echo $c1->name;}
										else{echo 'مهمان';}
									}else{
										if($c1->name!=''){echo $c1->name;}
										else{echo 'کاربر';}
									} ?>
						</span>
								<span class="pull-left" style="color: #a1a1a1"> <?php echo $c1->created;?> </span>
								<span style="margin-left: 10px;background-color: #f2f1f1;font-size: 11px; padding: 6px"
									  class="pull-left">
											<?php echo $c1->cond;?>
										</span>
								<br><br>
								<!--								<span style="color: #a1a1a1">نظر:</span>-->
								<span><?php echo $c1->text;?></span>

								<br>
								<br>


								<?php foreach($comments2 as $c2){if($c1->id==$c2->parent_id){?>
									<div style="padding: 0 20px 20px 20px;margin-right: 30px;

											border-radius:11px; ">
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
								<?php }}?>
								<br>
							</div>

							<br>
						</div>
					</div>
				<?php }if($c1->parent_id!=0) {foreach ($comments1 as $c1){foreach ($comments2 as $c2){if ($c1->id==$c2->parent_id && $c2->user_id==$uid){ ?>
					<div style="padding: 0px 30px 0px 30px;">
						<div style=" background-color: #fff;">
							<div style="box-shadow: 5px 5px 5px #c9ccc7; border: 1px solid #ccc;
								border-radius:11px; padding: 10px;">
								<span style="color: #a1a1a1">نام:</span>
								<span><?php if($c1->user_id==''){
										if($c1->name!=''){echo $c1->name;}
										else{echo 'مهمان';}
									}else{
										if($c1->name!=''){echo $c1->name;}
										else{echo 'کاربر';}
									} ?>
						</span>
								<span class="pull-left" style="color: #a1a1a1"> <?php echo $c1->created;?> </span>
								<span style="margin-left: 10px;background-color: #f2f1f1;font-size: 11px; padding: 6px"
									  class="pull-left">
											<?php echo $c1->cond;?>
										</span>
								<br><br>
								<!--								<span style="color: #a1a1a1">نظر:</span>-->
								<span><?php echo $c1->text;?></span>


								<br>
								<br>



									<div style="padding: 0 20px 20px 20px;margin-right: 30px;
background-color: #fdfdde;
											border-radius:11px; ">
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

								<br>
							</div>

							<br>
						</div>
					</div>

				<?php }}}break;}}}?>
				<br>
			</div>


			<div class="col-md-1"></div>

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
							<button style="float: right;outline: unset" id="accept2" class="btn btn-default" data-dismiss="modal"  parent_id="<?php foreach ($comments1 as $c1){echo $c1->id;}?>">تایید و ارسال</button>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>
<?php }?>
