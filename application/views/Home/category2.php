<style>
	@media only screen and (max-device-width: 750px) {
		/* موبایل  */
		.row_pdng{padding-top: 130px;}
		.div_pdng{padding-bottom: 35px;}
		.artcl{height:auto;margin: 0 auto;background: #fff;
			direction: rtl;border-radius: 7px;padding: 20px; box-shadow: 0px 0px 10px 0px #cec9c9;
			text-align: center;}
		.artcl_img{width:100%; height:220px;}
		.artcl_h4{height: auto; clear: both;
			margin: 15px auto;display: block;
			overflow: hidden;text-overflow: ellipsis;text-align: center;}
		.artcl_h5{text-align: center;line-height: 22px;}
		.artcl_spn{font-weight: bold; display: inline;}
	}
	@media only screen and (min-device-width: 750px ) {
		/* pc  */
		.row_pdng{padding-top: 130px;}
		.div_pdng{padding-bottom: 35px;}
		.artcl{height:auto;margin: 0 auto;background: #fff;
			direction: rtl;border-radius: 7px;padding: 20px; box-shadow: 0px 0px 10px 0px #cec9c9;
			text-align: center;}
		.artcl_img{width:100%; height:220px;}
		.artcl_h4{height: auto; clear: both;
			margin: 15px auto;display: block;
			overflow: hidden;text-overflow: ellipsis;text-align: center;}
		.artcl_h5{text-align: center;line-height: 22px;}
		.artcl_spn{font-weight: bold; display: inline;}
	}

</style>

<!--category1-->
<div class="container-fluid">
	<div class="row row_pdng">
		<?php $i=0; foreach ($products as $p){ foreach ($category2 as $cat2){
			if($p->id_cat2==$cat2->id){ $i++; ?>

		<div class="col-md-3 div_pdng">
			<a href="<?php echo base_url('home/product_detail/').$p->id ?>">
			<article class="artcl">
				<img class="artcl_img" src="<?php foreach ($images as $img){ if ($img->user_id==$p->code){
						 echo base_url().$img->direction;break;}} ?>">

				<h4 class="artcl_h4">
					<a id="name_<?php echo $p->id?>" href="<?php echo base_url('home/product_detail/').$p->id ?>">
						<?php echo $p->name?>
					</a>
				</h4>
				<?php foreach ($attrs as $t){if($t->code_p==$p->code){?>
				<h5 class="artcl_h5">
					<span class="artcl_spn">قیمت:</span>
					<span><?php echo number_format($t->price)?> تومان</span>
				</h5>

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
				<?php }}}?>

			</article>
			</a>
		</div>

			<?php }}}?>


		<?php if ($i<=0){ ?>
		<div class="container" style="margin-top: 50px">
		<div class="row">
			<div class="alert alert-success" style="text-align: center;">
				در این دسته محصولی موجود نیست
			</div>
		</div>
		</div>

		<?php }?>


	</div><br>
</div>



