<?php
/**
 * Plugin Name:     WP Learn Form Submissions API
 * Description:     Manages REST API endpoints for a custom table
 * Author:          Your Name
 * Author URI:      https://yoururl.com
 * Text Domain:     wp-learn-form-submissions-api
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         WP_Learn_Form_Submissions_API
 */

/**
 * Set up the required form submissions table.
 * 
 * This code runs when the plugin is activated.
 */
register_activation_hook( __FILE__, 'wp_learn_setup_table' );

function wp_learn_setup_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'form_submissions';

	$sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  name varchar (100) NOT NULL,
	  email varchar (100) NOT NULL,
	  PRIMARY KEY  (id)
	)";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

/**
 * Register the REST API routes
 */
add_action( 'rest_api_init', 'wp_learn_register_routes' );
// GET all submissions
function wp_learn_register_routes() {
  register_rest_route(
    'wp-learn-form-submissions-api/v1',
    '/form-submissions/',
    array(
      'methods'  => 'GET',
      'callback' => 'wp_learn_get_form_submissions',
      'permission_callback' => '__return_true'
    )
  );
  // POST a new submission
  register_rest_route(
    'wp-learn-form-submissions-api/v1',
    '/form-submission/',
    array(
      'methods'  => 'POST',
      'callback' => 'wp_learn_create_form_submission',
      'permission_callback' => '__return_true'
    )
  );
  // GET a single submission with a path variable
  register_rest_route(
    'wp-learn-form-submissions-api/v1',
    '/form-submission/(?P<id>\d+)',
    array(
      'methods'  => 'GET',
      'callback' => 'wp_learn_rest_get_form_submission',
      'permission_callback' => '__return_true'
    )
  );  
}

/**
 * GET callback for the wp-learn-form-submissions-api/v1/form-submissions route
 *
 * @return array|object|stdClass[]|null
 */
function wp_learn_get_form_submissions() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'form_submissions';

  $results = $wpdb->get_results( "SELECT * FROM $table_name" );

  return $results;
}

/**
 * POST callback for the wp-learn-form-submissions-api/v1/form-submission route
 *
 * @param $request
 *
 * @return void
 */
function wp_learn_create_form_submission( $request ){
  global $wpdb;
  $table_name = $wpdb->prefix . 'form_submissions';

  $rows = $wpdb->insert(
    $table_name,
    array(
      'name' => $request['name'],
      'email' => $request['email'],
    )
  );

  return $rows;
}

/**
 * GET callback for the wp-learn-form-submissions-api/v1/form-submission route
 *
 * @param $request
 *
 * @return array|object|stdClass[]|null
 */
function wp_learn_rest_get_form_submission( $request ) {
  $id = $request['id'];
  global $wpdb;
  $table_name = $wpdb->prefix . 'form_submissions';

  $results = $wpdb->get_results( "SELECT * FROM $table_name WHERE id = $id" );

  return $results[0];
}