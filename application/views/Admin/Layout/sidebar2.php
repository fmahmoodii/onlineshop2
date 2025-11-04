<style>
	.sidebar-wrapper {
		position: relative;
		display: flex;
		transition: all 0.3s ease;
	}

	.sidebar-toggle {
		position: fixed;
		top: 15px;
		right: 15px;
		background: #3d3939;
		color: white;
		border: none;
		padding: 8px 10px;
		border-radius: 6px;
		cursor: pointer;
		z-index: 1001;
	}

	.sidebar {
		width: 240px;
		height: 100vh;
		background: #3d3939;
		color: #fff;
		position: fixed;
		right: 0;
		top: 0;
		padding-top: 60px;
		transition: all 0.3s ease;
		overflow-y: auto;
		z-index: 1000;
	}

	.sidebar.closed {
		right: -240px;
	}

	.sidebar-header {
		text-align: center;
		font-size: 18px;
		font-weight: bold;
		margin-bottom: 15px;
	}

	.sidebar-menu {
		list-style: none;
		padding: 0;
		margin: 0;
	}

	.sidebar-menu li {
		border-bottom: 1px solid #555;
	}

	.sidebar-menu a {
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 12px 20px;
		text-decoration: none;
		transition: background 0.2s;
		font-size: 15px;
	}

	.sidebar-menu a:hover {
		background: #575757;
	}

	.sidebar-menu a.active {
		background: #5ca0d3;
		color: #fff;
	}

	.sidebar-link-content {
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.sidebar-link-content i {
		width: 18px;
		text-align: center;
	}

	.arrow {
		transition: transform 0.3s ease;
		font-size: 13px;
		min-width: 14px;
		text-align: center;
	}

	.has-submenu.open > a .arrow {
		transform: rotate(-90deg);
	}

	.submenu {
		display: none;
		background: #4a4747;
	}

	.has-submenu.open .submenu {
		display: block;
	}

	.submenu a {
		padding-right: 40px;
		font-size: 14px;
	}

	/* استایل محتوای صفحه */
	#content {
		transition: all 0.3s ease;
		margin-right: 0;
	}

	body.sidebar-open #content {
		margin-right: 240px;
	}

	@media (max-width: 768px) {
		.sidebar {
			width: 200px;
		}
		body.sidebar-open #content {
			margin-right: 200px;
		}
	}
</style>


<?php if($this->session->userdata('id')){ ?>
	<div class="sidebar-wrapper">
		<button id="sidebarToggle" class="sidebar-toggle">
			<i class="fa fa-bars"></i>
		</button>

		<nav class="sidebar" id="sidebar">
			<div class="sidebar-header">مدیریت سایت</div>
			<ul class="sidebar-menu">
				<li><a href="<?= base_url('admin') ?>"><span class="sidebar-link-content"><i class="fa fa-home"></i> داشبورد</span></a></li>

				<li class="has-submenu">
					<a href="#">
						<span class="sidebar-link-content"><i class="fa fa-list"></i> دسته‌بندی محصولات</span>
						<i class="fa fa-chevron-left arrow"></i>
					</a>
					<ul class="submenu">
						<li><a href="<?= base_url('admin1/categories') ?>">دسته بندی محصول-تستی3</a></li>
						<li><a href="<?= base_url('admin/category') ?>">اصلی</a></li>
						<li><a href="<?= base_url('admin/category1') ?>">سطح ۱</a></li>
						<li><a href="<?= base_url('admin/category2') ?>">سطح ۲</a></li>
					</ul>
				</li>

				<li class="has-submenu">
					<a href="#">
						<span class="sidebar-link-content"><i class="fa fa-dropbox"></i> محصول‌گذاری</span>
						<i class="fa fa-chevron-left arrow"></i>
					</a>
					<ul class="submenu">
						<li><a href="<?= base_url('admin/insert_product') ?>">محصول‌گذاری</a></li>
						<li><a href="<?= base_url('admin/model') ?>">مدل</a></li>
						<li><a href="<?= base_url('admin/jens') ?>">جنس</a></li>
						<li><a href="<?= base_url('admin/size') ?>">سایز</a></li>
						<li><a href="<?= base_url('admin/brand') ?>">برند</a></li>
						<li><a href="<?= base_url('admin/color') ?>">رنگ</a></li>
					</ul>
				</li>

				<li><a href="<?= base_url('admin/off_code') ?>"><span class="sidebar-link-content"><i class="fa fa-ticket"></i> کد تخفیف</span></a></li>
				<li><a href="<?= base_url('admin/rules') ?>"><span class="sidebar-link-content"><i class="fa fa-gavel"></i> قوانین و مقررات</span></a></li>
				<li><a href="<?= base_url('admin/products') ?>"><span class="sidebar-link-content"><i class="fa fa-cubes"></i> محصولات</span></a></li>
				<li><a href="<?= base_url('admin/footer') ?>"><span class="sidebar-link-content"><i class="fa fa-copyright"></i> فوتر</span></a></li>
				<li><a href="<?= base_url('admin/sales') ?>"><span class="sidebar-link-content"><i class="fa fa-shopping-cart"></i> اطلاعات فروش</span></a></li>
				<li><a href="<?= base_url('admin/comments') ?>"><span class="sidebar-link-content"><i class="fa fa-comments"></i> نظرات کاربران</span></a></li>

				<li class="has-submenu">
					<a href="#">
						<span class="sidebar-link-content"><i class="fa fa-archive"></i> انبارداری</span>
						<i class="fa fa-chevron-left arrow"></i>
					</a>
					<ul class="submenu">
						<li><a href="<?= base_url('admin/inventory_attrs') ?>">انبار</a></li>
						<li><a href="<?= base_url('admin/inventory') ?>">لیست انبار</a></li>
						<li><a href="<?= base_url('admin/attributes') ?>">موجودی</a></li>
					</ul>
				</li>
			</ul>
		</nav>
	</div>
<?php } ?>


<script>
	// باز و بسته کردن سایدبار
	$('#sidebarToggle').on('click', function () {
		$('#sidebar').toggleClass('closed open');
		$('body').toggleClass('sidebar-open');
	});

	// باز و بسته کردن زیرمنو
	$('.has-submenu > a').on('click', function (e) {
		e.preventDefault();
		$(this).parent().toggleClass('open');
	});

	// فعال کردن لینک صفحه فعلی
	$(document).ready(function () {
		const currentUrl = window.location.href;
		const links = $('.sidebar a');

		links.each(function () {
			const href = $(this).attr('href');
			if (currentUrl.includes(href) && href !== '#') {
				$(this).addClass('active');
				const parent = $(this).closest('.has-submenu');
				if (parent.length) parent.addClass('open');
			}
		});
	});
</script>
