<head>
	<meta charset="UTF-8">
	<title><?php echo $title;?></title>

	<link href="<?php echo base_url()?>assets/css/style.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/css/bootstrap.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/css/bootstrap-rtl.min.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
	<link href="<?php echo base_url()?>assets/css/Structure.css" rel="stylesheet" >



	<script src="<?php echo base_url()?>assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url()?>assets/js/bootstrap.min.js"></script>


	<script src="<?php echo base_url()?>assets/ckeditor/ckeditor.js"></script>

	<script src="<?php echo base_url()?>assets/js/persian-date.js"></script>
	<script src="<?php echo base_url()?>assets/js/persian-datepicker.js"></script>
	<link href="<?php echo base_url()?>assets/css/persian-datepicker.css" rel="stylesheet" >


	<script src="<?php echo base_url()?>assets/DataTables/datatables.js"></script>
	<script src="<?php echo base_url()?>assets/DataTables/DataTables-1.12.1/js/dataTables.bootstrap.js"></script>
	<script src="<?php echo base_url()?>assets/DataTables/DataTables-1.12.1/js/dataTables.dataTables.js"></script>
	<script src="<?php echo base_url()?>assets/DataTables/DataTables-1.12.1/js/jquery.dataTables.js"></script>


	<link rel="stylesheet" href="<?php echo base_url()?>assets/DataTables/datatables.css" />
	<link rel="stylesheet" href="<?php echo base_url()?>assets/DataTables/DataTables-1.12.1/css/dataTables.bootstrap.css" />
	<link rel="stylesheet" href="<?php echo base_url()?>assets/DataTables/DataTables-1.12.1/css/dataTables.dataTables.css" />
	<link rel="stylesheet" href="<?php echo base_url()?>assets/DataTables/DataTables-1.12.1/css/jquery.dataTables.css" />

	<script src="<?php echo base_url()?>assets/js/dataTables.treeGrid.min.js"></script>

	<!-- CSS لوکال -->
	<link href="<?php echo base_url()?>assets/css/select2.min.css" rel="stylesheet" />

	<!-- JS لوکال -->
	<script src="<?php echo base_url()?>assets/js/select2.min.js"></script>

	<script src="<?php echo base_url()?>assets/js/bootstrap-show-password.js"></script>


	<script>
		$("input[type='password']").password();

		function showSnackbar(type) {
			// type: 'del', 'ins', 'upd'
			var id = 'snackbar_' + type;
			var x = document.getElementById(id);
			x.className = "snackbar show";

			setTimeout(function() {
				x.className = x.className.replace("show", "");
			}, 3000);
		}
	</script>

<!--	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />-->
<!--	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>-->

	<script src="https://code.highcharts.com/highcharts.js"></script>

