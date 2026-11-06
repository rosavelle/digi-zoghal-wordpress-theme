<?php
/**
 * Customer dashboard.
 *
 * @package Digi_Zoghal
 */

defined( 'ABSPATH' ) || exit;

$customer_id  = get_current_user_id();
$current_user = wp_get_current_user();
$display_name = $current_user->display_name ? $current_user->display_name : __( 'دوست عزیز', 'digi-zoghal' );
$name_parts   = preg_split( '/\s+/u', trim( $display_name ) );
$first_name   = ! empty( $name_parts[0] ) ? $name_parts[0] : __( 'دوست عزیز', 'digi-zoghal' );
$order_count  = function_exists( 'wc_get_customer_order_count' ) ? wc_get_customer_order_count( $customer_id ) : 0;
$total_spent  = function_exists( 'wc_get_customer_total_spent' ) ? wc_get_customer_total_spent( $customer_id ) : 0;

$active_query = wc_get_orders(
	array(
		'customer_id' => $customer_id,
		'status'      => array( 'pending', 'on-hold', 'processing' ),
		'limit'       => 1,
		'paginate'    => true,
		'return'      => 'ids',
	)
);
$active_count = isset( $active_query->total ) ? (int) $active_query->total : 0;
$latest       = wc_get_orders(
	array(
		'customer_id' => $customer_id,
		'limit'       => 1,
		'orderby'     => 'date',
		'order'       => 'DESC',
	)
);
$latest_order = ! empty( $latest ) ? $latest[0] : false;
$status       = $latest_order ? $latest_order->get_status() : '';
$status_label = $latest_order ? wc_get_order_status_name( $status ) : __( 'بدون سفارش فعال', 'digi-zoghal' );
$progress     = array(
	'pending'    => 0,
	'on-hold'    => 1,
	'processing' => 2,
	'completed'  => 3,
);
$progress_at  = isset( $progress[ $status ] ) ? $progress[ $status ] : -1;
$steps        = array(
	__( 'ثبت سفارش', 'digi-zoghal' ),
	__( 'تأیید پرداخت', 'digi-zoghal' ),
	__( 'در حال انجام', 'digi-zoghal' ),
	__( 'تکمیل سفارش', 'digi-zoghal' ),
);
?>
<section class="dz-account-overview">
	<header class="dz-account-welcome">
		<div>
			<span class="dz-account-eyebrow"><?php esc_html_e( 'حساب کاربری', 'digi-zoghal' ); ?></span>
			<h1><?php printf( esc_html__( 'سلام، %s', 'digi-zoghal' ), esc_html( $first_name ) ); ?></h1>
			<p><?php esc_html_e( 'سفارش‌ها، آدرس‌ها و اطلاعات حساب خود را از همین‌جا مدیریت کنید.', 'digi-zoghal' ); ?></p>
		</div>
		<div class="dz-account-latest-status">
			<span><?php esc_html_e( 'آخرین وضعیت سفارش', 'digi-zoghal' ); ?></span>
			<strong><?php echo esc_html( $status_label ); ?></strong>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'مشاهده سفارش‌ها', 'digi-zoghal' ); ?> ←</a>
		</div>
	</header>

	<div class="dz-account-stats">
		<div class="dz-account-stat"><span class="dz-account-stat-icon">▤</span><div><small><?php esc_html_e( 'همه سفارش‌ها', 'digi-zoghal' ); ?></small><strong><?php echo esc_html( number_format_i18n( $order_count ) ); ?> <?php esc_html_e( 'سفارش', 'digi-zoghal' ); ?></strong></div></div>
		<div class="dz-account-stat"><span class="dz-account-stat-icon">◷</span><div><small><?php esc_html_e( 'در حال انجام', 'digi-zoghal' ); ?></small><strong><?php echo esc_html( number_format_i18n( $active_count ) ); ?> <?php esc_html_e( 'سفارش', 'digi-zoghal' ); ?></strong></div></div>
		<div class="dz-account-stat"><span class="dz-account-stat-icon">✓</span><div><small><?php esc_html_e( 'مجموع خرید', 'digi-zoghal' ); ?></small><strong><?php echo wp_kses_post( wc_price( $total_spent ) ); ?></strong></div></div>
	</div>

	<div class="dz-account-dashboard-grid">
		<section class="dz-account-card dz-account-order-card">
			<div class="dz-account-card-head"><h2><?php esc_html_e( 'آخرین سفارش', 'digi-zoghal' ); ?></h2><?php if ( $latest_order ) : ?><a href="<?php echo esc_url( $latest_order->get_view_order_url() ); ?>"><?php esc_html_e( 'جزئیات کامل', 'digi-zoghal' ); ?> ←</a><?php endif; ?></div>
			<?php if ( $latest_order ) : ?>
				<div class="dz-account-order-top">
					<div><strong><?php printf( esc_html__( 'سفارش #%s', 'digi-zoghal' ), esc_html( $latest_order->get_order_number() ) ); ?></strong><small><?php echo esc_html( wc_format_datetime( $latest_order->get_date_created(), 'Y/m/d' ) ); ?></small></div>
					<span class="dz-account-status dz-status-<?php echo esc_attr( sanitize_html_class( $status ) ); ?>"><?php echo esc_html( $status_label ); ?></span>
				</div>
				<div class="dz-account-order-total"><span><?php esc_html_e( 'مبلغ سفارش', 'digi-zoghal' ); ?></span><strong><?php echo wp_kses_post( wc_price( $latest_order->get_total(), array( 'currency' => $latest_order->get_currency() ) ) ); ?></strong></div>
				<?php if ( $progress_at >= 0 ) : ?>
					<div class="dz-account-track">
						<?php foreach ( $steps as $index => $step ) : ?>
							<span class="<?php echo $index <= $progress_at ? 'is-done' : ''; ?>"><?php echo esc_html( $step ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<div class="dz-account-empty"><strong><?php esc_html_e( 'هنوز سفارشی ثبت نشده است.', 'digi-zoghal' ); ?></strong><a class="button" href="<?php echo esc_url( digi_zoghal_shop_url() ); ?>"><?php esc_html_e( 'مشاهده محصولات', 'digi-zoghal' ); ?></a></div>
			<?php endif; ?>
		</section>

		<section class="dz-account-card">
			<div class="dz-account-card-head"><h2><?php esc_html_e( 'دسترسی سریع', 'digi-zoghal' ); ?></h2></div>
			<div class="dz-account-quick-list">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>"><span><strong><?php esc_html_e( 'آدرس‌های ارسال', 'digi-zoghal' ); ?></strong><small><?php esc_html_e( 'مدیریت نشانی‌های تحویل', 'digi-zoghal' ); ?></small></span><b><?php esc_html_e( 'مدیریت', 'digi-zoghal' ); ?></b></a>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>"><span><strong><?php esc_html_e( 'اطلاعات شخصی', 'digi-zoghal' ); ?></strong><small><?php esc_html_e( 'نام، ایمیل و رمز عبور', 'digi-zoghal' ); ?></small></span><b><?php esc_html_e( 'ویرایش', 'digi-zoghal' ); ?></b></a>
				<a href="<?php echo esc_url( digi_zoghal_shop_url() ); ?>"><span><strong><?php esc_html_e( 'خرید دوباره', 'digi-zoghal' ); ?></strong><small><?php esc_html_e( 'بازگشت به محصولات فروشگاه', 'digi-zoghal' ); ?></small></span><b><?php esc_html_e( 'فروشگاه', 'digi-zoghal' ); ?></b></a>
			</div>
		</section>
	</div>
</section>

<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
