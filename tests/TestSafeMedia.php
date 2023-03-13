<?php
/**
 * Class TestSafeMedia
 *
 * @package Safe_Media
 */

class TestSafeMedia extends WP_UnitTestCase {

	protected $attached_image_id = 26; //Image linked with post or term

	/**
	 * Test if image can be deleted or not
	 */
	public function test_safe_media_delete() {

		$result = wp_delete_attachment($this->attached_image_id);
		$this->assertNotEquals( 1, $result, 'Image could not be deleted!' );

	}
}
