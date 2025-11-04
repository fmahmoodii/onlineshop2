<style>
	@media only screen and (max-device-width: 750px) {
		/* موبایل  */
		.row_pdng{padding-top: 130px;}
		.div_pdng{padding-bottom: 35px;}
		.artcl{margin: 0 auto;background: #fff;
			direction: rtl;border-radius: 7px;padding: 10px; box-shadow: 0px 0px 10px 0px #cec9c9;}
		.artcl_a{position: relative;}
		.artcl_img{width:100%; height:150px;}
		.artcl_div{padding: 0 10px 10px 10px;}
		.artcl_h4{height: auto;margin: 30px auto;display: block;}
		.div_prc{text-align: left;}
		.prc_spn{font-weight: bold; font-size: 16px; color: #777;}
		.prc{font-size: 12px;color: #777;}
		.div_br{text-align: center;}
	}
	@media only screen and (min-device-width: 750px ) {
		/* pc  */
		.row_pdng{padding-top: 130px;}
		.div_pdng{padding-bottom: 35px;}
		.artcl{margin: 0 auto;background: #fff;
			direction: rtl;border-radius: 7px;padding: 10px; box-shadow: 0px 0px 10px 0px #cec9c9;}
		.artcl_a{position: relative;}
		.artcl_img{width:100%; height:150px;}
		.artcl_div{padding: 0 10px 10px 10px;}
		.artcl_h4{height: auto;margin: 30px auto;display: block;}
		.div_prc{text-align: left;}
		.prc_spn{font-weight: bold; font-size: 16px; color: #777;}
		.prc{font-size: 12px;color: #777;}
		.div_br{text-align: center;}
	}

</style>
<!--bookmarks-->
<div class="container">
    <div class="row row_pdng">
		<?php foreach ($products as $p){foreach ($bookmarks as $b){if($p->id==$b->id_p){?>

        <div class="col-md-3 div_pdng">
				<article class="artcl">
					<input id="id_p" value="<?php echo $p->id?>" type="hidden">
					<a class="artcl_a" href="<?php echo base_url().'home/product_detail/'.$p->id.'/'.$p->code?>">
						<img class="artcl_img" src="5.jpg">
					</a>
					<div class="artcl_div">
						<h4 class="artcl_h4">
							<a id="name_<?php echo $p->id?>" href="<?php echo base_url('home/product_detail/').$p->id.'/'.$p->code ?>">
								<?php echo $p->name?>
							</a>
						</h4>
						<?php foreach ($attrs as $t){if($t->code_p==$p->code){?>
						<div class="div_prc">
							<span class="prc_spn" style=""><?php echo number_format($t->price)?></span>
							<span class="prc">تومان</span>
						</div>
						<?php }}?>
						<b style="font-size: 16px; margin-left: 25px;line-height: 36px">تعداد:</b><br>
						<input id="qty_<?php echo $p->id?>"
							   type="number" step="1" min="1" max="100" value="1" size="4"
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
						<?php }?>
						<div class="div_br">
                        <br>
						</div>
					</div>
				</article>
		</div>
		<?php }}}?>
    </div><br>

</div>
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
		$.post('<?php  echo base_url();?>home/delete_bookmark', {'id_p': id,},
				function (data) {
					if(data==1){

					}
				}
		);
	});

</script>
