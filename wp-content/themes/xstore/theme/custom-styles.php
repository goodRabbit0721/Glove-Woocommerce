<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');
// **********************************************************************//
// ! Ititialize theme configuration and variables
// **********************************************************************//

add_action('wp_head', 'etheme_assets');
if(!function_exists('etheme_assets')) {
function etheme_assets() {
global $et_selectors;
$et_selectors = array();

$et_selectors['active_color'] = '
.active-color,
.cart-widget-products a:hover,
.star-rating span:before,
.price ins .amount,
.big-coast .amount,
.tabs .tab-title.opened,
.tabs .tab-title:hover,
.product-brands .view-products,
.shipping-calculator-button,
.views-count,
.post-comments-count,
.read-more,
span.active,
.active-link,
.active-link:hover,
ul.active > li:before,
.author-info .author-link,
.comment-reply-link,
.lost_password a,
.product-content .compare:hover:before,
.product-content .compare.added:before,
.footer-product .compare:hover:before,
.footer-product .compare.added:before,
.product-content .compare:hover,
.mobile-menu-wrapper .links li a:hover,
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab.vc_active>a,
.page-404 .largest,
.meta-post-timeline .time-mon,
.portfolio-filters .active,
.tabs .accordion-title.opened-parent:after,
.item-design-mega-menu .nav-sublist-dropdown .item-level-1:hover > a,
.text-color-dark .category-grid .categories-mask span,
.header-standard .navbar-header .et-wishlist-widget .fa,
.team-member .member-details h5,
.team-member .member-content .menu-social-icons li:hover i,
.fixed-header .menu-wrapper .menu > li.current-menu-item > a,
.et-header-not-overlap.header-wrapper .menu-wrapper .menu > li.current-menu-item > a,
.sidebar-widget ul li > ul.children li > a:hover,
.product-information .out-of-stock,
.sidebar-widget li a:hover,
#etheme-popup .mfp-close:hover:before,
.etheme_widget_brands li a strong,
.widget_product_categories.sidebar-widget ul li.current-cat > a,
.shipping-calculator-button:focus,
table.cart .product-details a:hover,
.mobile-menu-wrapper .menu li a:hover,
.mobile-menu-wrapper .menu > li .sub-menu li a:hover,
.mobile-menu-wrapper .menu > li .sub-menu .menu-show-all a,
#review_form .stars a:hover:before, #review_form .stars a.active:before,
.item-design-mega-menu .nav-sublist-dropdown .nav-sublist li.current-menu-item a,
.item-design-dropdown .nav-sublist-dropdown ul > li.current-menu-item > a,
.mobile-menu-wrapper .mobile-sidebar-widget.etheme_widget_socials a:hover,
.mobile-sidebar-widget.etheme_widget_socials .et-follow-buttons.buttons-size-large a:hover,
.product-view-mask2.view-color-transparent .footer-product .button:hover:before, .product-view-mask2.view-color-transparent .show-quickly:hover:before,
.product-view-mask2.view-color-transparent .yith-wcwl-add-button a.add_to_wishlist:hover:before,
.product-view-default .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse.show a:before, .product-view-default .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse.show a:before,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse.show a:before, .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse.show a:before,
.product-view-mask2.view-color-transparent .yith-wcwl-wishlistexistsbrowse a:hover:before, .product-view-mask2.view-color-transparent .yith-wcwl-wishlistaddedbrowse a:hover:before,
.product-information .yith-wcwl-add-to-wishlist a:hover:before, .product-info-wrapper .yith-wcwl-add-to-wishlist a:hover:before, .product-summary-center .yith-wcwl-add-to-wishlist a:hover:before,
.widget_product_categories.sidebar-widget ul li a:hover,
.et-wishlist-widget .wishlist-dropdown li .product-title a:hover,
.woocommerce-MyAccount-navigation li.is-active a,
.wcpv-sold-by-single a,
.sb-infinite-scroll-load-more:not(.finished):hover,
.single-product-booking .product-side-information-inner .price .amount,
.product-view-booking .price .amount,
.product-view-booking .content-product .button.compare:hover
';

$et_selectors['active_bg'] = '
.tagcloud a:hover,
.button.active,
.btn.active,
.btn.active:hover,
.btn-checkout,
.btn-checkout:hover,
.button:hover, .btn:hover, input[type="submit"]:hover,
.type-label-2,
.et-loader svg .outline,
.header-search.act-default #searchform .btn:hover,
.widget_product_categories .widget-title,
.price_slider_wrapper .ui-slider .ui-slider-handle,
.price_slider_wrapper .ui-slider-range,
.pagination-cubic ul li span.current,
.pagination-cubic ul li a:hover,
.view-switcher .switch-list:hover a,
.view-switcher .switch-grid:hover a,
.view-switcher .switch-list.switcher-active a,
.view-switcher .switch-grid.switcher-active a,

.tabs .tab-title.opened span:after,
.wpb_tabs .wpb_tabs_nav li a.opened span:after,
table.shop_table .remove-item:hover,
.et-tabs-wrapper .tabs-nav li:after,
.checkout-button,
.active-link:before,
.block-title .label,
.form-row.place-order input[type="submit"],
.wp-picture .post-categories,
.single-tags a:hover,
.portfolio-filters li a:after,
.form-submit input[type="submit"],

.woocommerce table.wishlist_table .product-remove a:hover,
.vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab.vc_active > a:after,
.vc_tta-style-classic .vc_tta-panel.vc_active .vc_tta-panel-heading a span:after,
.posts-nav-btn:hover .button,
.posts-nav-btn .post-info,
#cboxClose:hover,
.global-post-template-large .post-categories,
.global-post-template-large2 .post-categories,
.portfolio-item .portfolio-image,
.header-standard.header-color-dark .ico-design-1 .cart-bag,
.testimonials-slider .owl-buttons .owl-prev:hover, .testimonials-slider .owl-buttons .owl-next:hover,
.item-design-posts-subcategories .posts-content .post-preview-thumbnail .post-category,
.sidebar-slider .owl-carousel .owl-controls .owl-next:hover,
.sidebar-slider .owl-carousel .owl-controls .owl-prev:hover,
.ibox-block .ibox-symbol i,
ol.active > li:before,
span.dropcap.dark,
.fixed-header .menu-wrapper .menu > li.current-menu-item > a:after,
.etheme_widget_entries_tabs .tabs .tab-title:after,
.articles-pagination .current, .articles-pagination a:hover,
.product-information .yith-wcwl-add-to-wishlist a:hover:before,
.product-information .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:before,
.product-information .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
.top-panel-container .menu-social-icons a:hover,
.wp-picture .blog-mask:before,
.main-images .owl-controls .owl-prev:hover,
.main-images .owl-controls .owl-next:hover,
.thumbnails-list .owl-controls .owl-next:hover,
.thumbnails-list .owl-controls .owl-prev:hover,
.portfolio-single-item .menu-social-icons a:hover i,
.flexslider .flex-direction-nav a:hover,
.back-top:hover,
.tagcloud a:hover,
.footer.text-color-light .tagcloud a:hover,
.widget_search button:hover,
.thumbnails-list .video-thumbnail span,
.carousel-area .owl-prev:hover,
.carousel-area .owl-next:hover,
.brands-carousel .owl-prev:hover, .owl-images-carousel .owl-prev:hover, .brands-carousel .owl-next:hover, .owl-images-carousel .owl-next:hover,
.post-gallery-slider .owl-controls .owl-buttons >div:hover,
.quantity.buttons_added span:hover,
.openswatch_widget_layered_nav ul li.chosen, .openswatch_widget_layered_nav ul li:hover,
ul.swatch li.selected,
.open-filters-btn a:hover,
.owl-carousel .owl-pagination .owl-page:hover, .owl-carousel .owl-pagination .owl-page.active,
.zoom-images-button:hover, .open-video-popup:hover, .open-360-popup:hover,
.et-products-navigation > div:hover,
.et-looks .et-looks-nav li.active a,
.et-looks .et-looks-nav li:hover a,
.quick-view-popup .mfp-close:hover,
.read-more:before,
.team-member .member-image:before,
#cookie-notice .button,
#cookie-notice .button.bootstrap,
#cookie-notice .button.wp-default,
#cookie-notice .button.wp-default:hover,
.mfp-image-holder .mfp-close:hover, .mfp-iframe-holder .mfp-close:hover,#product-video-popup .mfp-close:hover,
.et-products-navigation > div:hover .swiper-nav-arrow,
.product-view-default .footer-product .show-quickly
.et-tabs-wrapper .tabs-nav li:after,
.et-tabs-wrapper .tabs .accordion-title:after
';

$et_selectors['active_border'] = '
.tagcloud a:hover,
.button.active,
.btn.active,
.btn.active:hover,
.btn-checkout,
.btn-checkout:hover,
.button:hover, input[type="submit"]:hover, .btn:hover,
.form-row.place-order input[type="submit"],
.pagination-cubic ul li span.current,
.pagination-cubic ul li a:hover,
.form-submit input[type="submit"],
.fixed-header,
.single-product-center .quantity.buttons_added span:hover,
.header-standard.header-color-dark .cart-bag:before,
.articles-pagination .current, .articles-pagination a:hover,
.widget_search button:hover,
table.cart .remove-item:hover,
.checkout-button,
.openswatch_widget_layered_nav ul li.chosen,
.openswatch_widget_layered_nav ul li:hover,
.open-filters-btn a:hover,
.header-standard.header-color-dark .cart-bag,
.header-standard.header-color-dark .cart-summ:hover .cart-bag,
.header-standard .header-standard.header-color-dark,
.header-standard .shopping-container.ico-design-1.ico-bg-yes .cart-bag:before,
.header-standard .shopping-container .cart-summ:hover .cart-bag:before,
.header-standard .shopping-container.ico-design-1.ico-bg-yes .cart-bag,
.et-tabs-wrapper .tabs-nav li.et-opened:before,
.et-tabs-wrapper .tabs .accordion-title.opened:before


';

$et_selectors['active_stroke'] = '
.et-loader svg .outline,
.et-timer.dark .time-block .circle-box svg circle
';

?>

<?php
$activeColor = (etheme_get_option('activecol')) ? etheme_get_option('activecol') : '#8a8a8a';
$post_id = etheme_get_page_id();
$header_bg = etheme_get_custom_field('header_bg', $post_id['id']);
?>

<style type="text/css">
    <?php echo etheme_js2tring($et_selectors['active_color']); ?>              { color: <?php echo $activeColor; ?>; }
    <?php echo etheme_js2tring($et_selectors['active_bg']); ?>                 { background-color: <?php echo $activeColor; ?>; }
    <?php echo etheme_js2tring($et_selectors['active_border']); ?>             { border-color: <?php echo $activeColor; ?>; }
    <?php echo etheme_js2tring($et_selectors['active_stroke']); ?>             { stroke: <?php echo $activeColor; ?>; }

	.et-header-full-width .main-header .container,
	.et-header-full-width .fixed-header .container {
		max-width: <?php etheme_option('header_width'); ?>px;
	}

	<?php if( ! empty( $header_bg) ): ?>
		.main-header {
			background-color: <?php echo $header_bg; ?>;
		}
	<?php endif; ?>
</style>
<?php
}
}