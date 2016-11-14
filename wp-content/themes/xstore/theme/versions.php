<?php  if ( ! defined('ABSPATH')) exit('No direct script access allowed');

$main_domain = 'http://8theme.com/demo/xstore/';

return array(
    'default' => array(
        'title' => 'Default',
        'preview_url' => $main_domain,
        'to_import' => array(
            'content' => true,
            'slider' => true,
            'widgets' => true,
            'options' => true,
            'menu' => true,
            'home_page' => true
        ),
        'type' => 'demo'
    ),
    'wedding' => array(
        'title' => 'Wedding',
        'preview_url' => $main_domain . 'wedding/',
        'to_import' => array(
            'content' => true,
            'slider' => 2,
            'options' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'bicycle' => array(
        'title' => 'Bicycle',
        'preview_url' => $main_domain . 'bike/',
        'to_import' => array(
            'content' => true,
            'slider' => 3,
            'options' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'furniture' => array(
        'title' => 'Furniture',
        'preview_url' => $main_domain . 'furniture/',
        'to_import' => array(
            'content' => true,
            'slider' => true,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'cosmetics' => array(
        'title' => 'Cosmetics',
        'preview_url' => $main_domain . 'cosmetics/',
        'to_import' => array(
            'content' => true,
            'slider' => true,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'engineer' => array(
        'title' => 'Engineer',
        'preview_url' => $main_domain . 'engineer/',
        'to_import' => array(
            'content' => true,
            'slider' => 2,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'glasses' => array(
        'title' => 'Glasses',
        'preview_url' => $main_domain . 'glasses/',
        'to_import' => array(
            'content' => true,
            'slider' => true,
            'options' => true,
            'widgets' => false,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'kids' => array(
        'title' => 'Kids',
        'preview_url' => $main_domain . 'kids/',
        'to_import' => array(
            'content' => true,
            'slider' => 2,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'organic' => array(
        'title' => 'Organic',
        'preview_url' => $main_domain . 'organic/',
        'to_import' => array(
            'content' => true,
            'slider' => 2,
            'options' => true,
            'widgets' => false,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'drinks' => array(
        'title' => 'Drinks',
        'preview_url' => $main_domain . 'drinks/',
        'to_import' => array(
            'content' => true,
            'slider' => 2,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'bakery' => array(
        'title' => 'Bakery',
        'preview_url' => $main_domain . 'bakery/',
        'to_import' => array(
            'content' => true,
            'slider' => true,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'hipster' => array(
        'title' => 'Hipster',
        'preview_url' => $main_domain . 'hipster/',
        'to_import' => array(
            'content' => true,
            'slider' => 2,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'jewellery' => array(
        'title' => 'Jewellery',
        'preview_url' => $main_domain . 'jewellery/',
        'to_import' => array(
            'content' => true,
            'slider' => 2,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'landing' => array(
        'title' => 'Landing',
        'preview_url' => $main_domain . 'landing/',
        'to_import' => array(
            'content' => true,
            'slider' => 2,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'hosting' => array(
        'title' => 'Hosting',
        'preview_url' => $main_domain . 'hosting/',
        'to_import' => array(
            'content' => true,
            'slider' => 2,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'dark' => array(
        'title' => 'Dark',
        'preview_url' => $main_domain . 'dark/',
        'to_import' => array(
            'content' => true,
            'slider' => true,
            'options' => true,
            'widgets' => true,
            'home_page' => true,
        ),
        'type' => 'demo'
    ),
    'faq' => array(
        'title' => 'FaQ',
        'preview_url' => $main_domain . 'typography-page/faq/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'presentation' => array(
        'title' => 'Home presentation',
        'preview_url' => $main_domain . 'home-presentation/?preset=header6',
        'to_import' => array(
            'content' => true,
            'home_page' => true,
            'options' => true,
        ),
        'type' => 'page'
    ),
    'parallax' => array(
        'title' => 'Home parallax',
        'preview_url' => $main_domain . 'home-parallax/?preset=lookbook',
        'to_import' => array(
            'content' => true,
            'home_page' => true,
            'slider' => true,
            'options' => true,
        ),
        'type' => 'page'
    ),
    'banners' => array(
        'title' => 'Home banners',
        'preview_url' => $main_domain . 'home-banners/?preset=header2',
        'to_import' => array(
            'content' => true,
            'home_page' => true,
            'slider' => true,
            'options' => true,
        ),
        'type' => 'page'
    ),
    'full-width' => array(
        'title' => 'Home full width',
        'preview_url' => $main_domain . 'home-full-width/?preset=header2',
        'to_import' => array(
            'content' => true,
            'home_page' => true,
            'slider' => true,
            'options' => true,
        ),
        'type' => 'page'
    ),
    'simple' => array(
        'title' => 'Home simple',
        'preview_url' => $main_domain . 'home-simple/',
        'to_import' => array(
            'content' => true,
            'home_page' => true,
            'slider' => true,
            'options' => true,
        ),
        'type' => 'page'
    ),
    'video' => array(
        'title' => 'Home video',
        'preview_url' => $main_domain . 'home-video/',
        'to_import' => array(
            'content' => true,
            'home_page' => true,
            'slider' => true,
            'options' => true,
        ),
        'type' => 'page'
    ),
    'minimal' => array(
        'title' => 'Home minimal',
        'preview_url' => $main_domain . 'home-minimal/?preset=header6',
        'to_import' => array(
            'content' => true,
            'home_page' => true,
            'slider' => 2,
            'options' => true,
        ),
        'type' => 'page'
    ),
    'flat' => array(
        'title' => 'Home flat',
        'preview_url' => $main_domain . 'home-flat/?preset=header6',
        'to_import' => array(
            'content' => true,
            'home_page' => true,
            'slider' => true,
            'options' => true,
        ),
        'type' => 'page'
    ),
    'team' => array(
        'title' => 'Meet the team',
        'preview_url' => $main_domain . 'typography-page/team-members/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'about-us' => array(
        'title' => 'About Us 2',
        'preview_url' => $main_domain . 'typography-page/about-us/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'about-us-3' => array(
        'title' => 'About Us 3',
        'preview_url' => $main_domain . 'typography-page/about-us-two/?preset=about-us-2',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'look-book-fresh' => array(
        'title' => 'Look Book Fresh',
        'preview_url' => $main_domain . 'typography-page/look-book-fresh/?preset=lookbook',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'look-book' => array(
        'title' => 'Look Book',
        'preview_url' => $main_domain . 'typography-page/look-book/?preset=lookbook',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'single-member' => array(
        'title' => 'Single Member',
        'preview_url' => $main_domain . 'typography-page/single-member-page/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'parallax-presentation' => array(
        'title' => 'Parallax Presentation',
        'preview_url' => $main_domain . 'typography-page/parallax-presentation/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'modern-process' => array(
        'title' => 'Modern Process',
        'preview_url' => $main_domain . 'typography-page/modern-process/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'informations' => array(
        'title' => 'Informations',
        'preview_url' => $main_domain . 'typography-page/informations/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'our-office' => array(
        'title' => 'Our Office',
        'preview_url' => $main_domain . 'our-office/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'instagram' => array(
        'title' => 'Instagram Wall',
        'preview_url' => $main_domain . 'typography-page/instagram-wall/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'track-order' => array(
        'title' => 'Track order',
        'preview_url' => $main_domain . 'track-order-2/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'coming-soon' => array(
        'title' => 'Coming soon',
        'preview_url' => $main_domain . 'coming-soon-page/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'coming-soon-dark' => array(
        'title' => 'Coming soon dark',
        'preview_url' => $main_domain . 'coming-soon-black/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'coming-soon-dark2' => array(
        'title' => 'Coming soon dark2',
        'preview_url' => $main_domain . 'coming-soon-black2/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'coming-soon-white' => array(
        'title' => 'Coming soon white',
        'preview_url' => $main_domain . 'coming-soon-white/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'coming-soon-flat' => array(
        'title' => 'Coming soon flat',
        'preview_url' => $main_domain . 'coming-soon-flat/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'coming-soon-xstore' => array(
        'title' => 'Coming soon xstore',
        'preview_url' => $main_domain . 'coming-soon-xstore/',
        'to_import' => array(
            'content' => true
        ),
        'type' => 'page'
    ),
    'about-cosmetics' => array(
        'title' => 'About Cosmetics',
        'preview_url' => $main_domain . 'cosmetics/about-us/',
        'to_import' => array(
            'content' => true,
            'slider' => true
        ),
        'type' => 'page'
    ),
    'about-engineer' => array(
        'title' => 'About Engineer',
        'preview_url' => $main_domain . 'engineer/about-us/',
        'to_import' => array(
            'content' => true,
            'slider' => true
        ),
        'type' => 'page'
    ),
    'about-glasses' => array(
        'title' => 'About Glasses',
        'preview_url' => $main_domain . 'glasses/',
        'to_import' => array(
            'content' => true,
            'slider' => true
        ),
        'type' => 'page'
    ),
    'about-kids' => array(
        'title' => 'About Kids',
        'preview_url' => $main_domain . 'kids/about-us/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
    'about-organic' => array(
        'title' => 'About Organic',
        'preview_url' => $main_domain . 'organic/about-us/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
    'contacts-organic' => array(
        'title' => 'Contacts Organic',
        'preview_url' => $main_domain . 'organic/contact/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
    'contacts-hosting' => array(
        'title' => 'Contacts hosting',
        'preview_url' => $main_domain . 'hosting/contact-us/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
    'our-history-drinks' => array(
        'title' => 'Our history drinks',
        'preview_url' => $main_domain . 'drinks/our-history/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
    'about-furniture' => array(
        'title' => 'About Furniture',
        'preview_url' => $main_domain . 'furniture/about-us/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
    'about-bakery' => array(
        'title' => 'About Bakery',
        'preview_url' => $main_domain . 'bakery/about-us/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
    'about-hipster' => array(
        'title' => 'About hipster',
        'preview_url' => $main_domain . 'hipster/about-us/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
    'about-jewellery' => array(
        'title' => 'About jewellery',
        'preview_url' => $main_domain . 'jewellery/about-us/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
    'about-hosting' => array(
        'title' => 'About hosting',
        'preview_url' => $main_domain . 'hosting/about-us/',
        'to_import' => array(
            'content' => true,
            'slider' => false
        ),
        'type' => 'page'
    ),
);