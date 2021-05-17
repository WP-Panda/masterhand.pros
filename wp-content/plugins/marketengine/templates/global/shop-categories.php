<?php
/**
 * The Template for displaying shop categories menu.
 *
 * This template can be overridden by copying it to yourtheme/marketengine/global/shop-categories.php.
 *
 * @package     MarketEngine/Templates
 * @version       1.0
 */
?>
<?php if ( has_nav_menu( 'category-menu' ) ) : ?>
    <ul class="me-shopcategories me-has-category">
        <li>
            <span class="marketengine-bar-title"><i class="icon-me-list"></i><span><?php echo __('SHOP CATEGORIES', 'enginethemes'); ?></span></span>
                <nav class="me-nav-category">
                    <div class="me-container">
                        <span class="me-triangle-top"></span>
                        <?php
                            wp_nav_menu( array(
                                'theme_location' => 'category-menu',
                                'menu_class'     => 'me-menu-category',
                            ));
                        ?>
                    </div>
                </nav>
        </li>
    </ul>
<?php endif; ?>
