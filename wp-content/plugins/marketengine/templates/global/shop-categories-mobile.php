<?php
/**
 * The Template for displaying shop categories menu. This is version for mobile.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/global/shop-categories-mobile.php.
 *
 * @package     MarketEngine/Templates
 * @version       1.0
 */
?>
<?php if ( has_nav_menu( 'category-menu' ) ) : ?>
    <?php
        wp_nav_menu( array(
            'theme_location' => 'category-menu',
            'menu_class'     => 'me-menu-category-mobile',
        ));
    ?>
<?php endif; ?>
