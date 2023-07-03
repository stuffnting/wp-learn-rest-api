<?php
/**
 * Plugin Name:     WP Learn REST API
 * Description:     Learning about the WP REST API
 * Version:         0.0.1
 */

/**
 * Create an admin page to show the form submissions
 */
add_action( 'admin_menu', 'wp_learn_rest_submenu', 11 );
function wp_learn_rest_submenu() {
  add_submenu_page(
    'tools.php',
    esc_html__( 'WP Learn REST Admin Page', 'wp_learn' ),
    esc_html__( 'WP Learn REST Admin Page', 'wp_learn' ),
    'manage_options',
    'wp_learn_admin',
    'wp_learn_rest_render_admin_page'
  );
}

/**
 * Render the form submissions admin page
 */
function wp_learn_rest_render_admin_page() {
  ?>
    <div class="wrap" id="wp_learn_admin">
        <h1>Admin</h1>
        <button id="wp-learn-rest-api-button">Load Posts via REST</button>
        <button id="wp-learn-clear-posts">Clear Posts</button>
        <h2>Posts</h2>
        <textarea id="wp-learn-posts" cols="125" rows="15"></textarea>
        <div style="width:50%;">
          <h2>Add Post</h2>
            <form>
              <div>
                  <label for="wp-learn-post-title">Post Title</label>
                  <input type="text" id="wp-learn-post-title" placeholder="Title">
              </div>
              <div>
                  <label for="wp-learn-post-content">Post Content</label>
                  <textarea id="wp-learn-post-content" cols="100" rows="10"></textarea>
              </div>
              <div>
                  <label for="wp-learn-post-url">Post Url</label>
                  <input type="text" id="wp-learn-post-url" placeholder="Url">
                </div>
              <div>
                  <input type="button" id="wp-learn-submit-post" value="Add">
              </div>
          </form>
      </div>
      <div style="width:50%;">
        <h2>Update Post</h2>
        <form>
            <div>
                <label for="wp-learn-update-post-id">Post ID</label>
                <input type="text" id="wp-learn-update-post-id" placeholder="ID">
            </div>
            <div>
              <div>
                <label for="wp-learn-update-post-title">Post Title</label>
                <input type="text" id="wp-learn-update-post-title" placeholder="Title">
              </div>
              <div>
                <label for="wp-learn-update-post-content">Post Content</label>
                <textarea id="wp-learn-update-post-content" cols="100" rows="10"></textarea>
              </div>
              <div>
                <input type="button" id="wp-learn-update-post" value="Update">
              </div>
        </form>
      </div>
      <div style="width:50%;">
        <h2>Delete Post</h2>
        <form>
            <div>
                <label for="wp-learn-post-id">Post ID</label>
                <input type="text" id="wp-learn-post-id" placeholder="ID">
            </div>
            <div>
                <input type="button" id="wp-learn-delete-post" value="Delete">
            </div>
        </form>
      </div>
    </div>
  <?php
}

/**
 * Enqueue the main plugin JavaScript file.
 * 
 * Note that the dependencies needs to be added 
 * to allow backbone.js to be used.
 */
add_action( 'admin_enqueue_scripts', 'wp_learn_rest_enqueue_script' );
function wp_learn_rest_enqueue_script() {
  wp_register_script(
    'wp-learn-rest-api',
    plugin_dir_url( __FILE__ ) . 'wp-learn-rest-api.js',
    array( 'wp-api' ),
    time(),
    true
  );
  wp_enqueue_script( 'wp-learn-rest-api' );
}

/**
 * Register a url and isbn custom fields
 */
add_action( 'init', 'wp_learn_register_meta' );
function wp_learn_register_meta(){
  register_meta(
    'post',
    'url',
    array(
      'single'         => true,
      'type'           => 'string',
      'default'        => '',
      'show_in_rest'   => true,
      'object_subtype' => 'book',
    )
  );
  register_meta(
    'post',
    'isbn',
    array(
      'single'         => true,
      'type'           => 'string',
      'default'        => '',
      'show_in_rest'   => true,
      'object_subtype' => 'book',
    )
  );
}

/**
 * Register a book custom post type
 */
add_action( 'init', 'wp_learn_register_book' );
function wp_learn_register_book() {
  register_post_type(
    'book',
    array(
      'labels'       	=> array(
        'name'          => __( 'Books' ),
        'singular_name' => __( 'Book' )
      ),
      'public'       => true,
      'has_archive'  => true,
      'show_in_rest' => true,
      'supports'     => array(
        'title',
        'editor',
        'thumbnail',
        'excerpt',
        'custom-fields',
        'revisions',
      ),
      'taxonomies'   => array(
        'category',
        'post_tag',
      ),
    )
  );
}

/**
 * Add the isbn custom field to the top-level response
 */
add_action( 'rest_api_init', 'wp_learn_rest_add_fields' );
function wp_learn_rest_add_fields() { 
  register_rest_field(
    'book',
    'isbn',
    array(
      'get_callback'    => 'wp_learn_rest_get_isbn',
      'update_callback' => 'wp_learn_rest_update_isbn',
      'schema'          => array(
          'description' => __( 'The ISBN of the book' ),
          'type'        => 'string',
      ),
    )
  );
}

function wp_learn_rest_get_isbn( $book ){
  return  get_post_meta( $book['id'], 'isbn', true );
}

function wp_learn_rest_update_isbn( $value, $book ){
  return update_post_meta( $book->ID, 'isbn', $value );
}