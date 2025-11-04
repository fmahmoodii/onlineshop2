<!--space-->
<div style="height: 130px"></div>

<?php foreach ($products as $p){?>
		<input id="id_p" value="<?php echo $p->id?>" type="hidden">
		<input id="code_p" value="<?php echo $p->code?>" type="hidden">


<!--details-->
<div class="container">
    <div class="row">
        <div class="col-md-6" >
            <div  class="owl-carousel" style="border: 1px solid #ccc;border-radius: 10px;padding: 28px;text-align: center">
				<?php foreach ($images as $img){if ($img->user_id==$p->code){ ?>
					<img src="<?php echo base_url().$img->direction; ?>" style="width: 600px; height: 500px">
				<?php }}?>
            </div>
        </div>
        <div class="col-md-6">
            <div style="border: 1px solid #ccc;border-radius: 10px;padding: 30px">

                <h3 id="name_<?php echo $p->id?>"><?php echo $p->name?></h3>
                <i id="add_bookmark"
				   class="fa fa-bookmark pull-left"
				   style=" position: absolute;left: 67px;top: 66px;
                font-size: 25px;color: red;cursor: pointer;
display:<?php if (isset($bookmarks1[0])){echo 'block';}else{echo 'none';} ?>"></i>

				<i id="delete_bookmark"
				   class="fa fa-bookmark-o pull-left"
				   style=" position: absolute;left: 67px;top: 66px;
                font-size: 25px;color: red;cursor: pointer;
display:<?php if (empty($bookmarks1[0])){echo 'block';}else{echo 'none';} ?>"></i>

				<?php foreach ($attrs as $t){?>
						<input type="radio" name="attr" id="attr_<?php echo $t->id?>" value="<?php echo $t->id?>"
							   id_attr="<?php echo $t->id?>" is_default="<?php echo $t->default?>" >
				<?php }?>

				<hr>

				<?php foreach ($attrs as $t){?>

				<div class="product_attr" id="product_attr_<?php echo $t->id?>" id_attr="<?php echo $t->id?>"
					 is_default="<?php echo $t->default?>"  style="display: none">
				<div style="border: 1px solid #ccc;border-radius: 10px;padding: 20px;display: <?php if(empty($t->off_percent)){echo 'block';}else{echo 'none';}?>">
					<i style="font-size: 22px" class="fa fa-tag"></i>
					<span style="font-size:22px;">
						<?php echo number_format($t->price)?> تومان
					</span>
				</div>
				<div style="border: 1px solid #ccc;border-radius: 10px;padding: 20px;display: <?php if(empty($t->off_percent)){echo 'none';}else{echo 'block';}?>">
					<span style="font-size:22px;">
						<i class="fa fa-tag"></i>
						<?php echo number_format($t->price-(($t->off_percent)/100)*$t->price);?> تومان</h3>
					</span>
					<br>
					&nbsp
					&nbsp
					&nbsp
					<strike style="font-size:22px;">
						<?php echo number_format($t->price)?> تومان
					</strike>
					<i class="fa fa-di"></i>
					<span style="color:#fff;background-color: #e11c1c; padding: 5px; border-radius: 25px;
					text-align: center;line-height: 28px;font-size: 18px;" class="pull-left">
						<?php echo $t->off_percent?>%
					</span>
				</div>


                <br>

                <div style="padding: 10px;display:<?php if (($t->model==0 || $t->model==null)&&($t->jens==0 || $t->jens==null)
						&&($t->size==0 || $t->size==null)&&($t->brand==0 || $t->brand==null)&&($t->color==0 || $t->color==null))
				{echo 'none';}else{echo 'inline';} ?>">
                    <h4 style="color: #969696;font-weight: bold; ">مشخصات محصول:</h4>
					<span style="font-size: 20px; font-weight: bold;display:<?php if (!($t->model==0 || $t->model==null)){echo 'inline';}else{echo 'none';} ?> ">
						مدل:</span>
					<?php foreach ($model as $mo){if($t->model==$mo->id){?>
					<span><?php echo $mo->name?></span><?php }}?>
					<?php if($t->model!=0){?>
						<br>
					<?php }?>

					<span style="font-size: 20px; font-weight: bold;display:<?php if (!($t->jens==0 || $t->jens==null)){echo 'inline';}else{echo 'none';} ?> ">
						جنس:</span>
					<?php foreach ($jens as $je){if($t->jens==$je->id){?>
						<span><?php echo $je->name?></span><?php }}?>
					<?php if($t->jens!=0){?>
							<br>
					<?php }?>

					<span style="font-size: 20px; font-weight: bold;display:<?php if (!($t->size==0 || $t->size==null)){echo 'inline';}else{echo 'none';} ?> ">
						سایز:</span>
					<?php foreach ($size as $si){if($t->size==$si->id){?>
						<span><?php echo $si->name?></span><?php }}?>
					<?php if($t->size!=0){?>
						<br>
					<?php }?>

					<span style="font-size: 20px; font-weight: bold;display:<?php if (!($t->brand==0 || $t->brand==null)){echo 'inline';}else{echo 'none';} ?> ">
						برند:</span>
					<?php foreach ($brand as $br){if($t->brand==$br->id){?>
						<span><?php echo $br->name?></span><?php }}?>
					<?php if($t->brand!=0){?>
						<br>
					<?php }?>

					<span style="font-size: 20px; font-weight: bold;display:<?php if (!($t->color==0 || $t->color==null)){echo 'inline';}else{echo 'none';} ?> ">
						رنگ:</span>
					<?php foreach ($color as $co){if($t->color==$co->id){?>
						<span><?php echo $co->name?></span><?php }}?>
					<?php if($t->color!=0){?>
						<br>
					<?php }?>
					<br>
                </div>



                <b style="font-size: 16px; margin-left: 25px;line-height: 36px">تعداد:</b><br>
                <input id="qty_<?php echo $t->id?>"
					   type="number" step="1" min="1" max="<?php echo $t->supply?>" value="1" size="4"
					   pattern="[0-9]*" inputmode="numeric" style="width: 90px;height: 37px;padding: 9px;font-size: 16px;
                           border-radius: 5px;border: 1px solid #809c80;outline: unset;">

				<?php if($this->session->userdata('user_id')){ ?>
                <div>
                    <button id="shopping_cart" id_attr="<?php echo $t->id?>" id_p="<?php echo $p->id?>"
							class="btn btn-success" style="width: 100%;padding: 15px;outline: unset;
							border-radius: 8px;border: 1px solid #ccc;box-shadow: 3px 3px 3px #dbdbdb;margin-top: 28px;">
                        <span style="font-size: 18px;"> افزودن به سبد خرید</span>
                    </button>
                </div>
				<?php }else{?>
				<div>
					<button id="empty_btn" class="btn btn-success" style="width: 100%;padding: 15px;outline: unset;
                        border-radius: 8px;border: 1px solid #ccc;box-shadow: 3px 3px 3px #dbdbdb;margin-top: 28px;">
						<span style="font-size: 18px;"> افزودن به سبد خرید</span>
					</button>
				</div>
				<?php }?>

				</div>

				<?php } ?>
            </div>
        </div>
    </div>
