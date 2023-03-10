<?php
/**
 * The mobile header that will be displayed on desktop devices.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$wishlist = null;

if ( class_exists( 'RedParts\Sputnik\Wishlist' ) ) {
	$wishlist = RedParts\Sputnik\Wishlist::instance();
}

?>
<div class="th-mobile-header">
	<div class="th-container">
		<div class="th-mobile-header__body">
			<button class="th-mobile-header__menu-button" type="button">
				<?php redparts_the_icon( 'menu-18x14' ); ?>
			</button>

			<?php get_template_part( 'partials/mobile-header/logo' ); ?>

			<?php
			get_search_form(
				array(
					'echo'                 => true,
					'redparts_location'   => 'mobile-header',
					'redparts_search_by'  => 'product',
					'redparts_categories' => true,
					'redparts_classes'    => 'th-mobile-header__search',
				)
			);
			?>

			<div class="th-mobile-header__indicators">
				<div class="th-mobile-indicator th-mobile-indicator--search th-display-md-none">
					<button type="button" class="th-mobile-indicator__button">
						<span class="th-mobile-indicator__icon">
							<?php redparts_the_icon( 'search-20' ); ?>
						</span>
					</button>
				</div>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<div class="th-mobile-indicator th-display-none th-display-md-block">
						<a
							href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>"
							class="th-mobile-indicator__button"
						>
							<span class="th-mobile-indicator__icon">
								<?php redparts_the_icon( 'person-20' ); ?>
							</span>
						</a>
					</div>
				<?php endif; ?>
				<?php if ( $wishlist ) : ?>
					<div class="th-mobile-indicator th-mobile-indicator--wishlist th-display-none th-display-md-block">
						<a
							href="<?php echo esc_url( $wishlist->get_page_url() ); ?>"
							class="th-mobile-indicator__button"
						>
							<span class="th-mobile-indicator__icon">
								<?php redparts_the_icon( 'heart-20' ); ?>
								<span class="th-mobile-indicator__counter">
									<?php echo esc_html( $wishlist->get_count() ); ?>
								</span>
							</span>
						</a>
					</div>
				<?php endif; ?>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<div class="th-mobile-indicator th-mobile-indicator--cart">
						<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="th-mobile-indicator__button">
							<span class="th-mobile-indicator__icon">
								<?php redparts_the_icon( 'cart-20' ); ?>
								<span class="th-mobile-indicator__counter">
									<?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?>
								</span>
							</span>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
