<!DOCTYPE html>
<html lang="fa">
<head>
	<meta charset="UTF-8">
	<title>دسته‌بندی‌ها</title>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
	<style>
		.toggle-btn {
			font-weight: bold;
			margin-left: 2px;
		}
		table.dataTable tbody td {
			vertical-align: middle;
		}
	</style>
</head>
<body>

<div class="container">
	<table id="categoriesTable" class="display" style="width:100%">
		<thead>
		<tr>
			<th><input type="checkbox" id="select-all"></th> <!-- سلکت همه -->
			<th>آیکون</th>
			<th>نام دسته</th>
			<th>تاریخ ایجاد</th>
			<th>تاریخ ویرایش</th>
			<th>عملیات</th>
		</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

<!-- مودال افزودن دسته -->
<div class="modal fade" id="insertModal" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="بستن">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="insertModalLabel">افزودن دسته جدید</h4>
			</div>

			<form id="insertForm">
				<div class="modal-body">
					<div class="form-group">
						<label for="name_cat">نام دسته:</label>
						<input type="text" class="form-control" id="name_cat" name="name_cat" placeholder="نام دسته را وارد کنید">
					</div>
					<div class="form-group">
						<label for="parentId">والد:</label>
						<select class="form-control" id="parentId" name="parentId">
							<option value="0">بدون والد (سطح 0)</option>
							<!-- گزینه‌های والد اینجا با JS اضافه میشه -->
						</select>
					</div>

				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
					<button type="submit" class="btn btn-primary" >ذخیره</button>
				</div>
			</form>

		</div>
	</div>
</div>


<!--<div id="insertModal" class="modal" tabindex="-1" style="display:none;">-->
<!--	<div class="modal-dialog">-->
<!--		<div class="modal-content p-3">-->
<!--			<button type="button" class="btn-close" id="closeModal"></button>-->
<!--			<h5 class="modal-title mb-3">افزودن دسته جدید</h5>-->
<!--			<form id="insertForm">-->
<!--				<div class="mb-3">-->
<!--					<label for="parentId" class="form-labl">والد:</label>-->
<!--					<select id="parentId" name="parentId" class="form-select"></select>-->
<!--				</div>-->
<!--				<div class="mb-3">-->
<!--					<label for="name_cat" class="form-label">نام دسته:</label>-->
<!--					<input type="text" id="name_cat" name="name_cat" class="form-control" required>-->
<!--				</div>-->
<!--				<button type="submit" class="btn btn-primary">ذخیره</button>-->
<!--			</form>-->
<!--		</div>-->
<!--	</div>-->
<!--</div>-->




<!--<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>-->

