<?php
/**
 * Guided customer address fields for Iran.
 *
 * @package Digi_Zoghal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function digi_zoghal_get_iran_locations() {
	static $locations = null;
	if ( null !== $locations ) {
		return $locations;
	}

	$path = get_template_directory() . '/assets/data/iran-locations.json';
	if ( ! is_readable( $path ) ) {
		$locations = array( 'provinces' => array(), 'counties' => array(), 'cities' => array() );
		return $locations;
	}

	$decoded   = json_decode( file_get_contents( $path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$locations = is_array( $decoded ) ? $decoded : array( 'provinces' => array(), 'counties' => array(), 'cities' => array() );
	return $locations;
}

function digi_zoghal_enqueue_address_assets() {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url( 'edit-address' ) ) {
		return;
	}

	wp_enqueue_script(
		'digi-zoghal-address',
		get_template_directory_uri() . '/assets/account-address.js',
		array(),
		DIGI_ZOGHAL_VERSION,
		true
	);

	wp_localize_script(
		'digi-zoghal-address',
		'digiZoghalIranLocations',
		array_merge(
			digi_zoghal_get_iran_locations(),
			array(
				'selectedCounty' => get_user_meta( get_current_user_id(), 'billing_county', true ),
				'selectedCity'   => get_user_meta( get_current_user_id(), 'billing_city', true ),
			)
		)
	);
}
add_action( 'wp_enqueue_scripts', 'digi_zoghal_enqueue_address_assets', 20 );

function digi_zoghal_billing_address_fields( $fields ) {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return $fields;
	}

	unset( $fields['billing_company'] );

	$fields['billing_country']['label']    = __( 'کشور', 'digi-zoghal' );
	$fields['billing_country']['priority'] = 40;
	$fields['billing_country']['default']  = 'IR';
	$fields['billing_country']['type']     = 'select';
	$fields['billing_country']['options']  = array( 'IR' => __( 'ایران', 'digi-zoghal' ) );
	$fields['billing_state']['label']      = __( 'استان', 'digi-zoghal' );
	$fields['billing_state']['priority']   = 50;
	$fields['billing_state']['required']   = true;

	$current_county = get_user_meta( get_current_user_id(), 'billing_county', true );
	$current_city   = get_user_meta( get_current_user_id(), 'billing_city', true );
	$county_options = array( '' => __( 'ابتدا استان را انتخاب کنید', 'digi-zoghal' ) );
	$city_options   = array( '' => __( 'ابتدا شهرستان را انتخاب کنید', 'digi-zoghal' ) );
	if ( $current_county ) {
		$county_options[ $current_county ] = $current_county;
	}
	if ( $current_city ) {
		$city_options[ $current_city ] = $current_city;
	}

	$fields['billing_county'] = array(
		'type'     => 'select',
		'label'    => __( 'شهرستان', 'digi-zoghal' ),
		'required' => true,
		'priority' => 60,
		'options'  => $county_options,
		'class'    => array( 'form-row-wide' ),
	);
	$fields['billing_city']['type']        = 'select';
	$fields['billing_city']['label']       = __( 'شهر', 'digi-zoghal' );
	$fields['billing_city']['required']    = true;
	$fields['billing_city']['priority']    = 70;
	$fields['billing_city']['options']     = $city_options;
	$fields['billing_address_1']['label']  = __( 'خیابان و نشانی اصلی', 'digi-zoghal' );
	$fields['billing_address_1']['placeholder'] = __( 'مثلاً خیابان، بلوار، محله و کوچه', 'digi-zoghal' );
	$fields['billing_address_1']['priority']    = 80;
	$fields['billing_address_2']['label']       = __( 'جزئیات تکمیلی', 'digi-zoghal' );
	$fields['billing_address_2']['placeholder'] = __( 'طبقه، واحد، نام ساختمان یا توضیح لازم', 'digi-zoghal' );
	$fields['billing_address_2']['priority']    = 90;
	$fields['billing_house_number'] = array(
		'type'              => 'text',
		'label'             => __( 'پلاک', 'digi-zoghal' ),
		'required'          => true,
		'priority'          => 100,
		'class'             => array( 'form-row-first' ),
		'custom_attributes' => array( 'inputmode' => 'numeric', 'maxlength' => '10' ),
	);
	$fields['billing_postcode']['label']             = __( 'کدپستی ۱۰ رقمی', 'digi-zoghal' );
	$fields['billing_postcode']['required']          = true;
	$fields['billing_postcode']['priority']          = 110;
	$fields['billing_postcode']['class']             = array( 'form-row-last' );
	$fields['billing_postcode']['custom_attributes'] = array( 'inputmode' => 'numeric', 'maxlength' => '10', 'pattern' => '[0-9۰-۹٠-٩]{10}' );
	$fields['billing_phone']['label']                 = __( 'شماره تماس گیرنده', 'digi-zoghal' );
	$fields['billing_phone']['required']              = true;
	$fields['billing_phone']['priority']              = 120;

	return $fields;
}
add_filter( 'woocommerce_billing_fields', 'digi_zoghal_billing_address_fields', 20 );

function digi_zoghal_validate_saved_address( $user_id, $address_type, $address = array(), $customer = null ) {
	if ( 'billing' !== $address_type ) {
		return;
	}

	$county      = isset( $_POST['billing_county'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_county'] ) ) : '';
	$city        = isset( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '';
	$house       = isset( $_POST['billing_house_number'] ) ? digi_zoghal_normalize_digits( wp_unslash( $_POST['billing_house_number'] ) ) : '';
	$postcode    = isset( $_POST['billing_postcode'] ) ? digi_zoghal_normalize_digits( wp_unslash( $_POST['billing_postcode'] ) ) : '';

	if ( '' === $county || '' === $city ) {
		wc_add_notice( __( 'استان، شهرستان و شهر را کامل انتخاب کنید.', 'digi-zoghal' ), 'error' );
	}
	if ( '' === $house ) {
		wc_add_notice( __( 'پلاک را وارد کنید.', 'digi-zoghal' ), 'error' );
	}
	if ( ! preg_match( '/^\d{10}$/', $postcode ) ) {
		wc_add_notice( __( 'کدپستی باید دقیقاً ۱۰ رقم باشد.', 'digi-zoghal' ), 'error' );
	}
}
add_action( 'woocommerce_after_save_address_validation', 'digi_zoghal_validate_saved_address', 10, 4 );

function digi_zoghal_save_custom_address_fields( $user_id, $address_type ) {
	if ( 'billing' !== $address_type ) {
		return;
	}

	if ( isset( $_POST['billing_county'] ) ) {
		update_user_meta( $user_id, 'billing_county', sanitize_text_field( wp_unslash( $_POST['billing_county'] ) ) );
	}
	if ( isset( $_POST['billing_house_number'] ) ) {
		update_user_meta( $user_id, 'billing_house_number', digi_zoghal_normalize_digits( wp_unslash( $_POST['billing_house_number'] ) ) );
	}
	if ( isset( $_POST['billing_postcode'] ) ) {
		update_user_meta( $user_id, 'billing_postcode', digi_zoghal_normalize_digits( wp_unslash( $_POST['billing_postcode'] ) ) );
	}
}
add_action( 'woocommerce_customer_save_address', 'digi_zoghal_save_custom_address_fields', 10, 2 );

function digi_zoghal_customer_has_complete_address( $user_id ) {
	$required = array( 'billing_state', 'billing_county', 'billing_city', 'billing_address_1', 'billing_house_number', 'billing_postcode' );
	foreach ( $required as $meta_key ) {
		if ( '' === trim( (string) get_user_meta( $user_id, $meta_key, true ) ) ) {
			return false;
		}
	}

	return true;
}

function digi_zoghal_address_dashboard_guide() {
	$user_id = get_current_user_id();
	if ( ! $user_id || digi_zoghal_customer_has_complete_address( $user_id ) ) {
		return;
	}

	$url = function_exists( 'wc_get_endpoint_url' ) ? wc_get_endpoint_url( 'edit-address', 'billing', wc_get_page_permalink( 'myaccount' ) ) : '#';
	?>
	<section class="dz-address-guide" aria-labelledby="dz-address-guide-title">
		<div>
			<span class="dz-address-guide-badge"><?php esc_html_e( 'قدم بعدی', 'digi-zoghal' ); ?></span>
			<h2 id="dz-address-guide-title"><?php esc_html_e( 'آدرس تحویل را کامل کنید', 'digi-zoghal' ); ?></h2>
			<p><?php esc_html_e( 'برای سریع‌ترشدن خرید، استان، شهرستان، شهر، کدپستی، نشانی و پلاک را یک‌بار ثبت کنید.', 'digi-zoghal' ); ?></p>
		</div>
		<a class="button" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'ثبت آدرس', 'digi-zoghal' ); ?></a>
	</section>
	<?php
}
add_action( 'woocommerce_account_dashboard', 'digi_zoghal_address_dashboard_guide', 5 );
