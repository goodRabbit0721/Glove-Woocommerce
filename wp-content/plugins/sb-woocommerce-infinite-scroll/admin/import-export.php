<?php
	$settings = $this->get_infinite_scroll_setting();
	$export = base64_encode(serialize($settings));
?>
<div id="import-box">
    <form id="frm-import-settings" method="post" action="">
    	<strong>Export Settings: </strong>
        <textarea readonly="readonly" onclick="this.select();"><?php echo $export; ?></textarea>
        <p><strong>Important Notes: Import function will replace your current settings.</strong></p>
        <strong>Paset your settings here to Import Settings: </strong>
        <textarea name="settings"></textarea>
        <input type="hidden" name="action" value="import_settings">
        <input style="margin-top:10px;" type="submit" value="Import Settings" class="button-primary sb-btn import-is-setting alignleft">
        <img class="ajax-loader" src="<?php echo $this->parent->plugin_dir_url; ?>assets/img/ajax-loader.gif" alt="Importing..." />
    </form>
</div>