<?php
/**
 * Faraz SMS / IPPanel OTP integration.
 *
 * @package Digi_Zoghal
 */

defined( 'ABSPATH' ) || exit;

function digi_zoghal_sms_get_settings() {
	$settings = get_option( 'digi_zoghal_sms_settings', array() );

	return wp_parse_args(
		is_array( $settings ) ? $settings : array(),
		array(
			'enabled'      => false,
			'api_key'      => '',
			'pattern_code' => '',
			'sender'       => '',
		)
	);
}

function digi_zoghal_sms_is_configured() {
	$settings = digi_zoghal_sms_get_settings();

	return ! empty( $settings['enabled'] )
		&& ! empty( $settings['api_key'] )
		&& ! empty( $settings['pattern_code'] )
		&& ! empty( $settings['sender'] );
}

function digi_zoghal_sms_normalize_sender( $sender ) {
	$sender = preg_replace( '/\D+/', '', (string) $sender );

	if ( 0 === strpos( $sender, '0098' ) ) {
		$sender = substr( $sender, 4 );
	} elseif ( 0 === strpos( $sender, '98' ) ) {
		$sender = substr( $sender, 2 );
	}

	return ltrim( $sender, '0' );
}

function digi_zoghal_sms_sanitize_settings( $input ) {
	$current      = digi_zoghal_sms_get_settings();
	$input        = is_array( $input ) ? $input : array();
	$new_api_key  = isset( $input['api_key'] ) ? trim( sanitize_text_field( wp_unslash( $input['api_key'] ) ) ) : '';
	$clear_key    = ! empty( $input['clear_api_key'] );
	$settings     = array(
		'enabled'      => ! empty( $input['enabled'] ),
		'api_key'      => $clear_key ? '' : ( $new_api_key ? $new_api_key : $current['api_key'] ),
		'pattern_code' => isset( $input['pattern_code'] ) ? sanitize_text_field( wp_unslash( $input['pattern_code'] ) ) : '',
		'sender'       => isset( $input['sender'] ) ? digi_zoghal_sms_normalize_sender( wp_unslash( $input['sender'] ) ) : '',
	);

	if ( $settings['enabled'] && ( empty( $settings['api_key'] ) || empty( $settings['pattern_code'] ) || empty( $settings['sender'] ) ) ) {
		$settings['enabled'] = false;
		add_settings_error(
			'digi_zoghal_sms_settings',
			'digi_zoghal_sms_incomplete',
			__( 'ورود پیامکی فعال نشد؛ کلید دسترسی، کد پترن و خط ارسال‌کننده باید تکمیل شوند.', 'digi-zoghal' ),
			'error'
		);
	}

	return $settings;
}

