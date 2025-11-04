<style>/* The snackbar - position it at the bottom and in the middle of the screen */
	#snackbar ,#snackbar2 ,#snackbar3 {
		visibility: hidden; /* Hidden by default. Visible on click */
		min-width: 250px; /* Set a default minimum width */
		margin-left: -125px; /* Divide value of min-width by 2 */
		background-color: #333; /* Black background color */
		color: #fff; /* White text color */
		text-align: center; /* Centered text */
		border-radius: 2px; /* Rounded borders */
		padding: 16px; /* Padding */
		position: fixed; /* Sit on top of the screen */
		z-index: 1; /* Add a z-index if needed */
		left: 50%; /* Center the snackbar */
		bottom: 30px; /* 30px from the bottom */
	}

	/* Show the snackbar when clicking on a button (class added with JavaScript) */
	#snackbar.show ,#snackbar2.show ,#snackbar3.show {
		visibility: visible; /* Show the snackbar */
		/* Add animation: Take 0.5 seconds to fade in and out the snackbar.
		However, delay the fade out process for 2.5 seconds */
		-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
		animation: fadein 0.5s, fadeout 0.5s 2.5s;
	}

	/* Animations to fade the snackbar in and out */
	@-webkit-keyframes fadein {
		from {bottom: 0; opacity: 0;}
		to {bottom: 30px; opacity: 1;}
	}

	@keyframes fadein {
		from {bottom: 0; opacity: 0;}
		to {bottom: 30px; opacity: 1;}
	}

	@-webkit-keyframes fadeout {
		from {bottom: 30px; opacity: 1;}
		to {bottom: 0; opacity: 0;}
	}

	@keyframes fadeout {
		from {bottom: 30px; opacity: 1;}
		to {bottom: 0; opacity: 0;}
	}
	#loading-image{
		position: fixed;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		z-index: 9999;
		background: url("<?php echo base_url()?>assets/images/loading-gif.gif") 50% 50% no-repeat rgb(15 10 10 /59%);
	}
	.details-control {
		background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
		cursor: pointer;
	}
	tr.shown .details-control {
		background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
	}


</style>

<?php if($this->session->userdata('id')){ ?>
	<div id="loading-image" style="display: none"></div>
	<div class="container box" id="content" >
		<div>

			<button class="btn btn-sm ml-3 btn-success" id="newCategoryButton" style="outline: unset" >دسته جدید</button>

			<button id='delete_selected' class="btn btn-sm ml-3 btn-danger" style="outline: none" >حذف همه</button>
			<button id='update_selected' class="btn btn-sm ml-3 btn-warning" >ویرایش همه</button>
			<button id='active_selected' class="btn btn-sm ml-3 btn-primary" >فعال سازی همه</button>
			<button id='deactive_selected' class="btn btn-sm ml-3 btn-secondry" >غیرفعال سازی همه</button>
		</div>

		<br>
		<br>
		<input type="text" id="searchInput" placeholder="جستجو دسته‌ها..." class="form-control mb-3">
		<br>
		<table id="categoryTable" class="table table-bordered">
			<thead>
			<tr>
				<th><input type="checkbox" id="checkAll" class="row-check"></th>
				<th></th>
				<th>نام دسته</th>
				<th>توضیحات</th>
				<th>تاریخ ایجاد</th>
				<th>آخرین ویرایش</th>
				<th>فعال/غیرفعال</th>
				<th>عملیات</th>
			</tr>
			</thead>
			<tbody></tbody>
		</table>

		<div id="snackbar">ثبت شد</div>
		<div id="snackbar2">حذف شد</div>
		<div id="snackbar3">ویرایش انجام شد</div>
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
							<input type="text" class="form-control" id="name_cat" name="name_cat" placeholder="نام دسته">
						</div>
						<div class="form-group">
							<label for="name_cat">توضیحات:</label>
							<input type="text" class="form-control" id="details" name="name_cat" placeholder="توضیحات">
						</div>
<!--						<div class="form-group">-->
<!--							<label for="parentId">والد:</label>-->
<!--							<select class="form-control" id="parentId" name="parentId">-->
<!--								<option value="0">بدون والد (سطح 0)</option>-->
<!--								 گزینه‌های والد اینجا با JS اضافه میشه -->
<!--							</select>-->
<!--						</div>-->

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
						<button type="submit" class="btn btn-primary" >ذخیره</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="بستن">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="editModalLabel">ویرایش دسته</h4>
				</div>

				<form id="editForm">
					<div class="modal-body">
						<div class="form-group">
							<label for="edit_name_cat">نام دسته:</label>
							<input type="text" class="form-control" id="edit_name_cat" name="edit_name_cat" placeholder="نام دسته">
						</div>
						<div class="form-group">
							<label for="edit_details">توضیحات:</label>
							<input type="text" class="form-control" id="edit_details" name="edit_details" placeholder="توضیحات">
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
						<button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<div class="modal fade" id="newCategoryModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<form id="newCategoryForm">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">دسته جدید</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="بستن">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="mb-3">
							<label>نام دسته</label>
							<input type="text" id="new_name_cat" class="form-control" required>
						</div>
						<div class="mb-3">
							<label>توضیحات</label>
							<input type="text" id="new_details" class="form-control">
						</div>
						<div class="mb-3">
							<label>والد</label>
							<select id="new_parent_id" class="form-control">
								<option value="0">بدون والد</option>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
						<button type="submit" class="btn btn-primary">ذخیره</button>
					</div>
				</div>
			</form>
		</div>
	</div>


<?php }?>


