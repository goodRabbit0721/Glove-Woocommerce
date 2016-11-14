<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Blog
// **********************************************************************//

class ETheme_Blog_Shortcodes {

	public function __construct() {
		$this->init();
	}

	public function init() {
		$this->init_VC();
	}

	public function blog( $atts = array() ) {

		$atts = $this->get_atts( $atts, array(
			'blog_layout' => ''
		) );


		return $this->get_grid( $atts );

	}

	public function blog_timeline( $atts = array() ) {

		$atts = $this->get_atts( $atts, array(
			'blog_layout' => 'timeline'
		) );

		return $this->get_grid( $atts );

	}

	public function blog_list( $atts = array() ) {

		$atts = $this->get_atts( $atts, array(
			'blog_layout' => 'small'
		) );

		return $this->get_grid( $atts );

	}

	public function blog_carousel( $atts = array() ) {

		$atts = $this->get_atts( $atts, array(
			'blog_layout' => '',
			'blog_align' => 'left',
			'large' => 4,
			'notebook' => 3,
			'tablet_land' => 2,
			'tablet_portrait' => 2,
			'mobile' => 1,
			'slider_autoplay' => false,
			'slider_speed' => 10000,
			'hide_pagination' => false,
			'hide_buttons' => false,
		) );

		return $this->get_carousel( $atts );

	}

	public function get_atts( $atts = array(), $additional_atts ) {

		$dafault_atts = $this->get_default_atts($additional_atts);

		return shortcode_atts( $dafault_atts, $atts);
	}

	public function get_default_atts( $additional_atts = array() ) {
		$default_atts = array_merge( $additional_atts, array(
			'slide_view'  => 'vertical',
			'size' => 'medium',
			'class'  => '',
		) );
		return array_merge( $this->get_query_atts(), $default_atts );
	}

	public function get_query_atts() {
		return array(
			'post_type'  => 'post',
			'include'  => '',
			'custom_query'  => '',
			'taxonomies'  => '',
			'items_per_page'  => 10,
			'orderby'  => 'date',
			'order'  => 'DESC',
			'meta_key'  => '',
			'blog_hover'  => '',
			'exclude'  => '',
		);
	}

	public function get_query_args( $atts = array() ) {

		extract(shortcode_atts(array(
			'post_type'  => 'post',
			'include'  => '',
			'custom_query'  => '',
			'taxonomies'  => '',
			'items_per_page'  => 10,
			'orderby'  => 'date',
			'order'  => 'DESC',
			'meta_key'  => '',
			'exclude'  => '',
		), $atts));


		$paged = (get_query_var('page')) ? get_query_var('page') : 1;

		$args = array(
			'post_type' => 'post',
			'status' => 'published',
			'paged' => $paged,
			'posts_per_page' => $items_per_page
		);

		if($post_type == 'ids' && $include != '') {
			$args['post__in'] = explode(',', $include);
		}


		if(!empty( $exclude ) ) {
			$args['post__not_in'] = explode(',', $exclude);
		}

		if(!empty( $taxonomies )) {
			$taxonomy_names = get_object_taxonomies( 'post' );
			$terms = get_terms( $taxonomy_names, array(
				'orderby' => 'name',
				'include' => $taxonomies
			));

			if( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$args['tax_query'] = array('relation' => 'OR');
				foreach ($terms as $key => $term) {
					$args['tax_query'][] = array(
						'taxonomy' => $term->taxonomy,                //(string) - Taxonomy.
						'field' => 'slug',                    //(string) - Select taxonomy term by ('id' or 'slug')
						'terms' => array( $term->slug ),    //(int/string/array) - Taxonomy term(s).
						'include_children' => true,           //(bool) - Whether or not to include children for hierarchical taxonomies. Defaults to true.
						'operator' => 'IN'
					);
				}
			}
		}

		if(!empty( $order )) {
			$args['order'] = $order;
		}

		if(!empty( $meta_key )) {
			$args['meta_key'] = $meta_key;
		}

		if(!empty( $orderby )) {
			$args['orderby'] = $orderby;
		}

		return $args;

	}

	public function get_query( $atts = array() ) {

		$args = $this->get_query_args( $atts );

		return new WP_Query($args);

	}

