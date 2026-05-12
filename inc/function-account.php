<?php
add_action('wp_ajax_create_account_handle', 'create_account_handle');
add_action('wp_ajax_nopriv_create_account_handle', 'create_account_handle');

function create_account_handle()
{
	if (!check_ajax_referer('register_account', 'nonce', false)) {
		wp_send_json_error(array('message' => __('Xác thực không hợp lệ', 'canhcamtheme'), 'status' => 'error'));
	}



	$form_data = [];
	if (isset($_POST['dataForm'])) {
		parse_str($_POST['dataForm'], $form_data);
	}

	// Sanitize inputs
	$fullName = sanitize_text_field($form_data['fullName'] ?? ($_POST['fullName'] ?? ''));
	$displayName = sanitize_text_field($form_data['displayName'] ?? ($_POST['displayName'] ?? ''));
	$phone = sanitize_text_field($form_data['phone'] ?? ($_POST['phone'] ?? ''));
	$email = sanitize_email($form_data['email'] ?? ($_POST['email'] ?? ''));
	$birthDate = sanitize_text_field($form_data['birthDate'] ?? ($_POST['birthDate'] ?? ''));
	$password = $form_data['password'] ?? ($_POST['password'] ?? '');
	$confirmPassword = $form_data['confirmPassword'] ?? ($_POST['confirmPassword'] ?? '');
	$idNumber = sanitize_text_field($form_data['idNumber'] ?? ($_POST['idNumber'] ?? ''));
	$idDate = sanitize_text_field($form_data['idDate'] ?? ($_POST['idDate'] ?? ''));
	$idPlace = sanitize_text_field($form_data['idPlace'] ?? ($_POST['idPlace'] ?? ''));
	$newsletter = !empty($form_data['newsletter']) || !empty($_POST['newsletter']) ? 1 : 0;
	$termsAccept = !empty($form_data['termsAccept']) || !empty($_POST['termsAccept']) ? 1 : 0;
	$ageConfirm = !empty($form_data['ageConfirm']) || !empty($_POST['ageConfirm']) ? 1 : 0;

	if (empty($fullName) || empty($displayName) || empty($phone) || empty($email) || empty($birthDate) || empty($password) || empty($confirmPassword) || empty($idNumber) || empty($idDate) || empty($idPlace)) {
		wp_send_json_error(array('message' => __('Vui lòng nhập đầy đủ thông tin', 'canhcamtheme'), 'data' => $fullName, 'status' => 'error'));
	}
	if (!$termsAccept || !$ageConfirm) {
		wp_send_json_error(array('message' => __('Bạn phải đồng ý điều khoản và xác nhận độ tuổi.', 'canhcamtheme'), 'status' => 'error'));
	}
	if (exist_user_phone($phone)) {
		wp_send_json_error(array('message' => __('Số điện thoại đã tồn tại', 'canhcamtheme'), 'status' => 'error'));
	}
	if ($password !== $confirmPassword) {
		wp_send_json_error(array('message' => __('Mật khẩu không khớp', 'canhcamtheme'), 'status' => 'error'));
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		wp_send_json_error(array('message' => __('Email không hợp lệ', 'canhcamtheme'), 'status' => 'error'));
	}
	$register_otp = get_transient('register_otp_' . $email);

	$daily_attempts_key = 'otp_daily_attempts_' . md5($email);
	$hourly_attempts_key = 'otp_hourly_attempts_' . md5($email);
	$daily_attempts = (int) get_transient($daily_attempts_key);
	$hourly_attempts = (int) get_transient($hourly_attempts_key);
	if ($daily_attempts >= 15) {
		wp_send_json_error(array('message' => __('Email của bạn đã vượt quá số lần gửi OTP trong ngày. Vui lòng thử lại sau.', 'canhcamtheme'), 'status' => 'error', 'email' => $email));
	}
	if ($hourly_attempts >= 5) {
		wp_send_json_error(array('message' => __('Email của bạn đã vượt quá số lần gửi OTP trong giờ. Vui lòng thử lại sau.', 'canhcamtheme'), 'status' => 'error', 'email' => $email));
	}



	$user_data = [
		'full_name' => $fullName,
		'display_name' => $displayName,
		'email' => $email,
		'password' => $password,
		'phone' => $phone,
		'birth_date' => $birthDate,
		'id_number' => $idNumber,
		'id_date' => $idDate,
		'id_place' => $idPlace,
		'newsletter' => $newsletter,
	];

	$result = save_user_handle(0, $user_data);

	if (is_wp_error($result)) {
		wp_send_json_error(array('message' => $result->get_error_message(), 'status' => 'error'));
	}
	create_otp_send_email($result, $email);
	ob_start();
	get_template_part('components/my-account/form-otp', '', array('email' => $email));
	$html_otp = ob_get_clean();


	wp_send_json_success(array('message' => __('Tạo tài khoản thành công, vui lòng kiểm tra email để lấy mã OTP', 'canhcamtheme'), 'status' => 'success', 'email' => $email, 'html_otp' => $html_otp));
}

function create_otp_send_email($user_id, $email)
{
	$daily_attempts_key  = 'otp_daily_attempts_' . md5($email);
	$hourly_attempts_key = 'otp_hourly_attempts_' . md5($email);

	$daily_attempts  = (int) get_transient($daily_attempts_key);
	$hourly_attempts = (int) get_transient($hourly_attempts_key);

	// Tạo OTP và lưu vào transient
	$otp = wp_rand(100000, 999999);
	set_transient('register_otp_' . $user_id, $otp, 5 * MINUTE_IN_SECONDS);

	// Reset số lần nhập sai
	set_transient('otp_attempts_' . $user_id, 0, 5 * MINUTE_IN_SECONDS);
	set_transient($daily_attempts_key, $daily_attempts + 1, 24 * HOUR_IN_SECONDS);
	set_transient($hourly_attempts_key, $hourly_attempts + 1, HOUR_IN_SECONDS);

	// Subject
	$subject = __('Xác minh đăng ký tài khoản', 'canhcamtheme');

	// Nội dung HTML
	$message = '
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f4f4f4; padding:30px 0;">
    <tr>
      <td align="center">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="background-color:#ffffff; border-radius:8px; padding:20px 30px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
          <tr>
            <td align="left">
              <h2 style="color:#333333; font-size:20px; margin:0 0 15px 0;">' . __('Xin chào!', 'canhcamtheme') . '</h2>
              <p style="color:#555555; font-size:14px; line-height:1.5; margin:0 0 15px 0;">' . __('Cảm ơn bạn đã đăng ký tài khoản.', 'canhcamtheme') . '</p>
              <p style="color:#555555; font-size:14px; line-height:1.5; margin:0 0 10px 0;">' . __('Mã OTP của bạn là:', 'canhcamtheme') . '</p>
              <div style="font-size:24px; font-weight:bold; color:#f03a2b; margin:20px 0; text-align:center;">' . $otp . '</div>
              <p style="color:#555555; font-size:14px; line-height:1.5; margin:0 0 15px 0;">' . __('Mã này có hiệu lực trong 5 phút. Vui lòng không chia sẻ mã này cho bất kỳ ai.', 'canhcamtheme') . '</p>
              <p style="margin-top:30px; font-size:12px; color:#888888; text-align:center;">&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>';

	add_filter('wp_mail_content_type', function () {
		return "text/html";
	});

	wp_mail($email, $subject, $message);

	remove_filter('wp_mail_content_type', 'set_html_content_type');
}