<script type="text/javascript" language="javascript" >

	$(document).ready(function() {
		let nodesById = {};
		let rootNodes = [];

		// دریافت داده‌ها و ساخت درخت
		$.getJSON("<?= base_url('admin1/categories_table') ?>", function(flat) {
			buildTree(flat);
			renderAllToTable();
			hideAllExceptRoots();
		});

		// ساخت درخت
		function buildTree(flat) {
			nodesById = {};
			rootNodes = [];

			flat.forEach(item => {
				const id = String(item.id);
				const parentId = String(item.parentId);
				nodesById[id] = {
					id,
					parentId,
					name_cat: item.name_cat,
					details: item.details,
					created: item.created,
					modified: item.modified,
					product_count: item.product_count || 0,
					isActive: item.isActive,
					children: []
				};
			});

			Object.values(nodesById).forEach(node => {
				if (node.parentId === '0') rootNodes.push(node);
				else if (nodesById[node.parentId])
					nodesById[node.parentId].children.push(node);
			});
		}

		function renderAllToTable() {
			const tbody = $('#categoryTable tbody');
			tbody.empty();

			function renderNode(node, level) {
				const indent = '— '.repeat(level);
				const btnText = node.children.length ? '+' : '—';
				const btnDisabled = node.children.length ? '' : 'disabled';
				const $tr = $(`
                <tr data-id="${node.id}" data-parent="${node.parentId}" data-level="${level}">
                	<td><input type="checkbox" class="row-check" data-id="${node.id}"></td>
                    <td><button class="expand" data-id="${node.id}" ${btnDisabled}>${btnText}</button></td>
                    <td>${indent}<a href="<?= base_url('admin/products?=') ?>${node.name_cat}">${node.name_cat}</a> (${node.product_count})</td>
                    <td>${node.details || '-'}</td>
                    <td>${node.created || '-'}</td>
                    <td>${node.modified || '-'}</td>
                    <td class="status">${node.isActive == 1 ? 'فعال' : 'غیرفعال'}</td>
                    <td>
                        <button class="edit" data-id="${node.id}">ویرایش</button>
                        <button class="delete" data-id="${node.id}">حذف</button>
                        <button class="add" data-id="${node.id}">افزودن</button>
                    </td>
                </tr>
            `);
				tbody.append($tr);
				node.children.forEach(ch => renderNode(ch, level + 1));
			}

			rootNodes.forEach(n => renderNode(n, 0));
		}

		function hideAllExceptRoots() {
			$('#categoryTable tbody tr').each(function() {
				const level = $(this).data('level');
				if (level > 0) $(this).hide();
			});
		}

		// Expand / Collapse
		$('#categoryTable').on('click', '.expand', function() {
			const id = $(this).data('id');
			const tr = $(this).closest('tr');

			if (tr.hasClass('shown')) {
				hideSubtree(id);
				tr.removeClass('shown');
				$(this).text('+');
			} else {
				showChildren(id);
				tr.addClass('shown');
				$(this).text('−');
			}
		});

		function showChildren(parentId) {
			$('#categoryTable tbody tr').each(function() {
				if ($(this).data('parent') == parentId) {
					$(this).show();
					const childId = $(this).data('id');
					const node = nodesById[childId];
					const $btn = $(this).find('.expand');
					if (node.children.length === 0) $btn.text('—').prop('disabled', true);
					else $btn.text('+').prop('disabled', false);
				}
			});
		}

		function hideSubtree(parentId) {
			$('#categoryTable tbody tr').each(function() {
				if ($(this).data('parent') == parentId) {
					const childId = $(this).data('id');
					$(this).hide();
					hideSubtree(childId);
					$(`tr[data-id="${childId}"]`).removeClass('shown');
					$(`tr[data-id="${childId}"] .expand`).text('+').prop('disabled', false);
				}
			});
		}

		// جستجو
		$('#searchInput').on('keyup', function() {
			const term = $(this).val().trim().toLowerCase();
			if (term === '') {
				$('#categoryTable tbody tr').each(function() {
					const tr = $(this);
					const level = tr.data('level');
					if (level === 0) tr.show();
					else tr.hide();
					tr.removeClass('shown');
					tr.find('.expand').text('+');
				});
				return;
			}

			$('#categoryTable tbody tr').hide();
			Object.values(nodesById).forEach(node => {
				if (node.name_cat.toLowerCase().includes(term)) {
					$(`tr[data-id="${node.id}"]`).show();
					showParents(node.id);
				}
			});
		});

		function showParents(id) {
			let current = nodesById[id];
			while (current && current.parentId !== '0') {
				$(`tr[data-id="${current.parentId}"]`).show();
				current = nodesById[current.parentId];
			}
		}

		// حذف
		$('#categoryTable').on('click', '.delete', function() {
			const id = $(this).data('id');
			if (!confirm('آیا مطمئن هستید؟')) return;

			const parentId = nodesById[id]?.parentId;
			deleteSubtree(id);
			if (parentId) updateParentAfterDelete(id);

			$.getJSON("<?= base_url('admin1/delete_category/') ?>" + id, function(res) {
				if (!res.success) alert('خطا در حذف از سرور');
				const x = document.getElementById("snackbar2");
				x.className = "show";
				setTimeout(() => x.className = x.className.replace("show", ""), 3000);
			});
		});


		let deletedIds = []; // آرایه‌ای برای ذخیره آیدی‌های حذف شده

		function deleteSubtree(id) {
			if (!nodesById[id]) return;

			// بازگشتی برای فرزندان
			nodesById[id].children.forEach(ch => deleteSubtree(ch.id));

			// ذخیره آیدی
			deletedIds.push(id);

			// حذف از DOM
			$(`tr[data-id="${id}"]`).remove();

			// حذف از children والد
			const parentId = nodesById[id].parentId;
			if (parentId && nodesById[parentId]) {
				nodesById[parentId].children = nodesById[parentId].children.filter(ch => ch.id != id);
			}

			// حذف از حافظه
			delete nodesById[id];

			// بعد از حذف فرزند، والدش را بررسی کن
			updateParentAfterDelete(parentId);
		}


		// افزودن
		$('#categoryTable').on('click', '.add', function() {
			const parentId = $(this).data('id');
			$('#insertForm').data('parent-id', parentId);
			$('#name_cat').val('');
			$('#details').val('');
			$('#insertModal').modal('show');
		});

		// ارسال فرم مودال
		$('#insertForm').on('submit', function(e) {
			e.preventDefault();

			const name_cat = $('#name_cat').val().trim();
			const details = $('#details').val().trim();
			const parentId = $(this).data('parent-id');

			if (!name_cat) { alert('نام دسته الزامی است.'); return; }

			$.post("<?= base_url('admin1/add_category') ?>", {
				name_cat, details: details, parentId
			}, function(res) {
				if (res.id) {
					const level = parseInt($('#categoryTable tr[data-id="' + parentId + '"]').data('level')) + 1;
					const indent = '— '.repeat(level);

					nodesById[res.id] = {
						id: res.id,
						parentId,
						name_cat: res.name_cat,
						details: res.details || '',
						created: res.created || '-',
						modified: res.modified || '-',
						isActive: 1,
						product_count: 0,
						children: []
					};
					nodesById[parentId].children.push(nodesById[res.id]);

					const $newRow = $(`
                    <tr data-id="${res.id}" data-parent="${parentId}" data-level="${level}">
                    	<td><input type="checkbox" class="row-check" data-id="${res.id}"></td>
                        <td><button class="expand" data-id="${res.id}" disabled>—</button></td>
                        <td>${indent}<a href="<?= base_url('admin1/products?category=') ?>${res.id}">${res.name_cat}</a> (0)</td>
                        <td>${res.details || '-'}</td>
                        <td>${res.created || '-'}</td>
                        <td>${res.modified || '-'}</td>
                        <td class="status">فعال</td>
                        <td>
                            <button class="edit" data-id="${res.id}">ویرایش</button>
                            <button class="delete" data-id="${res.id}">حذف</button>
                            <button class="add" data-id="${res.id}">افزودن</button>
                        </td>
                    </tr>
                `);

					$('#categoryTable tr[data-id="' + parentId + '"]').after($newRow);

					openParentAfterAdd(parentId); // باز کردن والد و نمایش فرزند

					$('#insertModal').modal('hide');

					const x = document.getElementById("snackbar");
					x.className = "show";
					setTimeout(() => x.className = x.className.replace("show", ""), 3000);
				} else {
					alert(res.msg || 'خطا در افزودن دسته.');
				}
			}, 'json');
		});

		// ------------------------
		// توابع جدید برای مدیریت والد
		// ------------------------
		// function openParentAfterAdd(parentId) {
		// 	const $parentBtn = $('#categoryTable').find('button.expand[data-id="' + parentId + '"]');
		// 	const $parentTr = $('#categoryTable').find('tr[data-id="' + parentId + '"]');
		//
		// 	$parentTr.addClass('shown');
		// 	$parentBtn.text('−').prop('disabled', false);
		// 	showChildren(parentId);
		// }

		function openParentAfterAdd(parentId) {
			const $parentBtn = $('#categoryTable').find('button.expand[data-id="' + parentId + '"]');
			const $parentTr = $('#categoryTable').find('tr[data-id="' + parentId + '"]');

			// والد را باز کن
			$parentTr.addClass('shown');
			$parentBtn.text('−').prop('disabled', false);

			// نمایش فرزندان والد
			nodesById[parentId].children.forEach(child => {
				const $childTr = $('#categoryTable').find('tr[data-id="' + child.id + '"]');
				$childTr.show();

				const $childBtn = $childTr.find('button.expand');
				const isOpen = $childTr.hasClass('shown'); // بررسی کن الان بازه یا نه

				if (child.children.length === 0) {
					$childBtn.text('—').prop('disabled', true);
				} else {
					// فقط اگر بسته بود، علامتشو برگردون
					if (!isOpen && $childBtn.text() !== '−') {
						$childBtn.text('+').prop('disabled', false);
					}
				}
			});
		}


		function updateParentAfterDelete(parentId) {
			if (!parentId || parentId === '0' || !nodesById[parentId]) return;

			const parentNode = nodesById[parentId];
			const $parentBtn = $(`button.expand[data-id="${parentId}"]`);
			const $parentTr = $(`tr[data-id="${parentId}"]`);

			if (parentNode.children.length === 0) {
				// هیچ فرزندی ندارد
				$parentBtn.text('—').prop('disabled', true);
			} else {
				// هنوز فرزند دارد
				if ($parentTr.hasClass('shown')) {
					$parentBtn.text('−').prop('disabled', false);
				} else {
					$parentBtn.text('+').prop('disabled', false);
				}
			}

			// بازگشتی تا بالا
			updateParentAfterDelete(parentNode.parentId);
		}

// وقتی روی دکمه ویرایش کلیک شد
		$('#categoryTable').on('click', '.edit', function() {
			const id = $(this).data('id');
			const node = nodesById[id];
			if (!node) return;

			// پر کردن فیلدهای مودال با مقادیر فعلی
			$('#edit_name_cat').val(node.name_cat);
			$('#edit_details').val(node.details);
			$('#editForm').data('id', id);

			// نمایش مودال
			$('#editModal').modal('show');
		});

// ارسال فرم مودال ویرایش
		$('#editForm').on('submit', function(e) {
			e.preventDefault();
			const id = $(this).data('id');
			const newName = $('#edit_name_cat').val().trim();
			const newDetails = $('#edit_details').val().trim();

			if (!newName) {
				alert('نام دسته الزامی است.');
				return;
			}

			$.post("<?= base_url('admin1/update_category') ?>", {
				id: id,
				name_cat: newName,
				details: newDetails
			}, function(res) {
				if (res.success) {
					// آپدیت در حافظه
					nodesById[id].name_cat = newName;
					nodesById[id].details = newDetails;
					nodesById[id].modified = res.modified || new Date().toISOString();

					// ✅ آپدیت دقیق در جدول (بدون تغییر در تابع render)
					const tr = $('#categoryTable').find(`tr[data-id="${id}"]`);
					tr.find('td:eq(2) a').text(newName);      // نام دسته
					tr.find('td:eq(3)').text(newDetails || '-'); // جزئیات
					tr.find('td:eq(5)').text(nodesById[id].modified); // تاریخ ویرایش

					$('#editModal').modal('hide');

					const x = document.getElementById("snackbar3");
					x.className = "show";
					setTimeout(() => x.className = x.className.replace("show", ""), 3000);
				} else {
					alert(res.msg || 'خطا در بروزرسانی');
				}
			}, 'json');
		});



// وقتی دکمه "جدید" بالایی کلیک شد، مودال رو باز کن
		$('#newCategoryButton').on('click', function() {
			// پاک کردن فیلدها
			$('#new_name_cat').val('');
			$('#new_details').val('');

			// پر کردن select والد
			const $select = $('#new_parent_id');
			$select.empty();
			$select.append('<option value="0">بدون والد</option>');

			function addOptions(nodes, level = 0) {
				nodes.forEach(node => {
					const indent = '— '.repeat(level);
					$select.append(`<option value="${node.id}">${indent}${node.name_cat}</option>`);
					if (node.children.length) addOptions(node.children, level + 1);
				});
			}

			addOptions(rootNodes);

			// نمایش مودال
			$('#newCategoryModal').modal('show');
		});

		// ارسال فرم مودال
		$('#newCategoryForm').on('submit', function(e) {
			e.preventDefault();

			const name_cat = $('#new_name_cat').val().trim();
			const details = $('#new_details').val().trim();
			const parentId = $('#new_parent_id').val();

			if (!name_cat) { alert('نام دسته الزامی است.'); return; }

			$.post("<?= base_url('admin1/add_category') ?>", {
				name_cat,
				details: details,
				parentId
			}, function(res) {
				if (res.id) {
					const level = parentId === '0' ? 0 : parseInt($(`#categoryTable tr[data-id="${parentId}"]`).data('level')) + 1;
					const indent = '— '.repeat(level);

					// ذخیره در حافظه JS
					nodesById[res.id] = {
						id: res.id,
						parentId,
						name_cat: res.name_cat,
						details: res.details || details,
						created: res.created || '-',
						modified: res.modified || '-',
						isActive: 1,
						product_count: 0,
						children: []
					};

					if (parentId !== '0') nodesById[parentId].children.push(nodesById[res.id]);

					// اضافه کردن ردیف جدید به جدول
					const $newRow = $(`
                    <tr data-id="${res.id}" data-parent="${parentId}" data-level="${level}">
                    	<td><input type="checkbox" class="row-check" data-id="${res.id}"></td>
                        <td><button class="expand" data-id="${res.id}" disabled>—</button></td>
                        <td>${indent}<a href="<?= base_url('admin1/products?category=') ?>${res.id}">${res.name_cat}</a> (0)</td>
                        <td>${details || '-'}</td>
                        <td>${res.created || '-'}</td>
                        <td>${res.modified || '-'}</td>
                        <td class="status">فعال</td>
                        <td>
                            <button class="edit" data-id="${res.id}">ویرایش</button>
                            <button class="delete" data-id="${res.id}">حذف</button>
                            <button class="add" data-id="${res.id}">افزودن</button>
                        </td>
                    </tr>
                `);

					if (parentId === '0') {
						$('#categoryTable tbody').append($newRow);
						rootNodes.push(nodesById[res.id]);
					} else {
						$(`#categoryTable tr[data-id="${parentId}"]`).after($newRow);
					}

					openParentAfterAdd(parentId);

					$('#newCategoryModal').modal('hide');

					const x = document.getElementById("snackbar");
					x.className = "show";
					setTimeout(() => x.className = x.className.replace("show", ""), 3000);
				} else {
					alert(res.msg || 'خطا در افزودن دسته.');
				}
			}, 'json');
		});






		// انتخاب همه
		$('#checkAll').on('change', function() {
			const checked = $(this).is(':checked');
			$('.row-check').prop('checked', checked);
		});

// وقتی یه دسته تیک می‌خوره، همهٔ زیر‌دسته‌هاش هم تیک بخورن یا برداشته بشن
		$('#categoryTable').on('change', '.row-check', function() {
			const id = $(this).closest('tr').data('id');
			const checked = $(this).is(':checked');

			// تابع بازگشتی برای اعمال وضعیت به همه فرزندان
			function checkChildren(nodeId, isChecked) {
				if (!nodesById[nodeId]) return;
				nodesById[nodeId].children.forEach(ch => {
					const $childCheckbox = $(`tr[data-id="${ch.id}"] .row-check`);
					$childCheckbox.prop('checked', isChecked);
					checkChildren(ch.id, isChecked);
				});
			}

			checkChildren(id, checked);

			// هماهنگی چک‌باکس هدر با ردیف‌ها
			const all = $('.row-check').length;
			const checkedCount = $('.row-check:checked').length;
			$('#checkAll').prop('checked', all === checkedCount);
		});


		$('#delete_selected').on('click', function() {
			console.log('hi');
			const selectedIds = $('.row-check:checked').map(function() {
				return $(this).closest('tr').data('id');
			}).get();

			if (selectedIds.length === 0) {
				alert('هیچ دسته‌ای انتخاب نشده است.');
				return;
			}

			if (!confirm('آیا از حذف دسته‌های انتخاب شده و فرزندانشان مطمئن هستید؟')) return;

			// حذف از جدول و حافظه JS
			selectedIds.forEach(id => deleteSubtree(id));

			// ارسال به سرور برای پاکسازی محصولات و دسته‌ها
			$.post("<?= base_url('admin1/delete_categories_bulk') ?>", {ids: selectedIds}, function(res) {
				if (!res.success) alert('خطا در حذف از سرور');
				else {
					const x = document.getElementById("snackbar2");
					x.className = "show";
					setTimeout(() => x.className = x.className.replace("show", ""), 3000);
				}
			}, 'json');
		});

		let deletedIdsMultiple = []; // آرایه‌ای برای ذخیره آیدی‌های حذف شده در حذف چندتایی

		function deleteSubtreeMultiple(id) {
			if (!nodesById[id]) return;

			// بازگشتی برای فرزندان
			nodesById[id].children.forEach(ch => deleteSubtreeMultiple(ch.id));

			// ذخیره آیدی
			deletedIdsMultiple.push(id);

			// حذف از DOM
			$(`tr[data-id="${id}"]`).remove();

			// حذف از children والد
			const parentId = nodesById[id].parentId;
			if (parentId && nodesById[parentId]) {
				nodesById[parentId].children = nodesById[parentId].children.filter(ch => ch.id != id);
			}

			// حذف از حافظه
			delete nodesById[id];

			// بعد از حذف فرزند، والدش را بررسی کن
			updateParentAfterDelete(parentId);
		}


// تابع بازگشتی برای گرفتن همه‌ی آیدی‌های زیرمجموعه‌ها
		function getAllChildIds(id, collected = []) {
			if (!nodesById[id]) return collected;
			collected.push(id);
			nodesById[id].children.forEach(ch => getAllChildIds(ch.id, collected));
			return collected;
		}

// فعال‌سازی چندتایی (با فرزندان)
		$('#active_selected').on('click', function() {
			if (!confirm('آیا مطمئن هستید؟')) return;
			const selectedIds = $('.row-check:checked').map(function() {
				return $(this).closest('tr').data('id');
			}).get();

			if (selectedIds.length === 0) {
				alert('هیچ دسته‌ای انتخاب نشده است.');
				return;
			}

			let allIds = [];
			selectedIds.forEach(id => {
				allIds = allIds.concat(getAllChildIds(id));
			});

			$.post("<?= base_url('admin1/update_categories_status_bulk') ?>", { ids: allIds, status: 1 }, function(res) {
				if (!res.success) {
					alert('خطا در فعال‌سازی دسته‌ها');
					return;
				}

				allIds.forEach(id => {
					$(`tr[data-id="${id}"] td.status`).text('فعال');
				});

				const x = document.getElementById("snackbar");
				x.className = "show";
				setTimeout(() => x.className = x.className.replace("show", ""), 3000);
			}, 'json');
		});

// غیرفعال‌سازی چندتایی (با فرزندان)
		$('#deactive_selected').on('click', function() {
			if (!confirm('آیا مطمئن هستید؟')) return;
			const selectedIds = $('.row-check:checked').map(function() {
				return $(this).closest('tr').data('id');
			}).get();

			if (selectedIds.length === 0) {
				alert('هیچ دسته‌ای انتخاب نشده است.');
				return;
			}

			let allIds = [];
			selectedIds.forEach(id => {
				allIds = allIds.concat(getAllChildIds(id));
			});

			$.post("<?= base_url('admin1/update_categories_status_bulk') ?>", { ids: allIds, status: 0 }, function(res) {
				if (!res.success) {
					alert('خطا در غیرفعال‌سازی دسته‌ها');
					return;
				}

				allIds.forEach(id => {
					$(`tr[data-id="${id}"] td.status`).text('غیرفعال');
				});

				const x = document.getElementById("snackbar");
				x.className = "show";
				setTimeout(() => x.className = x.className.replace("show", ""), 3000);
			}, 'json');
		});


		function clearAllCheckboxes() {
			$('.row-check, #checkAll').prop('checked', false);
		}

		$('#categoryTable').on('click', 'button:not(#delete_selected, #active_selected, #deactive_selected)', function() {
			clearAllCheckboxes();
		});


	});



</script>