	public function get_grid( $atts = array() ) {
		global $et_loop;

		$query = $this->get_query( $atts );

		ob_start();

		$et_loop['columns'] = 2;
		$et_loop['loop'] = 0;
		$et_loop['blog_layout'] = $atts['blog_layout'];
		$et_loop['blog_hover'] = $atts['blog_hover'];
		$et_loop['size'] = $atts['size'];

		$smaller_from = 8;

		if( ! empty( $atts['blog_layout'] ) ) {
			$smaller_from = 999;
		}

		$paged = (get_query_var('page')) ? get_query_var('page') : 1;

		$start_post = ($paged - 1) * $atts['items_per_page'] + 1;
		$last_post = ($paged - 1) * $atts['items_per_page']  + $query->post_count;
		?>
		<div class="et-blog">
			<?php

			$_i = 0;

			while ( $query->have_posts() ) {
				$query->the_post();
				$_i++;
				if( $_i == $smaller_from ) {
					$et_loop['size'] = 'thumbnail';
					echo '<div class="posts-small">';
				}

				if( $_i == 1 && empty($et_loop['blog_layout']) ) {
					$et_loop['size'] = 'large';
				}

				if( $et_loop['blog_layout'] == 'timeline' || $et_loop['blog_layout'] == 'small' || $et_loop['blog_layout'] == 'title-left' ) {
					get_template_part( 'content' );
				} else {
					get_template_part( 'content', 'grid' );
				}


				if( $_i == 1 && empty($et_loop['blog_layout']) ) {
					$et_loop['size'] = $atts['size'];
				}
			}

			if( $_i >= $smaller_from ) {
				echo '</div>';
			}
			?>
			<?php if ($query->max_num_pages > 0): ?>
				<div class="et-blog-bottom">
					<div class="et-shown-posts">
						<?php printf(__('Showing: %d-%d shown from %s', 'xstore'), $start_post, $last_post, $query->found_posts) ?>
					</div>

					<?php etheme_pagination($query, $paged); ?>
				</div>
			<?php endif ?>
		</div>
		<?php

		wp_reset_postdata();
		unset($et_loop);

		$output = ob_get_clean();
		ob_flush();

		return $output;
	}

	public function get_carousel( $atts = array() ) {
		global $et_loop;

		$args = $this->get_query_args( $atts );

		$et_loop['blog_hover'] = $atts['blog_hover'];
		$et_loop['slide_view'] = $atts['slide_view'];
		$et_loop['blog_align'] = $atts['blog_align'];
		$et_loop['size'] = $atts['size'];

		ob_start();

		etheme_create_posts_slider( $args, false, $atts );

		$output = ob_get_clean();

		ob_flush();

		return $output;
	}


