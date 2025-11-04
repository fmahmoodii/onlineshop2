<?php if($this->session->userdata('id')){ ?>

<div class="container-fluid" id="content" style="padding: 20px 0 70px 0">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">

			<span style="font-weight: bold">وضعیت:</span>
			<?php foreach ($shopping_cart_order as $ord){ echo $ord->status;break;} ?>
			<br>
			<br>
			<br>

			<table class="table table-bordered table-striped" >
				<thead>
				<tr style="padding: 20px;"  >
					<?php foreach ($info_fact as $inf){ ?>
						<td colspan="7" style="border: none;" >
							<span style="font-weight: bold">کد پیگیری:</span>
							<span style="font-weight: bold"><?php echo $ord->order_code?></span><br><hr>
							<span>گیرنده:</span>
							<span><?php echo $inf->name?></span><br>
							<span>آدرس:</span>
							<span><?php echo $inf->address?></span><br>
							<span>کدپستی:</span>
							<span><?php echo $inf->postal_code?></span><br>
							<span>تلفن:</span>
							<span><?php echo $inf->phone_number?></span><br>
						</td>

					<?php } ?>
				</tr>
				<tr>
					<th >نام محصول</th>
					<th >تعداد</th>
					<th >قیمت واحد</th>
					<th >مجموع</th>
					<th >درصد تخفیف</th>
					<th >مبلغ تخفیف</th>
					<th >قیمت نهایی</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($shopping_cart_order as $ord){  ?>
					<input type="hidden" id="id" value="<?php echo $ord->order_code?>">

					<tr>
						<td><?php echo $ord->name?></td>
						<td><?php echo $ord->qty?></td>
						<td><?php echo number_format($ord->price)?></td>
						<td><?php echo number_format($ord->total_price1)?></td>
						<td><?php echo number_format($ord->off_percent)?></td>
						<td><?php echo number_format($ord->off_price)?></td>
						<td><?php echo number_format($ord->total_price)?></td>
					</tr>
				<?php }?>

				<tr>
					<td colspan="6" >جمع کل</td>
					<td><?php echo number_format($ord->final_price)?></td>
				</tr>
				<tr>
					<td colspan="6" >کد تخفیف</td>
					<td><?php echo $ord->off_code?></td>
				</tr>
				<tr>
					<td colspan="6" >پرداخت شده</td>
					<td><?php echo number_format($ord->payment)?></td>
				</tr>
				</tbody>
			</table>
			<br>

			<button onclick="window.print();return false" class="btn btn-info">چاپ</button>
			<a href="<?php echo base_url()?>admin/sales" ><button class="btn btn-danger">بازگشت</button></a>
		</div>
		<div class="col-md-2"></div>

	</div>

</div>
<?php }?>
<script>
	$(document).on('click', '#accept', function(e) {
		var status = $('#status').val();
		var id = $('#id').val();
		$.ajax({
			url: "<?php echo base_url(); ?>admin/status",
			method: "POST",
			data: {'status':status,'id':id},
			success:
					function(data) {
						alert('ثبت شد')
						window.location.reload();
					}

		})
		;

	});

</script>
