<?php
/**
 * Shared catalog links for the header and footer.
 */

defined( 'ABSPATH' ) || exit;

function dz_catalog_destination( $slug, $label ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' ) ?: get_term_by( 'name', $label, 'product_cat' );
	$url  = $term ? get_term_link( $term ) : false;
	return $url && ! is_wp_error( $url )
		? $url
		: add_query_arg( array( 's' => rawurlencode( $label ), 'post_type' => 'product' ), home_url( '/' ) );
}

function dz_pressed_product_url( $fallback ) {
	$product = wc_get_product( wc_get_product_id_by_sku( 'dz-pressed-charcoal' ) );
	if ( $product && 'publish' === $product->get_status() && $product->is_type( 'variable' ) ) {
		return $product->get_permalink();
	}

	$matches = get_posts( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'title'          => 'زغال فشرده و قالبی',
		'posts_per_page' => 2,
		'fields'         => 'ids',
	) );
	if ( 1 === count( $matches ) ) {
		$product = wc_get_product( $matches[0] );
		if ( $product && $product->is_type( 'variable' ) ) {
			return $product->get_permalink();
		}
	}

	return $fallback;
}

function dz_about_page() {
	$page = get_page_by_path( 'about' );
	return $page && 'publish' === $page->post_status ? $page : null;
}

function dz_about_url() {
	$page = dz_about_page();
	return $page ? get_permalink( $page ) : home_url( '/#about' );
}

function dz_catalog_category_links() {
	$groups = array(
		'ماشین‌آلات صنعتی' => array( 'industrial-equipment', 'ماشین‌آلات صنعتی' ),
		'زغال قلیان'      => array( 'hookah-charcoal', 'زغال قلیان', 'charcoal', 'زغال' ),
		'زغال کبابی'      => array( 'barbecue-charcoal', 'زغال کبابی' ),
		'انواع کوره'      => array( 'furnaces', 'انواع کوره', 'کوره' ),
	);
	$links = array();
	foreach ( $groups as $label => $aliases ) {
		foreach ( $aliases as $alias ) {
			$term = get_term_by( 'slug', $alias, 'product_cat' ) ?: get_term_by( 'name', $alias, 'product_cat' );
			if ( ! $term ) {
				continue;
			}
			$url = get_term_link( $term );
			if ( ! is_wp_error( $url ) ) {
				$links[ $label ] = $url;
				break;
			}
		}
	}
	if ( ! isset( $links['زغال کبابی'] ) && post_type_exists( 'product' ) ) {
		$links['زغال کبابی'] = dz_catalog_destination( 'barbecue-charcoal', 'زغال کبابی' );
	}
	return $links;
}

function dz_render_catalog_links( $links, $class = '' ) {
	if ( ! $links ) {
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $links as $label => $url ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

add_filter( 'wp_nav_menu_objects', function ( $items, $args ) {
	if ( 'primary' !== $args->theme_location ) {
		return $items;
	}
	$links = dz_catalog_category_links();
	$shop_url = untrailingslashit( digi_zoghal_shop_url() );
	foreach ( $items as $shop ) {
		if ( $shop->menu_item_parent || ( untrailingslashit( $shop->url ) !== $shop_url && 'فروشگاه' !== trim( $shop->title ) ) ) {
			continue;
		}
		$existing = array();
		foreach ( $items as $item ) {
			if ( (int) $item->menu_item_parent === (int) $shop->ID ) {
				$existing[ untrailingslashit( $item->url ) ] = $item;
			}
		}
		$index = 0;
		foreach ( $links as $label => $url ) {
			$key = untrailingslashit( $url );
			if ( isset( $existing[ $key ] ) ) {
				$existing[ $key ]->title = $label;
				continue;
			}
			$id = -10000 - $index++;
			$items[] = (object) array(
				'ID' => $id, 'db_id' => $id, 'menu_item_parent' => $shop->ID,
				'title' => $label, 'url' => $url, 'classes' => array( 'menu-item' ),
				'target' => '', 'attr_title' => '', 'xfn' => '', 'current' => false,
			);
		}
		if ( $links && ! in_array( 'menu-item-has-children', $shop->classes, true ) ) {
			$shop->classes[] = 'menu-item-has-children';
		}
		break;
	}
	$about = dz_about_url();
	foreach ( $items as $item ) {
		if ( untrailingslashit( $item->url ) === untrailingslashit( $about ) || 'درباره ما' === trim( $item->title ) ) {
			return $items;
		}
	}
	$items[] = (object) array(
		'ID' => -11000, 'db_id' => -11000, 'menu_item_parent' => 0,
		'title' => __( 'درباره ما', 'digi-zoghal' ), 'url' => $about, 'classes' => array( 'menu-item' ),
		'target' => '', 'attr_title' => '', 'xfn' => '', 'current' => false,
	);
	return $items;
}, 10, 2 );
