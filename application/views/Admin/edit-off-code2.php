<?php if($this->session->userdata('id')){ ?>

<div class="container-fluid" id="content" style="margin-top: 40px; margin-bottom: 60px">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
			<?php foreach ($off_code as $off){ ?>
            <div style="padding: 20px;border-radius: 10px; border: 1px solid #ccc">
				<input id="id" type="hidden" value="<?php echo $off->id?>">
                <label class="required">کد تخفیف:</label><br>
                <input id="code" class="form-control" type="text" value="<?php echo $off->code?>"><br>
				<label class="required">مبلغ تخفیف:</label><br>
				<input id="mablagh" class="form-control" type="text" value="<?php echo $off->mablagh?>"><br>
                <label class="required">تاریخ شروع:</label><br>
                <input id="start_date" class="form-control" type="date" value="<?php echo $off->start_date?>"><br>
                <label class="required">تاریخ پایان:</label><br>
                <input id="end_date" class="form-control" type="date" value="<?php echo $off->end_date?>"><br>
                <button id="edit" class="btn btn-success">ثبت ویرایش</button>
                <a href="<?php echo base_url()?>admin/off_code" ><button class="btn btn-danger">بازگشت</button></a>
            </div>
			<?php }?>
        </div>
        <div class="col-md-3"></div>
    </div>
</div>
<?php }?>

<script>
	// let today = new Date().toISOString().split('T')[0];
	// $('#start_date')[0].setAttribute('min', today);

	$('#end_date').click(function(){
		let end = $('#start_date').val();
		$('#end_date')[0].setAttribute('min', end);
	});

	$(document).on('click', '#edit', function(e){
		var id = $("#id").val();
		var code=$("#code").val();
		var mablagh=$("#mablagh").val();
		var start_date=$("#start_date").val();
		var end_date=$("#end_date").val();
		$.post('<?php echo base_url()?>admin/off_code_validation2', {
			'id':id,
			'code':code,
			'mablagh':mablagh,
			'start_date':start_date,
			'end_date':end_date,
			},
			function (data) {
				if (data.includes(1)) {
					$.post('<?php echo base_url()?>admin/edit_off', {
							'id':id,
							'code':code,
							'mablagh':mablagh,
							'start_date':start_date,
							'end_date':end_date,
						},
						function (data) {
							if (data.includes(1)) {
								alert('ویرایش با موفقیت انجام شد')
								window.location.href = "<?php echo base_url(); ?>admin/off_code";
							}
						});

				}else {
					if(data.includes(2)){
						$('#code').css({'border-color':'red'});
						$('#code').keyup(function(){
							$('#code').css({'border-color':''});
						});

					}
					if(data.includes(3)){
						$('#mablagh').css({'border-color':'red'});
						$('#mablagh').keyup(function(){
							$('#mablagh').css({'border-color':''});
						});

					}


				}
			});

	});


</script>
