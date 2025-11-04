<?php if ($this->session->userdata('id')) { ?>

	<div class="container-fluid" id="content" >
		<div class="row" style="margin-top: 50px; margin-bottom: 60px">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<?php foreach ($off_code as $off){ ?>
				<div style="padding: 20px;border-radius: 10px; border: 1px solid #ccc">
					<form method="post" action='<?php echo base_url('admin/edit_off') ?>' onsubmit="return validateForm()" >

						<input id="id" name="id" type="hidden" value="<?php echo $off->id?>">

						<div class="form-group">
							<label for="code" class="required">کد تخفیف:</label><br>
							<input required autocomplete="off" id="code" name="code"
								   class="form-control" type="text"
								   value="<?php echo $off->code; ?>"
								   placeholder="کد تخفیف">
							<div hidden id="dp_code" class="form-control" style="width: 100%;
						 height: 50px;
						 border: 1px solid #cccccc;
						 display: block;
						 overflow-y: scroll;"></div>
							<span id="email_result"></span>
							<?php echo form_error('code','<span id="vvv" style="color: red">','</span>') ?>
						</div>

						<div class="form-group">
							<label for="mablagh" class="required">مبلغ تخفیف:</label><br>
							<input required autocomplete="off" id="mablagh" name="mablagh"
								   class="form-control positive" type="number" min="0"
								   value="<?php echo $off->mablagh; ?>"
								   placeholder="مبلغ تخفیف">
							<span id="email_result2"></span>
							<?php echo form_error('mablagh','<span style="color: red">','</span>') ?>

						</div>

						<div class="form-group start_date" data-date="<?php echo $off->start_date; ?>" style="display: inline-block; width: 270px">
							<label for="start_date" class="required">تاریخ شروع:</label><br>
							<input readonly id="start_date" name="start_date" class="form-control" placeholder="تاریخ شروع"><br>
							<input hidden id="datetime1" name="datetime1" value="<?php echo $off->start_date; ?>">
							<?php echo form_error('start_date','<span style="color: red">','</span>') ?>
						</div>

						<div class="form-group end_date" data-date="<?php echo $off->end_date; ?>" style="display: inline-block; width: 270px">
							<label for="end_date" class="required">تاریخ پایان:</label><br>
							<input readonly id="end_date" name="end_date" class="form-control" placeholder="تاریخ پایان"><br>
							<input hidden id="datetime2" name="datetime2" value="<?php echo $off->end_date; ?>">
							<?php echo form_error('end_date','<span style="color: red">','</span>') ?>
						</div>
						<br>
						<br>


						<div class="form-group">
							<label for="min_price" class="required">حداقل خرید:</label><br>
							<input required autocomplete="off" id="min_price" name="min_price"
								   class="form-control positive" type="number" min="0"
								   value="<?php echo $off->min_price; ?>"
								   placeholder="حداقل خرید">

							<?php echo form_error('min_price','<span style="color: red">','</span>') ?>

						</div>

						<div class="form-group">
							<label for="uses" class="required">تعداد دفعات استفاده:</label><br>
							<input required autocomplete="off" id="uses" name="uses"
								   class="form-control positive" type="number" min="1"
								   value="<?php echo $off->uses; ?>"
								   placeholder="تعداد دفعات استفاده">

							<?php echo form_error('uses','<span style="color: red">','</span>') ?>

						</div>

						<br>

						<div class="form-group p_div" style="border: 3px solid #ccc; padding: 20px; background-color: #efe41b">
							<i class="fa fa-info-circle"
							   style="font-size: 26px; color: white"
							   title="برای انتخاب همه محصولات این فیلد را خالی بگذارید"
							></i>
							<label for="products" class="" style="color: white;
    line-height: 26px;
    position: absolute;
    margin-right: 10px;">محصولات:</label>
							<br>
							<select multiple class="form-control products" id="products" name="products[]" >

								<?php foreach ($products as $p){ ?>
									<option <?php foreach($off_code_products as $off_p){if($off_p->id_product==$p->id){ echo 'selected'; break;}}?>
											value="<?php echo $p->id?>"><?php echo $p->name?>
									</option>
								<?php }?>

							</select>
							<?php echo form_error('products','<span style="color: red">','</span>') ?>
							<br>
							<!--							<br>-->
							<!--							<br>-->
							<!--							<button type="button" id="selectAll_p" class="btn btn-default">انتخاب همه</button>-->
							<!--							<button type="button" id="deselectAll_p" class="btn btn-default">حذف همه</button>-->

						</div>

						<br>

						<div class="form-group u_div" style="border: 3px solid #ccc; padding: 20px; background-color: #1fcbe7">
							<i class="fa fa-info-circle"
							   style="font-size: 26px; color: white"
							   title="برای انتخاب همه کاربران این فیلد را خالی بگذارید"
							></i>
							<label for="users" class="" style="color: white;
    line-height: 26px;
    position: absolute;
    margin-right: 10px;">کاربران:</label>
							<br>
							<select multiple class="form-control users" id="users" name="users[]" >

								<?php foreach ($register as $reg){ foreach ($profile as $prof){if($reg->id == $prof->user_id){ ?>
									<option
											<?php foreach($off_code_users as $off_u){if($off_u->id_user==$reg->id){ echo 'selected'; break;}}?>
											value="<?php echo $reg->id?>"><?php $x=$prof->name.' '.$prof->family; echo $x;?></option>
								<?php }}}?>

							</select>
							<?php echo form_error('users','<span style="color: red">','</span>') ?>

							<!--							<br>-->
							<!--							<br>-->
							<!--							<button type="button" id="selectAll_u" class="btn btn-default">انتخاب همه</button>-->
							<!--							<button type="button" id="deselectAll_u" class="btn btn-default">حذف همه</button>-->
						</div>

						<br>

						<button class="btn btn-success" id="submit" style="width: 150px; ">ثبت و ذخیره</button>
						<button type="button" id="limit_p" class="btn btn-default pull-left" style="margin-left: 5px; background-color: #efe41b; outline: unset; ">انتخاب محصولات</button>
						<button type="button" id="limit_u" class="btn btn-default pull-left" style="margin-left: 5px; background-color: #1fcbe7; outline: unset; ">انتخاب کاربران</button>



					</form>

				</div>

			</div>
			<?php } ?>
			<div class="col-md-2"></div>
		</div>

	</div>

<?php }?>