// 
function verify_otp_handle()
{
	if (!check_ajax_referer('verify_otp', 'nonce', false)) {
		wp_send_json_error(array('message' => __('Xác thực không hợp lệ', 'canhcamtheme'), 'status' => 'error'));
	}

	$email = sanitize_email($_POST['email'] ?? '');
	$otp = sanitize_text_field($_POST['otp_verify'] ?? '');

	$user = get_user_by('email', $email);
	if (!$user) {
		wp_send_json_error(array('message' => __('Email không tồn tại', 'canhcamtheme'), 'status' => 'error'));
	}

	// Kiểm tra số lần nhập sai
	// $attempts = (int) get_transient('otp_attempts_' . $user->ID);
	// if ($attempts >= 3) {
	//     wp_send_json_error(array('message' => __('Bạn đã nhập sai OTP quá 3 lần. Vui lòng thử lại sau.', 'canhcamtheme'), 'status' => 'error'));
	// }
	$action = isset($_POST['action_otp']) ? sanitize_text_field($_POST['action_otp']) : '';


	$stored_otp = get_transient('register_otp_' . $user->ID);
	if ($stored_otp && $stored_otp == $otp) {
		// Xóa OTP và số lần thử sau khi xác minh thành công
		delete_transient('register_otp_' . $user->ID);
		delete_transient('otp_attempts_' . $user->ID);
		update_user_meta($user->ID, '_account_status', 'active');

		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID, true);
		$first_login = get_user_meta($user->ID, '_first_login', true);
		update_user_meta($user->ID, '_first_login', 1);
		if ($action == 'update_info') {
			$action = 'verify_otp';
			$nonce = wp_create_nonce('save_my_account');
		}

		wp_send_json_success(array(
			'message' => __('Xác thực thành công', 'canhcamtheme'),
			'status' => 'success',
			'email' => $email,
			'action' => $action,
			'first_login' => $first_login,
			'nonce_my_account' => $nonce,
			'redirect_url' => get_my_account_page(),
		));
	}


	// set_transient('otp_attempts_' . $user->ID, $attempts + 1, 5 * MINUTE_IN_SECONDS);
	wp_send_json_error(array(
		'message' => __('Mã OTP không hợp lệ', 'canhcamtheme'),
		'status' => 'error'
	));
}
add_action('wp_ajax_verify_otp_handle', 'verify_otp_handle');
add_action('wp_ajax_nopriv_verify_otp_handle', 'verify_otp_handle');

// Chặn đăng nhập nếu tài khoản chưa kích hoạt
add_filter('wp_authenticate_user', function ($user) {
	if (is_a($user, 'WP_User') && get_user_meta($user->ID, '_account_status', true) == 'pending') {
		return new WP_Error('account_not_activated', __('Tài khoản chưa được kích hoạt. Vui lòng xác minh OTP.', 'canhcamtheme'));
	}
	return $user;
}, 10, 1);

/* < ---------------------------------------------------------------> */



function save_user_handle($user_id, $data)
{
	if (! empty($data['social_contact'])) {
		$clean = preg_replace('/\s+/', '', $data['social_contact']);

		if (preg_match('/^\+?\d{8,15}$/', $clean)) {
			$social_contact = sanitize_text_field($clean);
		} else {
			$social_contact = esc_url_raw($data['social_contact']);
		}
	} else {
		$social_contact = '';
	}
	$user_data = [
		'full_name'        => sanitize_text_field($data['full_name'] ?? ''),
		'display_name'     => sanitize_text_field($data['display_name'] ?? ''),
		'email'            => sanitize_email($data['email'] ?? ''),
		'password'         => $data['password'] ?? '',
		'phone'            => sanitize_text_field($data['phone'] ?? ''),
		'birth_date'       => sanitize_text_field($data['birth_date'] ?? ''),
		'id_number'        => sanitize_text_field($data['id_number'] ?? ''),
		'id_date'          => sanitize_text_field($data['id_date'] ?? ''),
		'id_place'         => sanitize_text_field($data['id_place'] ?? ''),
		'newsletter'       => !empty($data['newsletter']) ? 1 : 0,
		'gender'           => sanitize_text_field($data['gender'] ?? ''),
		'user_description' => sanitize_text_field($data['user_description'] ?? ''),
		'province_current' => sanitize_text_field($data['province_current'] ?? ''),
		'ward_current'     => sanitize_text_field($data['ward_current'] ?? ''),
		'district_current' => sanitize_text_field($data['district_current'] ?? ''),
		'province_from'    => sanitize_text_field($data['province_from'] ?? ''),
		'district_from'    => sanitize_text_field($data['district_from'] ?? ''),
		'education'        => isset($data['education']) ? array_map('sanitize_text_field', (array)$data['education']) : [],
		'social_facebook'  => esc_url_raw($data['social_facebook'] ?? ''),
		'social_instagram' => esc_url_raw($data['social_instagram'] ?? ''),
		'social_tiktok'    => esc_url_raw($data['social_tiktok'] ?? ''),
		'social_youtube'   => esc_url_raw($data['social_youtube'] ?? ''),
		'social_threads'   => esc_url_raw($data['social_threads'] ?? ''),
		'social_linkedin'  => esc_url_raw($data['social_linkedin'] ?? ''),
		'social_twitter'   => esc_url_raw($data['social_twitter'] ?? ''),
		'social_contact'   => $social_contact,
		'id_front'         => esc_url_raw($data['id_front'] ?? ''),
		'id_back'          => esc_url_raw($data['id_back'] ?? ''),
		'bio_image'        => esc_url_raw($data['bio_image'] ?? ''),
		'user_setting_followers' => $data['setting_followers'] ?? "show",
		'user_setting_following' => $data['setting_following'] ?? "show",
	];

	if ($user_id && get_user_by('ID', $user_id)) {
		$user_args = [
			'ID'           => $user_id,
			'user_email'   => $user_data['email'],
			'display_name' => $user_data['display_name'],
			'first_name'   => $user_data['full_name'],
		];

		if (!empty($user_data['password'])) {
			$user_args['user_pass'] = $user_data['password'];
		}

		$updated_user = wp_update_user($user_args);

		if (is_wp_error($updated_user)) {
			return $updated_user;
		}
	} else {
		$email = $user_data['email'];

		if (email_exists($email)) {
			return new WP_Error('email_exists', __('Email đã tồn tại!', 'canhcamtheme'));
		}

		if (username_exists($email)) {
			return new WP_Error('username_exists', __('Email đã tồn tại!', 'canhcamtheme'));
		}
		$user_args = [
			'user_login'   => $user_data['email'],
			'user_email'   => $user_data['email'],
			'user_pass'    => $user_data['password'],
			'display_name' => $user_data['display_name'],
			'first_name'   => $user_data['full_name'],
			'role'         => 'subscriber',
		];

		$user_id = wp_insert_user($user_args);

		if (is_wp_error($user_id)) {
			return $user_id;
		}
	}

	if (! empty($data['social_contact'])) {
		$clean = preg_replace('/\s+/', '', $data['social_contact']);

		if (preg_match('/^\+?\d{8,15}$/', $clean)) {
			$social_contact = sanitize_text_field($clean);
		} else {
			$social_contact = esc_url_raw($data['social_contact']);
		}
	} else {
		$social_contact = '';
	}
	// Lưu các trường meta\
	if (!empty($user_data['phone'])) {
		update_user_meta($user_id, 'user_phone', $user_data['phone']);
	}
	if (!empty($user_data['birth_date'])) {
		update_user_meta($user_id, 'user_birth_date', $user_data['birth_date']);
	}
	if (!empty($user_data['id_number'])) {
		update_user_meta($user_id, 'user_id_number', $user_data['id_number']);
	}
	if (!empty($user_data['id_date'])) {
		update_user_meta($user_id, 'user_id_date', $user_data['id_date']);
	}
	if (!empty($user_data['id_place'])) {
		update_user_meta($user_id, 'user_id_place', $user_data['id_place']);
	}
	if (!empty($user_data['newsletter'])) {
		update_user_meta($user_id, 'user_newsletter', $user_data['newsletter']);
	}
	if (!empty($user_data['gender'])) {
		update_user_meta($user_id, 'user_gender', $user_data['gender']);
	}
	if (!empty($user_data['user_description'])) {
		update_user_meta($user_id, 'user_description', $user_data['user_description']);
	}
	if (!empty($user_data['province_current'])) {
		update_user_meta($user_id, 'user_province_current', $user_data['province_current']);
	}
	if (!empty($user_data['ward_current'])) {
		update_user_meta($user_id, 'user_ward_current', $user_data['ward_current']);
	}
	if (!empty($user_data['district_current'])) {
		update_user_meta($user_id, 'user_district_current', $user_data['district_current']);
	}
	if (!empty($user_data['province_from'])) {
		update_user_meta($user_id, 'user_province_from', $user_data['province_from']);
	}
	if (!empty($user_data['district_from'])) {
		update_user_meta($user_id, 'user_district_from', $user_data['district_from']);
	}
	if (!empty($user_data['education'])) {
		update_user_meta($user_id, 'user_education', $user_data['education']);
	}
	if (!empty($user_data['social_facebook'])) {
		update_user_meta($user_id, 'user_social_facebook', $user_data['social_facebook']);
	}
	if (!empty($user_data['social_instagram'])) {
		update_user_meta($user_id, 'user_social_instagram', $user_data['social_instagram']);
	}
	if (!empty($user_data['social_tiktok'])) {
		update_user_meta($user_id, 'user_social_tiktok', $user_data['social_tiktok']);
	}
	if (!empty($user_data['social_threads'])) {
		update_user_meta($user_id, 'user_social_threads', $user_data['social_threads']);
	}
	if (!empty($user_data['social_linkedin'])) {
		update_user_meta($user_id, 'user_social_linkedin', $user_data['social_linkedin']);
	}
	if (!empty($user_data['social_twitter'])) {
		update_user_meta($user_id, 'user_social_twitter', $user_data['social_twitter']);
	}
	if (!empty($social_contact)) {
		update_user_meta($user_id, 'user_social_contact', $social_contact);
	}
	if (!empty($user_data['id_front'])) {
		update_user_meta($user_id, 'user_id_front', $user_data['id_front']);
	}
	if (!empty($user_data['id_back'])) {
		update_user_meta($user_id, 'user_id_back', $user_data['id_back']);
	}
	if (!empty($user_data['bio_image'])) {
		update_user_meta($user_id, 'user_bio_image', $user_data['bio_image']);
	}
	if (!empty($user_data['user_setting_followers'])) {
		update_user_meta($user_id, 'user_setting_followers', $user_data['user_setting_followers']);
	}
	if (!empty($user_data['user_setting_following'])) {
		update_user_meta($user_id, 'user_setting_following', $user_data['user_setting_following']);
	}



	$first_login = get_user_meta($user_id, '_first_login', true);
	if (empty($first_login)) {
		update_user_meta($user_id, '_first_login', 0);
	}
	$account_status = get_user_meta($user_id, '_account_status', true);
	if (empty($account_status)) {
		update_user_meta($user_id, '_account_status', 'pending');
	}

	return $user_id;
}

