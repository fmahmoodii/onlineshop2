<?php if($this->session->userdata('id')){ ?>
	<div class="container-fluid">

		<div style="position: relative; top: -45px">
			<a>
				<button id="btn" class="btn btn-default" style="margin-right: 40px">
					<i class="fa fa-navicon"></i>
				</button>

				<button id="btn2" class="btn btn-default" style="margin-right: 200px">
					<i class="fa fa-navicon"></i>
				</button>

			</a>
		</div>

		<div id="1" class="row" style="width: 44px;height: 100%;
    position: fixed;
    top: 0;
    right: 0;
    display: inline-block;
    z-index: 9;
    background-color: #3d3939;"></div>

		<div id="nav" class="row" style="width: 200px;height: 100%;
    position: fixed;
    top: 0;
    right: 0;
    display: inline-block;
    z-index: 9;
    overflow-x: hidden;overflow-y: scroll ">

			<ul id="list" style="">
				<li id="website" style="width: 200px;height: 60px;display: flex;font-size: 18px;
            align-items: center;justify-content: center;background-color: #5e5e5e;
            border: 1px solid #4e4e4e;position: relative;left: 40px;">وبسایت</li>
				<a href="<?php echo base_url()?>admin"><li id="dashboard" >داشبورد</li></a>
<!--				<a href="--><?php //echo base_url()?><!--admin/category_test2"><li id="category3" >دسته بندی محصول-تستی3</li></a>-->
				<a href="<?php echo base_url()?>admin1/categories"><li id="category3" >دسته بندی محصول-تستی3</li></a>
				<a href="<?php echo base_url()?>admin/category"><li id="category" >دسته بندی محصول</li></a>
				<ul id="list2" class="ul2">
					<a href="<?php echo base_url()?>admin/category1"><li>سطح 1</li></a>
					<a href="<?php echo base_url()?>admin/category2"><li>سطح 2</li></a>
				</ul>
				<a href="<?php echo base_url()?>admin/insert_product"><li id="insert_product" >محصول گذاری</li></a>
				<ul id="list2" class="ul2">
					<a href="<?php echo base_url()?>admin/model"><li>مدل</li></a>
					<a href="<?php echo base_url()?>admin/jens"><li>جنس</li></a>
					<a href="<?php echo base_url()?>admin/size"><li>سایز</li></a>
					<a href="<?php echo base_url()?>admin/brand"><li>برند</li></a>
					<a href="<?php echo base_url()?>admin/color"><li>رنگ</li></a>
				</ul>
				<a href="<?php echo base_url()?>admin/off_code"><li id="off_code">کد تخفیف</li></a>
				<a href="<?php echo base_url()?>admin/rules"><li id="rules" >قوانین و مقررات</li></a>
				<a href="<?php echo base_url()?>admin/products"><li id="products">محصولات</li></a>
				<a href="<?php echo base_url()?>admin/footer"><li id="footer" >فوتر</li></a>
				<a href="<?php echo base_url()?>admin/sales"><li id="sales" >اطلاعات فروش</li></a>
				<a href="<?php echo base_url()?>admin/comments" id="a-comments"><li id="comments">نظرات کاربران</li></a>
				<a href="<?php echo base_url()?>admin/registered_users"><li id="registered_users" >لیست کاربران</li></a>
				<a href="<?php echo base_url()?>admin/inventory"><li id="inventory" style="width: 200px;height: 40px;display: flex;
            align-items: center;justify-content: center;
            border: 1px solid #4e4e4e;position: relative;left: 40px;">انبارداری</li></a>
				<ul id="list2" class="ul2">
					<a href="<?php echo base_url()?>admin/inventory_attrs"><li>انبار</li></a>
					<a href="<?php echo base_url()?>admin/inventory"><li>لیست انبار</li></a>
					<a href="<?php echo base_url()?>admin/attributes"><li>لیست موجودی</li></a>
				</ul>
			</ul>
			<script>
				$('#list a li').mouseover(function () {
					$(this).css({'background-color':'white'});
				}).mouseleave(function () {
					$(this).css({'background-color':'#3d3939'});
				});
				$('#list2 a li').mouseover(function () {
					$(this).css({'background-color':'white'});
				}).mouseleave(function () {
					$(this).css({'background-color':'#5d5757'});
				});
			</script>

		</div>


	</div>

<?php }?>

<script>
	$(document).ready(function () {
		$('#nav').show();
		$('#1').hide();
		$('#btn2').show();
		$('#btn').hide();
		$('#content').css({'padding-right':'150px'});
	});
	$('#btn').click(function(){
		$('#nav').show();
		$('#1').hide();
		$('#btn2').show();
		$('#btn').hide();
		$('#content').css({'padding-right':'150px'});
	});
	$('#btn2').click(function(){
		$('#nav').hide();
		$('#1').show();
		$('#btn2').hide();
		$('#btn').show();
		$('#content').css({'padding-right':'0px'});

	});


</script>
