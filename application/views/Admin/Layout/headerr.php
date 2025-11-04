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

	<!--	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />-->
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<script src="https://code.highcharts.com/highcharts.js"></script>

</head>
<style>

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


	/* والد هدر */
	.header-container {
		display: flex;
		justify-content: space-between;
		align-items: center;
		height: 60px;
		background-color: #5478f6;
		padding: 0 20px;
		color: #fff;
		position: relative;
		z-index: 999;
	}

	/* بخش آیکون‌ها و نوتیفیکیشن */
	.header-icons {
		display: flex;
		gap: 20px;
		align-items: center;
	}

	.header-icons a {
		position: relative;
		display: flex;
		align-items: center;
	}

	.header-icons a button {
		width: 22px;
		height: 22px;
		font-size: 12px;
		border-radius: 50%;
		border: none;
		background-color: #d23030;
		color: #fff;
		position: absolute;
		top: -8px;
		right: -8px;
		line-height: 1;
	}

	/* بخش کاربر */
	.header-user {
		display: flex;
		align-items: center;
		gap: 10px;
	}

	/* نام کاربر */
	.header-user span {
		color: #fff;
		font-weight: 500;
	}

	/* خروج */
	.header-logout {
		display: flex;
		align-items: center;
		gap: 5px;
		cursor: pointer;
		font-weight: bold;
		color: #fff;
	}
</style>

<div class="header-container">
	<div class="header-left">
		<!-- می‌تونیم اینجا لوگو یا نام سایت بگذاریم -->
	</div>

	<div class="header-icons">
		<a>
			<i class="fa fa-envelope-o" style="font-size:22px;"></i>
			<button>99</button>
		</a>
		<a>
			<i class="fa fa-bell-o" style="font-size:22px;"></i>
			<button>99</button>
		</a>
		<a>
			<i class="fa fa-shopping-bag" style="font-size:22px;"></i>
			<button>99</button>
		</a>
	</div>

	<div class="header-user">
		<img src="" style="width:25px; height:25px; border-radius:50%;" alt="#">
		<span>فاطمه محمودی</span>
		<div class="header-logout" onclick="location.href='<?= base_url('admin/logout') ?>'">
			خروج <i class="fa fa-sign-out"></i>
		</div>
	</div>
</div>