<script>
	$(document).ready(function () {
		let allData = [];
		let table;
		let isSearching = false;

		// بارگذاری و ساخت داده‌ها
		table = $('#categoriesTable').DataTable({
			ajax: {
				url: '<?= site_url("admin/getCategories") ?>',
				dataSrc: function (json) {
					let flat = [];
					function flatten(arr, level = 0) {
						arr.forEach(item => {
							let temp = { ...item, level: level };
							delete temp.children;
							flat.push(temp);
							if (item.children) flatten(item.children, level + 1);
						});
					}
					flatten(json.data);
					allData = flat;
					return flat;
				}
			},
			columns: [
				{
					data: null,
					orderable: false,
					searchable: false,
					width: "30px",
					render: function(data, type, row) {
						return `<input type="checkbox" class="row-select" data-id="${row.id}">`;
					}
				},
				{
					data: null,
					orderable: false,
					searchable: false,
					width: "40px",
					render: function(data, type, row) {
						// فرض کن آیکون در row.iconUrl هست، اگر نیست میتونی تغییرش بدی
						let icon = row.iconUrl ? `<img src="${row.iconUrl}" alt="icon" style="width:24px; height:24px;">` : '';
						return icon;
					}
				},
				{
					data: 'name_cat',
					render: function (data, type, row) {
						let indent = '&nbsp;'.repeat(row.level * 4);
						let hasChild = allData.some(c => c.parentId == row.id);
						let toggleBtn = hasChild ? `<span class="toggle-btn" data-id="${row.id}" style="cursor:pointer">▶</span>` : '';
						return `${indent}${toggleBtn} ${data}`;
					}
				},
				{ data: 'created', width: "120px" },
				{ data: 'modified', width: "120px" },
				{
					data: null,
					orderable: false,
					searchable: false,
					width: "140px",
					render: function(data, type, row) {
						return `
                    <button class="btn-edit" title="ویرایش" data-id="${row.id}" style="border:none;background:none;cursor:pointer;color:#2a9df4;">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn-delete" title="حذف" data-id="${row.id}" style="border:none;background:none;cursor:pointer;color:#e74c3c;">
                        <i class="fa fa-trash"></i>
                    </button>
                    <button class="btn-view-products" title="مشاهده محصولات" data-id="${row.id}" style="border:none;background:none;cursor:pointer;color:#27ae60;">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button class="btnOpenInsertModal" data-toggle="modal" data-target="#insertModal" title="افزودن زیر دسته" data-id="${row.id}" style="border:none;background:none;cursor:pointer;color:#f39c12;">
                        <i class="fa fa-plus"></i>
                    </button>
                    <button class="btn-history" title="تاریخچه تغییرات" data-id="${row.id}" style="border:none;background:none;cursor:pointer;color:#8e44ad;">
                        <i class="fa fa-history"></i>
                    </button>
                    `;
					}
				}
			],
			paging: false,
			info: false,
			ordering: false, // برای حفظ ترتیب درختی
			language: {
				search: "جستجو:",
				emptyTable: "داده‌ای یافت نشد",
				paginate: {
					first: "اولین",
					last: "آخرین",
					next: "بعدی",
					previous: "قبلی"
				}
			},
			rowCallback: function (row, data) {
				if (data.level !== 0 && !data.visible) {
					$(row).hide();
				} else {
					$(row).show();
				}
			}
		});

		// تابع باز و بسته کردن فرزندان
		function toggleChildren(parentId, show) {
			table.rows().every(function () {
				let d = this.data();
				if (d.parentId == parentId) {
					let $row = $(this.node());
					if (show) $row.show(); else $row.hide();

					// اگر بسته می‌شود، تمام فرزندانش هم بسته شوند
					if (!show) {
						$(`.toggle-btn[data-id="${d.id}"]`).removeClass('open').text('▶');
						toggleChildren(d.id, false);
					}
				}
			});
		}

		// کلیک روی فلش باز/بسته کردن دسته‌ها
		$('#categoriesTable tbody').on('click', '.toggle-btn', function () {
			let $btn = $(this);
			let categoryId = $btn.data('id');
			let rowData = table.rows().data().toArray().find(d => d.id == categoryId);

			// والدها در حالت سرچ غیرفعال شوند
			if (isSearching && rowData && rowData.isParentInSearch) {
				return;
			}

			let isOpen = $btn.hasClass('open');
			$btn.toggleClass('open').text(isOpen ? '▶' : '▼');
			toggleChildren(categoryId, !isOpen);
		});

		// جستجوی سفارشی
		$('#categoriesTable_filter input').off().on('input', function () {
			let keyword = this.value.trim().toLowerCase();
			if (keyword === '') {
				isSearching = false;
				table.clear().rows.add(allData).draw();
				setTimeout(() => collapseAllExceptLevel0(), 100);
				return;
			}
			isSearching = true;

			let matched = allData.filter(row => row.name_cat && row.name_cat.toLowerCase().includes(keyword));
			let matchedIds = matched.map(row => row.id);
			let parentIds = matchedIds.flatMap(id => getAllParentIds(id));
			let finalIds = [...new Set([...matchedIds, ...parentIds])];

			let finalRows = allData.map(row => {
				return {
					...row,
					isParentInSearch: parentIds.includes(row.id) && !matchedIds.includes(row.id)
				};
			});

			table.clear().rows.add(finalRows).draw();

			setTimeout(() => {
				table.rows().every(function () {
					let d = this.data();
					let $row = $(this.node());
					$row.toggle(finalIds.includes(d.id));
				});
			}, 100);
		});

		function getAllParentIds(id) {
			let result = [];
			let current = allData.find(x => x.id == id);
			while (current && current.parentId != 0) {
				result.push(current.parentId);
				current = allData.find(x => x.id == current.parentId);
			}
			return result;
		}

		function getAllChildIds(parentId) {
			let result = [];
			let children = allData.filter(x => x.parentId == parentId);
			children.forEach(child => {
				result.push(child.id);
				result = result.concat(getAllChildIds(child.id));
			});
			return result;
		}

		// جمع کردن همه دسته‌ها به جز سطح 0 (نمایش فقط سطح 0 باز)
		function collapseAllExceptLevel0() {
			table.rows().every(function () {
				let d = this.data();
				let $row = $(this.node());
				if (d.level === 0) {
					$row.show();
				} else {
					$row.hide();
				}
			});
			$('.toggle-btn').removeClass('open').text('▶');
		}

		// انتخاب همه
		$('#selectAll').on('change', function () {
			let checked = $(this).is(':checked');
			$('.row-select').prop('checked', checked);
		});

		// حذف فرزندان به‌صورت بازگشتی
		function removeChildrenRows(parentId) {
			// از snapshot استفاده کن تا حین حذف، iteration به‌هم نخوره
			const rows = $('#categoriesTable tbody tr').toArray();
			rows.forEach(tr => {
				const r = table.row(tr);
				const d = r.data();
				if (d && d.parentId == parentId) {
					removeChildrenRows(d.id);  // بازگشتی برای نوه‌ها
					r.remove();                // حذف از DataTables
				}
			});
		}

// بعد از حذف، اگر پدر بچه‌ای نداشت آیکون فلش رو بردار
		function maybeRemoveParentToggle(parentId) {
			if (parentId == null) return;
			const hasChild = table.rows().data().toArray().some(x => x && x.parentId == parentId);
			if (!hasChild) {
				const $parentRow = $('#categoriesTable tbody tr')
					.filter(function(){
						const d = table.row(this).data();
						return d && d.id == parentId;
					});
				$parentRow.find('.toggle-btn').remove(); // فلش رو حذف کن
			}
		}

		$('#categoriesTable tbody').on('click', '.btn-delete', function () {
			const id   = $(this).data('id');
			const $row = $(this).closest('tr');

			if (!confirm('آیا از حذف این دسته مطمئن هستید؟')) return;

			$.ajax({
				url: '<?php echo base_url();?>admin/delete_category',
				type: 'POST',
				dataType: 'json',
				data: { id },
				success: function(resp){
					if (resp && resp.success) {

						// حذف فرزندان از DOM و دیتاتیبل (بازگشتی)
						function removeChildrenRows(parentId){
							$('#categoriesTable tbody tr').each(function(){
								const d = table.row(this).data();
								if (d && d.parentId == parentId) {
									removeChildrenRows(d.id);
									table.row(this).remove(); // فقط از حافظه دیتاتیبل
									$(this).remove();         // از DOM
								}
							});
						}

						// اول فرزندان بعد خود ردیف
						removeChildrenRows(id);
						table.row($row).remove();
						$row.remove();

						console.log('دسته با ID ' + id + ' حذف شد (بدون ریفرش کل جدول).');

					} else {
						alert('حذف با مشکل مواجه شد.');
					}
				},
				error: function() {
					alert('خطا در ارتباط با سرور.');
				}
			});
		});




// باز کردن مودال اینزرت با دکمه‌ای مثلا
		$('#categoriesTable tbody').on('click', '.btnOpenInsertModal', function() {
			let parentId = $(this).data('id');

			let options = '<option value="0">بدون والد (سطح 0)</option>';
			allData.forEach(cat => {
				let indent = '\u00A0'.repeat(cat.level * 4); // فضای غیرشکستنی
				options += `<option value="${cat.id}">${indent}${cat.name_cat}</option>`;
			});
			$('#parentId').html(options);

			// انتخاب والد بر اساس دکمه کلیک شده
			$('#parentId').val(parentId);

		});


		// بستن مودال با کلیک روی ضربدر
		$('#insertClose').on('click', function() {
			$('#insertModal').modal('hide');
			$('#insertForm')[0].reset();
		});

		$('#insertForm').on('submit', function(e) {
			e.preventDefault();
			e.stopPropagation();

			let formData = $(this).serialize();

			$.ajax({
				url: '<?= base_url("admin/insert_cat") ?>',
				type: 'POST',
				data: formData,
				dataType: 'json',
				success: function(resp) {
					if (resp && resp.success) {
						$('#insertModal').modal('hide');
						$('#insertForm')[0].reset();

						let level = getLevel(resp.data.parentId);

						let newRow = {
							id: resp.data.id,
							name_cat: resp.data.name_cat,
							parentId: resp.data.parentId,
							icon: resp.data.icon || '',
							image: resp.data.image || '',
							created: resp.data.created,
							modified: resp.data.modified || '',
							level: level,
							visible: true
						};

						allData.push(newRow);

						// پیدا کردن والد
						let $parentRow = $('#categoriesTable tbody tr').filter(function(){
							let d = table.row(this).data();
							return d && d.id == resp.data.parentId;
						});

						// ساختن ردیف جدید
						let $tr = $('<tr></tr>');
						$tr.append(`<td><input type="checkbox" class="row-select" data-id="${newRow.id}"></td>`);
						$tr.append(`<td>${newRow.icon ? `<img src="${newRow.icon}" style="width:24px;height:24px;">` : ''}</td>`);

						let indent = '&nbsp;'.repeat(level * 4);
						let toggleBtn = ''; // اول هیچ فرزندی نداره
						$tr.append(`<td>${indent}${toggleBtn} ${newRow.name_cat}</td>`);

						$tr.append(`<td>${newRow.created}</td>`);
						$tr.append(`<td>${newRow.modified}</td>`);
						$tr.append(`
                    <td>
                        <button class="btn-edit" data-id="${newRow.id}">ویرایش</button>
                        <button class="btn-delete" data-id="${newRow.id}">حذف</button>
                        <button class="btn-view-products" data-id="${newRow.id}">مشاهده</button>
                        <button class="btnOpenInsertModal" data-id="${newRow.id}">+</button>
                        <button class="btn-history" data-id="${newRow.id}">تاریخچه</button>
                    </td>
                `);

						// درج دقیق زیر والد
						if ($parentRow.length) {
							$tr.insertAfter($parentRow);
							// اگر والد هنوز toggle نداشت، اضافه کن
							let $toggle = $parentRow.find('.toggle-btn');
							if ($toggle.length === 0) {
								$parentRow.find('td:nth-child(3)').prepend(
									`<span class="toggle-btn open" data-id="${resp.data.parentId}" style="cursor:pointer">▼</span>`
								);
							}
						} else {
							$('#categoriesTable tbody').append($tr);
						}

						// اضافه کردن به DataTable بدون draw کلی
						table.row.add(newRow).invalidate();

						// اختصاص handler برای toggle فلش
						$tr.find('.toggle-btn').on('click', function() {
							let $btn = $(this);
							let categoryId = $btn.data('id');
							let isOpen = $btn.hasClass('open');
							$btn.toggleClass('open').text(isOpen ? '▶' : '▼');
							toggleChildren(categoryId, !isOpen);
						});

						console.log('دسته جدید اضافه شد:', resp.data);
					} else {
						alert('مشکل در ثبت دسته.');
					}
				},
				error: function() {
					alert('خطا در ارتباط با سرور.');
				}
			});
		});

// محاسبه سطح برای تو رفتگی
		function getLevel(parentId) {
			if (parentId == 0) return 0;
			let parent = allData.find(x => x.id == parentId);
			return parent ? parent.level + 1 : 0;
		}

// باز و بسته کردن فرزندان
		function toggleChildren(parentId, show) {
			table.rows().every(function() {
				let d = this.data();
				if (d.parentId == parentId) {
					let $row = $(this.node());
					if (show) $row.show(); else $row.hide();

					// اگر بسته میشه، تمام فرزندانش هم بسته بشن
					if (!show) {
						$(`.toggle-btn[data-id="${d.id}"]`).removeClass('open').text('▶');
						toggleChildren(d.id, false);
					}
				}
			});
		}




// تابع کمکی: پیدا کردن سطح والد برای محاسبه level
		function getLevel(parentId) {
			if (parentId == 0) return 0;
			let parent = allData.find(x => x.id == parentId);
			return parent ? parent.level + 1 : 0;
		}



	});
</script>

<!-- آیکون‌های FontAwesome لازمه -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />



</body>
</html>
