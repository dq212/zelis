<?php


/*
*
* ===================================================
*	Theme Setup
* ===================================================
*
*/

function db_get_default_image( $post_type, $post_id ){
	$image = false;
	
	if( $post_type == 'resource' ){
		$resource_type = get_field('resource_type' , $post_id );
		if( have_rows( 'resource_defaults', 'option' ) ){
			while( have_rows( 'resource_defaults', 'option' ) ){
				the_row();
				if( get_sub_field( 'resource_type' ) == $resource_type ){
					$image = wp_get_attachment_image( get_sub_field( 'default_image' ), 'preview', 'false', array( 'class' => 'img-responsive' ) );
				}			
			}			
		}
	}
	
	if( $post_type == 'news' ){
		$audience_terms = wp_get_post_terms( $post_id, 'news_type' );		
		
		if( count( $audience_terms ) > 0 ){			
			if( have_rows( 'news_defaults', 'option' ) ){
				while( have_rows( 'news_defaults', 'option' ) ){
					the_row();
					if( get_sub_field( 'news_type' ) == $audience_terms[0]->term_id ){
						$image = wp_get_attachment_image( get_sub_field( 'default_image' ), 'preview', 'false', array( 'class' => 'img-responsive' ) );
					}			
				}
			}
			
		}
	}
	
	return $image;
}


//utility function for neatly displaying array data. Can be deleted after development.
function db_pre_array($array){
	echo '<pre>';
	
	print_r($array);
	
	echo '</pre>';
}

//add login styling
function db_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_template_directory_uri() . '/assets/stylesheets/style-login.css' );
}
add_action( 'login_enqueue_scripts', 'db_login_stylesheet' );

function db_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'db_login_logo_url' );


function my_acf_google_map_api( $api ){
	
	$api['key'] = 'AIzaSyAsMnvQmSf3RRN4RkkoIAQzGWA5PcfDGJU';
	
	return $api;
	
}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');


if ( ! function_exists( 'zelis_setup' ) ) :

	function zelis_setup() {


		register_nav_menus( array(
			'primary' => __( 'Primary Menu', 'zelis' ),	
			'utility' => __( 'Utility Menu', 'zelis' ),	
			'footer' => __( 'Footer Menu', 'zelis' ),
			'legal' => __( 'Legal Menu', 'zelis' ),
            'level_3_nav' => __( 'level 3 nav', 'zelis' ),
            'payment_integrity_nav' => __( 'Payment Integrity Nav', 'zelis' ),
            'provider_solutions_nav' => __( 'Provider Solutions Nav', 'zelis' ),
            'zna_consultants_nav' => __( 'Consultants Nav', 'zelis' ),
            'zna_payers_nav' => __( 'Payers Nav', 'zelis' ),

		) );


		add_theme_support( 'post-thumbnails' );
		
		add_image_size( 'bio', 360, 360, true );
		add_image_size( 'preview', 405, 300, true );
		add_image_size( 'preview-wide', 705, 300, true );

	}
endif; // zelis_setup
add_action( 'after_setup_theme', 'zelis_setup' );

//return the post id by the slug
function get_ID_by_slug($page_slug , $post_type = 'page') {
	$page = get_page_by_path($page_slug , OBJECT , $post_type );
	
	if ($page) {
		return $page->ID;
	} else {
		return null;
	}
};


//remove menu items.
function remove_menus(){
  
  //remove_menu_page( 'index.php' );                 //Dashboard
  //remove_menu_page( 'jetpack' );                   //Jetpack* 
  remove_menu_page( 'edit.php' );                     //Posts
  //remove_menu_page( 'upload.php' );                //Media
  //remove_menu_page( 'edit.php?post_type=page' );   //Pages
  remove_menu_page( 'edit-comments.php' );            //Comments
  //remove_menu_page( 'themes.php' );                //Appearance
  //remove_menu_page( 'plugins.php' );               //Plugins
  //remove_menu_page( 'users.php' );                 //Users
  //remove_menu_page( 'tools.php' );                 //Tools
  //remove_menu_page( 'options-general.php' );       //Settings
  
}
add_action( 'admin_menu', 'remove_menus' );

function return_section_id( $heading ){
	return preg_replace( "/[\s']/", "-", strtolower( $heading ) );
}


//hide content on certain templates
function db_hide_editor() {
  
  // Get the Post ID.
  $post_id = get_queried_object_id();
  if( !isset( $post_id ) ) return;
  
  // Hide the editor on a page with a specific page template
  // Get the name of the Page Template file.
  $template_file = get_post_meta($post_id, '_wp_page_template', true);
  
  $templates = array( 'template-login.php', 'template-audience.php', 'template-partners.php', 'template-faqs.php', 'template-contact.php' );
  
  if( in_array ( $template_file , $templates ) ){ // the filename of the page template
    remove_post_type_support('page', 'editor');
  }
  
}
add_action( 'init', 'db_hide_editor' );

