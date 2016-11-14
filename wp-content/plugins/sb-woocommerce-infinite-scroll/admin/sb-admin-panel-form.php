<?php
/*
 * Plugin Admin Settings Panel
 */
	global $wpdb;	
	?>
    <div class="wrap" id="sbis-wrapper">
        <h2 class="title">
        	<img class="title-icon" src="<?php echo $this->parent->plugin_dir_url; ?>assets/img/logo-icon.jpg" alt="SB Themes" /> <?php echo $this->parent->plugin_name; ?>
            <a id="import-export-link" class="alignright button-primary sb-btn">Import / Export</a>
        </h2>
        <div class="clear"></div><br />
        <div class="sb-message">
        	Data Saved...
        </div>
        
        <div id="dashboard-widgets-wrap">
        	<div id="dashboard-widgets" class="metabox-holder">
                <?php $this->sb_admin_setting_box(); ?>
            </div>
        </div>
        <div id="import-export"></div>
	</div>