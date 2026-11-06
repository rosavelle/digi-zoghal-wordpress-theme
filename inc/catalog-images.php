<?php
/**
 * Bundled product photos and illustrations.
 */

defined( 'ABSPATH' ) || exit;

function dz_catalog_illustration( $product ) {
	if ( ! $product instanceof WC_Product || $product->get_parent_id() ) {
		return '';
	}

	$sku_images = array(
		'dz-pressed-charcoal' => 'product-pressed-assortment.webp',
		'dz-fire-starter-gel' => 'fire-starter-gel.svg',
		'hermes-mix' => 'product-mixer.webp',
		'hermes-fill' => 'product-weighing-filler.webp',
		'hermes-seal' => 'product-packaging-sealer.webp',
		'hermes-kiln-batch' => 'product-batch-kiln.webp',
		'hermes-kiln-continuous' => 'product-continuous-kiln.webp',
		'hermes-kiln-electric' => 'product-electric-kiln.webp',
		'demo-lemon' => 'lemon.png',
		'demo-cube' => 'cube.png',
		'demo-grill' => 'grill.png',
		'demo-hex' => 'hex.png',
		'demo-round' => 'round.png',
		'demo-oak' => 'oak.png',
	);
	$name_images = array(
		'زغال فشرده و قالبی' => 'product-pressed-assortment.webp',
		'تنباکو کلاسیک' => 'product-tobacco-classic.webp',
		'تنباکو ویژه' => 'product-tobacco-special.webp',
		'تنباکو سنتی' => 'product-tobacco-traditional.webp',
		'زغال بامبو' => 'product-bamboo-charcoal.webp',
		'زغال خودسوز' => 'product-self-lighting.webp',
		'زغال قالبی سکه' => 'product-coin-charcoal.webp',
		'زغال نارگیلی سوپر' => 'product-coconut-super.webp',
		'زغال نارگیلی مکعبی' => 'product-coconut-cubes.webp',
	);

	$file = $sku_images[ $product->get_sku() ] ?? '';
	if ( ! $file ) {
		foreach ( $name_images as $name => $image ) {
			if ( 0 === strpos( $product->get_name(), $name ) ) {
				$file = $image;
				break;
			}
		}
	}
	if ( ! $file ) {
		return '';
	}

	$extension = pathinfo( $file, PATHINFO_EXTENSION );
	$photo     = 'webp' === $extension;
	if ( ! $photo && $product->get_image_id() ) {
		return '';
	}

	$dimensions = array( 'webp' => 900, 'png' => 640, 'svg' => 320 );
	$size       = $dimensions[ $extension ];
	return sprintf(
		'<img class="wp-post-image dz-catalog-illustration" src="%1$s" alt="%2$s" width="%3$d" height="%3$d">',
		esc_url( get_template_directory_uri() . '/assets/catalog/' . $file ),
		esc_attr( $photo ? $product->get_name() : sprintf( __( 'تصویر نمونهٔ %s', 'digi-zoghal' ), $product->get_name() ) ),
		$size
	);
}

add_filter( 'woocommerce_product_get_image', function ( $html, $product, $size, $attributes, $placeholder ) {
	return $placeholder ? ( dz_catalog_illustration( $product ) ?: $html ) : $html;
}, 10, 5 );

add_filter( 'woocommerce_single_product_image_thumbnail_html', function ( $html, $attachment_id ) {
	if ( ! is_singular( 'product' ) ) {
		return $html;
	}

	$product = wc_get_product( get_queried_object_id() );
	if ( ! $product || ( $attachment_id && (int) $attachment_id !== (int) $product->get_image_id() ) ) {
		return $html;
	}

	$image = dz_catalog_illustration( $product );
	return $image ? '<div class="woocommerce-product-gallery__image--placeholder">' . $image . '</div>' : $html;
}, 10, 2 );