/* < ---------------------------------------------------------------> */

function exist_user_phone($phone, $exclude_user_id = 0)
{
	$phone = preg_replace('/^\+84/', '0', trim($phone));

	$args = [
		'fields'     => 'ID',
		'meta_query' => [
			[
				'key'     => 'user_phone',
				'value'   => $phone,
				'compare' => '='
			]
		]
	];

	// Loại bỏ user đang check (nếu có)
	if ($exclude_user_id) {
		$args['exclude'] = [intval($exclude_user_id)];
	}

	$users = get_users($args);

	return ! empty($users);
}

function exist_user_email($email, $user_id = 0)
{
	$user = get_user_by('email', $email);
	if ($user && $user->ID != $user_id) {
		return true;
	}
	return false;
}

add_action('wp_ajax_login_account_handle', 'login_account_handle');
add_action('wp_ajax_nopriv_login_account_handle', 'login_account_handle');

function login_account_handle()
{
	if (!check_ajax_referer('login_account', 'nonce', false)) {
		wp_send_json_error(array('message' => __('Xác thực không hợp lệ', 'canhcamtheme'), 'status' => 'error'));
	}
	$email = sanitize_email($_POST['email'] ?? '');
	$password = sanitize_text_field($_POST['password'] ?? '');
	if (empty($email) || empty($password)) {
		wp_send_json_error(array('message' => __('Vui lòng nhập đầy đủ thông tin', 'canhcamtheme'), 'status' => 'error'));
		return;
	}

	$user = get_user_by('email', $email);
	if (!$user) {
		wp_send_json_error(array('message' => __('Email không tồn tại', 'canhcamtheme'), 'status' => 'error'));
		return;
	}


	ob_start();


	get_template_part('components/my-account/form-otp', '', array('email' => $email));
	$html_otp = ob_get_clean();
	if (!wp_check_password($password, $user->user_pass, $user->ID)) {
		wp_send_json_error(array(
			'message' => __('Mật khẩu không đúng', 'canhcamtheme'),
			'status'  => 'error'
		));
		return;
	}

	if (get_user_meta($user->ID, '_account_status', true) == 'pending') {

		$otp = get_transient('register_otp_' . $user->ID);
		if (!$otp) {
			create_otp_send_email($user->ID, $email);
		}

		wp_send_json_error(array('message' => __('Tài khoản của bạn chưa được xác thực', 'canhcamtheme'), 'status' => 'error', 'otp' => $otp, 'email' => $email, 'html_otp' => $html_otp));
		return;
	}

	if (get_user_meta($user->ID, '_account_status', true) == 'blocked') {
		wp_send_json_error(array('message' => __('Tài khoản của bạn đã bị khóa ', 'canhcamtheme'), 'status' => 'error'));
		return;
	}

	$user = wp_signon([
		'user_login' => $email,
		'user_password' => $password,
		'remember' => true,
	]);
	$first_login = get_user_meta($user->ID, '_first_login', true);


	if (is_wp_error($user)) {
		wp_send_json_error(array('message' => $user->get_error_message(), 'status' => 'error'));
		return;
	}
	wp_set_current_user($user->ID);
	wp_set_auth_cookie($user->ID);
	if (!$first_login) {
		update_user_meta($user->ID, '_first_login', 1);
	}
	wp_send_json_success(array('message' => __('Đăng nhập thành công', 'canhcamtheme'), 'status' => 'success', 'first_login' =>  $first_login));
}



add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar()
{
	if (current_user_can('subscriber') && !is_admin()) {
		show_admin_bar(false);
	}
}
function generate_random_filename($original_name, $length = 16)
{
	$extension = pathinfo($original_name, PATHINFO_EXTENSION);
	$random_name = wp_generate_password($length, false);

	// Đảm bảo có extension
	if ($extension) {
		return $random_name . '.' . strtolower($extension);
	} else {
		return $random_name;
	}
}


// upload avatar
add_action('wp_ajax_ajax_upload_avatar', 'handle_ajax_upload_avatar');
add_action('wp_ajax_nopriv_ajax_upload_avatar', 'handle_ajax_upload_avatar');

function handle_ajax_upload_avatar()
{
	if (!is_user_logged_in()) {
		wp_send_json_error(['message' => __('Bạn cần đăng nhập.', 'canhcamtheme')]);
	}

	if (!function_exists('wp_handle_upload')) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	if (!function_exists('wp_generate_attachment_metadata')) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$file = $_FILES['account-profile-avatar'] ?? null;
	if (!$file || $file['error'] !== 0) {
		wp_send_json_error(['message' => __('Không có file hợp lệ.', 'canhcamtheme')]);
	}

	$random_name = generate_random_filename($file['name']);
	$_FILES['account-profile-avatar']['name'] = $random_name;

	// Upload file
	$upload = wp_handle_upload($_FILES['account-profile-avatar'], ['test_form' => false]);

	if (!empty($upload['error'])) {
		wp_send_json_error(['message' => $upload['error']]);
	}

	$file_path = $upload['file'];
	$file_url  = $upload['url'];
	$file_type = wp_check_filetype($file_path);

	$attachment = [
		'guid'           => $file_url,
		'post_mime_type' => $file_type['type'],
		'post_title'     => sanitize_file_name($random_name),
		'post_content'   => '',
		'post_status'    => 'inherit',
	];

	$attach_id = wp_insert_attachment($attachment, $file_path);

	// Tạo metadata cho attachment
	$attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
	wp_update_attachment_metadata($attach_id, $attach_data);

	// Lưu URL ảnh hoặc ID vào user meta
	$user_id = get_current_user_id();
	update_user_meta($user_id, 'user_avatar', $file_url);
	// Hoặc: update_user_meta($user_id, 'user_avatar_id', $attach_id);

	wp_send_json_success([
		'url' => $file_url,
		'id'  => $attach_id,
	]);
}


