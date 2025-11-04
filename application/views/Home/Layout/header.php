<head>
	<meta charset="UTF-8">
	<title><?php echo $title;?></title>
	<link href="<?php echo base_url()?>assets/css/style.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/css/bootstrap.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/css/bootstrap-rtl.min.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/css/Structure.css" rel="stylesheet" >

	<link href="<?php echo base_url()?>assets/owl.carousel/css/owl.carousel.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url()?>assets/owl.carousel/css/owl.theme.default.min.css" rel="stylesheet" type="text/css">

	<script src="<?php echo base_url()?>assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url()?>assets/js/bootstrap.min.js"></script>
	<script src="<?php echo base_url()?>assets/owl.carousel/js/owl.carousel.min.js"></script>
	<script src="<?php echo base_url()?>assets/js/owl.js"></script>

	<script src="https://cdn.ckeditor.com/4.14.0/full/ckeditor.js"></script>
<!--	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->

</head>

<style>
	option:checked { display:none; }
	.required:before {
		content:"* ";
		color: red;
	}
	@media only screen and (max-device-width: 750px) {
		/* موبایل  */
		.pc_header{display: none;}
		.header_con{position: fixed; z-index: 999; background: white; top: 0;right: 0;left: 0;
			margin: 0 auto; border-bottom: 1px solid #ccc;box-shadow: -2px 2px 19px #ccc;}
		.logo_pdng{padding: 45px;}
		.logo_img{height: 50px; width: 70px;}
		.drpdwn_pdng{padding: 60px 0 0 0;}
		.srch_inpt{padding: 8px;
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			position: absolute;
			margin: 53px 0px 0 0;
			width: 175px;}
		.srch_fa{z-index: 9;
			position: relative;
			right: 152px;
			top: 57px;
			color: #e11c1c;
			font-size: 15px;
			line-height: 27px;}
		.div_dirc{direction: ltr}
		.login_a{color: #5e5e5e;}
		.login_but{border-radius: 25px;height: 35px;outline: unset;border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;width: 120px;padding-top: 6px;margin: 53px 0 0 0;}
		.login_spn{  font-weight: bolder;
			margin-left: 20px;}
		.login_fa{cursor: pointer;
			z-index: 9;
			position: relative;
			right: 45px;
			top: -17px;
			color: #e11c1c;
			font-size: 16px;}
		.upnl_a{color: #5e5e5e;}
		.upnl_but{padding: 8px;
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			margin: 53px 8px 0 0;}
		.upnl_spn{font-weight: bolder;}
		.logout_a{color: #5e5e5e;}
		.logout_but{padding: 8px;

			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 80px;
			margin: 53px 0 0 0;}
		.logout_spn{font-weight: bolder;margin-right: -18px;}
		.logout_fa{cursor: pointer;
			z-index: 9;
			position: absolute;
			right: 150px;
			top: 60px;
			color: #e11c1c;
			font-size: 16px;
			line-height: 27px;}
		.div_w{width: 12%;}
		.shop_a{color: #5e5e5e;}
		.shop_but{padding: 8px;
			border-radius: 25px;
			height: 35px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			width: 120px;
			margin: 53px 0 0 0;}
		.shop_spn{font-weight: bolder;
			margin-right: -18px;}
		.drpdwn_pdng{padding:0 20px 20px 20px;}
		.drpdwn_disp{display: inline;}
		.drpdwn_a{cursor:pointer;margin-left: 40px; color: #5e5e5e;}
		.drpdwn_ul{cursor:pointer;margin-right: 60px; padding: 10px;}
		.drpdwn_li{border-bottom: 1px solid #ccc; padding: 20px;}
		.div_space{height: 130px;}
		.drpdwn_ul_a{color: #5e5e5e;}
	}
	@media only screen and (min-device-width: 750px ) {
		/* pc  */
		#category-list li > a {
			color: #4b4b4b;
			text-decoration: none;
		}

		#category-list li > a:hover {
			background: #f8e1e1;
			color: #2d2d2d;
			text-shadow: 10px 10px 10px rgba(0,0,0,0.2);
		}


		#category-list li > a {
			display: inline-block;
			max-width: 80%;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
			vertical-align: middle;
		}

		/* لیست دسته‌بندی‌های سطح اول */
		#category-list {
			display: flex;
			gap: 20px;
			list-style: none;
			padding: 0;
			margin: 0;
			background: #f5f5f5;
			border: 1px solid #ccc;
			direction: rtl; /* راست به چپ */
		}

		/* هر دسته */
		#category-list li {
			position: relative;
			padding: 10px 15px;
			cursor: pointer;
			white-space: nowrap;
			background: #fff;
			border-radius: 4px;
			transition: background 0.2s;
		}

		#category-list li:hover {
			background: #e0e0e0;
		}

		/* منوی زیرشاخه سطح 0 (عمودی) */
		#category-list li > ul {
			display: none;
			position: absolute;
			top: 100%; /* قرار گرفتن زیر سطح 0 */
			left: 0;
			list-style: none;
			background: white;
			padding: 10px 0;
			margin: 0;
			border: 1px solid #ccc;
			min-width: 150px;
			z-index: 100;
		}

		/* منوی زیرشاخه‌های سطح 1 و بعدی (کنار فلش) */
		#category-list li ul {
			display: none;
			position: absolute;
			top: 0; /* به صورت کنار فلش */
			right: 100%; /* حرکت به سمت چپ فلش */
			list-style: none;
			background: white;
			padding: 10px 0;
			margin: 0;
			border: 1px solid #ccc;
			min-width: 150px;
			z-index: 100;
		}

		/* آیتم‌های زیرشاخه */
		#category-list li ul li {
			padding: 8px 15px;
			white-space: nowrap;
			background: #fff;
		}

		#category-list li ul li:hover {
			background: #f0f0f0;
		}

		/* فلش برای دسته‌هایی که زیرمنو دارند */
		.arrow {
			font-size: 12px;
			margin-left: 5px;
			color: #999;
			margin-right: 10px;
		}

		/* چرخش فلش به سمت چپ */
		li.open > .arrow {
			transform: rotate(-90deg); /* چرخش به سمت چپ */
			transition: transform 0.2s;
			margin-right: 10px;
		}

		/* نمایش پیام "دیتا ندارد" در صورت عدم وجود داده */
		.no-data {
			padding: 10px;
			color: #888;
			font-size: 14px;
			background: #f0f0f0;
		}

		ul.vertical {
			top: 100% !important;
			right: 0 !important;

		}

		/* لایه محو برای نمایش بارگذاری */
		#loading-overlay {
			display: none;  /* به‌صورت پیش‌فرض مخفی است */
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.5);  /* پس‌زمینه محو */
			color: white;
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 9999;  /* لایه بالا روی همه چیز */
			font-size: 20px;
			font-weight: bold;
		}



		.mobile_header{display: none;}
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
			top: 63px;
			color: #e11c1c;
			font-size: 15px;
			line-height: 27px;
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
			right: 164px;
			top: 60px;
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

		.drpdwn_disp {
			display: inline;
		}

		.drpdwn_a {
			cursor: pointer;
			margin-left: 40px;
			color: #5e5e5e;
		}

		.drpdwn_ul {
			cursor: pointer;
			margin-right: 60px;
			padding: 10px;
		}

		.drpdwn_li {
			border-bottom: 1px solid #ccc;
			padding: 20px;
		}

		.div_space {
			height: 130px;
		}

		.drpdwn_ul_a {
			color: #5e5e5e;
		}
	}