function digi_zoghal_sms_register_settings() {
	register_setting(
		'digi_zoghal_sms_group',
		'digi_zoghal_sms_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'digi_zoghal_sms_sanitize_settings',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'digi_zoghal_sms_register_settings' );

function digi_zoghal_sms_add_settings_page() {
	add_options_page(
		__( 'ورود پیامکی دیجی زغال', 'digi-zoghal' ),
		__( 'ورود پیامکی', 'digi-zoghal' ),
		'manage_options',
		'digi-zoghal-sms',
		'digi_zoghal_sms_render_settings_page'
	);
}
add_action( 'admin_menu', 'digi_zoghal_sms_add_settings_page' );

function digi_zoghal_sms_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = digi_zoghal_sms_get_settings();
	$key_hint = $settings['api_key'] ? substr( $settings['api_key'], -4 ) : '';
	?>
	<div class="wrap" dir="rtl">
		<h1><?php esc_html_e( 'ورود پیامکی دیجی زغال', 'digi-zoghal' ); ?></h1>
		<p><?php esc_html_e( 'اطلاعات اتصال فراز اس‌ام‌اس را اینجا وارد کنید. کلید دسترسی در صفحه عمومی سایت نمایش داده نمی‌شود.', 'digi-zoghal' ); ?></p>
		<?php settings_errors( 'digi_zoghal_sms_settings' ); ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'digi_zoghal_sms_group' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'فعال‌سازی ورود پیامکی', 'digi-zoghal' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="digi_zoghal_sms_settings[enabled]" value="1" <?php checked( ! empty( $settings['enabled'] ) ); ?>>
							<?php esc_html_e( 'ارسال و بررسی کد یکبارمصرف فعال باشد', 'digi-zoghal' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="dz-sms-api-key"><?php esc_html_e( 'کلید دسترسی API', 'digi-zoghal' ); ?></label></th>
					<td>
						<input id="dz-sms-api-key" class="regular-text" type="password" name="digi_zoghal_sms_settings[api_key]" value="" autocomplete="new-password" placeholder="<?php echo esc_attr( $key_hint ? sprintf( __( 'ذخیره شده؛ پایان کلید: %s', 'digi-zoghal' ), $key_hint ) : __( 'کلید جدید را وارد کنید', 'digi-zoghal' ) ); ?>">
						<p class="description"><?php esc_html_e( 'برای نگه‌داشتن کلید فعلی، این کادر را خالی بگذارید.', 'digi-zoghal' ); ?></p>
						<?php if ( $settings['api_key'] ) : ?>
							<label><input type="checkbox" name="digi_zoghal_sms_settings[clear_api_key]" value="1"> <?php esc_html_e( 'حذف کلید ذخیره‌شده', 'digi-zoghal' ); ?></label>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="dz-sms-pattern"><?php esc_html_e( 'کد پترن', 'digi-zoghal' ); ?></label></th>
					<td><input id="dz-sms-pattern" class="regular-text" type="text" name="digi_zoghal_sms_settings[pattern_code]" value="<?php echo esc_attr( $settings['pattern_code'] ); ?>" autocomplete="off"></td>
				</tr>
				<tr>
					<th scope="row"><label for="dz-sms-sender"><?php esc_html_e( 'خط ارسال‌کننده', 'digi-zoghal' ); ?></label></th>
					<td>
						<input id="dz-sms-sender" class="regular-text" type="text" name="digi_zoghal_sms_settings[sender]" value="<?php echo esc_attr( $settings['sender'] ); ?>" inputmode="numeric" placeholder="90008361">
						<p class="description"><?php esc_html_e( 'شماره خط خدماتی اشتراکی فعال در پنل فراز اس‌ام‌اس را وارد کنید.', 'digi-zoghal' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'ذخیره تنظیمات پیامک', 'digi-zoghal' ) ); ?>
		</form>
	</div>
	<?php
}

function digi_zoghal_otp_transient_key( $purpose, $value ) {
	$digest = hash_hmac( 'sha256', (string) $value, wp_salt( 'auth' ) );
	return 'dz_' . sanitize_key( $purpose ) . '_' . substr( $digest, 0, 32 );
}

function digi_zoghal_otp_client_ip() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
}

function digi_zoghal_otp_hit_rate_limit( $scope, $identity, $limit, $ttl ) {
	$key   = digi_zoghal_otp_transient_key( 'rate_' . $scope, $identity );
	$count = (int) get_transient( $key );

	if ( $count >= $limit ) {
		return false;
	}

	set_transient( $key, $count + 1, $ttl );
	return true;
}

function digi_zoghal_sms_send_pattern( $phone, $otp_code ) {
	$settings = digi_zoghal_sms_get_settings();
	$sender   = digi_zoghal_sms_normalize_sender( $settings['sender'] );
	$payload  = array(
		'code'          => $settings['pattern_code'],
		'attributes'    => array(
			'code' => (string) $otp_code,
		),
		'recipient'     => $phone,
		'line_number'   => $sender,
		'number_format' => 'english',
	);

	$response = wp_remote_post(
		'https://api.iranpayamak.com/ws/v1/sms/pattern',
		array(
			'timeout' => 15,
			'headers' => array(
				'Api-Key'      => $settings['api_key'],
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
			),
			'body'    => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'dz_sms_connection', __( 'ارتباط با سرویس پیامک برقرار نشد. کمی بعد دوباره تلاش کنید.', 'digi-zoghal' ) );
	}

	$status_code = (int) wp_remote_retrieve_response_code( $response );
	$body        = json_decode( wp_remote_retrieve_body( $response ), true );
	$is_success  = $status_code >= 200
		&& $status_code < 300
		&& is_array( $body )
		&& isset( $body['status'] )
		&& 'success' === strtolower( (string) $body['status'] );

	if ( ! $is_success ) {
		return new WP_Error( 'dz_sms_rejected', __( 'ارسال پیامک انجام نشد. تنظیمات پیامک یا اعتبار پنل را بررسی کنید.', 'digi-zoghal' ) );
	}

	return true;
}

