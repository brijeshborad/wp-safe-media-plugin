<?php

class TestRestApi extends WP_UnitTestCase {

	/**
	 * Test REST API
	 *
	 * @var WP_REST_Server
	 */
	protected $server;

	protected $route_namespace = 'assignment/v1';

	public function setUp()
	: void {
		parent::setUp();
		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$this->server = $wp_rest_server = new \WP_REST_Server;
		do_action( 'rest_api_init' );

	}


	/**
	 * Check if namespace is registerd or not
	 */
	public function test_namespace_registered() {
		$routes = $this->server->get_namespaces();
		$this->assertContains( $this->route_namespace, $routes, 'Namespace is not registered!' );
	}


	/**
	 * Test endpoints is exists will all needed parameters or not
	 */
	public function test_namespace_endpoint_exist() {
		$the_route = $this->route_namespace;

		$routes = $this->server->get_routes( $this->route_namespace );
		foreach( $routes as $route => $route_config ) {
			if( 1 === strpos( $route, $the_route ) ) {

				$this->assertTrue( is_array( $route_config ) );
				foreach( $route_config as $i => $endpoint ) {
					$this->assertArrayHasKey( 'callback', $endpoint );
					$this->assertArrayHasKey( 0, $endpoint['callback'], get_class( $this ) );
					$this->assertArrayHasKey( 1, $endpoint['callback'], get_class( $this ) );
					$this->assertTrue( is_callable( array( $endpoint['callback'][0], $endpoint['callback'][1] ) ) );
				}
			}
		}
	}


}
