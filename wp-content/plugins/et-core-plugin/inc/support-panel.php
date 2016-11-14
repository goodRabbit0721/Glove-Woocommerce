<?php 

// **********************************************************************//
// ! Add admin Theme Options links in menu
// **********************************************************************//

if(!function_exists('etheme_add_support_pages')) {
    add_action( 'admin_menu', 'etheme_add_support_pages', 3500 );
    function etheme_add_support_pages() {
        add_submenu_page(
            '_options',
            'Support',
            'Support',
            'manage_options',
            'et-faq-page',
            'etheme_faq_page'
        );
        add_submenu_page(
            '_options',
            'FaQs',
            'FaQs',
            'manage_options',
            'et-support-page',
            'etheme_support_page'
        );
        add_submenu_page(
            '_options',
            'Customization service',
            'Customization service',
            'manage_options',
            'et-customization-page',
            'etheme_customization_page'
        );
    }
}


if(!function_exists('etheme_faq_page')) {
    function etheme_faq_page() {
        etheme_page_header( 'faq' );
        ?>
        <div class="et-column-full">
            <h2 class="et-page-title et-faq-page-title">Frequently asked questions</h2>
            <?php etheme_get_remote_faq(); ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('body').on('click', '.open-toggle', function(e) {
                        e.preventDefault();
                        var content = $(this).parent().find('.et-toggle-content');

                        if( content.hasClass('shown') ) {
                            content.stop().slideUp().removeClass('shown');
                        } else {
                            $('.et-toggle-content.shown').slideUp().removeClass('shown');
                            content.stop().slideDown().addClass('shown');
                        }
                    });
                });
            </script>
        </div>
        <?php etheme_page_footer();
    }
}

if(!function_exists('etheme_get_remote_faq')) {
    function etheme_get_remote_faq() {
        $url = 'https://www.8theme.com/faq/';
        $faq_html = etheme_decoding(get_transient('et_faq_html'));
        if( ! $faq_html ) {
            $faq_html = '';
            $http = new WP_Http();
            $response = $http->request($url);
            if( $response['response']['code'] == 200 ) {
                $page_html = $response['body'];
                preg_match("/<section[^>]*class=\"content span12\">(.*?)<\\/section>/si", $page_html, $match);
                $faq_html = $match[1];
                set_transient( 'et_faq_html', etheme_encoding($faq_html), 7 * 24 * HOUR_IN_SECONDS );
            } else {
                echo '<a href="' . $url .'" target="_blank">View FaQs page</a>';
            }
        }
        echo $faq_html;
    }
}



if(!function_exists('etheme_support_page')) {
    function etheme_support_page() {
        etheme_page_header( 'support' );

        $url = 'https://www.8theme.com/wp-admin/admin-ajax.php?action=et_forum_search&bbp_search=';

        $search_url = 'https://www.8theme.com/?bbp_search=';

        $old = "";

        if( isset( $_POST["forum_search"] )) $old = $_POST["forum_search"];

        ?>
        <div class="et-column-half">
            <h2 class="et-page-title">Search for our forum</h2>
            <p>Want to get a quick answer to your question or find an appropriate topic? Type your key word and click on Search button.</p>

            <form action="" class="et-form" method="POST">
                <div class="et-inline-inputs">
                    <input type="text" class="et-input" value="<?php echo $old; ?>" name="forum_search" placeholder="Type here..">
                    <input type="submit" class="et-button" value="Search">
                </div>

                <?php
                    if( $_SERVER['REQUEST_METHOD'] == 'POST' && ! empty( $_POST['forum_search'] ) ) {
                        $word = sanitize_text_field( $_POST['forum_search'] );
                        $http = new WP_Http();

                        $response = $http->request( $url . $word );

                        if( $response['response']['code'] == 200 ) {
                            if( ! empty( $response['body'] ) ) {
                                $list = str_replace('href="', 'target="_blank" href="', $response['body']);
                                echo '<div class="et-forums-list">' . $list . '</div>';

                                echo '<a href="' . $search_url . $word . '" class="et-view-all" target="_blank">View all results</a>';
                            } else {
                                echo 'Any results match your request';
                            }

                        } else {
                            echo 'Can\'t access 8theme server';
                        }


                    } else {
                       ?>
                            <div class="et-call-to">
                                <strong>Can’t find what you’re looking for?  </strong>
                                <br>
                                <p>Submit a new Topic to our Forum but don’t forget to look through our FAQ before.</p>
                                <a href="https://www.8theme.com/forums/" target="_blank" class="et-button">Visit our forum</a>
                                <a href="https://www.8theme.com/faq/" target="_blank" class="et-button et-button-gray">Check Faq</a>
                            </div>
                       <?php
                    }
                ?>
            </form>


        </div>
        <div class="et-column-half et-forum-column">

        </div>
        <?php etheme_page_footer();
    }
}


if(!function_exists('etheme_customization_page')) {
    function etheme_customization_page() {
        etheme_page_header( 'customization' );
        ?>
        <div class="et-column-half">
            <h2 class="et-page-title">Check our customization services</h2>
            <p>
                Purchased one of our premium templates and it still does not meet your demands?   Want to add new features
                and elements or improve the current options, but lack of knowledge to implement that?  Our professional
                team at 8Theme is here to help you.   
            </p>

            <ul>
                <li>Enter our Customization service page.</li>
                <li>Fill out the form below with your details and click on SEND button.</li>
                <li>Get the reply with an offer to help, and your budget according to described requirements.</li>
            </ul>

            <p>
                We offer flexible rates for any size project and budget.  Contact our experienced and knowledgeable 8Theme team today.
            </p>
            <a href="https://www.8theme.com/customization-services/" class="et-button">Read More</a>
        </div>
        <div class="et-column-half et-customization-image">

        </div>
        <?php etheme_page_footer();
    }
}


if( ! function_exists('etheme_page_header') ) {
    function etheme_page_header($active_menu = 'faqs' ) {
        $menu = array(
            'faq' => array(
                'url' => menu_page_url('et-faq-page', false),
                'title' => 'FaQ',
                'icon' => 'question'
            ),
            'support' => array(
                'url' => menu_page_url('et-support-page', false),
                'title' => 'Search',
                'icon' => 'search'
            ),
            'customization' => array(
                'url' => menu_page_url('et-customization-page', false),
                'title' => 'Customization',
                'icon' => 'gear'
            )
        );
    ?>
        <div class="wrap et-support-page">
            <div class="et-tabs">
                <div class="et-tabs-header">
                    <h2>Free Support <span>We are here to assist you</span></h2>
                    <a href="https://www.8theme.com/forums/" target="_blank">Visit our support forum</a>
                </div>
                <div class="et-page-body">
                    <div class="et-tabs-nav">
                        <ul>
                            <?php foreach( $menu as $key => $menu_item ): ?>
                                <li class="<?php if( $key == $active_menu ) echo 'active-menu-item'; ?>">
                                    <a href="<?php echo $menu_item['url']; ?>">
                                        <i class="fa fa-<?php echo $menu_item['icon']; ?>"></i>
                                        <span><?php echo $menu_item['title']; ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="et-tabs-content">
                        <div class="et-columns">
        <?php
    }
}


if( ! function_exists('etheme_page_footer') ) {
    function etheme_page_footer() {
    ?>
        </div>
        </div>
        </div>
        </div>
        </div>
    <?php
    }
}