
<head>
	<meta charset="UTF-8">
	<title><?php echo $title;?></title>
	<link href="<?php echo base_url()?>assets/css/style.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/css/bootstrap.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/css/bootstrap-rtl.min.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/css/Structure.css" rel="stylesheet" >

	<link href="<?php echo base_url()?>assets/OwlCarousel2-2.3.4/dist/assets/owl.carousel.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url()?>assets/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css" rel="stylesheet" type="text/css">

	<script src="<?php echo base_url()?>assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url()?>assets/js/bootstrap.min.js"></script>
	<script src="<?php echo base_url()?>assets/owl.carousel/js/owl.carousel.min.js"></script>
	<script src="<?php echo base_url()?>assets/js/owl.js"></script>

</head>

<style>
	option:checked { display:none; }
	.required:before {
		content:"* ";
		color: red;
	}
	@media only screen and (max-device-width: 750px ) {
		/* موبایل  */
		.header_con {
			position: fixed;
			z-index: 999;
			background: white;
			top: 0;
			right: 0;
			left: 0;
			margin: 0 auto;
			border-bottom: 1px solid #ccc;
			box-shadow: -2px 2px 19px #ccc;
		}

		.logo_pdng {
			padding: 45px;
		}

		.logo_img {
			height: 50px;
			width: 70px;
		}

		.drpdwn_pdng {
			padding: 60px 0 0 0;
		}

		.div_dirc {
			direction: ltr
		}

		.login_a {
			color: #5e5e5e;
		}

		.login_but {
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			padding-top: 6px;
			margin: 53px 0 0 0;
		}

		.login_spn {
			font-weight: bolder;
			margin-left: 20px;
		}

		.login_fa {
			cursor: pointer;
			z-index: 9;
			position: relative;
			right: 45px;
			top: -17px;
			color: #e11c1c;
			font-size: 16px;
		}

		.upnl_a {
			color: #5e5e5e;
		}

		.upnl_but {
			padding: 8px;
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			margin: 53px 8px 0 0;
		}

		.upnl_spn {
			font-weight: bolder;
		}

		.logout_a {
			color: #5e5e5e;
		}

		.logout_but {
			padding: 8px;

			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 80px;
			margin: 53px 0 0 0;
		}

		.logout_spn {
			font-weight: bolder;
			margin-right: -18px;
		}

		.logout_fa {
			cursor: pointer;
			z-index: 9;
			position: absolute;
			right: 145px;
			top: 57px;
			color: #e11c1c;
			font-size: 16px;
			line-height: 27px;
		}

		.div_w {
			width: 12%;
		}

		.shop_a {
			color: #5e5e5e;
		}

		.shop_but {
			padding: 8px;
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			margin: 53px 0 0 0;
		}

		.shop_spn {
			font-weight: bolder;
			margin-right: -18px;
		}

		.drpdwn_pdng {
			padding: 0 20px 20px 20px;
		}

		.div_space {
			height: 130px;
		}

	}
	@media only screen and (min-device-width: 750px ) {
		/* pc  */
		.header_con {
			position: fixed;
			z-index: 999;
			background: white;
			top: 0;
			right: 0;
			left: 0;
			margin: 0 auto;
			border-bottom: 1px solid #ccc;
			box-shadow: -2px 2px 19px #ccc;
		}

		.logo_pdng {
			padding: 45px;
		}

		.logo_img {
			height: 50px;
			width: 70px;
		}

		.drpdwn_pdng {
			padding: 60px 0 0 0;
		}

		.div_dirc {
			direction: ltr
		}

		.login_a {
			color: #5e5e5e;
		}

		.login_but {
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			padding-top: 6px;
			margin: 53px 0 0 0;
		}

		.login_spn {
			font-weight: bolder;
			margin-left: 20px;
		}

		.login_fa {
			cursor: pointer;
			z-index: 9;
			position: relative;
			right: 45px;
			top: -17px;
			color: #e11c1c;
			font-size: 16px;
		}

		.upnl_a {
			color: #5e5e5e;
		}

		.upnl_but {
			padding: 8px;
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			margin: 53px 8px 0 0;
		}

		.upnl_spn {
			font-weight: bolder;
		}

		.logout_a {
			color: #5e5e5e;
		}

		.logout_but {
			padding: 8px;

			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 80px;
			margin: 53px 0 0 0;
		}

		.logout_spn {
			font-weight: bolder;
			margin-right: -18px;
		}

		.logout_fa {
			cursor: pointer;
			z-index: 9;
			position: absolute;
			right: 145px;
			top: 57px;
			color: #e11c1c;
			font-size: 16px;
			line-height: 27px;
		}

		.div_w {
			width: 12%;
		}

		.shop_a {
			color: #5e5e5e;
		}

		.shop_but {
			padding: 8px;
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			margin: 53px 0 0 0;
		}

		.shop_spn {
			font-weight: bolder;
			margin-right: -18px;
		}

		.drpdwn_pdng {
			padding: 0 20px 20px 20px;
		}

		.div_space {
			height: 130px;
		}

	}

