<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/brijesh2911/
 * @since      1.0.0
 *
 * @package    Safe_Media
 * @subpackage Safe_Media/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Safe_Media
 * @subpackage Safe_Media/includes
 * @author     Brijesh <brijeshborad29@gmail.com>
 */
class Safe_Media_Attachment {

	/**
	 * @var string
	 */
	private $_taxonomy = 'category';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

	}

	/**
	 * This function will return all post ids where the image is attached
	 *
	 * @param      $attachment_id
	 * @param bool $string
	 *
	 * @return int[]|string
	 */
	function get_linked_posts( $attachment_id, $string = true ) {

		// Get posts where attachment is set as featured image
		$featured_img_posts = get_posts(
			array(
				'post_type'   => 'post',
				'post_status' => 'publish',
				'numberposts' => - 1,
				'meta_key'    => '_thumbnail_id',
				'meta_value'  => $attachment_id,
				'fields'      => 'ids',
			) );

		// Get posts where image is attached or its parent
		$parent_posts = get_posts(
			array(
				'post_type'     => 'attachment',
				'attachment_id' => $attachment_id,
				'hide_empty'    => true,
				'fields'        => 'id=>parent',
			) );

		$posts = array_unique( array_filter( array_merge( $featured_img_posts, $parent_posts ) ) );

		if( !$string ) {
			return $posts;
		}

		// Convert array to clickable comma separated string
		$post_string = '';
		foreach( $posts as $post_id ) {
			$post_string .= '<a href="'.get_edit_post_link( $post_id ).'">'.$post_id.'</a>';
			if( next( $posts ) == true ) {
				$post_string .= ', ';
			}
		}

		return $post_string;
	}


	/**
	 * This function will return all term ids where the image is attached
	 *
	 * @param      $attachment_id
	 * @param bool $string
	 *
	 * @return int[]|string
	 */
	function get_linked_terms( $attachment_id, $string = true ) {
		$args = array(
			'hide_empty' => false,
			'taxonomy'   => $this->_taxonomy,
			'meta_query' => array(
				array(
					'key'     => 'safe_media_term_image_id',
					'value'   => $attachment_id,
					'compare' => '='
				)
			),
			'fields'     => 'ids'
		);

		$terms = get_terms( $args );

		if( !$string ) {
			return $terms;
		}

		// Convert array to clickable comma separated string
		$term_string = '';
		foreach( $terms as $term_id ) {
			$term_string .= '<a href="'.get_edit_term_link( $term_id ).'">'.$term_id.'</a>';
			if( next( $terms ) == true ) {
				$term_string .= ', ';
			}
		}

		return $term_string;
	}


}