</head>
<style>
	/* پایه همه اسنکبارها */
	.snackbar {
		visibility: hidden; /* مخفی به صورت پیش‌فرض */
		min-width: 250px;
		margin-left: -125px; /* نصف عرض منفی برای وسط چین کردن */
		background-color: #333;
		color: #fff;
		text-align: center;
		border-radius: 2px;
		padding: 16px;
		position: fixed;
		z-index: 1000;
		left: 50%;
		bottom: 30px;
		font-size: 14px;
	}

	/* حالت نمایش */
	.snackbar.show {
		visibility: visible;
		-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
		animation: fadein 0.5s, fadeout 0.5s 2.5s;
	}

	/* رنگ‌های متفاوت بر اساس نوع */
	#snackbar_del { background-color: #e74c3c; } /* قرمز برای حذف */
	#snackbar_ins { background-color: #2ecc71; } /* سبز برای درج */
	#snackbar_upd { background-color: #3498db; } /* آبی برای ویرایش */

	/* انیمیشن‌ها */
	@-webkit-keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
	@keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
	@-webkit-keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
	@keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }


	button{
		outline: none;
	}
	#image_prd:empty{
		display: none;
	}
	#image_prd{
		display: flex;
	}


	/*.select2-results__option--selected { 	display: none; }*/

	.select2,.select2-results{
		direction: rtl;
		font-family: IRANSans;
	}

	.select2-results__options {
		height: 100px;
	}

	select{
		height: 50px;
	}


	.password {
		text-security:disc;
		-webkit-text-security:disc;
		-mox-text-security:disc;
	}

	#inv_sup_data,#inv_attr_data,#inv_data,#attr_data,#ord_data,#prd_data,#cmnt_data,#off_data,#usr_data
	,#size_data,#category1_data,#category2_data,#category_data,#cat-data {
		font-size: 12px;
		font-family: IRANSans;
		width: 100%;
	}

	#model_data,#jens_data,#brand_data,#color_data{
		font-size: 12px;
		font-family: IRANSans;
		width: 100%;
	}


	#content{
		padding-bottom: 100px;
	}


	.choices__item{
		direction: ltr;
	}

	#list a li{
	width: 200px;height: 40px;display: flex;
	align-items: center;justify-content: center;
	border: 1px solid #4e4e4e;position: relative;left: 40px;
	background-color: #3d3939;
}
#list2 ul{
	left: 0;
}
#list2 a li{
	width: 200px;height: 30px;display: flex;
	align-items: center;justify-content: center;
	border: 0px solid #4e4e4e;position: relative;left: 65px;
	background-color: #5d5757;
}

	.tag2:hover,.tag:hover {
		background-color: yellow;
		cursor: pointer;
	}



	.required:before {
		content:"* ";
		color: red;
	}

	a {
		color: #337ab7;
		text-decoration: none;
	}
	li{
		list-style:none;
	}
</style>


<div class="container-fluid">
	<div id="2" class="row" style=" height: 60px;
    background-color:#5478f6; padding: 20px ">
		<div class="col-md-5">

		</div>
		<div class="col-md-4">
			<ul id="ul-top" style="display: inline-block;margin-right: 30px">
				<li style="margin-left: 25px ">
					<a>
						<i class="fa fa-envelope-o" style="cursor: pointer;color: #ffff;
    position: absolute;
    outline: unset;
    font-size: 22px;"></i>
						<button style="width: 25px;
    height: 25px;
    text-align: center;
    line-height: 1;
    font-weight: bold;
    font-size: 14px;
    background-color: #d23030;
    color: #fff;
    position: relative;
    top: -15px;
    border-radius: 50%;
    border: none;
    left: -12px;"> 99 </button>
					</a>
				</li>
					<li style="margin-left: 25px ">
					<a>
						<i class="fa fa-bell-o" style="cursor: pointer;color: #ffff;
		position: absolute;
		outline: unset;
		font-size: 22px;"></i>
						<button style="width: 25px;
		height: 25px;
		text-align: center;
		line-height: 1;
		font-weight: bold;
		font-size: 14px;
		background-color: #d23030;
		color: #fff;
		position: relative;
		top: -15px;
		border-radius: 50%;
		border: none;
		left: -12px;"> 99 </button>
					</a>
				</li>
				<li style="margin-left: 25px ">
					<a>
						<i class="fa fa-shopping-bag" style="cursor: pointer;color: #ffff;
		position: absolute;
		outline: unset;
		font-size: 22px;">
						</i>
						<button style="width: 25px;
		height: 25px;
		text-align: center;
		line-height: 1;
		font-weight: bold;
		font-size: 14px;
		background-color: #d23030;
		color: #fff;
		position: relative;
		top: -15px;
		border-radius: 50%;
		border: none;
		left: -12px;"> 99 </button>
					</a>
				</li>
			</ul>
		</div>
		<div style="display: inline-block; margin-right: 30px; ">
			<img src="" style="width: 25px; height: 25px; border-radius: 50%" alt="#">
			<span style="color: #FFFFFF">فاطمه محمودی</span>
		</div>
		<div style="display: inline-block; margin-right: 40px; ">
			<a href="<?php echo base_url('admin/logout')?>" style="cursor: pointer;font-weight: bolder;color: #ffff;">خروج</a>
			<i style="cursor: pointer;
    z-index: 9;
    position: relative;
    color: #ffff;
    font-size: 16px;
    line-height: 27px;" class="fa fa-sign-out"></i>

		</div>
	</div>
</div>


