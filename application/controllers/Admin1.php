<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin1 extends CI_Controller
{
	//<<---------------date_shamsi_ghamari---------------->>
	function date_j($miladi_date){
		//gregorian_to_jalali without time
		$exploadeddate = explode(' ',$miladi_date);
		$gmtdate = explode('-',$exploadeddate[0]);
		$persiandate=$this->jalali_date->gregorian_to_jalali($gmtdate[0],$gmtdate[1],$gmtdate[2],'/');
		return $persiandate;
	}
	//<<---------------end date_shamsi_ghamari---------------->>

	public function categories()
	{
		$data['title']='دسته بندی محصولات';
		$data['category'] = $this->base_model->get_data('category_test', '*');
		$data['products'] = $this->base_model->get_data('products', '*');

		$this->load->view('Admin/layout/header', $data);
		$this->load->view('Admin/layout/sidebar');
		$this->load->view('Admin/cat-test4');

	}

	// لود دسته‌های سطح صفر برای DataTable
	public function categories_table() {
		$categories = $this->base_model->get_data('category_test', '*', NULL, NULL, NULL, NULL, 'name_cat ASC');
		foreach ($categories as $cat) {
			$cat->product_count = $this->base_model->get_data(
				'products',
				'COUNT(*) as count',
				['id_cat1' => $cat->id]
			)[0]->count;
		}
		echo json_encode($categories);
	}

	// AJAX: افزودن دسته جدید
	public function add_category()
	{
		$name = $this->input->post('name_cat');
		$details = $this->input->post('details'); // جزئیات از فرم مودال
		$parentId = $this->input->post('parentId');

		// داده‌های دسته جدید
		$data = [
			'name_cat'  => $name,
			'details'    => $details,
			'parentId'  => $parentId,
			'isActive'  => 1,
			'created'   => date('Y-m-d H:i:s'),
			'modified'  => date('Y-m-d H:i:s')
		];

		// درج رکورد جدید
		$id = $this->base_model->insert('category_test', $data);

		if ($id) {
			// دریافت رکورد درج‌شده
			$row = $this->db->get_where('category_test', ['id' => $id])->row_array();

			// 🧾 توضیحات
			$detailsText = "دسته '{$row['name_cat']}' ایجاد شد.";

			// 📌 ساخت group_id و توضیح کلی عملیات
			$group_id = uniqid('grp_', true);
			$operationInfo = "عملیات افزودن دسته‌بندی جدید";

			// 📜 ثبت لاگ
			$this->base_model->add_log(
				'category_test',   // entity_type
				$id,               // entity_id
				'insert',          // action
				null,              // old_value
				$data,             // new_value
				$detailsText,      // details
				$group_id,         // group_id
				$operationInfo     // operation_info
			);
		}

		// پاسخ JSON برای AJAX
		echo json_encode([
			'id'        => $row['id'],
			'name_cat'  => $row['name_cat'],
			'parentId'  => $row['parentId'],
			'details'   => $row['details'],
			'created'   => $row['created'],
			'modified'  => $row['modified'],
			'isActive'  => $row['isActive']
		]);
	}

	public function delete_category($id)
	{
		$this->db->trans_start();

		// 📌 ساخت group_id مشترک برای تمام لاگ‌های این عملیات
		$group_id = uniqid('grp_', true);

		// 📌 تعریف توضیح کلی عملیات برای همه‌ی لاگ‌ها
		$operationInfo = "عملیات حذف دسته‌بندی و محصولات مرتبط";

		$deletedCategories = []; // ذخیره نام دسته‌هایی که حذف می‌شن
		$deletedIds = [];        // ذخیره آیدی‌ها برای حذف محصولات

		// 🌀 تابع بازگشتی حذف دسته‌ها و فرزندان
		$deleteRecursive = function($catId) use (&$deleteRecursive, &$deletedCategories, &$deletedIds, $group_id, $operationInfo) {
			$children = $this->base_model->get_data('category_test', 'id', ['parentId' => $catId]);

			foreach ($children as $child) {
				$deleteRecursive($child->id);
			}

			$oldData = $this->db->get_where('category_test', ['id' => $catId])->row_array();

			if ($oldData) {
				$deletedCategories[] = $oldData['name_cat'];
				$deletedIds[] = $catId;

				// حذف دسته
				$this->base_model->delete_row('category_test', 'id', $catId);

				// ثبت لاگ حذف برای دسته
				$detailsText = "دسته '{$oldData['name_cat']}' حذف شد.";
				$this->base_model->add_log(
					'category_test',
					$catId,
					'delete',
					$oldData,
					null,
					$detailsText,
					$group_id,
					$operationInfo
				);
			}
		};

		// اجرای حذف بازگشتی
		$deleteRecursive($id);

		// 🧾 اگر محصولاتی مرتبط با این دسته‌ها وجود داشته باشند
		if (!empty($deletedIds)) {
			$products = $this->db->where_in('id_cat1', $deletedIds)->get('products')->result_array();

			if (!empty($products)) {
				foreach ($products as $prd) {
					$oldPrd = $prd;
					$prd['id_cat1'] = null; // حذف دسته از محصول

					$detailsText = "محصول '{$oldPrd['name']}' از دسته‌بندی حذف شد.";

					// ثبت لاگ برای هر محصول
					$this->base_model->add_log(
						'products',
						$prd['id'],
						'update',
						$oldPrd,
						$prd,
						$detailsText,
						$group_id,
						$operationInfo
					);
				}

				// آپدیت محصولات
				$this->base_model->update_rows_by_col('id_cat1', $deletedIds, 'products', ['id_cat1' => NULL]);
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['success' => false, 'msg' => 'خطا در حذف دسته‌ها و محصولات']);
		} else {
			// 📜 ثبت لاگ کلی (خلاصه عملیات)
			if (!empty($deletedCategories)) {
				$summaryDetails = "دسته‌ها و زیرمجموعه‌های حذف‌شده: " . implode('، ', $deletedCategories);
				$this->base_model->add_log(
					'category_test',
					null,
					'delete_group',
					null,
					null,
					$summaryDetails,
					$group_id,
					$operationInfo
				);
			}

			echo json_encode(['success' => true, 'deleted_ids' => $deletedIds]);
		}
	}

	//اگه فقط «جزئیات» تغییر کرده باشه، دیتیل می‌گه: «دسته فلان ویرایش شد»
	//اگه اسمش هم عوض شده باشه، دیتیل دقیق‌تر می‌گه: «نام دسته از قدیمی به جدید ویرایش شد»
	public function update_category()
	{
		$id        = $this->input->post('id');
		$name_cat  = $this->input->post('name_cat');
		$details   = $this->input->post('details');

		if (!$id || !$name_cat) {
			echo json_encode(['success' => false, 'msg' => 'اطلاعات ناقص است']);
			return;
		}

		// گرفتن داده‌های قبلی برای لاگ
		$oldData = $this->db->get_where('category_test', ['id' => $id])->row_array();

		// آماده‌سازی داده‌های جدید
		$data = [
			'name_cat' => $name_cat,
			'details'   => $details,
			'modified' => date('Y-m-d H:i:s')
		];

		// بروزرسانی رکورد
		$res = $this->base_model->update('category_test', ['id' => $id], $data);

		if ($res) {
			// بررسی تغییر نام برای نوشتن دیتیل دقیق‌تر
			if ($oldData['name_cat'] !== $name_cat) {
				$detailsText = "نام دسته از «{$oldData['name_cat']}» به «{$name_cat}» ویرایش شد.";
			} else {
				$detailsText = "دسته «{$name_cat}» ویرایش شد.";
			}

			// 📌 ساخت group_id و operation_info
			$group_id = uniqid('grp_', true);
			$operationInfo = "عملیات ویرایش دسته‌بندی";

			// 📜 ثبت لاگ با مدل جدید
			$this->base_model->add_log(
				'category_test',   // entity_type
				$id,               // entity_id
				'update',          // action
				$oldData,          // old_value
				$data,             // new_value
				$detailsText,      // details
				$group_id,         // group_id
				$operationInfo     // operation_info
			);

			echo json_encode([
				'success'  => true,
				'modified' => $data['modified'],
			]);
		} else {
			echo json_encode(['success' => false, 'msg' => 'خطا در بروزرسانی']);
		}
	}


	public function delete_categories_bulk() {
		$ids = $this->input->post('ids');
		if (!$ids) {
			echo json_encode(['success' => false]);
			return;
		}

		$this->db->trans_start();

		$groupId = uniqid('grp_', true); // شناسه گروهی
		$operationInfo = 'عملیات حذف گروهی دسته‌بندی و محصولات مرتبط';
		$allDeletedIds = [];

		foreach ($ids as $id) {
			$this->delete_category_and_children($id, $allDeletedIds, $groupId, $operationInfo);
		}

		if (!empty($allDeletedIds)) {
			$products = $this->db->where_in('id_cat1', $allDeletedIds)->get('products')->result_array();
			foreach ($products as $prd) {
				$oldPrd = $prd;
				$prd['id_cat1'] = null;

				$detailsText = "محصول '{$oldPrd['name']}' از دسته‌بندی حذف شد";

				$this->base_model->add_log(
					'products',
					$prd['id'],
					'update',
					$oldPrd,
					$prd,
					$detailsText,
					$groupId,
					$operationInfo
				);
			}

			$this->base_model->update_rows_by_col('id_cat1', $allDeletedIds, 'products', ['id_cat1' => null]);
		}

		$this->db->trans_complete();

		echo json_encode(['success' => $this->db->trans_status()]);
	}

	private function delete_category_and_children($id, &$allDeletedIds, $groupId, $operationInfo) {
		$children = $this->base_model->get_data('category_test', 'id', ['parentId' => $id]);
		foreach ($children as $child) {
			$this->delete_category_and_children($child->id, $allDeletedIds, $groupId, $operationInfo);
		}

		$oldData = $this->db->get_where('category_test', ['id' => $id])->row_array();
		if ($oldData) {
			$this->base_model->delete_row('category_test', 'id', $id);
			$allDeletedIds[] = $id;

			$detailsText = "دسته '{$oldData['name_cat']}' حذف شد";

			$this->base_model->add_log(
				'category_test',
				$id,
				'delete',
				$oldData,
				null,
				$detailsText,
				$groupId,
				$operationInfo
			);
		}
	}


	public function update_categories_status_bulk() {
		$ids = $this->input->post('ids'); // آرایه آیدی‌ها
		$status = $this->input->post('status'); // 0 یا 1

		if (!$ids || !isset($status)) {
			echo json_encode(['success' => false]);
			return;
		}

		// حذف آی‌دی‌های تکراری
		$ids = array_unique($ids);

		// گرفتن داده‌های قبلی برای لاگ
		$oldCategories = $this->db->where_in('id', $ids)->get('category_test')->result_array();

		// آماده‌سازی داده‌های جدید
		$dataUpdate = ['isActive' => $status, 'modified' => date('Y-m-d H:i:s')];
		$this->base_model->update_rows_by_col('id', $ids, 'category_test', $dataUpdate);

		// ایجاد شناسه گروهی یکتا
		$groupId = uniqid('grp_', true);

		foreach ($oldCategories as $oldCat) {
			// ساخت new_value برای هر دسته
			$newCat = array_merge($oldCat, ['isActive' => $status, 'modified' => $dataUpdate['modified']]);

			// ساخت details اختصاصی برای هر دسته
			$actionText = $status == 1 ? 'فعال شد' : 'غیرفعال شد';
			$detailsText = "دسته '{$oldCat['name_cat']}' $actionText";

			// ثبت لاگ اختصاصی
			$this->base_model->add_log(
				'category_test',
				$oldCat['id'],
				'update',
				$oldCat,                // old_value
				$newCat,                // new_value
				$detailsText,           // details اختصاصی
				$groupId,               // group_id مشترک
				$status == 1 ? 'عملیات فعال‌سازی دسته‌ها' : 'عملیات غیرفعال‌سازی دسته‌ها' // operation_info
			);
		}

		echo json_encode(['success' => true]);
	}











}
