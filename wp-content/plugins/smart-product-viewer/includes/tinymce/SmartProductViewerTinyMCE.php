<?php

class SmartProductViewerTinyMCE {

    public $prefix;
    public $params;

    public function SmartProductViewerTinyMCE( $prefix, $params ) {

        $this->prefix = $prefix;
        $this->params = $params;

        add_action( 'init' , array( &$this, 'init' ) );
    }

    function init() {

        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
            return;

        if( ! is_admin() )
            return;

        // Enqueue files
        wp_enqueue_style( 'spv-tinymce-style',  plugins_url( 'lib/tinymce-style.css', __FILE__ ) );

        // Colorpicker
        wp_enqueue_style ( 'colpick', plugins_url( 'lib/colpick/css/colpick.css', __FILE__ ) );
        wp_enqueue_script( 'colpick', plugins_url( 'lib/colpick/js/colpick.js', __FILE__ ) );

        // Modal
        wp_enqueue_style ( 'wp-jquery-ui-dialog' );
        wp_enqueue_script( 'wpdialogs' );

        // Popup view
        add_action( 'admin_footer', array( &$this, 'output' ) );

        // Save params via AJAX
        add_action( 'wp_ajax_save_user_params', array( &$this, 'save_user_params' ) );
        
        // Filters 
        add_filter( 'mce_external_plugins', array( &$this, 'register_plugin' ) );
        add_filter( 'mce_buttons',          array( &$this, 'register_button' ) );

    }

    function register_button ( $buttons ) {
        
        array_push( $buttons, $this->prefix . 'shortcode_btn' );
        
        return $buttons;
    }

    function register_plugin ( $plugins ) {

        $plugins[$this->prefix . 'shortcode'] = plugins_url( 'lib/tinymce-script.js', __FILE__ );
        
        return $plugins;
    }

    function output() {

        $params = $this->params;

        // Get saved options
        $user_ID    = get_current_user_id();
        $saved_meta = get_user_meta( $user_ID, $this->prefix . 'user_generator_params', true );

        require_once( "lib/views/shortcode.form.php" );
        require_once( "lib/views/shortcode.script.php" );

    }

    function save_user_params() {

        global $wpdb;
        
        $user_ID  = intval( $_POST['user_ID'] );

        // sanitize params json
        $params = $_POST['params'];
        $params = stripslashes( $params );
        $params = json_decode( $params );
        $params = (array) $params;

        // save user input
        update_user_meta( $user_ID, $this->prefix . 'user_generator_params', $params );

        die();
    }
}
