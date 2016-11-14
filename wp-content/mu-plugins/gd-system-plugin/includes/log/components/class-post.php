<?php

namespace WPaaS\Log\Components;

use \WPaaS\Log\Event;
use \WPaaS\Log\Timer;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

class Post extends Component {

	/**
	 * Array of post types to always ignore.
	 *
	 * @var array
	 */
	protected $excluded_post_types = [
		'attachment',
		'nav_menu_item',
		'revision',
	];

	/**
	 * Array of post statuses to always ignore.
	 *
	 * @var array
	 */
	protected $excluded_post_statuses = [
		'auto-draft',
		'inherit',
	];

	/**
	 * Run on load.
	 */
	protected function load() {

		foreach ( get_post_types( [ 'hierarchical' => true ] ) as $post_type ) {

			$this->excluded_post_types[] = $post_type;

		}

	}

	/**
	 * Return the post revision ID for a given post.
	 *
	 * @param  \WP_Post $post
	 *
	 * @return int
	 */
	protected function get_post_revision_id( $post ) {

		if ( ! wp_revisions_enabled( $post ) ) {

			return '';

		}

		$revision = get_children(
			[
				'post_type'      => 'revision',
				'post_status'    => 'inherit',
				'post_parent'    => $post->ID,
				'posts_per_page' => 1,
				'orderby'        => 'post_date',
				'order'          => 'DESC',
			]
		);

		if ( ! $revision ) {

			return '';

		}

		$revision = array_values( $revision );

		return $revision[0]->ID;

	}

	/**
	 * Return a label for a given post type.
	 *
	 * @param  string $post_type
	 * @param  string $label (optional)
	 *
	 * @return string
	 */
	protected function get_post_type_label( $post_type, $label = 'singular_name' ) {

		$name = __( 'Post' );

		if ( post_type_exists( $post_type ) ) {

			$labels = get_post_type_object( $post_type )->labels;
			$name   = isset( $labels->{$label} ) ? $labels->{$label} : $name;

		}

		return $name;

	}

	/**
	 * Check if a post type is excluded.
	 *
	 * @param  string $post_type
	 *
	 * @return bool
	 */
	protected function is_excluded_post_type( $post_type ) {

		return in_array( $post_type, $this->excluded_post_types );

	}

	/**
	 * Check if a post status is excluded.
	 *
	 * @param  string $post_status
	 *
	 * @return bool
	 */
	protected function is_excluded_post_status( $post_status ) {

		return in_array( $post_status, $this->excluded_post_statuses );

	}

	/**
	 * {Post Type} > {Action}
	 *
	 * @action transition_post_status
	 *
	 * @param string   $new_status
	 * @param string   $old_status
	 * @param \WP_Post $post
	 */
	public function callback_transition_post_status( $new_status, $old_status, $post ) {

		if ( ! is_a( $post, 'WP_Post' ) ) {

			return;

		}

		if (
			$this->is_excluded_post_type( $post->post_type )
			||
			$this->is_excluded_post_status( $new_status )
		) {

			return;

		}

		Timer::stop();

		// Defaults
		$action  = 'update';
		$summary = _x(
			'"%1$s" %2$s updated',
			'1: Post title, 2: Post type singular name',
			'gd-system-plugin'
		);

		switch ( true ) {

			case ( $new_status === $old_status ) :

				$summary = _x(
					'"%1$s" %2$s updated',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

			case ( 'auto-draft' === $old_status ) :

				$action  = 'create';
				$summary = _x(
					'"%1$s" %2$s created',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

			case ( 'trash' === $old_status ) :

				$action  = 'restore';
				$summary = _x(
					'"%1$s" %2$s restored from trash',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

			case ( 'draft' === $new_status && 'publish' === $old_status ) :

				$summary = _x(
					'"%1$s" %2$s unpublished',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

			case ( 'draft' === $new_status ) :

				$summary = _x(
					'"%1$s" %2$s drafted',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

			case ( 'pending' === $new_status ) :

				$summary = _x(
					'"%1$s" %2$s pending review',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

			case ( 'future' === $new_status ) :

				$summary = _x(
					'"%1$s" %2$s scheduled for %3$s',
					'1: Post title, 2: Post type singular name, 3: Scheduled post date',
					'gd-system-plugin'
				);

				break;

			case ( 'publish' === $new_status && 'future' === $old_status ) :

				$summary = _x(
					'"%1$s" scheduled %2$s published',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

			case ( 'publish' === $new_status ) :

				$summary = _x(
					'"%1$s" %2$s published',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

			case ( 'private' === $new_status ) :

				$summary = _x(
					'"%1$s" %2$s privately published',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

			case ( 'trash' === $new_status ) :

				$action  = 'trash';
				$summary = _x(
					'"%1$s" %2$s trashed',
					'1: Post title, 2: Post type singular name',
					'gd-system-plugin'
				);

				break;

		}

		if (
			in_array( $new_status, [ 'publish', 'future' ] )
			||
			( in_array( $old_status, [ 'publish', 'future' ] ) && 'trash' === $new_status )
		) {

			$this->log_metric( 'publish' );

		}

		$this->log(
			$action,
			$summary,
			[
				'post_title'      => $post->post_title,
				'singular_name'   => strtolower( $this->get_post_type_label( $post->post_type ) ),
				'post_date'       => get_date_from_gmt( $post->post_date_gmt, __( 'M j, Y @ H:i' ) ),
				'post_date_gmt'   => Event::e_time( $post->post_date_gmt ),
				'post_id'         => $post->ID,
				'post_type'       => $post->post_type,
				'post_status'     => $new_status,
				'old_post_status' => $old_status,
				'revision_id'     => $this->get_post_revision_id( $post ),
				'sticky'          => is_sticky( $post->ID ),
			]
		);

	}

}