</style>

<!--Mobile_header-->
<div class="container-fluid" style="position: fixed;
    background-color: #fff;
    z-index: 999;
    width: 100%;
    top: 0;
border-bottom: 1px solid #ccc;box-shadow: -2px 2px 19px #ccc;">
	<div class="row" style="padding: 10px 5px;">
		<!--logo-->
		<div class="col-lg-1 col-md-2 col-sm-2" style="margin-bottom: 5px">
			<a href="<?php echo base_url()?>home">
				<img style="width: 85px;
				height: 43px;
    /*height: auto;*/
    border: 1px solid #ccc;
    object-fit: contain;" src="assets/images/logo.png">
			</a>
		</div>
		<!--search input-->
		<div class="col-lg-6 col-md-4 col-sm-4" style="margin-bottom: 5px">
			<a href="<?php echo base_url()?>home/search">
				<input style="
			padding: 0 5%;
			border-radius: 25px;
			height: 40px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			" id="search_input" class="" type="text" placeholder="جستجو1..">
				<i style="
			z-index: 999;
    position: relative;
    right: -30px;
    top: 2px;
    color: #e11c1c;
    font-size: 19px;
			" class="fa fa-search" aria-hidden="true"></i>
			</a>
		</div>

<!--		//space-->
<!--		<div class="col-lg-1 col-md-1 col-sm-1" style="-->
<!--		/*display: inline;margin-right:*/-->
<!--		--><?php ////if($this->session->userdata('user_id')){ echo "25%;"; }else{echo "30%;";}?>
<!--				"></div>-->
<!---->
<!---->
<!--		//shopping card-->
<!--		<div class="col-lg-2 col-md-2 col-sm-2" style="">-->
<!---->
<!---->
<!---->
<!---->
<!--		</div>-->

		<!--sign-up/sign-in-->
		<div class="col-lg-4 col-md-5 col-sm-5" style="direction:ltr;margin-bottom: 5px">

			<?php if(!$this->session->userdata('user_id')){ ?>
				<a style="color: #5e5e5e;" href="<?php echo base_url()?>home/login_form">
					<button style="
			border-radius: 25px;
			height: 40px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
			margin-right: 4px;
			">
						<span style="font-weight: bolder;">ورود | ثبت نام</span>
						<i style="
			cursor: pointer;
			z-index: 999;
			position: relative;
			top: 1px;
			color: #e11c1c;
			font-size: 20px;
			" class="fa fa-sign-in" aria-hidden="true"></i>
					</button>
				</a>
			<?php } ?>
			<?php if($this->session->userdata('user_id')){ ?>
				<a style="color: #5e5e5e;" href="<?php echo base_url()?>home/logout">
					<button style="border-radius: 25px;
			height: 40px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
margin-right: 4px;
			">
						<span style="
			font-weight: bolder;
			">خروج</span>
						<i style="
						   z-index: 999;
    position: relative;
    /* right: 0px; */
    top: 1px;
    color: #e11c1c;
    font-size: 20px;
						" class="fa fa-sign-out" aria-hidden="true"></i>
					</button>
				</a>
				<a style="color: #5e5e5e;" href="<?php echo base_url()?>home/user_panel">
					<button style="border-radius: 25px;
			height: 40px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
margin-right: 4px;
			">
						<span style="font-weight: bolder;">پنل کاربری</span>
						<i style="
						   z-index: 999;
						   position: relative;
						   top: 1px;
						   color: #e11c1c;
						   font-size: 20px;
						" class="fa fa-user" aria-hidden="true"></i>
					</button>
				</a>

			<?php } ?>
			<a class="" style="color: #5e5e5e;" href="<?php echo base_url()?>home/shopping_cart">
				<button class="" style="
			border-radius: 25px;
			height: 40px;
			outline: unset;
			border: 1px solid #ccc;
			box-shadow: 0 0 3px #ccc;
