<?php $setting = $this->get_infinite_scroll_setting(); ?>
<div class="meta-box-sortables" id="dashboard-widgets">
    <form class="infinite_scroll_setting_form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
    	<div class="postbox-container">
        	 <div class="postbox">
             	<div class="handlediv" title="Click to toggle"><br></div>
	            <h3 class="hndle"><span>Global Settings</span></h3>
                <div class="inside">
	                <div class="main">
                    	<div class="form-row">
                            <label>Status</label>
                            <div class="field-wrapper">
                                <input <?php checked($this->sb_display_field($setting['status']), 1); ?> name="settings[status]" type="checkbox" value="1" /> Enable / Disable
                                <p class="description">Uncheck this box to disabled plugin pagination</p>
                            </div>
                        </div>
                    	<div class="form-row">
                            <label>Pagination Type</label>
                            <div class="field-wrapper">
                                <select name="settings[pagination_type]">
                                    <?php
                                        $pagination_types = $this->get_pagination_type();
                                        if(isset($pagination_types)) {
                                            foreach($pagination_types as $pagination_type_key => $pagination_type) {
                                                echo '<option '.selected($setting['pagination_type'], $pagination_type_key).' value="'.$pagination_type_key.'">'.$pagination_type.'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                                <p class="description">Select type of pagination</p>
                            </div>
                        </div>
                        <div class="form-row">
                        	<input type="checkbox" id="mobile_pagination_settings" <?php checked($setting['mobile_pagination_settings'], '1'); ?> name="settings[mobile_pagination_settings]" value="1" /> <strong>Enable different pagination type for smaller devices.</strong>
                            <div class="small-device-settings-box" style=" <?php if($setting['mobile_pagination_settings'] == 1) { echo 'display:block;'; } ?>">
                                <div class="clear"></div><br />
                                <label>Pagination Type</label>
                                <div class="field-wrapper">
                                    <select name="settings[mobile_pagination_type]">
                                        <?php
                                            $mobile_pagination_type = $this->get_pagination_type();
                                            if(isset($mobile_pagination_type)) {
                                                foreach($mobile_pagination_type as $pagination_type_key => $pagination_type) {
                                                    echo '<option '.selected($setting['mobile_pagination_type'], $pagination_type_key).' value="'.$pagination_type_key.'">'.$pagination_type.'</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                    <p class="description">Select pagination type for small devices</p>
                                </div>
                                <div class="clear"></div>
                                <label>Break Point</label>
                                <div class="field-wrapper">
                                    <input type="number" min="0" value="<?php echo $setting['break_point']; ?>" name="settings[break_point]" /> Pixels
                                    <p class="description">Pagination type will change for smaller<br />device than break point pixels.</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Animation</label>
                            <div class="field-wrapper">
                                <select name="settings[animation]" id="animation">
                                    <option <?php selected($setting['animation'], 'none'); ?> value="none">None</option>
                                    <?php
                                        $animations = $this->parent->get_animation_style();
                                        if(isset($animations)) {
                                            foreach($animations as $animation_key => $animation) {
                                                echo '<optgroup label="'.$animation_key.'">';
                                                foreach($animation as $anim_key => $anim)
                                                echo '<option '.selected($setting['animation'], $anim_key).' value="'.$anim_key.'">'.$anim.'</option>';
                                                echo '</optgroup>';
                                            }
                                        }
                                    ?>
                                </select>
                                <img id="animate-img" src="<?php echo $this->parent->plugin_dir_url; ?>assets/img/logo-icon.jpg" alt="SB Themes" />
                                <p class="description">Animation style after loading products</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Products Per Page</label>
                            <div class="field-wrapper">
                                <input name="settings[products_per_page]" type="number" value="<?php echo $this->sb_display_field($setting['products_per_page']); ?>" min="1" />
                                <p class="description"><strong>(Optional)</strong> Set number to initially load products.<br />Leave empty to default.</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Placeholder Image</label>
                            <div class="field-wrapper">
                                <input name="settings[woo_placeholder_image]" class="loading_image" type="text" value="<?php echo $this->sb_display_field($setting['woo_placeholder_image']); ?>" /><input type="button" class="button upload_image" value="Upload" />
                                <img width="32" height="32" class="loading_image_preview alignright" src="<?php echo $this->sb_display_field($setting['woo_placeholder_image']); ?>" alt=" " />
                                <span class="alignright">&nbsp; &nbsp;</span>
                                <p class="description">Default product placeholder thumbnail.<br />Loads when product image is not available.</p>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
             </div>
             
             <div class="postbox">
             	<div class="handlediv" title="Click to toggle"><br></div>
	            <h3 class="hndle"><span>Messages and Loader Settings</span></h3>
                <div class="inside">
	                <div class="main">
                    	<div class="form-row">
                            <label>Loading Message</label>
                            <div class="field-wrapper">
                                <input name="settings[loading_message]" type="text" value="<?php echo $this->sb_display_field($setting['loading_message']); ?>" />
                                <p class="description">Text to display when products are retrieving</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Loading Wrapper Class</label>
                            <div class="field-wrapper">
                                <input name="settings[loading_wrapper_class]" type="text" value="<?php echo $this->sb_display_field($setting['loading_wrapper_class']); ?>" />
                                <p class="description">Add custom class to customize loading message style</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Finished Message</label>
                            <div class="field-wrapper">
                                <input name="settings[finished_message]" type="text" value="<?php echo $this->sb_display_field($setting['finished_message']); ?>" />
                                <p class="description">Text to display when no additional products are available</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Loading Image</label>
                            <div class="field-wrapper">
                                <input name="settings[loading_image]" class="loading_image" type="text" value="<?php echo $this->sb_display_field($setting['loading_image']); ?>" /><input type="button" class="button upload_image" value="Upload" />
                                <img width="32" height="32" class="loading_image_preview alignright" src="<?php echo $this->sb_display_field($setting['loading_image']); ?>" alt=" " />
                                <span class="alignright">&nbsp; &nbsp;</span>
                                <p class="description">Loader image to display when products are retrieving</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Load More Button Text</label>
                            <div class="field-wrapper">
                                <input name="settings[load_more_button_text]" type="text" value="<?php echo $this->sb_display_field($setting['load_more_button_text']); ?>" />
                                <p class="description">Add Load More Button Text</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Load More Button Class</label>
                            <div class="field-wrapper">
                                <input name="settings[load_more_button_class]" type="text" value="<?php echo $this->sb_display_field($setting['load_more_button_class']); ?>" />
                                <p class="description">Add custom class to customize button style (Use space for multiple)</p>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
             </div>
             
        </div>
        <div class="postbox-container" id="sb-postbox-container-right">
        	 <div class="postbox">
             	<div class="handlediv" title="Click to toggle"><br></div>
	            <h3 class="hndle"><span>Selector Settings</span></h3>
                <div class="inside">
	                <div class="main">
                    	<div class="form-row">
                            <label>Content Selector</label>
                            <div class="field-wrapper">
                                <input name="settings[content_selector]" type="text" value="<?php echo $this->sb_display_field($setting['content_selector']); ?>" />
                                <p class="description">Div containing your products</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Item Selector</label>
                            <div class="field-wrapper">
                                <input name="settings[item_selector]" type="text" value="<?php echo $this->sb_display_field($setting['item_selector']); ?>" />
                                <p class="description">Div containing an individual product</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Navigation Selector</label>
                            <div class="field-wrapper">
                                <input name="settings[navigation_selector]" type="text" value="<?php echo $this->sb_display_field($setting['navigation_selector']); ?>" />
                                <p class="description">Div containing your products navigation (pagination)</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Next Selector</label>
                            <div class="field-wrapper">
                                <input name="settings[next_selector]" type="text" value="<?php echo $this->sb_display_field($setting['next_selector']); ?>" />
                                <p class="description">Link to next page of products (Next page link selector)</p>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
             </div>
             
             <div class="postbox">
             	<div class="handlediv" title="Click to toggle"><br></div>
	            <h3 class="hndle"><span>Lazy Load Settings</span></h3>
                <div class="inside">
	                <div class="main">
                    	<div class="form-row">
                            <label>Enable Lazy Load</label>
                            <div class="field-wrapper">
                                <input <?php checked($this->sb_display_field($setting['lazyload']), 1); ?> name="settings[lazyload]" type="checkbox" value="1" /> Enable / Disable
                                <p class="description">Check this box to enable lazy load for WooCommerce.</p>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <label>Disable On Mobile Devices</label>
                            <div class="field-wrapper">
                                <input <?php checked($this->sb_display_field($setting['lazyload_mobile']), 1); ?> name="settings[lazyload_mobile]" type="checkbox" value="1" /> Yes / No
                                <p class="description">Check to disable lazy load on mobile devices.</p>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <label>Loader Image</label>
                            <div class="field-wrapper">
                                <input name="settings[lazyload_loading_image]" class="loading_image" type="text" value="<?php echo $this->sb_display_field($setting['lazyload_loading_image']); ?>" /><input type="button" class="button upload_image" value="Upload" />
                                <img width="32" height="32" class="loading_image_preview alignright" src="<?php echo $this->sb_display_field($setting['lazyload_loading_image']); ?>" alt=" " />
                                <span class="alignright">&nbsp; &nbsp;</span>
                                <p class="description">Loader image for lazy load</p>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
             </div>
             
             <div class="postbox">
             	<div class="handlediv" title="Click to toggle"><br></div>
	            <h3 class="hndle"><span>Miscellaneous Settings</span></h3>
                <div class="inside">
	                <div class="main">
                    	<div class="form-row">
                            <label>Buffer Pixels</label>
                            <div class="field-wrapper">
                                <input name="settings[buffer_pixels]" type="number" value="<?php echo $this->sb_display_field($setting['buffer_pixels']); ?>" /> Pixels
                                <p class="description">Increase this number if you want infinite scroll to fire quicker</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Scroll Top</label>
                            <div class="field-wrapper">
                                <input <?php checked($this->sb_display_field($setting['scrolltop']), 1); ?> name="settings[scrolltop]" type="checkbox" value="1" /> Yes / No
                                <p class="description">Check to scroll top after data loading (only for: Ajax Pagination)</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Scroll To</label>
                            <div class="field-wrapper">
                                <input name="settings[scrollto]" type="text" value="<?php echo $this->sb_display_field($setting['scrollto']); ?>" />
                                <p class="description">Scroll top destination. Only works if scroll top is enable</p>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
             </div>
             
             <div class="postbox advanced-settings closed">
             	<div class="handlediv" title="Click to toggle"><br></div>
	            <h3 class="hndle"><span>Advanced Settings</span></h3>
                <div class="inside">
	                <div class="main">
                    	<div class="form-row">
                            <label><strong>On Pagination Start</strong></label>
                            <div class="clear"></div>
                            <div class="field-wrapper">
                                <textarea name="settings[onstart]"><?php echo $this->sb_display_field($setting['onstart']); ?></textarea>
                                <p class="description">Executes on pagination start. (Use Javasctipt/jQuery code to trigger custom event)</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <label><strong>On Pagination End</strong></label>
                            <div class="clear"></div>
                            <div class="field-wrapper">
                                <textarea name="settings[onfinish]"><?php echo $this->sb_display_field($setting['onfinish']); ?></textarea>
                                <p class="description">Executes immediately after pagination completed. (Use Javasctipt/jQuery code to trigger custom event)</p>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
             </div>
        </div>
        
        <div class="form-row">
            <div class="field-wrapper">
                <input type="hidden" name="action" value="save_infinite_scroll_settings" />
                <input type="submit" value="Save Settings" class="button-primary btn-save-settings sb-btn alignleft" /><span class="alignleft">&nbsp;</span>
                <img class="ajax-loader" src="<?php echo $this->parent->plugin_dir_url; ?>assets/img/ajax-loader.gif" alt="Saving..." />
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>