</div>
<br>
<!--tabs-->
<div class="container" style="margin: 0 auto">
    <div class="row">
        <div style="border-radius: 8px;border: 1px solid #ccc;box-shadow: 3px 3px 3px #dbdbdb;padding: 20px">
            <ul class="nav nav-tabs" style="margin-bottom: 30px">
                <li class="active"><a style="color: #414141" data-toggle="tab" href="#home"><b style="font-size: 16px;font-weight: bold">توضیحات</b></a></li>
                <li><a style="color: #414141" data-toggle="tab" href="#menu2"><b style="font-size: 16px;font-weight: bold">نظرات کاربران</b></a></li>
                <li><a style="color: #414141" data-toggle="tab" href="#menu3"><b style="font-size: 16px;font-weight: bold">مشخصات محصول</b></a></li>
            </ul>

            <div class="tab-content" style="padding: 20px">
                <div id="home" class="tab-pane fade in active">
                    <p style="font-size: 16px;font-weight: bold"><?php echo $p->text?></p>
                </div>
                <div id="menu2" class="tab-pane fade">
                    <button style="margin-bottom: 20px;outline: unset;font-size: 16px;font-weight: bold" data-toggle="modal"
                            data-target="#comment_modal"
                            class="btn btn-success" >ارسال دیدگاه</button>
					<?php foreach ($comments as $c){if ($p->code == $c->code){ if ($c->role=='1' && $c->parent_id==0){ ?>
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
                                <br><br>
<!--								<span style="color: #a1a1a1">نظر:</span>-->
								<span><?php echo $c->text;?></span>
								<br>
								<button style="margin-left: 20px;outline: unset;" data-toggle="modal"
										data-target="#ans_modal"
										class="pull-left btn btn-sm ml-3 btn-info ">پاسخ</button>
								<br>
								<br>

								<?php foreach($comments2 as $c2){if ($c2->role=='1' && $c2->parent_id==$c->id){
									?>
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
											<br><br>
		<!--									<span style="color: #a1a1a1">نظر:</span>-->
											<span><?php echo $c2->text;?></span>
											<br>
										</div>
									<?php
								}}?>
								<br>
                            </div>

                            <br>
                        </div>
                    </div>
					<?php }}}?>
				</div>

                <div id="menu3" class="tab-pane fade">

                    <p style="font-size: 16px;font-weight: bold">مشخصات محصول</p>
                </div>
            </div>
        </div>
    </div>
