<?php if($this->session->userdata('id')){ ?>

<div class="container-fluid" id="content" style="margin: 50px 0 60px 0">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8" style="
        padding: 20px;border-radius: 10px; border: 1px solid #ccc">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#about"><b>اطلاعات تماس</b></a> </li>
                <li><a data-toggle="tab" href="#links"><b>لینک ها</b></a> </li>
                <li><a data-toggle="tab" href="#social"><b>شبکه های اجتماعی</b></a> </li>
            </ul>
            <div class="tab-content" style="padding: 20px">
                <div  id="about" class="tab-pane fade active in">
					<?php if(isset($call_us[0])){foreach ($call_us as $ca){?>
						<input type="hidden" id="id_ca" value="<?php echo $ca->id?>">
						<label>شماره موبایل:</label><br>
						<input type="tel" class="form-control" id="mobile" min="11" maxlength="11"
						value="<?php echo $ca->mobile?>"><br>
						<label>شماره تلفن:</label><br>
						<input type="tel" class="form-control" id="phone" value="<?php echo $ca->phone?>"><br>
						<label>آدرس:</label><br>
						<input type="text" class="form-control" id="address" value="<?php echo $ca->address?>"><br>
						<label>کدپستی:</label><br>
						<input type="text" class="form-control" id="postal_code" value="<?php echo $ca->postal_code?>"><br>
						<label>ایمیل:</label><br>
						<input type="email" class="form-control" id="email" value="<?php echo $ca->email?>"><br>
						<label>درباره ما:</label><br>
						<textarea rows="10" cols="20" name="text" class="form-control" id="about_us">
							<?php echo $ca->text?>
						</textarea><br>
						<button id="about_btn" class="btn btn-success">ثبت ویرایش</button>
						<button id="cancel_btn" class="btn btn-danger" style="padding-right: 20px; width: 100px">لغو</button>					<?php }}?>
                </div>
                <div  id="links" class="tab-pane fade">
					<?php if(isset($links[0])){foreach ($links as $li){?>
						<input type="hidden" id="id_li" value="<?php echo $li->id?>">
						<label>لینک 1:</label><br>
						<input type="text" class="form-control" id="link1" value="<?php echo $li->link1?>"><br>
						<label>لینک 2:</label><br>
						<input type="text" class="form-control" id="link2" value="<?php echo $li->link2?>"><br>
						<label>لینک 3:</label><br>
						<input type="text" class="form-control" id="link3" value="<?php echo $li->link3?>"><br>
						<label>لینک 4:</label><br>
						<input type="text" class="form-control" id="link4" value="<?php echo $li->link4?>"><br>
						<label>لینک 5:</label><br>
						<input type="text" class="form-control" id="link5" value="<?php echo $li->link5?>"><br>
						<label>لینک 6:</label><br>
						<input type="text" class="form-control" id="link6" value="<?php echo $li->link6?>"><br>
						<label>لینک 7:</label><br>
						<input type="text" class="form-control" id="link7" value="<?php echo $li->link7?>"><br>
						<label>لینک 8:</label><br>
						<input type="text" class="form-control" id="link8" value="<?php echo $li->link8?>"><br>
						<button id="links_btn" class="btn btn-success">ثبت ویرایش</button>
						<button id="cancel_btn" class="btn btn-danger" style="padding-right: 20px; width: 100px">لغو</button>					<?php }}?>
                </div>
                <div  id="social" class="tab-pane fade">
					<?php if(isset($socials[0])){foreach ($socials as $so){?>
						<input type="hidden" id="id_so" value="<?php echo $so->id?>">
						<i class="fa fa-whatsapp"></i>
						<label>whats app</label>
						<br>
						<input id="whatsapp" type="text" class="form-control" value="<?php echo $so->whatsapp?>"><br>
						<i class="fa fa-facebook"></i>
						<label>facebook</label>
						<br>
						<input id="facebook" type="text" class="form-control" value="<?php echo $so->facebook?>"><br>
						<i class="fa fa-telegram"></i>
						<label>telegram</label>
						<br>
						<input id="telegram" type="text" class="form-control" value="<?php echo $so->telegram?>"><br>
						<i class="fa fa-instagram"></i>
						<label>instagram</label>
						<br>
						<input id="instagram" type="text" class="form-control" value="<?php echo $so->instagram?>"><br>
						<button id="social_btn" class="btn btn-success">ثبت ویرایش</button>
						<button id="cancel_btn" class="btn btn-danger" style="padding-right: 20px; width: 100px">لغو</button>
					<?php }}?>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
<?php }?>
<script>
	$(document).on('click', '#about_btn', function(e) {
		var id=$('#id_ca').val();
		var mobile=$('#mobile').val();
		var phone=$('#phone').val();
		var address=$('#address').val();
		var postal_code=$('#postal_code').val();
		var email=$('#email').val();
		var text=$('#about_us').val();
		$.post('<?php  echo base_url();?>admin/edit_call_us', {'id':id,'mobile': mobile,
					'phone': phone,'address': address,'postal_code': postal_code,'email': email,'text': text,},
				function (data) {
					if(data==1){
						alert('ویرایش با موفقیت انجام شد')
						window.location.href="<?php echo base_url(); ?>admin/footer";
					}
				}
		);
	});
	$(document).on('click', '#links_btn', function(e){
		var id=$("#id_li").val();
		var link1=$('#link1').val();
		var link2=$('#link2').val();
		var link3=$('#link3').val();
		var link4=$('#link4').val();
		var link5=$('#link5').val();
		var link6=$('#link6').val();
		var link7=$('#link7').val();
		var link8=$('#link8').val();
		$.post('<?php echo base_url();?>admin/edit_links',{'id':id,'link1':link1,'link2':link2,'link3':link3
					,'link4':link4,'link5':link5,'link6':link6,'link7':link7,'link8':link8},
				function (data) {
					if (data==1){
						alert('ویرایش با موفقیت انجام شد')
						window.location.href="<?php echo base_url(); ?>admin/footer";
					}
				}
		);
	});
	$(document).on('click', '#social_btn', function(e) {
		var id=$('#id_so').val();
		var whatsapp=$('#whatsapp').val();
		var facebook=$('#facebook').val();
		var telegram=$('#telegram').val();
		var instagram=$('#instagram').val();
		$.post('<?php  echo base_url();?>admin/edit_socials', {'id':id,'whatsapp': whatsapp,'facebook': facebook,'telegram': telegram,'instagram': instagram,},
				function (data) {
					if(data==1){
						alert('ویرایش با موفقیت انجام شد')
						window.location.href="<?php echo base_url(); ?>admin/footer";
					}
				}
		);
	});
	$(document).on('click', '#cancel_btn', function(e) {
		var m = confirm('تغییرات ذخیره نمی شوند. آیا از لغو عملیات اطمینان دارید؟')
		if (m == true) {
			window.location.href="<?php echo base_url(); ?>admin/footer";
		}
	});


</script>