add_filter('get_avatar_url', function ($url, $id_or_email) {
	$user = false;
	if (is_numeric($id_or_email)) {
		$user = get_user_by('id', $id_or_email);
	} elseif (is_object($id_or_email) && isset($id_or_email->user_id)) {
		$user = get_user_by('id', $id_or_email->user_id);
	} else {
		$user = get_user_by('email', $id_or_email);
	}

	if ($user) {
		$custom = get_user_meta($user->ID, 'user_avatar', true);
		if ($custom) return $custom;
	}

	return $url;
}, 10, 2);

function check_user_exists_avatar($file)
{
	$user_id = get_current_user_id();
	$user_avatar = get_user_meta($user_id, 'user_avatar', true);
	if ($user_avatar) {
		return true;
	}
	return false;
}

add_filter('get_avatar', function ($avatar, $id_or_email, $size, $default, $alt) {
	$user = false;

	if (is_numeric($id_or_email)) {
		$user = get_user_by('id', $id_or_email);
	} elseif (is_object($id_or_email) && isset($id_or_email->user_id)) {
		$user = get_user_by('id', $id_or_email->user_id);
	} else {
		$user = get_user_by('email', $id_or_email);
	}

	if ($user) {
		$custom_avatar = get_user_meta($user->ID, 'user_avatar', true);
		if (!empty($custom_avatar)) {
			return sprintf(
				'<img alt="%s"  src="%s" class="avatar user-avatar avatar-%d photo" height="%d" width="%d" />',
				esc_attr($alt),
				esc_url($custom_avatar),
				$size,
				$size,
				$size
			);
		}
	}

	return $avatar;
}, 10, 5);
// Update anh bia

add_action('wp_ajax_ajax_upload_banner', 'handle_ajax_upload_banner');
add_action('wp_ajax_nopriv_ajax_upload_banner', 'handle_ajax_upload_banner');

function handle_ajax_upload_banner()
{
	if (!is_user_logged_in()) {
		wp_send_json_error(['message' => __('Bạn chưa đăng nhập.', 'canhcamtheme')]);
	}

	if (!function_exists('wp_handle_upload')) {
		require_once(ABSPATH . 'wp-admin/includes/file.php');
	}
	if (!function_exists('wp_generate_attachment_metadata')) {
		require_once(ABSPATH . 'wp-admin/includes/image.php');
	}

	$file = $_FILES['account-profile-banner'] ?? null;
	$random_name = generate_random_filename($file['name']);
	$_FILES['account-profile-banner']['name'] = $random_name;
	if (!$file || $file['error'] !== 0) {
		wp_send_json_error(['message' => __('Không có file hoặc lỗi khi upload.', 'canhcamtheme')]);
	}

	$upload = wp_handle_upload($_FILES['account-profile-banner'], ['test_form' => false]);

	if (!empty($upload['error'])) {
		wp_send_json_error(['message' => $upload['error']]);
	}

	$user_id   = get_current_user_id();
	$file_path = $upload['file'];
	$file_url  = $upload['url'];
	$file_type = wp_check_filetype($file_path);

	// Tạo attachment
	$attachment = [
		'guid'           => $file_url,
		'post_mime_type' => $file_type['type'],
		'post_title'     => sanitize_file_name($file['name']),
		'post_content'   => '',
		'post_status'    => 'inherit',
	];

	$attach_id = wp_insert_attachment($attachment, $file_path);

	// Tạo metadata
	$attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
	wp_update_attachment_metadata($attach_id, $attach_data);

	// Lưu vào user meta (có thể lưu ID hoặc URL)
	update_user_meta($user_id, 'user_image_cover', $file_url);

	// Trả về URL và attachment ID nếu cần
	wp_send_json_success([
		'url' => $file_url,
		'id'  => $attach_id,
	]);
}



function get_user_image_cover_url($user_id)
{
	$user_image_cover = get_user_meta($user_id, 'user_image_cover', true);
	$default_image = get_template_directory_uri() . '/img/image-cover.png';
	if ($user_image_cover) {
		return $user_image_cover;
	}
	return $default_image;
}

// update_user_meta(2, '_first_login', 0);
// update_user_meta(6, '_account_status', 1);


function get_register_page()
{
	$page_template_register = get_page_link_by_template('templates/template_register.php');
	return $page_template_register;
}

function get_login_page()
{
	$page_template_login = get_page_link_by_template('templates/template_login.php');
	return $page_template_login;
}

function get_my_account_page()
{
	$page_template_my_account = get_page_link_by_template('templates/my-account.php');
	return $page_template_my_account;
}
function get_my_account_change_password_page()
{
	$page_template_my_account_change_password = get_page_link_by_template('templates/template-change-password.php');
	return $page_template_my_account_change_password;
}
function get_my_account_create_post_page()
{
	$page_template_my_account_create_post = get_page_link_by_template('create-post.php');
	return $page_template_my_account_create_post;
}

function get_login_trend()
{
	$page_template_trend = get_page_link_by_template('templates/template_trend.php');
	return $page_template_trend;
}
function get_page_id_by_template($template)
{
	$page = get_pages(array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => $template,
		'number'     => 1
	));
	return !empty($page) ? $page[0]->ID : false;
}
function is_register_page()
{
	$page_id = get_page_id_by_template('templates/template_register.php');
	if ($page_id && is_page($page_id)) {
		return true;
	}
	return false;
}
function is_login_page()
{
	$page_id = get_page_id_by_template('templates/template_login.php');
	if ($page_id && is_page($page_id)) {
		return true;
	}
	return false;
}
function is_close_password_page()
{
	$page_id = get_page_id_by_template('templates/template_close-password.php');
	if ($page_id && is_page($page_id)) {
		return true;
	}
	return false;
}
function is_lost_password_page()
{
	$page_id = get_page_id_by_template('templates/template_lost-password.php');
	if ($page_id && is_page($page_id)) {
		return true;
	}
	return false;
}

function is_reset_password_page()
{
	$page_id = get_page_id_by_template('templates/template_reset-password.php');
	if ($page_id && is_page($page_id)) {
		return true;
	}
	return false;
}
function is_create_post_page()
{
	$page_id = get_page_id_by_template('create-post.php');
	if ($page_id && is_page($page_id)) {
		return true;
	}
	return false;
}

function is_edit_post_page()
{
	$page_id = get_page_id_by_template('edit-post.php');
	if ($page_id && is_page($page_id)) {
		return true;
	}
	return false;
}

function is_login_trend()
{
	$page_id = get_page_id_by_template('templates/template_trend.php');
	if ($page_id && is_page($page_id)) {
		return true;
	}
	return false;
}