</div>


<!--comment_modal-->
<div class="modal fade" id="comment_modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content " >
            <div class="modal-header text-center" style="background-color: #fff" >
                <button style="float: right;" type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h4 class="modal-title">ارسال دیدگاه</h4>
            </div>
            <div class="modal-body">
				<input type="hidden" id="code"  value="<?php echo $p->code ?>">
                <label>نام و نام خانوادگی</label>
                <input id="name" class="form-control" type="text" placeholder="نام و نام خانوادگی"
					   user_id="<?php if($this->session->userdata('user_id')){
					   foreach ($profile as $prof){if($prof->user_id==$this->session->userdata('user_id')){
						   echo $prof->user_id; }}} ?>"
				value="<?php if($this->session->userdata('user_id')){
					foreach ($profile as $prof){if($prof->user_id==$this->session->userdata('user_id')){
					echo $prof->name.' '.$prof->family; }else{echo '';}}}?>"
				>
                <br>
                <label>نظر</label>
                <textarea id="text" class="form-control" type="text" rows="10"  name="text"></textarea><br><br>
            </div>
            <div class="modal-footer">
                <button style="float: right;outline: unset" id="accept" class="btn btn-default" data-dismiss="modal">تایید و ارسال</button>
            </div>
        </div>
    </div>
</div>

<!--comment_modal-->
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
					<input type="hidden" id="code2"  value="<?php echo $p->code ?>">
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
					<button style="float: right;outline: unset" id="accept2" class="btn btn-default" data-dismiss="modal"  parent_id="<?php foreach ($comments as $c){if ($p->code == $c->code){ if ($c->role=='1' && $c->parent_id==0){echo $c->id; break;}}}?>">تایید و ارسال</button>
				</div>
			</div>
		</div>
	</div>