	public function get_params() {

		$post_types_list = array();
		$post_types_list[] = array( 'post', esc_html__( 'Post', 'xstore' ) );
		//$post_types_list[] = array( 'custom', esc_html__( 'Custom query', 'xstore' ) );
		$post_types_list[] = array( 'ids', esc_html__( 'List of IDs', 'xstore' ) );

		$params_array = array(
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Data source', 'xstore' ),
				'param_name' => 'post_type',
				'value' => $post_types_list,
				'description' => esc_html__( 'Select content type for your grid.', 'xstore' )
			),
			array(
				'type' => 'autocomplete',
				'heading' => esc_html__( 'Include only', 'xstore' ),
				'param_name' => 'include',
				'description' => esc_html__( 'Add posts, pages, etc. by title.', 'xstore' ),
				'settings' => array(
					'multiple' => true,
					'sortable' => true,
					'groups' => true,
				),
				'dependency' => array(
					'element' => 'post_type',
					'value' => array( 'ids' ),
					//'callback' => 'vc_grid_include_dependency_callback',
				),
			),
			// Custom query tab
			array(
				'type' => 'textarea_safe',
				'heading' => esc_html__( 'Custom query', 'xstore' ),
				'param_name' => 'custom_query',
				'description' => __( 'Build custom query according to <a href="http://codex.wordpress.org/Function_Reference/query_posts">WordPress Codex</a>.', 'xstore' ),
				'dependency' => array(
					'element' => 'post_type',
					'value' => array( 'custom' ),
				),
			),
			array(
				'type' => 'autocomplete',
				'heading' => esc_html__( 'Narrow data source', 'xstore' ),
				'param_name' => 'taxonomies',
				'settings' => array(
					'multiple' => true,
					// is multiple values allowed? default false
					// 'sortable' => true, // is values are sortable? default false
					'min_length' => 1,
					// min length to start search -> default 2
					// 'no_hide' => true, // In UI after select doesn't hide an select list, default false
					'groups' => true,
					// In UI show results grouped by groups, default false
					'unique_values' => true,
					// In UI show results except selected. NB! You should manually check values in backend, default false
					'display_inline' => true,
					// In UI show results inline view, default false (each value in own line)
					'delay' => 500,
					// delay for search. default 500
					'auto_focus' => true,
					// auto focus input, default true
					// 'values' => $taxonomies_for_filter,
				),
				'param_holder_class' => 'vc_not-for-custom',
				'description' => esc_html__( 'Enter categories, tags or custom taxonomies.', 'xstore' ),
				'dependency' => array(
					'element' => 'post_type',
					'value_not_equal_to' => array( 'ids', 'custom' ),
				),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Items per page', 'xstore' ),
				'param_name' => 'items_per_page',
				'description' => esc_html__( 'Number of items to show per page.', 'xstore' ),
				'value' => '10',
				/*'dependency' => array(
                    'element' => 'style',
                    'value' => array( 'lazy', 'load-more', 'pagination' ),
                ),
                'edit_field_class' => 'vc_col-sm-6 vc_column',*/
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Images size', 'js_composer' ),
				'param_name' => 'size',
				'value' => '',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Blog hover effect', 'xstore' ),
				'param_name' => 'blog_hover',
				'value' => array(
					'Default' => 'default',
					'Zoom' => 'zoom',
					'Animated' => 'animated',
				),
			),
			// Data settings
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Order by', 'xstore' ),
				'param_name' => 'orderby',
				'value' => array(
					__( 'Date', 'xstore' ) => 'date',
					__( 'Order by post ID', 'xstore' ) => 'ID',
					__( 'Author', 'xstore' ) => 'author',
					__( 'Title', 'xstore' ) => 'title',
					__( 'Last modified date', 'xstore' ) => 'modified',
					__( 'Post/page parent ID', 'xstore' ) => 'parent',
					__( 'Number of comments', 'xstore' ) => 'comment_count',
					__( 'Menu order/Page Order', 'xstore' ) => 'menu_order',
					__( 'Meta value', 'xstore' ) => 'meta_value',
					__( 'Meta value number', 'xstore' ) => 'meta_value_num',
					// esc_html__('Matches same order you passed in via the 'include' parameter.', 'js_composer') => 'post__in'
					__( 'Random order', 'xstore' ) => 'rand',
				),
				'description' => esc_html__( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'xstore' ),
				'group' => esc_html__( 'Data Settings', 'xstore' ),
				'param_holder_class' => 'vc_grid-data-type-not-ids',
				'dependency' => array(
					'element' => 'post_type',
					'value_not_equal_to' => array( 'ids', 'custom' ),
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Sorting', 'xstore' ),
				'param_name' => 'order',
				'group' => esc_html__( 'Data Settings', 'xstore' ),
				'value' => array(
					__( 'Descending', 'xstore' ) => 'DESC',
					__( 'Ascending', 'xstore' ) => 'ASC',
				),
				'param_holder_class' => 'vc_grid-data-type-not-ids',
				'description' => esc_html__( 'Select sorting order.', 'xstore' ),
				'dependency' => array(
					'element' => 'post_type',
					'value_not_equal_to' => array( 'ids', 'custom' ),
				),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Meta key', 'xstore' ),
				'param_name' => 'meta_key',
				'description' => esc_html__( 'Input meta key for grid ordering.', 'xstore' ),
				'group' => esc_html__( 'Data Settings', 'xstore' ),
				'param_holder_class' => 'vc_grid-data-type-not-ids',
				'dependency' => array(
					'element' => 'orderby',
					'value' => array( 'meta_value', 'meta_value_num' ),
				),
			),
			/*array(
                'type' => 'textfield',
                'heading' => esc_html__( 'Offset', 'xstore' ),
                'param_name' => 'offset',
                'description' => esc_html__( 'Number of grid elements to displace or pass over.', 'xstore' ),
                'group' => esc_html__( 'Data Settings', 'xstore' ),
                'param_holder_class' => 'vc_grid-data-type-not-ids',
                'dependency' => array(
                    'element' => 'post_type',
                    'value_not_equal_to' => array( 'ids', 'custom' ),
                ),
            ),*/
			array(
				'type' => 'autocomplete',
				'heading' => esc_html__( 'Exclude', 'xstore' ),
				'param_name' => 'exclude',
				'description' => esc_html__( 'Exclude posts, pages, etc. by title.', 'xstore' ),
				'group' => esc_html__( 'Data Settings', 'xstore' ),
				'settings' => array(
					'multiple' => true,
				),
				'param_holder_class' => 'vc_grid-data-type-not-ids',
				'dependency' => array(
					'element' => 'post_type',
					'value_not_equal_to' => array( 'ids', 'custom' ),
					'callback' => 'vc_grid_exclude_dependency_callback',
				),
			)

		);

