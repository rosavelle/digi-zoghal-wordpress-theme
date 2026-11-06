<?php
/**
 * Consultation requests for industrial equipment.
 */

defined( 'ABSPATH' ) || exit;

function dz_is_industrial( $product_id ) {
	return has_term( array( 'industrial-equipment', 'ماشین‌آلات صنعتی', 'furnaces', 'انواع کوره' ), 'product_cat', $product_id );
}

add_filter( 'woocommerce_is_purchasable', function ( $purchasable, $product ) {
	return $purchasable && ! dz_is_industrial( $product->get_id() );
}, 20, 2 );

add_filter( 'woocommerce_get_price_html', function ( $price, $product ) {
	return dz_is_industrial( $product->get_id() ) ? 'استعلام قیمت' : $price;
}, 30, 2 );

add_action( 'init', function () {
	register_post_type( 'dz_quote', array(
		'labels' => array( 'name' => 'استعلام دستگاه‌ها', 'singular_name' => 'استعلام دستگاه' ),
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => 'woocommerce',
		'supports' => array( 'title', 'editor' ),
		'capability_type' => 'shop_order',
		'map_meta_cap' => true,
	) );
} );

add_action( 'wp', function () {
	if ( is_singular( 'product' ) && dz_is_industrial( get_queried_object_id() ) ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	}
} );

add_action( 'woocommerce_single_product_summary', function () {
	$product_id = get_queried_object_id();
	if ( ! dz_is_industrial( $product_id ) ) {
		return;
	}
	$result = sanitize_key( $_GET['quote'] ?? '' );
	?>
	<details class="dz-quote" id="consultation"<?php if ( in_array( $result, array( 'received', 'invalid', 'failed', 'open' ), true ) ) { echo ' open'; } ?>>
		<summary class="dz-quote-trigger"><span>استعلام قیمت</span><span aria-hidden="true" class="dz-quote-icon">+</span></summary>
		<div class="dz-quote-body">
		<h2>درخواست مشاوره و استعلام قیمت روز</h2>
		<p class="dz-quote-intro">کارشناس پس از بررسی نیاز شما، مشخصات پیشنهادی و قیمت روز را اعلام می‌کند.</p>
		<?php if ( 'received' === $result ) : ?>
			<p class="dz-quote-notice" role="status">درخواست ثبت شد. برای هماهنگی مشاوره از شماره‌ای که وارد کردید استفاده می‌شود.</p>
		<?php elseif ( 'invalid' === $result ) : ?>
			<p class="dz-quote-notice dz-quote-error" role="alert">نام، شماره موبایل معتبر، شهر و توضیح نیاز را وارد کنید.</p>
		<?php elseif ( 'failed' === $result ) : ?>
			<p class="dz-quote-notice dz-quote-error" role="alert">ثبت درخواست انجام نشد. دوباره تلاش کنید.</p>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="dz_submit_quote">
			<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>">
			<?php wp_nonce_field( 'dz_quote_' . $product_id ); ?>
			<div class="dz-quote-grid">
			<p><label>نام و نام خانوادگی <input name="customer_name" required maxlength="100" autocomplete="name"></label></p>
			<p><label>نام شرکت یا کارگاه <input name="organization" maxlength="100" autocomplete="organization"></label></p>
			<p><label>شماره موبایل <input name="mobile" type="tel" required maxlength="20" autocomplete="tel" dir="ltr"></label></p>
			<p><label>شهر <input name="city" required maxlength="80" autocomplete="address-level2"></label></p>
			</div>
			<p><label>ظرفیت مدنظر و توضیح نیاز <textarea name="requirements" required maxlength="1500" rows="4"></textarea></label></p>
			<button class="button" type="submit">ثبت درخواست مشاوره</button>
		</form>
	</div>
	</details>
	<?php
}, 32 );

