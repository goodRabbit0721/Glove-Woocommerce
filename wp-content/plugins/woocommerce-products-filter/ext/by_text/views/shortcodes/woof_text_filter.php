<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div data-css-class="woof_text_search_container" class="woof_text_search_container woof_container">
    <div class="woof_container_overlay_item"></div>
    <div class="woof_container_inner">
        <?php
        global $WOOF;
        $woof_text = '';
        $request = $WOOF->get_request_data();

        if (isset($request['woof_text']))
        {
            $woof_text = $request['woof_text'];
        }
        //+++
        if (!isset($placeholder))
        {
            $p = __('enter a product title here ...', 'woocommerce-products-filter');
        }

        if (isset($WOOF->settings['by_text']['placeholder']) AND ! isset($placeholder))
        {
            if (!empty($WOOF->settings['by_text']['placeholder']))
            {
                $p = $WOOF->settings['by_text']['placeholder'];
                $p = WOOF_HELPER::wpml_translate(null, $p);
                $p = __($p, 'woocommerce-products-filter');
            }


            if ($WOOF->settings['by_text']['placeholder'] == 'none')
            {
                $p = '';
            }
        }
        //***
        $unique_id = uniqid('woof_text_search_');
        ?>

        <table class="woof_text_table">
            <tr>
                <td style="width: 100%;">
                    <input type="search" class="woof_show_text_search <?php echo $unique_id ?>" id="<?php echo $unique_id ?>" data-uid="<?php echo $unique_id ?>" placeholder="<?php echo(isset($placeholder) ? $placeholder : $p) ?>" name="woof_text" value="<?php echo $woof_text ?>" />
                </td>
                <td>
                    <a href="javascript:void(0);" data-uid="<?php echo $unique_id ?>" class="woof_text_search_go <?php echo $unique_id ?>"></a>
                </td>
            </tr>
        </table>


    </div>
</div>