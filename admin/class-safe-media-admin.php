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


	function media_columns_filter( $columns ) {
		$columns['colAttachedObjects'] = __( 'Attached Objects' );
		return $columns;
	}


	function media_custom_column_action( $column_name, $attachment_id ) {
		if( $column_name !== 'colAttachedObjects' ) {
			return;
		}
		$post_string = $this->safe_media_attachment->get_linked_posts( $attachment_id );

		$term_string = $this->safe_media_attachment->get_linked_terms( $attachment_id );

		echo $post_string ? 'Articles <br/>'.$post_string.'<br/>' : '';
		echo $term_string ? 'Terms <br/>'.$term_string : '';
	}


	function media_attachment_fields_filter( $form_fields, $attachment ) {

		$form_fields['linked_articles'] = array(
			'label' => __( 'Linked Articles', SAFE_MEDIA_TEXT_DOMAIN ),
			'input' => 'html',
			'html'  => $this->safe_media_attachment->get_linked_posts( $attachment->ID )
		);

		$form_fields['linked_terms'] = array(
			'label' => __( 'Linked Terms', SAFE_MEDIA_TEXT_DOMAIN ),
			'input' => 'html',
			'html'  => $this->safe_media_attachment->get_linked_terms( $attachment->ID )
		);

		return $form_fields;
	}

	/**
	 * @param $delete
	 * @param $post
	 * @param $force_delete
	 *
	 * @return mixed
	 */
	function pre_delete_attachment_filter( $delete, $post, $force_delete ) {

		$posts = $this->safe_media_attachment->get_linked_posts( $post->ID );
		$terms = $this->safe_media_attachment->get_linked_terms( $post->ID );
		if( $posts || $terms ) {
			if( wp_doing_ajax() ) {
				// In ajax call, unable to send custom message right now.
				wp_die( 0 );
			}
			$message = __( 'Please remove the image from below objects before deleting it.', SAFE_MEDIA_TEXT_DOMAIN );
			$message .= $posts ? '<br/>'.__( 'Articles: ', SAFE_MEDIA_TEXT_DOMAIN ).$posts.'<br/>' : '';
			$message .= $terms ? __( 'Terms: ', SAFE_MEDIA_TEXT_DOMAIN ).$terms : '';
			wp_die( $message );
		}

		return $delete;

	}

}