margin-right: 4px;
">
					<span class="" style="font-weight: bolder;">سبد خرید</span>
					<i style="
			cursor: pointer;
			z-index: 999;
			position: relative;
			top: 1px;
			color: #e11c1c;
			font-size: 20px;
			" class="fa fa-shopping-cart" aria-hidden="true"></i>
				</button>
			</a>
		</div>

	</div>

	<hr>

	<div class="row" style="padding: 0 20px 20px 20px;">
		<ul id="category-list">
			<!-- دسته‌ها اینجا لود میشن -->
		</ul>

	</div>
	<!-- Overlay لودینگ -->
	<div id="loading-overlay">
		<div>در حال بارگذاری...</div>
	</div>


</div>

<!--//space-->
<!--<div class="container-fluid" style="height: 50px"></div>-->

</div>

<!--END Mobile_header-->




<script>
	// تابع برای نمایش overlay
	function showLoadingOverlay() {
		$('#loading-overlay').show();
	}

	// تابع برای مخفی کردن overlay
	function hideLoadingOverlay() {
		$('#loading-overlay').hide();
	}

	$(document).ready(function () {
		loadCategories(0, $('#category-list'), 0, null); // شروع از سطح 0

		function loadCategories(parentId, container, level, parentParentId) {

			let loadingTimer;

			// تابع برای شروع تایمر
			function startLoadingTimer() {
				loadingTimer = setTimeout(function () {
					showLoadingOverlay();  // نمایش لودینگ بعد از 500ms
				}, 500);  // 500ms تأخیر برای نمایش لودینگ
			}

			// تابع برای لغو تایمر در صورت بارگذاری سریع
			function cancelLoadingTimer() {
				clearTimeout(loadingTimer);  // لغو تایمر اگر بارگذاری سریع انجام شود
				hideLoadingOverlay();  // مخفی کردن لودینگ
			}

			// نمایش لودینگ
			function showLoadingOverlay() {
				$('#loading-overlay').show();
			}

			// مخفی کردن لودینگ
			function hideLoadingOverlay() {
				$('#loading-overlay').hide();
			}

			// شروع تایمر لودینگ
			startLoadingTimer();

			$.post("<?= base_url('home/get_categories') ?>", { parent_id: parentId }, function (data) {
				let categories = JSON.parse(data);

				// لغو تایمر و مخفی کردن لودینگ بعد از بارگذاری داده‌ها
				cancelLoadingTimer();


				categories.forEach(cat => {

					let li = $('<li></li>').attr('data-id', cat.id);
					// let span = $('<span></span>').text(cat.name_cat).attr('title', cat.name_cat);
					let link = $('<a></a>').text(cat.name_cat)
						.attr('href', '<?= base_url("home/category/") ?>' + cat.id)
						.attr('title', cat.name_cat);
					/*let nameElement = (cat.has_subcategories || level === 0)
						? $('<span></span>').text(cat.name_cat).attr('title', cat.name_cat)
						: $('<a></a>').text(cat.name_cat)
							.attr('href', '<?= base_url("category/") ?>' + cat.id)
							.attr('title', cat.name_cat);*/
					let arrow = $('<span class="arrow pull-left">◀</span>');
					let subUl = $('<ul></ul>').hide();  // زیرمنوها به طور پیش‌فرض پنهان هستند

					if (parentId === 0) {
						subUl.addClass('vertical');
					}

					// اگر این دسته زیرمنو داشته باشد
					if (cat.has_subcategories) {
						li.append(arrow);

						// رویداد کلیک روی هر دسته
						li.on('click', function (e) {
							e.stopPropagation();  // جلوگیری از بسته شدن منو در هنگام کلیک روی دسته

							// بسته شدن زیرمنوهای قبلی
							container.find('ul').not(subUl).slideUp().parent().removeClass('open');

							// اگر زیرمنوها هنوز بارگذاری نشده باشند، بارگذاری کنیم
							if (subUl.children().length === 0) {
								loadCategories(cat.id, subUl, level + 1, parentId);
							}

							// باز و بسته کردن زیرمنو
							subUl.toggle();  // فقط زیرمنو را باز یا بسته می‌کنیم
							li.toggleClass('open');  // کلاس open برای چرخاندن فلش
						});
					}else{
						subUl.hide();
					}

					// li.append(span);
					li.append(link);
					// li.append(nameElement);
					li.append(subUl);  // زیرمنو به هر دسته اضافه می‌شود
					container.append(li);
				});
			});
		}

		// این رو بعد از بالا و قبل از بسته شدن document ready بگذار
		$(document).on('click', function (e) {
			if (!$(e.target).closest('#category-list li span').length) {
				$('#category-list ul').slideUp();
				$('#category-list li').removeClass('open');
			}
		});
	});






	$(document).on('mouseover', '.cat', function(e) {
		const id_cat=$(this).attr("id_cat");
		const x=$('#cat_'+id_cat);

		console.log(id_cat);
		$.ajax({
			url: "<?php echo base_url(); ?>home/show_cat",
			method:"POST",
			data:{id:id_cat, },
			success:
				function(data) {
					$(this).css({'background-color':'#ccc'})
					x.append(data);
					console.log(x);
				}
		});
	});
	$(document).on('mouseleave', '.cat', function(e) {
		$('.childs').remove();
	});

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


</script>
