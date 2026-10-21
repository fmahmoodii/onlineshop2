<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content" style="padding: 0 0 100px 0">

		<div style="margin-bottom: 15px;">
			<a href="<?= base_url('admin/inventory') ?>">لیست انبارها</a>
			<span> / لیست کل محصولات</span>
		</div>

		<table id="pstock_data" class="table table-bordered table-striped" style="width:100%">
			<thead>
			<tr>
				<th width="10%">
					کد کالا<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="45%">
					نام کالا<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="12%">قیمت</th>
				<th width="13%">موجودی کل</th>
				<th width="10%">تاریخچه</th>
			</tr>
			</thead>
		</table>

	</div>

	<script>
		$(document).ready(function () {
			var dataTable = $('#pstock_data').DataTable({
				language: {
					lengthMenu: "نمایش _MENU_ رکورد هر صفحه",
					zeroRecords: "موردی یافت نشد",
					info: "صفحه _PAGE_ از _PAGES_",
					infoEmpty: "موردی یافت نشد",
					infoFiltered: "(فیلتر _MAX_ رکورد)",
					search: "جستجو ",
					loadingRecords: "درحال بارگذاری",
					processing: "در حال پردازش",
					paginate: { first: "ابتدا", last: "انتها", next: "بعدی", previous: "قبلی" }
				},
				processing: true,
				serverSide: false,   // ✅ همه داده‌ها یکجا میاد، چون این یه گزارش کلیه نه لیست سنگین
				ajax: {
					url: "<?= base_url('admin/product_stock_list') ?>",
					type: "POST",
					dataSrc: "data"
				},
				columnDefs: [{
					targets: [2, 3, 4],
					orderable: false,
					searchable: false
				}]
			});

			$('.column-filter').on('keyup change', function () {
				dataTable.column($(this).closest('th').index()).search(this.value).draw();
			});
			$('.column-filter').on('click', function (e) { e.stopPropagation(); });
		});
	</script>

<?php } ?>