</style>


<!--header-->
<div class="container-fluid header_con">

	<div class="row">
		<!--logo-->
		<div class="col-md-1 logo_pdng">
			<a href="<?php echo base_url()?>">
				<img class="logo_img" src="assets/images/logo.png">
			</a>
		</div>
		<!--dropdown-menu-->
		<div class="col-md-4 drpdwn_pdng">

		</div>

		<!--search input-->
		<div class="col-md-2">
		</div>

		<!--sign-up/sign-in-->
		<div class="col-md-3 div_dirc">
			<?php if(!$this->session->userdata('user_id')){ ?>
				<a class="login_a" href="<?php echo base_url()?>home/login_form">
					<button class="login_but">
						<span class="login_spn">ورود | ثبت نام</span>
						<i class="fa fa-user login_fa" aria-hidden="true"></i>
					</button>
				</a>
			<?php } ?>
			<?php if($this->session->userdata('user_id')){ ?>
				<a class="upnl_a" href="<?php echo base_url()?>home/user_panel">
					<button class="upnl_but">
						<span class="upnl_spn">پنل کاربری</span>
					</button>
				</a>
				<a class="logout_a" href="<?php echo base_url()?>home/logout">
					<button class="logout_but">
						<span class="logout_spn">خروج</span>
						<i class="fa fa-sign-out logout_fa" aria-hidden="true"></i>
					</button>
				</a>
			<?php } ?>
		</div>
		<!--shopping card-->
		<div class="col-md-1 div_w">

			<a class="shop_a" href="<?php echo base_url()?>home/shopping_cart">
				<button class="shop_but">
                <span class="shop_spn">سبد خرید</span>
				</button>
			</a>
			<span id="cardno" style="width: 25px;
    height: 25px;
    text-align: center;
    line-height: 28px;
    font-size: 18px;
    background-color: #d23030;
    color: #fff;
    position: absolute;
    top: 40px;
    border-radius: 50%;
    left: 25px;"> 0 </span>

		</div>
	</div>

</div>

<!--space-->
<div class="div_space"></div>

<script>
	$(document).on('click', '#shopping_cart', function(e) {
		var id_p=$(this).attr("id_p");
		var id_attr=$(this).attr("id_attr");
		var qty=$('#qty_'+id_attr).val();
		var name=$('#name_'+id_p).text();
		if (qty>0){
			$.ajax({
				url: "<?php echo base_url(); ?>home/insert_shopping_cart",
				method:"POST",
				data:{id_p:id_p, id_attr:id_attr, qty:qty, name:name,},
				success:
					function(data) {
						$('#cart_ajax').append(data);
						$('#qty_'+id_attr).val('');
						alert(qty+' عدد ازین محصول به سبد خرید اضافه شد');
					}
			});
		}else {
			alert('تعداد را وارد نمایید')
		}


	});
	//$(document).on('click', '#shopping_cart', function(e) {
	//	var id_p=$(this).attr("id_p");
	//	var qty=$('#qty_'+id_p).val();
	//	var name=$('#name_'+id_p).text();
	//	if (qty>0){
	//		$.ajax({
	//			url: "<?php //echo base_url(); ?>//home/insert_shopping_cart",
	//			method:"POST",
	//			data:{id_p:id_p, qty:qty, name:name,},
	//			success:
	//				function(data) {
	//					$('#cart_ajax').append(data);
	//					$('#qty_'+id_p).val('');
	//					alert(qty+' عدد ازین محصول به سبد خرید اضافه شد');
	//				}
	//		});
	//	}else {
	//		alert('تعداد را وارد نمایید')
	//	}
	//
	//
	//});


</script>