function digi_zoghal_ajax_send_otp() {
	check_ajax_referer( 'digi_zoghal_otp', 'nonce' );

	if ( ! digi_zoghal_sms_is_configured() ) {
		wp_send_json_error( array( 'message' => __( 'ورود پیامکی هنوز فعال نشده است.', 'digi-zoghal' ) ), 503 );
	}

	$phone = isset( $_POST['phone'] ) ? digi_zoghal_normalize_phone( wp_unslash( $_POST['phone'] ) ) : '';
	if ( ! preg_match( '/^09\d{9}$/', $phone ) ) {
		wp_send_json_error( array( 'message' => __( 'شماره موبایل را به‌صورت ۰۹xxxxxxxxx وارد کنید.', 'digi-zoghal' ) ), 400 );
	}

	$cooldown_key = digi_zoghal_otp_transient_key( 'cooldown', $phone );
	if ( get_transient( $cooldown_key ) ) {
		wp_send_json_error( array( 'message' => __( 'کد قبلی ارسال شده است؛ کمی صبر کنید.', 'digi-zoghal' ) ), 429 );
	}

	$phone_allowed = digi_zoghal_otp_hit_rate_limit( 'phone', $phone, 3, 15 * MINUTE_IN_SECONDS );
	$ip_allowed    = digi_zoghal_otp_hit_rate_limit( 'ip', digi_zoghal_otp_client_ip(), 12, 15 * MINUTE_IN_SECONDS );
	if ( ! $phone_allowed || ! $ip_allowed ) {
		wp_send_json_error( array( 'message' => __( 'سه درخواست پیاپی ثبت شده است. ۱۵ دقیقه صبر کنید و دوباره تلاش کنید.', 'digi-zoghal' ) ), 429 );
	}

	set_transient( $cooldown_key, 1, 2 * MINUTE_IN_SECONDS );
	$otp_code = (string) random_int( 100000, 999999 );
	$sent     = digi_zoghal_sms_send_pattern( $phone, $otp_code );

	if ( is_wp_error( $sent ) ) {
		delete_transient( $cooldown_key );
		wp_send_json_error( array( 'message' => $sent->get_error_message() ), 502 );
	}

	set_transient(
		digi_zoghal_otp_transient_key( 'otp', $phone ),
		array(
			'hash'     => wp_hash_password( $otp_code ),
			'attempts' => 0,
			'expires'  => time() + ( 2 * MINUTE_IN_SECONDS ),
		),
		2 * MINUTE_IN_SECONDS
	);

	wp_send_json_success(
		array(
			'message'  => __( 'کد تأیید برای شما پیامک شد.', 'digi-zoghal' ),
			'cooldown' => 2 * MINUTE_IN_SECONDS,
			'expires'  => 2 * MINUTE_IN_SECONDS,
		)
	);
}
add_action( 'wp_ajax_nopriv_digi_zoghal_send_otp', 'digi_zoghal_ajax_send_otp' );

/**
 * Validate a one-time code without consuming it. The caller consumes it only
 * after the requested action (login or account creation) succeeds.
 */
function digi_zoghal_otp_validate_code( $phone, $code ) {
	$phone = digi_zoghal_normalize_phone( $phone );
	$code  = digi_zoghal_normalize_phone( $code );

	if ( ! preg_match( '/^09\d{9}$/', $phone ) || ! preg_match( '/^\d{6}$/', $code ) ) {
		return new WP_Error( 'dz_otp_invalid', __( 'شماره موبایل یا کد واردشده صحیح نیست.', 'digi-zoghal' ) );
	}

	$otp_key = digi_zoghal_otp_transient_key( 'otp', $phone );
	$record  = get_transient( $otp_key );
	if ( ! is_array( $record ) || empty( $record['hash'] ) || empty( $record['expires'] ) || time() > (int) $record['expires'] ) {
		delete_transient( $otp_key );
		return new WP_Error( 'dz_otp_expired', __( 'کد منقضی شده است؛ کد تازه‌ای دریافت کنید.', 'digi-zoghal' ) );
	}

	$record['attempts'] = isset( $record['attempts'] ) ? (int) $record['attempts'] + 1 : 1;
	if ( $record['attempts'] > 5 ) {
		delete_transient( $otp_key );
		return new WP_Error( 'dz_otp_attempts', __( 'تعداد تلاش‌ها بیش از حد مجاز بود؛ کد تازه‌ای دریافت کنید.', 'digi-zoghal' ) );
	}

	set_transient( $otp_key, $record, max( 1, (int) $record['expires'] - time() ) );
	if ( ! wp_check_password( $code, $record['hash'] ) ) {
		return new WP_Error( 'dz_otp_incorrect', __( 'کد واردشده صحیح نیست.', 'digi-zoghal' ) );
	}

	return true;
}

function digi_zoghal_otp_get_customer( $phone ) {
	$user = get_user_by( 'login', $phone );
	if ( ! $user ) {
		$user = digi_zoghal_get_user_by_phone( $phone );
	}

	if ( ! $user ) {
		return false;
	}

	if ( user_can( $user, 'manage_options' ) || user_can( $user, 'manage_woocommerce' ) ) {
		return new WP_Error( 'dz_otp_privileged', __( 'برای این حساب از ورود با رمز عبور استفاده کنید.', 'digi-zoghal' ) );
	}

	return $user;
}

