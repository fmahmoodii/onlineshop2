<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('base_model');
		$this->load->library('jalali_date'); // اجباری برای اطمینان از لود صحیح
		$this->load->library('form_validation');
		$this->load->helper('form');
	}
	protected function check_permission($permissions, $table = null)
	{
		$user_id = $this->session->userdata('id');

		if (!$user_id) {
			echo json_encode(['status' => 0, 'message' => 'session_expired']);
			exit;
		}

		if (!$this->base_model->has_permission($user_id, $permissions, $table)) {
			echo json_encode(['status' => 0, 'message' => 'شما دسترسی انجام این عملیات را ندارید']);
			exit;
		}

		return $user_id;
	}
	protected function get_protected_user_ids()
	{
		// کاربرانی که فلگ is_protected دارن
		$protected = $this->base_model->get_data('users', 'id', ['is_protected' => 1]);
		$protected_users = array_column($protected, 'id');

		// کاربرانی که role ادمین دارن
		$admin_role = $this->base_model->get_data('roles', 'id', ['role_name' => 'ادمین']);

		if (!empty($admin_role)) {
			$admin_role_id = $admin_role[0]->id;
			$admin_users = $this->base_model->get_data('user_roles', 'user_id', ['role_id' => $admin_role_id]);
			$protected_users = array_merge($protected_users, array_column($admin_users, 'user_id'));
		}

		return array_unique($protected_users);
	}
	/**
	 *  یکسان‌سازی فرمت شماره موبایل به حالت استاندارد: 09xxxxxxxxx
	 * ورودی می‌تونه +989xx، 989xx، 09xx یا 9xx باشه
	 * خروجی همیشه: 09xxxxxxxxx (11 رقم)
	 */
	private function _normalize_phone($phone)
	{
		if (empty($phone)) {
			return $phone;
		}

		// حذف همه کاراکترها به جز عدد
		$phone = preg_replace('/[^\d]/', '', $phone);

		// حذف پیشوند 98 (از +98 یا 0098 اومده باشه)
		if (substr($phone, 0, 2) === '98' && strlen($phone) === 12) {
			$phone = substr($phone, 2);
		}

		// اگه با 9 شروع می‌شه و 10 رقمیه (بدون صفر)، صفر جلوش اضافه کن
		if (substr($phone, 0, 1) === '9' && strlen($phone) === 10) {
			$phone = '0' . $phone;
		}

		return $phone; // نتیجه نهایی همیشه: 09xxxxxxxxx
	}

	//<<--------------- date_shamsi_ghamari ---------------->>
	public function date_j($miladi_date)
	{
		if (empty($miladi_date)) return null;

		$exploadeddate = explode(' ', $miladi_date);
		$gmtdate = explode('-', $exploadeddate[0]);
		return $this->jalali_date->gregorian_to_jalali($gmtdate[0], $gmtdate[1], $gmtdate[2], '-');
	}
	//<<--------------- end date_shamsi_ghamari ---------------->>

	// صفحه‌ی اصلی ادمین (داشبورد)
	public function index()
	{
		// بررسی لاگین بودن
		if (!$this->session->userdata('id')) {
			redirect('admin/login_page');
			return;
		}

		$data['title'] = 'داشبورد';
		$data['products'] = $this->base_model->get_data('products', '*');

		// داده‌های نمودار (نمونه‌ی ساده)
		$query1 = $this->db->query("SELECT COUNT(id_p) AS count FROM shopping_cart_order GROUP BY YEAR(date) ORDER BY date");
		$data['click'] = json_encode(array_column($query1->result(), 'count'), JSON_NUMERIC_CHECK);

		// (در صورت نیاز این رو با داده‌ی متفاوت جایگزین کن)
		$query2 = $this->db->query("SELECT COUNT(id_p) AS count FROM shopping_cart_order GROUP BY YEAR(date) ORDER BY date");
		$data['viewer'] = json_encode(array_column($query2->result(), 'count'), JSON_NUMERIC_CHECK);

		$this->load->view('admin/admin-panel', $data);
	}

	// صفحه‌ی ورود
	public function login_page()
	{
		if ($this->session->userdata('id')) {
			redirect('admin');
			return;
		}

		$data['title'] = 'ورود';
		$this->load->view('admin/layout/header2', $data);
		$this->load->view('admin/login');
	}

	// لاگین
	public function login()
	{
		if (!$this->input->post()) {
			return redirect('admin/login_page');
		}

		$username = $this->input->post('phone_number', true);
		$password = $this->input->post('password', true);

		$group_id      = uniqid('grp_', true);
		$operationInfo = "ورود ادمین";


		// 1) دریافت کاربر
		$users = $this->base_model->get_data('users', '*', array('phone_number' => $username));

		if (empty($users)) {
			$this->base_model->add_log(
				'admin',
				null,
				'login_failed_username_not_found',
				null,
				null,
				'نام کاربری یافت نشد: ' . $username,
				$group_id,
				$operationInfo
			);

			$this->session->set_flashdata('err', 'نام کاربری یا رمز عبور اشتباه است');
			return redirect('admin/login_page');
		}

		// آرایه به آبجکت تبدیل شود
		$users = $users[0];


		// 2) بررسی رمز عبور
		if (!password_verify($password, $users->password)) {

			$this->base_model->add_log(
				'admin',
				$users->id,
				'login_failed_wrong_password',
				null,
				null,
				'رمز عبور اشتباه برای نام کاربری: ' . $username,
				$group_id,
				$operationInfo
			);

			$this->session->set_flashdata('err', 'نام کاربری یا رمز عبور اشتباه است');
			return redirect('admin/login_page');
		}


		// 3) بررسی فعال بودن
		if ($users->isActive != 1) {

			$this->base_model->add_log(
				'admin',
				$users->id,
				'login_inactive_user',
				null,
				null,
				'تلاش برای ورود کاربر غیرفعال: ' . $username,
				$group_id,
				$operationInfo
			);

			$this->session->set_flashdata('err', 'حساب شما فعال نیست. لطفا با مدیریت تماس بگیرید');
			return redirect('admin/login_page');
		}


		// 4) بررسی پرمیشن ورود به پنل
		if (!$this->base_model->has_permission($users->id, 'login_adminPanel')) {

			$this->base_model->add_log(
				'admin',
				$users->id,
				'login_no_admin_permission',
				null,
				null,
				'تلاش برای ورود کاربر بدون مجوز: ' . $username,
				$group_id,
				$operationInfo
			);

			$this->session->set_flashdata('err', 'شما دسترسی ورود به پنل مدیریت را ندارید');
			return redirect('admin/login_page');
		}


		// 5) ورود موفق
		$this->session->sess_regenerate(TRUE);
		$this->session->set_userdata('id', $users->id);

		$this->base_model->add_log(
			'admin',
			$users->id,
			'login_success',
			null,
			null,
			'ورود موفق با نام کاربری: ' . $username,
			$group_id,
			$operationInfo
		);

		return redirect('admin');
	}

	// خروج از حساب
	public function logout()
	{
		// دریافت ID از سشن
		$user_id = $this->session->userdata('id');

		if ($user_id) {

			// گرفتن اطلاعات کاربر
			$user = $this->base_model->get_data('users', 'phone_number', array('id' => $user_id));
			$username = isset($user[0]) ? $user[0]->phone_number : 'نامشخص';

			$group_id = uniqid('grp_', true);

			// ثبت لاگ خروج
			$this->base_model->add_log(
				'admin',                        // entity_type
				$user_id,                       // entity_id
				'logout',                       // action
				null,                           // old_value
				null,                           // new_value
				"خروج موفق از پنل ادمین توسط: $username", // details
				$group_id,                      // group_id
				'خروج ادمین'                    // operation_info
			);
		}

		// حذف تمام سشن ها
		$this->session->sess_destroy();

		return redirect('admin/login_page');
	}

	public function registered_users()
	{
		if (!$this->session->userdata('id')) {
			redirect('admin/login_page');
			return;
		}

		$user_id = $this->session->userdata('id');
		$has_view = $this->base_model->has_permission($user_id, ['view','full'], 'users');

		if (!$has_view)
		{
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		// فقط وقتی permission داره، query ها رو اجرا کن
		$data = [];
		$data['profile']     = $this->base_model->get_data('profile','*');
		$data['users']       = $this->base_model->get_data('users','*');
		$data['roles']       = $this->base_model->get_data('roles','*');
		$data['user_roles']  = $this->base_model->get_data('user_roles','*');

		$data['permissions'] = [
			'add'    => $this->base_model->has_permission($user_id, ['insert','full'], 'users'),
			'edit'   => $this->base_model->has_permission($user_id, ['edit','full'], 'users'),
			'delete' => $this->base_model->has_permission($user_id, ['delete','full'], 'users'),
		];

		$data['title'] = 'کاربران';

		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/registered-users');
	}

	public function users_list()
	{
		$user_id = $this->check_permission(['view', 'full'], 'users');

		$post_data = $this->input->post(NULL, TRUE);

		$columns = [
			null,
			'roles.role_name',
			'profile.name',
			'profile.family',
			'users.phone_number',
			'users.created',
			'users.modified',
			null, null, null, null
		];

		$join = [
			'profile'     => 'users.id = profile.user_id',
			'user_roles'  => 'users.id = user_roles.user_id',
			'roles'       => 'user_roles.role_id = roles.id'
		];

		$table  = 'users';
		$select = '
        profile.id AS profile_id,
        users.id AS user_id,
        roles.role_name AS role,
        profile.name,
        profile.family,
        users.phone_number,
        users.created,
        users.modified,
        users.isActive
    ';

		$result = $this->base_model->datatable(
			$table,
			$columns,
			$post_data,
			$select,
			$join,
			null,
			['users.id' => 'DESC']
		);

		$permissions = [
			'delete' => $this->base_model->has_permission($user_id, ['delete', 'full'], 'users'),
			'edit'   => $this->base_model->has_permission($user_id, ['edit', 'full'], 'users'),
			'add'    => $this->base_model->has_permission($user_id, ['insert', 'full'], 'users')
		];

		$data = [];
		foreach ($result['data'] as $row) {

			$sub_array = [];

			// Checkbox
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check"
                    user_id="'.htmlspecialchars($row->user_id).'"
                    id_prof="'.htmlspecialchars($row->profile_id).'">';

			// Role
			$sub_array[] = htmlspecialchars($row->role);

			// Profile fields
			$sub_array[] = htmlspecialchars($row->name);
			$sub_array[] = htmlspecialchars($row->family);

			// Phone
			$sub_array[] = htmlspecialchars($row->phone_number);

			// Dates
			$sub_array[] = htmlspecialchars($row->created);
			$sub_array[] = htmlspecialchars($row->modified);

			// Active / Deactive button
			$sub_array[] = ($row->isActive == 0)
				? '<button type="button" id="active" user_id="'.$row->user_id.'" id_prof="'.$row->profile_id.'" class="btn btn-primary btn-xs">فعالسازی</button>'
				: '<button type="button" id="deactive" user_id="'.$row->user_id.'" id_prof="'.$row->profile_id.'" class="btn btn-secondry btn-xs">غیرفعالسازی</button>';

			// Reset password
			$sub_array[] = '<button type="button" id="reset" user_id="'.$row->user_id.'" id_prof="'.$row->profile_id.'" class="btn btn-info"><i class="fa fa-key"></i></button>';

			// Edit
			$sub_array[] = '<a href="'.base_url('admin/edit_user/'.$row->user_id).'">
            <button class="btn btn-warning edit-btn" '.($permissions['edit'] ? '' : 'data-no-permission="true"').'>
                <i class="fa fa-edit"></i>
            </button>
        </a>';

			// Delete
			$sub_array[] = '<button id="delete" user_id="'.$row->user_id.'" id_prof="'.$row->profile_id.'" class="btn btn-danger"
'.($permissions['delete'] ? '' : 'data-no-permission="true"').'><i class="fa fa-trash"></i></button>';

			$data[] = $sub_array;
		}

		$output = [
			"draw"            => isset($post_data['draw']) ? intval($post_data['draw']) : 0,
			"recordsTotal"    => $result['recordsTotal'],
			"recordsFiltered" => $result['recordsFiltered'],
			"data"            => $data
		];

		echo json_encode($output);
	}

	public function soft_delete_user()
	{
		// 1️⃣ چک دسترسی
		$is_user = $this->check_permission(['delete', 'full'], 'users');

		// 2️⃣ اعتبارسنجی ورودی
		$post_data = $this->input->post(NULL, TRUE);

		if (empty($post_data) || !isset($post_data['user_ids']) || !is_array($post_data['user_ids'])) {
			echo json_encode([
				'status'  => 0,
				'message' => 'داده نامعتبر است'
			]);
			return;
		}

		$user_ids = $post_data['user_ids'];

		// 3️⃣ جلوگیری از حذف خود کاربر لاگین‌شده
		if (in_array($is_user, $user_ids)) {
			echo json_encode([
				'status'  => 0,
				'message' => 'امکان حذف حساب کاربری خودتان وجود ندارد'
			]);
			return;
		}

		// 4️⃣ جلوگیری از حذف کاربر محافظت‌شده
		$protected_users = $this->get_protected_user_ids();

		if (array_intersect($protected_users, $user_ids)) {
			echo json_encode(['status' => 0, 'message' => 'امکان حذف این کاربر وجود ندارد']);
			return;
		}

		$group_id      = uniqid('grp_', true);
		$operationInfo = "حذف کاربران";

		// 5️⃣ گرفتن اطلاعات قبل از حذف
		$users      = $this->base_model->get_data('users', '*', null, null, ['id' => $user_ids]);
		$profiles   = $this->base_model->get_data('profile', '*', null, null, ['user_id' => $user_ids]);
		$user_roles = $this->base_model->get_data('user_roles', '*', null, null, ['user_id' => $user_ids]);

		if (empty($users)) {
			echo json_encode([
				'status'  => 0,
				'message' => 'کاربر مورد نظر یافت نشد'
			]);
			return;
		}

		// map پروفایل بر اساس user_id
		$profileMap = [];
		foreach ($profiles as $p) {
			$profileMap[$p->user_id] = $p;
		}

		// 6️⃣ حذف نرم با transaction
		$this->db->trans_start();

		$this->base_model->soft_delete('profile', $user_ids, true, 'user_id');
		$this->base_model->soft_delete('user_roles', $user_ids, true, 'user_id');
		$this->base_model->soft_delete('users', $user_ids, true, 'id');

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode([
				'status'  => 0,
				'message' => 'خطا در حذف کاربران'
			]);
			return;
		}

		// 7️⃣ ثبت لاگ‌ها
		foreach ($users as $user) {
			$fullName = isset($profileMap[$user->id])
				? $profileMap[$user->id]->name . ' ' . $profileMap[$user->id]->family
				: 'نامشخص';

			$userData = (array)$user;
			unset($userData['password']);

			$this->base_model->add_log(
				'users',
				$user->id,
				'soft_delete',
				$userData,
				null,
				'حذف کاربر: ' . $fullName,
				$group_id,
				$operationInfo
			);
		}

		foreach ($profiles as $p) {
			$this->base_model->add_log(
				'profile',
				$p->id,
				'soft_delete',
				(array)$p,
				null,
				'حذف پروفایل کاربر: ' . $p->name . ' ' . $p->family,
				$group_id,
				$operationInfo
			);
		}

		foreach ($user_roles as $ur) {
			$fullName = isset($profileMap[$ur->user_id])
				? $profileMap[$ur->user_id]->name . ' ' . $profileMap[$ur->user_id]->family
				: 'نامشخص';

			$this->base_model->add_log(
				'user_roles',
				$ur->id,
				'soft_delete',
				(array)$ur,
				null,
				'حذف نقش کاربر: ' . $fullName,
				$group_id,
				$operationInfo
			);
		}

		echo json_encode([
			'status' => 1
		]);
	}

	public function toggle_user_status()
	{
		// 1️⃣ چک دسترسی
		$logged_user_id = $this->check_permission(['edit', 'full'], 'users');
		// 2️⃣ دریافت ورودی
		$post_data = $this->input->post(NULL, TRUE);

		if (empty($post_data)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		if (isset($post_data['user_ids']) && is_array($post_data['user_ids'])) {
			$user_ids = $post_data['user_ids'];
		} elseif (isset($post_data['user_id'])) {
			$user_ids = [$post_data['user_id']];
		} else {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		if (empty($user_ids)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$status = isset($post_data['status']) ? intval($post_data['status']) : 1;

		// 3️⃣ جلوگیری از تغییر وضعیت خود کاربر لاگین‌شده
		if (in_array($logged_user_id, $user_ids)) {
			echo json_encode(['status' => 0, 'message' => 'امکان تغییر وضعیت حساب کاربری خودتان وجود ندارد']);
			return;
		}

		// 4️⃣ جلوگیری از تغییر وضعیت کاربر محافظت‌شده
		$protected_users = $this->get_protected_user_ids();

		if (array_intersect($protected_users, $user_ids)) {
			echo json_encode(['status' => 0, 'message' => 'امکان تغییر وضعیت این کاربر وجود ندارد']);
			return;
		}

		// 5️⃣ گرفتن اطلاعات قبل از تغییر
		$users_before = $this->base_model->get_data('users', '*', NULL, NULL, ['id' => $user_ids]);

		if (empty($users_before)) {
			echo json_encode(['status' => 0, 'message' => 'کاربر مورد نظر یافت نشد']);
			return;
		}

		$group_id      = uniqid('grp_', true);
		$operationInfo = "تغییر وضعیت کاربران";

		// 6️⃣ آپدیت با Transaction
		$this->db->trans_start();

		foreach ($users_before as $user) {
			$this->base_model->update_data('users', ['isActive' => $status], ['id' => $user->id]);
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در تغییر وضعیت کاربران']);
			return;
		}

		// 7️⃣ ثبت لاگ‌ها
		foreach ($users_before as $user) {
			$old_value = ['isActive' => $user->isActive];
			$new_value = ['isActive' => $status];

			$details = ($status ? 'فعالسازی ' : 'غیرفعالسازی ') .
				'کاربر با شماره موبایل: ' . $user->phone_number;

			$this->base_model->add_log(
				'users',
				$user->id,
				'update_status',
				$old_value,
				$new_value,
				$details,
				$group_id,
				$operationInfo
			);
		}

		echo json_encode(['status' => 1]);
	}

	public function reset_pass()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$new_pass = $_POST['new_pass'];
			$re_pass = $_POST['re_new_pass'];

			if ($new_pass === $re_pass) {
				// گرفتن اطلاعات قبلی کاربر
				$user_before = $this->base_model->get_data('users', '*', ['id' => $id]);
				if (!$user_before || !isset($user_before[0])) {
					echo 0; // کاربر پیدا نشد
					return;
				}

				$user_before = (array) $user_before[0]; // برای ذخیره در old_value
				$old_value = $user_before;
				unset($old_value['password']); //  حذف هش پسورد قدیمی از لاگ

				// هش کردن رمز جدید
				$hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);

				// آپدیت رمز در جدول users
				$this->base_model->update_data('users', ['password' => $hashed_pass], ['id' => $id]);

				// گرفتن اطلاعات جدید بعد از آپدیت
				$user_after = $this->base_model->get_data('users', '*', ['id' => $id]);
				$user_after = isset($user_after[0]) ? (array) $user_after[0] : [];
				unset($user_after['password']); //  حذف هش پسورد جدید از لاگ

				// آماده‌سازی داده‌های لاگ
				$group_id = uniqid('grp_', true);
				$operationInfo = "تغییر رمز عبور کاربر";

				// ثبت لاگ
				$this->base_model->add_log(
					'users',                         // entity_type
					$id,                                // entity_id
					'update_password',                  // action
					$old_value,                         // old_value = اطلاعات قبلی کامل
					$user_after,                        // new_value = اطلاعات جدید کامل
					'تغییر رمز عبور برای کاربر با شماره موبایل: ' . $user_before['phone_number'], // details
					$group_id,                          // group_id
					$operationInfo                      // operation_info
				);

				echo 1; // موفق
			} else {
				echo 0; // رمزها مطابقت ندارند
			}
		} else {
			echo 0; // درخواست POST نیست
		}
	}

	/**
	 *  بررسی صحت شماره موبایل اصلی (الزامی)
	 */
	public function _phoneRegex($phone_number)
	{
		if (empty($phone_number)) {
			return FALSE; // الزامی است، بنابراین اگر خالی باشد false برگردون
		}

		if (preg_match('/^(\+98|0)?9\d{9}$/', $phone_number)) {
			return TRUE;
		}

		$this->form_validation->set_message('_phoneRegex', 'فرمت شماره موبایل نادرست است');
		return FALSE;
	}

	/**
	 *  بررسی صحت شماره موبایل ضروری (اختیاری)
	 */
	public function _phoneRegex2($phone_number)
	{
		if (empty($phone_number)) {
			return TRUE; // اختیاری است، خالی بودن مجازه
		}

		if (preg_match('/^(\+98|0)?9\d{9}$/', $phone_number)) {
			return TRUE;
		}

		$this->form_validation->set_message('_phoneRegex2', 'فرمت شماره موبایل نادرست است');
		return FALSE;
	}

	/**
	 *  بررسی صحت کد پستی (10 رقم)
	 */
	public function _postal_check($postal_code)
	{
		if (empty($postal_code)) {
			return TRUE; // اختیاری است
		}

		if (preg_match('/^\d{10}$/', $postal_code)) {
			return TRUE;
		}

		$this->form_validation->set_message('_postal_check', 'کد پستی باید 10 رقم باشد');
		return FALSE;
	}

	/**
	 *  دریافت شهرها برای استان انتخاب شده (AJAX)
	 */
	public function get_city()
	{
		if (!$this->input->post('province_id')) {
			echo '<option value="">انتخاب کنید</option>';
			return;
		}

		$province_id = $this->input->post('province_id', TRUE);

		//  از base_model استفاده کنید
		$cities = $this->base_model->get_data(
			'city',
			'id, name',
			['province_id' => $province_id]
		);

		$result = '<option value="">انتخاب کنید</option>';

		foreach ($cities as $city) {
			$result .= sprintf(
				'<option value="%d">%s</option>',
				$city->id,
				htmlspecialchars($city->name, ENT_QUOTES, 'UTF-8')
			);
		}

		echo $result;
	}

	/**
	 *  بررسی تکراری بودن شماره موبایل (AJAX)
	 */
	public function check_phone()
	{
		if (!$this->input->post('phone_number')) {
			echo 'exists';
			return;
		}

		//  نرمال‌سازی قبل از بررسی
		$phone = $this->_normalize_phone($this->input->post('phone_number', TRUE));

		//  صحت شماره را قبل از دیتابیس چک کنید
		if (!preg_match('/^(\+98|0)?9\d{9}$/', $phone)) {
			echo 'invalid';
			return;
		}

		//  استفاده صحیح از get_data
		$existing = $this->base_model->get_data(
			'users',
			'id',
			['phone_number' => $phone]
		);

		if (!empty($existing)) {
			echo 'exists';
		} else {
			echo 'ok';
		}
	}

	/**
	 *  Callback برای بررسی تکراری نبودن شماره موبایل در فرم validation
	 */
	public function _check_phone_unique($phone)
	{
		if (empty($phone)) {
			return TRUE; // required خودش خالی بودن رو چک می‌کنه
		}

		//  نرمال‌سازی قبل از بررسی تکراری بودن
		$phone = $this->_normalize_phone($phone);

		$existing = $this->base_model->get_data(
			'users',
			'id',
			['phone_number' => $phone]
		);

		if (!empty($existing)) {
			$this->form_validation->set_message('_check_phone_unique', 'کاربری با این شماره موبایل قبلاً ثبت شده است');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 *  نمایش فرم درج کاربر
	 */
	public function insert_user()
	{
		//  بررسی دسترسی
		$user_id = $this->session->userdata('id');
		if (!$user_id) {
			redirect('login');
			return;
		}

		$data = [
			'title' => 'افزودن کاربر جدید',
			'roles' => $this->base_model->get_data('roles', '*', ['isActive' => 1]),
			'province' => $this->base_model->get_data('province', '*')
		];

		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert_user', $data);
	}

	/**
	 *  ثبت کاربر جدید
	 */
	public function add_user()
	{
		// 1️⃣ بررسی دسترسی
		$user_id = $this->session->userdata('id');
		if (!$user_id) {
			$this->session->set_flashdata('error', 'لطفاً وارد سیستم شوید');
			redirect('login');
			return;
		}

		if (!$this->base_model->has_permission($user_id, ['insert', 'full'], 'users')) {
			$this->session->set_flashdata('error', 'شما دسترسی افزودن کاربر را ندارید');
			redirect('admin/registered_users');
			return;
		}

		// 2️⃣ اگر فرم ارسال نشده، به فرم برگردید
		if (!$this->input->post()) {
			return $this->insert_user();
		}

		// 3️⃣ تنظیم قوانین validation
		$this->_set_validation_rules();

		// 4️⃣ اجرای validation (شامل بررسی تکراری بودن شماره موبایل)
		if (!$this->form_validation->run()) {
			return $this->insert_user();
		}

//  نرمال‌سازی شماره‌ها قبل از استفاده
		$phone_number = $this->_normalize_phone($this->input->post('phone_number', TRUE));
		$phone_number1 = $this->_normalize_phone($this->input->post('phone_number1', TRUE));
		
		// 6️⃣ شروع تراکنش
		$this->db->trans_start();

		try {
			//  محافظت در برابر Race Condition:
			// با SELECT ... FOR UPDATE ردیف‌های فعال با این شماره رو قفل می‌کنیم
			// تا درخواست‌های همزمان دیگه نتونن همزمان همین بررسی رو بگذرونن
			$lock_check = $this->db
				->select('id')
				->from('users')
				->where('phone_number', $phone_number)
				->where('deleted_at', NULL)
				->get()
				->result();

			if (!empty($lock_check)) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'کاربری با این شماره موبایل قبلاً ثبت شده است');
				redirect('admin/insert_user');
				return;
			}

			$now = date('Y-m-d H:i:s');
			$group_id = uniqid('grp_', true);
			$operation_info = 'افزودن کاربر جدید: ' . $phone_number;

			//  داده‌های جدول users
			$user_data = [
				'phone_number' => $phone_number,
				'password' => password_hash($this->input->post('password', TRUE), PASSWORD_DEFAULT),
				'isActive' => 0,
				'created' => $now,
				'modified' => $now
			];
			$new_user_id = $this->base_model->insert_data('users', $user_data);

			if (!$new_user_id) {
				throw new Exception('خرابی در افزودن کاربر');
			}

			//  دوباره‌بررسی نهایی بعد از insert: اگه بیشتر از یک کاربر فعال
			// با این شماره پیدا شد (یعنی یه درخواست موازی هم درج کرده)، rollback کن
			$duplicate_check = $this->base_model->get_data(
				'users',
				'id',
				['phone_number' => $phone_number]
			);

			if (count($duplicate_check) > 1) {
				throw new Exception('این شماره موبایل همزمان توسط درخواست دیگری ثبت شد');
			}

			//  داده‌های جدول profile
			$profile_data = [
				'user_id' => $new_user_id,
				'name' => $this->input->post('name', TRUE) ?: '',
				'family' => $this->input->post('family', TRUE) ?: '',
				'reciever_phone_number' => $this->input->post('phone_number1', TRUE) ?: NULL,
				'ostan' => $this->input->post('ostan', TRUE) ?: NULL,
				'city' => $this->input->post('city', TRUE) ?: NULL,
				'address' => $this->input->post('address', TRUE) ?: '',
				'postal_code' => $this->input->post('postal_code', TRUE) ?: NULL,
				'created' => $now,
				'modified' => $now
			];
			$this->base_model->insert_data('profile', $profile_data);

			//  داده‌های جدول user_roles
			$role_id = $this->input->post('role', TRUE);
			$role_data = [
				'user_id' => $new_user_id,
				'role_id' => $role_id,
				'isActive' => 1,
				'created' => $now,
				'modified' => $now
			];
			$this->base_model->insert_data('user_roles', $role_data);

			//  ثبت لاگ‌ها
			$this->base_model->add_log(
				'users',
				$new_user_id,
				'add_user_success',
				NULL,
				$user_data,
				'کاربر جدید ایجاد شد',
				$group_id,
				$operation_info
			);

			$this->base_model->add_log(
				'profile',
				$new_user_id,
				'add_user_success',
				NULL,
				$profile_data,
				'پروفایل کاربر ایجاد شد',
				$group_id,
				$operation_info
			);

			//  اتمام تراکنش
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				throw new Exception('خرابی در ذخیره‌سازی');
			}

			$this->session->set_flashdata('success', 'کاربر با موفقیت افزوده شد');
			redirect('admin/insert_user');

		} catch (Exception $e) {
			$this->db->trans_rollback();
			log_message('error', 'Add User Error: ' . $e->getMessage());
			$this->session->set_flashdata('error', 'خرابی رخ داد: ' . $e->getMessage());
			redirect('admin/insert_user');
		}
	}

	/**
	 *  تنظیم قوانین validation
	 */
	private function _set_validation_rules()
	{
		$this->form_validation->set_message('required', '{field} الزامی است');
		$this->form_validation->set_message('min_length', '{field} باید حداقل {param} کاراکتر داشته باشد');
		$this->form_validation->set_message('max_length', '{field} باید حداکثر {param} کاراکتر داشته باشد');
		$this->form_validation->set_message('_phoneRegex', 'شماره موبایل نادرست است');
		$this->form_validation->set_message('_phoneRegex2', 'شماره موبایل ضروری نادرست است');
		$this->form_validation->set_message('_check_phone_unique', 'کاربری با این شماره موبایل قبلاً ثبت شده است');

		$rules = [
			['field' => 'role', 'label' => 'نوع کاربر', 'rules' => 'required|integer'],
			['field' => 'password', 'label' => 'رمز عبور', 'rules' => 'required|min_length[8]|max_length[25]'],
			['field' => 'phone_number', 'label' => 'شماره موبایل', 'rules' => 'required|callback__phoneRegex|callback__check_phone_unique'],
			['field' => 'phone_number1', 'label' => 'شماره موبایل ضروری', 'rules' => 'callback__phoneRegex2'],  //  استفاده از _phoneRegex2
			['field' => 'postal_code', 'label' => 'کد پستی', 'rules' => 'callback__postal_check'],
			['field' => 'name', 'label' => 'نام', 'rules' => 'max_length[25]'],
			['field' => 'family', 'label' => 'نام خانوادگی', 'rules' => 'max_length[25]'],
			['field' => 'address', 'label' => 'آدرس', 'rules' => 'max_length[255]']
		];

		$this->form_validation->set_rules($rules);
	}

	/**
	 *  نمایش فرم ویرایش کاربر
	 */
	public function edit_user($id)
	{
		// 1️⃣ بررسی لاگین بودن
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			redirect('admin/login_page');
			return;
		}

		// 2️⃣ بررسی دسترسی مشاهده/ویرایش
		if (!$this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'users')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		// 3️⃣ بررسی وجود کاربر
		$user_exists = $this->base_model->get_data('users', 'id', ['id' => $id]);
		if (empty($user_exists)) {
			$this->session->set_flashdata('error', 'کاربر مورد نظر یافت نشد');
			redirect('admin/registered_users');
			return;
		}

		$data['title']='ویرایش کاربر';
		$data['profile']=$this->base_model->get_data('profile','*',array('user_id'=>$id));
		$data['roles']=$this->base_model->get_data('roles','*');
		$data['user_roles']=$this->base_model->get_data('user_roles','*');
		$data['province']=$this->base_model->get_data('province','*');
		$data['city']=$this->base_model->get_data('city','*');
		$data['users']=$this->base_model-> get_data('users','*',array('id'=>$id));

		//  پیدا کردن role_id کاربر بدون نیاز به join/alias
		$current_user_role = $this->base_model->get_data('user_roles', 'role_id', ['user_id' => $id]);
		$data['user_data'] = !empty($current_user_role)
			? [ (object) ['user_id' => $id, 'role_id' => $current_user_role[0]->role_id] ]
			: [];


		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit-user', $data);
	}

	/**
	 *  ثبت تغییرات ویرایش کاربر
	 */
	public function edit_u($id)
	{
		// 1️⃣ چک دسترسی – حتماً در ابتدای متد
		$logged_user_id = $this->session->userdata('id');

		if (!$logged_user_id) {
			$this->session->set_flashdata('error', 'لطفاً وارد سیستم شوید');
			redirect('admin/login_page');
			return;
		}

		if (!$this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'users')) {
			$this->session->set_flashdata('error', 'شما دسترسی ویرایش کاربر را ندارید');
			redirect('admin/registered_users');
			return;
		}

		// 2️⃣ بررسی وجود کاربر
		$user_exists = $this->base_model->get_data('users', 'id', ['id' => $id]);
		if (empty($user_exists)) {
			$this->session->set_flashdata('error', 'کاربر مورد نظر یافت نشد');
			redirect('admin/registered_users');
			return;
		}

		// 3️⃣ جلوگیری از ویرایش کاربر محافظت‌شده توسط غیرمجاز
		// (در صورتی که بخوای فقط خودشون بتونن پروفایل خودشون رو بزنن، این شرط رو تغییر بده)
		$protected_users = $this->get_protected_user_ids();
		if (in_array((int)$id, $protected_users) && $logged_user_id != $id) {
			$this->session->set_flashdata('error', 'امکان ویرایش این کاربر وجود ندارد');
			redirect('admin/registered_users');
			return;
		}

		if (!$this->input->post()) {
			return $this->edit_user($id);
		}

		$this->load->library('form_validation');
		$this->load->helper('form');

		// 4️⃣ پیام‌ها
		$this->form_validation->set_message('required', '{field} الزامی است');
		$this->form_validation->set_message('min_length', '{field} باید حداقل {param} کاراکتر داشته باشد');
		$this->form_validation->set_message('max_length', '{field} باید حداکثر {param} کاراکتر داشته باشد');
		$this->form_validation->set_message('_phoneRegex2', 'شماره وارد شده نادرست است');
		$this->form_validation->set_message('_postal_check', 'در صورت ورود کدپستی، باید 10 رقم باشد');

		// 5️⃣ قوانین
		$rules = [
			['field' => 'role', 'label' => 'نوع کاربر', 'rules' => 'required|integer'],
			['field' => 'phone_number1', 'label' => 'شماره موبایل ضروری', 'rules' => 'callback__phoneRegex2'],
			['field' => 'postal_code', 'label' => 'کد پستی', 'rules' => 'callback__postal_check'],
			['field' => 'name', 'label' => 'نام', 'rules' => 'max_length[25]'],
			['field' => 'family', 'label' => 'نام خانوادگی', 'rules' => 'max_length[25]'],
			['field' => 'address', 'label' => 'آدرس', 'rules' => 'max_length[255]']
		];
		$this->form_validation->set_rules($rules);

		if (!$this->form_validation->run()) {
			return $this->edit_user($id);
		}

		//  زمان صحیح (فقط میلادی، مطابق ستون DATETIME)
		$modified_time = date('Y-m-d H:i:s');

		//  نرمال‌سازی شماره موبایل ضروری
		$phone_number1 = $this->_normalize_phone($this->input->post('phone_number1', TRUE));

		// داده‌های جدید (با فیلتر XSS)
		$new_profile = [
			'name' => $this->input->post('name', TRUE),
			'family' => $this->input->post('family', TRUE),
			'reciever_phone_number' => $phone_number1 ?: NULL,
			'ostan' => $this->input->post('ostan', TRUE) ?: NULL,
			'city' => $this->input->post('city', TRUE) ?: NULL,
			'address' => $this->input->post('address', TRUE),
			'postal_code' => $this->input->post('postal_code', TRUE) ?: NULL,
			'modified' => $modified_time
		];

		$new_register = [
			'modified' => $modified_time,
		];

		$new_user_roles = [
			'role_id' => $this->input->post('role', TRUE),
			'modified' => $modified_time
		];

		// --- داده‌های قبلی برای لاگ ---
		$old_profile_row = $this->base_model->get_data('profile', '*', ['user_id' => $id]);
		$old_register_row = $this->base_model->get_data('users', '*', ['id' => $id]);
		$old_user_roles_row = $this->base_model->get_data('user_roles', '*', ['user_id' => $id]);

		if (empty($old_profile_row) || empty($old_register_row) || empty($old_user_roles_row)) {
			$this->session->set_flashdata('error', 'اطلاعات کاربر ناقص است');
			redirect('admin/registered_users');
			return;
		}

		$old_profile = (array)$old_profile_row[0];
		$old_register = (array)$old_register_row[0];
		$old_user_roles = (array)$old_user_roles_row[0];

		$group_id = uniqid('grp_', true);
		$phone_number = $old_register['phone_number'];

		// --- محاسبه تفاوت‌ها برای لاگ (قبل از آپدیت) ---
		$diff_p_old = $diff_p_new = [];
		foreach ($new_profile as $k => $v) {
			if ((string)$old_profile[$k] !== (string)$v) {
				$diff_p_old[$k] = $old_profile[$k];
				$diff_p_new[$k] = $v;
			}
		}

		$diff_r_old = $diff_r_new = [];
		foreach ($new_register as $k => $v) {
			if ((string)$old_register[$k] !== (string)$v) {
				$diff_r_old[$k] = $old_register[$k];
				$diff_r_new[$k] = $v;
			}
		}

		$diff_ur_old = $diff_ur_new = [];
		if ((string)$old_user_roles['role_id'] !== (string)$new_user_roles['role_id']) {
			$diff_ur_old['role_id'] = $old_user_roles['role_id'];
			$diff_ur_new['role_id'] = $new_user_roles['role_id'];
		}

		//  6️⃣ آپدیت با Transaction
		$this->db->trans_start();

		try {
			$this->base_model->update_data('profile', $new_profile, ['user_id' => $id]);
			$this->base_model->update_data('users', $new_register, ['id' => $id]);
			$this->base_model->update_data('user_roles', $new_user_roles, ['user_id' => $id]);

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				throw new Exception('خرابی در بروزرسانی اطلاعات');
			}

			// --- ثبت لاگ‌ها (فقط بعد از موفقیت تراکنش) ---
			if (!empty($diff_p_old)) {
				$this->base_model->add_log('profile', $id, 'update', $diff_p_old, $diff_p_new, 'ویرایش کاربر با شماره موبایل: ' . $phone_number, $group_id, 'ویرایش اطلاعات کاربر');
			}

			if (!empty($diff_r_old)) {
				$this->base_model->add_log('users', $id, 'update', $diff_r_old, $diff_r_new, 'ویرایش کاربر با شماره موبایل: ' . $phone_number, $group_id, 'ویرایش اطلاعات کاربر');
			}

			if (!empty($diff_ur_old)) {
				$this->base_model->add_log('user_roles', $id, 'update', $diff_ur_old, $diff_ur_new, 'تغییر نقش کاربر با شماره موبایل: ' . $phone_number, $group_id, 'ویرایش نقش کاربر');
			}

			$this->session->set_flashdata('success', 'کاربر با موفقیت ویرایش شد');
			redirect('admin/registered_users');

		} catch (Exception $e) {
			$this->db->trans_rollback();
			log_message('error', 'Edit User Error: ' . $e->getMessage());
			$this->session->set_flashdata('error', 'خطا در ویرایش کاربر: ' . $e->getMessage());
			redirect('admin/edit_user/'.$id);
		}
	}

	/**
	 *  حذف نرم دسترسی(ها) + پاک کردن ارتباطشون از نقش‌ها
	 */
	public function soft_delete_permission()
	{
		$this->check_permission(['delete', 'full'], 'permissions');

		$post_data = $this->input->post(NULL, TRUE);

		if (empty($post_data) || !isset($post_data['prm_ids']) || !is_array($post_data['prm_ids'])) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$prm_ids = $post_data['prm_ids'];

		$perms_before = $this->base_model->get_data('permissions', '*', null, null, ['id' => $prm_ids]);
		if (empty($perms_before)) {
			echo json_encode(['status' => 0, 'message' => 'دسترسی مورد نظر یافت نشد']);
			return;
		}

		$group_id = uniqid('grp_', true);

		$this->db->trans_start();

		//  حذف نرم خود permission
		$this->base_model->soft_delete('permissions', $prm_ids, true, 'id');

		//  پاک کردن کامل ارتباط این permission از همه نقش‌ها
		$this->base_model->delete_data('role_permissions', null, ['permission_id' => $prm_ids]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در حذف']);
			return;
		}

		foreach ($perms_before as $perm) {
			$permData = (array)$perm;
			$this->base_model->add_log(
				'permissions',
				$perm->id,
				'soft_delete',
				$permData,
				null,
				'حذف دسترسی: ' . $perm->name,
				$group_id,
				'حذف دسترسی'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 *  فعال/غیرفعال کردن دسترسی(ها)
	 */
	public function toggle_permission_status()
	{
		$logged_user_id = $this->check_permission(['edit', 'full'], 'permissions');

		$post_data = $this->input->post(NULL, TRUE);

		if (empty($post_data) || !isset($post_data['prm_ids']) || !is_array($post_data['prm_ids'])) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$prm_ids = $post_data['prm_ids'];
		$status = isset($post_data['status']) ? intval($post_data['status']) : 1;

		$perms_before = $this->base_model->get_data('permissions', '*', null, null, ['id' => $prm_ids]);
		if (empty($perms_before)) {
			echo json_encode(['status' => 0, 'message' => 'دسترسی مورد نظر یافت نشد']);
			return;
		}

		$group_id = uniqid('grp_', true);

		$this->db->trans_start();

		foreach ($perms_before as $perm) {
			$this->base_model->update_data('permissions', ['isActive' => $status], ['id' => $perm->id]);
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در تغییر وضعیت']);
			return;
		}

		foreach ($perms_before as $perm) {
			$this->base_model->add_log(
				'permissions',
				$perm->id,
				'update_status',
				['isActive' => $perm->isActive],
				['isActive' => $status],
				($status ? 'فعالسازی ' : 'غیرفعالسازی ') . 'دسترسی: ' . $perm->name,
				$group_id,
				'تغییر وضعیت دسترسی'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 *  نمایش صفحه تنظیمات دسترسی
	 */
	public function permissions()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			redirect('admin/login_page');
			return;
		}

		$has_view = $this->base_model->has_permission($logged_user_id, ['view', 'full'], 'permissions');
		if (!$has_view) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		//  اصلاح باگ: قبلاً کلید 'permissions' دوبار استفاده شده بود و مقدار اول پاک می‌شد
		$data['action_permissions'] = [
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'permissions'),
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'permissions'),
			'add'    => $this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'permissions'),
			'view'   => $has_view,
		];

		//  فقط چیزی که واقعاً توی ویو لازمه رو می‌فرستیم (بقیه از طریق AJAX لود می‌شن)
		$data['roles'] = $this->base_model->get_data('roles', '*', ['isActive' => 1]);

		$data['title'] = 'تنظیمات دسترسی';
		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/permissions', $data);
	}

	/**
	 *  لیست دسترسی‌ها برای DataTables
	 */
	public function permissions_list()
	{
		$user_id = $this->check_permission(['view', 'full'], 'permissions');

		$post_data = $this->input->post(NULL, TRUE);

		$columns = [
			null,
			'permissions.name',
			'permissions.key_name',
			'permissions.table_name',
			null, // نقش‌های متصل (قابل جستجو/مرتب‌سازی نیست چون چندتاییه)
			null,
			null
		];

		$table  = 'permissions';
		$select = '
			permissions.id,
			permissions.name,
			permissions.key_name,
			permissions.table_name AS table_name,
			permissions.isActive
		';

		$result = $this->base_model->datatable(
			$table,
			$columns,
			$post_data,
			$select,
			null,
			null,
			['permissions.table_name' => 'ASC', 'permissions.key_name' => 'DESC']
		);

		$permissions_check = [
			'delete' => $this->base_model->has_permission($user_id, ['delete', 'full'], 'permissions'),
			'edit'   => $this->base_model->has_permission($user_id, ['edit', 'full'], 'permissions'),
		];

		//  گرفتن نقش‌های متصل به هر دسترسی (یکجا، نه توی حلقه با کوئری جدا)
		$permission_ids = array_column($result['data'], 'id');
		$roles_map = [];

		if (!empty($permission_ids)) {
			$role_links = $this->base_model->get_data(
				'role_permissions',
				'permission_id, roles.role_name',
				['role_permissions.isActive' => 1],
				null,                                    // like
				['permission_id' => $permission_ids],     // where_in
				null,                                     // where_not_in
				null,                                     // or_where
				['roles' => 'roles.id = role_permissions.role_id'] // join
			);

			foreach ($role_links as $link) {
				$roles_map[$link->permission_id][] = $link->role_name;
			}
		}

		$data = [];
		foreach ($result['data'] as $row) {

			$sub_array = [];

			// Checkbox
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" prm_id="'.$row->id.'">';

			// Permission name
			$sub_array[] = htmlspecialchars($row->name);

			// key_name
			$sub_array[] = htmlspecialchars($row->key_name);

			// table_name
			$sub_array[] = htmlspecialchars($row->table_name);

			//  نقش‌های متصل (به‌صورت badge)
			$assigned_roles = isset($roles_map[$row->id]) ? $roles_map[$row->id] : [];
			if (!empty($assigned_roles)) {
				$badges = array_map(function($r) {
					return '<span class="badge badge-info" style="margin-left:3px;">' . htmlspecialchars($r) . '</span>';
				}, $assigned_roles);
				$sub_array[] = implode(' ', $badges);
			} else {
				$sub_array[] = '<span class="text-muted">بدون نقش</span>';
			}

			//  دکمه مدیریت نقش‌ها
			$sub_array[] = '<button type="button" id="manage_roles" prm_id="'.$row->id.'" class="btn btn-info btn-xs"
				'.($permissions_check['edit'] ? '' : 'data-no-permission="true"').'>
				<i class="fa fa-users"></i> نقش‌ها
			</button>';

			//  دکمه ویرایش
			$sub_array[] = '<button type="button" id="edit_permission" prm_id="'.$row->id.'" class="btn btn-warning"
				'.($permissions_check['edit'] ? '' : 'data-no-permission="true"').'><i class="fa fa-edit fa-lg"></i></button>';

			// Active / Deactive button
			$sub_array[] = ($row->isActive == 0)
				? '<button type="button" id="active" prm_id="'.$row->id.'" class="btn btn-primary btn-xs"
					'.($permissions_check['edit'] ? '' : 'data-no-permission="true"').'>فعالسازی</button>'
				: '<button type="button" id="deactive" prm_id="'.$row->id.'" class="btn btn-secondry btn-xs"
					'.($permissions_check['edit'] ? '' : 'data-no-permission="true"').'>غیرفعالسازی</button>';

			//  دکمه حذف
			$sub_array[] = '<button id="delete" prm_id="'.$row->id.'" class="btn btn-danger"
				'.($permissions_check['delete'] ? '' : 'data-no-permission="true"').'><i class="fa fa-trash fa-lg"></i></button>';

			$data[] = $sub_array;
		}

		$output = [
			"draw"            => isset($post_data['draw']) ? intval($post_data['draw']) : 0,
			"recordsTotal"    => $result['recordsTotal'],
			"recordsFiltered" => $result['recordsFiltered'],
			"data"            => $data
		];

		echo json_encode($output);
	}

	/**
	 *  گرفتن اطلاعات یک دسترسی (برای پر کردن مودال ویرایش)
	 */
	public function get_permission()
	{
		$this->check_permission(['view', 'full'], 'permissions');

		$id = $this->input->post('id', TRUE);
		if (!$id) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$perm = $this->base_model->get_data('permissions', '*', ['id' => $id]);
		if (empty($perm)) {
			echo json_encode(['status' => 0, 'message' => 'یافت نشد']);
			return;
		}

		echo json_encode(['status' => 1, 'data' => $perm[0]]);
	}

	/**
	 *  افزودن دسترسی جدید (از طریق مودال)
	 */
	public function insert_permission()
	{
		$this->check_permission(['insert', 'full'], 'permissions');

		$name = $this->input->post('name', TRUE);
		$key_name = $this->input->post('key_name', TRUE);
		$table_name = $this->input->post('table_name', TRUE) ?: NULL;

		if (empty($name) || empty($key_name)) {
			echo json_encode(['status' => 0, 'message' => 'نام و کلید دسترسی الزامی است']);
			return;
		}

		$now = date('Y-m-d H:i:s');
		$data = [
			'name'       => $name,
			'key_name'   => $key_name,
			'table_name' => $table_name,
			'isActive'   => 1,
			'created'    => $now,
			'modified'   => $now
		];

		$new_id = $this->base_model->insert_data('permissions', $data);

		if (!$new_id) {
			echo json_encode(['status' => 0, 'message' => 'خطا در افزودن دسترسی']);
			return;
		}

		$this->base_model->add_log(
			'permissions', $new_id, 'insert', null, $data,
			'افزودن دسترسی جدید: ' . $name, uniqid('grp_', true), 'افزودن دسترسی'
		);

		echo json_encode(['status' => 1]);
	}

	/**
	 *  ویرایش دسترسی موجود (از طریق مودال)
	 */
	public function update_permission()
	{
		$this->check_permission(['edit', 'full'], 'permissions');

		$id = $this->input->post('id', TRUE);
		$name = $this->input->post('name', TRUE);
		$key_name = $this->input->post('key_name', TRUE);
		$table_name = $this->input->post('table_name', TRUE) ?: NULL;

		if (!$id || empty($name) || empty($key_name)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$old_row = $this->base_model->get_data('permissions', '*', ['id' => $id]);
		if (empty($old_row)) {
			echo json_encode(['status' => 0, 'message' => 'دسترسی یافت نشد']);
			return;
		}
		$old = $old_row[0];

		$now = date('Y-m-d H:i:s');
		$new_data = [
			'name'       => $name,
			'key_name'   => $key_name,
			'table_name' => $table_name,
			'modified'   => $now
		];

		$this->base_model->update_data('permissions', $new_data, ['id' => $id]);

		// --- محاسبه تفاوت برای لاگ ---
		$diff_old = $diff_new = [];
		foreach ($new_data as $k => $v) {
			if ((string)$old->$k !== (string)$v) {
				$diff_old[$k] = $old->$k;
				$diff_new[$k] = $v;
			}
		}

		if (!empty($diff_old)) {
			$this->base_model->add_log(
				'permissions', $id, 'update', $diff_old, $diff_new,
				'ویرایش دسترسی: ' . $name, uniqid('grp_', true), 'ویرایش دسترسی'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 *  افزودن گروهی یک یا چند نقش به چند دسترسی انتخاب‌شده (بدون حذف نقش‌های قبلی)
	 */
	public function bulk_assign_roles()
	{
		$this->check_permission(['edit', 'full'], 'permissions');

		$post_data = $this->input->post(NULL, TRUE);
		$prm_ids  = (isset($post_data['prm_ids']) && is_array($post_data['prm_ids'])) ? $post_data['prm_ids'] : [];
		$role_ids = (isset($post_data['role_ids']) && is_array($post_data['role_ids'])) ? $post_data['role_ids'] : [];

		if (empty($prm_ids) || empty($role_ids)) {
			echo json_encode(['status' => 0, 'message' => 'حداقل یک دسترسی و یک نقش انتخاب کنید']);
			return;
		}

		$now = date('Y-m-d H:i:s');
		$group_id = uniqid('grp_', true);

		$this->db->trans_start();

		foreach ($prm_ids as $permission_id) {

			//  نقش‌های موجود این دسترسی رو می‌گیریم تا دوباره اضافه نشن (جلوگیری از تکراری)
			$existing = $this->base_model->get_data(
				'role_permissions', 'role_id',
				['permission_id' => $permission_id, 'isActive' => 1]
			);
			$existing_role_ids = array_map(function($r) { return (int)$r->role_id; }, $existing);

			$batch = [];
			foreach ($role_ids as $role_id) {
				if (!in_array((int)$role_id, $existing_role_ids)) {
					$batch[] = [
						'permission_id' => $permission_id,
						'role_id'       => (int)$role_id,
						'isActive'      => 1,
						'created'       => $now,
						'modified'      => $now
					];
				}
			}

			if (!empty($batch)) {
				$this->base_model->insert_data('role_permissions', $batch, true);
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در ذخیره‌سازی']);
			return;
		}

		$this->base_model->add_log(
			'role_permissions', null, 'bulk_assign', null,
			['permission_ids' => $prm_ids, 'role_ids' => $role_ids],
			'افزودن گروهی نقش به چند دسترسی', $group_id, 'افزودن گروهی نقش'
		);

		echo json_encode(['status' => 1]);
	}

	/**
	 *  گرفتن نقش‌های فعلی یک دسترسی (برای پر کردن مودال)
	 */
	public function get_permission_roles()
	{
		$this->check_permission(['view', 'full'], 'permissions');

		$permission_id = $this->input->post('permission_id', TRUE);

		if (!$permission_id) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$links = $this->base_model->get_data(
			'role_permissions',
			'role_id',
			['permission_id' => $permission_id, 'isActive' => 1]
		);

		$role_ids = array_map(function($r) { return (int)$r->role_id; }, $links);

		echo json_encode(['status' => 1, 'role_ids' => $role_ids]);
	}

	/**
	 *  ذخیره نقش‌های انتخاب‌شده برای یک دسترسی (چندتایی)
	 */
	public function save_permission_roles()
	{
		$user_id = $this->check_permission(['edit', 'full'], 'permissions');

		$permission_id = $this->input->post('permission_id', TRUE);
		$role_ids = $this->input->post('role_ids', TRUE); // آرایه‌ای از role_id ها

		if (!$permission_id) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		if (!is_array($role_ids)) {
			$role_ids = [];
		}

		//  بررسی وجود خود دسترسی
		$perm_exists = $this->base_model->get_data('permissions', 'id, name', ['id' => $permission_id]);
		if (empty($perm_exists)) {
			echo json_encode(['status' => 0, 'message' => 'دسترسی مورد نظر یافت نشد']);
			return;
		}

		// --- گرفتن وضعیت قبلی برای لاگ ---
		$old_links = $this->base_model->get_data(
			'role_permissions',
			'role_id',
			['permission_id' => $permission_id, 'isActive' => 1]
		);
		$old_role_ids = array_map(function($r) { return (int)$r->role_id; }, $old_links);

		$group_id = uniqid('grp_', true);
		$now = date('Y-m-d H:i:s');

		$this->db->trans_start();

		try {
			//  حذف همه ارتباط‌های قبلی این permission
			$this->base_model->delete_data('role_permissions', ['permission_id' => $permission_id]);

			//  درج ارتباط‌های جدید
			if (!empty($role_ids)) {
				$batch_data = [];
				foreach ($role_ids as $role_id) {
					$batch_data[] = [
						'permission_id' => $permission_id,
						'role_id'       => (int)$role_id,
						'isActive'      => 1,
						'created'       => $now,
						'modified'      => $now
					];
				}
				$this->base_model->insert_data('role_permissions', $batch_data, true);
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				throw new Exception('خرابی در ذخیره‌سازی');
			}

			// --- ثبت لاگ ---
			$this->base_model->add_log(
				'role_permissions',
				$permission_id,
				'update',
				['role_ids' => $old_role_ids],
				['role_ids' => array_map('intval', $role_ids)],
				'تغییر نقش‌های متصل به دسترسی: ' . $perm_exists[0]->name,
				$group_id,
				'مدیریت اتصال نقش به دسترسی'
			);

			echo json_encode(['status' => 1]);

		} catch (Exception $e) {
			$this->db->trans_rollback();
			log_message('error', 'Save Permission Roles Error: ' . $e->getMessage());
			echo json_encode(['status' => 0, 'message' => 'خطا در ذخیره‌سازی']);
		}
	}


	/**
	 * ✅ نمایش صفحه مدیریت دسته‌بندی
	 */
	public function categories()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			redirect('admin/login_page');
			return;
		}

		if (!$this->base_model->has_permission($logged_user_id, ['view', 'full'], 'categories')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		$data['action_permissions'] = [
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'categories'),
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'categories'),
			'add'    => $this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'categories'),
		];

		$data['title'] = 'دسته‌بندی محصولات';
		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/categories', $data);
	}

	/**
	 * ✅ لیست دسته‌بندی‌ها به‌صورت جدول تخت با تورفتگی (بر اساس عمق درخت)
	 * توجه: چون تعداد دسته‌ها معمولاً زیاد نیست، از DataTables سمت کلاینت استفاده می‌کنیم
	 * (نه serverSide) تا بتونیم ترتیب درختی رو خودمون توی PHP بسازیم.
	 */
	public function categories_list()
	{
		$this->check_permission(['view', 'full'], 'categories');

		// ✅ گرفتن همه دسته‌بندی‌ها
		$all = $this->base_model->get_data(
			'categories', '*', null, null, null, null, null, null,
			['parentId' => 'ASC', 'name_cat' => 'ASC']
		);

		// ✅ گرفتن تعداد محصولات برای همه دسته‌ها (یه کوئری به‌جای N تا)
		$products = $this->base_model->get_data('products', 'id_cat');
		$product_count_map = [];
		foreach ($products as $p) {
			if ($p->id_cat) {
				$product_count_map[$p->id_cat] = ($product_count_map[$p->id_cat] ?? 0) + 1;
			}
		}

		// ✅ گروه‌بندی بر اساس parentId برای ساخت درخت
		$by_parent = [];
		foreach ($all as $row) {
			$by_parent[(string)$row->parentId][] = $row;
		}

		$flat = [];
		$this->_flatten_category_tree($by_parent, '0', 0, $flat);

		// ✅ نگاشت id -> name برای نمایش «دسته والد»
		$id_to_name = [];
		foreach ($all as $row) {
			$id_to_name[(string)$row->id] = $row->name_cat;
		}

		$logged_user_id = $this->session->userdata('id');
		$permissions_check = [
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'categories'),
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'categories'),
		];

		$data = [];
		foreach ($flat as $item) {
			$node  = $item['node'];
			$depth = $item['depth'];

			$sub_array = [];

			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" cat_id="'.$node->id.'">';

			// ✅ نام با تورفتگی بر اساس عمق
			$prefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth) . ($depth > 0 ? '└─ ' : '');
			$sub_array[] = $prefix . htmlspecialchars($node->name_cat);

			// ✅ دسته والد
			$sub_array[] = ($node->parentId != '0' && isset($id_to_name[(string)$node->parentId]))
				? htmlspecialchars($id_to_name[(string)$node->parentId])
				: '<span class="text-muted">— سطح اول —</span>';

			// توضیحات
			$sub_array[] = htmlspecialchars($node->details ?? '');

			// ✅ تعداد محصولات از lookup map (بدون N+1)
			$sub_array[] = $product_count_map[$node->id] ?? 0;

			// وضعیت
			$sub_array[] = ($node->isActive == 0)
				? '<button type="button" id="active" cat_id="'.$node->id.'" class="btn btn-primary btn-xs"
					'.($permissions_check['edit'] ? '' : 'data-no-permission="true"').'>فعالسازی</button>'
				: '<button type="button" id="deactive" cat_id="'.$node->id.'" class="btn btn-secondry btn-xs"
					'.($permissions_check['edit'] ? '' : 'data-no-permission="true"').'>غیرفعالسازی</button>';

			// افزودن زیردسته
			$sub_array[] = '<button type="button" id="add_sub" cat_id="'.$node->id.'" class="btn btn-success btn-xs"
				'.($permissions_check['edit'] ? '' : 'data-no-permission="true"').'><i class="fa fa-plus"></i></button>';

			// ویرایش
			$sub_array[] = '<button type="button" id="edit_category" cat_id="'.$node->id.'" class="btn btn-warning btn-xs"
				'.($permissions_check['edit'] ? '' : 'data-no-permission="true"').'><i class="fa fa-edit fa-lg"></i></button>';

			// حذف
			$sub_array[] = '<button id="delete" cat_id="'.$node->id.'" class="btn btn-danger btn-xs"
				'.($permissions_check['delete'] ? '' : 'data-no-permission="true"').'><i class="fa fa-trash fa-lg"></i></button>';

			$data[] = $sub_array;
		}

		echo json_encode(['data' => $data]);
	}

	/**
	 * ✅ تبدیل لیست تخت (parentId) به آرایه‌ی مرتب درختی (عمق‌محور)
	 */
	private function _flatten_category_tree($by_parent, $parent_id, $depth, &$flat)
	{
		if (!isset($by_parent[$parent_id])) return;

		foreach ($by_parent[$parent_id] as $node) {
			$flat[] = ['node' => $node, 'depth' => $depth];
			$this->_flatten_category_tree($by_parent, (string)$node->id, $depth + 1, $flat);
		}
	}

	/**
	 * ✅ گرفتن لیست همه دسته‌ها برای پر کردن select والد در مودال
	 */
	public function get_categories_for_select()
	{
		$this->check_permission(['view', 'full'], 'categories');

		$exclude_id = $this->input->post('exclude_id', TRUE); // موقع ویرایش، خود دسته نباید تو لیست باشه

		$all = $this->base_model->get_data(
			'categories', '*', null, null, null, null, null, null,
			['parentId' => 'ASC', 'name_cat' => 'ASC']
		);

		$by_parent = [];
		foreach ($all as $row) {
			$by_parent[(string)$row->parentId][] = $row;
		}

		// ✅ اگه در حال ویرایش هستیم، خود دسته + همه‌ی زیردسته‌هاش رو باید حذف کنیم
		// (تا نتونه به فرزند خودش وصل بشه و چرخه ایجاد نشه)
		$excluded_ids = [];
		if ($exclude_id) {
			$this->_collect_descendant_ids($by_parent, (string)$exclude_id, $excluded_ids);
			$excluded_ids[] = (string)$exclude_id;
		}

		$flat = [];
		$this->_flatten_category_tree($by_parent, '0', 0, $flat);

		$options = [];
		foreach ($flat as $item) {
			if (in_array((string)$item['node']->id, $excluded_ids)) continue;

			$options[] = [
				'id'    => $item['node']->id,
				'label' => str_repeat('— ', $item['depth']) . $item['node']->name_cat
			];
		}

		echo json_encode(['status' => 1, 'options' => $options]);
	}

	/**
	 * ✅ جمع‌آوری بازگشتی id همه‌ی زیرمجموعه‌های یک دسته (برای جلوگیری از چرخه)
	 */
	private function _collect_descendant_ids($by_parent, $parent_id, &$result)
	{
		if (!isset($by_parent[$parent_id])) return;

		foreach ($by_parent[$parent_id] as $node) {
			$result[] = (string)$node->id;
			$this->_collect_descendant_ids($by_parent, (string)$node->id, $result);
		}
	}

	/**
	 * ✅ گرفتن اطلاعات یک دسته (برای مودال ویرایش)
	 */
	public function get_category()
	{
		$this->check_permission(['view', 'full'], 'categories');

		$id = $this->input->post('id', TRUE);
		if (!$id) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$cat = $this->base_model->get_data('categories', '*', ['id' => $id]);
		if (empty($cat)) {
			echo json_encode(['status' => 0, 'message' => 'یافت نشد']);
			return;
		}

		echo json_encode(['status' => 1, 'data' => $cat[0]]);
	}

	/**
	 * ✅ افزودن دسته جدید (سطح اول یا زیردسته)
	 */
	public function insert_category()
	{
		$this->check_permission(['insert', 'full'], 'categories');

		$name = $this->input->post('name_cat', TRUE);
		$parent_id = $this->input->post('parentId', TRUE) ?: '0';
		$details = $this->input->post('details', TRUE);

		if (empty($name)) {
			echo json_encode(['status' => 0, 'message' => 'نام دسته الزامی است']);
			return;
		}

		$now = date('Y-m-d H:i:s');
		$data = [
			'parentId'  => $parent_id,
			'name_cat'  => $name,
			'details'   => $details,
			'isActive'  => 1,
			'created'   => $now,
			'modified'  => $now
		];

		$new_id = $this->base_model->insert_data('categories', $data);

		if (!$new_id) {
			echo json_encode(['status' => 0, 'message' => 'خطا در افزودن دسته']);
			return;
		}

		$this->base_model->add_log(
			'categories', $new_id, 'insert', null, $data,
			'افزودن دسته جدید: ' . $name, uniqid('grp_', true), 'افزودن دسته'
		);

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ ویرایش دسته (با جلوگیری از چرخه در انتخاب والد)
	 */
	public function update_category()
	{
		$this->check_permission(['edit', 'full'], 'categories');

		$id = $this->input->post('id', TRUE);
		$name = $this->input->post('name_cat', TRUE);
		$parent_id = $this->input->post('parentId', TRUE) ?: '0';
		$details = $this->input->post('details', TRUE);

		if (!$id || empty($name)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		// ✅ جلوگیری از انتخاب خود دسته یا یکی از زیردسته‌هاش به‌عنوان والد (جلوگیری از چرخه)
		if ((string)$parent_id === (string)$id) {
			echo json_encode(['status' => 0, 'message' => 'دسته نمی‌تواند والد خودش باشد']);
			return;
		}

		$all = $this->base_model->get_data('categories', '*');
		$by_parent = [];
		foreach ($all as $row) {
			$by_parent[(string)$row->parentId][] = $row;
		}
		$descendants = [];
		$this->_collect_descendant_ids($by_parent, (string)$id, $descendants);

		if (in_array((string)$parent_id, $descendants)) {
			echo json_encode(['status' => 0, 'message' => 'نمی‌توان یکی از زیردسته‌ها را به‌عنوان والد انتخاب کرد']);
			return;
		}

		$old_row = $this->base_model->get_data('categories', '*', ['id' => $id]);
		if (empty($old_row)) {
			echo json_encode(['status' => 0, 'message' => 'دسته یافت نشد']);
			return;
		}
		$old = $old_row[0];

		$now = date('Y-m-d H:i:s');
		$new_data = [
			'name_cat' => $name,
			'parentId' => $parent_id,
			'details'  => $details,
			'modified' => $now
		];

		$this->base_model->update_data('categories', $new_data, ['id' => $id]);

		$diff_old = $diff_new = [];
		foreach ($new_data as $k => $v) {
			if ((string)$old->$k !== (string)$v) {
				$diff_old[$k] = $old->$k;
				$diff_new[$k] = $v;
			}
		}

		if (!empty($diff_old)) {
			$this->base_model->add_log(
				'categories', $id, 'update', $diff_old, $diff_new,
				'ویرایش دسته: ' . $name, uniqid('grp_', true), 'ویرایش دسته'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ فعال/غیرفعال کردن دسته(ها)
	 */
	public function toggle_category_status()
	{
		$this->check_permission(['edit', 'full'], 'categories');

		$post_data = $this->input->post(NULL, TRUE);

		if (empty($post_data) || !isset($post_data['cat_ids']) || !is_array($post_data['cat_ids'])) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$cat_ids = $post_data['cat_ids'];
		$status = isset($post_data['status']) ? intval($post_data['status']) : 1;

		$cats_before = $this->base_model->get_data('categories', '*', null, null, ['id' => $cat_ids]);
		if (empty($cats_before)) {
			echo json_encode(['status' => 0, 'message' => 'دسته مورد نظر یافت نشد']);
			return;
		}

		// ✅ فعال/غیرفعال کردن زنجیره‌ای: تمام زیردسته‌های هر دسته انتخاب‌شده رو هم پیدا می‌کنیم
		$all_categories = $this->base_model->get_data('categories', '*');
		$by_parent = [];
		foreach ($all_categories as $row) {
			$by_parent[(string)$row->parentId][] = $row;
		}

		$all_affected_ids = $cat_ids;
		foreach ($cat_ids as $id) {
			$descendants = [];
			$this->_collect_descendant_ids($by_parent, (string)$id, $descendants);
			$all_affected_ids = array_merge($all_affected_ids, $descendants);
		}
		$all_affected_ids = array_unique($all_affected_ids);

		// ✅ گرفتن اطلاعات کامل همه‌ی دسته‌های تحت‌تأثیر (برای لاگ درست)
		$all_affected_cats = $this->base_model->get_data('categories', '*', null, null, ['id' => $all_affected_ids]);

		$group_id = uniqid('grp_', true);

		$this->db->trans_start();
		foreach ($all_affected_cats as $cat) {
			$this->base_model->update_data('categories', ['isActive' => $status], ['id' => $cat->id]);
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در تغییر وضعیت']);
			return;
		}

		foreach ($all_affected_cats as $cat) {
			$this->base_model->add_log(
				'categories', $cat->id, 'update_status',
				['isActive' => $cat->isActive], ['isActive' => $status],
				($status ? 'فعالسازی ' : 'غیرفعالسازی ') . 'دسته: ' . $cat->name_cat,
				$group_id, 'تغییر وضعیت دسته (زنجیره‌ای)'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ حذف نرم دسته(ها) - اگر زیردسته‌ی فعال داشته باشه، اجازه حذف نمی‌ده
	 */
	public function soft_delete_category()
	{
		$this->check_permission(['delete', 'full'], 'categories');

		$post_data = $this->input->post(NULL, TRUE);

		if (empty($post_data) || !isset($post_data['cat_ids']) || !is_array($post_data['cat_ids'])) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$cat_ids = $post_data['cat_ids'];

		$cats_before = $this->base_model->get_data('categories', '*', null, null, ['id' => $cat_ids]);
		if (empty($cats_before)) {
			echo json_encode(['status' => 0, 'message' => 'دسته مورد نظر یافت نشد']);
			return;
		}

		// ✅ بررسی اینکه هیچ‌کدوم زیردسته‌ی فعال (حذف‌نشده) نداشته باشن
		foreach ($cats_before as $cat) {
			$children = $this->base_model->get_data('categories', 'id', ['parentId' => $cat->id]);
			if (!empty($children)) {
				echo json_encode([
					'status' => 0,
					'message' => 'دسته «' . $cat->name_cat . '» دارای زیردسته است. ابتدا زیردسته‌ها را حذف یا منتقل کنید.'
				]);
				return;
			}
		}

		// ✅ بررسی اینکه هیچ‌کدوم محصول متصل نداشته باشن
		foreach ($cats_before as $cat) {
			$products = $this->base_model->get_data('products', 'id', ['id_cat' => $cat->id]);
			if (count($products) > 0) {
				echo json_encode([
					'status' => 0,
					'message' => 'دسته «' . $cat->name_cat . '» دارای ' . count($products) . ' محصول است. ابتدا محصولات را به دسته دیگری منتقل کنید.'
				]);
				return;
			}

		}

		$group_id = uniqid('grp_', true);

		$this->db->trans_start();
		$this->base_model->soft_delete('categories', $cat_ids, true, 'id');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در حذف']);
			return;
		}

		foreach ($cats_before as $cat) {
			$this->base_model->add_log(
				'categories', $cat->id, 'soft_delete', (array)$cat, null,
				'حذف دسته: ' . $cat->name_cat, $group_id, 'حذف دسته'
			);
		}

		echo json_encode(['status' => 1]);
	}



	/**
	 * ✅ نمایش صفحه ورود محصول
	 */
	public function insert_product()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			redirect('admin/login_page');
			return;
		}

		if (!$this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'products')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		// ✅ کد یکتا برای این session ورود محصول
		$data['code'] = uniqid('prd_', true);

		$data['title']    = 'ورود محصول';

		$all_cats = $this->base_model->get_data(
			'categories', '*', ['isActive' => 1], null, null, null, null, null,
			['parentId' => 'ASC', 'name_cat' => 'ASC']
		);
		$by_parent = [];
		foreach ($all_cats as $row) {
			$by_parent[(string)$row->parentId][] = $row;
		}
		$flat = [];
		$this->_flatten_category_tree($by_parent, '0', 0, $flat);
		$data['categories'] = $flat;

		$data['model']  = $this->base_model->get_data('model', '*');
		$data['size']   = $this->base_model->get_data('size', '*');
		$data['jens']   = $this->base_model->get_data('jens', '*');
		$data['brand']  = $this->base_model->get_data('brand', '*');
		$data['color']  = $this->base_model->get_data('color', '*');

		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert_product', $data);
	}

	/**
	 * ✅ آپلود موقت عکس‌ها به پوشه temp/{code}/
	 */
	public function upload_img_temp()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			echo json_encode(['status' => 0, 'message' => 'session_expired']);
			return;
		}

		$code = $this->input->post('code', TRUE);
		if (empty($code)) {
			echo json_encode(['status' => 0, 'message' => 'کد محصول نامعتبر است']);
			return;
		}

		// ✅ پوشه temp برای این کد
		$temp_path = FCPATH . 'uploads/temp/' . $code . '/';
		if (!is_dir($temp_path)) {
			mkdir($temp_path, 0755, true);
		}

		$files = $_FILES['files'];
		$uploaded = [];
		$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

		for ($i = 0; $i < count($files['name']); $i++) {
			$ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));

			if (!in_array($ext, $allowed_ext)) {
				continue;
			}

			// ✅ حداکثر ۵ عکس در temp
			$existing = glob($temp_path . '*');
			if (count($existing) >= 5) {
				echo json_encode(['status' => 0, 'message' => 'حداکثر ۵ عکس مجاز است']);
				return;
			}

			$new_name = uniqid() . '.' . $ext;
			$dest = $temp_path . $new_name;

			if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
				$uploaded[] = [
					'filename' => $new_name,
					'url'      => base_url('uploads/temp/' . $code . '/' . $new_name)
				];
			}
		}

		echo json_encode(['status' => 1, 'files' => $uploaded]);
	}

	/**
	 * ✅ حذف عکس موقت
	 */
	public function delete_img_temp()
	{
		$code     = $this->input->post('code', TRUE);
		$filename = $this->input->post('filename', TRUE);

		if (empty($code) || empty($filename)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		// ✅ جلوگیری از path traversal
		$filename = basename($filename);
		$file_path = FCPATH . 'uploads/temp/' . $code . '/' . $filename;

		if (file_exists($file_path)) {
			unlink($file_path);
			echo json_encode(['status' => 1]);
		} else {
			echo json_encode(['status' => 0, 'message' => 'فایل یافت نشد']);
		}
	}

	/**
	 * ✅ گرفتن نام attribute از ID (برای نمایش توی ردیف جدول - بدون N+1)
	 */
	public function get_attr_labels()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			echo json_encode(['status' => 0, 'message' => 'session_expired']);
			return;
		}

		// ✅ همه lookup داده‌ها رو یکجا می‌گیریم
		$model_map  = [];
		$jens_map   = [];
		$size_map   = [];
		$brand_map  = [];
		$color_map  = [];

		foreach ($this->base_model->get_data('model', 'id, name') as $r) {
			$model_map[$r->id] = $r->name;
		}
		foreach ($this->base_model->get_data('jens', 'id, name') as $r) {
			$jens_map[$r->id] = $r->name;
		}
		foreach ($this->base_model->get_data('size', 'id, name') as $r) {
			$size_map[$r->id] = $r->name;
		}
		foreach ($this->base_model->get_data('brand', 'id, name') as $r) {
			$brand_map[$r->id] = $r->name;
		}
		foreach ($this->base_model->get_data('color', 'id, name, color_code') as $r) {
			$color_map[$r->id] = ['name' => $r->name, 'code' => $r->color_code];
		}

		$post = $this->input->post(NULL, TRUE);

		echo json_encode([
			'status'    => 1,
			'model'     => $model_map[$post['model']]  ?? '',
			'jens'      => $jens_map[$post['jens']]    ?? '',
			'size'      => $size_map[$post['size']]    ?? '',
			'brand'     => $brand_map[$post['brand']]  ?? '',
			'color_name'=> isset($color_map[$post['color']]) ? $color_map[$post['color']]['name'] : '',
			'color_code'=> isset($color_map[$post['color']]) ? $color_map[$post['color']]['code'] : '',
		]);
	}

	/**
	 * ✅ ثبت نهایی محصول + attribute ها + انتقال عکس‌ها (همه توی یه transaction)
	 */
	public function insert_prd()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			echo json_encode(['status' => 0, 'message' => 'session_expired']);
			return;
		}

		if (!$this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'products')) {
			echo json_encode(['status' => 0, 'message' => 'شما دسترسی ثبت محصول را ندارید']);
			return;
		}

		$code    = $this->input->post('code', TRUE);
		$id_cat  = $this->input->post('id_cat', TRUE);
		$name    = $this->input->post('name', TRUE);
		$text    = $this->input->post('text', TRUE);
		$attrs   = $this->input->post('attributes', TRUE); // JSON

		// ✅ validation پایه
		if (empty($name)) {
			echo json_encode(['status' => 0, 'message' => 'نام محصول الزامی است']);
			return;
		}

		if (empty($attrs)) {
			echo json_encode(['status' => 0, 'message' => 'حداقل یک ویژگی (قیمت) الزامی است']);
			return;
		}

		$attrs_data = json_decode($attrs, true);

		if (empty($attrs_data) || !is_array($attrs_data)) {
			echo json_encode(['status' => 0, 'message' => 'داده ویژگی‌ها نامعتبر است']);
			return;
		}

		// ✅ بررسی وجود حداقل یک ردیف default
		$has_default = false;
		foreach ($attrs_data as $attr) {
			if (!empty($attr['default']) && $attr['default'] == '1') {
				$has_default = true;
				break;
			}
		}

		if (!$has_default) {
			echo json_encode(['status' => 0, 'message' => 'یک ردیف باید به عنوان پیش‌فرض انتخاب شود']);
			return;
		}

		// ✅ بررسی وجود قیمت در همه ردیف‌ها
		foreach ($attrs_data as $attr) {
			if (empty($attr['price']) || $attr['price'] <= 0) {
				echo json_encode(['status' => 0, 'message' => 'قیمت برای همه ردیف‌ها الزامی است']);
				return;
			}
		}

		$now = date('Y-m-d H:i:s');
		$group_id = uniqid('grp_', true);

		$this->db->trans_start();

		try {
			// 1️⃣ ثبت محصول اصلی
			$product_data = [
				'code'     => $code,
				'id_cat'   => $id_cat ?: NULL,
				'name'     => $name,
				'text'     => $text,
				'isActive' => 1,
				'created'  => $now,
				'modified' => $now
			];
			$product_id = $this->base_model->insert_data('products', $product_data);

			if (!$product_id) {
				throw new Exception('خطا در ثبت محصول');
			}

			// 2️⃣ ثبت attribute ها (batch)
			$batch_attrs = [];
			foreach ($attrs_data as $attr) {
				$batch_attrs[] = [
					'code_p'      => $code,
					'model'       => $attr['model']       ?? NULL,
					'jens'        => $attr['jens']        ?? NULL,
					'size'        => $attr['size']        ?? NULL,
					'brand'       => $attr['brand']       ?? NULL,
					'color'       => $attr['color']       ?? NULL,
					'buy_price'   => $attr['buy_price']   ?? 0,
					'price'       => $attr['price'],
					'off_percent' => $attr['off_percent'] ?? NULL,
					'supply'      => $attr['supply']      ?? 0,
					'isActive'    => $attr['isActive']    ?? 1,
					'default'     => $attr['default']     ?? 0,
					'created'     => $now,
					'modified'    => $now
				];
			}
			$this->base_model->insert_data('product_attributes', $batch_attrs, true);

			// 3️⃣ انتقال عکس‌ها از temp به پوشه اصلی
			$temp_path   = FCPATH . 'uploads/temp/' . $code . '/';
			$final_path  = FCPATH . 'uploads/products/' . $product_id . '/';

			if (is_dir($temp_path)) {
				if (!is_dir($final_path)) {
					mkdir($final_path, 0755, true);
				}

				$files = glob($temp_path . '*');
				foreach ($files as $file) {
					$filename = basename($file);
					rename($file, $final_path . $filename);

					// ✅ ثبت عکس توی دیتابیس
					$this->base_model->insert_data('product_images', [
						'product_id' => $product_id,
						'code_p'     => $code,
						'filename'   => $filename,
						'created'    => $now
					]);
				}

				// حذف پوشه temp
				rmdir($temp_path);
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				throw new Exception('خطا در ذخیره‌سازی');
			}

			// ✅ لاگ
			$this->base_model->add_log(
				'products', $product_id, 'insert',
				null, $product_data,
				'ثبت محصول جدید: ' . $name,
				$group_id, 'ورود محصول'
			);

			echo json_encode(['status' => 1, 'product_id' => $product_id]);

		} catch (Exception $e) {
			$this->db->trans_rollback();
			log_message('error', 'Insert Product Error: ' . $e->getMessage());
			echo json_encode(['status' => 0, 'message' => 'خطا در ثبت: ' . $e->getMessage()]);
		}
	}

	/**
	 * ✅ cleanup: حذف پوشه‌های temp قدیمی‌تر از ۲۴ ساعت
	 * (می‌تونه از cron job صدا زده بشه)
	 */
	public function cleanup_temp()
	{
		$temp_base = FCPATH . 'uploads/temp/';
		if (!is_dir($temp_base)) return;

		$dirs = glob($temp_base . '*', GLOB_ONLYDIR);
		$now  = time();

		foreach ($dirs as $dir) {
			// اگه بیشتر از ۲۴ ساعت از آخرین تغییر گذشته
			if ($now - filemtime($dir) > 86400) {
				array_map('unlink', glob($dir . '/*'));
				rmdir($dir);
			}
		}

		echo json_encode(['status' => 1, 'message' => 'cleanup انجام شد']);
	}




	/**
	 * ✅ نمایش صفحه لیست محصولات
	 */
	public function products()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			redirect('admin/login_page');
			return;
		}

		if (!$this->base_model->has_permission($logged_user_id, ['view', 'full'], 'products')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		$data['action_permissions'] = [
			'add'    => $this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'products'),
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'products'),
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'products'),
		];

		$data['title'] = 'محصولات';
		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/products', $data);
	}

	/**
	 * ✅ لیست محصولات برای DataTables (server-side)
	 */
	public function products_list()
	{
		$this->check_permission(['view', 'full'], 'products');

		$post_data = $this->input->post(NULL, TRUE);

		$columns = [
			null,
			'products.name',
			'pa.price',
			'categories.name_cat',
			'products.created',
			'products.modified',
			null, null, null, null
		];

		$table  = 'products';
		$select = '
			products.id,
			products.code,
			products.name,
			products.isActive,
			products.created,
			products.modified,
			categories.name_cat,
			pa.price
		';

		$join = [
			'categories'        => 'categories.id = products.id_cat',
			// ✅ join به attribute پیش‌فرض برای گرفتن قیمت
			'product_attributes pa' => 'pa.code_p = products.code AND pa.`default` = 1'
		];

		$result = $this->base_model->datatable(
			$table,
			$columns,
			$post_data,
			$select,
			$join,
			null,
			['products.id' => 'DESC']
		);

		$logged_user_id = $this->session->userdata('id');
		$permissions_check = [
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'products'),
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'products'),
			'add'    => $this->base_model->has_permission($logged_user_id, ['insert','full'], 'products'),
		];

		$data = [];
		foreach ($result['data'] as $row) {
			$sub_array = [];

			// Checkbox
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" id_p="' . $row->id . '" code_p="' . htmlspecialchars($row->code) . '">';

			// نام محصول
			$sub_array[] = '<span style="display:inline-block;width:120px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="' . htmlspecialchars($row->name) . '">'
				. htmlspecialchars($row->name) . '</span>';

			// قیمت پیش‌فرض
			$sub_array[] = '<span>' . number_format($row->price) . '</span>';

			// دسته‌بندی
			$sub_array[] = '<span style="display:inline-block;width:100px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="' . htmlspecialchars($row->name_cat ?? '') . '">'
				. htmlspecialchars($row->name_cat ?? '—') . '</span>';

			// تاریخ ایجاد
			$sub_array[] = htmlspecialchars($row->created);

			// آخرین ویرایش
			$sub_array[] = htmlspecialchars($row->modified);

			// وضعیت
			$sub_array[] = ($row->isActive == 0)
				? '<button type="button" id="active" id_p="' . $row->id . '" class="btn btn-primary btn-xs"
					' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>فعالسازی</button>'
				: '<button type="button" id="deactive" id_p="' . $row->id . '" class="btn btn-secondary btn-xs"
					' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>غیرفعالسازی</button>';

			// کپی
			$sub_array[] = '<button type="button" id="copy" id_p="' . $row->id . '" code_p="' . htmlspecialchars($row->code) . '" class="btn btn-info btn-xs"
				' . ($permissions_check['add'] ? '' : 'data-no-permission="true"') . '>
				<i class="fa fa-copy"></i>
			</button>';

			// ویرایش
			$sub_array[] = '<a href="' . base_url('admin/edit_product/' . $row->code) . '">
				<button type="button" class="btn btn-warning btn-xs"
					' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>
					<i class="fa fa-edit fa-lg"></i>
				</button>
			</a>';

			// حذف
			$sub_array[] = '<button type="button" id="delete" id_p="' . $row->id . '" code_p="' . htmlspecialchars($row->code) . '" class="btn btn-danger btn-xs"
				' . ($permissions_check['delete'] ? '' : 'data-no-permission="true"') . '>
				<i class="fa fa-trash fa-lg"></i>
			</button>';

			$data[] = $sub_array;
		}

		echo json_encode([
			'draw'            => isset($post_data['draw']) ? intval($post_data['draw']) : 0,
			'recordsTotal'    => $result['recordsTotal'],
			'recordsFiltered' => $result['recordsFiltered'],
			'data'            => $data
		]);
	}

	/**
	 * ✅ فعال/غیرفعال کردن محصول(ها)
	 */
	public function toggle_product_status()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$post_data = $this->input->post(NULL, TRUE);
		$prd_ids   = isset($post_data['prd_ids']) ? (array)$post_data['prd_ids'] : [];
		$status    = isset($post_data['status']) ? intval($post_data['status']) : 1;

		if (empty($prd_ids)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$products_before = $this->base_model->get_data('products', '*', null, null, ['id' => $prd_ids]);
		if (empty($products_before)) {
			echo json_encode(['status' => 0, 'message' => 'محصول مورد نظر یافت نشد']);
			return;
		}

		$group_id = uniqid('grp_', true);

		$this->db->trans_start();

		$batch = [];
		foreach ($products_before as $p) {
			$batch[] = ['id' => $p->id, 'isActive' => $status];
		}
		$this->base_model->update_data('products', $batch, null, true, 'id');

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در تغییر وضعیت']);
			return;
		}

		foreach ($products_before as $p) {
			$this->base_model->add_log(
				'products', $p->id, 'update_status',
				['isActive' => $p->isActive],
				['isActive' => $status],
				($status ? 'فعالسازی' : 'غیرفعالسازی') . ' محصول: ' . $p->name,
				$group_id, 'تغییر وضعیت محصول'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ چک کردن وجود محصول در سبد خرید
	 */
	public function check_orders()
	{
		$this->check_permission(['delete', 'full'], 'products');

		$id_p = $this->input->post('id_p', TRUE);

		if (!$id_p) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		// ✅ چک می‌کنیم آیا این محصول توی سبد خرید هست
		$in_cart = $this->db
			->where('id_p', $id_p)
			->count_all_results('shopping_cart');

		echo json_encode([
			'status'   => 1,
			'in_cart'  => $in_cart,
		]);
	}

	/**
	 * ✅ حذف نرم محصول (+ attribute ها + پاکسازی سبد خرید)
	 */
	public function delete_prd()
	{
		$this->check_permission(['delete', 'full'], 'products');

		$id_p      = $this->input->post('id_p', TRUE);
		$code_p    = $this->input->post('code_p', TRUE);
		$confirmed = $this->input->post('confirmed', TRUE); // اگه ادمین اخطار رو تأیید کرده

		if (!$id_p || !$code_p) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$product = $this->base_model->get_data('products', '*', ['id' => $id_p]);
		if (empty($product)) {
			echo json_encode(['status' => 0, 'message' => 'محصول یافت نشد']);
			return;
		}
		$product = $product[0];

		// ✅ چک سبد خرید
		$in_cart = $this->db
			->where('id_p', $id_p)
			->count_all_results('shopping_cart');

		// اگه توی سبد هست و ادمین هنوز تأیید نکرده، اخطار بده
		if ($in_cart > 0 && !$confirmed) {
			echo json_encode([
				'status'   => 0,
				'in_cart'  => $in_cart,
				'needs_confirm' => true,
				'message'  => 'این محصول در سبد خرید ' . $in_cart . ' کاربر وجود دارد. با حذف، این محصول از سبد خریدشان برداشته می‌شود. ادامه می‌دهید؟'
			]);
			return;
		}

		$group_id = uniqid('grp_', true);

		$this->db->trans_start();

		// ✅ حذف نرم محصول
		$this->base_model->soft_delete('products', ['id' => $id_p]);

		// ✅ حذف نرم attribute ها
		$this->base_model->soft_delete('product_attributes', ['code_p' => $code_p]);

		// ✅ حذف فیزیکی از سبد خرید (چون محصول دیگه وجود نداره)
		if ($in_cart > 0) {
			$this->base_model->delete_data('shopping_cart', ['id_p' => $id_p]);

			// TODO: اطلاع‌رسانی به کاربران (پیامک/ایمیل/notification داخلی)
			// در آینده اینجا پیاده‌سازی می‌شه
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در حذف']);
			return;
		}

		$this->base_model->add_log(
			'products', $id_p, 'soft_delete',
			(array)$product, null,
			'حذف محصول: ' . $product->name . ($in_cart > 0 ? ' (از سبد ' . $in_cart . ' کاربر حذف شد)' : ''),
			$group_id, 'حذف محصول'
		);

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ حذف نرم چند محصول انتخاب‌شده
	 */
	public function delete_products()
	{
		$this->check_permission(['delete', 'full'], 'products');

		$ids   = $this->input->post('ids', TRUE);
		$codes = $this->input->post('codes', TRUE);

		if (empty($ids) || empty($codes)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$id_arr   = explode(',', $ids);
		$code_arr = explode(',', $codes);

		$products_before = $this->base_model->get_data('products', '*', null, null, ['id' => $id_arr]);
		if (empty($products_before)) {
			echo json_encode(['status' => 0, 'message' => 'محصولات یافت نشدند']);
			return;
		}

		$group_id = uniqid('grp_', true);

		$this->db->trans_start();

		// ✅ حذف نرم محصولات
		$this->base_model->soft_delete('products', $id_arr, true, 'id');

		// ✅ حذف نرم attribute ها
		$this->base_model->soft_delete('product_attributes', $code_arr, true, 'code_p');

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در حذف']);
			return;
		}

		foreach ($products_before as $p) {
			$this->base_model->add_log(
				'products', $p->id, 'soft_delete',
				(array)$p, null,
				'حذف محصول: ' . $p->name,
				$group_id, 'حذف گروهی محصول'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ کپی محصول (+ attribute ها + عکس‌ها)
	 */
	public function copy_product()
	{
		$this->check_permission(['insert', 'full'], 'products');

		$id_p   = $this->input->post('id', TRUE);
		$code_p = $this->input->post('code', TRUE);

		if (!$id_p || !$code_p) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$product = $this->base_model->get_data('products', '*', ['id' => $id_p]);
		if (empty($product)) {
			echo json_encode(['status' => 0, 'message' => 'محصول یافت نشد']);
			return;
		}
		$product = $product[0];

		$attrs  = $this->base_model->get_data('product_attributes', '*', ['code_p' => $code_p]);
		$images = $this->base_model->get_data('product_images', '*', ['product_id' => $id_p]);

		$new_code = uniqid('prd_', true);
		$now      = date('Y-m-d H:i:s');
		$group_id = uniqid('grp_', true);

		$this->db->trans_start();

		// 1️⃣ کپی محصول اصلی
		$new_product = [
			'code'     => $new_code,
			'id_cat'   => $product->id_cat,
			'name'     => 'کپی - ' . $product->name,
			'text'     => $product->text,
			'isActive' => 0, // کپی غیرفعال ثبت میشه
			'created'  => $now,
			'modified' => $now
		];
		$new_product_id = $this->base_model->insert_data('products', $new_product);

		if (!$new_product_id) {
			$this->db->trans_rollback();
			echo json_encode(['status' => 0, 'message' => 'خطا در کپی محصول']);
			return;
		}

		// 2️⃣ کپی attribute ها
		if (!empty($attrs)) {
			$batch_attrs = [];
			foreach ($attrs as $attr) {
				$batch_attrs[] = [
					'code_p'      => $new_code,
					'model'       => $attr->model,
					'jens'        => $attr->jens,
					'size'        => $attr->size,
					'brand'       => $attr->brand,
					'color'       => $attr->color,
					'buy_price'   => $attr->buy_price,
					'price'       => $attr->price,
					'off_percent' => $attr->off_percent,
					'supply'      => $attr->supply,
					'isActive'    => $attr->isActive,
					'default'     => $attr->default,
					'created'     => $now,
					'modified'    => $now
				];
			}
			$this->base_model->insert_data('product_attributes', $batch_attrs, true);
		}

		// 3️⃣ کپی عکس‌ها (فایل + دیتابیس)
		$src_path  = FCPATH . 'uploads/products/' . $id_p . '/';
		$dest_path = FCPATH . 'uploads/products/' . $new_product_id . '/';

		if (is_dir($src_path) && !empty($images)) {
			if (!is_dir($dest_path)) {
				mkdir($dest_path, 0755, true);
			}

			$batch_images = [];
			foreach ($images as $img) {
				$src_file  = $src_path . $img->filename;
				$dest_file = $dest_path . $img->filename;

				if (file_exists($src_file)) {
					copy($src_file, $dest_file);
					$batch_images[] = [
						'product_id' => $new_product_id,
						'code_p'     => $new_code,
						'filename'   => $img->filename,
						'created'    => $now
					];
				}
			}

			if (!empty($batch_images)) {
				$this->base_model->insert_data('product_images', $batch_images, true);
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			// پاکسازی پوشه کپی در صورت خطا
			if (is_dir($dest_path)) {
				array_map('unlink', glob($dest_path . '*'));
				rmdir($dest_path);
			}
			echo json_encode(['status' => 0, 'message' => 'خطا در کپی']);
			return;
		}

		$this->base_model->add_log(
			'products', $new_product_id, 'copy',
			null, $new_product,
			'کپی از محصول: ' . $product->name,
			$group_id, 'کپی محصول'
		);

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ ویرایش همگانی (نام + توضیحات) برای چند محصول انتخاب‌شده
	 */
	public function update_products_checkall()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$ids  = $this->input->post('id', TRUE);
		$name = $this->input->post('name', TRUE);
		$text = $this->input->post('text', TRUE);

		if (empty($ids)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$id_arr = explode(',', $ids);
		$now    = date('Y-m-d H:i:s');
		$group_id = uniqid('grp_', true);

		$update_data = ['modified' => $now];
		if (!empty($name)) $update_data['name'] = $name;
		if (!empty($text)) $update_data['text'] = $text;

		if (count($update_data) === 1) {
			// فقط modified — چیزی برای آپدیت نیست
			echo json_encode(['status' => 0, 'message' => 'حداقل یک فیلد برای ویرایش وارد کنید']);
			return;
		}

		$products_before = $this->base_model->get_data('products', '*', null, null, ['id' => $id_arr]);

		$this->db->trans_start();

		$batch = [];
		foreach ($id_arr as $id) {
			$row = array_merge(['id' => $id], $update_data);
			$batch[] = $row;
		}
		$this->base_model->update_data('products', $batch, null, true, 'id');

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در ویرایش']);
			return;
		}

		foreach ($products_before as $p) {
			$this->base_model->add_log(
				'products', $p->id, 'update',
				(array)$p, $update_data,
				'ویرایش همگانی محصول: ' . $p->name,
				$group_id, 'ویرایش همگانی محصول'
			);
		}

		echo json_encode(['status' => 1]);
	}




	/**
	 * ✅ نمایش صفحه ویرایش محصول
	 */
	public function edit_product($code = null)
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			redirect('admin/login_page');
			return;
		}

		if (!$this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'products')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		if (!$code) {
			redirect('admin/products');
			return;
		}

		$product = $this->base_model->get_data('products', '*', ['code' => $code]);
		if (empty($product)) {
			redirect('admin/products');
			return;
		}

		$data['title']      = 'ویرایش محصول';
		$data['product']    = $product[0];

		$all_cats = $this->base_model->get_data(
			'categories', '*', ['isActive' => 1], null, null, null, null, null,
			['parentId' => 'ASC', 'name_cat' => 'ASC']
		);
		$by_parent = [];
		foreach ($all_cats as $row) {
			$by_parent[(string)$row->parentId][] = $row;
		}
		$flat = [];
		$this->_flatten_category_tree($by_parent, '0', 0, $flat);
		$data['categories'] = $flat;

		$data['model']  = $this->base_model->get_data('model', '*');
		$data['size']   = $this->base_model->get_data('size', '*');
		$data['jens']   = $this->base_model->get_data('jens', '*');
		$data['brand']  = $this->base_model->get_data('brand', '*');
		$data['color']  = $this->base_model->get_data('color', '*');
		$data['images'] = $this->base_model->get_data('product_images', '*', ['product_id' => $product[0]->id]);

		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit_product', $data);
	}

	/**
	 * ✅ لیست attribute های یک محصول برای DataTable
	 */
	public function attr_list()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$post_data = $this->input->post(NULL, TRUE);
		$code_p    = isset($post_data['code_p']) ? $post_data['code_p'] : null;

		if (!$code_p) {
			echo json_encode(['data' => []]);
			return;
		}

		$columns = [
			null,
			'product_attributes.model',
			'product_attributes.jens',
			'product_attributes.size',
			'product_attributes.brand',
			'product_attributes.color',
			'product_attributes.buy_price',
			'product_attributes.price',
			'product_attributes.off_percent',
			'product_attributes.supply',
			null,
			null,
			null
		];

		$result = $this->base_model->datatable(
			'product_attributes',
			$columns,
			$post_data,
			'product_attributes.*',
			null,
			['code_p' => $code_p]
		);

		// ✅ گرفتن lookup map ها یکجا (بدون N+1)
		$model_map = $jens_map = $size_map = $brand_map = $color_map = [];
		foreach ($this->base_model->get_data('model', 'id, name') as $r)  { $model_map[$r->id] = $r->name; }
		foreach ($this->base_model->get_data('jens', 'id, name') as $r)   { $jens_map[$r->id]  = $r->name; }
		foreach ($this->base_model->get_data('size', 'id, name') as $r)   { $size_map[$r->id]  = $r->name; }
		foreach ($this->base_model->get_data('brand', 'id, name') as $r)  { $brand_map[$r->id] = $r->name; }
		foreach ($this->base_model->get_data('color', 'id, name, color_code') as $r) {
			$color_map[$r->id] = ['name' => $r->name, 'code' => $r->color_code];
		}

		$logged_user_id = $this->session->userdata('id');
		$can_edit = $this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'products');

		$data = [];
		foreach ($result['data'] as $row) {
			$sub_array = [];

			$sub_array[] = '<input type="checkbox" name="attr-check" attr_id="' . $row->id . '">';

			$sub_array[] = htmlspecialchars($model_map[$row->model] ?? '—');
			$sub_array[] = htmlspecialchars($jens_map[$row->jens]   ?? '—');
			$sub_array[] = htmlspecialchars($size_map[$row->size]   ?? '—');
			$sub_array[] = htmlspecialchars($brand_map[$row->brand] ?? '—');

			// رنگ با دایره رنگی
			if (isset($color_map[$row->color])) {
				$sub_array[] = '<span style="height:14px;width:14px;border:1px solid #000;border-radius:50%;display:inline-block;background:' . $color_map[$row->color]['code'] . ';margin-left:4px;"></span>'
					. htmlspecialchars($color_map[$row->color]['name']);
			} else {
				$sub_array[] = '—';
			}

			$sub_array[] = number_format($row->buy_price ?? 0);
			$sub_array[] = number_format($row->price);
			$sub_array[] = $row->off_percent ? $row->off_percent . '%' : '—';
			$sub_array[] = $row->supply;

			// isActive
			$sub_array[] = ($row->isActive == 0)
				? '<button type="button" class="btn btn-primary btn-xs toggle_attr_status" attr_id="' . $row->id . '" data-status="1"
					' . ($can_edit ? '' : 'data-no-permission="true"') . '>فعالسازی</button>'
				: '<button type="button" class="btn btn-secondary btn-xs toggle_attr_status" attr_id="' . $row->id . '" data-status="0"
					' . ($can_edit ? '' : 'data-no-permission="true"') . '>غیرفعالسازی</button>';

			// default
			$sub_array[] = '<input type="radio" name="default_attr" class="set_default_attr" attr_id="' . $row->id . '" '
				. ($row->default == 1 ? 'checked' : '') . '>';

			// ویرایش
			$sub_array[] = '<button type="button" class="btn btn-warning btn-xs edit_attr" attr_id="' . $row->id . '"
				' . ($can_edit ? '' : 'data-no-permission="true"') . '>
				<i class="fa fa-edit fa-lg"></i>
			</button>';

			$data[] = $sub_array;
		}

		echo json_encode([
			'draw'            => isset($post_data['draw']) ? intval($post_data['draw']) : 0,
			'recordsTotal'    => $result['recordsTotal'],
			'recordsFiltered' => $result['recordsFiltered'],
			'data'            => $data
		]);
	}

	/**
	 * ✅ گرفتن اطلاعات یک attribute برای مودال ویرایش
	 */
	public function get_attr()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$id = $this->input->post('id', TRUE);
		if (!$id) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$attr = $this->base_model->get_data('product_attributes', '*', ['id' => $id]);
		if (empty($attr)) {
			echo json_encode(['status' => 0, 'message' => 'یافت نشد']);
			return;
		}

		echo json_encode(['status' => 1, 'data' => $attr[0]]);
	}

	/**
	 * ✅ ویرایش یک attribute
	 */
	public function update_attr()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$id    = $this->input->post('id', TRUE);
		$price = $this->input->post('price', TRUE);

		if (!$id || empty($price) || $price <= 0) {
			echo json_encode(['status' => 0, 'message' => 'شناسه و قیمت الزامی است']);
			return;
		}

		$old = $this->base_model->get_data('product_attributes', '*', ['id' => $id]);
		if (empty($old)) {
			echo json_encode(['status' => 0, 'message' => 'ردیف یافت نشد']);
			return;
		}
		$old = $old[0];

		$now      = date('Y-m-d H:i:s');
		$new_data = [
			'model'       => $this->input->post('model', TRUE)       ?: NULL,
			'jens'        => $this->input->post('jens', TRUE)        ?: NULL,
			'size'        => $this->input->post('size', TRUE)        ?: NULL,
			'brand'       => $this->input->post('brand', TRUE)       ?: NULL,
			'color'       => $this->input->post('color', TRUE)       ?: NULL,
			'buy_price'   => $this->input->post('buy_price', TRUE)   ?: 0,
			'price'       => $price,
			'off_percent' => $this->input->post('off_percent', TRUE) ?: NULL,
			'supply'      => $this->input->post('supply', TRUE)      ?: 0,
			'modified'    => $now
		];

		$this->base_model->update_data('product_attributes', $new_data, ['id' => $id]);

		// لاگ فقط اگه تغییر واقعی بوده
		$diff_old = $diff_new = [];
		foreach ($new_data as $k => $v) {
			if ($k === 'modified') continue;
			if ((string)$old->$k !== (string)$v) {
				$diff_old[$k] = $old->$k;
				$diff_new[$k] = $v;
			}
		}
		if (!empty($diff_old)) {
			$this->base_model->add_log(
				'product_attributes', $id, 'update',
				$diff_old, $diff_new,
				'ویرایش attribute محصول',
				uniqid('grp_', true), 'ویرایش attribute'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ افزودن attribute جدید به محصول موجود
	 */
	public function add_attr()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$code_p = $this->input->post('code_p', TRUE);
		$price  = $this->input->post('price', TRUE);

		if (!$code_p || empty($price) || $price <= 0) {
			echo json_encode(['status' => 0, 'message' => 'کد محصول و قیمت الزامی است']);
			return;
		}

		$now  = date('Y-m-d H:i:s');
		$data = [
			'code_p'      => $code_p,
			'model'       => $this->input->post('model', TRUE)       ?: NULL,
			'jens'        => $this->input->post('jens', TRUE)        ?: NULL,
			'size'        => $this->input->post('size', TRUE)        ?: NULL,
			'brand'       => $this->input->post('brand', TRUE)       ?: NULL,
			'color'       => $this->input->post('color', TRUE)       ?: NULL,
			'buy_price'   => $this->input->post('buy_price', TRUE)   ?: 0,
			'price'       => $price,
			'off_percent' => $this->input->post('off_percent', TRUE) ?: NULL,
			'supply'      => $this->input->post('supply', TRUE)      ?: 0,
			'isActive'    => 1,
			'default'     => 0,
			'created'     => $now,
			'modified'    => $now
		];

		$new_id = $this->base_model->insert_data('product_attributes', $data);
		if (!$new_id) {
			echo json_encode(['status' => 0, 'message' => 'خطا در افزودن']);
			return;
		}

		$this->base_model->add_log(
			'product_attributes', $new_id, 'insert',
			null, $data,
			'افزودن attribute به محصول: ' . $code_p,
			uniqid('grp_', true), 'افزودن attribute'
		);

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ تغییر وضعیت فعال/غیرفعال یک attribute
	 */
	public function toggle_attr_status()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$id     = $this->input->post('id', TRUE);
		$status = intval($this->input->post('status', TRUE));

		if (!$id) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$attr = $this->base_model->get_data('product_attributes', '*', ['id' => $id]);
		if (empty($attr)) {
			echo json_encode(['status' => 0, 'message' => 'یافت نشد']);
			return;
		}

		// ✅ default نمی‌تونه غیرفعال بشه
		if ($attr[0]->default == 1 && $status == 0) {
			echo json_encode(['status' => 0, 'message' => 'ردیف پیش‌فرض نمی‌تواند غیرفعال شود']);
			return;
		}

		$this->base_model->update_data('product_attributes', [
			'isActive' => $status,
			'modified' => date('Y-m-d H:i:s')
		], ['id' => $id]);

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ تنظیم یک attribute به عنوان default
	 */
	public function set_default_attr()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$id     = $this->input->post('id', TRUE);
		$code_p = $this->input->post('code_p', TRUE);

		if (!$id || !$code_p) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$now = date('Y-m-d H:i:s');

		$this->db->trans_start();

		// ✅ همه رو از default خارج کن
		$this->base_model->update_data('product_attributes',
			['default' => 0, 'modified' => $now],
			['code_p' => $code_p]
		);

		// ✅ این یکی رو default کن + مطمئن شو فعاله
		$this->base_model->update_data('product_attributes',
			['default' => 1, 'isActive' => 1, 'modified' => $now],
			['id' => $id]
		);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در تنظیم پیش‌فرض']);
			return;
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ ویرایش اطلاعات اصلی محصول
	 */
	public function update_product()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$code   = $this->input->post('code', TRUE);
		$id_p   = $this->input->post('id_p', TRUE);
		$name   = $this->input->post('name', TRUE);
		$id_cat = $this->input->post('id_cat', TRUE) ?: NULL;
		$text   = $this->input->post('text', TRUE);

		if (!$code || !$id_p || empty($name)) {
			echo json_encode(['status' => 0, 'message' => 'نام محصول الزامی است']);
			return;
		}

		$old = $this->base_model->get_data('products', '*', ['id' => $id_p]);
		if (empty($old)) {
			echo json_encode(['status' => 0, 'message' => 'محصول یافت نشد']);
			return;
		}
		$old = $old[0];

		$now      = date('Y-m-d H:i:s');
		$new_data = [
			'name'     => $name,
			'id_cat'   => $id_cat,
			'text'     => $text,
			'modified' => $now
		];

		$this->base_model->update_data('products', $new_data, ['id' => $id_p]);

		$diff_old = $diff_new = [];
		foreach ($new_data as $k => $v) {
			if ($k === 'modified') continue;
			if ((string)$old->$k !== (string)$v) {
				$diff_old[$k] = $old->$k;
				$diff_new[$k] = $v;
			}
		}
		if (!empty($diff_old)) {
			$this->base_model->add_log(
				'products', $id_p, 'update',
				$diff_old, $diff_new,
				'ویرایش محصول: ' . $name,
				uniqid('grp_', true), 'ویرایش محصول'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ آپلود عکس جدید برای محصول موجود
	 */
	public function upload_img_product()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$product_id = $this->input->post('product_id', TRUE);
		$code_p     = $this->input->post('code_p', TRUE);

		if (!$product_id || !$code_p) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		// چک تعداد عکس‌های فعلی
		$existing_images = $this->base_model->get_data('product_images', 'id', ['product_id' => $product_id]);
		$files = $_FILES['files'];

		if (count($existing_images) + count($files['name']) > 5) {
			echo json_encode(['status' => 0, 'message' => 'حداکثر ۵ تصویر مجاز است']);
			return;
		}

		$upload_path  = FCPATH . 'uploads/products/' . $product_id . '/';
		$allowed_ext  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
		$now          = date('Y-m-d H:i:s');
		$uploaded     = [];

		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0755, true);
		}

		for ($i = 0; $i < count($files['name']); $i++) {
			$ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
			if (!in_array($ext, $allowed_ext)) continue;

			$new_name = uniqid() . '.' . $ext;
			$dest     = $upload_path . $new_name;

			if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
				$img_id = $this->base_model->insert_data('product_images', [
					'product_id' => $product_id,
					'code_p'     => $code_p,
					'filename'   => $new_name,
					'created'    => $now
				]);

				$uploaded[] = [
					'id'       => $img_id,
					'filename' => $new_name,
					'url'      => base_url('uploads/products/' . $product_id . '/' . $new_name)
				];
			}
		}

		echo json_encode(['status' => 1, 'files' => $uploaded]);
	}

	/**
	 * ✅ حذف عکس محصول
	 */
	public function delete_img_product()
	{
		$this->check_permission(['edit', 'full'], 'products');

		$img_id     = $this->input->post('img_id', TRUE);
		$product_id = $this->input->post('product_id', TRUE);
		$filename   = basename($this->input->post('filename', TRUE));

		if (!$img_id || !$product_id || !$filename) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		// حذف از دیتابیس
		$this->base_model->delete_data('product_images', ['id' => $img_id]);

		// حذف فایل از سرور
		$file_path = FCPATH . 'uploads/products/' . $product_id . '/' . $filename;
		if (file_exists($file_path)) {
			unlink($file_path);
		}

		echo json_encode(['status' => 1]);
	}




	/**
	 * ✅ نمایش صفحه لیست کدهای تخفیف
	 */
	public function discount_codes()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			redirect('admin/login_page');
			return;
		}

		if (!$this->base_model->has_permission($logged_user_id, ['view', 'full'], 'discount_codes')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		$data['action_permissions'] = [
			'add'    => $this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'discount_codes'),
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit', 'full'],   'discount_codes'),
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'discount_codes'),
		];

		$data['title'] = 'کدهای تخفیف';
		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/discount_codes', $data);
	}

	/**
	 * ✅ لیست کدهای تخفیف برای DataTable
	 */
	public function discount_list()
	{
		$this->check_permission(['view', 'full'], 'discount_codes');

		$post_data = $this->input->post(NULL, TRUE);

		$columns = [
			null,
			'discount_codes.code',
			'discount_codes.mablagh',
			'discount_codes.start_date',
			'discount_codes.end_date',
			'discount_codes.min_price',
			'discount_codes.uses',
			null, null, null, null
		];

		$result = $this->base_model->datatable(
			'discount_codes',
			$columns,
			$post_data,
			'discount_codes.*',
			null, null,
			['discount_codes.id' => 'DESC']
		);

		$logged_user_id    = $this->session->userdata('id');
		$permissions_check = [
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit',   'full'], 'discount_codes'),
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'discount_codes'),
		];

		// ✅ گرفتن تعداد کاربران و محصولات مرتبط یکجا (بدون N+1)
		$disc_ids          = array_column($result['data'], 'id');
		$user_count_map    = [];
		$product_count_map = [];

		if (!empty($disc_ids)) {
			$user_links = $this->base_model->get_data(
				'discount_users', 'discount_id',
				null, null, ['discount_id' => $disc_ids]
			);
			foreach ($user_links as $u) {
				$user_count_map[$u->discount_id] = ($user_count_map[$u->discount_id] ?? 0) + 1;
			}

			$product_links = $this->base_model->get_data(
				'discount_products', 'discount_id',
				null, null, ['discount_id' => $disc_ids]
			);
			foreach ($product_links as $p) {
				$product_count_map[$p->discount_id] = ($product_count_map[$p->discount_id] ?? 0) + 1;
			}
		}

		$now  = date('Y-m-d');
		$data = [];

		foreach ($result['data'] as $row) {
			$sub_array = [];

			$sub_array[] = '<input type="checkbox" name="row-check" class="checkall" disc_id="' . $row->id . '">';
			$sub_array[] = '<code>' . htmlspecialchars($row->code) . '</code>';
			$sub_array[] = number_format($row->mablagh) . ' تومان';

			// ✅ تاریخ شمسی برای نمایش
			$sub_array[] = $row->start_date ? $this->date_j($row->start_date) : '—';
			$sub_array[] = $row->end_date   ? $this->date_j($row->end_date)   : '—';

			$sub_array[] = $row->min_price ? number_format($row->min_price) . ' تومان' : '—';
			$sub_array[] = $row->uses ?? 0;

			// ✅ وضعیت انقضا
			if (empty($row->end_date)) {
				$sub_array[] = '<span class="label label-default">نامحدود</span>';
			} elseif ($row->end_date < $now) {
				$sub_array[] = '<span class="label label-danger">منقضی شده</span>';
			} elseif ($row->end_date == $now) {
				$sub_array[] = '<span class="label label-warning">امروز منقضی می‌شود</span>';
			} else {
				$days_left   = (strtotime($row->end_date) - strtotime($now)) / 86400;
				$sub_array[] = $days_left <= 7
					? '<span class="label label-warning">' . intval($days_left) . ' روز مانده</span>'
					: '<span class="label label-success">فعال</span>';
			}

			// ✅ محدودیت کاربران و محصولات
			$u_count     = $user_count_map[$row->id]    ?? 0;
			$p_count     = $product_count_map[$row->id] ?? 0;
			$sub_array[] = '<small>'
				. ($u_count > 0 ? $u_count . ' کاربر' : '<span class="text-muted">همه کاربران</span>')
				. ' | '
				. ($p_count > 0 ? $p_count . ' محصول' : '<span class="text-muted">همه محصولات</span>')
				. '</small>';

			// وضعیت (فعال/غیرفعال)
			$sub_array[] = ($row->isActive == 0)
				? '<button class="btn btn-primary btn-xs toggle_disc_status" disc_id="' . $row->id . '" data-status="1"
					' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>فعالسازی</button>'
				: '<button class="btn btn-secondary btn-xs toggle_disc_status" disc_id="' . $row->id . '" data-status="0"
					' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>غیرفعالسازی</button>';

			// ویرایش — لینک به صفحه جداگانه
			if ($permissions_check['edit']) {
				$sub_array[] = '<a href="' . base_url('admin/edit_discount/' . $row->id) . '">
					<button class="btn btn-warning btn-xs"><i class="fa fa-edit fa-lg"></i></button>
				</a>';
			} else {
				$sub_array[] = '<button class="btn btn-warning btn-xs" onclick="alert(\'شما دسترسی ویرایش ندارید\')">
					<i class="fa fa-edit fa-lg"></i>
				</button>';
			}

			// حذف
			$sub_array[] = '<button class="btn btn-danger btn-xs delete_disc" disc_id="' . $row->id . '"
				' . ($permissions_check['delete'] ? '' : 'data-no-permission="true"') . '>
				<i class="fa fa-trash fa-lg"></i>
			</button>';

			$data[] = $sub_array;
		}

		echo json_encode([
			'draw'            => isset($post_data['draw']) ? intval($post_data['draw']) : 0,
			'recordsTotal'    => $result['recordsTotal'],
			'recordsFiltered' => $result['recordsFiltered'],
			'data'            => $data
		]);
	}

	/**
	 * ✅ فعال/غیرفعال کردن
	 */
	public function toggle_discount_status()
	{
		$this->check_permission(['edit', 'full'], 'discount_codes');

		$id     = $this->input->post('id', TRUE);
		$status = intval($this->input->post('status', TRUE));

		if (!$id) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$this->base_model->update_data('discount_codes', [
			'isActive' => $status,
			'modified' => date('Y-m-d H:i:s')
		], ['id' => $id]);

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ حذف نرم (تکی و گروهی)
	 */
	public function delete_discount()
	{
		$this->check_permission(['delete', 'full'], 'discount_codes');

		$ids = $this->input->post('ids', TRUE);
		if (empty($ids)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}
		$ids = (array)$ids;

		$before = $this->base_model->get_data('discount_codes', '*', null, null, ['id' => $ids]);
		if (empty($before)) {
			echo json_encode(['status' => 0, 'message' => 'یافت نشد']);
			return;
		}

		$this->base_model->soft_delete('discount_codes', $ids, true, 'id');

		$group_id = uniqid('grp_', true);
		foreach ($before as $disc) {
			$this->base_model->add_log(
				'discount_codes', $disc->id, 'soft_delete',
				(array)$disc, null,
				'حذف کد تخفیف: ' . $disc->code,
				$group_id, 'حذف کد تخفیف'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ نمایش فرم افزودن کد تخفیف
	 */
	public function discount_form()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) { redirect('admin/login_page'); return; }

		if (!$this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'discount_codes')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		$data['title']    = 'افزودن کد تخفیف';
		$data['users']    = $this->base_model->get_data('users', 'id, phone_number', ['isActive' => 1]);
		$data['profiles'] = $this->base_model->get_data('profile', 'user_id, name, family');
		$data['products'] = $this->base_model->get_data(
			'products',
			'products.id, products.name, pa.price',
			['products.isActive' => 1],
			null, null, null, null,
			['product_attributes pa' => 'pa.code_p = products.code AND pa.`default` = 1']
		);

		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert_discount', $data);
	}

	/**
	 * ✅ ثبت کد تخفیف جدید با validation
	 */
	public function insert_discount()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) { redirect('admin/login_page'); return; }

		if (!$this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'discount_codes')) {
			redirect('admin/discount_form');
			return;
		}

		if (!$this->input->post()) {
			redirect('admin/discount_form');
			return;
		}

		$this->load->library('form_validation');
		$this->load->helper('form');

		$mablagh = $this->input->post('mablagh', TRUE);

		$this->form_validation->set_message('required',     'فیلد الزامی است');
		$this->form_validation->set_message('greater_than', '%s باید بیشتر از مبلغ تخفیف (%d تومان) باشد');

		$this->form_validation->set_rules('code',       'کد تخفیف',           'trim|required|max_length[100]|callback__check_code_unique');
		$this->form_validation->set_rules('mablagh',    'مبلغ تخفیف',          'trim|required|greater_than[0]');
		$this->form_validation->set_rules('start_date', 'تاریخ شروع',          'trim|required');
		$this->form_validation->set_rules('end_date',   'تاریخ پایان',         'trim|required');
		$this->form_validation->set_rules('uses',       'تعداد دفعات استفاده', 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('min_price',  'حداقل خرید',          'trim|required|greater_than[' . $mablagh . ']');

		if ($this->form_validation->run()) {

			$now      = date('Y-m-d H:i:s');
			$code     = strtoupper(trim($this->input->post('code', TRUE)));
			$disc_data = [
				'code'       => $code,
				'mablagh'    => $this->input->post('mablagh',   TRUE),
				'start_date' => $this->input->post('datetime1', TRUE),
				'end_date'   => $this->input->post('datetime2', TRUE),
				'min_price'  => $this->input->post('min_price', TRUE),
				'uses'       => $this->input->post('uses',      TRUE),
				'isActive'   => 1,
				'created'    => $now,
				'modified'   => $now
			];

			$this->db->trans_start();

			$new_id = $this->base_model->insert_data('discount_codes', $disc_data);

			if ($new_id) {
				$products = $this->input->post('products', TRUE);
				if (!empty($products) && is_array($products)) {
					$batch = [];
					foreach ($products as $pid) {
						$batch[] = ['discount_id' => $new_id, 'product_id' => (int)$pid];
					}
					$this->base_model->insert_data('discount_products', $batch, true);
				}

				$users = $this->input->post('users', TRUE);
				if (!empty($users) && is_array($users)) {
					$batch = [];
					foreach ($users as $uid) {
						$batch[] = ['discount_id' => $new_id, 'user_id' => (int)$uid];
					}
					$this->base_model->insert_data('discount_users', $batch, true);
				}
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'خطا در ثبت کد تخفیف');
				redirect('admin/discount_form');
				return;
			}

			$this->base_model->add_log(
				'discount_codes', $new_id, 'insert', null, $disc_data,
				'ثبت کد تخفیف: ' . $code, uniqid('grp_', true), 'ثبت کد تخفیف'
			);

			redirect('admin/discount_codes');

		} else {
			$this->discount_form();
		}
	}

	/**
	 * ✅ نمایش فرم ویرایش کد تخفیف
	 */
	public function edit_discount($id = null)
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) { redirect('admin/login_page'); return; }

		if (!$this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'discount_codes')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		if (!$id) { redirect('admin/discount_codes'); return; }

		$discount = $this->base_model->get_data('discount_codes', '*', ['id' => $id]);
		if (empty($discount)) { redirect('admin/discount_codes'); return; }

		$data['title']             = 'ویرایش کد تخفیف';
		$data['discount']          = $discount; // ✅ هم‌نام با ویو
		$data['discount_products'] = $this->base_model->get_data('discount_products', '*', ['discount_id' => $id]);
		$data['discount_users']    = $this->base_model->get_data('discount_users',    '*', ['discount_id' => $id]);
		$data['users']             = $this->base_model->get_data('users', 'id, phone_number', ['isActive' => 1]);
		$data['profiles']          = $this->base_model->get_data('profile', 'user_id, name, family');
		$data['products']          = $this->base_model->get_data(
			'products',
			'products.id, products.name, pa.price',
			['products.isActive' => 1],
			null, null, null, null,
			['product_attributes pa' => 'pa.code_p = products.code AND pa.`default` = 1']
		);

		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit_discount', $data);
	}

	/**
	 * ✅ ذخیره ویرایش کد تخفیف
	 */
	public function update_discount()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) { redirect('admin/login_page'); return; }

		if (!$this->base_model->has_permission($logged_user_id, ['edit', 'full'], 'discount_codes')) {
			redirect('admin/discount_codes');
			return;
		}

		if (!$this->input->post()) {
			redirect('admin/discount_codes');
			return;
		}

		$this->load->library('form_validation');
		$this->load->helper('form');

		$id      = $this->input->post('id',      TRUE);
		$mablagh = $this->input->post('mablagh', TRUE);

		if (!$id) { redirect('admin/discount_codes'); return; }

		$this->form_validation->set_message('required',     'فیلد الزامی است');
		$this->form_validation->set_message('greater_than', '%s باید بیشتر از مبلغ تخفیف (%d تومان) باشد');

		$this->form_validation->set_rules('code',       'کد تخفیف',           'trim|required|max_length[100]|callback__check_code_edit[' . $id . ']');
		$this->form_validation->set_rules('mablagh',    'مبلغ تخفیف',          'trim|required|greater_than[0]');
		$this->form_validation->set_rules('start_date', 'تاریخ شروع',          'trim|required');
		$this->form_validation->set_rules('end_date',   'تاریخ پایان',         'trim|required');
		$this->form_validation->set_rules('uses',       'تعداد دفعات استفاده', 'trim|required|greater_than[0]');
		$this->form_validation->set_rules('min_price',  'حداقل خرید',          'trim|required|greater_than[' . $mablagh . ']');

		if ($this->form_validation->run()) {

			$old = $this->base_model->get_data('discount_codes', '*', ['id' => $id]);
			if (empty($old)) { redirect('admin/discount_codes'); return; }
			$old = $old[0];

			$now      = date('Y-m-d H:i:s');
			$code     = strtoupper(trim($this->input->post('code', TRUE)));
			$new_data = [
				'code'       => $code,
				'mablagh'    => $this->input->post('mablagh',   TRUE),
				'start_date' => $this->input->post('datetime1', TRUE),
				'end_date'   => $this->input->post('datetime2', TRUE),
				'min_price'  => $this->input->post('min_price', TRUE),
				'uses'       => $this->input->post('uses',      TRUE),
				'modified'   => $now
			];

			$this->db->trans_start();

			$this->base_model->update_data('discount_codes', $new_data, ['id' => $id]);

			$this->base_model->delete_data('discount_products', ['discount_id' => $id]);
			$products = $this->input->post('products', TRUE);
			if (!empty($products) && is_array($products)) {
				$batch = [];
				foreach ($products as $pid) {
					$batch[] = ['discount_id' => $id, 'product_id' => (int)$pid];
				}
				$this->base_model->insert_data('discount_products', $batch, true);
			}

			$this->base_model->delete_data('discount_users', ['discount_id' => $id]);
			$users = $this->input->post('users', TRUE);
			if (!empty($users) && is_array($users)) {
				$batch = [];
				foreach ($users as $uid) {
					$batch[] = ['discount_id' => $id, 'user_id' => (int)$uid];
				}
				$this->base_model->insert_data('discount_users', $batch, true);
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'خطا در ویرایش کد تخفیف');
				redirect('admin/edit_discount/' . $id);
				return;
			}

			$diff_old = $diff_new = [];
			foreach ($new_data as $k => $v) {
				if ($k === 'modified') continue;
				if ((string)$old->$k !== (string)$v) {
					$diff_old[$k] = $old->$k;
					$diff_new[$k] = $v;
				}
			}
			if (!empty($diff_old)) {
				$this->base_model->add_log(
					'discount_codes', $id, 'update', $diff_old, $diff_new,
					'ویرایش کد تخفیف: ' . $code, uniqid('grp_', true), 'ویرایش کد تخفیف'
				);
			}

			redirect('admin/discount_codes');

		} else {
			$this->edit_discount($id);
		}
	}

	/**
	 * ✅ چک real-time تکراری بودن کد (insert)
	 * get_data خودکار deleted_at IS NULL رو فیلتر می‌کنه
	 */
	public function check_discount_code()
	{
		$code = strtoupper(trim($this->input->post('code', TRUE)));
		if (empty($code)) { echo ''; return; }

		$exists = $this->base_model->get_data('discount_codes', 'id', ['code' => $code]);

		echo !empty($exists)
			? '<label class="text-danger"><i class="fa fa-times"></i> در سیستم موجود است</label>'
			: '<label class="text-success"><i class="fa fa-check"></i> قابل قبول است</label>';
	}

	/**
	 * ✅ چک real-time تکراری بودن کد (edit — به جز خود رکورد)
	 */
	public function check_discount_code_edit()
	{
		$code = strtoupper(trim($this->input->post('code', TRUE)));
		$id   = $this->input->post('id', TRUE);
		if (empty($code)) { echo ''; return; }

		$exists = $this->base_model->get_data(
			'discount_codes', 'id',
			['code' => $code],
			null, null,
			['id' => [$id]]  // where_not_in
		);

		echo !empty($exists)
			? '<label class="text-danger"><i class="fa fa-times"></i> در سیستم موجود است</label>'
			: '<label class="text-success"><i class="fa fa-check"></i> قابل قبول است</label>';
	}

	/**
	 * ✅ callback validation — یکتایی کد برای insert
	 */
	public function _check_code_unique($code)
	{
		$exists = $this->base_model->get_data('discount_codes', 'id', ['code' => strtoupper(trim($code))]);
		if (!empty($exists)) {
			$this->form_validation->set_message('_check_code_unique', 'این کد تخفیف قبلاً ثبت شده است');
			return false;
		}
		return true;
	}

	/**
	 * ✅ callback validation — یکتایی کد برای edit (به جز خود رکورد)
	 */
	public function _check_code_edit($code, $id)
	{
		$exists = $this->base_model->get_data(
			'discount_codes', 'id',
			['code' => strtoupper(trim($code))],
			null, null,
			['id' => [$id]]  // where_not_in
		);
		if (!empty($exists)) {
			$this->form_validation->set_message('_check_code_edit', 'این کد تخفیف قبلاً ثبت شده است');
			return false;
		}
		return true;
	}

	/**
	 * ✅ جستجوی کد تخفیف برای dropdown
	 */
	public function search_code()
	{
		$query = $this->input->post('query', TRUE) ?: '';
		if (empty($query)) { echo 0; return; }

		// ✅ get_data با like — deleted_at هم خودکار فیلتر می‌شه
		$results = $this->base_model->get_data(
			'discount_codes', 'id, code',
			null,
			['code' => $query]  // like
		);

		if (empty($results)) { echo 0; return; }

		$output = '';
		foreach ($results as $row) {
			$output .= '<a class="tag" id="tag_' . $row->id . '" id_c="' . $row->id . '" style="display:block;">'
				. htmlspecialchars($row->code) . '</a>';
		}
		echo $output;
	}

















	public function attributes()
	{
		$data['title']='انبارداری';
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['products']=$this->base_model->get_data('products','*');

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/attributes');
	}
	public function attrs_list(){
		$fetch_data = $this->base_model->attrs_make_datatables();
		$data = array();
		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->id.'" code_p="'.$row->code.'"></input> ';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="'.$row->code_p.'">
			'.$row->code_p.'
			</span>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="'.$row->name.'">'.$row->name.'</span>';

			$sub_array[] = '<span>'.number_format($row->buy_price).'</span>';
			$sub_array[] = '<span>'.number_format($row->price).'</span>';
			$sub_array[] = '<span>'.number_format($row->supply).'</span>';
			$sub_array[] = '<span>'.$row->unit.'</span>';
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" id_attr="'.$row->id.'" class="btn btn-primary btn-xs">فعال سازی</button>';}
			else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" id_attr="'.$row->id.'" class="btn btn-secondary btn-xs">غیرفعال سازی</button>';}
			$sub_array[] = '<a href="'. base_url('admin/inventory_attrs/').$row->id.'">
			<button type="button" id="report" id_attr="'.$row->id.'" class="btn btn-info">
			<i class="fa fa-bars"></i>
			</button>
			</a>';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<button type="button" id="edit" code_p="'.$row->code.'" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->attrs_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->attrs_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}











//size
	public function size(){
		$data['title']='جدول سایز';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/size');
	}
	function size_list(){
		$fetch_data = $this->base_model->size_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '
			<a href="'. base_url('admin/info_size/').$row->id.'">
			<span id="name_' . $row->id . '">'.$row->name.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-size: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-size: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			 if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_size="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_size="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_size="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_size="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_size="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->size_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->size_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_size($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_size', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_size');
	}
	public function info_size_list(){
		$fetch_data = $this->base_model->size_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_size');
		foreach($fetch_data as $row)
		{if ($row->size==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->size_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->size_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_size()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['IsActive'] = 1;
			$this->base_model->insert('size', $data);
			echo 1;
		}

	}
	public function get_size($id)
	{
		$size=$this->base_model->get_data('size','name',array('id'=>$id));
		if (isset($size[0])){
			echo $size[0]->name;
		}
//		foreach ($size as $si){
//			echo $si->name;
//			break;
//		}

	}
	function search_size()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_size($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function size_validation(){
		echo $this->base_model->is_input_available('$_POST["name"]','name','size');
		$data['name'] = $_POST['name'];
		$this->form_validation->set_rules('name', 'size', 'required|trim|is_unique[size.name]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["name"],'name','size') || $_POST["name"]==''){
				echo 2;
			}

		}
	}
	function check_size()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','size'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}
	function check_size_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('size'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'size', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این سایز ویرایش می شود'. "\r\n";
			}

//			$i3=0;
//			foreach ($products as $p) {
//				if ($this->base_model->is_input_available($p->id, 'id_p', 'shopping_cart')) {
//					$i3++;
//				}
//			}
//			if($i3>0){
//				echo ' تعداد ' .$i2. ' محصول با این سایز از سبدهای خرید حذف می شود';
//			}

		}


	}
	public function edit_si(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$this->base_model->update('size',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_size()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','*',array('size'=>$id));
			if($this->base_model->is_input_available($id, 'id', 'size')){
//				$this->base_model->delete_row('size', 'id', $id);
				$this->base_model->delete_rows_by_ids($id,'size');
//				$results3 = $this->base_model->delete_rows_by_col('code_p',$codes,'product_attributes');
			}
			if($this->base_model->is_input_available($id, 'size', 'product_attributes')){
//				$this->base_model->delete_row('product_attributes', 'size', $id);
				$data['size'] = 0;
				$this->base_model->update('product_attributes',array('size'=>$id) ,$data);
				echo 2;
			}
//			foreach ($product_attr as $p_attr) {
//				if($this->base_model->is_input_available($p_attr->id, 'id_attr', 'shopping_cart')){
//					$data['size'] = 0;
//					$this->base_model->update('shopping_cart',array('size'=>$id) ,$data);
////					$this->base_model->delete_row('shopping_cart', 'id_attr', $p_attr->id);
//					echo 3;
//				}
//			}

			echo 1;
		}
	}
	public function size_copy(){
		$id=$_POST['id'];
		date_default_timezone_set("Asia/Tehran");
		$name=$this->base_model->get_data('size','name',array('id'=>$id));
		if(isset($name[0])){
			echo $name1=$name[0]->name;
		}
//		foreach ($name as $na){
//			echo $name1=$na->name;
//			break;
//		}

//		$characters = '01';
//		$charactersLength = strlen($characters);
//		$randomString = '';
//		for ($i = 0; $i < 2; $i++) {
//			$randomString .= $characters[random_int(0, $charactersLength - 1)];
//		}
//		$result2=$name1.'-'.$randomString;

		for ($j = 1; $j < 1000; $j++) {
			$result=$name1.'-copy('.$j.')';
			if($this->base_model->is_size_unique($result)){
				$data['name'] = $result;
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$IsActive=$this->base_model->get_data('size','IsActive',array('id'=>$id));
				if(isset($IsActive[0])){
					echo $data['IsActive']=$IsActive[0]->IsActive;
				}
//				foreach ($IsActive as $cr){
//					echo $data['IsActive']=$cr->IsActive;
//					break;
//				}

				$this->base_model->insert('size', $data);
				break;
			}
			else{
				echo 'error';
			}
		}

	}
