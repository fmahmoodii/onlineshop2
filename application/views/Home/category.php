

<!--space-->
<div style="height: 200px"></div>

<?php foreach ($products as $p){?>
	<input id="id_p" value="<?php echo $p->id?>" type="hidden">
	<input id="code_p" value="<?php echo $p->code?>" type="hidden">

<div id="category-tabs">
	<button class="tab-btn active" data-cat-id="<?= $id ?>">نمایش همه</button>

	<?php foreach ($subcategories as $subcat): ?>
		<button class="tab-btn" data-cat-id="<?= $subcat->id ?>">
			<?= htmlspecialchars($subcat->name_cat) ?>
		</button>
	<?php endforeach; ?>



	<!--category-->
	<div class="container" >
		<div id="products-list" class="row" style="padding-top: 30px">
			<?php  foreach ($products as $p){ foreach ($category as $cat){ if($p->id_cat2==$cat->id){?>
				<div class="col-md-3" style="padding-bottom: 35px">
					<a href="<?php echo base_url('home/product_detail/').$p->id.'/'.$p->code ?>">
						<article style="height:auto;margin: 0 auto;background: #fff;
                direction: rtl;border-radius: 7px;padding: 20px; box-shadow: 0px 0px 10px 0px #cec9c9;text-align: center; ">
							<img style=" width:100%; height:220px"
								 src="<?php foreach ($images as $img){ if ($img->user_id==$p->code){
									 echo base_url().$img->direction;break;}} ?>">
							<h4 style="height: auto; clear: both;
                    margin: 15px auto;display: block; ;
                    overflow: hidden;text-overflow: ellipsis;text-align: center">
								<a href="<?php echo base_url('home/product_detail/').$p->id.'/'.$p->code ?>">
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
</div>
<?php break;}?>


<script>
	$(document).ready(function(){

		$('.tab-btn').click(function(){
			$('.tab-btn').removeClass('active');
			$(this).addClass('active');

			let cat_id = $(this).data('cat-id');

			$.ajax({
				url: '<?= base_url("home/get_products_by_category") ?>',
				type: 'POST',
				data: {cat_id: cat_id},
				success: function(response) {
					$('#products-list').html(response);
				},
				error: function() {
					$('#products-list').html('<p>خطا در بارگذاری محصولات.</p>');
				}
			});

		});

	});
</script>

<style>
	#category-tabs {
		margin-bottom: 20px;
	}
	.tab-btn {
		background-color: #ddd;
		border: none;
		padding: 10px 15px;
		cursor: pointer;
		margin-right: 5px;
		border-radius: 4px;
		transition: background-color 0.3s;
	}
	.tab-btn:hover {
		background-color: #ccc;
	}
	.tab-btn.active {
		background-color: #888;
		color: white;
	}
	.product-item {
		padding: 10px;
		border-bottom: 1px solid #eee;
	}
</style>




