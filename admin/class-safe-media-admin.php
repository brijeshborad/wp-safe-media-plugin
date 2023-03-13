<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/brijesh2911/
 * @since      1.0.0
 *
 * @package    Safe_Media
 * @subpackage Safe_Media/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Safe_Media
 * @subpackage Safe_Media/admin
 * @author     Brijesh <brijeshborad29@gmail.com>
 */
class Safe_Media_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;


	/**
	 * @var string
	 */
	private $_taxonomy = 'category';

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Safe_Media_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ).'css/safe-media-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Safe_Media_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ).'js/safe-media-admin.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Register the Hooks for the admin area.
	 *
	 * @since    1.0.0
	 */

	public function admin_init() {

		// Register hooks to show attached objects for media library list
		add_filter( 'manage_media_columns', [ $this, 'media_columns_filter' ] );
		add_action( 'manage_media_custom_column', [ $this, 'media_custom_column_action' ], 10, 2 );

		// Register hook to show attached objects for media library Popup
		add_filter( 'attachment_fields_to_edit', [ $this, 'media_attachment_fields_filter' ], 10, 2 );

		// Register filter hook to prevent deletion of attached image with post or term
		add_filter( 'pre_delete_attachment', [ $this, 'pre_delete_attachment_filter' ], 10, 3 );
	}


	/**
	 *
	 */
	public function register_image_box_for_term() {
		/**
		 * Register CMB2 box for category taxonomy
		 */
		$cmb = new_cmb2_box(
			array(
				'id'           => 'safe-media-term-image-mb',
				'title'        => '',
				'object_types' => array( 'term' ),
				'taxonomies'   => array( $this->_taxonomy )
			) );

		/**
		 * Add Image field to choose the image
		 */
		$cmb->add_field(
			array(
				'name'         => 'Image',
				'id'           => 'safe_media_term_image',
				'type'         => 'file',
				'preview_size' => array( 150, 150 ),
				'options'      => array( 'url' => false ),
				'text'         => array( 'add_upload_file_text' => 'Choose Image' ),
				'column'       => array(
					'position'         => 1,
					'name'             => 'Image',
					'disable_sortable' => true
				),
				'query_args'   => array(
					'type' => array( 'image/jpeg', 'image/png' ),
				),
			)
		);

	}


	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	function media_columns_filter( $columns ) {
		$columns['colAttachedObjects'] = __( 'Attached Objects' );

		return $columns;
	}


	/**
	 * @param $column_name
	 * @param $attachment_id
	 */
	function media_custom_column_action( $column_name, $attachment_id ) {
		if( $column_name !== 'colAttachedObjects' ) {
			return;
		}
		$objects = $this->safe_media_attachment->get_attached_objects( $attachment_id, true );

		echo $objects['posts'] ? 'Articles <br/>'.$objects['posts'].'<br/>' : '';
		echo $objects['terms'] ? 'Terms <br/>'.$objects['terms'] : '';
	}


	/**
	 * @param $form_fields
	 * @param $attachment
	 *
	 * @return mixed
	 */
	function media_attachment_fields_filter( $form_fields, $attachment ) {
		$objects = $this->safe_media_attachment->get_attached_objects( $attachment->ID, true );

		if( !empty( $objects['posts'] ) ) {
			$form_fields['linked_articles'] = array(
				'label' => __( 'Linked Articles', SAFE_MEDIA_TEXT_DOMAIN ),
				'input' => 'html',
				'html'  => $objects['posts']
			);
		}

		if( !empty( $objects['terms'] ) ) {
			$form_fields['linked_terms'] = array(
				'label' => __( 'Linked Terms', SAFE_MEDIA_TEXT_DOMAIN ),
				'input' => 'html',
				'html'  => $objects['terms']
			);
		}

		return $form_fields;
	}

	/**
	 * This function will be used to prevent deletion of image attached with objects
	 *
	 * @param $delete
	 * @param $post
	 * @param $force_delete
	 *
	 * @return mixed
	 */
	function pre_delete_attachment_filter( $delete, $post, $force_delete ) {

		$objects = $this->safe_media_attachment->get_attached_objects( $post->ID, true );

		if( !empty( $objects['posts'] ) || !empty( $objects['terms'] ) ) {
			if( wp_doing_ajax() ) {
				// In ajax call, unable to send custom message right now.
				wp_die( 0 );
			}

			$message = __( 'Please remove the image from below objects before deleting it.', SAFE_MEDIA_TEXT_DOMAIN );
			$message .= $objects['posts'] ? '<br/>'.__( 'Articles: ', SAFE_MEDIA_TEXT_DOMAIN ).$objects['posts'] : '';
			$message .= $objects['terms'] ? '<br/>'.__( 'Terms: ', SAFE_MEDIA_TEXT_DOMAIN ).$objects['terms'] : '';
			wp_die( $message, 'Action Needed', array( 'response' => 400, 'back_link' => true ) );
		}

		return $delete;

	}

}
