<?php if($this->session->userdata('id')){ ?>

	<div class="container box" id="content" style="padding: 0 0 100px 0">

		<!-- breadcrumb -->
		<div style="margin-bottom: 15px;">
			<a href="<?= base_url('admin/inventory') ?>">لیست انبارها</a>
			<?php if (!empty($inventory)): ?>
				<span> / </span>
				<a href="<?= base_url('admin/inventory_stock/' . $inv_id) ?>"><?= htmlspecialchars($inventory->name) ?></a>
			<?php endif; ?>
			<?php if (!empty($product)): ?>
				<span> / </span>
				<span><?= htmlspecialchars($product->name) ?></span>
			<?php endif; ?>
			<span> / تاریخچه</span>
		</div>

		<table id="trans_data" class="table table-bordered table-striped" style="width:100%">
			<thead>
			<tr>
				<th width="5%">شناسه</th>
				<th width="12%">
					انبار<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="15%">
					کالا<br>
					<input type="text" class="form-control form-control-sm column-filter" placeholder="جستجو...">
				</th>
				<th width="8%">نوع</th>
				<th width="8%">دسته</th>
				<th width="7%">تعداد</th>
				<th width="15%">توضیحات</th>
				<th width="12%">تاریخ ثبت</th>
				<th width="10%">ثبت‌کننده</th>
			</tr>
			</thead>
		</table>

	</div>

	<script>
		$(document).ready(function () {
			$('#trans_data').DataTable({
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
				serverSide: true,
				order: [['0', 'desc']],
				ajax: {
					url: "<?= base_url('admin/inventory_transactions_list') ?>",
					type: "POST",
					data: function (d) {
						d.inv_id  = <?= json_encode($inv_id) ?>;
						d.attr_id = <?= json_encode($attr_id) ?>;
					}
				},
				columnDefs: [{
					targets: [0, 3, 4, 5, 8],
					orderable: false,
					searchable: false
				}]
			});

			$('.column-filter').on('keyup change', function () {
				$('#trans_data').DataTable().column($(this).closest('th').index()).search(this.value).draw();
			});
			$('.column-filter').on('click', function (e) { e.stopPropagation(); });
		});
	</script>

<?php } ?>