//size - end


//model
	public function model(){
		$data['title']='جدول مدل';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/model');
	}
	function model_list(){
		$fetch_data = $this->base_model->model_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '
			<a href="'. base_url('admin/info_model/').$row->id.'">
			<span id="name_' . $row->id . '">'.$row->name.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-model: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-model: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_model="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_model="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_model="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_model="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_model="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->model_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->model_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_model($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_model', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_model');
	}
	public function info_model_list(){
		$fetch_data = $this->base_model->model_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_model');
		foreach($fetch_data as $row)
		{if ($row->model==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->model_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->model_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_model()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['IsActive'] = 1;
			$this->base_model->insert('model', $data);
			echo 1;
		}

	}
	public function get_model($id)
	{
		$model=$this->base_model->get_data('model','name',array('id'=>$id));
		if (isset($model[0])){
			echo $model[0]->name;
		}


	}
	function search_model()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_model($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function model_validation(){
		echo $this->base_model->is_input_available('$_POST["name"]','name','model');
		$data['name'] = $_POST['name'];
		$this->form_validation->set_rules('name', 'model', 'required|trim|is_unique[model.name]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["name"],'name','model') || $_POST["name"]==''){
				echo 2;
			}

		}
	}
	function check_model()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','model'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}
	function check_model_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('model'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'model', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این سایز ویرایش می شود'. "\r\n";
			}



		}


	}
	public function edit_mo(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$this->base_model->update('model',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_model()
	{
		if ($_POST) {
			$id = $_POST['id'];

			if($this->base_model->is_input_available($id, 'id', 'model')){

				$this->base_model->delete_rows_by_ids($id,'model');

			}
			if($this->base_model->is_input_available($id, 'model', 'product_attributes')){

				$data['model'] = 0;
				$this->base_model->update('product_attributes',array('model'=>$id) ,$data);
				echo 2;
			}


			echo 1;
		}
	}
	public function model_copy(){
		$id=$_POST['id'];
		date_default_timezone_set("Asia/Tehran");
		$name=$this->base_model->get_data('model','name',array('id'=>$id));
		if(isset($name[0])){
			echo $name1=$name[0]->name;
		}

		for ($j = 1; $j < 1000; $j++) {
			$result=$name1.'-copy('.$j.')';
			if($this->base_model->is_input_unique($result,'name','model')){
				$data['name'] = $result;
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$IsActive=$this->base_model->get_data('model','IsActive',array('id'=>$id));
				if(isset($IsActive[0])){
					echo $data['IsActive']=$IsActive[0]->IsActive;
				}


				$this->base_model->insert('model', $data);
				break;
			}
			else{
				echo 'error';
			}
		}

	}
//model - end


//jens
	public function jens(){
		$data['title']='جدول جنس';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/jens');
	}
	function jens_list(){
		$fetch_data = $this->base_model->jens_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '
			<a href="'. base_url('admin/info_jens/').$row->id.'">
			<span id="name_' . $row->id . '">'.$row->name.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-jens: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-jens: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_jens="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_jens="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_jens="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_jens="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_jens="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->jens_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->jens_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_jens($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_jens', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_jens');
	}
	public function info_jens_list(){
		$fetch_data = $this->base_model->jens_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_jens');
		foreach($fetch_data as $row)
		{if ($row->jens==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->jens_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->jens_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_jens()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['IsActive'] = 1;
			$this->base_model->insert('jens', $data);
			echo 1;
		}

	}
	public function get_jens($id)
	{
		$jens=$this->base_model->get_data('jens','name',array('id'=>$id));
		if (isset($jens[0])){
			echo $jens[0]->name;
		}


	}
	function search_jens()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_jens($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function jens_validation(){
		echo $this->base_model->is_input_available('$_POST["name"]','name','jens');
		$data['name'] = $_POST['name'];
		$this->form_validation->set_rules('name', 'jens', 'required|trim|is_unique[jens.name]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["name"],'name','jens') || $_POST["name"]==''){
				echo 2;
			}

		}
	}
	function check_jens()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','jens'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}
	function check_jens_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('jens'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'jens', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این سایز ویرایش می شود'. "\r\n";
			}


		}


	}
	public function edit_je(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$this->base_model->update('jens',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_jens()
	{
		if ($_POST) {
			$id = $_POST['id'];
			if($this->base_model->is_input_available($id, 'id', 'jens')){
				$this->base_model->delete_rows_by_ids($id,'jens');
			}
			if($this->base_model->is_input_available($id, 'jens', 'product_attributes')){
				$data['jens'] = 0;
				$this->base_model->update('product_attributes',array('jens'=>$id) ,$data);
				echo 2;
			}


			echo 1;
		}
	}
	public function jens_copy(){
		$id=$_POST['id'];
		date_default_timezone_set("Asia/Tehran");
		$name=$this->base_model->get_data('jens','name',array('id'=>$id));
		if(isset($name[0])){
			echo $name1=$name[0]->name;
		}

		for ($j = 1; $j < 1000; $j++) {
			$result=$name1.'-copy('.$j.')';
			if($this->base_model->is_input_unique($result,'name','jens')){
				$data['name'] = $result;
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$IsActive=$this->base_model->get_data('jens','IsActive',array('id'=>$id));
				if(isset($IsActive[0])){
					echo $data['IsActive']=$IsActive[0]->IsActive;
				}

				$this->base_model->insert('jens', $data);
				break;
			}
			else{
				echo 'error';
			}
		}

	}
//jens - end


//brand
	public function brand(){
		$data['title']='جدول برند';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/brand');
	}
	function brand_list(){
		$fetch_data = $this->base_model->brand_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '
			<a href="'. base_url('admin/info_brand/').$row->id.'">
			<span id="name_' . $row->id . '">'.$row->name.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-brand: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-brand: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_brand="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_brand="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_brand="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_brand="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_brand="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->brand_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->brand_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_brand($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_brand', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_brand');
	}
	public function info_brand_list(){
		$fetch_data = $this->base_model->brand_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_brand');
		foreach($fetch_data as $row)
		{if ($row->brand==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->brand_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->brand_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_brand()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['IsActive'] = 1;
			$this->base_model->insert('brand', $data);
			echo 1;
		}

	}
	public function get_brand($id)
	{
		$brand=$this->base_model->get_data('brand','name',array('id'=>$id));
		if (isset($brand[0])){
			echo $brand[0]->name;
		}


	}
	function search_brand()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_brand($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function brand_validation(){
		echo $this->base_model->is_input_available('$_POST["name"]','name','brand');
		$data['name'] = $_POST['name'];
		$this->form_validation->set_rules('name', 'brand', 'required|trim|is_unique[brand.name]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["name"],'name','brand') || $_POST["name"]==''){
				echo 2;
			}

		}
	}
	function check_brand()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','brand'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}
	function check_brand_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('brand'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'brand', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این سایز ویرایش می شود'. "\r\n";
			}


		}


	}
	public function edit_br(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$this->base_model->update('brand',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_brand()
	{
		if ($_POST) {
			$id = $_POST['id'];
			if($this->base_model->is_input_available($id, 'id', 'brand')){
				$this->base_model->delete_rows_by_ids($id,'brand');
			}
			if($this->base_model->is_input_available($id, 'brand', 'product_attributes')){
				$data['brand'] = 0;
				$this->base_model->update('product_attributes',array('brand'=>$id) ,$data);
				echo 2;
			}

			echo 1;
		}
	}
	public function brand_copy(){
		$id=$_POST['id'];
		date_default_timezone_set("Asia/Tehran");
		$name=$this->base_model->get_data('brand','name',array('id'=>$id));
		if(isset($name[0])){
			echo $name1=$name[0]->name;
		}


		for ($j = 1; $j < 1000; $j++) {
			$result=$name1.'-copy('.$j.')';
			if($this->base_model->is_input_unique($result,'name','brand')){
				$data['name'] = $result;
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$IsActive=$this->base_model->get_data('brand','IsActive',array('id'=>$id));
				if(isset($IsActive[0])){
					echo $data['IsActive']=$IsActive[0]->IsActive;
				}


				$this->base_model->insert('brand', $data);
				break;
			}
			else{
				echo 'error';
			}
		}

	}
//brand - end


//color
	public function color(){
		$data['title']='جدول رنگ';
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/color');
	}
	function color_list(){
		$fetch_data = $this->base_model->color_make_datatables();
		$data = array();

		foreach($fetch_data as $row)
		{

			$sub_array = array();
			$sub_array[] = "";
			$sub_array[] = '<span style="height: 16px;width: 16px;border:1px solid black;border-radius: 50%;display: inline-block;background:'.$row->color_code.' "></span>
			<span id="name_' . $row->id . '">'.$row->name.'</span>';
			$sub_array[] = '
			<a href="'. base_url('admin/info_color/').$row->id.'">
			<span id="code_' . $row->id . '">'.$row->color_code.'</span>
			</a>';
			if($row->IsActive==0) {$sub_array[] = '<span style="color: red;font-color: 12px;">غیرفعال</span>';}
			else if($row->IsActive==1){$sub_array[] = '<span style="color: green;font-color: 12px;">فعال</span>';}
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->IsActive==0) {$sub_array[] = '<button type="button" id="active" id_color="'.$row->id.'" class="btn btn-primary btn-xs">فعال</button>';}
			else if($row->IsActive==1){$sub_array[] = '<button type="button" id="deactive" id_color="'.$row->id.'" class="btn btn-primary btn-xs">غیرفعال</button>';}
//			$sub_array[] = '
//			<button type="button" id="copy" id_color="'.$row->id.'" class="btn btn-info btn-xs">کپی</button>
//			';
			$sub_array[] = '
			<button type="button" id="edit" id_color="'.$row->id.'" class="btn btn-warning btn-xs">ویرایش</button>
			';
			$sub_array[] = '<button type="button" id="delete" id_color="'.$row->id.'" class="btn btn-danger btn-xs">حذف</button>';

			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->color_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->color_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}
	public function info_color($id){
		$data['title']='محصولات مرتبط';

		$this->session->set_flashdata('id_color', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/info_color');
	}
	public function info_color_list(){
		$fetch_data = $this->base_model->color_attr_make_datatables();
		$data = array();
		$id = $this->session->flashdata('id_color');
		foreach($fetch_data as $row)
		{if ($row->color==$id){

			$sub_array = array();
			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/edit_product/').$row->code.'">
			<span>'.$row->name.'</span>
			</a>';
			$sub_array[] = $row->supply;
			$sub_array[] = $row->price;
			$sub_array[] = $row->off_percent;
			$sub_array[] = '';


			$data[] = $sub_array;
		}}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->color_attr_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->color_attr_get_filtered_data(),
			"data"                    =>     $data
		);
		echo json_encode($output);

	}
	public function insert_color()
	{
		if ($_POST) {
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['color_code'] = $_POST['code'];
			$data['IsActive'] = 1;
			$this->base_model->insert('color', $data);
			echo 1;
		}

	}
	public function get_color($id)
	{
		$color=$this->base_model->get_data('color','color_code',array('id'=>$id));
		if (isset($color[0])){
			echo $color[0]->color_code;
		}


	}

	function color_validation(){
		echo $this->base_model->is_input_available('$_POST["code"]','color_code','color');
		$data['color_code'] = $_POST['code'];
		$this->form_validation->set_rules('code', 'color', 'required|trim|is_unique[color.color_code]');

		if($this->form_validation->run())
		{
			echo 1;
		}else{
			if($this->base_model->is_input_available($_POST["code"],'color_code','color') || $_POST["code"]==''){
				echo 2;
			}

		}
	}
	function check_color()
	{

		if($this->base_model->is_input_available($_POST["code"],'color_code','color'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}


	}

	function color_validation2()
	{
		$id_color = $_POST['id'];
		$this->form_validation->set_message('_edit_unique_color', 'در سیستم موجود است');

		$this->form_validation->set_rules('code', 'color', 'trim|required|callback__edit_unique_color[' . $id_color . ']');

		if ($this->form_validation->run()) {
			echo 1;
		} else {
			if (!$this->form_validation->run() || $_POST['code']='') {
				echo 2;
			}
		}
	}
	function check_color2()
	{
		$x = $_POST['code'];
		$y = $_POST['id'];

		if($this->base_model->check_value('color',array('color_code'=>$x,'id!='=>$y)))
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}
		else
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}


	}
	function _edit_unique_color($x,$y)
	{
		$return_value = $this->base_model->check_value('color',array('color_code'=>$x,'id!='=>$y));
		if ($return_value)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function check_color_in_tables()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$product_attr=$this->base_model->get_data('product_attributes','id',array('color'=>$id));

			$i2=0;
			foreach ($product_attr as $p) {
				if ($this->base_model->is_input_available($id, 'color', 'product_attributes')) {
					$i2++;
				}
			}
			if($i2>0){
				echo ' تعداد ' .$i2. ' محصول با این رنگ ویرایش می شود'. "\r\n";
			}


		}


	}
	public function edit_co(){
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['color_code'] = $_POST['code'];
			$this->base_model->update('color',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_color()
	{
		if ($_POST) {
			$id = $_POST['id'];
			if($this->base_model->is_input_available($id, 'id', 'color')){
				$this->base_model->delete_rows_by_ids($id,'color');
			}
			if($this->base_model->is_input_available($id, 'color', 'product_attributes')){
				$data['color'] = 0;
				$this->base_model->update('product_attributes',array('color'=>$id) ,$data);
				echo 2;
			}

			echo 1;
		}
	}

//color - end






	/**
	 * ✅ نمایش صفحه لیست انبارها
	 */
	public function inventory()
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) {
			redirect('admin/login_page');
			return;
		}

		if (!$this->base_model->has_permission($logged_user_id, ['view', 'full'], 'inventory')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		$data['action_permissions'] = [
			'add'    => $this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'inventory'),
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit',   'full'], 'inventory'),
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'inventory'),
		];

		$data['title'] = 'مدیریت انبار';
		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/inventory', $data);
	}

	/**
	 * ✅ لیست انبارها برای DataTable
	 */
	public function inventory_list()
	{
		$this->check_permission(['view', 'full'], 'inventory');

		$post_data = $this->input->post(NULL, TRUE);

		$columns = [
			null,
			'inventory.name',
			'inventory.details',
			null, // تعداد کل موجودی (محاسباتی)
			'inventory.created',
			'inventory.modified',
			null, null, null, null, null
		];

		$result = $this->base_model->datatable(
			'inventory',
			$columns,
			$post_data,
			'inventory.*',
			null, null,
			['inventory.id' => 'DESC']
		);

		$logged_user_id    = $this->session->userdata('id');
		$permissions_check = [
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit',   'full'], 'inventory'),
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'inventory'),
		];

		// ✅ گرفتن مجموع موجودی هر انبار یکجا (بدون N+1)
		$inv_ids   = array_column($result['data'], 'id');
		$stock_map = [];
		if (!empty($inv_ids)) {
			$stocks = $this->base_model->get_data(
				'inventory_stock', 'inventory_id, qty',
				null, null, ['inventory_id' => $inv_ids]
			);
			foreach ($stocks as $s) {
				$stock_map[$s->inventory_id] = ($stock_map[$s->inventory_id] ?? 0) + $s->qty;
			}
		}

		$data = [];
		foreach ($result['data'] as $row) {
			$sub_array = [];

			$sub_array[] = '<input type="checkbox" name="row-check" inv_id="' . $row->id . '">';

			$sub_array[] = '<span id="name_' . $row->id . '" style="display:inline-block;width:100px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="' . htmlspecialchars($row->name) . '">'
				. htmlspecialchars($row->name) . '</span>';

			$sub_array[] = '<span id="details_' . $row->id . '" style="display:inline-block;width:100px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="' . htmlspecialchars($row->details ?? '') . '">'
				. htmlspecialchars($row->details ?? '—') . '</span>';

			// ✅ مجموع موجودی این انبار
			$total_qty   = $stock_map[$row->id] ?? 0;
			$sub_array[] = number_format($total_qty);

			$sub_array[] = htmlspecialchars($row->created);
			$sub_array[] = htmlspecialchars($row->modified);

			// وضعیت
			$sub_array[] = ($row->isActive == 0)
				? '<button class="btn btn-primary btn-xs toggle_inv_status" inv_id="' . $row->id . '" data-status="1"
					' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>فعالسازی</button>'
				: '<button class="btn btn-secondary btn-xs toggle_inv_status" inv_id="' . $row->id . '" data-status="0"
					' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>غیرفعالسازی</button>';

			// گزارش (ورود/خروج)
			$sub_array[] = '<a href="' . base_url('admin/inventory_transactions/' . $row->id) . '">
				<button class="btn btn-info btn-xs"><i class="fa fa-list"></i></button>
			</a>';

			// موجودی کالا
			$sub_array[] = '<a href="' . base_url('admin/inventory_stock/' . $row->id) . '">
				<button class="btn btn-primary btn-xs"><i class="fa fa-cube"></i></button>
			</a>';

			// ویرایش
			$sub_array[] = '<button class="btn btn-warning btn-xs edit_inv" inv_id="' . $row->id . '"
				' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>
				<i class="fa fa-edit fa-lg"></i>
			</button>';

			// حذف
			$sub_array[] = '<button class="btn btn-danger btn-xs delete_inv" inv_id="' . $row->id . '"
				' . ($permissions_check['delete'] ? '' : 'data-no-permission="true"') . '>
				<i class="fa fa-trash fa-lg"></i>
			</button>';

			$data[] = $sub_array;
		}

		echo json_encode([
			'draw'            => isset($post_data['draw']) ? intval($post_data['draw']) : 0,
			'recordsTotal'    => $result['recordsTotal'],
			'recordsFiltered' => $result['recordsFiltered'],
			'data'            => $data
		]);
	}

	/**
	 * ✅ افزودن انبار جدید
	 */
	public function insert_inv()
	{
		$this->check_permission(['insert', 'full'], 'inventory');

		if (!$this->input->post()) {
			redirect('admin/inventory');
			return;
		}

		$this->load->library('form_validation');
		$this->load->helper('form');

		$this->form_validation->set_message('required', 'فیلد الزامی است');
		$this->form_validation->set_rules('name',    'نام انبار',  'trim|required|max_length[255]|callback__check_inv_name_unique');
		$this->form_validation->set_rules('details', 'توضیحات',    'trim|max_length[255]');

		if ($this->form_validation->run()) {
			$now  = date('Y-m-d H:i:s');
			$data = [
				'name'     => $this->input->post('name',    TRUE),
				'details'  => $this->input->post('details', TRUE) ?: NULL,
				'isActive' => 1,
				'created'  => $now,
				'modified' => $now
			];

			$new_id = $this->base_model->insert_data('inventory', $data);

			if ($new_id) {
				$this->base_model->add_log(
					'inventory', $new_id, 'insert', null, $data,
					'افزودن انبار: ' . $data['name'],
					uniqid('grp_', true), 'افزودن انبار'
				);
				$this->session->set_flashdata('success', 'انبار با موفقیت افزوده شد');
			}

			redirect('admin/inventory');

		} else {
			$this->inventory();
		}
	}

	/**
	 * ✅ ویرایش انبار
	 */
	public function edit_inv()
	{
		$this->check_permission(['edit', 'full'], 'inventory');

		if (!$this->input->post()) {
			redirect('admin/inventory');
			return;
		}

		$this->load->library('form_validation');
		$this->load->helper('form');

		$id = $this->input->post('id_modal', TRUE);
		if (!$id) { redirect('admin/inventory'); return; }

		$this->form_validation->set_message('required', 'فیلد الزامی است');
		$this->form_validation->set_rules('name_modal',    'نام انبار', 'trim|required|max_length[255]|callback__check_inv_name_edit[' . $id . ']');
		$this->form_validation->set_rules('details_modal', 'توضیحات',   'trim|max_length[255]');

		if ($this->form_validation->run()) {
			$old = $this->base_model->get_data('inventory', '*', ['id' => $id]);
			if (empty($old)) { redirect('admin/inventory'); return; }
			$old = $old[0];

			$now      = date('Y-m-d H:i:s');
			$new_data = [
				'name'     => $this->input->post('name_modal',    TRUE),
				'details'  => $this->input->post('details_modal', TRUE) ?: NULL,
				'modified' => $now
			];

			$this->base_model->update_data('inventory', $new_data, ['id' => $id]);

			$diff_old = $diff_new = [];
			foreach ($new_data as $k => $v) {
				if ($k === 'modified') continue;
				if ((string)$old->$k !== (string)$v) {
					$diff_old[$k] = $old->$k;
					$diff_new[$k] = $v;
				}
			}
			if (!empty($diff_old)) {
				$this->base_model->add_log(
					'inventory', $id, 'update', $diff_old, $diff_new,
					'ویرایش انبار: ' . $new_data['name'],
					uniqid('grp_', true), 'ویرایش انبار'
				);
			}

			$this->session->set_flashdata('success', 'انبار با موفقیت ویرایش شد');
			redirect('admin/inventory');

		} else {
			$this->inventory();
		}
	}

	/**
	 * ✅ فعال/غیرفعال کردن انبار (تکی و گروهی)
	 */
	public function toggle_inventory_status()
	{
		$this->check_permission(['edit', 'full'], 'inventory');

		$inv_ids = $this->input->post('inv_ids', TRUE);
		$status  = intval($this->input->post('status', TRUE));

		if (empty($inv_ids)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$inv_ids = (array)$inv_ids;
		$before  = $this->base_model->get_data('inventory', '*', null, null, ['id' => $inv_ids]);
		if (empty($before)) {
			echo json_encode(['status' => 0, 'message' => 'انبار یافت نشد']);
			return;
		}

		$batch = [];
		foreach ($before as $inv) {
			$batch[] = ['id' => $inv->id, 'isActive' => $status];
		}
		$this->base_model->update_data('inventory', $batch, null, true, 'id');

		$group_id = uniqid('grp_', true);
		foreach ($before as $inv) {
			$this->base_model->add_log(
				'inventory', $inv->id, 'update_status',
				['isActive' => $inv->isActive], ['isActive' => $status],
				($status ? 'فعالسازی' : 'غیرفعالسازی') . ' انبار: ' . $inv->name,
				$group_id, 'تغییر وضعیت انبار'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ حذف نرم انبار
	 */
	public function delete_inv()
	{
		$this->check_permission(['delete', 'full'], 'inventory');

		$inv_ids = $this->input->post('inv_ids', TRUE);
		if (empty($inv_ids)) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}
		$inv_ids = (array)$inv_ids;

		// ✅ چک اینکه انبار موجودی داشته باشه
		$has_stock = $this->base_model->get_data(
			'inventory_stock', 'inventory_id',
			null, null, ['inventory_id' => $inv_ids]
		);

		if (!empty($has_stock)) {
			echo json_encode([
				'status'  => 0,
				'message' => 'انبار دارای موجودی است. ابتدا موجودی را به انبار دیگری منتقل کنید'
			]);
			return;
		}

		$before = $this->base_model->get_data('inventory', '*', null, null, ['id' => $inv_ids]);
		if (empty($before)) {
			echo json_encode(['status' => 0, 'message' => 'انبار یافت نشد']);
			return;
		}

		$this->base_model->soft_delete('inventory', $inv_ids, true, 'id');

		$group_id = uniqid('grp_', true);
		foreach ($before as $inv) {
			$this->base_model->add_log(
				'inventory', $inv->id, 'soft_delete',
				(array)$inv, null,
				'حذف انبار: ' . $inv->name,
				$group_id, 'حذف انبار'
			);
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ چک real-time یکتا بودن نام انبار (insert)
	 */
	public function check_inv_name()
	{
		$name = trim($this->input->post('name', TRUE));
		if (empty($name)) { echo ''; return; }

		$exists = $this->base_model->get_data('inventory', 'id', ['name' => $name]);
		echo !empty($exists)
			? '<label class="text-danger"><i class="fa fa-times"></i> این نام قبلاً ثبت شده است</label>'
			: '<label class="text-success"><i class="fa fa-check"></i> قابل قبول است</label>';
	}

	/**
	 * ✅ چک real-time یکتا بودن نام انبار (edit)
	 */
	public function check_inv_name_edit()
	{
		$name = trim($this->input->post('name', TRUE));
		$id   = $this->input->post('id',   TRUE);
		if (empty($name)) { echo ''; return; }

		$exists = $this->base_model->get_data(
			'inventory', 'id', ['name' => $name],
			null, null, ['id' => [$id]]
		);
		echo !empty($exists)
			? '<label class="text-danger"><i class="fa fa-times"></i> این نام قبلاً ثبت شده است</label>'
			: '<label class="text-success"><i class="fa fa-check"></i> قابل قبول است</label>';
	}

	/**
	 * ✅ callback validation — یکتایی نام برای insert
	 */
	public function _check_inv_name_unique($name)
	{
		$exists = $this->base_model->get_data('inventory', 'id', ['name' => trim($name)]);
		if (!empty($exists)) {
			$this->form_validation->set_message('_check_inv_name_unique', 'این نام انبار قبلاً ثبت شده است');
			return false;
		}
		return true;
	}

	/**
	 * ✅ callback validation — یکتایی نام برای edit
	 */
	public function _check_inv_name_edit($name, $id)
	{
		$exists = $this->base_model->get_data(
			'inventory', 'id', ['name' => trim($name)],
			null, null, ['id' => [$id]]
		);
		if (!empty($exists)) {
			$this->form_validation->set_message('_check_inv_name_edit', 'این نام انبار قبلاً ثبت شده است');
			return false;
		}
		return true;
	}




	/**
	 * ✅ نمایش صفحه موجودی یک انبار
	 */
	public function inventory_stock($inv_id = null)
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) { redirect('admin/login_page'); return; }

		if (!$this->base_model->has_permission($logged_user_id, ['view', 'full'], 'inventory')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		if (!$inv_id) { redirect('admin/inventory'); return; }

		$inventory = $this->base_model->get_data('inventory', '*', ['id' => $inv_id]);
		if (empty($inventory)) { redirect('admin/inventory'); return; }

		$data['action_permissions'] = [
			'add'    => $this->base_model->has_permission($logged_user_id, ['insert', 'full'], 'inventory'),
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit',   'full'], 'inventory'),
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'inventory'),
		];

		// ✅ لیست محصولات برای مودال افزودن
		$data['products']    = $this->base_model->get_data('products', 'id, name, code', ['isActive' => 1]);
		$data['attributes']  = $this->base_model->get_data('product_attributes', '*', ['isActive' => 1]);
		$data['model']       = $this->base_model->get_data('model',  'id, name');
		$data['jens']        = $this->base_model->get_data('jens',   'id, name');
		$data['size']        = $this->base_model->get_data('size',   'id, name');
		$data['brand']       = $this->base_model->get_data('brand',  'id, name');
		$data['color']       = $this->base_model->get_data('color',  'id, name, color_code');

		$data['inventory'] = $inventory[0];
		$data['inv_id']    = $inv_id;
		$data['title']     = 'موجودی انبار: ' . $inventory[0]->name;

		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/inventory_stock', $data);
	}

	/**
	 * ✅ لیست موجودی انبار برای DataTable
	 */
	public function inventory_stock_list($inv_id = null)
	{
		$this->check_permission(['view', 'full'], 'inventory');

		if (!$inv_id) { echo json_encode(['data' => []]); return; }

		$post_data = $this->input->post(NULL, TRUE);

		$columns = [
			null,
			'products.code',
			'products.name',
			'inventory_stock.qty',
			'inventory_stock.modified',
			null, null, null
		];

		$result = $this->base_model->datatable(
			'inventory_stock',
			$columns,
			$post_data,
			'inventory_stock.id,
			 inventory_stock.attr_id,
			 inventory_stock.qty,
			 inventory_stock.modified,
			 products.name,
			 products.code,
			 product_attributes.price,
			 product_attributes.model,
			 product_attributes.jens,
			 product_attributes.size,
			 product_attributes.brand,
			 product_attributes.color',
			[
				'product_attributes' => 'product_attributes.id = inventory_stock.attr_id',
				'products'           => 'products.code = product_attributes.code_p'
			],
			['inventory_stock.inventory_id' => $inv_id]
		);

		// ✅ lookup maps بدون N+1
		$model_map = $jens_map = $size_map = $brand_map = $color_map = [];
		foreach ($this->base_model->get_data('model', 'id, name') as $r)  { $model_map[$r->id] = $r->name; }
		foreach ($this->base_model->get_data('jens',  'id, name') as $r)  { $jens_map[$r->id]  = $r->name; }
		foreach ($this->base_model->get_data('size',  'id, name') as $r)  { $size_map[$r->id]  = $r->name; }
		foreach ($this->base_model->get_data('brand', 'id, name') as $r)  { $brand_map[$r->id] = $r->name; }
		foreach ($this->base_model->get_data('color', 'id, name, color_code') as $r) {
			$color_map[$r->id] = ['name' => $r->name, 'code' => $r->color_code];
		}

		$logged_user_id    = $this->session->userdata('id');
		$permissions_check = [
			'edit'   => $this->base_model->has_permission($logged_user_id, ['edit',   'full'], 'inventory'),
			'delete' => $this->base_model->has_permission($logged_user_id, ['delete', 'full'], 'inventory'),
		];

		$data = [];
		foreach ($result['data'] as $row) {
			$sub_array = [];

			$sub_array[] = '<input type="checkbox" name="row-check" attr_id="' . $row->attr_id . '">';

			// کد کالا
			$sub_array[] = htmlspecialchars($row->code ?? '—');

			// نام کالا + مشخصات
			$attrs = [];
			if (!empty($row->model) && isset($model_map[$row->model])) $attrs[] = 'مدل: ' . $model_map[$row->model];
			if (!empty($row->jens)  && isset($jens_map[$row->jens]))   $attrs[] = 'جنس: ' . $jens_map[$row->jens];
			if (!empty($row->size)  && isset($size_map[$row->size]))   $attrs[] = 'سایز: ' . $size_map[$row->size];
			if (!empty($row->brand) && isset($brand_map[$row->brand])) $attrs[] = 'برند: ' . $brand_map[$row->brand];
			if (!empty($row->color) && isset($color_map[$row->color])) {
				$cc = $color_map[$row->color];
				$attrs[] = 'رنگ: <span style="height:12px;width:12px;border-radius:50%;display:inline-block;background:' . $cc['code'] . ';border:1px solid #000;"></span> ' . $cc['name'];
			}
			$label = htmlspecialchars($row->name ?? '');
			if (!empty($attrs)) $label .= ' <small class="text-muted">(' . implode(' - ', $attrs) . ')</small>';
			$sub_array[] = $label;

			// موجودی
			$qty_color   = $row->qty <= 0 ? 'color:red' : ($row->qty <= 5 ? 'color:orange' : '');
			$sub_array[] = '<span style="' . $qty_color . '">' . number_format($row->qty) . '</span>';

			// آخرین تغییر
			$sub_array[] = htmlspecialchars($row->modified);

			// دکمه ورود
			$sub_array[] = '<button class="btn btn-success btn-xs stock_in" attr_id="' . $row->attr_id . '"
				' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>
				<i class="fa fa-plus"></i> ورود
			</button>';

			// دکمه خروج
			$sub_array[] = '<button class="btn btn-warning btn-xs stock_out" attr_id="' . $row->attr_id . '"
				' . ($permissions_check['edit'] ? '' : 'data-no-permission="true"') . '>
				<i class="fa fa-minus"></i> خروج
			</button>';

			// تاریخچه
			$sub_array[] = '<a href="' . base_url('admin/inventory_transactions/' . $inv_id . '/' . $row->attr_id) . '">
				<button class="btn btn-info btn-xs"><i class="fa fa-list"></i></button>
			</a>';

			$data[] = $sub_array;
		}

		echo json_encode([
			'draw'            => isset($post_data['draw']) ? intval($post_data['draw']) : 0,
			'recordsTotal'    => $result['recordsTotal'],
			'recordsFiltered' => $result['recordsFiltered'],
			'data'            => $data
		]);
	}

	/**
	 * ✅ افزودن کالا به انبار (transaction ورودی + آپدیت stock)
	 */
	public function add_stock()
	{
		$this->check_permission(['insert', 'full'], 'inventory');

		$inv_id    = $this->input->post('inv_id',    TRUE);
		$attr_id   = $this->input->post('attr_id',   TRUE);
		$qty       = intval($this->input->post('qty', TRUE));
		$reference = $this->input->post('reference', TRUE) ?: 'purchase';
		$desc      = $this->input->post('description', TRUE) ?: NULL;

		if (!$inv_id || !$attr_id || $qty <= 0) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$valid_refs = ['purchase', 'return', 'transfer', 'adjustment', 'initial'];
		if (!in_array($reference, $valid_refs)) {
			echo json_encode(['status' => 0, 'message' => 'نوع تراکنش نامعتبر است']);
			return;
		}

		$now = date('Y-m-d H:i:s');
		$logged_user_id = $this->session->userdata('id');

		$this->db->trans_start();

		// ✅ ثبت transaction
		$this->base_model->insert_data('inventory_transactions', [
			'inventory_id'   => $inv_id,
			'attr_id'        => $attr_id,
			'type'           => 'in',
			'reference'      => $reference,
			'qty'            => $qty,
			'description'    => $desc,
			'created'        => $now,
			'created_by'     => $logged_user_id
		]);

		// ✅ آپدیت یا insert stock
		$existing = $this->base_model->get_data('inventory_stock', '*', [
			'inventory_id' => $inv_id,
			'attr_id'      => $attr_id
		]);

		if (!empty($existing)) {
			$new_qty = $existing[0]->qty + $qty;
			$this->base_model->update_data('inventory_stock',
				['qty' => $new_qty, 'modified' => $now],
				['inventory_id' => $inv_id, 'attr_id' => $attr_id]
			);
		} else {
			$this->base_model->insert_data('inventory_stock', [
				'inventory_id' => $inv_id,
				'attr_id'      => $attr_id,
				'qty'          => $qty,
				'modified'     => $now
			]);
		}

		// ✅ sync با product_attributes.supply
		$total_stock = $this->base_model->get_data(
			'inventory_stock', 'qty',
			null, null, ['attr_id' => [$attr_id]]
		);
		$total_qty = array_sum(array_column($total_stock, 'qty'));
		$this->base_model->update_data('product_attributes',
			['supply' => $total_qty, 'modified' => $now],
			['id' => $attr_id]
		);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در ثبت ورودی']);
			return;
		}

		echo json_encode(['status' => 1]);
	}

	/**
	 * ✅ خروج کالا از انبار (transaction خروجی + آپدیت stock)
	 */
	public function remove_stock()
	{
		$this->check_permission(['edit', 'full'], 'inventory');

		$inv_id    = $this->input->post('inv_id',    TRUE);
		$attr_id   = $this->input->post('attr_id',   TRUE);
		$qty       = intval($this->input->post('qty', TRUE));
		$reference = $this->input->post('reference', TRUE) ?: 'sale';
		$desc      = $this->input->post('description', TRUE) ?: NULL;

		if (!$inv_id || !$attr_id || $qty <= 0) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$valid_refs = ['sale', 'transfer', 'adjustment', 'return'];
		if (!in_array($reference, $valid_refs)) {
			echo json_encode(['status' => 0, 'message' => 'نوع تراکنش نامعتبر است']);
			return;
		}

		// ✅ چک موجودی کافی
		$stock = $this->base_model->get_data('inventory_stock', '*', [
			'inventory_id' => $inv_id,
			'attr_id'      => $attr_id
		]);

		if (empty($stock) || $stock[0]->qty < $qty) {
			echo json_encode(['status' => 0, 'message' => 'موجودی کافی نیست']);
			return;
		}

		$now            = date('Y-m-d H:i:s');
		$logged_user_id = $this->session->userdata('id');
		$new_qty        = $stock[0]->qty - $qty;

		$this->db->trans_start();

		// ✅ ثبت transaction
		$this->base_model->insert_data('inventory_transactions', [
			'inventory_id' => $inv_id,
			'attr_id'      => $attr_id,
			'type'         => 'out',
			'reference'    => $reference,
			'qty'          => $qty,
			'description'  => $desc,
			'created'      => $now,
			'created_by'   => $logged_user_id
		]);

		// ✅ آپدیت stock
		$this->base_model->update_data('inventory_stock',
			['qty' => $new_qty, 'modified' => $now],
			['inventory_id' => $inv_id, 'attr_id' => $attr_id]
		);

		// ✅ sync با product_attributes.supply
		$total_stock = $this->base_model->get_data(
			'inventory_stock', 'qty',
			null, null, ['attr_id' => [$attr_id]]
		);
		$total_qty = array_sum(array_column($total_stock, 'qty'));
		$this->base_model->update_data('product_attributes',
			['supply' => $total_qty, 'modified' => $now],
			['id' => $attr_id]
		);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در ثبت خروجی']);
			return;
		}

		echo json_encode(['status' => 1, 'new_qty' => $new_qty]);
	}

	/**
	 * ✅ تعدیل مستقیم موجودی (adjustment)
	 */
	public function adjust_stock()
	{
		$this->check_permission(['edit', 'full'], 'inventory');

		$inv_id  = $this->input->post('inv_id',   TRUE);
		$attr_id = $this->input->post('attr_id',  TRUE);
		$new_qty = intval($this->input->post('qty', TRUE));
		$desc    = $this->input->post('description', TRUE) ?: 'تعدیل موجودی';

		if (!$inv_id || !$attr_id || $new_qty < 0) {
			echo json_encode(['status' => 0, 'message' => 'داده نامعتبر است']);
			return;
		}

		$stock = $this->base_model->get_data('inventory_stock', '*', [
			'inventory_id' => $inv_id,
			'attr_id'      => $attr_id
		]);

		$current_qty    = !empty($stock) ? $stock[0]->qty : 0;
		$diff           = $new_qty - $current_qty;
		$now            = date('Y-m-d H:i:s');
		$logged_user_id = $this->session->userdata('id');

		$this->db->trans_start();

		// ✅ ثبت transaction فقط اگه تغییری بوده
		if ($diff !== 0) {
			$this->base_model->insert_data('inventory_transactions', [
				'inventory_id' => $inv_id,
				'attr_id'      => $attr_id,
				'type'         => $diff > 0 ? 'in' : 'out',
				'reference'    => 'adjustment',
				'qty'          => abs($diff),
				'description'  => $desc,
				'created'      => $now,
				'created_by'   => $logged_user_id
			]);
		}

		// ✅ آپدیت یا insert stock
		if (!empty($stock)) {
			$this->base_model->update_data('inventory_stock',
				['qty' => $new_qty, 'modified' => $now],
				['inventory_id' => $inv_id, 'attr_id' => $attr_id]
			);
		} else {
			$this->base_model->insert_data('inventory_stock', [
				'inventory_id' => $inv_id,
				'attr_id'      => $attr_id,
				'qty'          => $new_qty,
				'modified'     => $now
			]);
		}

		// ✅ sync با product_attributes.supply
		$total_stock = $this->base_model->get_data(
			'inventory_stock', 'qty',
			null, null, ['attr_id' => [$attr_id]]
		);
		$total_qty = array_sum(array_column($total_stock, 'qty'));
		$this->base_model->update_data('product_attributes',
			['supply' => $total_qty, 'modified' => $now],
			['id' => $attr_id]
		);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => 0, 'message' => 'خطا در تعدیل موجودی']);
			return;
		}

		echo json_encode(['status' => 1, 'new_qty' => $new_qty]);
	}




	/**
	 * ✅ نمایش صفحه تاریخچه ورود/خروج
	 * $inv_id: اگه null باشه همه انبارها، وگرنه فقط یه انبار
	 * $attr_id: اگه null باشه همه کالاها، وگرنه فقط یه کالا
	 */
	public function inventory_transactions($inv_id = null, $attr_id = null)
	{
		$logged_user_id = $this->session->userdata('id');
		if (!$logged_user_id) { redirect('admin/login_page'); return; }

		if (!$this->base_model->has_permission($logged_user_id, ['view', 'full'], 'inventory')) {
			$data['title'] = 'دسترسی غیرمجاز';
			$this->load->view('admin/layout/header', $data);
			$this->load->view('admin/layout/sidebar');
			$this->load->view('admin/errors/no_permission');
			return;
		}

		// ✅ اطلاعات انبار (برای breadcrumb و فیلتر)
		if ($inv_id) {
			$inventory = $this->base_model->get_data('inventory', '*', ['id' => $inv_id]);
			if (empty($inventory)) { redirect('admin/inventory'); return; }
			$data['inventory'] = $inventory[0];
		} else {
			$data['inventory'] = null;
		}

		// ✅ اطلاعات کالا (برای breadcrumb)
		if ($attr_id) {
			$attr = $this->base_model->get_data('product_attributes', '*', ['id' => $attr_id]);
			if (!empty($attr)) {
				$product = $this->base_model->get_data('products', 'name, code', ['code' => $attr[0]->code_p]);
				$data['attr']    = $attr[0];
				$data['product'] = !empty($product) ? $product[0] : null;
			}
		} else {
			$data['attr']    = null;
			$data['product'] = null;
		}

		$data['inv_id']  = $inv_id;
		$data['attr_id'] = $attr_id;
		$data['title']   = 'تاریخچه ورود/خروج';

		$this->load->view('admin/layout/header', $data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/inventory_transactions', $data);
	}

	/**
	 * ✅ لیست تراکنش‌ها برای DataTable
	 */
	public function inventory_transactions_list()
	{
		$this->check_permission(['view', 'full'], 'inventory');

		$post_data = $this->input->post(NULL, TRUE);
		$inv_id    = $this->input->post('inv_id',  TRUE) ?: null;
		$attr_id   = $this->input->post('attr_id', TRUE) ?: null;

		$columns = [
			'inventory_transactions.id',
			'inventory.name',
			'products.name',
			'inventory_transactions.type',
			'inventory_transactions.reference',
			'inventory_transactions.qty',
			'inventory_transactions.description',
			'inventory_transactions.created',
			null // ثبت‌کننده
		];

		$where = [];
		if ($inv_id)  $where['inventory_transactions.inventory_id'] = $inv_id;
		if ($attr_id) $where['inventory_transactions.attr_id']      = $attr_id;

		$result = $this->base_model->datatable(
			'inventory_transactions',
			$columns,
			$post_data,
			'inventory_transactions.*,
			 inventory.name AS inv_name,
			 products.name  AS prod_name,
			 products.code  AS prod_code,
			 users.phone_number AS creator_phone,
			 profile.name   AS creator_name,
			 profile.family AS creator_family',
			[
				'inventory'          => 'inventory.id = inventory_transactions.inventory_id',
				'product_attributes' => 'product_attributes.id = inventory_transactions.attr_id',
				'products'           => 'products.code = product_attributes.code_p',
				'users'              => 'users.id = inventory_transactions.created_by',
				'profile'            => 'profile.user_id = inventory_transactions.created_by'
			],
			!empty($where) ? $where : null,
			['inventory_transactions.id' => 'DESC']
		);

		$reference_labels = [
			'purchase'   => '<span class="label label-info">خرید</span>',
			'sale'       => '<span class="label label-primary">فروش</span>',
			'transfer'   => '<span class="label label-default">جابجایی</span>',
			'adjustment' => '<span class="label label-warning">تعدیل</span>',
			'return'     => '<span class="label label-danger">مرجوعی</span>',
			'initial'    => '<span class="label label-success">اولیه</span>',
		];

		$data = [];
		foreach ($result['data'] as $row) {
			$sub_array = [];

			// شناسه تراکنش
			$sub_array[] = '#' . $row->id;

			// انبار
			$sub_array[] = htmlspecialchars($row->inv_name ?? '—');

			// نام کالا
			$sub_array[] = '<span style="display:inline-block;max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="' . htmlspecialchars($row->prod_name ?? '') . '">'
				. htmlspecialchars($row->prod_name ?? '—') . '</span>';

			// نوع (ورود/خروج)
			$sub_array[] = ($row->type === 'in')
				? '<span class="label label-success"><i class="fa fa-arrow-down"></i> ورود</span>'
				: '<span class="label label-danger"><i class="fa fa-arrow-up"></i> خروج</span>';

			// نوع تراکنش
			$sub_array[] = $reference_labels[$row->reference] ?? htmlspecialchars($row->reference);

			// تعداد
			$sub_array[] = '<strong>' . number_format($row->qty) . '</strong>';

			// توضیحات
			$sub_array[] = htmlspecialchars($row->description ?? '—');

			// تاریخ
			$sub_array[] = htmlspecialchars($row->created);

			// ثبت‌کننده
			$creator = trim(($row->creator_name ?? '') . ' ' . ($row->creator_family ?? ''));
			$sub_array[] = $creator ?: htmlspecialchars($row->creator_phone ?? '—');

			$data[] = $sub_array;
		}

		echo json_encode([
			'draw'            => isset($post_data['draw']) ? intval($post_data['draw']) : 0,
			'recordsTotal'    => $result['recordsTotal'],
			'recordsFiltered' => $result['recordsFiltered'],
			'data'            => $data
		]);
	}























	/*public function insert_inv(){

		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name'];
			$data['details'] = $_POST['details'];
			$data['isActive'] = '1';

			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_message('is_unique[inventory.name]', 'این مقدار تکراری است');

			$this->form_validation->set_rules('name', 'کد تخفیف', 'trim|required|is_unique[inventory.name]');

			if ($this->form_validation->run()) {
				if ($_POST) {
					date_default_timezone_set("Asia/Tehran");
					$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data['name'] = $_POST['name'];
					$data['details'] = $_POST['details'];
					$data['isActive'] = '1';

					$this->base_model->insert('inventory',$data);

					redirect('admin/inventory');

				}

			} else {
				$this->inventory();
			}
		}

	}*/

	function search_name()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_name($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag" id="tag_'.$row->id.'" id_c="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function check_name()
	{

		if($this->base_model->is_input_available($_POST["name"],'name','inventory'))
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}
		else
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}

	}


	/*public function edit_inv(){

		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			$id = $_POST['id_modal'];
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name'] = $_POST['name_modal'];
			$data['details'] = $_POST['details_modal'];
			$data['isActive'] = '1';

			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_message('_edit_unique_name', 'در سیستم موجود است');

			$this->form_validation->set_rules('name_modal', 'کد تخفیف', 'trim|required|callback__edit_unique_name['.$id.']');

			if ($this->form_validation->run()) {
				if ($_POST) {
					$id = $_POST['id_modal'];
					date_default_timezone_set("Asia/Tehran");
					$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data['name'] = $_POST['name_modal'];
					$data['details'] = $_POST['details_modal'];
					$data['isActive'] = '1';

					$this->base_model->update('inventory', array('id'=>$id), $data);

					redirect('admin/inventory');

				}

			} else {
				$this->inventory();
			}
		}

	}*/

	function _edit_unique_name($x,$y)
	{
		$return_value = $this->base_model->check_value('inventory',array('name'=>$x,'id!='=>$y));
		if ($return_value)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function search_name2()
	{
		$output = '';
		$query = '';
		if($this->input->post('query'))
		{
			$query = $this->input->post('query');
		}

		$data = $this->base_model->search_name($query);


		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row)
			{
				$output .= ' <a
				class="tag2" id="tag2_'.$row->id.'" id_c2="'.$row->id.'" style="display: block;">'.$row->name.'</a> ';

			}
		} else {
			$output = 0;
		}
		echo $output;
	}
	function check_name2()
	{

		if($this->base_model->check_value('inventory',array('name'=>$_POST["name"],'id!='=>$_POST["id"])) )
		{
			echo '<label class="text-success"></span> قابل قبول است</label>';
		}
		else
		{
			echo '<label class="text-danger"></span> در سیستم موجود است</label>';
		}


	}

	public function inventory_supply($id){
		$data['title']='موجودی انبار';
		$data['id']=$id;
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['inventory_attrs']=$this->base_model->get_data('inventory_attrs','*');
		$data['inventory_supply']=$this->base_model->get_data('inventory_supply','*',array('id_inv'=>$id));
		$data['inventory']=$this->base_model->get_data('inventory','*',array('id'=>$id));

		$data['size']=$this->base_model->get_data('size','*');
		$data['model']=$this->base_model->get_data('model','*');
		$data['color']=$this->base_model->get_data('color','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['jens']=$this->base_model->get_data('jens','*');

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/inventory_supply');
	}

	public function insert_inv_sup(){
		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['id_attr'] = $_POST['aaa'];
			$id_attr = $_POST['aaa'];
			$data['supply'] = $_POST['supply'];
			$data['details'] = $_POST['details'];
			$data['isActive'] = '1';
			$id_inv = $_POST['id_inv'];

			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_rules('aaa', 'نام محصول', 'trim|required');

			if ($this->form_validation->run()) {
				if ($_POST) {
					$id_inv = $_POST['id_inv'];
					$id_attr = $_POST['aaa'];
					$inventory_supply=$this->base_model->get_data('inventory_supply','*',array('id_inv'=>$id_inv,'id_attr'=>$id_attr));
					if(isset($inventory_supply[0])){
						$supply = $inventory_supply[0]->supply;
					}
					$product_attributes=$this->base_model->get_data('product_attributes','*',array('id'=>$id_attr));
					if(isset($product_attributes[0])){
						$code_p = $product_attributes[0]->code_p;
						$unit = $product_attributes[0]->unit;
						$price = $product_attributes[0]->price;
						$buy_price = $product_attributes[0]->buy_price;
					}

					date_default_timezone_set("Asia/Tehran");
					$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data['id_attr'] = $_POST['aaa'];
					$data['supply'] = $_POST['supply'];
					$data['details'] = $_POST['details'];
					$data['isActive'] = '1';
					$data['code_p'] = $code_p;
					$data['id_inv'] = $id_inv;

					date_default_timezone_set("Asia/Tehran");
					$data2['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$s = $_POST['supply'];
					$data2['supply'] = $s+$supply;
					$data2['details'] = $_POST['details'];

					date_default_timezone_set("Asia/Tehran");
					$data3['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
					$data3['id_inv_from'] = '0';
					$data3['id_inv_to'] = $_POST['id_inv'];
					$data3['date'] = $this->date_j(date('Y-m-d'));
					$data3['id_attr'] = $_POST['aaa'];
					$data3['code_p'] = $code_p;
					$data3['unit'] = $unit;
					$data3['qty'] = $_POST['supply'];
					$data3['buy_price'] = $buy_price;
					$data3['price'] = $price;
					$data3['details'] = 'خرید';
					$data3['isActive'] = '1';


					if(isset($inventory_supply[0])){
						$this->base_model->update('inventory_supply',array('id_inv'=>$id_inv,'id_attr'=>$id_attr),$data2);
						$this->base_model->insert('inventory_attrs',$data3);
					}else{
						$this->base_model->insert('inventory_supply',$data);
						$this->base_model->insert('inventory_attrs',$data3);
					}


					redirect('admin/inventory_supply/'.$id_inv);

				}

			} else {
				$this->inventory();
			}
		}

	}

	public function inventory_supply_list($id){

		$id_inv = $id;
		$fetch_data = $this->base_model->inv_sup_make_datatables(array('inventory_supply.id_inv'=>$id_inv));
		$data = array();
		foreach($fetch_data as $row)
		{
			if ($row->id_inv==$id_inv ) {
				$sub_array = array();
				$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="' . $row->id_attr . '" id_attr="' . $row->id_attr . '" ></input> ';
				$sub_array[] = '<span id="code_' . $row->id_attr . '" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="' . $row->code . '">
			' . $row->code . '
			</span>';
				$sub_array[] = '<span id="name_' . $row->id_attr . '" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="' . $row->name . ' - ' . $row->price . ''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':'(').''.($row->moId==$row->model?"مدل: $row->moName ":'').''.($row->siId==$row->size?"سایز: $row->size ":'').''.($row->jeId==$row->jens?"جنس: $row->jens ":'').''.($row->brId==$row->brand?"برند: $row->brand ":'').''.($row->coId==$row->color?"رنگ: $row->color ":'').''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':')').'">
			' . $row->name . ' - ' . $row->price . ''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':'(').''.($row->moId==$row->model?"مدل: $row->moName ":'').''.($row->siId==$row->size?"سایز: $row->size ":'').''.($row->jeId==$row->jens?"جنس: $row->jens ":'').''.($row->brId==$row->brand?"برند: $row->brand ":'').''.($row->coId==$row->color?"رنگ: $row->color ":'').''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':')').'
			</span>';
				$sub_array[] = '<span>' . number_format($row->supply) . '</span>';
				$sub_array[] = $row->created;
				$sub_array[] = $row->modified;
				if ($row->isActive == 0) {
					$sub_array[] = '<button type="button" id="active" id_attr="' . $row->id_attr . '" class="btn btn-primary btn-xs">فعال سازی</button>';
				} else if ($row->isActive == 1) {
					$sub_array[] = '<button type="button" id="deactive" id_attr="' . $row->id_attr . '" class="btn btn-secondary btn-xs">غیرفعال سازی</button>';
				}
				$sub_array[] = '
			<button type="button" id="edit" id_attr="' . $row->id_attr . '" class="btn btn-warning ">
			<i class="fa fa-edit" ></i>
			</button>
			';
				$sub_array[] = '<button type="button" id="delete" id_attr="' . $row->id_attr . '" class="btn btn-danger">
			<i class="fa fa-trash" ></i>
			</button>';

				$data[] = $sub_array;
			}
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->inv_sup_get_all_data(array('inventory_supply.id_inv'=>$id_inv)),
			"recordsFiltered"     =>     $this->base_model->inv_sup_get_filtered_data(array('inventory_supply.id_inv'=>$id_inv)),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}

	public function insert_inventory_attrs(){
		$data['title']='افزودن اطلاعات کالا';
		$data['register']=$this->base_model->get_data('register','*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['inventory']=$this->base_model->get_data('inventory','*');
		$data['inventory_attrs']=$this->base_model->get_data('inventory_attrs','*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['size']=$this->base_model->get_data('size','*');
		$data['model']=$this->base_model->get_data('model','*');
		$data['color']=$this->base_model->get_data('color','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['jens']=$this->base_model->get_data('jens','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert_inventory_attrs');
	}

	public function add_inv_attr(){
		if ($_POST) {
			$this->load->library('form_validation');
			$this->load->helper('form');
			date_default_timezone_set("Asia/Tehran");
			$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['id_inv_from'] = $_POST['inv_from'];
			$inv_from = $_POST['inv_from'];
			$data['id_inv_to'] = $_POST['inv_to'];
			$inv_to = $_POST['inv_to'];
			$data['date'] = $_POST['datetime2'];
			$data['id_attr'] = $_POST['aaa'];
			$id_attr = $_POST['aaa'];
			$data['unit'] = $_POST['unit'];
			$unit = $_POST['unit'];
			$data['qty'] = $_POST['qty'];
			$qty = $_POST['qty'];
			$max = $_POST['max']+1;
			$data['buy_price'] = $_POST['buy_price'];
			$data['price'] = $_POST['price'];
			$data['details'] = $_POST['details'];
			$data['isActive'] = '1';

			$product_attributes=$this->base_model->get_data('product_attributes','*',array('id'=>$id_attr));
			if (isset($product_attributes[0])){
				$code_p = $product_attributes[0]->code_p;
			}

			$x=$this->base_model->get_data('inventory_supply','*',array('id_inv'=>$inv_from, 'id_attr'=>$id_attr));
			if (isset($x[0])){
				$supply = $x[0]->supply;
			} else {
				$supply = 0;
			}
			$x1=$this->base_model->get_data('inventory_supply','*',array('id_inv'=>$inv_to, 'id_attr'=>$id_attr));
			if (isset($x1[0])){
				$id1 = $x1[0]->id;
				$supply1 = $x1[0]->supply;
			} else {
				$id1 = '';
				$supply1 = 0;
			}


			$this->form_validation->set_message('required', 'فیلد الزامی');
			$this->form_validation->set_message('less_than', '%s باید کمتر از %d باشد');
			$this->form_validation->set_message('select_required', 'فیلد الزامی');

			$this->form_validation->set_rules('inv_from', 'انبار مبدا', 'trim|required|callback_select_required['.$inv_from.']');
			$this->form_validation->set_rules('inv_to', 'انبار مقصد', 'trim|required|callback_select_required['.$inv_to.']');
			$this->form_validation->set_rules('date', 'تاریخ', 'trim|required');
			$this->form_validation->set_rules('aaa', 'محصول', 'trim|required|callback_select_required['.$id_attr.']');
			$this->form_validation->set_rules('unit', 'واحد', 'trim|required|callback_select_required['.$unit.']');
			$this->form_validation->set_rules('qty', 'تعداد', 'trim|required');
			$this->form_validation->set_rules('buy_price', 'قیمت خرید', 'trim|required');
			$this->form_validation->set_rules('price', 'قیمت فروش', 'trim|required');
			$this->form_validation->set_rules('price', 'قیمت فروش', 'trim|required|greater_than['.$_POST['buy_price'].']');
			$this->form_validation->set_rules('qty', 'تعداد', 'trim|required|less_than['.$max.']');

			if ($this->form_validation->run()) {
				date_default_timezone_set("Asia/Tehran");
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data['id_inv_from'] = $_POST['inv_from'];
				$inv_from = $_POST['inv_from'];
				$data['id_inv_to'] = $_POST['inv_to'];
				$inv_to = $_POST['inv_to'];
				$data['date'] = $_POST['datetime2'];
				$data['id_attr'] = $_POST['aaa'];
				$id_attr = $_POST['aaa'];
				$data['code_p'] = $code_p;
				$data['unit'] = $_POST['unit'];
				$data['qty'] = $_POST['qty'];
				$qty = $_POST['qty'];
				$data['buy_price'] = $_POST['buy_price'];
				$data['price'] = $_POST['price'];
				$data['details'] = $_POST['details'];
				$data['isActive'] = '1';

				date_default_timezone_set("Asia/Tehran");
				$data2['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data2['supply'] = $supply-$qty;

				date_default_timezone_set("Asia/Tehran");
				$data3['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data3['supply'] = $supply1+$qty;

				$data4['id_inv'] = $inv_to;
				$data4['id_attr'] = $id_attr;
				$data4['code_p'] = $code_p;
				$data4['supply'] = $qty;
				$data4['isActive'] = '1';
				date_default_timezone_set("Asia/Tehran");
				$data4['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');

				$this->base_model->insert('inventory_attrs', $data);

				$this->base_model->update('inventory_supply',array('id_inv'=>$inv_from, 'id_attr'=>$id_attr) ,$data2);

				if($id1==''){
					$this->base_model->insert('inventory_supply', $data4);

				}else {
					$this->base_model->update('inventory_supply',array('id_inv'=>$inv_to, 'id_attr'=>$id_attr) ,$data3);
				}

				redirect('admin/insert_inventory_attrs');


			} else {
				$this->insert_inventory_attrs();
			}
		}
	}
	function select_required($x)
	{
		if($x == '0')
		{
			return FALSE;
		}
		return TRUE;
	}
	public function inventory_attrs($id){
		$data['title']='ویرایش انبار';
		$data['id']=$id;
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['products']=$this->base_model->get_data('products','*');
		//or
		$data['inventory_attrs']=$this->base_model->get_data('inventory_attrs','*',array('id_inv_from'=>$id),NULL, array('id_inv_to'=>$id));
		$data['inventory']=$this->base_model->get_data('inventory','*',array('id'=>$id));
		$data['register']=$this->base_model->get_data('register','*');
		$data['profile']=$this->base_model->get_data('profile','*');

		$data['size']=$this->base_model->get_data('size','*');
		$data['model']=$this->base_model->get_data('model','*');
		$data['color']=$this->base_model->get_data('color','*');
		$data['brand']=$this->base_model->get_data('brand','*');
		$data['jens']=$this->base_model->get_data('jens','*');

//		$this->session->set_flashdata('id_inv', $id);

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/inventory_attrs');
	}
	public function inventory_attrs_list($id){

//		$id = $this->session->flashdata('id_inv');

		$id_inv = $id;
		$fetch_data = $this->base_model->inventory_attrs_make_datatables(array('inventory_attrs.id_inv_from'=>$id_inv) || array('inventory_attrs.id_inv_to'=>$id_inv));
		$data = array();

		foreach($fetch_data as $row)
		{
			if ($row->id_inv_from==$id_inv || $row->id_inv_to==$id_inv){

				$sub_array = array();
				$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->row_id.'" id_row="'.$row->row_id.'" ></input> ';
				$sub_array[] = '<span style="display: inline-block;width: 30px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
				title="'.$row->code_p.'">
				'.$row->code_p.'
				</span>';
				$sub_array[] = '<span id="name_' . $row->id_attr . '" style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
			title="' . $row->pname . ' - ' . $row->price . ''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':'(').''.($row->moId==$row->model?"مدل: $row->moName ":'').''.($row->siId==$row->size?"سایز: $row->size ":'').''.($row->jeId==$row->jens?"جنس: $row->jens ":'').''.($row->brId==$row->brand?"برند: $row->brand ":'').''.($row->coId==$row->color?"رنگ: $row->color ":'').''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':')').'">
			' . $row->pname . ' - ' . $row->price . ''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':'(').''.($row->moId==$row->model?"مدل: $row->moName ":'').''.($row->siId==$row->size?"سایز: $row->size ":'').''.($row->jeId==$row->jens?"جنس: $row->jens ":'').''.($row->brId==$row->brand?"برند: $row->brand ":'').''.($row->coId==$row->color?"رنگ: $row->color ":'').''.($row->moId==''&&$row->siId==''&&$row->jeId==''&&$row->brId==''&&$row->coId==''?'':')').'
			</span>';
	//			$sub_array[] = '<span>'.number_format($row->buy_price).'</span>';
	//			$sub_array[] = '<span>'.number_format($row->price).'</span>';

				$sub_array[] = '<span style="display: inline-block;width: 50%;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" 
				title="'.$row->details.'">'.$row->details.'</span>';
				$sub_array[] = '<span>'.number_format($row->qty).'</span>';
				$sub_array[] = $row->unit;
				if($row->id_inv_from==$id_inv){$sub_array[] = 'خروج';}
				else if($row->id_inv_to==$id_inv){$sub_array[] = 'ورود';}
				$sub_array[] = '<span>'.$row->inv1_name.'</span>';
				$sub_array[] = '<span>'.$row->inv2_name.'</span>';
				$sub_array[] = $row->date;
				$sub_array[] = $row->created;
				$sub_array[] = $row->modified;
				if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" id_row="'.$row->row_id.'" class="btn btn-primary btn-xs">فعال سازی</button>';}
				else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" id_row="'.$row->row_id.'" class="btn btn-secondary btn-xs">غیرفعال سازی</button>';}
				$sub_array[] = '<a href="'. base_url('admin/edit_inventory_attrs/').$row->row_id.'">
				<button type="button" id="edit" id_row="'.$row->row_id.'" class="btn btn-warning ">
				<i class="fa fa-edit" ></i>
				</button>
				</a>';
				$sub_array[] = '<button type="button" id="delete" id_row="'.$row->row_id.'" class="btn btn-danger">
	<i class="fa fa-trash" ></i>
	</button>';

				$data[] = $sub_array;
			}
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->inventory_attrs_get_all_data(array('inventory_attrs.id_inv_from'=>$id_inv) || array('inventory_attrs.id_inv_to'=>$id_inv)),
			"recordsFiltered"     =>     $this->base_model->inventory_attrs_get_filtered_data(array('inventory_attrs.id_inv_from'=>$id_inv) || array('inventory_attrs.id_inv_to'=>$id_inv)),
			"data"                    =>     $data
		);
		echo json_encode($output);
	}


	public function edit_inventory_attrs($id){
		$data['title']='ویرایش انبار';
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$data['products']=$this->base_model->get_data('products','*');
		$data['inventory_attrs']=$this->base_model->get_data('inventory_attrs','*',array('id'=>$id));
		$data['inventory']=$this->base_model->get_data('inventory','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['profile']=$this->base_model->get_data('profile','*');

		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit_inventory_attrs');
	}







	public function rules()
	{
		$data['title']='قوانین و مقررات';
		$data['rules']=$this->base_model->get_data('rules','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/rules');
	}
	public function insert_rules()
	{
		if ($_POST) {
			$data['text'] = $_POST['text1'];
			$this->base_model->insert('rules', $data);
			echo 1;
		}
	}
	public function edit_rules()
	{
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['text']=$_POST['text2'];
			$this->base_model->update('rules',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function footer()
	{
		$data['title']='فوتر';
		$data['rules']=$this->base_model->get_data('rules','*');
		$data['call_us']=$this->base_model->get_data('call_us','*');
		$data['socials']=$this->base_model->get_data('socials','*');
		$data['links']=$this->base_model->get_data('links','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/footer');
	}
	public function edit_call_us()
	{
		if ($_POST) {
			$id = $_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['mobile'] = $_POST['mobile'];
			$data['phone'] = $_POST['phone'];
			$data['address'] = $_POST['address'];
			$data['postal_code'] = $_POST['postal_code'];
			$data['email'] = $_POST['email'];
			$data['text'] = $_POST['text'];
			$this->base_model->update('call_us', array('id'=>$id), $data);
			echo 1;
		}
	}
	public function edit_links()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$data['link1'] = $_POST['link1'];
			$data['link2'] = $_POST['link2'];
			$data['link3'] = $_POST['link3'];
			$data['link4'] = $_POST['link4'];
			$data['link5'] = $_POST['link5'];
			$data['link6'] = $_POST['link6'];
			$data['link7'] = $_POST['link7'];
			$data['link8'] = $_POST['link8'];
			$this->base_model->update('links', array('id'=>$id), $data);
			echo 1;
		}
	}
	public function edit_socials()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$data['whatsapp'] = $_POST['whatsapp'];
			$data['facebook'] = $_POST['facebook'];
			$data['telegram'] = $_POST['telegram'];
			$data['instagram'] = $_POST['instagram'];
			$this->base_model->update('socials', array('id'=>$id), $data);
			echo 1;
		}
	}



	public function comments(){
		$data['title']='نظرات کاربر';
		$data['comment']=$this->base_model->get_data('comments','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/comments2');
	}
	public function comments_product($code){
		$data['title']='نظرات محصول';
		$data['code']=$code;
		$data['comments']=$this->base_model->get_data('comments','*',array('code'=>$code));
		$data['comments2']=$this->base_model->get_data('comments','*',array('code'=>$code));
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/comments_product');
	}
	public function comments_user($uid){
		$data['title']='سوایق نظرات کاربر';
		$data['uid']=$uid;

		$data['profile']=$this->base_model->get_data('profile','*',array('user_id'=>$uid));

		$data['comments1']=$this->base_model->get_data('comments','*');
		$data['comments2']=$this->base_model->get_data('comments','*');


		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/comments_user');
	}

	function comments_list()
	{
		$fetch_data = $this->base_model->comments_make_datatables();
		$id = [];
		$data = [];
		$color = '';

		$profile=$this->base_model->get_data('profile','*');


		foreach($fetch_data as $row)
		{

			foreach($profile as $row1)
			{
				if($row->uid==$row1->user_id){
					$href=base_url('admin/comments_user/') .$row->uid;
				}
				if ($row->uid==''){
					$href="";
				}
			}


			if($row->role==0){$color='red';}

			$sub_array = [];
			$id[] = $color;


			$sub_array[] = '';
			$sub_array[] = '<a href="'. base_url('admin/comments_product/').$row->pcode .'"><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" title="'.$row->pname.'" >'.$row->pname.'</span></a>';
			$sub_array[] = '<a href='.($href=="" ? "" : $href).'><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" id="name_' . $row->id . '" title="'.$row->name.'" >'.$row->name.'</span></a>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;" id="text_' . $row->id . '" title="'.$row->text.'" >'.$row->text.'</span>';
			$sub_array[] = '<span style="" id_cmnt="' . $row->id . '">'.$row->created.'</span>';
			$sub_array[] = '<span style="" id_cmnt="' . $row->id . '">'.$row->modified.'</span>';
			$sub_array[] = '<span style="" id_cmnt="' . $row->id . '">'.$row->cond.'</span>';

			$sub_array[] =
				'<button style="display:'.($row->role ==0 || $row->role ==2 ? "inline" : "none").'" id="accept" id_cmnt="' . $row->id . '" class="btn btn-success btn-xs">تایید</button>
				<button style="display:'.($row->role ==0 || $row->role ==1 ? "inline" : "none").'" id="reject" id_cmnt="' . $row->id . '" class="btn btn-primary btn-xs">رد</button>';
			$sub_array[] = '<a href="'. base_url('admin/edit_comments/').$row->id.'/'.$row->code .'">
			<button type="button" id="edit" id_cmnt="' . $row->id . '" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';
			$sub_array[] = '<button id="delete" id_cmnt="' . $row->id . '" class="btn btn-danger">
<i class="fa fa-trash"></i>
</button>';

			$data[] = $sub_array;
			$color = '';

		}
		$output = [
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->comments_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->comments_get_filtered_data(),
			"data"                    =>     $data,
			"id"                    =>     $id,
		];

		echo json_encode($output);

	}
	function comments_list2()
	{
		$output = '';
		$query = '';
		if($_POST)
		{
			$query = $this->input->post('query');
		}
		$data = $this->base_model->search_comment($query);
		$data2= $this->base_model->get_data('products','*');

		$output .= '
  <div class="table-responsive">
     <table class="table table-bordered">
     			<tr>
					<th>نام محصول</th>
					<th>نام کاربر</th>
					<th>نظر</th>
					<th >تاریخ</th>
					<th >وضعیت</th>
					<th style="text-align: center">عملیات</th>
				</tr>
  ';
		if($data->num_rows() > 0)
		{
			foreach($data->result() as $row){ foreach ($data2 as $row2) {if ($row->code==$row2->code){
				if ($row->role ==1){
				$output .= '
            			<tr id="cmnt_' . $row->id . '"
						style="background-color: #fff;">
						<td>' . $row2->name . '</td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"  
						id="name_' . $row->id . '" >' . $row->name . '</span></td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"   
						id="text_' . $row->id . '">' . $row->text . '</span></td>
						<td><span id="date_' . $row->id . '" >' . $row->date . '</span></td>
						<td><span id="con_' . $row->id . '" >' . $row->cond . '</span></td>
						<td style="text-align: center">
							
							<button id="reject" id_cmnt="' . $row->id . '" class="btn btn-warning">رد</button>
							
							<button style="margin-right: if (' . $row->role . '==1){ echo 57px;}"
									class="btn btn-info" id="edit_1" id_cmnt="' . $row->id . '" >
								نمایش	 </button>
							<button id="delete" id_cmnt="' . $row->id . '" class="btn btn-danger">حذف</button>
						</td>
						</tr>
              ';

			}
			else if ($row->role ==0){
				$output .= '
            			<tr id="cmnt_' . $row->id . '"
						style="background-color: #fceeee;">
						<td>' . $row2->name . '</td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"  
						id="name_' . $row->id . '" >' . $row->name . '</span></td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"   
						id="text_' . $row->id . '">' . $row->text . '</span></td>
						<td><span id="date_' . $row->id . '" >' . $row->date . '</span></td>
						<td><span id="con_' . $row->id . '" >' . $row->cond . '</span></td>
						<td style="text-align: center">
							<div style="display: inline;">
								<button id="accept" id_cmnt="' . $row->id . '" class="btn btn-success">تایید</button>
								<button id="reject" id_cmnt="' . $row->id . '" class="btn btn-warning">رد</button>
							</div>
							<button
									class="btn btn-info" id="edit_1" id_cmnt="' . $row->id . '" >
								نمایش	 </button>
							<button id="delete" id_cmnt="' . $row->id . '" class="btn btn-danger">حذف</button>
						</td>
						</tr>
              ';
			}
			else if ($row->role ==2){
					$output .= '
            			<tr id="cmnt_' . $row->id . '"
						style="background-color: #fff;">
						<td>' . $row2->name . '</td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"  
						id="name_' . $row->id . '" >' . $row->name . '</span></td>
						<td><span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;"   
						id="text_' . $row->id . '">' . $row->text . '</span></td>
						<td><span id="date_' . $row->id . '" >' . $row->date . '</span></td>
						<td><span id="con_' . $row->id . '" >' . $row->cond . '</span></td>
						<td style="text-align: center">
							
							<button id="accept" id_cmnt="' . $row->id . '" class="btn btn-success">تایید</button>
							
							<button style="margin-right: if (' . $row->role . '==1){ echo 57px;}"
									class="btn btn-info" id="edit_1" id_cmnt="' . $row->id . '" >
								نمایش	 </button>
							<button id="delete" id_cmnt="' . $row->id . '" class="btn btn-danger">حذف</button>
						</td>
						</tr>
              ';

				}
			}}}
		}
		else
		{
			$output .= '<tr>
       <td colspan="5">موردی یافت نشد</td>
      </tr>';
		}
		$output .= '</table>';
		echo $output;
	}
	public function reject_comment()
	{
		if($_POST){
			$id=$_POST['id'];
			$data['role']='2';
			$data['cond']='رد شده';
			$this->base_model->update('comments',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function accept_comment()
	{
		if($_POST){
			$id=$_POST['id'];
			$data['role']='1';
			$data['cond']='تایید شده';
			$this->base_model->update('comments',array('id'=>$id) ,$data);
			echo 1;
		}
	}
	public function delete_comment()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$this->base_model->delete_row('comments', 'id', $id);
			echo 1;
		}
	}
	public function edit_comments($id,$code){
		$data['title']='نظرات کاربر';
		$data['code']=$code;
		$data['id']=$id;
		$data['products'] = $this->base_model->get_data('products', '*', array('code' => $code));
		if($this->session->userdata('user_id')) {
			$data['profile'] = $this->base_model->get_data('profile', '*', array('user_id' => $_SESSION['user_id']));}


		$comments1=$this->base_model->get_data('comments','*',array('id'=>$id));
		$data['parent_id']=$comments1[0]->parent_id;
		$parent_id=$comments1[0]->parent_id;

		if($parent_id==0){
			$data['comments1']=$this->base_model->get_data('comments','*',array('id'=>$id));
			$data['comments2']=$this->base_model->get_data('comments','*',array('parent_id'=>$id));
		}else{
			$data['comments1']=$this->base_model->get_data('comments','*',array('id'=>$parent_id,'id!='=>$id));
			$data['comments2']=$this->base_model->get_data('comments','*',array('parent_id'=>$parent_id));
		}


		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit_comments');
	}
	public function edit_cm()
	{
		if($_POST){
			$id=$_POST['id'];
			date_default_timezone_set("Asia/Tehran");
			$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
			$data['name']=$_POST['name'];
			$data['text']=$_POST['text'];
			$this->base_model->update('comments',array('id'=>$id) ,$data);
			echo 1;
		}
	}

	public function sales()
	{
		$data['title']='اطلاعات فروش';
		$data['info_fact']=$this->base_model->get_data('info_fact','*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*',NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'order_code');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/sales');
	}
	public function sales_list2(){
		$fetch_data = $this->base_model->ord_make_datatables();
		$data = [];

		foreach($fetch_data as $row)
		{

			$sub_array = array();

			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->id.'" id_ord="'.$row->id.'"></input> ';
			$sub_array[] = '
			<a href="'. base_url('admin/sale_datail/').$row->info_id.'/'.$row->order_code.'">
			<span id="ord_' . $row->id . '">'.$row->order_code.'</span>
			</a>';
			$sub_array[] = '
			<a href="'. base_url('admin/user_detail/').$row->prof_id.'">
			<span id="ord_' . $row->id . '">'.$row->name.' '.$row->family.'</span>
			</a>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;
" title="'.$row->payment.'">'.number_format($row->payment).'</span>';
			$sub_array[] = $row->status;
			date_default_timezone_set('Asia/Tehran');
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" id_ord="'.$row->id.'" class="btn btn-primary btn-xs">فعالسازی</button>';}
			else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" id_ord="'.$row->id.'" class="btn btn-secondry btn-xs">غیرفعالسازی</button>';}
			$sub_array[] = '
			<button type="button" id="copy" id_ord="'.$row->id.'" code_ord="'.$row->order_code.'" class="btn btn-info">
			<i class="fa fa-copy"></i>
			</button>
			';

			$sub_array[] = '<a href="'.  base_url('admin/edit_order/').$row->info_id.'/'.$row->order_code .'">
			<button type="button" id="edit" id_ord="'.$row->id.'" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';
			$sub_array[] = '<button type="button" id="delete" id_ord="'.$row->id.'" class="btn btn-danger">
			<i class="fa fa-trash" ></i>
			</button>';
			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->ord_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->ord_get_filtered_data(),
			"data"                    =>     $data,
		);
		echo json_encode($output);
	}
	public function sales_list(){
		$fetch_data = $this->base_model->ord_make_datatables();
		$data = [];

		foreach($fetch_data as $row)
		{

			$sub_array = array();

			$sub_array[] = '<input type="checkbox" class="checkall" name="row-check" value="'.$row->order_code.'" code_ord="'.$row->order_code.'"></input> ';
			$sub_array[] = '
			<a href="'. base_url('admin/sale_datail/').$row->order_code.'">
			<span id="ord_' . $row->order_code . '">'.$row->order_code.'</span>
			</a>';
			$sub_array[] = '
			<a href="'. base_url('admin/edit_user/').$row->id_user_ord.'">
			<span id="ord_' . $row->order_code . '">'.$row->name.' '.$row->family.'</span>
			</a>';
			$sub_array[] = '<span style="display: inline-block;width: 100px;white-space: nowrap; overflow: hidden !important;text-overflow: ellipsis;
" title="'.$row->payment.'">'.number_format($row->payment).'</span>';
			$sub_array[] = $row->status;
			date_default_timezone_set('Asia/Tehran');
			$sub_array[] = $row->created;
			$sub_array[] = $row->modified;
			if($row->isActive==0) {$sub_array[] = '<button type="button" id="active" code_ord="'.$row->order_code.'" class="btn btn-primary btn-xs">فعالسازی</button>';}
			else if($row->isActive==1){$sub_array[] = '<button type="button" id="deactive" code_ord="'.$row->order_code.'" class="btn btn-secondry btn-xs">غیرفعالسازی</button>';}
			$sub_array[] = '
			<button type="button" id="copy" code_ord="'.$row->order_code.'" class="btn btn-info">
			<i class="fa fa-copy"></i>
			</button>
			';

			$sub_array[] = '<a href="'.  base_url('admin/edit_order/').'/'.$row->order_code .'">
			<button type="button" id="edit" code_ord="'.$row->order_code.'" class="btn btn-warning">
			<i class="fa fa-edit" ></i>
			</button>
			</a>';
			$sub_array[] = '<button type="button" id="delete" code_ord="'.$row->order_code.'" class="btn btn-danger">
			<i class="fa fa-trash" ></i>
			</button>';
			$data[] = $sub_array;
		}
		$output = array(
			"draw"                    =>     intval($_POST["draw"]),
			"recordsTotal"          =>      $this->base_model->ord_get_all_data(),
			"recordsFiltered"     =>     $this->base_model->ord_get_filtered_data(),
			"data"                    =>     $data,
		);
		echo json_encode($output);
	}

	public function insert_factor(){
		$data['title'] = 'ثبت فاکتور جدید';
		$data['info_fact'] = $this->base_model->get_data('info_fact','*');
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*');
		$data['products'] = $this->base_model->get_data('products', '*');
		$data['off_code'] = $this->base_model->get_data('off_code', '*');
		$data['model'] = $this->base_model->get_data('model', '*');
		$data['size'] = $this->base_model->get_data('size', '*');
		$data['jens'] = $this->base_model->get_data('jens', '*');
		$data['brand'] = $this->base_model->get_data('brand', '*');
		$data['color'] = $this->base_model->get_data('color', '*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/insert_factor',$data);
	}
	public function insert_fact() {
		$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
		$data['id_attr'] = $_POST['id_attr'];
		$data['qty'] = $_POST['qty'];
		$data['price'] = $_POST['price'];
		$data['total_price1'] = $_POST['total_price1'];
		$data['off_percent'] = $_POST['off_percent'];
		$data['off_price'] = $_POST['off_price'];
		$data['total_price'] = $_POST['total_price'];
		$data['id_p'] = $_POST['id_p'];
		$data['name'] = $_POST['name'];
		$data['code_p'] = $_POST['code_p'];
		$data['final_price'] = $_POST['final_price'];
		$data['payment'] = $_POST['payment'];
		$data['order_code'] = $_POST['order_code'];
		$data['status'] = $_POST['status'];
		$data['user_id'] = $_POST['user_id'];
		$data['off_code'] = $_POST['off_code'];
		$data['off_code_mablagh'] = $_POST['off_code_mablagh'];
		$data['isActive'] = '1';
		echo $this->base_model->insert('shopping_cart_order', $data);
	}

	public function edit_order( $id2 ){
		$data['title'] = 'ویرایش فاکتور';
		$data['order_code'] = $id2;
		$data['info_fact'] = $this->base_model->get_data('info_fact','*',array('ord_code'=>$id2));
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*',array('order_code'=>$id2),NULL,NULL,NULL,'created DESC');
		$data['products'] = $this->base_model->get_data('products', '*');
		$data['off_code'] = $this->base_model->get_data('off_code', '*');
		$data['model'] = $this->base_model->get_data('model', '*');
		$data['size'] = $this->base_model->get_data('size', '*');
		$data['jens'] = $this->base_model->get_data('jens', '*');
		$data['brand'] = $this->base_model->get_data('brand', '*');
		$data['color'] = $this->base_model->get_data('color', '*');
		$data['profile']=$this->base_model->get_data('profile','*');
		$data['register']=$this->base_model->get_data('register','*');
		$data['product_attributes']=$this->base_model->get_data('product_attributes','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/edit_order',$data);
	}

	public function add_ord() {
		$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
		$data['id_attr'] = $_POST['id_attr'];
		$data['qty'] = $_POST['qty'];
		$data['price'] = $_POST['price'];
		$data['total_price1'] = $_POST['total_price1'];
		$data['off_percent'] = $_POST['off_percent'];
		$data['off_price'] = $_POST['off_price'];
		$data['total_price'] = $_POST['total_price'];
		$data['id_p'] = $_POST['id_p'];
		$data['name'] = $_POST['name'];
		$data['code_p'] = $_POST['code_p'];
		$data['final_price'] = $_POST['final_price'];
		$data['payment'] = $_POST['payment'];
		$data['order_code'] = $_POST['order_code'];
		$data['status'] = $_POST['status'];
		$data['user_id'] = $_POST['user_id'];
		$data['off_code'] = $_POST['off_code'];
		$data['off_code_mablagh'] = $_POST['off_code_mablagh'];
		$data['isActive'] = '1';
		echo $this->base_model->insert('shopping_cart_order', $data);
	}

	public function edit_ord() {
		$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
		$id = $_POST['id'];
		$data['id_attr'] = $_POST['id_attr'];
		$data['qty'] = $_POST['qty'];
		$data['price'] = $_POST['price'];
		$data['total_price1'] = $_POST['total_price1'];
		$data['off_percent'] = $_POST['off_percent'];
		$data['off_price'] = $_POST['off_price'];
		$data['total_price'] = $_POST['total_price'];
		$data['id_p'] = $_POST['id_p'];
		$data['name'] = $_POST['name'];
		$data['code_p'] = $_POST['code_p'];
		$data['final_price'] = $_POST['final_price'];
		$data['payment'] = $_POST['payment'];
		$data['order_code'] = $_POST['order_code'];
		$data['status'] = $_POST['status'];
		$data['user_id'] = $_POST['user_id'];
		$data['off_code'] = $_POST['off_code'];
		$data['off_code_mablagh'] = $_POST['off_code_mablagh'];
		$data['isActive'] = '1';
		echo $this->base_model->update('shopping_cart_order',array('id'=>$id), $data);
	}

	public function deleted_ord(){
		$ids = $_POST['id'];
		$order_code = $_POST['order_code'];
		$comma='';
		$ids2='';
		if (isset($_POST['id'])) {
			$ids = explode(',', $_POST['id']);
			$shopping_cart_order = $this->base_model->get_datas('shopping_cart_order', 'id','order_code',$order_code,'id', $ids);
			foreach ($shopping_cart_order as $ord){
				$ids2 = $ids2 . $comma . $ord->id;
				$comma=',';
				echo $ids2;
			}
			$ids3 = explode(',', $ids2);
			$results = $this->base_model->delete_rows_by_ids($ids3,'shopping_cart_order');

			if ($results === TRUE) {
				echo '<span style="color:green;">row(s) successfully deleted</span>';
			} else {
				echo '<span style="color:red;">Something went wrong during row deletion</span>';
			}


		} else {
			echo '<span style="color:red;">You must select at least one row for deletion</span>';
		}
	}

	public function get_product(){
		$id_attr = $_POST['id_attr'];
		$products = $this->base_model->get_data('products', '*');
		$product_attributes = $this->base_model->get_data('product_attributes', '*');
		$i=0;
		$output='';
		$order_code = $_POST['order_code'];
		$user_id = $_POST['user_id'];
		$status = $_POST['status'];
		$code_p = $_POST['code_p'];
		$id_p = $_POST['id_p'];

		if(isset($products[0])){
			foreach ($product_attributes as $pa){foreach ($products as $p){if($p->code == $pa->code_p){
			$data[$i]['name'] = $p->name;
			$data[$i]['code_p'] = $pa->code_p;
			$data[$i]['price'] = $pa->price;
			$data[$i]['off_percent'] = $pa->off_percent;
			$data[$i]['id_attr'] = $pa->id;
			$data[$i]['id_p'] = $p->id;
			$data[$i]['code_p'] = $p->code;
			$data[$i]['order_code'] = $order_code;
			$data[$i]['user_id'] = $user_id;
			$data[$i]['status'] = $status;
			$i++;
		}}}
		}
		for ($j=0;$j<count($data);$j++){
			$output .= '
		<option '.($data[$j]['id_attr']==$id_attr? 'selected':'').'
		value="'.$data[$j]['id_attr'].'"
		price="'.$data[$j]['price'].'"
		id_attr="'.$data[$j]['id_attr'].'"
		id_p="'.$data[$j]['id_p'].'"
		name="'.$data[$j]['name'].'"
		off_percent="'.$data[$j]['off_percent'].'"
		code_p="'.$data[$j]['code_p'].'"
		user_id="'.$data[$j]['user_id'].'"
		order_code="'.$data[$j]['order_code'].'"
		status="'.$data[$j]['status'].'"
		>
		'.$data[$j]['name'].' - '.$data[$j]['price'].' ()
		</option>
  				
			';
		}
		echo $output;
	}

	public function edit_insert_info(){
		if($_POST){
			$user_id = $_POST['user_id'];
			$ord_code = $_POST['order_code'];
			$rand_code = rand(000000000,999999999);;
			$info = $this->base_model->get_data('info_fact', '*',array('ord_code'=>$ord_code));
			if(isset($info[0])){
				date_default_timezone_set("Asia/Tehran");
				$data['modified'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data['name'] = $_POST['name'];
				$data['address'] = $_POST['address'];
				$data['postal_code'] = $_POST['postal_code'];
				$data['phone_number'] = $_POST['phone_number'];
				$this->base_model->update('info_fact',array('ord_code'=>$ord_code) ,$data);
			}
			else{
				date_default_timezone_set("Asia/Tehran");
				$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data2['ord_code'] = $ord_code;
				$data2['user_id'] = $user_id;
				$data2['name'] = $_POST['name'];
				$data2['address'] = $_POST['address'];
				$data2['postal_code'] = $_POST['postal_code'];
				$data2['phone_number'] = $_POST['phone_number'];
				$data2['random_code'] = $rand_code;
				$this->base_model->insert('info_fact',$data2);
			}
		}
	}

	public function get_profile(){
		$user_id = $_POST['user_id'];
		$profile = $this->base_model->get_data('profile', '*',array('user_id'=>$user_id));

		if(isset($profile[0])){
			$data['name'] = $profile[0]->name.' '.$profile[0]->family;
			$data['address'] = $profile[0]->address;
			$data['postal_code'] = $profile[0]->postal_code;
			$data['phone_number'] = $profile[0]->reciever_phone_number;
		}
		echo json_encode($data);

	}
	public function get_inventory(){
		$inv_from = $_POST['inv_from'];
		$inventory = $this->base_model->get_data('inventory', '*',array('id!='=>$inv_from));

		$result='<option value="0">انتخاب کنید..</option>';
		if(isset($inventory[0])){foreach ($inventory as $inv){
			$result=$result."<option value='$inv->id'>$inv->name</option>";
		}}
		echo $result;

	}
	public function get_inventory_supply(){
		$inv_from = $_POST['inv_from'];
		$inventory_supply = $this->base_model->get_data('inventory_supply', '*', array('id_inv'=>$inv_from, 'supply!='=>'0'));
		$a = array();
		if(isset($inventory_supply[0])){
			foreach ($inventory_supply as $inv_sup){
				$a[] = $inv_sup->id_attr;
			}
		}
		$products = $this->base_model->get_data('products', '*');
		$product_attributes = $this->base_model->get_datas('product_attributes', '*', 'id', $a);

		$model=$this->base_model->get_data('model','*');
		$jens=$this->base_model->get_data('jens','*');
		$brand=$this->base_model->get_data('brand','*');
		$color=$this->base_model->get_data('color','*');
		$size=$this->base_model->get_data('size','*');
		$result='<option value="0">انتخاب کنید..</option>';
		$x1='';
		$x2='';
		$x3='';
		$x4='';
		$x5='';
		$x6='';
		$x7='';
		foreach ($inventory_supply as $inv_sup){foreach ($product_attributes as $pa){foreach ($products as $p){if ($inv_sup->code_p==$p->code && $pa->code_p==$p->code){
			if($pa->model!=0){foreach ($model as $mo){if($pa->model==$mo->id){ $x1='مدل:'.$je->name.' ';}}}
			if($pa->jens!=0){foreach ($jens as $je){if($pa->jens==$je->id){ $x2='جنس:'.$je->name.' ';}}}
			if($pa->size!=0){foreach ($size as $si){if($pa->size==$si->id){ $x3='سایز:'.$si->name.' ';}}}
			if($pa->brand!=0){foreach ($brand as $br){if($pa->brand==$br->id){ $x4='برند:'.$br->name.' ';}}}
			if($pa->color!=0){foreach ($color as $co){if($pa->color==$co->id){ $x5='رنگ:'.$co->name.' ';}}}
			if($x1!=''||$x2!=''||$x3!=''||$x4!=''||$x5!=''){
				$x6='(';
				$x7=')';
			}
			$result.="<option value='$inv_sup->id_attr' unit='$pa->unit' supply='$inv_sup->supply' buy_price='$pa->buy_price' price='$pa->price'>$p->name - $pa->price $x6 $x1 $x2 $x3 $x4 $x5 $x7</option>";
		}}}}
		if($result=='<option value="0">انتخاب کنید..</option>'){
			$result.='<optgroup label="انبار مبدا موجودی ندارد.." style="background-color: yellow; text-align: center; font-family: Farnaz"></optgroup>';
		}
		$data['result']=$result;
		echo json_encode($data);
	}

	public function copy_order()
	{
		$code = $_POST['code'];
		date_default_timezone_set("Asia/Tehran");
		$order = $this->base_model->get_data('shopping_cart_order', '*', array('order_code' => $code));
		$info = $this->base_model->get_data('info_fact', '*', array('ord_code' => $code));

		$ord1=rand(000000000,999999999);

		if ($this->base_model->is_input_unique($ord1, 'order_code', 'shopping_cart_order')) {
			foreach ($order as $ord) {
				$data['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data['order_code'] = $ord1;

				$data['user_id'] = $ord->user_id;
				$data['id_p'] = $ord->id_p;
				$data['code_p'] = $ord->code_p;
				$data['id_attr'] = $ord->id_attr;
				$data['name'] = $ord->name;
				$data['price'] = $ord->price;
				$data['qty'] = $ord->qty;
				$data['off_percent'] = $ord->off_percent;
				$data['off_price'] = $ord->off_price;
				$data['total_price1'] = $ord->total_price1;
				$data['total_price'] = $ord->total_price;
				$data['final_price'] = $ord->final_price;
				$data['off_code'] = $ord->off_code;
				$data['payment'] = $ord->payment;
				$data['off_code_mablagh'] = $ord->off_code_mablagh;
				$data['status'] = 'در انتظار ارسال';

				$data['isActive'] = $ord->isActive;
				$this->base_model->insert('shopping_cart_order', $data);
			}


			foreach ($info as $inf) {
				date_default_timezone_set("Asia/Tehran");
				$data2['created'] = $this->date_j(date('Y-m-d')) . ' ' . date('H:i:s');
				$data2['ord_code'] = $ord1;
				$data2['user_id'] = $inf->user_id;
				$data2['name'] = $inf->name;
				$data2['address'] = $inf->address;
				$data2['postal_code'] = $inf->postal_code;;
				$data2['phone_number'] = $inf->phone_number;
				$random_code = rand(000000000, 999999999);
				$data2['random_code'] = $random_code;
				$this->base_model->insert('info_fact', $data2);
			}
			echo 1;
		}


	}
	public function delete_ord()
	{
		if ($_POST) {
			if (isset($_POST['val'])) {
				$val = explode(',', $_POST['val']);
				$col = $_POST['col'];
				$table = $_POST['table'];
				$col2 = 'ord_code';
				$table2 = 'info_fact';

				$results = $this->base_model->delete_rows_by_col($col, $val,$table);

				if ($results === TRUE) {
					$results2 = $this->base_model->delete_rows_by_col($col2, $val,$table2);
					if ($results2 === TRUE) {
						echo '<span style="color:green;">row(s) successfully deleted</span>';
					}
				} else {
					echo '<span style="color:red;">Something went wrong during row deletion</span>';
				}
			} else {
				echo '<span style="color:red;">You must select at least one row for deletion</span>';
			}
		}
	}
	public function delete_orders() {
		if (isset($_POST['val'])) {
			$val = explode(',', $_POST['val']);
			$col = $_POST['col'];
			$table = $_POST['table'];
			$col2 = 'ord_code';
			$table2 = 'info_fact';

			$results = $this->base_model->delete_rows_by_col($col, $val,$table);

			if ($results === TRUE) {
				$results2 = $this->base_model->delete_rows_by_col($col2, $val,$table2);
				if ($results2 === TRUE) {
					echo '<span style="color:green;">row(s) successfully deleted</span>';
				}
			} else {
				echo '<span style="color:red;">Something went wrong during row deletion</span>';
			}
		} else {
			echo '<span style="color:red;">You must select at least one row for deletion</span>';
		}
	}
	public function user_detail($id){
		$data['title']='مشخصات دریافت کننده';
		$data['profile']=$this->base_model->get_data('profile','*',array('id'=>$id));
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/user_detail');
	}
	public function sale_datail($id2)
	{
		$data['title']='جزئیات فروش';
		$data['info_fact']=$this->base_model->get_data('info_fact','*',array('ord_code'=>$id2));
		$data['shopping_cart_order']=$this->base_model->get_data('shopping_cart_order','*',array('order_code'=>$id2));
		$data['products'] = $this->base_model->get_data('products', '*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/sale-detail');
	}
	public function delete_sale()
	{
		if ($_POST) {
			$id = $_POST['id'];
			$order_code = $_POST['order_code'];
			$this->base_model->delete_row('info_fact', 'id', $id);
			$this->base_model->delete_row('shopping_cart_order', 'order_code', $order_code);
			echo 1;
		}
	}
	function upload_img2()
	{
		//	sleep(3);
		$code = $_POST['code'];
		if($_FILES["files"]["name"] != '')
		{
			if (!is_dir('uploads/product/'.$code)){
				mkdir('uploads/product/'.$code, 0777, TRUE);
			}
			$output = '';
			$config["upload_path"] = 'uploads/product/'.$code.'/';
			$config["allowed_types"] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			for($count = 0; $count<count($_FILES["files"]["name"]); $count++)
			{
				$_FILES["file"]["name"] = $_FILES["files"]["name"][$count];
				$_FILES["file"]["type"] = $_FILES["files"]["type"][$count];
				$_FILES["file"]["tmp_name"] = $_FILES["files"]["tmp_name"][$count];
				$_FILES["file"]["error"] = $_FILES["files"]["error"][$count];
				$_FILES["file"]["size"] = $_FILES["files"]["size"][$count];

				if($this->upload->do_upload('file'))
				{
					$data = $this->upload->data();
					$uploadData[$count]['direction']='uploads/product/'.$code.'/'.$data['file_name'];
					$uploadData[$count]['role']='product';
					$uploadData[$count]['user_id']=$code;

					foreach($uploadData as $row1) {
						$img_id = $this->base_model->insert('images', $row1);
						$output .= '
  			<div id="show_image_' . $img_id . '" style="border: 1px solid rebeccapurple;border-radius: 10px;padding: 5px;width: 113px;height: 140px;text-align: center;margin:0 0 10px 10px;display: inline-block;overflow:auto">
			<span id="delete_image" id_img="' . $img_id . '" style="color: red;cursor: pointer;margin-bottom: 5px;">حذف</span>
			<img style="width: 100px;height: 100px;" src="' . base_url() . 'uploads/product/' . $code . '/' . $data["file_name"] . '">
			</div>	
			';
					}
				}
			}
			echo $output;
		}
	}

	function upload_img(){
		$code = $_POST['code'];
		$data = array();
		$errorUploadType = $statusMsg = '';

			// If files are selected to upload
			if(!empty($_FILES['files']['name']) && count(array_filter($_FILES['files']['name'])) > 0){
				$filesCount = count($_FILES['files']['name']);
				for($i = 0; $i < $filesCount; $i++){
					$_FILES['file']['name']     = $_FILES['files']['name'][$i];
					$_FILES['file']['type']     = $_FILES['files']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
					$_FILES['file']['error']     = $_FILES['files']['error'][$i];
					$_FILES['file']['size']     = $_FILES['files']['size'][$i];

					// File upload configuration
					if (!is_dir('uploads/product/'.$code)){
						mkdir('uploads/product/'.$code, 0777, TRUE);
					}
					$output = '';
					$config["upload_path"] = 'uploads/product/'.$code.'/';
					$config["allowed_types"] = 'gif|jpg|png|jpeg';
					//$config['max_size']    = '100';
					//$config['max_width'] = '1024';
					//$config['max_height'] = '768';

					// Load and initialize upload library
					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					// Upload file to server

					if($this->upload->do_upload('file')){
						// Uploaded file data
						$fileData = $this->upload->data();
						$uploadData[$i]['direction']='uploads/product/'.$code.'/'.$fileData['file_name'];
						$uploadData[$i]['role']='product';
						$uploadData[$i]['user_id']=$code;
						$uploadData[$i]['created'] = date("Y-m-d H:i:s");
					}else{
						$errorUploadType .= $_FILES['file']['name'].' | ';
					}
				}

				$errorUploadType = !empty($errorUploadType)?'<br/>File Type Error: '.trim($errorUploadType, ' | '):'';
				if(!empty($uploadData)){
					// Insert files data into the database
					foreach($uploadData as $row1) {
						$img_id = $this->base_model->insert('images', $row1);
						$dir=$this->base_model->get_data('images','*',array('id'=>$img_id));
						$output .= '
  			<div id="show_image_' . $img_id . '" style="border: 1px solid rebeccapurple;
  			border-radius: 10px;padding: 5px;width: 113px;height: 140px;
  			text-align: center;margin: 0 0 10px 10px;background-color: #fff">
			<span id="delete_image" id_img="' . $img_id . '" style="color: red;cursor: pointer;margin-bottom: 5px;">حذف</span>
			<img style="width: 100px;height: 100px;border: 1px solid #000;" src="' . base_url() . $dir[0]->direction .'">
			</div>	
			';
					}

					// Upload status message
/*					$statusMsg = $insert?'Files uploaded successfully!'.$errorUploadType:'Some problem occurred, please try again.';*/
				}else{
//					$statusMsg = "Sorry, there was an error uploading your file.".$errorUploadType;
				}
			}
			echo $output;
		}



	public function delete_img()
	{
		if($_POST){
			$id=$_POST['id'];
			$images=$this->base_model->get_data('images','*',array('id'=>$id));
			$this->base_model->delete('images',$id);
			unlink($images[0]->direction);
			echo 1;
		}
	}
	public function merchantid()
	{
		$data['title']='';
		$data['merchant']=$this->base_model->get_data('merchant','*');
		$this->load->view('admin/layout/header',$data);
		$this->load->view('admin/layout/sidebar');
		$this->load->view('admin/merchantid');
	}
	public function edit_merchant()
	{
		if($_POST){
			$id=$_POST['id'];
			$data['text']=$_POST['text'];
			$this->base_model->update('merchant',array('id'=>$id) ,$data);
			echo 1;
		}
	}

	public function status(){
		if ($_POST) {
			$id = $_POST['id'];
			$data['status'] = $_POST['status'];
			$this->base_model->update('shopping_cart_order', array('order_code' => $id), $data);
		}
	}






}