function dz_submit_quote() {
	$product_id = absint( $_POST['product_id'] ?? 0 );
	check_admin_referer( 'dz_quote_' . $product_id );
	$product = wc_get_product( $product_id );
	if ( ! $product || 'publish' !== $product->get_status() || ! dz_is_industrial( $product_id ) ) {
		wp_die( 'دستگاه در دسترس نیست.', '', array( 'response' => 404 ) );
	}
	$fields = array(
		'customer_name' => 'نام',
		'organization' => 'شرکت یا کارگاه',
		'mobile' => 'موبایل',
		'city' => 'شهر',
		'requirements' => 'توضیح نیاز',
	);
	$values = array();
	foreach ( $fields as $key => $label ) {
		$value = $_POST[ $key ] ?? '';
		$values[ $key ] = is_string( $value ) ? sanitize_textarea_field( wp_unslash( $value ) ) : '';
	}
	$mobile = strtr( $values['mobile'], array_combine( preg_split( '//u', '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', -1, PREG_SPLIT_NO_EMPTY ), str_split( '01234567890123456789' ) ) );
	$mobile = preg_replace( '/[\s()-]+/', '', $mobile );
	$limits = array( 'customer_name' => 100, 'organization' => 100, 'city' => 80, 'requirements' => 1500 );
	$valid = '' !== $values['customer_name'] && '' !== $values['city'] && '' !== $values['requirements'] && preg_match( '/^(?:0|\+98|0098)9[0-9]{9}$/D', $mobile );
	foreach ( $limits as $key => $limit ) {
		$valid = $valid && mb_strlen( $values[ $key ] ) <= $limit;
	}
	$result = 'invalid';
	if ( $valid ) {
		$values['mobile'] = $mobile;
		$lines = array();
		foreach ( $fields as $key => $label ) {
			$lines[] = $label . ': ' . $values[ $key ];
		}
		$request_id = wp_insert_post( array(
			'post_type' => 'dz_quote',
			'post_title' => 'استعلام: ' . $product->get_name(),
			'post_content' => implode( "\n", $lines ),
			'post_status' => 'private',
			'post_author' => get_current_user_id(),
			'meta_input' => array( '_dz_quote_product' => $product_id ),
		), true );
		$result = $request_id && ! is_wp_error( $request_id ) ? 'received' : 'failed';
	}
	wp_safe_redirect( add_query_arg( 'quote', $result, $product->get_permalink() ) . '#consultation', 303 );
	exit;
}
add_action( 'admin_post_dz_submit_quote', 'dz_submit_quote' );
add_action( 'admin_post_nopriv_dz_submit_quote', 'dz_submit_quote' );



add_filter( 'gettext_woocommerce', function ( $translated, $original ) {
	$labels = array(
		'Default sorting' => 'ترتیب پیش‌فرض',
		'Sort by popularity' => 'محبوب‌ترین',
		'Sort by average rating' => 'بالاترین امتیاز',
		'Sort by latest' => 'جدیدترین',
		'Sort by price: low to high' => 'ارزان‌ترین',
		'Sort by price: high to low' => 'گران‌ترین',
		'Showing the single result' => 'نمایش یک محصول',
		'No products were found matching your selection.' => 'محصولی در این بخش پیدا نشد.',
		'Read more' => 'مشاهده جزئیات',
		'SKU:' => 'شناسه کالا:',
		'Category:' => 'دسته‌بندی:',
		'Categories:' => 'دسته‌بندی‌ها:',
		'Related products' => 'محصولات مرتبط',
		'Reviews (%d)' => 'دیدگاه‌ها (%d)',
		'Reviews' => 'دیدگاه‌ها',
		'Description' => 'توضیحات',
		'Additional information' => 'مشخصات تکمیلی',
		'Home' => 'خانه',
		'Search products…' => 'جست‌وجوی محصولات…',
	);
	return $labels[ $original ] ?? $translated;
}, 10, 2 );

add_filter( 'ngettext_woocommerce', function ( $translated, $single ) {
	$labels = array( 'Showing all %1$d result' => 'نمایش هر %1$d محصول', 'Category:' => 'دسته‌بندی:', 'Reviews (%d)' => 'دیدگاه‌ها (%d)' );
	return $labels[ $single ] ?? $translated;
}, 10, 2 );


add_filter( 'woocommerce_product_add_to_cart_text', function ( $label, $product ) {
	return dz_is_industrial( $product->get_id() ) ? 'استعلام قیمت' : $label;
}, 20, 2 );

add_filter( 'woocommerce_loop_add_to_cart_link', function ( $html, $product ) {
	if ( ! dz_is_industrial( $product->get_id() ) ) {
		return $html;
	}
	return '<a class="button" href="' . esc_url( add_query_arg( 'quote', 'open', $product->get_permalink() ) . '#consultation' ) . '">استعلام قیمت</a>';
}, 20, 2 );



add_filter( 'ngettext_with_context_woocommerce', function ( $translated, $single, $plural, $number, $context ) {
	return 'with first and last result' === $context && 'Showing %1$d&ndash;%2$d of %3$d result' === $single
		? 'نمایش %1$d تا %2$d از %3$d محصول'
		: $translated;
}, 10, 5 );

add_filter( 'woocommerce_catalog_orderby', function ( $options ) {
	if ( is_product_category( array( 'furnaces', 'انواع کوره', 'industrial-equipment', 'ماشین‌آلات صنعتی' ) ) ) {
		unset( $options['price'], $options['price-desc'] );
	}
	return $options;
} );