		return $params_array;
	}

	public function get_carousel_params() {
		return array_merge($this->get_params(), array(
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Slide view', 'xstore' ),
				'param_name' => 'slide_view',
				'value' => array(
					'Vertical' => 'vertical',
					'Horizontal' => 'horizontal',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Blog align', 'xstore' ),
				'param_name' => 'blog_align',
				'value' => array(
					'Left' => 'left',
					'Center' => 'center',
					'Right' => 'right',
				),
			),
		), etheme_get_slider_params());
	}


	public function init_VC() {
		if(!function_exists('vc_map')) return;

		$blog_shortcodes = array( 'et_blog', 'et_blog_timeline', 'et_blog_list', 'et_blog_carousel');

		foreach ($blog_shortcodes as $shortcode) {
			add_filter( 'vc_autocomplete_' . $shortcode . '_include_callback', 'vc_include_field_search', 10, 1 );
			add_filter( 'vc_autocomplete_' . $shortcode . '_include_render', 'vc_include_field_render', 10, 1 );

			add_filter( 'vc_autocomplete_' . $shortcode . '_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
			add_filter( 'vc_autocomplete_' . $shortcode . '_taxonomies_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

			add_filter( 'vc_autocomplete_' . $shortcode . '_exclude_filter_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
			add_filter( 'vc_autocomplete_' . $shortcode . '_exclude_filter_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

			add_filter( 'vc_autocomplete_' . $shortcode . '_exclude_callback', 'vc_exclude_field_search', 10, 1 );
			add_filter( 'vc_autocomplete_' . $shortcode . '_exclude_render', 'vc_exclude_field_render', 10, 1 );
		}


		$params = array(
			'name' => '[8THEME] Blog',
			'base' => 'et_blog',
			'icon' => ETHEME_CODE_IMAGES . 'icon-blog.png',
			'category' => 'Eight Theme',
			'params' => $this->get_params()
		);

		$params_timeline = array(
			'name' => '[8THEME] Blog Timeline',
			'base' => 'et_blog_timeline',
			'icon' => ETHEME_CODE_IMAGES . 'vc/el-timeline.png',
			'category' => 'Eight Theme',
			'params' => $this->get_params()
		);

		$params_list = array(
			'name' => '[8THEME] Blog List',
			'base' => 'et_blog_list',
			'icon' => ETHEME_CODE_IMAGES . 'vc/el-blog-list.png',
			'category' => 'Eight Theme',
			'params' => $this->get_params()
		);

		$params_carousel = array(
			'name' => '[8THEME] Blog carousel',
			'base' => 'et_blog_carousel',
			'icon' => ETHEME_CODE_IMAGES . 'icon-blog.png',
			'category' => 'Eight Theme',
			'params' => $this->get_carousel_params()
		);

		vc_map($params);
		vc_map($params_timeline);
		vc_map($params_list);
		vc_map($params_carousel);
	}
}