function handle_image_upload_user($input_name, $user_id, $meta_key)
{
	if (!empty($_FILES[$input_name]['name'])) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Bước 1: Upload ảnh vào thư mục uploads
		$random_name = generate_random_filename($_FILES[$input_name]['name']);
		$_FILES[$input_name]['name'] = $random_name;
		$uploaded = wp_handle_upload($_FILES[$input_name], ['test_form' => false]);

		if (!isset($uploaded['error'])) {
			$file_url  = $uploaded['url'];
			$file_type = wp_check_filetype($uploaded['file'], null);
			$file_path = $uploaded['file'];

			// Bước 2: Tạo attachment (post type = attachment)
			$attachment = [
				'guid'           => $file_url,
				'post_mime_type' => $file_type['type'],
				'post_title'     => sanitize_file_name($_FILES[$input_name]['name']),
				'post_content'   => '',
				'post_status'    => 'inherit'
			];

			$attach_id = wp_insert_attachment($attachment, $file_path);

			// Bước 3: Tạo metadata cho ảnh (thumbnail, sizes...)
			$attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
			wp_update_attachment_metadata($attach_id, $attach_data);

			// Bước 4: Lưu URL hoặc attachment ID tùy bạn
			update_user_meta($user_id, $meta_key, $file_url);

			return $file_url;
		} else {
			return new WP_Error('upload_error', $uploaded['error']);
		}
	}

	return false;
}

add_action('wp_ajax_ajax_save_my_account_handle', 'ajax_save_my_account_handle');
add_action('wp_ajax_nopriv_ajax_save_my_account_handle', 'ajax_save_my_account_handle');

function ajax_save_my_account_handle()
{
	if (isset($_POST['action_otp']) && $_POST['action_otp'] != 'verify_otp') {
		// Kiểm tra nonce
		if (!check_ajax_referer('save_my_account', 'nonce', false)) {
			wp_send_json_error([
				'message' => __('Xác thực không hợp lệ', 'canhcamtheme'),
				'status' => 'error'
			]);
		}
	}

	// Lấy ID người dùng
	$user_id = get_current_user_id();
	if (!$user_id) {
		wp_send_json_error([
			'message' => __('Không xác định được người dùng', 'canhcamtheme'),
			'status' => 'error'
		]);
	}

	if (exist_user_phone($_POST['phone'], $user_id) && $_POST['phone'] != get_user_meta($user_id, 'user_phone', true)) {
		wp_send_json_error([
			'message' => __('Số điện thoại đã tồn tại', 'canhcamtheme'),
			'status' => 'error'
		]);
	}
	$current_user = wp_get_current_user();
	$current_email = $current_user->user_email;
	$new_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
	if (!empty($new_email) && $new_email !== $current_email && exist_user_email($new_email, $current_user->ID)) {
		wp_send_json_error([
			'message' => __('Email đã tồn tại', 'canhcamtheme'),
			'status'  => 'error'
		]);
	}
	$action = isset($_POST['action_otp']) ? sanitize_text_field($_POST['action_otp']) : '';
	$user_id = get_current_user_id();

	$current_phone = get_user_meta($user_id, 'user_phone', true);
	$new_phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

	if ($action !== 'verify_otp') {
		$is_changed_email = !empty($new_email) && $new_email !== $current_email;
		$is_changed_phone = !empty($new_phone) && $new_phone !== $current_phone;

		if ($is_changed_email || $is_changed_phone) {
			create_otp_send_email($user_id, $current_email);

			ob_start();
			get_template_part('components/my-account/form-otp', '', [
				'email'  => $current_email,
				'action' => 'update_info',
			]);
			$html_otp = ob_get_clean();

			wp_send_json_error([
				'message' => __('Vui lòng xác thực OTP để cập nhật thông tin', 'canhcamtheme'),
				'data'    => $html_otp,
				'status'  => 'otp'
			]);
		}
	}
	if (!empty($_POST['social_contact'])) {
		$clean = preg_replace('/\s+/', '', $_POST['social_contact']);

		if (preg_match('/^\+?\d{8,15}$/', $clean)) {
			$social_contact = sanitize_text_field($clean);
		} else {
			$social_contact = esc_url_raw($_POST['social_contact']);
		}
	} else {
		$social_contact = '';
	}

	// Chuẩn bị dữ liệu từ POST
	$user_data = [
		'full_name'        => sanitize_text_field($_POST['fullName'] ?? ''),
		'display_name'     => sanitize_text_field($_POST['displayName'] ?? ''),
		'email'            => sanitize_email($_POST['email'] ?? ''),
		'password'         => $_POST['password'] ?? '',
		'phone'            => sanitize_text_field($_POST['phone'] ?? ''),
		'birth_date'       => sanitize_text_field($_POST['user_birth_date'] ?? ''),
		'id_number'        => sanitize_text_field($_POST['idNumber'] ?? ''),
		'id_date'          => sanitize_text_field($_POST['idDate'] ?? ''),
		'id_place'         => sanitize_text_field($_POST['idPlace'] ?? ''),
		'id_front'         => sanitize_text_field($_POST['idFront'] ?? ''),
		'id_back'          => sanitize_text_field($_POST['idBack'] ?? ''),
		'newsletter'       => !empty($_POST['newsletter']) ? 1 : 0,
		'gender'           => sanitize_text_field($_POST['gender'] ?? ''),
		'user_description' => sanitize_text_field($_POST['user_description'] ?? ''),
		'province_current' => sanitize_text_field($_POST['province-current'] ?? ''),
		'ward_current'     => sanitize_text_field($_POST['ward-current'] ?? ''),
		'district_current' => sanitize_text_field($_POST['district-current'] ?? ''),
		'province_from'    => sanitize_text_field($_POST['province-from'] ?? ''),
		'district_from'    => sanitize_text_field($_POST['district-from'] ?? ''),
		'education'        => isset($_POST['education']) ? array_map('sanitize_text_field', (array)$_POST['education']) : [],
		'social_facebook'  => esc_url_raw($_POST['social_facebook'] ?? ''),
		'social_instagram' => esc_url_raw($_POST['social_instagram'] ?? ''),
		'social_tiktok'    => esc_url_raw($_POST['social_tiktok'] ?? ''),
		'social_youtube'   => esc_url_raw($_POST['social_youtube'] ?? ''),
		'social_threads'   => esc_url_raw($_POST['social_threads'] ?? ''),
		'social_linkedin'  => esc_url_raw($_POST['social_linkedin'] ?? ''),
		'social_twitter'   => esc_url_raw($_POST['social_twitter'] ?? ''),
		'social_contact'   => $social_contact,
		'setting_followers' => !empty($_POST['setting_followers']) ? $_POST['setting_followers'] : "hide",
		'setting_following' => !empty($_POST['setting_following']) ? $_POST['setting_following'] : "hide",
	];

	// Xử lý tải lên hình ảnh
	$image_fields = [
		'idFront'   => 'user_id_front',
		'idBack'    => 'user_id_back',
		'bio_image' => 'user_bio_image',
	];

	foreach ($image_fields as $input_name => $meta_key) {
		$upload_result = handle_image_upload_user($input_name, $user_id, $meta_key);
		if ($upload_result && !is_wp_error($upload_result)) {
			$user_data[str_replace('user_', '', $meta_key)] = $upload_result;
		} elseif (is_wp_error($upload_result)) {
			wp_send_json_error([
				'message' => $upload_result->get_error_message(),
				'status' => 'error'
			]);
		}
	}

	// Gọi hàm lưu thông tin người dùng
	$result = save_user_handle($user_id, $user_data);

	if (is_wp_error($result)) {
		wp_send_json_error([
			'message' => $result->get_error_message(),
			'status' => 'error'
		]);
	}

	// Trả về phản hồi thành công
	if ($action == 'verify_otp') {
		$reload = true;
	}

	wp_send_json_success([
		'message' => __('Cập nhật thông tin thành công', 'canhcamtheme'),
		'status' => 'success',
		'reload' => $reload ? true : false,
	]);
}




add_action('wp_ajax_ajax_save_education_handle', 'ajax_save_education_handle');
add_action('wp_ajax_nopriv_ajax_save_education_handle', 'ajax_save_education_handle');

