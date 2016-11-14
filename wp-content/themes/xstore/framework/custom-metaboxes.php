<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 */


add_action( 'cmb2_admin_init', 'etheme_base_metaboxes');
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
 
if(!function_exists('etheme_base_metaboxes')) {
	function etheme_base_metaboxes() {

		// Start with an underscore to hide fields from custom fields list
		$prefix = '_et_';

	    $cmb = new_cmb2_box( array(
			'id'         => 'page_metabox',
			'title'      => esc_html__( '[8theme] Layout options', 'xstore' ),
			'object_types'      => array( 'page', 'post'), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
	        // 'cmb_styles' => false, // false to disable the CMB stylesheet
	        // 'closed'     => true, // Keep the metabox closed by default
	    ) );

	    $cmb->add_field( array(
	            'id'          => ETHEME_PREFIX .'custom_logo',
	            'name'        => 'Custom logo for this page/post',
			    'desc' => 'Upload an image or enter an URL.',
			    'type' => 'file',
			    'allow' => array( 'url', 'attachment' ) // limit to just attachments with array( 'attachment' )
	        )
    	);

	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'header_overlap',
	            'name'        => 'Header overlap',
	            'default'     => false,
	            'type'        => 'checkbox'
	        )
    	);

	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'header_bg',
	            'name'        => 'Header background color',
			    'type' => 'colorpicker',
	        )
    	);

	    $cmb->add_field(array(
	            'id'          => ETHEME_PREFIX .'header_color',
	            'name'        => 'Header text color',
	            'type'        => 'radio',
	            'options'     => array(
	                'inherit' => 'Inherit',
	                'dark' => 'Dark',
	                'white' => 'White',
	            )
	        ) 
    	);

	    $cmb->add_field(array(
	            'id'          => ETHEME_PREFIX .'sidebar_state',
	            'name'        => 'Sidebar Position',
	            'type'        => 'radio',
	            'options'     => array(
	                'default' => 'Inherit',
	                'without' => 'Without',
	                'left' => 'Left',
	                'right' => 'Right' 
	            )
	        ) 
    	);

	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'widget_area',
	            'name'        => 'Widget Area',
	            'type'        => 'select',
	            'options'     => etheme_get_sidebars()
	        )
    	);

	    $cmb->add_field( array(
		        'id'          => ETHEME_PREFIX .'sidebar_width',
		        'name'        => 'Sidebar width',
		        'type'        => 'radio',
		        'options'     => array(
	                '' => 'Inherit', 
	                2 => '1/6', 
	                3 => '1/4', 
	                4 => '1/3' 
	            )
		    )
    	);

	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'custom_nav',
	            'name'       => 'Custom navigation',
	            'type'        => 'select',
	            'options'     => etheme_get_menus_options()
	        )
    	);

	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'custom_nav_right',
	            'name'       => 'Custom navigation right (for double menu header)',
	            'type'        => 'select',
	            'options'     => etheme_get_menus_options()
	        )
    	);

	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'custom_nav_mobile',
	            'name'       => 'Custom navigation for mobile',
	            'type'        => 'select',
	            'options'     => etheme_get_menus_options()
	        )
    	);

	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'one_page',
	            'name'        => 'One page navigation',
	            'default'     => false,
	            'type'        => 'checkbox'
	        )
    	);
    	
	    $cmb->add_field( 
			array(
				'id'          => ETHEME_PREFIX .'breadcrumb_type',
				'name'        => 'Breadcrumbs Style',
				'type'        => 'select',
				'options'     => array(
					''   => '',
					'default'   => 'Center',
					'left'   => 'Align left',
					'left2' => 'Left inline',
					'disable'   => 'Disable',
				)
			)
    	);
    	
	    $cmb->add_field( 
			array(
				'id'          => ETHEME_PREFIX .'breadcrumb_effect',
				'name'        => 'Breadcrumbs Effect',
				'type'        => 'select',
				'class'       => '',
				'options'     => array(
					''   => '',
					'none' => 'None',
					'mouse' => 'Parallax on mouse move',
					'text-scroll' => 'Text animation on scroll',
				)
			)
    	);
    	
	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'page_slider',
	            'name'        => 'Page slider',
	            'desc'        => 'Show revolution slider instead of breadcrumbs and page title',
	            'type'        => 'select',
	            'options'     => etheme_get_revsliders()
	        )
    	);
    	
	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'custom_footer',
	            'name'        => 'Use custom footer for this page/post',
	            'type'        => 'select',
	            'options'     => etheme_get_post_options( array( 'post_type' => 'staticblocks', 'numberposts' => 100 ) ),
	        )
    	);
	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'bg_image',
	            'name'        => 'Custom background image',
			    'desc' => 'Upload an image or enter an URL.',
			    'type' => 'file',
			    'allow' => array( 'url', 'attachment' ) // limit to just attachments with array( 'attachment' )
	        )
    	);
	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'bg_color',
	            'name'        => 'Custom background color',
			    'type' => 'colorpicker',
	        )
    	);

		$static_blocks = array();
		$static_blocks[] = "--choose--";
		
		foreach (etheme_get_static_blocks() as $block) {
			$static_blocks[$block['value']] = $block['label'];
		}

	    $cmb = new_cmb2_box( array(
			'id'         => 'product_metabox',
			'title'      => esc_html__( '[8theme] Product Options', 'xstore' ),
			'object_types'      => array( 'product', ), // Post type
			'context'    => 'normal',
			'priority'   => 'low',
			'show_names' => true, // Show field names on the left
	    ) );

	    $cmb->add_field( 
			array(
				'name' => 'Product layout',
				'id' => ETHEME_PREFIX . 'single_layout',
				'type' => 'select',
				'options'          => array(
					'standard' => esc_html__( 'Inherit', 'xstore' ),
					'small' => esc_html__( 'Small', 'xstore' ),
					'default' => esc_html__( 'Default', 'xstore' ),
					'large' => esc_html__( 'Large', 'xstore' ),
					'fixed' => esc_html__( 'Fixed', 'xstore' ),
					'center' => esc_html__( 'Center', 'xstore' ),
					'xsmall' => esc_html__( 'Thin description', 'xstore' ),
					'wide' => esc_html__( 'Wide', 'xstore' ),
				),
			)
    	);
    	
	    $cmb->add_field( 
			array(
				'name' => 'Additional custom block',
				'id' => $prefix . 'additional_block',
				'type'    => 'select',
				'options' => $static_blocks
			)
    	);


    	
	    $cmb->add_field( 
			array(
				'name' => 'Disable sidebar',
				'id' => $prefix . 'disable_sidebar',
				'type'    => 'checkbox',
			)
    	);


    	
	    $cmb->add_field( 
			array(
				'name' => 'Disable thumbnails gallery',
				'id' => $prefix . 'disable_gallery',
				'type'    => 'checkbox',
			)
    	);


    	
	    $cmb->add_field( 
	        array(
	            'id'          => ETHEME_PREFIX .'size_guide_img',
	            'name'        => 'Size guide image',
			    'desc' => 'Upload an image or enter an URL.',
			    'type' => 'file',
			    'allow' => array( 'url', 'attachment' ) // limit to just attachments with array( 'attachment' )
	        )
    	);


	
		$product_category_options = array(
			'auto' => '--Auto--',
		);

		$terms = get_terms( 'product_cat', 'hide_empty=0' );

		foreach ( $terms as $term ) {
			$product_category_options[$term->slug] = $term->name;
		}


	    $cmb->add_field( 
			array(
			    'name' => 'Primary category',
			    'id' => $prefix . 'primary_category',
			    'type' => 'select',
			    'options' => $product_category_options
			)
    	);

	
		$category_options = array(
			'auto' => '--Auto--',
		);

		$terms = get_terms( 'category', 'hide_empty=0' );

		foreach ( $terms as $term ) {
			$category_options[$term->slug] = $term->name;
		}


	    $cmb = new_cmb2_box( array(
			'id'         => 'post_metabox',
			'title'      => esc_html__( '[8theme] Post Options', 'xstore' ),
			'object_types'      => array( 'post', ), // Post type
			'context'    => 'normal',
			'priority'   => 'low',
			'show_names' => true, // Show field names on the left
	    ) );

    	
	    $cmb->add_field( 
			array(
			    'name' => 'Post template',
			    'id' => $prefix . 'post_template',
			    'type' => 'select',
			    'options'          => array(
			        '' => esc_html__( 'Inherit', 'xstore' ),
			        'default' => esc_html__( 'Default', 'xstore' ),
			        'full-width' => esc_html__( 'Large', 'xstore' ),
			        'large' => esc_html__( 'Full width', 'xstore' ),
			        'large2' => esc_html__( 'Full width centered', 'xstore' ),
			    ),
			)
    	);


    	
	    $cmb->add_field( 
			array(
			    'name' => 'Hide featured image on single',
			    'id' => $prefix . 'post_featured',
			    'type' => 'checkbox',
			    'value' => 'enable'
			)
    	);
    	
	    $cmb->add_field( 
			array(
			    'name' => 'Post featured video (for video post format)',
			    'id' => $prefix . 'post_video',
			    'type' => 'text_medium',
			    'desc' => 'Paste a link from Vimeo or Youtube, it will be embeded in the post'
			)
    	);
    	
	    $cmb->add_field( 
			array(
			    'name' => 'Soundcloud audio shortcode (for audio post format)',
			    'id' => $prefix . 'post_audio',
			    'type' => 'text_medium',
			)
    	);
    	
	    $cmb->add_field( 
			array(
			    'name' => 'Quote (for quote post format)',
			    'id' => $prefix . 'post_quote',
			    'type' => 'textarea',
			)
    	);
    	
	    $cmb->add_field( 
			array(
			    'name' => 'Primary category',
			    'id' => $prefix . 'primary_category',
			    'type' => 'select',
			    'options' => $category_options
			)
    	);
	}
}