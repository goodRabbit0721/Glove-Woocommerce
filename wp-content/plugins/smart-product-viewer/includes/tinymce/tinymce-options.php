<?php

$product = array (

	"id" => array (
		"type"		=> 'post',
		"post_type" => 'smart-product',
		"value"		=> '',
		"title"		=> 'Smart Product',
		"desc"		=> '',
	),

	"width" => array (
		"type"	=> 'text',
		"value"	=> '',
		"units"	=> 'px',
		"title"	=> 'Width',
		"desc"	=> 'Keep it empty to use original image size' 
	),

	"border" => array (
		"type"	=> 'select',
		"value"	=> 'true',
		"options"	=> array (
			"true" 	=> 'Show',
			"false" => 'Don\'t Show',
			),
		"title"	=> 'Border',
		"desc"	=> 'Choose if you want to show or hide viewer border',
	),

	"autoplay" => array (
		"type"	=> 'select',
		"value"	=> 'false',
		"options"	=> array (
			"true" 	=> 'True',
			"false" => 'False',
			),
		"title"	=> 'Autoplay',
		"desc"	=> 'Set to True to make it autoplay on load',
	),

	"interval" => array (
		"type"	=> 'text',
		"value"	=> '40',
		"title"	=> 'Frames Interval',
		"units" => 'ms',
		"desc"	=> 'Time beetween frames change. Increase to slow down product spin'
	),

	"fullscreen" => array (
		"type"	=> 'select',
		"value"	=> 'false',
		"options"	=> array (
			"true" 	=> 'True',
			"false" => 'False',
			),
		"title"	=> 'Fullscreen Lightbox',
		"desc"	=> 'Set to True to allow users open product in fullscreen lightbox',
	),

	"move_on_scroll" => array (
		"type"	=> 'select',
		"value"	=> 'false',
		"options"	=> array (
			"true" 	=> 'True',
			"false" => 'False',
			),
		"title"	=> 'Move On Scroll',
		"desc"	=> 'Set to True to animate product on page scroll',
	),

	"move_on_hover" => array (
		"type"	=> 'select',
		"value"	=> 'false',
		"options"	=> array (
			"true" 	=> 'True',
			"false" => 'False',
			),
		"title"	=> 'Move On Hover',
		"desc"	=> 'Set to True to animate product on mouse hover',
	),
);

$navigation = array (

	"nav" => array (
		"type"	=> 'select',
		"value"	=> 'true',
		"options"	=> array (
			"true" 	=> 'Show',
			"false" => 'Don\'t Show',
			),
		"title"	=> 'Icons',
		"desc"	=> 'Choose if you want to show or hide navigation icons',
		"condition" => array (
				"scrollbar" => "false"
			)
	),

	"scrollbar" => array (
		"type"	=> 'select',
		"value"	=> 'false',
		"options"	=> array (
			"false" 	=> 'Don\'t Show',
			"top" 		=> 'Top',
			"bottom" 	=> 'Bottom',
			),
		"title"	=> 'Scrollbar',
		"desc"	=> 'Choose if you want to show scrollbar on top or bottom or hise it',
	),
	
	"color"	=> array (
		"type"	=> 'select',
		"value"	=> 'gray',
		"options"   => array (
			"dark-blue" 	=> 'Dark Blue',
			"light-blue" 	=> 'Light Blue',
			"red" 			=> 'Red',
			"brown" 		=> 'Brown',
			"purple" 		=> 'Purple',
			"gray" 			=> 'Gray',
			"yellow" 		=> 'Yellow',
			"green" 		=> 'Green', 
			),
		"title"  => 'Color',
		"desc"  => 'Icons or Scrollbar color',
	),

	"style"	=> array (
		"type"	=> 'select',
		"value"	=> 'glow',
		"options"   => array (
			"glow" 			=> 'Glow',
			"fancy" 		=> 'Fancy',
			"wave" 			=> 'Wave',
			"flat-round" 	=> 'Flat Round',
			"flat-square" 	=> 'Flat Square',
			"vintage" 		=> 'Vintage',
			"arrows" 		=> 'Arrows',
			"leather" 		=> 'Leather',
			),
		"title"  => 'Style',
		"desc"  => 'Icons Style',
	),
 
);



$params = array (
	"product" => array (
		"title" 	=> '<div class="dashicons dashicons-visibility"></div> Viewer',
		"params"	=> $product,
		),

	"navigation" => array (
		"title" 	=> '<div class="dashicons dashicons-editor-code"></div> Navigation',
		"params"	=> $navigation,
		),
	);

// Create instance
$spv_tinymce = new SmartProductViewerTinyMCE( 'spv_', $params );

?>