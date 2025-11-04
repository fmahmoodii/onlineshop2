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
					<img src="<?php echo base_url().$img->direction; ?>"  style="width: 600px; height: 500px">
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

				<hr>
				<div style="border: 1px solid #ccc;border-radius: 10px;padding: 20px;display: <?php if(empty($p->off_percent)){echo 'block';}else{echo 'none';}?>">
					<i style="font-size: 22px" class="fa fa-tag"></i>
					<span style="font-size:22px;">
						<?php echo number_format($p->price)?> تومان
					</span>
				</div>
				<div style="border: 1px solid #ccc;border-radius: 10px;padding: 20px;display: <?php if(empty($p->off_percent)){echo 'none';}else{echo 'block';}?>">
					<span style="font-size:22px;">
						<i class="fa fa-tag"></i>
						<?php echo number_format($p->price-(($p->off_percent)/100)*$p->price);?> تومان</h3>
					</span>
					<br>
					&nbsp
					&nbsp
					&nbsp
					<strike style="font-size:22px;">
						<?php echo number_format($p->price)?> تومان
					</strike>
					<i class="fa fa-di"></i>
					<span style="color:#fff;background-color: #e11c1c; padding: 5px; border-radius: 25px;
					text-align: center;line-height: 28px;font-size: 18px;" class="pull-left">
						<?php echo $p->off_percent?>%
					</span>
				</div>


                <br>
                <div style="padding: 10px">
                    <h4 style="color: #969696;font-weight: bold">مشخصات محصول:</h4>
                    <br>
					<span style="font-size: 20px; font-weight: bold;display:<?php if (isset($p->model[0])){echo 'inline';}else{echo 'none';} ?> ">
						مدل:</span>
					<span><?php echo $p->model?></span>
					<?php if (isset($p->model[0])){echo '<br>';}?>

					<span style="font-size: 20px; font-weight: bold;display:<?php if (isset($p->jens[0])){echo 'inline';}else{echo 'none';} ?> ">
						جنس:</span>
					<span><?php echo $p->jens;?></span>
					<?php if (isset($p->jens[0])){echo '<br>';}?>

					<span style="font-size: 20px; font-weight: bold;display:<?php if (isset($p->size[0])){echo 'inline';}else{echo 'none';} ?> ">
						سایز:</span>
					<span><?php echo $p->size?></span>
					<?php if (isset($p->size[0])){echo '<br>';}?>

					<span style="font-size: 20px; font-weight: bold;display:<?php if (isset($p->brand[0])){echo 'inline';}else{echo 'none';} ?> ">
						برند:</span>
					<span><?php echo $p->brand?></span>
					<?php if (isset($p->brand[0])){echo '<br>';}?>
                    <br>
                </div>

                <b style="font-size: 16px; margin-left: 25px;line-height: 36px">تعداد:</b><br>
                <input id="qty_<?php echo $p->id?>"
					   type="number" step="1" min="1" max="100" value="1" size="4"
					   pattern="[0-9]*" inputmode="numeric" style="width: 90px;height: 37px;padding: 9px;font-size: 16px;
                           border-radius: 5px;border: 1px solid #809c80;outline: unset;">

				<?php if($this->session->userdata('user_id')){ ?>
                <div>
                    <button id="shopping_cart" id_p="<?php echo $p->id?>" <?php echo $p->id?> class="btn btn-success" style="width: 100%;padding: 15px;outline: unset;
                        border-radius: 8px;border: 1px solid #ccc;box-shadow: 3px 3px 3px #dbdbdb;margin-top: 28px;">
                        <span style="font-size: 18px;"> افزودن به سبد خرید</span>
                    </button>
                </div>
				<?php }else{?>
				<div>
					<button id="empty_btn" id_p="<?php echo $p->id?>" code_p="<?php echo $p->code?>" class="btn btn-success" style="width: 100%;padding: 15px;outline: unset;
                        border-radius: 8px;border: 1px solid #ccc;box-shadow: 3px 3px 3px #dbdbdb;margin-top: 28px;">
						<span style="font-size: 18px;"> افزودن به سبد خرید</span>
					</button>
				</div>
				<?php }?>

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
					<?php foreach ($comments as $c){if ($p->code == $c->code){ if ($c->role=='1'){ ?>
                    <div style="padding: 0px 30px 0px 30px;">
                        <div style=" background-color: #fff;">
                            <div style="box-shadow: 5px 5px 5px #c9ccc7; border: 1px solid #ccc;
                                        border-radius:11px; padding: 10px;">
                                <span style="color: #a1a1a1">نام:</span>
								<span><?php echo $c->name;?></span>
								<span class="pull-left" style="color: #a1a1a1"> <?php echo $c->created;?> </span>
                                <br><br>
								<span style="color: #a1a1a1">نظر:</span>
								<span><?php echo $c->text;?></span>
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
                <input id="name" class="form-control" type="text" placeholder="نام و نام خانوادگی">
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
		var name=$('#name').val();
		var text=$('#text').val();
		$.post('<?php  echo base_url();?>home/insert_comment', {'code':code,'name': name,'text': text,},
				function (data) {
					if(data==1){
						console.log(data);
						$('#name').val('');
						$('#text').val('');
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
</script>
