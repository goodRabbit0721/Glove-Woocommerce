<?php
/**
 * ReduxFramework Sample Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
 */


if ( ! class_exists( 'Redux' ) ) {
    global $et_options;

    $et_options = array(
        'main_layout' => 'wide',
        'header_type' => 'xstore',
        'header_full_width' => '1',
        'header_color' => 'white',
        'header_overlap' => '1',
        'top_bar' => '1',
        'top_bar_color' => 'white',
        'logo_width' => '200',
        'top_links' => '1',
        'search_form' => '1',
        'breadcrumb_type' => 'default',
        'breadcrumb_size' => 'small',
        'breadcrumb_effect' => 'none',
        'breadcrumb_bg' =>
            array (
                'background-color' => '#d64444',
            ),
        'breadcrumb_color' => 'white',
        'activecol' => '#d64444',
        'blog_hover' => 'default',
        'blog_byline' => '1',
        'read_more' => '1',
        'views_counter' => '1',
        'blog_sidebar' => 'right',
        'excerpt_length' => '25',
        'post_template' => 'default',
        'blog_featured_image' => '1',
    );
    return;
}


if(!function_exists('etheme_redux_init')) {
    function etheme_redux_init() {
        // This is your option name where all the Redux data is stored.
        $opt_name = "et_options";


        /**
         * ---> SET ARGUMENTS
         * All the possible arguments for Redux.
         * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
         * */

        $theme = wp_get_theme(); // For use with some settings. Not necessary.

        $args = array(
            // TYPICAL -> Change these values as you need/desire
            'opt_name'             => $opt_name,
            // This is where your data is stored in the database and also becomes your global variable name.
            'display_name'         => ETHEME_THEME_NAME . ' <span>' . esc_html__('8theme WordPress Theme', 'xstore') .'</span>',
            // Name that appears at the top of your panel
            'display_version'      => $theme->get( 'Version' ),
            // Version that appears at the top of your panel
            'menu_type'            => 'menu',
            //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
            'allow_sub_menu'       => true,
            // Show the sections below the admin menu item or not
            'menu_title'           => esc_html__( '8Theme Options', 'xstore' ),
            'page_title'           => esc_html__( '8Theme Options', 'xstore' ),
            // You will need to generate a Google API key to use this feature.
            // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
            'google_api_key'       => '',
            // Set it you want google fonts to update weekly. A google_api_key value is required.
            'google_update_weekly' => false,
            // Must be defined to add google fonts to the typography module
            'async_typography'     => false,
            // Use a asynchronous font on the front end or font string
            //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
            'admin_bar'            => false,
            // Show the panel pages on the admin bar
            'admin_bar_icon'       => 'dashicons-portfolio',
            // Choose an icon for the admin bar menu
            'admin_bar_priority'   => 50,
            // Choose an priority for the admin bar menu
            'global_variable'      => '',
            // Set a different name for your global variable other than the opt_name
            'dev_mode'             => false,
            // Show the time the page took to load, etc
            'update_notice'        => true,
            // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
            'customizer'           => true,
            // Enable basic customizer support
            //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
            //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

            // OPTIONAL -> Give you extra features
            'page_priority'        => 63,
            // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
            'page_parent'          => 'themes.php',
            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
            'page_permissions'     => 'manage_options',
            // Permissions needed to access the options panel.
            'menu_icon'            => ETHEME_CODE_IMAGES . 'icon-etheme.png',
            // Specify a custom URL to an icon
            'last_tab'             => '',
            // Force your panel to always open to a specific tab (by id)
            'page_icon'            => 'icon-themes',
            // Icon displayed in the admin panel next to your menu_title
            'page_slug'            => '_options',
            // Page slug used to denote the panel
            'save_defaults'        => true,
            // On load save the defaults to DB before user clicks save or not
            'default_show'         => false,
            // If true, shows the default value next to each field that is not the default value.
            'default_mark'         => '',
            // What to print by the field's title if the value shown is default. Suggested: *
            'show_import_export'   => true,
            // Shows the Import/Export panel when not used as a field.

            // CAREFUL -> These options are for advanced use only
            'transient_time'       => 60 * MINUTE_IN_SECONDS,
            'output'               => true,
            // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
            'output_tag'           => true,
            // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
            'footer_credit'     => '8theme',                   // Disable the footer credit of Redux. Please leave if you can help it.


            'templates_path' => ETHEME_BASE . ETHEME_CODE_3D . 'options-framework/et-templates/',

            // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
            'database'             => '',
            // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
            'system_info'          => false
        );


        Redux::setArgs( $opt_name, $args );

        /*
         * ---> END ARGUMENTS
         */

        // -> START Basic Fields

        Redux::setSection( $opt_name, array(
            'title' => 'General',
            'id' => 'general',
            'icon' => 'el-icon-home',
        ) );


        Redux::setSection( $opt_name, array(
            'title' => 'Layout',
            'id' => 'general-layout',
            'subsection' => true,
            'icon' => 'el-icon-home',
            'fields' => array (
                array (
                    'id' => 'main_layout',
                    'type' => 'select',
                    'operator' => 'and',
                    'title' => 'Site Layout',
                    'options' => array (
                        'wide' => 'Wide layout',
                        'boxed' => 'Boxed',
                        'framed' => 'Framed',
                        'bordered' => 'Bordered',
                    ),
                    'default' => 'wide'
                ),
                array (
                    'id' => 'site_preloader',
                    'type' => 'switch',
                    'title' => 'Use site preloader',
                    'default' => false,
                ),
            ),
        ) );

        Redux::setSection( $opt_name, array(
            'title' => 'Header Type',
            'id' => 'general-header',
            'icon' => 'el-icon-cog',
            'subsection' => true,
            'fields' => array (
                array (
                    'id' => 'header_type',
                    'type' => 'image_select',
                    'title' => 'Header Type',
                    'options' => array (
                        'xstore' => array (
                            'title' => 'Variant xstore',
                            'img' => ETHEME_CODE_IMAGES . 'headers/xstore.jpg',
                        ),
                        'xstore2' => array (
                            'title' => 'Variant xstore2',
                            'img' => ETHEME_CODE_IMAGES . 'headers/xstore2.jpg',
                        ),
                        'center' => array (
                            'title' => 'Variant center',
                            'img' => ETHEME_CODE_IMAGES . 'headers/center.jpg',
                        ),
                        'center2' => array (
                            'title' => 'Variant center 2',
                            'img' => ETHEME_CODE_IMAGES . 'headers/center2.jpg',
                        ),
                        'center3' => array (
                            'title' => 'Variant center 3',
                            'img' => ETHEME_CODE_IMAGES . 'headers/center3.jpg',
                        ),
                        'standard' => array (
                            'title' => 'Variant standard',
                            'img' => ETHEME_CODE_IMAGES . 'headers/standard.jpg',
                        ),
                        'double-menu' => array (
                            'title' => 'Double menu',
                            'img' => ETHEME_CODE_IMAGES . 'headers/double-menu.jpg',
                        ),
                        'two-rows' => array (
                            'title' => 'Two rows',
                            'img' => ETHEME_CODE_IMAGES . 'headers/two-rows.jpg',
                        ),
                    ),
                    'default' => 'xstore'
                ),
            ),
        ) );

        Redux::setSection( $opt_name, array(
            'title' => 'Header Settings',
            'id' => 'general-header-settings',
            'icon' => 'el-icon-cog',
            'subsection' => true,
            'fields' => array (
                array (
                    'id' => 'header_full_width',
                    'type' => 'switch',
                    'title' => 'Header wide',
                    'default' => true,
                ),
                array (
                    'id'        => 'header_width',
                    'type'      => 'slider',
                    'title'     => 'Header maximum width',
                    "default"   => 1600,
                    "min"       => 1300,
                    "step"      => 1,
                    "max"       => 3000,
                    'display_value' => 'label',
                    'required' => array(
                        array( 'header_full_width', 'equals', true)
                    )
                ),
                array (
                    'id' => 'fixed_header',
                    'type' => 'switch',
                    'title' => 'Fixed header',
                    'default' => true
                ),
                array (
                    'id' => 'header_overlap',
                    'type' => 'switch',
                    'title' => 'Header overlaps the content',
                    'default' => true,
                ),
                array (
                    'id' => 'header_color',
                    'type' => 'select',
                    'title' => 'Header text color',
                    'options' => array (
                        'dark' => 'Dark',
                        'white' => 'White',
                    ),
                    'default' => 'white'
                ),
                array (
                    'id' => 'header_bg',
                    'type' => 'background',
                    'title' => 'Header background',
                    'output' => array('.main-header')
                ),
                array (
                    'id' => 'top_bar',
                    'type' => 'switch',
                    'title' => 'Enable top bar',
                    'default' => true,
                ),
                array (
                    'id' => 'top_bar_bg',
                    'type' => 'background',
                    'title' => 'Top bar background',
                    'output' => array('.top-bar')
                ),
                array (
                    'id' => 'top_bar_color',
                    'type' => 'select',
                    'title' => 'Top bar text color',
                    'options' => array (
                        'dark' => 'Dark',
                        'white' => 'White',
                    ),
                    'default' => 'white'
                ),
                array (
                    'id' => 'header_custom_block',
                    'type' => 'editor',
                    'title' => 'Header custom HTML',
                    'required' => array(
                        array( 'header_type', 'equals', 'standard')
                    )
                ),
                array (
                    'id' => 'logo',
                    'type' => 'media',
                    'desc' => 'Upload image: png, jpg or gif file',
                    'title' => 'Logo image',
                ),
                array (
                    'id' => 'logo_fixed',
                    'type' => 'media',
                    'desc' => 'Upload image: png, jpg or gif file',
                    'title' => 'Logo image for fixed header',
                ),
                array (
                    'id'        => 'logo_width',
                    'type'      => 'slider',
                    'title'     => 'Logo max width',
                    "default"   => 200,
                    "min"       => 50,
                    "step"      => 1,
                    "max"       => 500,
                    'display_value' => 'label'
                ),
                array (
                    'id' => 'top_links',
                    'type' => 'switch',
                    'title' => 'Enable Sign In link',
                    'default' => true,
                ),
                array (
                    'id' => 'search_form',
                    'type' => 'switch',
                    'title' => 'Enable search form in header',
                    'default' => true,
                ),
                array (
                    'id' => 'top_panel',
                    'type' => 'switch',
                    'title' => 'Enable top panel',
                    'default' => true,
                ),
                array (
                    'id' => 'shopping_cart_icon',
                    'type' => 'select',
                    'title' => 'Shopping cart icon',
                    'options' => array (
                        1 => 'Default',
                        2 => 'Additional',
                        3 => 'Additional 2',
                    ),
                    'default' => 1
                ),
                array (
                    'id' => 'shopping_cart_icon_bg',
                    'type' => 'switch',
                    'title' => 'Icon with background',
                    'default' => false,
                ),
                array (
                    'id' => 'favicon_label',
                    'type' => 'switch',
                    'title' => 'Show number of cart items on favicon',
                    'default' => true,
                ),
                array (
                    'id' => 'cart_badge_bg',
                    'type' => 'color_rgba',
                    'title' => 'Background color for cart number label',
                    'output' => array(
                        'background-color' => '.header-color-inherit .et-wishlist-widget .wishlist-count, .header-color-dark .et-wishlist-widget .wishlist-count, .header-color-white .et-wishlist-widget .wishlist-count, .cart-bag .badge-number, .shopping-container.ico-design-2 .cart-bag .badge-number, .shopping-container.ico-design-3 .cart-bag .badge-number, .shopping-container.ico-design-1.ico-bg-yes .badge-number'
                    )
                ),
                array (
                    'id' => 'cart_icon_label',
                    'type' => 'select',
                    'title' => 'Label position',
                    'options' => array (
                        'top' => 'Top',
                        'bottom' => 'Bottom',
                    ),
                    'default' => 'top'
                ),
            ),
        ) );

        Redux::setSection( $opt_name, array(
            'title' => 'Breadcrumbs',
            'id' => 'general-header-breadcrumbs',
            'icon' => 'el-icon-cog',
            'subsection' => true,
            'fields' => array (
                array (
                    'id' => 'breadcrumb_type',
                    'type' => 'select',
                    'title' => 'Breadcrumbs Style',
                    'options' => array (
                        'default' => 'Align center',
                        'left' => 'Align left',
                        'left2' => 'Left inline',
                        'disable' => 'Disable',
                    ),
                    'default' => 'default'
                ),
                array (
                    'id' => 'breadcrumb_size',
                    'type' => 'select',
                    'title' => 'Breadcrumbs size',
                    'options' => array (
                        'small' => 'Small',
                        'large' => 'Large',
                    ),
                    'default' => 'large'
                ),
                array (
                    'id' => 'breadcrumb_effect',
                    'type' => 'select',
                    'title' => 'Breadcrumbs effect',
                    'options' => array (
                        'none' => 'None',
                        'mouse' => 'Parallax on mouse move',
                        'text-scroll' => 'Text animation on scroll',
                    ),
                    'default' => 'mouse'
                ),
                array (
                    'id' => 'breadcrumb_bg',
                    'type' => 'background',
                    'title' => 'Breadcrumbs background',
                    'default' => array(
                        'background-color' => '#dc5958',
                        'background-image' => 'http://8theme.com/import/xstore/wp-content/uploads/2016/05/breadcrumb-1.png'
                    )
                ),
                array (
                    'id' => 'breadcrumb_color',
                    'type' => 'select',
                    'title' => 'Breadcrumbs text color',
                    'options' => array (
                        'dark' => 'Dark',
                        'white' => 'White',
                    ),
                    'default' => 'white'
                ),
                array (
                    'id' => 'return_to_previous',
                    'type' => 'switch',
                    'title' => '"Back to previous page" button',
                    'default' => true,
                ),
                array (
                    'id' => 'breadcrumb_padding',
                    'type' => 'spacing',
                    'title' => 'Breadcrumbs padding',
                    'output' => array('.page-heading, .et-header-overlap .page-heading, .et-header-overlap .page-heading.bc-size-small, .page-heading.bc-size-small'),
                    'units'          => array('em', 'px'),
                    'units_extended' => 'false',
                    'default' => ''
                ),
                array (
                    'id' => 'bc_title_font',
                    'type' => 'typography',
                    'title' => 'Breadcrumbs title font',
                    'output' => '.page-heading .title, .page-heading.bc-size-small .title',
                    'text-align' => false,
                    'text-transform' => true,
                ),
                array (
                    'id' => 'bc_breadcrumbs_font',
                    'type' => 'typography',
                    'title' => 'Breadcrumbs font',
                    'output' => '.woocommerce-breadcrumb, #breadcrumb, .bbp-breadcrumb, .woocommerce-breadcrumb a, #breadcrumb a, .bbp-breadcrumb a, .woocommerce-breadcrumb .delimeter, #breadcrumb .delimeter, .bbp-breadcrumb .delimeter, .page-heading.bc-type-left2 .back-history, .page-heading.bc-type-left2 .title, .page-heading.bc-type-left2 .woocommerce-breadcrumb a, .page-heading.bc-type-left2 .breadcrumbs a',
                    'text-align' => false,
                    'text-transform' => true,
                ),
                array (
                    'id' => 'bc_return_font',
                    'type' => 'typography',
                    'title' => '"Return to previous page" font',
                    'output' => '.page-heading .back-history',
                    'text-align' => false,
                    'text-transform' => true,
                ),
            ),
        ) );


        Redux::setSection( $opt_name, array(
            'title' => 'Footer',
            'id' => 'general-footer',
            'subsection' => true,
            'icon' => 'el-icon-cog',
            'fields' => array (
                array (
                    'id' => 'footer_columns',
                    'type' => 'select',
                    'title' => 'Footer columns',
                    'options' => array (
                        1 => '1 Column',
                        2 => '2 Columns',
                        3 => '3 Columns',
                        4 => '4 Columns',
                    ),
                    'default' => 4
                ),
                array (
                    'id' => 'footer_demo',
                    'type' => 'switch',
                    'title' => 'Show footer demo blocks',
                    'desc' => 'Will be shown if footer sidebars are empty',
                    'default' => true,
                ),
                // array (
                //     'id' => 'footer_fixed',
                //     'type' => 'switch',
                //     'title' => 'Footer fixed',
                //     'default' => true,
                // ),
                array (
                    'id' => 'to_top',
                    'type' => 'switch',
                    'title' => '"Back To Top" button',
                    'default' => true,
                ),
                array (
                    'id' => 'to_top_mobile',
                    'type' => 'switch',
                    'title' => '"Back To Top" button on mobile',
                    'default' => true,
                ),
            ),
        ));

        Redux::setSection( $opt_name, array(
            'title' => '404 page',
            'id' => 'general-page-not-found',
            'subsection' => true,
            'icon' => 'el-icon-cog',
            'fields' => array (
                array (
                    'id' => '404_text',
                    'type' => 'editor',
                    'title' => '404 page content'
                ),
            ),
        ));

        Redux::setSection( $opt_name, array(
            'title' => 'Facebook Login',
            'id' => 'general-facebook',
            'subsection' => true,
            'icon' => 'el-icon-facebook',
            'fields' => array (
                array(
                    'id'   => 'fb_info',
                    'type' => 'info',
                    'desc' => 'To create Facebook APP ID follow the instructions <a href="https://developers.facebook.com/docs/apps/register" target="_blank">https://developers.facebook.com/docs/apps/register</a>'
                ),
                array (
                    'id' => 'facebook_app_id',
                    'type' => 'text',
                    'title' => 'Facebook APP ID'
                ),
                array (
                    'id' => 'facebook_app_secret',
                    'type' => 'text',
                    'title' => 'Facebook APP SECRET'
                ),
            ),
        ));


        Redux::setSection( $opt_name, array(
            'title' => 'Share buttons',
            'id' => 'general-share',
            'subsection' => true,
            'icon' => 'el-icon-share',
            'fields' => array (
                array (
                    'id' => 'share_twitter',
                    'type' => 'switch',
                    'title' => 'Share twitter',
                    'default' => true,
                ),
                array (
                    'id' => 'share_facebook',
                    'type' => 'switch',
                    'title' => 'Share facebook',
                    'default' => true,
                ),
                array (
                    'id' => 'share_vk',
                    'type' => 'switch',
                    'title' => 'Share vk',
                    'default' => true,
                ),
                array (
                    'id' => 'share_pinterest',
                    'type' => 'switch',
                    'title' => 'Share pinterest',
                    'default' => true,
                ),
                array (
                    'id' => 'share_google',
                    'type' => 'switch',
                    'title' => 'Share google',
                    'default' => true,
                ),
                array (
                    'id' => 'share_mail',
                    'type' => 'switch',
                    'title' => 'Share mail',
                    'default' => true,
                ),
            ),
        ));

        Redux::setSection( $opt_name, array(
            'title' => 'Styling',
            'id' => 'style',
            'icon' => 'el-icon-picture',
        ) );


        Redux::setSection( $opt_name, array(
            'title' => 'Content',
            'id' => 'style-content',
            'icon' => 'el-icon-picture',
            'subsection' => true,
            'fields' => array (
                array (
                    'id' => 'dark_styles',
                    'type' => 'switch',
                    'title' => 'Dark version',
                ),
                array (
                    'id' => 'activecol',
                    'type' => 'color',
                    'title' => 'Main Color',
                    'default' => '#d64444'
                ),
                array (
                    'id' => 'background_img',
                    'type' => 'background',
                    'output' => 'body',
                    'title' => 'Site Background',
                ),

                array (
                    'id' => 'container_bg',
                    'type' => 'color_rgba',
                    'title' => 'Container Background Color',
                    'output' => array(
                        'background-color' =>'.select2-results, .select2-drop, .select2-container .select2-choice, .form-control, .page-wrapper, .cart-popup-container, select, .quantity input[type="number"], .emodal, input[type="text"], input[type="email"], input[type="password"], input[type="tel"], textarea, #searchModal, .quick-view-popup, #etheme-popup, .et-wishlist-widget .wishlist-dropdown, textarea.form-control, textarea'
                    )
                ),
                array (
                    'id' => 'forms_inputs_bg',
                    'type' => 'color_rgba',
                    'title' => 'Forms inputs Color',
                    'output' => array(
                        'border-color' =>'.select2-results, .select2-drop, .select2-container .select2-choice, .form-control, select, .quantity input[type="number"], .emodal, input[type="text"], input[type="email"], input[type="password"], input[type="tel"], textarea, textarea.form-control, textarea',
                        'background-color' =>'.select2-results, .select2-drop, .select2-container .select2-choice, .form-control, select, .quantity input[type="number"], .emodal, input[type="text"], input[type="email"], input[type="password"], input[type="tel"], textarea, textarea.form-control, textarea'
                    )
                ),
            ),
        ));

        
        Redux::setSection( $opt_name, array(
            'title' => 'Navigation',
            'id' => 'style-nav',
            'icon' => 'el-icon-picture',
            'subsection' => true,
            'fields' => array (
                array (
                    'id' => 'menu_align',
                    'type' => 'select',
                    'title' => 'Menu links align',
                    'options' => array (
                        'center' => 'Center',
                        'left' => 'Left',
                        'right' => 'Right',
                    ),
                    'default' => 'center'
                ),
            ),
        ));


        Redux::setSection( $opt_name, array(
            'title' => 'Footer',
            'id' => 'style-footer',
            'subsection' => true,
            'icon' => 'el-icon-cog',
            'fields' => array (
                array (
                    'id' => 'footer_color',
                    'type' => 'select',
                    'title' => 'Footer text color scheme',
                    'options' => array (
                        'light' => 'Light',
                        'dark' => 'Dark',
                    ),
                    'default' => 'light'
                ),
                array (
                    'id' => 'footer-links',
                    'type' => 'link_color',
                    'title' => 'Footer Links',
                    'output' => array('.footer a, .vc_wp_posts .widget_recent_entries li a')
                ),
                array (
                    'id' => 'footer_bg_color',
                    'type' => 'background',
                    'title' => 'Footer Background Color',
                    'output' => array(
                        'background' => 'footer.footer'
                    )
                ),
                array (
                    'id' => 'footer_padding',
                    'type' => 'spacing',
                    'title' => 'Footer padding',
                    'output' => array('.footer'),
                    'units'          => array('em', 'px'),
                    'units_extended' => 'false',
                    'default' => ''
                ),
            ),
        ));

        Redux::setSection( $opt_name, array(
            'title' => 'Copyrights',
            'id' => 'style-copyrights',
            'subsection' => true,
            'icon' => 'el-icon-cog',
            'fields' => array (
                array (
                    'id' => 'copyrights_color',
                    'type' => 'select',
                    'title' => 'Copyrights text color scheme',
                    'options' => array (
                        'light' => 'Light',
                        'dark' => 'Dark',
                    ),
                    'default' => 'light'
                ),
                array (
                    'id' => 'copyrights-links',
                    'type' => 'link_color',
                    'title' => 'Copyrights Links',
                    'output' => array('.footer-bottom a')
                ),
                array (
                    'id' => 'copyrights_bg_color',
                    'type' => 'background',
                    'title' => 'Copyrights Background Color',
                    'output' => array(
                        'background' => '.footer-bottom'
                    )
                ),
                array (
                    'id' => 'copyrights_padding',
                    'type' => 'spacing',
                    'title' => 'Copyrights padding',
                    'output' => array('.footer-bottom'),
                    'units'          => array('em', 'px'),
                    'units_extended' => 'false',
                    'default' => ''
                ),
            ),
        ));
        
        Redux::setSection( $opt_name, array(
            'title' => 'Custom CSS',
            'id' => 'style-custom_css',
            'icon' => 'el-icon-css',
            'subsection' => true,
            'fields' => array (
                array (
                    'id' => 'custom_css',
                    'type' => 'ace_editor',
                    'mode' => 'css',
                    'title' => 'Global Custom CSS',
                ),
                array (
                    'id' => 'custom_css_desktop',
                    'type' => 'ace_editor',
                    'mode' => 'css',
                    'title' => 'Custom CSS for desktop',
                ),
                array (
                    'id' => 'custom_css_tablet',
                    'type' => 'ace_editor',
                    'mode' => 'css',
                    'title' => 'Custom CSS for tablet',
                ),
                array (
                    'id' => 'custom_css_wide_mobile',
                    'type' => 'ace_editor',
                    'mode' => 'css',
                    'title' => 'Custom CSS for mobile landscape',
                ),
                array (
                    'id' => 'custom_css_mobile',
                    'type' => 'ace_editor',
                    'mode' => 'css',
                    'title' => 'Custom CSS for mobile',
                ),
            ),
        ));

        Redux::setSection( $opt_name, array(
            'title' => 'Typography',
            'id' => 'typography',
            'icon' => 'el-icon-font',
        ));

        Redux::setSection( $opt_name, array(
            'title' => 'Page',
            'id' => 'typography-page',
            'icon' => 'el-icon-font',
            'subsection' => true,
            'fields' => array(
                array (
                    'id' => 'sfont',
                    'type' => 'typography',
                    'title' => 'Body Font',
                    'output' => 'body, .quantity input[type="number"]',
                    'text-align' => false,
                    'text-transform' => true,
                ),
                array (
                    'id' => 'headings',
                    'type' => 'typography',
                    'title' => 'Headings',
                    'output' => 'h1, h2, h3, h4, h5, h6, .title h3, blockquote, .share-post .share-title, .sidebar-widget .tabs .tab-title, .widget-title, .related-posts .title span, .posts-slider article h2 a, .content-product .product-title a, table.cart .product-details a, .product_list_widget .product-title a, .woocommerce table.wishlist_table .product-name a, .comment-reply-title, .et-tabs .vc_tta-title-text, .single-product-right .product-information-inner .product_title, .single-product-right .product-information-inner h1.title, .post-heading h2 a, .sidebar .recent-posts-widget .post-widget-item h4 a, .et-tabs-wrapper .tabs .accordion-title span',
                    'text-align' => false,
                    'font-size' => false,
                    'text-transform' => true,
                ),
            )
        ));


        Redux::setSection( $opt_name, array(
            'title' => 'Navigation',
            'id' => 'typography-menu',
            'icon' => 'el-icon-font',
            'subsection' => true,
            'fields' => array(
                array (
                    'id' => 'menu_level_1',
                    'type' => 'typography',
                    'title' => 'Menu first level font',
                    'output' => '.menu-wrapper .menu > li > a, .mobile-menu-wrapper .menu > li > a, .mobile-menu-wrapper .links li a',
                    'text-align' => false,
                    'text-transform' => true,
                ),
                array (
                    'id' => 'menu_level_2',
                    'type' => 'typography',
                    'title' => 'Menu second level',
                    'output' => '.item-design-mega-menu .nav-sublist-dropdown .item-level-1 > a',
                    'text-align' => false,
                    'text-transform' => true,
                ),
                array (
                    'id' => 'menu_level_3',
                    'type' => 'typography',
                    'title' => 'Menu third level',
                    'output' => '.item-design-dropdown .nav-sublist-dropdown ul > li > a, .item-design-mega-menu .nav-sublist-dropdown .item-link',
                    'text-align' => false,
                    'text-transform' => true,
                ),
            )
        ));



        if( current_theme_supports('woocommerce') ) {

            Redux::setSection( $opt_name, array(
                'title' => 'E-Commerce',
                'id' => 'shop',
                'icon' => 'el-icon-shopping-cart',
            ));

            Redux::setSection( $opt_name, array(
                'title' => 'Shop',
                'id' => 'shop-shop',
                'icon' => 'el-icon-shopping-cart',
                'subsection' => true,
                'fields' => array (
                    array (
                        'id' => 'cart_widget',
                        'type' => 'switch',
                        'title' => 'Enable cart widget in header',
                        'default' => true,
                    ),
                    array (
                        'id' => 'just_catalog',
                        'type' => 'switch',
                        'description' => 'Disable "Add To Cart" button and shopping cart',
                        'title' => 'Just Catalog',
                    ),
                    array (
                        'id' => 'top_toolbar',
                        'type' => 'switch',
                        'title' => 'Show products toolbar on the shop page',
                        'default' => true,
                    ),
                    array (
                        'id' => 'filters_columns',
                        'type' => 'select',
                        'title' => 'Widgets columns for filters area',
                        'options' => array (
                            2 => '2',
                            3 => '3',
                            4 => '4',
                            5 => '5',
                        ),
                        'default' => 3
                    ),
                    array (
                        'id' => 'filter_opened',
                        'type' => 'switch',
                        'title' => 'Open filter by default',
                        'default' => false,
                    ),
                    array (
                        'id' => 'cats_accordion',
                        'type' => 'switch',
                        'title' => 'Enable Navigation Accordion',
                        'default' => true,
                    ),
                    array (
                        'id' => 'out_of_icon',
                        'type' => 'switch',
                        'title' => 'Enable "Out of stock" icon',
                        'default' => true,
                    ),
                    array (
                        'id' => 'sale_icon',
                        'type' => 'switch',
                        'title' => 'Enable "Sale" icon',
                        'default' => true,
                    ),
                    array (
                        'id' => 'sale_percentage',
                        'type' => 'switch',
                        'title' => 'Show sale percentage',
                        'desc' => 'For simple and external product types',
                        'default' => false,
                    ),
                    array (
                        'id' => 'product_bage_banner',
                        'type' => 'editor',
                        'desc' => 'Upload image: png, jpg or gif file',
                        'title' => 'Product Page Banner',
                    ),
                    array (
                        'id' => 'empty_cart_content',
                        'type' => 'editor',
                        'title' => 'Text for empty cart',
                        'default' => '<h1 style="text-align: center;">YOUR SHOPPING CART IS EMPTY</h1>
<p style="text-align: center;">We invite you to get acquainted with an assortment of our shop.
Surely you can find something for yourself!</p> ',
                    ),
                    // array (
                    //     'id' => 'register_text',
                    //     'type' => 'editor',
                    //     'title' => 'Text for registration page',
                    //     'default' => 'text',
                    // ),
                ),
            ));

            Redux::setSection( $opt_name, array(
                'title' => 'Categories',
                'id' => 'shop-categories',
                'icon' => 'el-icon-shopping-cart',
                'subsection' => true,
                'fields' => array (
                    array (
                        'id' => 'cat_style',
                        'type' => 'select',
                        'title' => 'Categories style',
                        'options' => array (
                            'default' => 'Default',
                            'with-bg' => 'Title with background',
                            'zoom' => 'Zoom' ,
                            'diagonal' => 'Diagonal',
                            'classic' => 'Classic',
                        ),
                        'default' => 'default'
                    ),
                    array (
                        'id' => 'cat_text_color',
                        'type' => 'select',
                        'title' => 'Categories text color',
                        'options' => array (
                            'dark' => 'Dark',
                            'white' => 'Light',
                        ),
                        'default' => 'dark'
                    ),
                    array (
                        'id' => 'cat_valign',
                        'type' => 'select',
                        'title' => 'Text vertical align',
                        'options' => array (
                            'center' => 'Center',
                            'top' => 'Top',
                            'bottom' => 'Bottom',
                        ),
                        'default' => 'center'
                    ),
                ),
            ));

            Redux::setSection( $opt_name, array(
                'title' => 'Products Page Layout',
                'id' => 'shop-product_grid',
                'icon' => 'el-icon-view-mode',
                'subsection' => true,
                'fields' => array (
                    array (
                        'id' => 'view_mode',
                        'type' => 'select',
                        'title' => 'Products view mode',
                        'options' => array (
                            'grid_list' => 'Grid/List',
                            'list_grid' => 'List/Grid',
                            'grid' => 'Only Grid',
                            'list' => 'Only List',
                        ),
                        'default' => 'grid_list'
                    ),
                    array (
                        'id' => 'prodcuts_per_row',
                        'type' => 'select',
                        'title' => 'Products per row',
                        'options' => array (
                            1 => '1',
                            2 => '2',
                            3 => '3',
                            4 => '4',
                            5 => '5',
                            6 => '6',
                        ),
                        'default' => 3
                    ),
                    array (
                        'id' => 'products_per_page',
                        'type' => 'text',
                        'title' => 'Products per page',
                    ),
                    array (
                        'id' => 'et_ppp_options',
                        'type' => 'text',
                        'title' => 'Per page variants separated by commas',
                        'default' => '12,24,36,-1',
                        'desc' => 'For example: 12,24,36,-1. Set -1 to show all products'
                    ),
                    array (
                        'id' => 'grid_sidebar',
                        'type' => 'image_select',
                        'desc' => 'Sidebar position',
                        'title' => 'Layout',
                        'options' => array (
                            'without' => array (
                                'alt' => 'full width',
                                'img' => ETHEME_CODE_IMAGES . 'layout/full-width.png',
                            ),
                            'left' => array (
                                'alt' => 'Left Sidebar',
                                'img' => ETHEME_CODE_IMAGES . 'layout/left-sidebar.png',
                            ),
                            'right' => array (
                                'alt' => 'Right Sidebar',
                                'img' => ETHEME_CODE_IMAGES . 'layout/right-sidebar.png',
                            ),
                        ),
                        'default' => 'left'
                    ),
                    array (
                        'id' => 'sidebar_for_mobile',
                        'type' => 'select',
                        'title' => 'Sidebar position for mobile',
                        'options' => array (
                            'top' => 'Top',
                            'bottom' => 'Bottom',
                        ),
                        'default' => 'top',
                    ),
                    array (
                        'id' => 'shop_sidebar_hide_mobile',
                        'type' => 'switch',
                        'title' => 'Hide sidebar for mobile devices',
                    ),
                    array (
                        'id' => 'shop_full_width',
                        'type' => 'switch',
                        'title' => 'Full width',
                    ),
                    array (
                        'id' => 'products_masonry',
                        'type' => 'switch',
                        'title' => 'Products masonry',
                        'default' => false,
                    ),
                    array (
                        'id' => 'product_img_hover',
                        'type' => 'select',
                        'title' => 'Image hover effect',
                        'options' => array (
                            'disable' => 'Disable',
                            'swap' => 'Swap',
                            'slider' => 'Images Slider',
                        ),
                        'default' => 'slider',
                    ),
                    array (
                        'id' => 'product_view',
                        'type' => 'select',
                        'title' => 'Buttons hover',
                        'options' => array (
                            'disable' => 'Disable',
                            'default' => 'Default',
                            'mask' => 'Buttons on hover',
                            'mask2' => 'Buttons on hover 2',
                            'info' => 'Information mask',
                            'booking' => 'Booking',
                        ),
                        'default' => 'disable',
                    ),
                    array (
                        'id' => 'product_view_color',
                        'type' => 'select',
                        'title' => 'Hover Color Scheme',
                        'options' => array (
                            'white' => 'White',
                            'dark' => 'Dark',
                            'transparent' => 'Transparent',
                        ),
                        'default' => 'white',
                        'required' => array(
                            array('product_view','equals', array('info','mask','mask2')),
                        )
                    ),
                    array (
                        'id' => 'hide_buttons_mobile',
                        'type' => 'switch',
                        'title' => 'Hide hover buttons on mobile',
                        'default' => false,
                    ),
                    array (
                        'id' => 'product_page_productname',
                        'type' => 'switch',
                        'title' => 'Show product name',
                        'default' => true,
                    ),
                    array (
                        'id' => 'product_page_cats',
                        'type' => 'switch',
                        'title' => 'Show product categories',
                    ),
                    array (
                        'id' => 'product_page_price',
                        'type' => 'switch',
                        'title' => 'Show Price',
                        'default' => true,
                    ),
                    array (
                        'id' => 'product_page_addtocart',
                        'type' => 'switch',
                        'title' => 'Show "Add to cart" button',
                        'default' => true,
                    ),
                ),
            ));


            Redux::setSection( $opt_name, array(
                'title' => 'Single Product Page',
                'id' => 'shop-single_product',
                'subsection' => true,
                'icon' => 'el-icon-indent-left',
                'fields' => array (
                    array (
                        'id' => 'single_sidebar',
                        'type' => 'image_select',
                        'title' => 'Sidebar position',
                        'options' => array (
                            'without' => array (
                                'alt' => 'Without Sidebar',
                                'img' => ETHEME_CODE_IMAGES . 'layout/full-width.png',
                            ),
                            'left' => array (
                                'alt' => 'Left Sidebar',
                                'img' => ETHEME_CODE_IMAGES . 'layout/left-sidebar.png',
                            ),
                            'right' => array (
                                'alt' => 'Right Sidebar',
                                'img' => ETHEME_CODE_IMAGES . 'layout/right-sidebar.png',
                            ),
                        ),
                        'default' => 'without'
                    ),
                    array (
                        'id' => 'single_layout',
                        'type' => 'image_select',
                        'title' => 'Page Layout',
                        'options' => array (
                            'small' => array (
                                'alt' => 'Small',
                                'img' => ETHEME_CODE_IMAGES . 'layout/product-small.png',
                            ),
                            'default' => array (
                                'alt' => 'Default',
                                'img' => ETHEME_CODE_IMAGES . 'layout/product-medium.png',
                            ),
                            'xsmall' => array (
                                'alt' => 'Thin description',
                                'img' => ETHEME_CODE_IMAGES . 'layout/product-thin.png',
                            ),
                            'large' => array (
                                'alt' => 'Large',
                                'img' => ETHEME_CODE_IMAGES . 'layout/product-large.png',
                            ),
                            'fixed' => array (
                                'alt' => 'Fixed content',
                                'img' => ETHEME_CODE_IMAGES . 'layout/product-fixed.png',
                            ),
                            'center' => array (
                                'alt' => 'Image center',
                                'img' => ETHEME_CODE_IMAGES . 'layout/product-center.png',
                            ),
                            'wide' => array (
                                'alt' => 'Wide',
                                'img' => ETHEME_CODE_IMAGES . 'layout/product-wide.png',
                            ),
                            'right' => array (
                                'alt' => 'Image right',
                                'img' => ETHEME_CODE_IMAGES . 'layout/product-right.png',
                            ),
                            'booking' => array (
                                'alt' => 'Booking',
                                'img' => ETHEME_CODE_IMAGES . 'layout/product-booking.png',
                            ),
                        ),
                        'default' => 'default'
                    ),
                    array (
                        'id' => 'single_product_hide_sidebar',
                        'type' => 'switch',
                        'title' => 'Hide sidebar on mobile',
                        'default' => false
                    ),
                    array (
                        'id' => 'fixed_images',
                        'type' => 'switch',
                        'title' => 'Fixed product image',
                        'default' => false,
                        'required' => array(
                            array('single_layout','equals', array('small', 'default', 'xsmall', 'large', 'wide', 'right')),
                        )
                    ),
                    array (
                        'id' => 'fixed_content',
                        'type' => 'switch',
                        'title' => 'Fixed product content',
                        'default' => false,
                        'required' => array(
                            array('single_layout','equals', array('small', 'default', 'xsmall', 'large', 'wide', 'right')),
                        )
                    ),
                    array (
                        'id' => 'product_name_signle',
                        'type' => 'switch',
                        'title' => 'Show product name above the price',
                        'default' => true,
                    ),
                    array (
                        'id' => 'upsell_location',
                        'type' => 'select',
                        'title' => 'Location of upsell products',
                        'options' => array (
                            'sidebar' => 'Sidebar',
                            'after_content' => 'After content',
                        ),
                    ),
                    array (
                        'id' => 'ajax_add_to_cart',
                        'type' => 'switch',
                        'title' => 'AJAX add to cart for simple products',
                        'default' => true,
                    ),
                    array (
                        'id' => 'product_photoswipe',
                        'type' => 'switch',
                        'title' => 'Lightbox for product images',
                        'default' => true,
                    ),
                    array (
                        'id' => 'show_related',
                        'type' => 'switch',
                        'title' => 'Display related products',
                        'default' => true,
                    ),
                    array (
                        'id' => 'related_limit',
                        'type' => 'text',
                        'title' => 'Display related products',
                        'default' => 10,
                        'required' => array(
                            array('show_related','equals', true),
                        )
                    ),
                    array (
                        'id' => 'thumbs_slider',
                        'type' => 'switch',
                        'title' => 'Enable slider for gallery thumbnails',
                        'default' => true,
                    ),
                    array (
                        'id' => 'product_posts_links',
                        'type' => 'switch',
                        'title' => 'Show Next/Previous product navigation',
                        'default' => true,
                    ),
                    array (
                        'id' => 'share_icons',
                        'type' => 'switch',
                        'title' => 'Show share buttons',
                        'default' => true,
                    ),
                    array (
                        'id' => 'tabs_type',
                        'type' => 'select',
                        'title' => 'Tabs type',
                        'options' => array (
                            'tabs-default' => 'Default',
                            'left-bar' => 'Left Bar',
                            'accordion' => 'Accordion',
                            'disable' => 'Disable',
                        ),
                        'default' => 'tabs-default'
                    ),
                    array (
                        'id' => 'tabs_scroll',
                        'type' => 'switch',
                        'title' => 'Tabs content scroll',
                        'default' => false,
                        'required' => array(
                            array('tabs_type', 'equals', 'accordion'),
                        )
                    ),
                    array(
                        'id'        => 'tab_height',
                        'type'      => 'slider',
                        'title'     => __('Tab content height', 'redux-framework-demo'),
                        "default"   => 250,
                        "min"       => 50,
                        "step"      => 1,
                        "max"       => 800,
                        'display_value' => 'label',
                        'required' => array(
                            array('tabs_type', 'equals', 'accordion'),
                            array('tabs_scroll', 'equals', true),
                        )
                    ),
                    array (
                        'id' => 'tabs_location',
                        'type' => 'select',
                        'title' => 'Location of product tabs',
                        'options' => array (
                            'after_image' => 'Next to image',
                            'after_content' => 'Under content',
                        ),
                        'default' => 'after_content',
                        'required' => array(
                            array('tabs_type','!=', 'disable'),
                        )
                    ),
                    array (
                        'id' => 'reviews_position',
                        'type' => 'select',
                        'title' => 'Reviews position',
                        'options' => array (
                            'tabs' => 'Tabs',
                            'outside' => 'Next to tabs',
                        ),
                        'default' => 'tabs',
                        'required' => array(
                            array('tabs_type','!=', 'disable'),
                        )
                    ),
                    array (
                        'id' => 'custom_tab_title',
                        'type' => 'text',
                        'title' => 'Custom Tab Title',
                        'required' => array(
                            array('tabs_type','!=', 'disable'),
                        ),
                    ),
                    array (
                        'id' => 'custom_tab',
                        'type' => 'editor',
                        'desc' => 'Enter custom content you would like to output to the product custom tab (for all products)',
                        'title' => 'Custom tab content',
                        'required' => array(
                            array('tabs_type','!=', 'disable'),
                        ),
                    ),
                ),
            ));


            Redux::setSection( $opt_name, array(
                'title' => 'Quick View',
                'id' => 'shop-quick_view',
                'subsection' => true,
                'icon' => 'el-icon-zoom-in',
                'fields' => array (
                    array (
                        'id' => 'quick_view',
                        'type' => 'switch',
                        'title' => 'Enable Quick View',
                        'default' => true,
                    ),
                    array (
                        'id' => 'quick_images',
                        'type' => 'select',
                        'title' => 'Product images',
                        'options' => array (
                            'slider' => 'Slider',
                            'single' => 'Single',
                        ),
                        'default' => 'slider',
                        'required' => array(
                            array('quick_view','equals', true),
                        )
                    ),
                    array (
                        'id' => 'quick_view_layout',
                        'type' => 'select',
                        'title' => 'Quick view layout',
                        'options' => array (
                            'default' => 'Default',
                            'centered' => 'Centered',
                        ),
                        'default' => 'default',
                        'required' => array(
                            array('quick_view','equals', true),
                        )
                    ),
                    array (
                        'id' => 'quick_product_name',
                        'type' => 'switch',
                        'title' => 'Product name',
                        'default' => true,
                        'required' => array(
                            array('quick_view','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'quick_categories',
                        'type' => 'switch',
                        'title' => 'Product categories',
                        'default' => true,
                        'required' => array(
                            array('quick_view','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'quick_price',
                        'type' => 'switch',
                        'title' => 'Price',
                        'default' => true,
                        'required' => array(
                            array('quick_view','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'quick_rating',
                        'type' => 'switch',
                        'title' => 'Product star rating',
                        'default' => true,
                        'required' => array(
                            array('quick_view','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'quick_descr',
                        'type' => 'switch',
                        'title' => 'Short description',
                        'default' => true,
                        'required' => array(
                            array('quick_view','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'quick_add_to_cart',
                        'type' => 'switch',
                        'title' => 'Add to cart',
                        'default' => true,
                        'required' => array(
                            array('quick_view','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'quick_share',
                        'type' => 'switch',
                        'title' => 'Share icons',
                        'default' => true,
                        'required' => array(
                            array('quick_view','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'product_link',
                        'type' => 'switch',
                        'title' => 'Product link',
                        'default' => true,
                        'required' => array(
                            array('quick_view','equals', true),
                        ),
                    ),

                ),
            ));



            Redux::setSection( $opt_name, array(
                'title' => 'Promo Popup',
                'id' => 'shop-promo_popup',
                'subsection' => true,
                'icon' => 'el-icon-tag',
                'fields' => array (
                    array (
                        'id' => 'promo_popup',
                        'type' => 'switch',
                        'operator' => 'and',
                        'title' => 'Enable promo popup',
                        'default' => true,
                    ),
                    array (
                        'id' => 'promo_auto_open',
                        'type' => 'switch',
                        'title' => 'Open popup on enter',
                        'required' => array(
                            array('promo_popup','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'promo_open_scroll',
                        'type' => 'switch',
                        'title' => 'Open when scrolled to the bottom of the page',
                        'required' => array(
                            array('promo_auto_open','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'promo_link',
                        'type' => 'switch',
                        'operator' => 'and',
                        'title' => 'Show link in the top bar',
                        'default' => true,
                        'required' => array(
                            array('promo_popup','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'promo-link-text',
                        'type' => 'text',
                        'title' => 'Promo link text',
                        'default' => 'Newsletter',
                        'required' => array(
                            array('promo_popup','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'pp_content',
                        'type' => 'editor',
                        'operator' => 'and',
                        'title' => 'Popup content',
                        'default' => '<p>You can add any HTML here (admin -&gt; Theme Options -&gt; E-Commerce -&gt; Promo Popup).<br /> We suggest you create a static block and put it here using shortcode</p>',
                        'required' => array(
                            array('promo_popup','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'pp_width',
                        'type' => 'text',
                        'operator' => 'and',
                        'title' => 'Popup width',
                        'required' => array(
                            array('promo_popup','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'pp_height',
                        'type' => 'text',
                        'operator' => 'and',
                        'title' => 'Popup height',
                        'required' => array(
                            array('promo_popup','equals', true),
                        ),
                    ),
                    array (
                        'id' => 'pp_bg',
                        'type' => 'background',
                        'title' => 'Popup background',
                        'required' => array(
                            array('promo_popup','equals', true),
                        ),
                    ),
                ),
            ));

        }

        Redux::setSection( $opt_name, array(
            'title' => 'Blog & Portfolio',
            'id' => 'blog',
            'icon' => 'el-icon-wordpress',
        ));

        Redux::setSection( $opt_name, array(
            'title' => 'Blog Layout',
            'id' => 'blog-blog_page',
            'subsection' => true,
            'icon' => 'el-icon-wordpress',
            'fields' => array (
                array (
                    'id' => 'blog_layout',
                    'type' => 'image_select',
                    'title' => 'Blog Layout',
                    'options' => array(
                        'default' => array(
                            'title' => 'Default',
                            'img' => ETHEME_CODE_IMAGES . 'blog/posts1-1.png',
                        ),
                        'center' => array(
                            'title' => 'Center',
                            'img' => ETHEME_CODE_IMAGES . 'blog/posts-center.png',
                        ),
                        'grid' => array(
                            'title' => 'Grid',
                            'img' => ETHEME_CODE_IMAGES . 'blog/posts2-1.png',
                        ),
                        'timeline' => array(
                            'title' => 'Timeline',
                            'img' => ETHEME_CODE_IMAGES . 'blog/posts5-1.png',
                        ),
                        'small' => array(
                            'title' => 'List',
                            'img' => ETHEME_CODE_IMAGES . 'blog/posts3-1.png',
                        ),
                        'chess' => array(
                            'title' => 'Chess',
                            'img' => ETHEME_CODE_IMAGES . 'blog/posts-chess.png',
                        ),
                    ),
                    'default' => 'default',
                ),
                array (
                    'id' => 'blog_columns',
                    'type' => 'select',
                    'title' => 'Columns',
                    'options' => array (
                        2 => '2',
                        3 => '3',
                        4 => '4',
                    ),
                    'default' => 3,
                    'required' => array(
                        array('blog_layout','equals', array('grid')),
                    ),
                ),
                array (
                    'id' => 'blog_full_width',
                    'type' => 'switch',
                    'title' => 'Full width',
                    'required' => array(
                        array('blog_layout','equals', array('grid')),
                    ),
                ),
                array (
                    'id' => 'blog_hover',
                    'type' => 'select',
                    'title' => 'Blog image hover',
                    'options' => array (
                        'default' => 'Default',
                        'zoom' => 'Zoom',
                        'animated' => 'Animated',
                    ),
                    'default' => 'default',
                ),
                array (
                    'id' => 'blog_byline',
                    'type' => 'switch',
                    'title' => 'Show "byline" on the blog',
                    'default' => true,
                ),
                array (
                    'id' => 'read_more',
                    'type' => 'switch',
                    'title' => 'Show "Continue reading link"',
                    'default' => true,
                ),
                array (
                    'id' => 'views_counter',
                    'type' => 'switch',
                    'title' => 'Enable views counter',
                    'default' => true,
                ),
                array (
                    'id' => 'blog_sidebar',
                    'type' => 'image_select',
                    'title' => 'Sidebar position',
                    'options' => array (
                        'without' => array (
                            'alt' => 'Without Sidebar',
                            'img' => ETHEME_CODE_IMAGES . 'layout/full-width.png',
                        ),
                        'left' => array (
                            'alt' => 'Left Sidebar',
                            'img' => ETHEME_CODE_IMAGES . 'layout/left-sidebar.png',
                        ),
                        'right' => array (
                            'alt' => 'Right Sidebar',
                            'img' => ETHEME_CODE_IMAGES . 'layout/right-sidebar.png',
                        ),
                    ),
                    'default' => 'right'
                ),
                array (
                    'id' => 'blog_pagination_align',
                    'type' => 'select',
                    'title' => 'Pagination align',
                    'options' => array (
                        'left' => 'Left',
                        'center' => 'Center',
                        'right' => 'Right',
                    ),
                    'default' => 'right'
                ),
                array (
                    'id' => 'sticky_sidebar',
                    'type' => 'switch',
                    'title' => 'Enable sticky sidebar',
                    'default' => false,
                ),
                array (
                    'id' => 'excerpt_length',
                    'type' => 'text',
                    'title' => 'Excerpt length (words)',
                    'default' => 25,
                ),
                array (
                    'id' => 'blog_images_size',
                    'type' => 'text',
                    'title' => 'Images sizes for blog',
                    'subtitle' => __( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'js_composer' ),
                    'default' => 'large',
                ),
                array (
                    'id' => 'blog_related_images_size',
                    'type' => 'text',
                    'title' => 'Images sizes for related articles',
                    'subtitle' => __( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'js_composer' ),
                    'default' => 'medium',
                ),
            ),
        ));

        Redux::setSection( $opt_name, array(
            'title' => 'Single post',
            'id' => 'blog-single-post',
            'subsection' => true,
            'icon' => 'el-icon-wordpress',
            'fields' => array (
                array (
                    'id' => 'post_template',
                    'type' => 'image_select',
                    'title' => 'Post template',
                    'options' => array (
                        'default' => array(
                            'title' => 'Default',
                            'img' => ETHEME_CODE_IMAGES . 'blog/3.png',
                        ),
                        'full-width' => array(
                            'title' => 'Large',
                            'img' => ETHEME_CODE_IMAGES . 'blog/2.png',
                        ),
                        'large' => array(
                            'title' => 'Full width',
                            'img' => ETHEME_CODE_IMAGES . 'blog/1.png',
                        ),
                        'large2' => array(
                            'title' => 'Full width centered',
                            'img' => ETHEME_CODE_IMAGES . 'blog/5.png',
                        ),
                    ),
                    'default' => 'default'
                ),
                array (
                    'id' => 'blog_featured_image',
                    'type' => 'switch',
                    'title' => 'Display featured image on single post',
                    'default' => true,
                ),
                array (
                    'id' => 'post_share',
                    'type' => 'switch',
                    'operator' => 'and',
                    'title' => 'Show Share buttons',
                    'default' => true,
                ),
                array (
                    'id' => 'about_author',
                    'type' => 'switch',
                    'operator' => 'and',
                    'title' => 'Show About Author block',
                    'default' => false,
                ),
                array (
                    'id' => 'posts_links',
                    'type' => 'switch',
                    'title' => 'Posts previous/next buttons',
                    'default' => true,
                ),
                array (
                    'id' => 'post_related',
                    'type' => 'switch',
                    'operator' => 'and',
                    'title' => 'Show Related posts',
                    'default' => true,
                ),
                array (
                    'id' => 'related_query',
                    'type' => 'select',
                    'title' => 'Related query type',
                    'options' => array (
                        'categories' => 'Categories',
                        'tags' => 'Tags',
                    ),
                    'default' => 'categories',
                    'required' => array(
                        array('post_related','equals', true),
                    ),
                ),

            ),
        ));



        Redux::setSection( $opt_name, array(
            'title' => 'Portfolio',
            'id' => 'blog-portfolioo',
            'subsection' => true,
            'icon' => 'el-icon-briefcase',
            'fields' => array (
                array (
                    'id' => 'portfolio_style',
                    'type' => 'select',
                    'title' => 'Project grid style',
                    'options' => array (
                        'default' => 'With title',
                        'classic' => 'Classic',
                    ),
                    'default' => 'default'
                ),
                array (
                    'id' => 'portfolio_fullwidth',
                    'type' => 'switch',
                    'title' => 'Full width portfolio',
                    'default' => false
                ),
                array (
                    'id' => 'port_first_wide',
                    'type' => 'switch',
                    'title' => 'Make first project wide',
                    'default' => false
                ),
                array (
                    'id' => 'portfolio_columns',
                    'type' => 'select',
                    'title' => 'Columns',
                    'options' => array (
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                        6 => '6',
                    ),
                    'default' => 3
                ),
                array (
                    'id' => 'portfolio_margin',
                    'type' => 'select',
                    'title' => 'Portfolio item spacing',
                    'options' => array (
                        1 => '0',
                        5 => '5',
                        10 => '10',
                        15 => '15',
                        20 => '20',
                        30 => '30',
                    ),
                    'default' => 15
                ),
                array (
                    'id' => 'portfolio_count',
                    'type' => 'text',
                    'desc' => 'Use -1 to show all items',
                    'title' => 'Items per page',
                ),
            ),
        ));


        Redux::setSection( $opt_name, array(
            'title' => 'Import / Export',
            'id' => 'import',
            'icon'   => 'el-icon-refresh',
        ));

        Redux::setSection( $opt_name, array(
            'title' => 'Dummy content',
            'id' => 'import-dummy',
            'subsection' => true,
            'icon' => 'el-icon-inbox',
            'fields' => array (
                array(
                    'id'         => 'dummy-content',
                    'type'       => 'dummy_content',
                    'title'      => 'Install Dummy content'
                ),
            )
        ));

        Redux::setSection( $opt_name, array(
            'title'  => esc_html__( 'Options', 'xstore' ),
            'desc'   => esc_html__( 'Import and Export your theme settings from file, text or URL.', 'xstore' ),
            'id' => 'import-export',
            'subsection' => true,
            'icon'   => 'el-icon-refresh',
            'fields' => array(
                array(
                    'id'         => 'opt-import-export',
                    'type'       => 'import_export',
                    'title'      => 'Import Export',
                    'subtitle'   => 'Save and restore your theme options',
                    'full_width' => false,
                ),
            ),
        ));


        /*
         * <--- END SECTIONS
         */
    }

    add_action( 'after_setup_theme', 'etheme_redux_init', 1 );
}


// If Redux is running as a plugin, this will remove the demo notice and links
add_action( 'redux/loaded', 'remove_demo' );

// Remove the demo link and the notice of integrated demo from the redux-framework plugin
function remove_demo() {
    // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
    if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
        remove_filter( 'plugin_row_meta', array(
            ReduxFrameworkPlugin::instance(),
            'plugin_metalinks'
        ), null, 2 );

        // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
        remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
    }
}
