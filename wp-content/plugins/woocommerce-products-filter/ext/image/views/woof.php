<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php
global $WOOF;
$show_count = get_option('woof_show_count', 0);
$show_count_dynamic = get_option('woof_show_count_dynamic', 0);
$hide_dynamic_empty_pos = get_option('woof_hide_dynamic_empty_pos', 0);
$woof_autosubmit = get_option('woof_autosubmit', 0);
//********************
?>

<ul class = "woof_list woof_list_image">
    <?php
    $woof_tax_values = array();
    $current_request = array();
    $request = $WOOF->get_request_data();
    $_REQUEST['additional_taxes'] = $additional_taxes;
    $_REQUEST['hide_terms_count_txt'] = isset($WOOF->settings['hide_terms_count_txt']) ? $WOOF->settings['hide_terms_count_txt'] : 0;

    if ($WOOF->is_isset_in_request_data($tax_slug))
    {
        $current_request = $request[$tax_slug];
        $current_request = explode(',', urldecode($current_request));
    }
//excluding hidden terms
    $hidden_terms = array();
    if (!isset($_REQUEST['woof_shortcode_excluded_terms']))
    {
        if (isset($WOOF->settings['excluded_terms'][$tax_slug]))
        {
            $hidden_terms = explode(',', $WOOF->settings['excluded_terms'][$tax_slug]);
        }
    } else
    {
        $hidden_terms = explode(',', $_REQUEST['woof_shortcode_excluded_terms']);
    }

    $terms = apply_filters('woof_sort_terms_before_out', $terms, 'image');
    ?>
    <?php if (!empty($terms)): ?>
        <?php foreach ($terms as $term) : $inique_id = uniqid(); ?>
            <?php
            //excluding hidden terms
            if (in_array($term['term_id'], $hidden_terms))
            {
                continue;
            }
            
            //***

            $term_key = 'images_term_' . $term['term_id'];
            $images = isset($woof_settings[$term_key]) ? $woof_settings[$term_key] : array();

            $image = '';
            if (isset($images['image_url']) AND ! empty($images['image_url']))
            {
                $image = $images['image_url'];
            } else
            {
                continue;
            }



            if ($images['image_url'] == 'hide')
            {
                continue;
            }

            //***

            $count_string = "";
            $count = 0;
            if (!in_array($term['slug'], $current_request))
            {
                if ($show_count)
                {
                    if ($show_count_dynamic)
                    {
                        $count = $WOOF->dynamic_count($term, 'multi', $_REQUEST['additional_taxes']);
                    } else
                    {
                        $count = $term['count'];
                    }
                    $count_string = '<span>(' . $count . ')</span>';
                }
                //+++
                if ($hide_dynamic_empty_pos AND $count == 0)
                {
                    continue;
                }
            }

            if ($_REQUEST['hide_terms_count_txt'])
            {
                $count_string = "";
            }



            $term_desc = strip_tags(term_description($term['term_id'], $term['taxonomy']));
            //***

            if (isset($images['image_styles']))
            {
                $styles = trim($images['image_styles']);
            }
            //$styles = "width: {$width}px !important; height: {$height}px !important;";
            ?>
            <li class="woof_image_term_li_<?php echo $term['term_id'] ?>">
                <p class="woof_tooltip"><span class="woof_tooltip_data"><?php echo $term['name'] ?> <?php echo $count_string ?><?php echo(!empty($term_desc) ? '<br /><i>' . $term_desc . '</i>' : '') ?></span><input type="checkbox" data-styles="<?php echo $styles ?>" <?php checked(in_array($term['slug'], $current_request)) ?> id="<?php echo 'woof_' . $term['term_id'] . '_' . $inique_id ?>" class="woof_image_term woof_image_term_<?php echo $term['term_id'] ?> <?php if (in_array($term['slug'], $current_request)): ?>checked<?php endif; ?>" data-image="<?php echo $image ?>" data-tax="<?php echo $tax_slug ?>" name="<?php echo $term['slug'] ?>" value="<?php echo $term['term_id'] ?>" data-term-id="<?php echo $term['term_id'] ?>" <?php echo checked(in_array($term['slug'], $current_request)) ?> /></p>
                <input type="hidden" value="<?php echo $term['name'] ?>" data-anchor="woof_n_<?php echo $tax_slug ?>_<?php echo $term['slug'] ?>" />
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
<div style="clear: both;"></div>
<?php
//we need it only here, and keep it in $_REQUEST for using in function for child items
unset($_REQUEST['additional_taxes']);

