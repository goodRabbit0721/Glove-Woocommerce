<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @author      Dovy Paukstys
 * @version     3.1.5
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


// Don't duplicate me!
if( !class_exists( 'ReduxFramework_dummy_content' ) ) {

    /**
     * Main ReduxFramework_dummy_content class
     *
     * @since       1.0.0
     */
    class ReduxFramework_dummy_content extends ReduxFramework {

        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {

            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                $this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
            }

            // Set default args for this field to avoid bad indexes. Change this to anything you use.
            $defaults = array(
                'options'           => array(),
                'stylesheet'        => '',
                'output'            => true,
                'enqueue'           => true,
                'enqueue_frontend'  => true
            );
            $this->field = wp_parse_args( $this->field, $defaults );

        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {
            $versions_imported = get_option('versions_imported');

            if( empty( $versions_imported ) ) $versions_imported = array();

            $class = '';

            if( ! in_array( 'default', $versions_imported ) ) {
                $class = ' no-default-imported';
            }

            foreach($versions_imported as $ver) {
                $class = ' imported-' . $ver;
            }

            echo '</td></tr></table><div class="etheme-import-section' . esc_attr( $class ) . '">';

            $versions = require apply_filters('etheme_file_url', ETHEME_THEME . 'versions.php');

            $pages = array_filter($versions, function( $el ) {
                return $el['type'] == 'page';
            });

            $demos = array_filter($versions, function( $el ) {
                return $el['type'] == 'demo';
            });

            ?>
            <div class="loading-info">
                <h2>Please wait, it may take up to 2 minutes.</h2>
                <div class="et-loader">
                    <svg viewBox="0 0 187.3 93.7" preserveAspectRatio="xMidYMid meet">
                        <path
                            stroke="#ededed"
                            class="outline"
                            fill="none"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-miterlimit="10"
                            d="M93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1 c-8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z">

                        </path>
                        <path
                            class="outline-bg"
                            opacity="0.05"
                            fill="none"
                            stroke="#ededed"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-miterlimit="10"
                            d="M93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1c-8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z">

                        </path>
                    </svg>
                </div>
            </div>

            <h3>
                <?php esc_html_e( 'Import base dummy content', 'xstore'); ?>
            </h3>

            <?php if( ! in_array( 'default', $versions_imported ) ): ?>
                <div class="etheme-import-info">
                    <strong>Import Base demo content</strong><br>
                    <p>Start working with our template by installing base demo content. Then you will get the opportunity to install the Home Page from the provided below list.</p>
                    <a href="#" class="et-button button-import-default button-import-version" data-version="default">
                        <?php esc_html_e('Import base dummy content', 'xstore'); ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="etheme-imported-info">
                <strong>Base Demo Data Installed!</strong>
                <br>
                <p>You have successfully imported our base demo content. Continue working with our template by choosing the Home Page version for installation to your website. </p>
            </div>

            <div class="import-demos-wrapper">
                <h3><?php esc_html_e( 'Import demo versions', 'xstore'); ?></h3>
                <div class="import-demos">
                    <?php foreach ($demos as $key => $version): ?>
                        <div class="version-preview <?php echo ( in_array( $key, $versions_imported ) ) ? 'version-imported' : 'not-imported'; ?> version-preview-<?php echo esc_attr( $key ); ?>">
                            <div class="version-screenshot">
                                <img src="<?php echo ETHEME_BASE_URI . 'theme/assets/dummy/' . $key . '/screenshot.jpg'; ?>" alt="">
                                <a href="<?php echo esc_url( $version['preview_url'] ); ?>" target="_blank" class="button-preview">
                                    <?php esc_html_e('Live prview', 'xstore'); ?>
                                </a>
                                <a href="#" class="et-button button-import-version button-import-version" data-version="<?php echo esc_attr( $key ); ?>">
                                    <?php echo ( ! in_array( $key, $versions_imported ) ) ? esc_html__('Import demo', 'xstore') : esc_html__('Activate', 'xstore') ; ?>
                                </a>
                                <span class="installed-icon"><?php esc_html_e('Data imported', 'xstore'); ?></span>
                            </div>
                            <span class="version-title"><?php echo esc_html( $version['title'] ); ?></span>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="install-base-first">
                    <h1><?php esc_html_e('No access!', 'xstore'); ?></h1>
                    <p><?php esc_html_e('Please, install Base demo content before, to access the collection of our Home Pages.', 'xstore'); ?></p>
                </div>
            </div>

            <div class="import-additional-pages">
                <h3><?php esc_html_e( 'Import additional pages', 'xstore'); ?></h3>

                <div class="page-preview">
                    <img src="<?php echo ETHEME_BASE_URI . 'theme/assets/dummy/faq/screenshot.jpg'; ?>" alt="">
                    <a href="<?php echo $pages['faq']['preview_url']; ?>" target="_blank" class="preview-page-button">
                        <?php esc_html_e('Live preview', 'xstore'); ?>
                    </a>
                </div>
                <div class="page-selector">
                    <select name="pages-selector" id="pages-selector" data-url="<?php echo ETHEME_BASE_URI . 'theme/assets/dummy/'; ?>">
                        <?php foreach ($pages as $key => $version): ?>
                            <option value="<?php echo esc_attr( $key ); ?>" data-preview="<?php echo $version['preview_url']; ?>"><?php echo esc_html( $version['title'] ); ?></option>
                        <?php endforeach ?>
                    </select>

                    <a href="#" class="et-button button-import-page button-import-version" data-version="faq">
                        <?php esc_html_e('Import', 'xstore'); ?>
                    </a>

                    <div class="etheme-options-info">
                        <b>Import Additional Pages</b><br>
                        Please, note, these pages should be added to your menu via Appearance>Menus All these pages are available for both Dark and Light version.
                    </div>
                    <div class="etheme-options-info info-red">
                        Attention! Before additional pages import, please, do the backup of your Theme Settings: "Import / Export - Options" and your website entirely.
                    </div>
                </div>
            </div>

            <?php

            echo '</div><table class="form-table no-border" style="margin-top: 0;"><tbody><tr style="border-bottom:0; display:none;"><th style="padding-top:0;"></th><td style="padding-top:0;">';

        }

        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {

            // $extension = ReduxFramework_extension_dummy_content::getInstance();

            wp_enqueue_script(
                'redux-field-dummy-content-js',
                $this->extension_url . 'field_dummy_content.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-dummy-content-css',
                $this->extension_url . 'field_dummy_content.css',
                time(),
                true
            );

        }

        /**
         * Output Function.
         *
         * Used to enqueue to the front-end
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function output() {

            if ( $this->field['enqueue_frontend'] ) {

            }

        }

    }
}
