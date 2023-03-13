<?php
/**
 * The core plugin class.
 *
 * This is used to define rest api endpoints
 *
 *
 * @since      1.0.0
 * @package    Safe_Media
 * @subpackage Safe_Media/includes
 * @author     Brijesh <brijeshborad29@gmail.com>
 */
class Safe_Media_Rest_Api {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Safe_Media_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->safe_media_attachment = new Safe_Media_Attachment();

	}

	/**
	 * This function is where we register our routes for our example endpoint.
	 */
	function register_assignment_route() {

		// Register a route to retrieve image information using image id
		register_rest_route(
			'assignment/v1',
			'/image/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'assignment_image_get_cb' ],
				'args'                => array(
					'id' => array(
						'required' => true
					),
				),
				'permission_callback' => function() {
					return true;
				}
			)
		);

		// Register a route to delete an image using image id
		register_rest_route(
			'assignment/v1',
			'/image/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'assignment_image_delete_cb' ],
				'args'                => array(
					'id' => array(
						'required' => true,
						'type'     => 'integer'
					),
				),
				'permission_callback' => function() {
					return current_user_can( 'delete_post' );
				}
			)
		);
	}

	/**
	 * This is the callback function that will send image's required information
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	function assignment_image_get_cb( WP_REST_Request $request ) {

		$image_id = $request->get_param( 'id' );
		$image    = get_post( $image_id );
		$response = new WP_REST_Response();

		if( !$image || $image->post_type !== 'attachment' ) {
			$response->set_status( 400 );
			$response->set_data( [
				                     'status'  => false,
				                     'message' => 'Seems like you are trying to fetch wrong resource.'
			                     ] );

			return rest_ensure_response( $response );
		}

		$response = array(
			'status' => true,
			'data'   => array(
				'id'               => $image->ID,
				'post_date'        => $image->post_date,
				'slug'             => $image->post_name,
				'type'             => get_post_mime_type( $image_id ),
				'link'             => wp_get_attachment_url( $image_id ),
				'alt_text'         => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
				'attached_objects' =>$this->safe_media_attachment->get_attached_objects( $image_id ),
			)
		);

		// rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
		return rest_ensure_response( $response );
	}


	/**
	 * This is the callback function to delete the image
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	function assignment_image_delete_cb( WP_REST_Request $request ) {

		$image_id = $request->get_param( 'id' );
		$image    = get_post( $image_id );
		$response = new WP_REST_Response();

		if( !current_user_can( 'delete_post', $image_id ) ) {
			$response->set_status( 403 );
			$response->set_data(
				[
					'status'  => false,
					'message' => 'You don\'t have permission to delete the image.'
				] );

			return rest_ensure_response( $response );
		}

		if( !$image || $image->post_type !== 'attachment' ) {
			$response->set_status( 400 );
			$response->set_data(
				[
					'status'  => false,
					'message' => 'Seems like you are trying to delete wrong image.'
				] );

			return rest_ensure_response( $response );
		}

		$objects = $this->safe_media_attachment->get_attached_objects( $image_id );

		if( !empty( $objects['posts'] ) || !empty( $objects['terms'] ) ) {
			$response->set_status( 400 );
			$response->set_data(
				[
					'status'  => false,
					'message' => __( 'Deletion is failed because image is attached to posts or terms.', SAFE_MEDIA_TEXT_DOMAIN )
				]
			);
		} else if( wp_delete_attachment( $image_id ) ) {
			$response->set_status( 200 );
			$response->set_data(
				[
					'status'  => true,
					'message' => __( 'Image is deleted successfully.', SAFE_MEDIA_TEXT_DOMAIN )
				]
			);
		} else {
			$response->set_status( 400 );
			$response->set_data(
				[
					'status'  => false,
					'message' => __( 'Error occurred while deleting image.', SAFE_MEDIA_TEXT_DOMAIN )
				]
			);
		}

		return rest_ensure_response( $response );
	}


}