function ajax_save_education_handle()
{
	// if (!check_ajax_referer('save_education', 'nonce', false)) {
	//     wp_send_json_error([
	//         'message' => __('Xác thực không hợp lệ', 'canhcamtheme'),
	//         'status' => 'error'
	//     ]);
	// }
	$user_id = get_current_user_id();
	if (!$user_id) {
		wp_send_json_error([
			'message' => __('Không xác định được người dùng', 'canhcamtheme'),
			'status' => 'error'
		]);
	}
	$education = isset($_POST['data']) ? $_POST['data'] : [];

	if (!empty($education)) {
		update_user_meta($user_id, 'user_list_education', $education);
		$data_education = get_user_meta($user_id, 'user_list_education', true);
		wp_send_json_success(['message' => __('Cập nhật thông tin thành công', 'canhcamtheme'), 'status' => 'success', 'data' => $data_education]);
	} else {
		delete_user_meta($user_id, 'user_list_education');
		wp_send_json_success(['message' => __('Xóa thông tin thành công', 'canhcamtheme'), 'status' => 'success', 'data' => []]);
	}
}

// update_user_meta(get_current_user_id(), 'user_list_education', '');
// save work

add_action('wp_ajax_ajax_save_work_handle', 'ajax_save_work_handle');
add_action('wp_ajax_nopriv_ajax_save_work_handle', 'ajax_save_work_handle');

function ajax_save_work_handle()
{
	// if (!check_ajax_referer('save_education', 'nonce', false)) {
	//     wp_send_json_error([
	//         'message' => __('Xác thực không hợp lệ', 'canhcamtheme'),
	//         'status' => 'error'
	//     ]);
	// }
	$user_id = get_current_user_id();
	if (!$user_id) {
		wp_send_json_error([
			'message' => __('Không xác định được người dùng', 'canhcamtheme'),
			'status' => 'error'
		]);
	}
	$work = isset($_POST['data']) ? $_POST['data'] : [];

	if (!empty($work)) {
		update_user_meta($user_id, 'user_list_work', $work);
		$data_work = get_user_meta($user_id, 'user_list_work', true);
		wp_send_json_success(['message' => __('Cập nhật thông tin thành công', 'canhcamtheme'), 'status' => 'success', 'data' => $data_work]);
	} else {
		delete_user_meta($user_id, 'user_list_work');
		wp_send_json_success(['message' => __('Xóa thông tin thành công', 'canhcamtheme'), 'status' => 'success', 'data' => []]);
	}
}



add_action('wp_ajax_ajax_change_password_handle', 'ajax_change_password_handle');
add_action('wp_ajax_nopriv_ajax_change_password_handle', 'ajax_change_password_handle');

function ajax_change_password_handle()
{
	if (!check_ajax_referer('change_password', 'nonce', false)) {
		wp_send_json_error([
			'message' => __('Xác thực không hợp lệ', 'canhcamtheme'),
			'status' => 'error'
		]);
	}
	$current_user = wp_get_current_user();
	$current_password = isset($_POST['current_password']) ? sanitize_text_field($_POST['current_password']) : '';
	$new_password     = isset($_POST['new_password']) ? sanitize_text_field($_POST['new_password']) : '';
	$confirm_password = isset($_POST['confirm_password']) ? sanitize_text_field($_POST['confirm_password']) : '';

	// Kiểm tra xác thực mật khẩu hiện tại
	if (!wp_check_password($current_password, $current_user->user_pass, $current_user->ID)) {
		wp_send_json_error(['message' => __('Mật khẩu hiện tại không đúng.', 'canhcamtheme'), 'status' => 'error']);
	}

	// Kiểm tra xác nhận mật khẩu
	if ($new_password !== $confirm_password) {
		wp_send_json_error(['message' => __('Mật khẩu xác nhận không khớp.', 'canhcamtheme'), 'status' => 'error']);
	}

	// Đổi mật khẩu
	wp_set_password($new_password, $current_user->ID);
	wp_send_json_success(['message' => __('Đổi mật khẩu thành công', 'canhcamtheme'), 'status' => 'success']);
	wp_logout(); // Đăng xuất user sau khi đổi
};


add_action('wp_ajax_ajax_lost_password_handle', 'ajax_lost_password_handle');
add_action('wp_ajax_nopriv_ajax_lost_password_handle', 'ajax_lost_password_handle');

function ajax_lost_password_handle()
{
	if (!check_ajax_referer('lost_password', 'nonce', false)) {
		wp_send_json_error([
			'message' => __('Xác thực không hợp lệ', 'canhcamtheme'),
			'status' => 'error'
		]);
	}
	$email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
	$user = get_user_by('email', $email);
	if (!$user) {
		wp_send_json_error([
			'message' => __('Không tìm thấy người dùng', 'canhcamtheme'),
			'status' => 'error'
		]);
	}
	$reset = retrieve_password($user->user_login);
	if ($reset === true) {
		wp_send_json_success([
			'message' => __('Liên kết đặt lại mật khẩu đã được gửi đến email của bạn.', 'canhcamtheme'),
			'status' => 'success'
		]);
	} else {
		wp_send_json_error([
			'message' => __('Lỗi trong quá trình gửi email. Vui lòng thử lại.', 'canhcamtheme'),
			'status' => 'error'
		]);
	}
}

add_action('wp_ajax_ajax_reset_password_handle', 'ajax_reset_password_handle');
add_action('wp_ajax_nopriv_ajax_reset_password_handle', 'ajax_reset_password_handle');

function ajax_reset_password_handle()
{
	if (!check_ajax_referer('reset_password', 'nonce', false)) {
		wp_send_json_error([
			'message' => __('Xác thực không hợp lệ', 'canhcamtheme'),
			'status' => 'error'
		]);
	}
	$login = isset($_POST['login']) ? sanitize_text_field($_POST['login']) : '';
	$key   = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
	$user  = check_password_reset_key($key, $login);
	if (is_wp_error($user)) {
		wp_send_json_error([
			'message' => $user->get_error_message(),
			'status' => 'error'
		]);
	}
	$password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
	$password_confirm = isset($_POST['password_confirm']) ? sanitize_text_field($_POST['password_confirm']) : '';
	if ($password !== $password_confirm) {
		wp_send_json_error([
			'message' => __('Mật khẩu không khớp', 'canhcamtheme'),
			'status' => 'error'
		]);
	}
	wp_set_password($password, $user->ID);
	wp_send_json_success([
		'message' => __('Đặt lại mật khẩu thành công', 'canhcamtheme'),
		'status' => 'success'
	]);
}


add_filter('retrieve_password_message', function ($message, $key, $user_login, $user_data) {
	$custom_reset_password_url = home_url('/dat-lai-mat-khau');

	$url = add_query_arg([
		'action' => 'rp',
		'key'    => $key,
		'login'  => rawurlencode($user_login)
	], $custom_reset_password_url);

	// Tạo nội dung HTML cho email
	$message  = '<div style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">';
	$message .= '<h2 style="color:#444;">Xin chào ' . esc_html($user_login) . ',</h2>';
	$message .= '<p>Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản của mình.</p>';
	$message .= '<p>Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu:</p>';
	$message .= '<p style="text-align:center; margin: 20px 0;">
                    <a href="' . esc_url($url) . '" 
                       style="background:#0073aa; color:#fff; padding:10px 20px; 
                              text-decoration:none; border-radius:4px; display:inline-block;">
                        Đặt lại mật khẩu
                    </a>
                 </p>';
	$message .= '<p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>';
	$message .= '</div>';

	return $message;
}, 10, 4);

// Đảm bảo gửi email dưới dạng HTML
add_filter('wp_mail_content_type', function () {
	return 'text/html';
});




// function block_wp_admin_for_subscribers()
// {
//     if (is_admin() && !current_user_can('edit_pages') && !(defined('DOING_AJAX') && DOING_AJAX)) {
//         wp_redirect(home_url());
//         exit;
//     }
// }
// add_action('init', 'block_wp_admin_for_subscribers');