function digi_zoghal_otp_create_registration_token( $phone ) {
	try {
		$token = bin2hex( random_bytes( 32 ) );
	} catch ( Exception $exception ) {
		return new WP_Error( 'dz_registration_token', __( 'شروع ثبت‌نام ممکن نشد. دوباره کد دریافت کنید.', 'digi-zoghal' ) );
	}

	set_transient(
		digi_zoghal_otp_transient_key( 'registration', $token ),
		array(
			'phone'   => digi_zoghal_normalize_phone( $phone ),
			'expires' => time() + ( 10 * MINUTE_IN_SECONDS ),
		),
		10 * MINUTE_IN_SECONDS
	);

	return $token;
}

function digi_zoghal_otp_validate_registration_token( $phone, $token ) {
	$phone = digi_zoghal_normalize_phone( $phone );
	$token = sanitize_text_field( (string) $token );
	if ( ! preg_match( '/^[a-f0-9]{64}$/', $token ) ) {
		return new WP_Error( 'dz_registration_token_invalid', __( 'تأیید شماره کامل نشده است. دوباره کد دریافت کنید.', 'digi-zoghal' ) );
	}

	$record = get_transient( digi_zoghal_otp_transient_key( 'registration', $token ) );
	if ( ! is_array( $record ) || empty( $record['phone'] ) || empty( $record['expires'] ) || time() > (int) $record['expires'] ) {
		return new WP_Error( 'dz_registration_token_expired', __( 'زمان تکمیل ثبت‌نام تمام شده است. دوباره کد دریافت کنید.', 'digi-zoghal' ) );
	}

	if ( ! hash_equals( (string) $record['phone'], $phone ) ) {
		return new WP_Error( 'dz_registration_token_phone', __( 'شماره تأییدشده با شماره ثبت‌نام یکسان نیست.', 'digi-zoghal' ) );
	}

	return true;
}

function digi_zoghal_otp_consume_registration_token( $token ) {
	$token = sanitize_text_field( (string) $token );
	if ( preg_match( '/^[a-f0-9]{64}$/', $token ) ) {
		delete_transient( digi_zoghal_otp_transient_key( 'registration', $token ) );
	}
}

function digi_zoghal_ajax_verify_otp() {
	check_ajax_referer( 'digi_zoghal_otp', 'nonce' );

	$phone = isset( $_POST['phone'] ) ? digi_zoghal_normalize_phone( wp_unslash( $_POST['phone'] ) ) : '';
	$code  = isset( $_POST['code'] ) ? digi_zoghal_normalize_phone( wp_unslash( $_POST['code'] ) ) : '';
	if ( ! preg_match( '/^09\d{9}$/', $phone ) || ! preg_match( '/^\d{6}$/', $code ) ) {
		wp_send_json_error( array( 'message' => __( 'شماره موبایل یا کد واردشده صحیح نیست.', 'digi-zoghal' ) ), 400 );
	}

	$validation = digi_zoghal_otp_validate_code( $phone, $code );
	if ( is_wp_error( $validation ) ) {
		$status_code = 'dz_otp_expired' === $validation->get_error_code() ? 410 : 400;
		if ( 'dz_otp_attempts' === $validation->get_error_code() ) {
			$status_code = 429;
		}
		wp_send_json_error( array( 'message' => $validation->get_error_message() ), $status_code );
	}

	$user = digi_zoghal_otp_get_customer( $phone );
	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array( 'message' => $user->get_error_message() ), 403 );
	}

	if ( ! $user ) {
		$registration_token = digi_zoghal_otp_create_registration_token( $phone );
		if ( is_wp_error( $registration_token ) ) {
			wp_send_json_error( array( 'message' => $registration_token->get_error_message() ), 500 );
		}

		delete_transient( digi_zoghal_otp_transient_key( 'otp', $phone ) );
		delete_transient( digi_zoghal_otp_transient_key( 'cooldown', $phone ) );
		wp_send_json_success(
			array(
				'state'              => 'register',
				'phone'              => $phone,
				'registrationToken'  => $registration_token,
				'message'            => __( 'شماره تأیید شد. اطلاعات حساب را یک‌بار تکمیل کنید.', 'digi-zoghal' ),
			)
		);
	}

	delete_transient( digi_zoghal_otp_transient_key( 'otp', $phone ) );
	delete_transient( digi_zoghal_otp_transient_key( 'cooldown', $phone ) );
	wp_clear_auth_cookie();
	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID, true, is_ssl() );
	do_action( 'wp_login', $user->user_login, $user );

	wp_send_json_success(
		array(
			'state'    => 'login',
			'message'  => __( 'ورود با موفقیت انجام شد.', 'digi-zoghal' ),
			'redirect' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ),
		)
	);
}
add_action( 'wp_ajax_nopriv_digi_zoghal_verify_otp', 'digi_zoghal_ajax_verify_otp' );
