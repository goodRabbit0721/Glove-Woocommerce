<?php  if ( ! defined('ABSPATH')) exit('No direct script access allowed');

return array(
	'default' => array(
		'sidebar-widgets' => array(
			'main-sidebar' => array(
				'widgets' => array(
					'etheme-about-author' => array(
						'image' => 'http://8theme.com/import/xstore/wp-content/uploads/2016/05/author-new.jpg',
						'bio' => 'Primis adipiscing non lobortis porttitor cras elit tempor vestibulum non ligula molestie massa consectetur.'
					),
					'categories' => array(
						'title' => 'Categories'
					),
					'etheme-posts-tabs' => array(
						'number' => 4
					),
					'tag_cloud' => array(
						'title' => 'Tags Cloud',
						'taxonomy' => 'post_tag'
					),
					'search' => array(
						'title' => 'Search'
					),
				),
				'flush' => true
			),
			'languages-sidebar' => array(
				'widgets' => array(
					'text' => array(
						'text' => 'ORDER ONLINE OR CALL US (+1800) 000 8808'
					),
				),
				'flush' => true
			),
			'top-bar-right' => array(
				'widgets' => array(
					'etheme-socials' => array(
						'size' => 'small',
						'align' => 'right',
						'facebook' => '#',
						'twitter' => '#',
						'instagram' => '#',
						'google' => '#',
						'pinterest' => '#',
					),
				),
				'flush' => true
			),
			'mobile-sidebar' => array(
				'widgets' => array(
					'etheme-socials' => array(
						'size' => 'small',
						'align' => 'right',
						'facebook' => '#',
						'twitter' => '#',
						'instagram' => '#',
						'google' => '#',
						'pinterest' => '#',
					),
				),
				'flush' => true
			),
			'top-panel' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 4245
					)
				),
				'flush' => true
			),
			'shop-sidebar' => array(
				'widgets' => array(
					'woocommerce_product_categories' => array(
						'title' => 'Categories'
					),
					'woocommerce_price_filter' => array(
						'title' => 'Filter by price'
					),
					'woocommerce_layered_nav' => array(
						'title' => 'Filter by',
						'display_type' => 'list',
						'query_type' => 'and',
					),
				),
				'flush' => true
			),
			'shop-filters-sidebar' => array(
				'widgets' => array(
					'woocommerce_product_categories' => array(
						'title' => 'Product Categories',
						'dropdown' => 1,
						'hierarchical' => 1,
					),
					'woocommerce_price_filter' => array(
						'title' => 'Filter by price'
					),
				),
				'flush' => true
			),
			'shop-after-products' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 4246
					)
				),
				'flush' => true
			),
			'prefooter' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 4270
					)
				),
				'flush' => true
			),
			'footer-1' => array(
				'widgets' => array(
					'text' => array(
						'text' => '<p><img src="http://8theme.com/import/xstore/wp-content/uploads/2016/05/logo-footer.png" /></p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod lorem.
<br><br>
48 Park Avenue,<br>
East 21st Street, Apt. 304<br>
New York NY 10016<br>
Email: <mark><a href="mailto:youremail@site.com" >youremail@site.com</a></mark><br>
Phone: <mark>+1 408 996 1010</mark>'
					),
				),
				'flush' => true
			),
			'footer-2' => array(
				'widgets' => array(
					'text' => array(
						'title' => 'USEFUL LINKS',
						'text' => '<div class="row">
<div class="col-md-6">
<ul class="menu">
<li><a href="#">Home Page</a></li>
<li><a href="#">About Us</a></li>
<li><a href="#">Delivery Info</a></li>
<li><a href="#">Conditions</a></li>
<li><a href="#">Order Tracking</a></li>
<li><a href="#">My Account</a></li>
<li><a href="#">My Wishlist</a></li>
</ul>
</div>
<div class="col-md-6">
<ul class="menu">
<li><a href="#">London</a></li>
<li><a href="#">San Fransisco</a></li>
<li><a href="#">New Orlean</a></li>
<li><a href="#">Seatle</a></li>
<li><a href="#">Portland</a></li>
<li><a href="#">Stockholm</a></li>
<li><a href="#">Hoffenheim</a></li>
</ul>
</div>
</div>'
					),
				),
				'flush' => true
			),
			'footer-3' => array(
				'widgets' => array(
					'etheme-recent-posts' => array(
						'title' => 'Latest Posts',
						'number' => 3,
						'image' => 1,
						'post_type' => 'post',
						'query' => 'recent',
					),
				),
				'flush' => true
			),
			'footer-4' => array(
				'widgets' => array(
					'tag_cloud' => array(
						'title' => 'Product Tags',
						'taxonomy' => 'product_tag',
					),
				),
				'flush' => true
			),
			'footer-copyrights' => array(
				'widgets' => array(
					'text' => array(
						'text' => '© Created by <a href="#"><i class="fa fa-heart"></i> &nbsp;<strong>8theme</strong></a> - Power Elite ThemeForest Author.'
					),
				),
				'flush' => true
			),
			'footer-copyrights2' => array(
				'widgets' => array(
					'text' => array(
						'text' => '<p style="margin-bottom:0;"><a href="#"><img src="http://8theme.com/import/xstore/wp-content/uploads/2016/05/payments.png" /></a></p>'
					),
				),
				'flush' => true
			),
			'recent products' => array(
				'widgets' => array(
					'woocommerce_products' => array(
						'title' => 'Latest Products',
						'number' => 3,
						'order' => 'desc',
					),
				),
				'flush' => true
			),
			'sale products' => array(
				'widgets' => array(
					'woocommerce_products' => array(
						'title' => 'ON SALE',
						'number' => 3,
						'show' => 'onsale',
						'order' => 'desc',
					),
				),
				'flush' => true
			),
			'featured products' => array(
				'widgets' => array(
					'woocommerce_products' => array(
						'title' => 'Featured Products',
						'number' => 3,
						'show' => 'featured',
						'order' => 'desc',
					),
				),
				'flush' => true
			),
		),
		'custom-sidebars' => array(
			'recent products',
			'sale products',
			'featured products'
		)
	),
	'furniture' => array(
		'sidebar-widgets' => array(
			'footer-1' => array(
				'widgets' => array(
					'text' => array(
						'text' => '<p><img src="http://8theme.com/import/xstore/versions/wp-content/uploads/sites/2/2016/06/logo-fixed.png" /></p>
	Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod lorem.
	<br><br>
	48 Park Avenue,<br>
	East 21st Street, Apt. 304<br>
	New York NY 10016<br>
	Email: <mark><a href="mailto:youremail@site.com" >youremail@site.com</a></mark><br>
	Phone: <mark>+1 408 996 1010</mark>'
					),
				),
				'flush' => true
			),
			'footer-4' => array(
				'widgets' => array(
					'null-instagram-feed' => array(
						'title' => 'Instagram',
						'username' => 'mrorlandosoria',
						'number' => 6,
						'columns' => 3,
					),
				),
				'flush' => true
			),
		)
	),
	'cosmetics' => array(
		'sidebar-widgets' => array(
			'cosmetics-sidebar' => array(
				'widgets' => array(
					'woocommerce_product_categories' => array(
						'title' => 'Categories'
					),
					'etheme_widget_products' => array(
						'title' => 'Special Products',
						'number' => 10,
						'order' => 'desc',
						'slider' => 1,
					),
				),
				'flush' => true
			),
		),
		'custom-sidebars' => array(
			'cosmetics-sidebar'
		)
	),
	'engineer' => array(
		'sidebar-widgets' => array(
			'Engineer' => array(
				'widgets' => array(
					'woocommerce_product_categories' => array(
						'title' => 'Categories'
					)
				),
				'flush' => true
			),
			'Engineer posts' => array(
				'widgets' => array(
					'etheme-recent-posts' => array(
						'title' => 'Latest Posts',
						'number' => 3,
						'image' => 1,
						'post_type' => 'post',
						'query' => 'recent',
					),
				),
				'flush' => true
			),
			'prefooter' => array(
				'widgets' => array(
				),
				'flush' => true
			),
			'footer-1' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 4590
					)
				),
				'flush' => true
			),
		),
		'custom-sidebars' => array(
			'Engineer',
			'Engineer posts'
		)
	),
	'kids' => array(
		'sidebar-widgets' => array(
			'Kids newsletter' => array(
				'widgets' => array(
					'text' => array(
						'title' => 'NEWSLETTER',
						'text' => 'You can be always up to date with our company news!
[mc4wp_form]
<p>*Don’t worry, we won’t spam our customers mailboxes
</p>'
					),
					'etheme-socials' => array(
						'title' => 'Follow us',
						'size' => 'small',
						'align' => 'left',
						'facebook' => '#',
						'twitter' => '#',
						'instagram' => '#',
						'google' => '#',
						'pinterest' => '#',
					),
				),
				'flush' => true
			),
			'prefooter' => array(
				'widgets' => array(
				),
				'flush' => true
			),
			'footer-1' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 4645
					)
				),
				'flush' => true
			),
		),
		'custom-sidebars' => array(
			'Kids newsletter'
		)
	),
	'dark' => array(
		'sidebar-widgets' => array(
			'prefooter' => array(
				'widgets' => array(
				),
				'flush' => true
			),
		)
	),	
	'drinks' => array(
		'sidebar-widgets' => array(
			'prefooter' => array(
				'widgets' => array(
				),
				'flush' => true
			),
			'footer-1' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 4990
					)
				),
				'flush' => true
			),
		),
	),
	'bakery' => array(
		'sidebar-widgets' => array(
			'prefooter' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 5053
					)
				),
				'flush' => true
			),
			'footer-1' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 5052
					)
				),
				'flush' => true
			),
		),
	),
	'hipster' => array(
		'sidebar-widgets' => array(
			'prefooter' => array(
				'widgets' => array(
				),
				'flush' => true
			),
			'popular products' => array(
				'widgets' => array(
					'woocommerce_products' => array(
						'title' => 'Popular Products',
						'number' => 3,
						'show' => 'featured',
						'order' => 'desc',
					),
				),
				'flush' => true
			),
		),
		'custom-sidebars' => array(
			'popular products',
		)
	),
	'jewellery' => array(
		'sidebar-widgets' => array(
			'prefooter' => array(
				'widgets' => array(
				),
				'flush' => true
			),
			'footer-1' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 5098
					)
				),
				'flush' => true
			),
		)
	),
	'landing' => array(
		'sidebar-widgets' => array(
			'prefooter' => array(
				'widgets' => array(
				),
				'flush' => true
			),
			'footer-1' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 5131 //
					)
				),
				'flush' => true
			),
		)
	),
	'hosting' => array(
		'sidebar-widgets' => array(
			'prefooter' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 5183
					)
				),
				'flush' => true
			),
			'footer-1' => array(
				'widgets' => array(
					'etheme-static-block' => array(
						'block_id' => 5182 //
					)
				),
				'flush' => true
			),
		)
	),

);