<?php }?>
<script>
	$(document).on('click', '#delete_bookmark', function(e) {
		$('#delete_bookmark').hide();
		$('#add_bookmark').show();
		var id_p=$('#id_p').val();
		$.post('<?php  echo base_url();?>home/add_bookmark', {'id_p':id_p,},
				function (data) {
					if(data==1){

					}
				}
		);
	});
	$(document).on('click', '#add_bookmark', function(e) {
		$('#delete_bookmark').show();
		$('#add_bookmark').hide();
		var id_p=$('#id_p').val();
		$.post('<?php  echo base_url();?>home/delete_bookmark', {'id_p': id_p,},
				function (data) {
					if(data==1){

					}
				}
		);
	});
	$(document).on('click', '#accept', function(e) {
		var code=$('#code').val();
		var user_id=$('#name').attr('user_id');
		var parent_id=0;
		var name=$('#name').val();
		var text=$('#text').val();
		$.post('<?php  echo base_url();?>home/insert_comment', {'code':code,'name': name,'text': text,'user_id': user_id,'parent_id': parent_id,},
				function (data) {
					if(data==1){
						console.log(data);
						$('#name').val('');
						$('#text').val('');
					}
				}
		);
	});
	$(document).on('click', '#accept2', function(e) {
		var code=$('#code').val();
		var user_id=$('#name2').attr('user_id');
		var parent_id=$('#accept2').attr('parent_id');
		console.log(parent_id);
		var name=$('#name2').val();
		var text=$('#text2').val();
		$.post('<?php  echo base_url();?>home/insert_comment', {'code':code,'name': name,'text': text,'user_id': user_id,'parent_id': parent_id,},
			function (data) {
				if(data==1){
					console.log(data);
					$('#name2').val('');
					$('#text2').val('');
				}
			}
		);
	});
	$(document).on('click', '#empty_btn', function(e) {
		alert('لطفا اول ثبت نام کنید')
	});
	$('.owl-carousel').owlCarousel(
			{
				margin: 10,
				rtl:true,
				loop: true,
				nav: true,
				navText : [
					'<i class="fa fa-caret-left" ' +
					'style="font-size: 26px;position: absolute;left: 4px;top: 180px;color: #d0d0d0" aria-hidden="true"></i>',
					'<i class="fa fa-caret-right"' +
					'style="font-size: 26px;position: absolute;right: 4px;top: 180px;color: #d0d0d0" aria-hidden="true"></i>'
				],
				navElement: 'div',
				items: 1,
				autoplay: true,
				autoplayTimeout: 3000,
				autoplayHoverPause: true,
				center:true,
				startPosition: 0,
				responsive:
						{
							0:
									{
										items: 1,
									},
							600:
									{
										items: 1
									},
							1200:
									{
										items: 1
									}

						}
			}
	);

	$(document).ready(function(e){
		var ids = [];
		$('input[name=attr]').each(function(i, e) {
			ids.push($(e).attr('id_attr'))
		})
		ids.forEach(function (i){
			var id=i;
			if($('input[value='+id+']').attr('is_default')==1) {
				$('input[value=' + id + ']').prop('checked', true);
				$('#product_attr_'+id).css({'display':''});
			}
		});


		$('input[name=attr]').click(function() {
			var ids = [];
			var id2 = $(this).attr("id_attr");
			if($("#product_attr_"+id2).attr("id_attr")==id2){
				$("#product_attr_"+id2).show();
			}
			$('input[name=attr]').each(function(i, e) {
				ids.push($(e).attr('id_attr'))
			})
			ids.forEach(function (i){
				var id=i;
				if($("#product_attr_"+id).attr("id_attr")!=id2){
					$("#product_attr_"+id).hide();
				}
			});


		});


	});

	// const max = +maca.getAttribute("max");
	//
	// maca.addEventListener("keydown", function(e) {
	// 	const typed = +e.key;
	//
	// 	if(!isNaN(typed)) e.preventDefault(); // Allow any non-number keys (backspace etc.)
	//
	// 	if ( +(e.target.value + typed) <= max) {
	// 		maca.value += typed
	// 	} else {
	// 		console.log(`Number too big! Max is ${max}`)
	// 	}
	// })
</script>