function remove_page_fields() {
	remove_meta_box( 'postexcerpt' , 'news' , 'normal' ); //removes excerpt
	remove_meta_box( 'postexcerpt' , 'resource' , 'normal' ); //removes excerpt
}
add_action( 'admin_menu' , 'remove_page_fields' );



/*
*
* ===================================================
*	Create custom post types
* ===================================================
*/

function create_post_type() {
	
	register_post_type( 'people',
		array(
			'labels' => array(
				'name' => __( 'People' ),
				'singular_name' => __( 'Person' ),
				'add_new_item' => __( 'Add new person' ),
				'edit_item' => __( 'Edit person' )
			),
		'public' => true,
		'menu_position' => 4,
		'supports' => array('title', 'editor', 'thumbnail'),
		'exclude_from_search' => true,
		'publicly_queryable' => false
		)
	);

	register_post_type( 'news',
		array(
			'labels' => array(
				'name' => __( 'News' ),
				'singular_name' => __( 'News' ),
				'add_new_item' => __( 'Add new news' ),
				'edit_item' => __( 'Edit news' )
			),
		'public' => true,
		'menu_position' => 5,
		'supports' => array('title', 'editor', 'thumbnail' , 'excerpt'),
		'exclude_from_search' => false,
		'publicly_queryable' => true
		)
	);

	register_post_type( 'resource',
		array(
			'labels' => array(
				'name' => __( 'Resources' ),
				'singular_name' => __( 'Resource' ),
				'add_new_item' => __( 'Add new Resource' ),
				'edit_item' => __( 'Edit Resource' )
			),
		'public' => true,
		'menu_position' => 5,
		'supports' => array('title', 'editor', 'thumbnail' , 'excerpt'),
		'exclude_from_search' => false,
		'publicly_queryable' => true
		)
	);


	$labels = array(
		'name'                       => _x( 'Topics', 'taxonomy general name' ),
		'singular_name'              => _x( 'Topic', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Topics' ),
		'all_items'                  => __( 'All Topics' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Topic' ),
		'update_item'                => __( 'Update Topic' ),
		'add_new_item'               => __( 'Add New Topic' ),
		'new_item_name'              => __( 'New Topic Name' ),
		'menu_name'                  => __( 'Topics' ),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'topic' ),
	);
	register_taxonomy( 'topic', 'resource', $args );
	
	$labels = array(
		'name'                       => _x( 'Audiences', 'taxonomy general name' ),
		'singular_name'              => _x( 'Audience', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Audiences' ),
		'all_items'                  => __( 'All Audiences' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Audience' ),
		'update_item'                => __( 'Update Audience' ),
		'add_new_item'               => __( 'Add New Audience' ),
		'new_item_name'              => __( 'New Audience Type' ),
		'menu_name'                  => __( 'Audience' ),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'audience' ),
	);
	register_taxonomy( 'audience', 'resource', $args );

	$labels = array(
		'name'                       => _x( 'News Type', 'taxonomy general name' ),
		'singular_name'              => _x( 'News Types', 'taxonomy singular name' ),
		'search_items'               => __( 'Search News Types' ),
		'all_items'                  => __( 'All News Types' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit News Type' ),
		'update_item'                => __( 'Update News Type' ),
		'add_new_item'               => __( 'Add New News Type' ),
		'new_item_name'              => __( 'New News Type Name' ),
		'menu_name'                  => __( 'News Types' ),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'news_type' ),
	);
	register_taxonomy( 'news_type', 'news', $args );



}
add_action( 'init', 'create_post_type' );






/*
*
* ===================================================
*	Enqueue Scripts and Styles
* ===================================================
*
*/

function zelis_scripts() {
	wp_enqueue_style( 'aos-css', get_stylesheet_directory_uri() . '/assets/stylesheets/aos.css' );
	wp_enqueue_style( 'zelis-style', get_stylesheet_uri(), array('aos-css') );
	wp_enqueue_style( 'claim-styles', get_stylesheet_directory_uri() . '/assets/stylesheets/claim-styles.css' );
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'zelis-bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js' , true);
	wp_enqueue_script( 'aos-js', get_stylesheet_directory_uri() . '/assets/javascripts/aos.js' , true);
	wp_enqueue_script( 'validate', get_stylesheet_directory_uri() . '/assets/javascripts/validate/jquery.validate.min.js', array( 'jquery' ), true  );
	wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/assets/javascripts/main.js', array( 'zelis-bootstrap-js', 'validate', 'jquery', 'aos-js' ), true );
}
add_action( 'wp_enqueue_scripts', 'zelis_scripts' );





/*
*
* ===================================================
*	ACF
* ===================================================
*
*/

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Header & Footer Settings',
		'menu_title'	=> 'Header & Footer',
		'menu_slug' 	=> 'header-footer-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	acf_add_options_page(array(
		'page_title' 	=> 'Locations',
		'menu_title'	=> 'Locations',
		'menu_slug' 	=> 'locations',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	acf_add_options_page(array(
		'page_title' 	=> 'Whitepaper Signup',
		'menu_title'	=> 'Whitepaper',
		'menu_slug' 	=> 'whitepaper-signup',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	acf_add_options_page(array(
		'page_title' 	=> 'Newsletter Sign-up',
		'menu_title'	=> 'Newsletter Sign-up',
		'menu_slug' 	=> 'newsletter-signup',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	acf_add_options_page(array(
		'page_title' 	=> 'Default Images',
		'menu_title'	=> 'Default Images',
		'menu_slug' 	=> 'default-images',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

function db_is_parent_or_child(){}

//create a side nav of all sub pages
function db_create_sub_nav($id, $depth = 2, $first_children = array()){
	
	$ancestors = get_ancestors( $id, 'page' );
	
	$total = count($ancestors);
	
	$top = ( $total > 0 ) ? end($ancestors) : $id;
	
	//check if the id passed is shallower than the depth
	if($depth - $total > 2)
		return false;
	if($depth - $total ==  2){
		$parent_id = $id;
	} else{
		$rev = array_reverse($ancestors);
		$parent_id = $rev[$depth - 2];
	}
	
	$args = array(
		'post_parent' => $parent_id,
		'post_type' => 'page'
	);
	
	//get children of parent
	$kids = get_children($args);
	
	$menu_args = array(
		'child_of' => $parent_id,
		'echo' => false
	);
	
	$menu = wp_page_menu( $menu_args );
	
	$menu = str_replace( 'class="menu"', 'class="container"', $menu );
	
	$menu = str_replace( '<ul>', '<ul id="menu-secondary-about" class="nav navbar-nav navbar-right">', $menu );
	
	//if it has children send menu
	if( count($kids) > 0){
		return $parent_id;
	} else {
		return false;
	}
	
}


/**
 * Create HTML list of nav menu items.
 *
 * @since 3.0.0
 * @uses Walker
 */
class zelis_add_sub_buttons extends Walker_Nav_Menu {
	/**
	 * What the class handles.
	 *
	 * @see Walker::$tree_type
	 * @since 3.0.0
	 * @var string
	 */
	public $tree_type = array( 'post_type', 'taxonomy', 'custom' );

	/**
	 * Database fields to use.
	 *
	 * @see Walker::$db_fields
	 * @since 3.0.0
	 * @todo Decouple this.
	 * @var array
	 */
	public $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu\">\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 * @since 4.4.0 'nav_menu_item_args' filter was added.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filter the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param array  $args  An array of arguments.
		 * @param object $item  Menu item data object.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		/**
		 * Filter the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		/**
		 * Filter the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );
		
		
		//get children of parent
		$children = get_pages( array( 'child_of' => $item->ID ) );
		
		$sub_link = ( count( $children ) > 0 ) ? '<span class="glyphicon glyphicon-chevron-right"></span>' : '';
		
		/**
		 * Filter a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string $title The menu item's title.
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $sub_link;
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Page data object. Not used.
	 * @param int    $depth  Depth of page. Not Used.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

} // Walker_Nav_Menu



/*
*
* ===================================================
*	SSL Get File Contents with cURL
* ===================================================
*
*/

    function file_get_contents_curl($url){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);     
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
    //source: https://stackoverflow.com/questions/8540800/how-to-use-curl-instead-of-file-get-contents   


/*
*
* ===================================================
*	Basic Taxonomy
* ===================================================
*
*/
function wptp_add_categories_to_attachments() {
    register_taxonomy_for_object_type( 'category', 'attachment' );
}
add_action( 'init' , 'wptp_add_categories_to_attachments' );

//https://code.tutsplus.com/articles/applying-categories-tags-and-custom-taxonomies-to-media-attachments--wp-32319


/*
*
* ===================================================
*	Custom Field Admin Styles
* ===================================================
*
*/
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function load_admin_style() {
    wp_enqueue_style( 'admin_css', get_template_directory_uri() . '/assets/stylesheets/admin-custom.css', false, '1.0.0' );
}