<?php if($this->session->userdata('id')){ ?>

<div class="container-fluid" id="content">
	<div class="row" style="margin: 60px; ">
		<div style="border: 1px solid #ccc; padding: 20px ; border-radius: 10px; ">
			<form id="form" method="post" action='<?php echo base_url('admin/add_inv_attr') ?>' onsubmit="return validateForm()" >
				<div class="form-group">
					<label>از انبار</label>
					<?php echo form_error('inv_from','<span style="color: red">','</span>') ?>
					<select onfocus='this.size=3;' onblur='this.size=1;' onchange='this.size=1; this.blur();'
							id="inv_from" name="inv_from" class="form-control">
						<option selected value="0">انتخاب کنید..</option>
						<?php foreach ($inventory as $inv){?>
							<option value="<?php echo $inv->id;?>">
								<?php echo $inv->name;?>
							</option>
						<?php }?>
					</select>
				</div>

				<div class="form-group">
					<label>به انبار</label>
					<?php echo form_error('inv_to','<span style="color: red">','</span>') ?>
					<select onfocus='this.size=3;' onblur='this.size=1;' onchange='this.size=1; this.blur();'
							id="inv_to" name="inv_to" class="form-control">
						<option selected value="0">انتخاب کنید..</option>
					</select>
				</div>

				<div class="form-group">
					<div style="width: 30%">
						<label for="date" class="required">تاریخ</label>
						<?php echo form_error('date','<span style="color: red">','</span>') ?>
						<input required readonly id="date" name="date" class="form-control">
						<br>
						<input hidden id="datetime2" name="datetime2" >
						<div class="datetime"></div>
					</div>
				</div>

				<div class="row" id="show_item" style="padding: 60px 20px 0 20px; display: grid;">

					<div class="form-group">
						<label>محصول</label>
						<?php echo form_error('aaa','<span style="color: red">','</span>') ?>
						<select onfocus='this.size=3;' onblur='this.size=1;' onchange='this.size=1; this.blur();'
								id="aaa" name="aaa" class="form-control">
							<option selected value="0">انتخاب کنید..</option>
						</select>
					</div>
					<div class="form-group">
						<label for="unit">واحد</label>
						<?php echo form_error('unit','<span style="color: red">','</span>') ?>
						<select id="unit" name="unit" class="form-control" name="unit">
							<option selected value="0"></option>
							<option value="متر">متر</option>
							<option value="عدد">عدد</option>
							<option value="جین">جین</option>
							<option value="حلقه">حلقه</option>
							<option value="کارتن">کارتن</option>
							<option value="کیلوگرم">کیلوگرم</option>
						</select>
					</div>
					<div class="form-group">
						<label for="qty">تعداد</label>
						<?php echo form_error('qty','<span style="color: red">','</span>') ?>
						<input autocomplete="off" id="qty" name="qty" min="1" type="number" class="form-control positive" placeholder="تعداد">
						<input hidden id="max" name="max">

					</div>
					<div class="form-group">
						<label for="buy_price">قیمت خرید</label>
						<?php echo form_error('buy_price','<span style="color: red">','</span>') ?>
						<input autocomplete="off" id="buy_price" name="buy_price" min="0" type="number" class="form-control positive" placeholder="قیمت خرید">

					</div>
					<div class="form-group">
						<label for="price">قیمت فروش</label>
						<?php echo form_error('price','<span style="color: red">','</span>') ?>
						<input autocomplete="off" id="price" name="price" min="0" type="number" class="form-control positive" placeholder="قیمت فروش">

					</div>
					<div class="form-group">
						<label for="details">توضیحات</label>
						<?php echo form_error('details','<span style="color: red">','</span>') ?>
						<input autocomplete="off" id="details" name="details" type="text" class="form-control" placeholder="توضیحات">

					</div>
					<button id="submit" class="btn btn-success" style="width: 250px">
						افزودن
					</button>
					<br>

				</div>
			</form>

				<br>
				<br>


		</div>
	</div>
</div>
<?php }?>


<script>
	$(document).ready(function(){

		x = $('.datetime').persianDatepicker({
			inline: true,
			observer: true,
			altField: '#date',
			altFormat: 'LLLL',
			calendarType: 'persian',
			initialValue: false,
			// initialValueType: 'persian',
			toolbox:{
				calendarSwitch:{
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
			// maxDate: new persianDate().add('month', 3).valueOf(),
			// minDate: new persianDate().subtract('month', 3).valueOf(),
			timePicker: {
				enabled: true,
				meridiem: {
					enabled: true
				}
			},
			responsive: true,
			onSelect: function (unix) {
				// to=$('#calenderAlt').val();
				console.log($('#calenderAlt').val());
				const state = x.getState();
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
				console.log($('#datetime2').val());

			}

		});




	});


	$(document).on('change', '#inv_from', function(e) {
		var inv_from = $('#inv_from').val();
		$.ajax({
			url: "<?php echo base_url(); ?>admin/get_inventory",
			method: "POST",
			data: {
				'inv_from':inv_from,
			},
			success:
				function(result) {
				$('#inv_to').html(result);
			}
		});
		$.ajax({
			url: "<?php echo base_url(); ?>admin/get_inventory_supply",
			method: "POST",
			data: {
				'inv_from':inv_from,
			},
			success:
				function(data) {
					var obj = JSON.parse(data);
					$('#aaa').html(obj.result);

					console.log(obj.result)
				}
		});
		$('#unit').val('');
		$('#qty').val('');
		$('#buy_price').val('');
		$('#price').val('');

	});

	$(document).on('change', '#aaa', function(e) {
		var unit = $(this).find(':selected').attr('unit');
		var qty = $(this).find(':selected').attr('supply');
		var buy_price = $(this).find(':selected').attr('buy_price');
		var price = $(this).find(':selected').attr('price');
		$('#unit').val(unit);
		$('#qty').val(qty);
		$('#max').val(qty);
		$('#qty').attr('max',qty);
		$('#buy_price').val(buy_price);
		$('#price').val(price);

	});



</script>
