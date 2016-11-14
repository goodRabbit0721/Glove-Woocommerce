<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'color_image_swatches_menu' );

function color_image_swatches_menu() {
	
	$plugin_dir_url = plugin_dir_url(dirname(__FILE__));

	add_menu_page( 'phoeniixx', __( 'Phoeniixx', 'phe' ), 'nosuchcapability', 'phoeniixx', NULL, $plugin_dir_url.'/assets/images/logo-wp.png', 57 );

	add_submenu_page( 'phoeniixx', 'Color and Image Swatches', 'Color and Image Swatches', 'manage_options','settings_color_image_swatches',  'settings_color_image_swatches' ); 	
	
}

function settings_color_image_swatches()
{
	
	$plugin_dir_url = plugin_dir_url(dirname(__FILE__));
	
	?>
		
		<div id="profile-page" class="wrap">
		<?php
		$tab = sanitize_text_field( $_GET['tab'] );
		?>
		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<a class="nav-tab <?php if($tab == 'set' || $tab == ''){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=settings_color_image_swatches&amp;tab=set">Settings</a>
			<a class="nav-tab <?php if($tab == 'premium'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=settings_color_image_swatches&amp;tab=premium">Premium Version</a>
			
		</h2>
		<?php
		if($tab == 'set' || $tab == '')
		{
			
			if($_POST['submit']) {
				
				if ( ! isset( $_POST['color_image_swatches_setting_nonce'] ) || ! wp_verify_nonce( $_POST['color_image_swatches_setting_nonce'], 'color_image_swatches_setting_submit' ) ) 
				{

				   print 'Sorry, your nonce did not verify.';
				   exit;

				} 
				else {
					
					$color_image_swatches_check =  sanitize_text_field( $_POST['color_image_swatches_check'] );
					
					$color_image_swatches_check =  ($color_image_swatches_check == '' ? '0' : '1'); 
				
					update_option( 'color_image_swatches_check', $color_image_swatches_check );
					
				}

			}
			
			$color_image_swatches_check  = get_option( 'color_image_swatches_check' );
			
			?>
			<div class="meta-box-sortables" id="normal-sortables">
					<div class="postbox " id="pho_wcpc_box">
						<h3><span class="upgrade-setting">Upgrade to the PREMIUM VERSION</span></h3>
						<div class="inside">
							<div class="pho_check_pin">

								<div class="column two">
								<!----<h2>Get access to Pro Features</h2>----->

									<p>Switch to the premium version </p>

									<div class="pho-upgrade-btn">
										<a target="_blank" href="https://www.phoeniixx.com/product/color-image-swatches-woocommerce/"><img src="<?php echo $plugin_dir_url; ?>assets/images/premium-btn.png" /></a>
									</div>
								</div>
							</div>
						</div>
					</div>
						
				<h2>

				Color and Image Swatches  - Plugin Options</h2>

				<form novalidate="novalidate" method="post" action="" >
				   <?php wp_nonce_field( 'color_image_swatches_setting_submit', 'color_image_swatches_setting_nonce' ); ?>
					<h3>General settings</h3>

					<table class="form-table">

						<tbody>

							<tr class="user-nickname-wrap">

								<th><label for="color_image_swatches_check">Enable Color and Image Swatches</label></th>

								<td><input type="checkbox" value="1" <?php if($color_image_swatches_check == 1){ echo "checked"; }  ?> id="color_image_swatches_check" name="color_image_swatches_check" ></label></td>

							</tr>
							
						</tbody>

					</table>	

					<p class="submit"><input type="submit" value="Save" class="button button-primary" id="submit" name="submit"></p>

				</form>
			
			</div>
			
			<?php
		
		}
		
		if($tab == 'premium')
		{
			require_once(dirname(__FILE__).'/premium-setting.php');
		}

		?>			
		</div>
		
		<style>
		.form-table th {
			width: 270px;
			padding: 25px;
		}
		.form-table td {
			
			padding: 20px 10px;
		}
		.form-table {
			background-color: #fff;
		}
		h3 {
			padding: 10px;
		}
		</style>
		
	<?php
	
}

?>