
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
	@media only screen and (max-device-width: 750px) {
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
		.srch_inpt {
			padding: 8px;
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			position: absolute;
			margin: 53px 0px 0 0;
			width: 175px;
		}

		.srch_fa {
			z-index: 9;
			position: relative;
			right: 152px;
			top: 57px;
			color: #e11c1c;
			font-size: 15px;
			line-height: 27px;
		}

		.div_dirc {
			direction: ltr
		}

		.upnl_a {
			color: #5e5e5e;
		}

		.upnl_but {
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			padding-top: 6px;
			margin: 53px 0 0 0;
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
			right: 150px;
			top: 60px;
			color: #e11c1c;
			font-size: 16px;
			line-height: 27px;
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
		.srch_inpt {
			padding: 8px;
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			position: absolute;
			margin: 53px 0px 0 0;
			width: 175px;
		}

		.srch_fa {
			z-index: 9;
			position: relative;
			right: 152px;
			top: 57px;
			color: #e11c1c;
			font-size: 15px;
			line-height: 27px;
		}

		.div_dirc {
			direction: ltr
		}

		.upnl_a {
			color: #5e5e5e;
		}

		.upnl_but {
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			padding-top: 5px;
			margin: 53px 0 0 0;
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
			right: 150px;
			top: 60px;
			color: #e11c1c;
			font-size: 16px;
			line-height: 27px;
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
				<img  class="logo_img" src="assets/images/log.png">
			</a>
		</div>
		<div class="col-md-5"></div>
		<!--search input-->
		<div class="col-md-2">

			<input id="search_input" class="srch_inpt" type="text" placeholder="جستجو..">

			<i id="i" class="fa fa-search srch_fa" aria-hidden="true"></i>


		</div>
		<script>
			$('#search_input').click(function () {
				window.location.href="<?php base_url()?>search"
			});
		</script>
		<!--sign-up/sign-in-->
		<div class="col-md-3 div_dirc">
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

	</div>
</div>

<!--space-->
<div class="div_space"></div>


