

<!--space-->
<div style="height: 150px"></div>

<!--category1-->
<div class="container" >
    <div class="row" style="padding-top: 30px">
		<?php  foreach ($products as $p){ foreach ($category1 as $cat1){ if($p->id_cat1==$cat1->id){?>
		<div class="col-md-3" style="padding-bottom: 35px">
			<a href="<?php echo base_url('home/product_detail/').$p->id ?>">
			<article style="height:auto;margin: 0 auto;background: #fff;
                direction: rtl;border-radius: 7px;padding: 20px; box-shadow: 0px 0px 10px 0px #cec9c9;text-align: center; ">
				<img style=" width:100%; height:220px"
					 src="<?php foreach ($images as $img){ if ($img->user_id==$p->code){
						 echo base_url().$img->direction;break;}} ?>">
				<h4 style="height: auto; clear: both;
                    margin: 15px auto;display: block; ;
                    overflow: hidden;text-overflow: ellipsis;text-align: center">
					<a href="<?php echo base_url('home/product_detail/').$p->id ?>">
						<?php echo $p->name?>
					</a>
				</h4>
			<?php foreach ($attrs as $t){if($t->code_p==$p->code){?>
				<h5 style="text-align: center;line-height: 22px;">
					<span style="font-weight: bold; display: inline">قیمت:</span>
					<span><?php echo number_format($t->price)?></span>
				</h5>
			<?php }}?>
			</article>
			</a>
		</div>
		<?php }}}?>
    </div><br>
</div>


