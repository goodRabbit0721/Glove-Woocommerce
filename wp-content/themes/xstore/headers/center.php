<?php 
    $ht = etheme_get_header_type();
    $color = etheme_get_header_color();
    $menu_class = 'menu-align-' . etheme_get_option('menu_align');
?>

<div class="header-wrapper header-<?php echo esc_attr( $ht ); ?> header-color-<?php echo esc_attr( $color ); ?>">
    <?php get_template_part('headers/parts/top-bar'); ?>
    <header class="header main-header">
        <div class="container">
            <div class="container-wrapper">
                <div class="header-left">
                    <?php if(etheme_get_option('search_form')): ?>
                        <?php etheme_search_form( array(
                            'action' => 'default'
                        )); ?>
                    <?php endif; ?>
                </div>
                <div class="header-logo"><?php etheme_logo(); ?></div>
                <div class="navbar-header">
                    <?php if( etheme_woocommerce_installed() ) etheme_wishlist_widget(); ?>
                    
                    <?php if(class_exists('Woocommerce') && current_theme_supports('woocommerce') && !etheme_get_option('just_catalog') && etheme_get_option('cart_widget')): ?>
                        <?php etheme_top_cart(); ?>
                    <?php endif ;?>
                </div>
                <div class="navbar-toggle">
                    <span class="sr-only"><?php esc_html_e('Menu', 'xstore'); ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </div>
            </div>
        </div>
    </header>
    <div class="navigation-wrapper">
        <div class="container">
            <div class="menu-inner">
                <div class="menu-wrapper <?php echo esc_attr($menu_class); ?>"><?php etheme_get_main_menu(); ?></div>
            </div>
        </div>
    </div>
</div>