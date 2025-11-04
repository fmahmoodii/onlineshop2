
<style>
	@media only screen and (max-device-width: 750px) {
		/* موبایل  */
		.div_con{padding-top: 130px;}
		.table_pdng{padding: 20px;}
		.txt_align{text-align: center;}
	}
	@media only screen and (min-device-width: 750px ) {
		/* pc  */
		.div_con{padding-top: 130px;}
		.table_pdng{padding: 20px;}
		.txt_align{text-align: center;}
	}

</style>
<div class="container-fluid div_con">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8 table_pdng">
			<table class="table table-bordered table-striped text-center">
				<thead>
				<tr>
					<th class="txt_align">شناسه خرید:</th>
					<th class="txt_align">جمع قیمت:</th>
					<th class="txt_align">وضعیت:</th>
					<th class="txt_align">جزئیات:</th>
				</tr>
				</thead>
				<tbody>

				</tbody>
				<?php foreach ($shopping_cart_order as $ord){ foreach ($info_fact as $info){if ($info->ord_code == $ord->order_code){?>
					<tr>
						<td id="id"><?php echo $ord->order_code?></td>
						<td><?php echo number_format($ord->payment)?></td>
						<td><?php echo $ord->status?></td>
						<td>
							<a href="<?php echo base_url('home/f_detail/'.$ord->order_code)?>">
								<button id="detail" class="btn btn-info">جزئیات سفارش</button>
							</a>
						</td>
					</tr>

				<?php }}}?>

				</tbody>
			</table>
		</div>
		<div class="col-md-2"></div>
	</div>
</div>