<script type="text/javascript" language="javascript" >

	$(document).ready(function(){



	});


	/*$('#start_date,#end_date').persianDatepicker({
		// viewMode: 'year'
		initialValueType: 'persian',
		observer: true,
		format: 'YYYY-MM-DD HH:mm:ss',
		timePicker: {
			enabled: true,
			meridiem: {
				enabled: true
			}
		},

	});*/


	var to, from;
	to = $(".end_date").persianDatepicker({
		inline: true,
		altField: '#end_date',
		// altFormat: 'YYYY-MM-DD HH:mm:ss',
		altFormat: 'LLLL',
		initialValue: true,
		timePicker: {
			enabled: true,
			meridiem: {
				enabled: true
			}
		},
		navigator:{
			scroll:{
				enabled: false
			},
			text: {

				btnNextText:'بعدی',

				btnPrevText:'قبلی',
			}
		},
		onSelect: function (unix) {

			// const state = to.getState();
			// var dt = new Date(state.view.unixDate);
			// var datetime2 = dt.toLocaleString('fa-IR', {numberingSystem: 'latn'}).replace(',','').replaceAll('/', '-') ;
			// var x = datetime2.toString();
			// console.log(x);
			// console.log(typeof x);
			// $('#datetime2').val(x);

			const state = to.getState();
			var dt = new Date(state.view.unixDate);
			let month = dt.getUTCMonth() + 1; //months from 1-12
			let day = dt.getUTCDate();
			let year = dt.getUTCFullYear();
			let hour = dt.getHours();
			let minute = dt.getMinutes();
			let second = dt.getSeconds();

			newdate = year + "/" + month + "/" + day + " " + hour + ":" + minute + ":" +second;
			// console.log(newdate);
			// console.log(typeof newdate)
			$('#datetime2').val(newdate);



			to.touched = true;
			if (from && from.options && from.options.maxDate != unix) {
				var cachedValue = from.getState().selected.unixDate;
				from.options = {maxDate: unix};
				if (from.touched) {
					from.setDate(cachedValue);
				}
			}
		}
	});
	from = $(".start_date").persianDatepicker({
		inline: true,
		altField: '#start_date',
		altFormat: 'LLLL',
		initialValue: true,
		timePicker: {
			enabled: true,
			meridiem: {
				enabled: true
			}
		},
		navigator:{
			scroll:{
				enabled: false
			},
			text: {

				btnNextText:'بعدی',

				btnPrevText:'قبلی',
			}
		},
		onSelect: function (unix) {

			// const state = from.getState();
			// var dt = new Date(state.view.unixDate);
			// var datetime1 = dt.toLocaleString('fa-IR', {numberingSystem: 'latn'}).replace(',','').replaceAll('/', '-') ;
			// var x = datetime1.toString();
			// console.log(x);
			// console.log(typeof x);
			// $('#datetime1').val(x);

			const state = from.getState();
			var dt = new Date(state.view.unixDate);
			let month = dt.getUTCMonth() + 1; //months from 1-12
			let day = dt.getUTCDate();
			let year = dt.getUTCFullYear();
			let hour = dt.getHours();
			let minute = dt.getMinutes();
			let second = dt.getSeconds();

			newdate = year + "/" + month + "/" + day + " " + hour + ":" + minute + ":" +second;
			// console.log(newdate);
			// console.log(typeof newdate)
			$('#datetime1').val(newdate);


			from.touched = true;
			if (to && to.options && to.options.minDate != unix) {
				var cachedValue = to.getState().selected.unixDate;
				to.options = {minDate: unix};
				if (to.touched) {
					to.setDate(cachedValue);
				}
			}
		}
	});




	$(document).on('click', '.tag', function(e) {
		let id = $(this).attr("id_c");
		let x=$("#tag_"+id).text();
		// console.log(x);
		$("#code").val(x);

		var code = $('#code').val();
		var id_off = $('#id').val();

		if(code != '')
		{
			$.ajax({
				url:"<?php echo base_url(); ?>admin/check_code2",
				method:"POST",
				data:{code:code,id_off:id_off},
				success:function(data){
					$('#email_result').html(data);
				}
			});
		}else{
			load_data();
			$('#email_result').html('');
		}

	});

	$('#limit_p').click(function() {
		if(! $(".p_div").is(':visible')){
			$(".p_div").show();}
		else if ($(".p_div").is(':visible')){
			$(".p_div").hide();}
	});

	$('#limit_u').click(function() {
		if(! $(".u_div").is(':visible')){
			$(".u_div").show();}
		else if ($(".u_div").is(':visible')){
			$(".u_div").hide();}
	});

	$('#selectAll_p').click(function() {
		$(".products > option").prop("selected", true);
		$(".products").trigger("change");
	});
	$('#deselectAll_p').click(function() {
		$(".products").val(null).trigger("change");
	});

	$('#selectAll_u').click(function() {
		$(".users > option").prop("selected", true);
		$(".users").trigger("change");
	});
	$('#deselectAll_u').click(function() {
		$(".users").val(null).trigger("change");
	});


	$(document).ready(function(){
		// console.log($('#products :selected').text());
		if($('.products').val()==''){
			$('.p_div').hide();
		}else if($('.products').val()!=''){
			$('.p_div').show();
		}

		if($('.users').val()==''){
			$('.u_div').hide();
		}else if($('.users').val()!=''){
			$('.u_div').show();
		}

		$('.products,.users').select2({
			placeholder: 'انتخاب کنید...',
			// allowClear: true,
			language: {
				noResults: function () {
					return "موردی یافت نشد";
				}
			},
			closeOnSelect: false,
		});



		$('#dp_code').hide();
		window.addEventListener('click', function(e){
			if (document.getElementById('dp_code').contains(e.target)){
			} else{
				$('#dp_code').hide();
			}
		});



		$('#email_result')
		$('#code').keyup(function() {

			$("#vvv").html('');

			function load_data(query) {
				$.ajax({
					url: "<?php echo base_url(); ?>admin/search_code",
					method: "POST",
					data: {query: query,},
					success: function (data) {
						if (data == 0) {
							$('#dp_code').hide();
						} else {
							$('#dp_code').show();
							$('#dp_code').html(data);


						}

					}
				});
			}

			var code = $('#code').val();
			var id_off = $('#id').val();


			if(code != '')
			{
				load_data(code);
				$.ajax({
					url:"<?php echo base_url(); ?>admin/check_code2",
					method:"POST",
					data:{code:code,id_off:id_off},
					success:function(data){
						$('#email_result').html(data);
					}
				});
			}else{
				load_data();
				$('#email_result').html('');
			}

		});




		//$('#email_result2')
		//$('#mablagh').keyup(function(){
		//	var mablagh = $('#mablagh').val();
		//	if(mablagh != '')
		//	{
		//		$.ajax({
		//			url:"<?php //echo base_url(); ?>//admin/check_mablagh",
		//			method:"POST",
		//			data:{mablagh:mablagh},
		//			success:function(data){
		//				$('#email_result2').html(data);
		//			}
		//		});
		//	}else{
		//		$('#email_result2').html('');
		//	}
		//});
	});


	$('.positive').keydown( function(e) {
		if(!((e.keyCode > 95 && e.keyCode < 106)
			|| (e.keyCode > 47 && e.keyCode < 58)
			|| e.keyCode == 8)) {
			return false;
		}
	});



	$('#code').on("input",function() {

		//چک کردن حروف فارسی. جلوگیری از اسپیس اول. جلوگیری از اسپیس های چنتایی
		this.value = this.value.replace(/\s*/g,"");


	});




</script>
