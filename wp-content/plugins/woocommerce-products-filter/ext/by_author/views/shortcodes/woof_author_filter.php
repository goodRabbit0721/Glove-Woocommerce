<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php global $WOOF; ?>
<div data-css-class="woof_author_search_container" class="woof_author_search_container woof_container">
    <div class="woof_container_overlay_item"></div>
    <div class="woof_container_inner">
        <?php
        $args = array(
            'role' => '',
            'meta_key' => '',
            'meta_value' => '',
            'meta_compare' => '',
            'meta_query' => array(),
            'date_query' => array(),
            'include' => array(),
            'exclude' => array(),
            'orderby' => 'login',
            'order' => 'ASC',
            'offset' => '',
            'search' => '',
            'number' => '',
            'count_total' => false,
            'fields' => 'all',
            'who' => ''
        );

        if (isset($role) AND ! empty($role))
        {
            $args['role'] = $role;
        }

        $authors = get_users($args);
        $request = $WOOF->get_request_data();
        $woof_author = '';
        if (isset($request['woof_author']))
        {
            $woof_author = $request['woof_author'];
        }
        //+++
        $p = __('Select a product author', 'woocommerce-products-filter');

        if (isset($placeholder) AND ! empty($placeholder))
        {
            $p = $placeholder;
        } else
        {
            if (isset($WOOF->settings['by_author']['placeholder']))
            {
                if (!empty($WOOF->settings['by_author']['placeholder']))
                {
                    $p = $WOOF->settings['by_author']['placeholder'];
                    $p = WOOF_HELPER::wpml_translate(null, $p);
                    $p = __($p, 'woocommerce-products-filter');
                }
            }
        }



        //***
        $unique_id = uniqid('woof_author_search_');
        ?>

        <select name="woof_author" class="woof_select woof_show_author_search <?php echo $unique_id ?>" data-uid="<?php echo $unique_id ?>">
            <option value="0"><?php echo $p ?></option>
            <?php if (!empty($authors)): ?>
                <?php foreach ($authors as $user): ?>
                    <option <?php echo selected($woof_author, $user->data->ID); ?> value="<?php echo $user->data->ID ?>"><?php echo $user->data->display_name ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>


    </div>
</div>
