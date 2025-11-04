
<style>
	@media only screen and (max-device-width: 750px) {
		/* موبایل  */
		.div_con{padding-top: 150px;}
		.table_pdng{padding: 20px;}
	}
	@media only screen and (min-device-width: 750px ) {
		/* pc  */
		.div_con{padding-top: 150px;}
		.table_pdng{padding: 20px;}
	}

</style>

<div class="container-fluid div_con">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<table class="table table-bordered table-striped" >
				<thead>

				<tr class="table_pdng" >
					<?php foreach ($info_fact as $inf){ ?>
						<td colspan="4" style="border: none;" >
							<span>وضعیت:</span>
							<span>
							<?php foreach ($shopping_cart_order as $ord){echo $ord->status; break;}?>
							</span><br><br>
							<span>گیرنده:</span>
							<span><?php echo $inf->name?></span><br>
							<span>آدرس:</span>
							<span><?php echo $inf->address?></span><br>
							<span>کدپستی:</span>
							<span><?php echo $inf->postal_code?></span><br>
							<span>تلفن:</span>
							<span><?php echo $inf->phone_number?></span><br>
						</td>
						<td style="border: none;"></td>
						<td style="border: none;"></td>
						<td style="border: none;"></td>

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
					<tr>
						<td><?php echo $ord->name?></td>
						<td><?php echo $ord->qty?></td>
						<td><?php echo number_format($ord->price)?></td>
						<td><?php echo number_format($ord->total_price1)?></td>
						<td><?php echo $ord->off_percent?></td>
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
		</div>
		<div class="col-md-2"></div>
	</div>
</div>