function allow_subscriber_posting()
{
	$role = get_role('subscriber');
	if ($role && !$role->has_cap('edit_posts')) {
		$role->add_cap('edit_posts'); // Cho phép viết bài
	}
}
add_action('init', 'allow_subscriber_posting');



function custom_user_info_rewrite()
{
	$page_slug = 'tieu-su';

	if (function_exists('icl_get_languages')) {
		$langs = icl_get_languages('skip_missing=0');
		foreach ($langs as $lang) {
			$translated_page = apply_filters('wpml_translate_single_string', $page_slug, 'Pages', $page_slug, $lang['language_code']);
			add_rewrite_rule('^' . $translated_page . '/([0-9]+)/?$', 'index.php?pagename=' . $translated_page . '&user_id=$matches[1]', 'top');
		}
	} else {
		// Nếu không dùng WPML, cứ rewrite mặc định
		add_rewrite_rule('^' . $page_slug . '/([0-9]+)/?$', 'index.php?pagename=' . $page_slug . '&user_id=$matches[1]', 'top');
	}
}
add_action('init', 'custom_user_info_rewrite');

function add_user_id_query_var($vars)
{
	$vars[] = 'user_id';
	return $vars;
}
add_filter('query_vars', 'add_user_id_query_var');





// admin user 
// Thêm các trường meta cụ thể vào trang hồ sơ người dùng trong admin
add_action('show_user_profile', 'display_specific_user_meta_fields');
add_action('edit_user_profile', 'display_specific_user_meta_fields');

function display_specific_user_meta_fields($user)
{
?>
<h3><?php _e('Thông tin Người dùng', 'canhcamtheme'); ?></h3>
<table class="form-table">
    <?php
		// Danh sách các trường meta từ code bạn cung cấp
		$meta_fields = [
			'user_phone' => __('Số điện thoại', 'canhcamtheme'),
			'user_birth_date' => __('Ngày sinh', 'canhcamtheme'),
			'user_id_number' => __('Số CMND/CCCD', 'canhcamtheme'),
			'user_id_date' => __('Ngày cấp CMND/CCCD', 'canhcamtheme'),
			'user_id_place' => __('Nơi cấp CMND/CCCD', 'canhcamtheme'),
			'user_newsletter' => __('Đăng ký nhận bản tin', 'canhcamtheme'),
			'user_gender' => __('Giới tính', 'canhcamtheme'),
			'user_description' => __('Mô tả', 'canhcamtheme'),
			'user_province_current' => __('Tỉnh/Thành phố hiện tại', 'canhcamtheme'),
			'user_ward_current' => __('Phường/Xã hiện tại', 'canhcamtheme'),
			'user_district_current' => __('Quận/Huyện hiện tại', 'canhcamtheme'),
			'user_province_from' => __('Tỉnh/Thành phố quê quán', 'canhcamtheme'),
			'user_district_from' => __('Quận/Huyện quê quán', 'canhcamtheme'),
			'user_education' => __('Học vấn', 'canhcamtheme'),
			'user_social_facebook' => __('URL Facebook', 'canhcamtheme'),
			'user_social_instagram' => __('URL Instagram', 'canhcamtheme'),
			'user_social_tiktok' => __('URL TikTok', 'canhcamtheme'),
			'user_social_threads' => __('URL Threads', 'canhcamtheme'),
			'user_social_linkedin' => __('URL LinkedIn', 'canhcamtheme'),
			'user_social_twitter' => __('URL Twitter', 'canhcamtheme'),
			'user_social_contact' => __('URL Liên hệ', 'canhcamtheme'),
			'user_id_front' => __('Hình ảnh mặt trước CMND/CCCD', 'canhcamtheme'),
			'user_id_back' => __('Hình ảnh mặt sau CMND/CCCD', 'canhcamtheme'),
			'user_bio_image' => __('Hình ảnh tiểu sử', 'canhcamtheme'),
			'user_setting_followers' => __('Cài đặt người theo dõi', 'canhcamtheme'),
			'user_setting_following' => __('Cài đặt đang theo dõi', 'canhcamtheme'),

		];

		foreach ($meta_fields as $meta_key => $label) {
			$meta_value = get_user_meta($user->ID, $meta_key, true);
		?>
    <tr>
        <th><label><?php echo esc_html($label); ?></label></th>
        <td>
            <?php
					// Hiển thị hình ảnh cho các trường hình ảnh
					if (in_array($meta_key, ['user_id_front', 'user_id_back', 'user_bio_image']) && !empty($meta_value)) {
						echo '<img src="' . esc_url($meta_value) . '" style="max-width:150px; height:auto;" />';
					} else if (in_array($meta_key, ['user_setting_followers', 'user_setting_following'])) {
						echo '<input type="checkbox" name="' . esc_attr($meta_key) . '" id="' . esc_attr($meta_key) . '" value="1" ' . checked($meta_value, '1', false) . ' />';
					} else if ($meta_key == 'user_province_from' || $meta_key == 'user_district_from' || $meta_key == 'user_province_current' || $meta_key == 'user_district_current' || $meta_key == 'user_ward_current') {
						$province = get_province_vietnam($meta_value);
						$district = get_district_vietnam($meta_value);
						$ward = get_ward_vietnam($meta_value);
						if ($meta_value) {
							echo esc_html($province . ' ' . $district . ' ' . $ward);
						} else {
							echo __('Chưa thiết lập', 'canhcamtheme');
						}
					} else {
						// Hiển thị giá trị văn bản hoặc "Chưa thiết lập" nếu trống
						echo esc_html($meta_value ?: __('Chưa thiết lập', 'canhcamtheme'));
					}
					?>
        </td>
    </tr>
    <?php
		}
		?>
</table>
<?php
}
// Add custom user meta fields to user profile in admin (editable)
add_action('show_user_profile', 'display_custom_user_meta_fields');
add_action('edit_user_profile', 'display_custom_user_meta_fields');

function display_custom_user_meta_fields($user)
{
?>
<h3><?php _e('Additional User Information', 'canhcamtheme'); ?></h3>
<table class="form-table">
    <?php
		$meta_fields = [
			'user_phone' => ['label' => __('Phone Number', 'canhcamtheme'), 'type' => 'text'],
			'user_birth_date' => ['label' => __('Birth Date', 'canhcamtheme'), 'type' => 'date'],
			'user_id_number' => ['label' => __('ID Number', 'canhcamtheme'), 'type' => 'text'],
			'user_id_date' => ['label' => __('ID Issue Date', 'canhcamtheme'), 'type' => 'date'],
			'user_id_place' => ['label' => __('ID Issue Place', 'canhcamtheme'), 'type' => 'text'],
			'user_newsletter' => ['label' => __('Newsletter Subscription', 'canhcamtheme'), 'type' => 'checkbox'],
			'_account_status' => ['label' => __('Account Status', 'canhcamtheme'), 'type' => 'select', 'options' => ['pending' => 'Pending', 'active' => 'Active', 'blocked' => 'Blocked']],
			'user_gender' => ['label' => __('Gender', 'canhcamtheme'), 'type' => 'select', 'options' => ['male' => 'Male', 'female' => 'Female', 'other' => 'Other']],
			'user_description' => ['label' => __('Description', 'canhcamtheme'), 'type' => 'textarea'],
			// Add other fields as needed
			// '_first_login' => ['label' => __('First Login Status', 'canhcamtheme'), 'type' => 'number'],
			// '_account_status' => ['label' => __('Account Status', 'canhcamtheme'), 'type' => 'number'],
		];

		foreach ($meta_fields as $meta_key => $field) {
			$meta_value = get_user_meta($user->ID, $meta_key, true);
		?>
    <tr>
        <th><label for="<?php echo esc_attr($meta_key); ?>"><?php echo esc_html($field['label']); ?></label></th>
        <td>
            <?php
					switch ($field['type']) {
						case 'text':
						case 'date':
						case 'number':
					?>
            <input type="<?php echo esc_attr($field['type']); ?>" name="<?php echo esc_attr($meta_key); ?>"
                id="<?php echo esc_attr($meta_key); ?>" value="<?php echo esc_attr($meta_value); ?>"
                class="regular-text" />
            <?php
							break;
						case 'checkbox':
						?>
            <input type="checkbox" name="<?php echo esc_attr($meta_key); ?>" id="<?php echo esc_attr($meta_key); ?>"
                <?php checked($meta_value, '1'); ?> value="1" />
            <?php
							break;
						case 'select':
						?>
            <select name="<?php echo esc_attr($meta_key); ?>" id="<?php echo esc_attr($meta_key); ?>">
                <?php foreach ($field['options'] as $value => $label) { ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($meta_value, $value); ?>>
                    <?php echo esc_html($label); ?></option>
                <?php } ?>
            </select>
            <?php
							break;
						case 'textarea':
						?>
            <textarea name="<?php echo esc_attr($meta_key); ?>" id="<?php echo esc_attr($meta_key); ?>" rows="5"
                cols="30"><?php echo esc_textarea($meta_value); ?></textarea>
            <?php
							break;
					}
					?>
        </td>
    </tr>
    <?php
		}
		?>
</table>
<?php
}

