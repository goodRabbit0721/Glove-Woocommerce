<?php
/**
 * Framework Name: YIT Plugin Framework
 * Version: 2.9.35
 * Author: Yithemes
 * Text Domain: yith-plugin-fw
 * Domain Path: /languages/
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 2.0
 */
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists ( 'yit_maybe_plugin_fw_loader' ) ) {
    /**
     * YITH WooCommerce Ajax Navigation
     *
     * @since 1.0.0
     */
    function yit_maybe_plugin_fw_loader ( $plugin_path ) {
        global $plugin_fw_data;

        $default_headers = array (
            'Name'       => 'Framework Name',
            'Version'    => 'Version',
            'Author'     => 'Author',
            'TextDomain' => 'Text Domain',
            'DomainPath' => 'Domain Path',
        );

        $framework_data      = get_file_data ( trailingslashit ( $plugin_path ) . 'plugin-fw/init.php', $default_headers );
        $plugin_fw_main_file = trailingslashit ( $plugin_path ) . 'plugin-fw/yit-plugin.php';

        if ( ! empty( $plugin_fw_data ) ) {
            foreach ( $plugin_fw_data as $version => $path ) {
                if ( version_compare ( $version, $framework_data[ 'Version' ], '<' ) ) {
                    $plugin_fw_data = array ( $framework_data[ 'Version' ] => $plugin_fw_main_file );
                }
            }
        } else {
            $plugin_fw_data = array ( $framework_data[ 'Version' ] => $plugin_fw_main_file );
        }
    }
}

if( is_admin() && function_exists( 'WC' ) && version_compare( WC()->version, '2.6', '<' ) ){
    add_action( 'admin_notices', 'yit_before_woocommerce_2_6_update' );
}

if( ! function_exists( 'yit_before_woocommerce_2_6_update' ) ){
    function yit_before_woocommerce_2_6_update(){ ?>
        <div id="message" class="error notice is-dismissible">
            <p>
                <strong>Attention!</strong> The new WooCommerce version 2.6 is now online. Before installing it we wholeheartedly suggest you backup your data and update all YITH plugins that are ready for WooCommerce 2.6 to the latest version.
                For more details about WooCommerce 2.6 compatibility, you can read this <a target="_blank" href="https://support.yithemes.com/hc/en-us/articles/215945378-Theme-and-Plugin-compatibility-with-WordPress-and-WooCommerce">article</a> which is constantly updated.
            </p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
        <?php
    }
}

