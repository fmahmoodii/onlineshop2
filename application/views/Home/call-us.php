<style>
	@media only screen and (max-device-width: 750px) {
		/* موبایل  */
		.div_space{height: 120px;}
		.div_mrg{margin: 0 auto;}
		.row_mrg{padding: 0 80px;}
		.msg_h4{padding: 20px 0;}
		.div_msg{border-radius:11px; padding: 20px;box-shadow: 5px 5px 5px #c9ccc7;
			border: 1px solid #ccc; position: relative;}
		.div_add{margin-top: 55px;}
		.div_add2{width:70%;border-radius:11px; padding: 20px;box-shadow: 5px 5px 5px #c9ccc7;
			border: 1px solid #ccc; position: relative; margin: 0 auto;}
		.add_h5{font-weight: bold;}
	}
	@media only screen and (min-device-width: 750px ) {
		/* pc  */
		.div_space{height: 120px;}
		.div_mrg{margin: 0 auto;}
		.row_mrg{padding: 0 80px;}
		.msg_h4{padding: 20px 0;}
		.div_msg{border-radius:11px; padding: 20px;box-shadow: 5px 5px 5px #c9ccc7;
			border: 1px solid #ccc; position: relative;}
		.div_add{margin-top: 55px;}
		.div_add2{width:70%;border-radius:11px; padding: 20px;box-shadow: 5px 5px 5px #c9ccc7;
			border: 1px solid #ccc; position: relative; margin: 0 auto;}
		.add_h5{font-weight: bold;}
	}

</style>
<div class="container-fluid div_space">
</div>
<?php foreach ($call_us as $c){?>
<!--call_us-->
<div class="container-fluid div_mrg">
    <div class="row row_mrg">
        <div class="col-md-7" >
            <h4 class="msg_h4">ارسال پیام:</h4>
            <div class="div_msg">
				<label>ایمیل</label>
				<span style="color: red" id="em_err"></span>
				<input autocomplete="off" class="form-control" type="text" id="email">
				<?php echo form_error('phone_number','<span style="color: red">','</span>') ?>
				<br>
                <label>نام و نام خانوادگی</label>
                <input autocomplete="off" class="form-control" type="text" id="name"><br>
                <label>موضوع</label>
                <input autocomplete="off" class="form-control" type="text" id="title"><br>
                <label>متن</label>
                <textarea class="form-control" rows="5" cols="30" id="text"></textarea>
                <br>
                <button class="btn btn-default" id="accept">تایید و ارسال</button>
            </div>

        </div>
        <div class="col-md-5 div_add" >
            <div class="div_add2">
                <h5 class="add_h5">آدرس: </h5>
                <span><?php echo $c->address?></span><br><br>
                <h5 style="font-weight: bold">شماره تماس: </h5>
                <span><?php echo $c->phone?></span><br><br>
				<h5 style="font-weight: bold">موبایل: </h5>
				<span><?php echo $c->mobile?></span><br><br>
                <h5 style="font-weight: bold">کد پستی: </h5>
                <span><?php echo $c->postal_code?></span><br><br>
                <h5 style="font-weight: bold">پست الکترونیکی: </h5>
                <span><?php echo $c->email?></span><br><br>
            </div>
        </div>
    </div>

</div>
<?php }?>

<script>
	$(document).on('click', '#accept', function(e) {
		var email=$('#email').val();
		var name=$('#name').val();
		var title=$('#title').val();
		var text=$('#text').val();
		$.post('<?php  echo base_url();?>home/insert_message', {'email': email,'name': name,'title': title,'text': text},
				function (data) {
					if(data==1){
						$('#email').val('');
						$('#name').val('');
						$('#title').val('');
						$('#text').val('');
						window.location.href="<?php echo base_url(); ?>home/call_us";
					}
				}
		);
	});

	$('#email').keyup(function(){
		var em=$(this).val();
		var regx = "[a-zA-Z0-9.-]+@[a-z-]+\.[a-z]{2,3}";
		var err='';
		var error = $('#em_err');

		if(em!==''){
			if(em.match(regx)){
				err= '';
				error.html(err);
			}
			else
			{
				err= 'فرمت ایمیل نادرست است';
				error.html(err);
				error.css({'color':'red'});
			}

		}else{
			error.html(err);
			error.css({'color':''});
		}

	});

</script>