// Save custom user meta fields
add_action('personal_options_update', 'save_custom_user_meta_fields');
add_action('edit_user_profile_update', 'save_custom_user_meta_fields');

function save_custom_user_meta_fields($user_id)
{
	if (!current_user_can('edit_user', $user_id)) {
		return;
	}

	$meta_fields = [
		'user_phone',
		'user_birth_date',
		'user_id_number',
		'user_id_date',
		'user_id_place',
		'user_newsletter',
		'user_gender',
		'user_description',
		'_first_login',
		'_account_status',
		// Add other fields as needed
	];

	foreach ($meta_fields as $meta_key) {
		if (isset($_POST[$meta_key])) {
			update_user_meta($user_id, $meta_key, sanitize_text_field($_POST[$meta_key]));
		} else {
			// Handle checkboxes (if unchecked, remove meta)
			if (in_array($meta_key, ['user_newsletter'])) {
				delete_user_meta($user_id, $meta_key);
			}
			if ($meta_key === '_account_status') {
				update_user_meta($user_id, '_account_status', sanitize_text_field($_POST[$meta_key]));
				if (sanitize_text_field($_POST[$meta_key]) == 'blocked') {
					if (function_exists('wp_destroy_user_sessions')) {
						wp_destroy_user_sessions($user_id);
					}
					wp_logout();
				}
			}
		}
	}
}


// Ajax 


function create_user_follow_table_once()
{
	global $wpdb;


	if (get_option('user_follow_table_created')) {
		return;
	}

	$table_name = $wpdb->prefix . 'user_follows';
	$charset_collate = $wpdb->get_charset_collate();

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE $table_name (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		follower_id BIGINT(20) UNSIGNED NOT NULL,
		followed_id BIGINT(20) UNSIGNED NOT NULL,
		created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY follower_id (follower_id),
		KEY followed_id (followed_id)
	) $charset_collate;";

	dbDelta($sql);

	add_option('user_follow_table_created', 1);
}
add_action('after_setup_theme', 'create_user_follow_table_once');


function follow_user($follower_id, $followed_id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'user_follows';
	$check_follow = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE follower_id = %d AND followed_id = %d", $follower_id, $followed_id));
	if ($check_follow) {
		return false;
	}
	$wpdb->insert($table_name, array(
		'follower_id' => $follower_id,
		'followed_id' => $followed_id,
	));
	return true;
}

function unfollow_user($follower_id, $followed_id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'user_follows';
	$wpdb->delete($table_name, array(
		'follower_id' => $follower_id,
		'followed_id' => $followed_id,
	));
}

function get_follower_count($user_id)
{
	global $wpdb;
	$table = $wpdb->prefix . 'user_follows';
	return $wpdb->get_var($wpdb->prepare("
		SELECT COUNT(*) FROM $table WHERE followed_id = %d
	", $user_id));
	return format_number($count);
}

function get_following_count($user_id)
{
	global $wpdb;
	$table = $wpdb->prefix . 'user_follows';
	$count = $wpdb->get_var($wpdb->prepare("
		SELECT COUNT(*) FROM $table WHERE follower_id = %d
	", $user_id));

	return format_number($count);
}


function check_user_follow($follower_id, $followed_id)
{
	global $wpdb;
	$table = $wpdb->prefix . 'user_follows';
	$check_follow = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE follower_id = %d AND followed_id = %d", $follower_id, $followed_id));
	if ($check_follow) {
		return true;
	}
	return false;
}

// 

function ajax_follow_user()
{
	if (!check_ajax_referer('follow_user', 'nonce', false)) {
		wp_send_json_error([
			'message' => __('Xác thực không hợp lệ', 'canhcamtheme'),
			'status' => 'error'
		]);
	}

	$follower_id = isset($_POST['follower_id']) ? intval($_POST['follower_id']) : 0;
	$followed_id = isset($_POST['followed_id']) ? intval($_POST['followed_id']) : 0;
	if ($follower_id == 0 || $followed_id == 0) {
		wp_send_json_error(array('status' => 'error', 'message' => __('Lỗi khi theo dõi', 'canhcamtheme')));
	}

	$check_follow = check_user_follow($follower_id, $followed_id);


	if ($check_follow) {
		unfollow_user($follower_id, $followed_id);
		$get_follower_count = get_follower_count($followed_id);
		$get_following_count = get_following_count($follower_id);
		wp_send_json_success(array('status' => 'success', 'text' => 'Theo dõi', 'message' => __('Đã hủy theo dõi', 'canhcamtheme'), 'follower_count' => $get_follower_count, 'following_count' => $get_following_count));
	} else {
		follow_user($follower_id, $followed_id);
		$get_follower_count = get_follower_count($followed_id);
		$get_following_count = get_following_count($follower_id);
		wp_send_json_success(array('status' => 'success', 'text' => 'Hủy theo dõi', 'message' => __('Đã theo dõi', 'canhcamtheme'), 'follower_count' => $get_follower_count, 'following_count' => $get_following_count));
	}
	wp_send_json_error(__('Lỗi khi theo dõi', 'canhcamtheme'));
}
add_action('wp_ajax_ajax_follow_user', 'ajax_follow_user');
add_action('wp_ajax_nopriv_ajax_follow_user', 'ajax_follow_user');


add_filter('wp_nav_menu_objects', function ($items, $args) {
	$category_id = 18;
	$category_link = get_category_link($category_id);

	if (!is_user_logged_in()) {
		foreach ($items as $key => $item) {
			if ($item->url === $category_link) {
				unset($items[$key]);
			}
		}
	}

	return $items;
}, 10, 2);

add_action('init', function () {
	if (is_admin() && !defined('DOING_AJAX') && current_user_can('subscriber')) {
		wp_redirect(home_url());
		exit;
	}
});


// Chỉ cho phép Subscriber upload hình ảnh
function limit_subscriber_uploads($mime_types)
{
	// Nếu user hiện tại là Subscriber
	if (current_user_can('subscriber')) {
		return array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png'          => 'image/png',
			'gif'          => 'image/gif',
			'webp'         => 'image/webp'
		);
	}

	// Các role khác thì vẫn giữ nguyên
	return $mime_types;
}
add_filter('upload_mimes', 'limit_subscriber_uploads');


// add_action('init', function () {
// 	if (!current_user_can('manage_options')) return; // chỉ cho admin

// 	$users = get_users(['fields' => ['ID']]);

// 	foreach ($users as $user) {
// 		update_user_meta($user->ID, 'user_setting_followers', 'show');
// 		update_user_meta($user->ID, 'user_setting_following', 'show');
// 	}

// 	echo 'Đã update ' . count($users) . ' users.';
// 	exit;
// });