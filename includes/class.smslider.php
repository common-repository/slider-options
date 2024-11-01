<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if(! class_exists('SMSlider')){
	class SMSlider{
		
	    private static $_instance;

	    protected $plugin_slug, $version;

	    private $table = 'sm_sliders';

	    CONST post_type = 'smsd-type';

	    /**
	     * Table name
	     * @since 1.1
	     * @var string
	     */
	    private static $_table = 'sm_sliders';
	    static $caps = array( 'manage_slider' =>'Manager Slider','smsd_addnew'=> 'Add New Slider', 'smsd_edit'=>'Edit Slider', 'smsd_config'=>'Config Slider', 'smsd_delete'=>'Delete Slider' );
	    /**
	     * Constructor. Called when plugin is initialised
	     */
	    public function __construct() {
	    	$this->version = SMSD_V;
	    	$this->plugin_slug = 'sm-slider';
	    	add_action( 'init', 'smsd_init_required', 0 );
	    	add_action( 'init', array( &$this, 'register_post_type' ) );
			add_action( 'init', array( &$this, 'register_taxonomy' ) );
	    	add_action( 'init', array( &$this, 'install' ), 1 );
	    }

	    public function install(){
	    	if( is_admin() ){
	    		add_action( 'smsd/required', 'smsd_init_required', 1, 2);
	    		$this->define_manager();
	    	}
	    	$this->define_public();
	    }

	    public function register_post_type(){

	    	register_post_type(self::post_type,array(
	    		'labels'=>array(
	    			'name'=>'SM Slider'
	    		),
	    		'public' => false,
	    		'exclude_from_search' => true,
	    		'publicly_queryable' => false,
	    		'show_ui' => false,
	    		'show_in_nav_menus' => false,
	    		'rewrite'=> false
	    	));
	    }

	    public function register_taxonomy(){
	    	register_taxonomy( 'smj-slider', array( self::post_type ), array(
	    		'public' => false,
	    		'hierarchical' => true,
	    		'query_var' => false,
	    		'rewrite' => false
	    	));
	    }

	    public function filter_content( $content ){
	    	$content = get_the_title();
	    	return $content;
	    }

	    public static function instance(){
	    	if(is_null( self::$_instance )){
	    		self::$_instance = new self();
	    	}
	    	return self::$_instance;
	    }

	    private function define_manager(){
	    	do_action( 'smsd/required', 'includes', 'admin');
	    	$admin_slider = new SMSD_Admin( $this->get_plugin_slug(), $this->get_version() );
	    }

	    private function define_public(){
	    	$public_slider = new SMSD_Public( $this->get_plugin_slug(), $this->get_version() );
	    }

	    public function get_plugin_slug(){
	    	return $this->plugin_slug;
	    }

	    public function get_version(){
	    	return $this->version;
	    }

	    /**
	     * Retrieve table name
	     * @access protected
	     * 
	     * @return string
	     */
	    public static function get_table(){
	        global $wpdb;
	        return $wpdb->prefix . self::$_table;
	    }

	   	public static function get_thumbnail( $size='thumbnail' ){
	   		global $post;

	   		if( is_string( $size ) && preg_match( '/(^[0-9]+)x([0-9]+)$/i', trim( $size ), $matches ) ){
	   			$size = array( $matches[1], $matches[2] );
	   		}
	   		
	   		if($post->post_parent && $img = wp_get_attachment_image_src( $post->post_parent, $size ) ){
	   			return $img[0];
	   		}

   			if( is_string( $size ) ){
   				$size = self::get_image_size( $size );
   			}
   			$post_title = empty( $post->post_title ) ? __('Image Slider','smslider') : $post->post_title;

	   		return apply_filters( 'smslider/default-img', self::get_no_image( $size, esc_attr( $post_title ) ), $size );
	   	}

	   	public static function get_post_meta( $post_id, $key ){
	   		if( $key === 'alt' ){
	   			$key = '_wp_attachment_image_alt';
	   		}else{
	   			$key = "_smsd-meta_{$key}";
	   		}
	   		return get_post_meta( $post_id, $key, true );
	   	}

	    public static function get_no_image(  $size, $alt ){
	    	$img = 'http://dummyimage.com/'. $size[0] .'x'. $size[1] .'/333/ddb8b8&text='. $alt;
	    	return $img;
	    }

	    public static function get_image_size( $size ){
        	global $_wp_additional_image_sizes;
        	$sizes = array();

	        $get_intermediate_image_sizes = get_intermediate_image_sizes();

	        foreach( $get_intermediate_image_sizes as $_size ) {
                if( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
                    $sizes[$_size][] = get_option( $_size . '_size_w' );
                    $sizes[$_size][] = get_option( $_size . '_size_h' );
                }elseif( isset( $_wp_additional_image_sizes[$_size] ) ) {
                    $sizes[$_size] = array( 
                        $_wp_additional_image_sizes[$_size]['width'],
                        $_wp_additional_image_sizes[$_size]['height'],
                    );
                }
	        }

	        if($size && isset( $sizes[$size] ) ) {
                return $sizes[$size];
	        }

	        $sizes = apply_filters( 'smslider/default-img-size', array( 800, 450 ), $size );

	        return $sizes;
	    }

	    /**
	     * Get Options by name
	     * @param  string  $name    name of option
	     * @param  boolean $default default value
	     * @return mixed
	     */
	    public static function get_option( $name, $raw=false, $default=false ){
	    	$name = "-smsd_{$name}";
	    	if( $raw ){
	    		return get_option( $name, $default );
	    	}
	    	return json_decode( get_option( $name, $default ) );
	    }

	    /**
	     * Get List slider show by alias name
	     * @param  string $alias alias slug
	     * @return object       List slider item
	     */
	    public static function get_edit_slide( $alias ){
	    	$args = array(
	    		'order' => 'ASC',
	    		'orderby' => 'menu_order',
	    		'tax_query' => array(
	    			array(
						'taxonomy' => 'smj-slider',
						'field'    => 'slug',
						'terms'    => array( $alias )
					)
	    		)
	    	);
	    	return self::find( $args );
	    }

	    public static function get_asset( $name ){
	    	$types = SMSLider::get_option( 'types' );
	    	if( !isset( $types->$name ) )
	    		return false;
	    	if(  $types->$name === 'theme' ){
	    		$name = '/smslider/'. $name;
	    		$file = is_dir( get_template_directory() .$name ) ? get_stylesheet_directory_uri() .$name : false;
	    	}else{
	    		$name = 'libs/'. $name;
	    		$file = is_dir( SMSD_ASSETS_DIR . $name ) ? SMSD_ASSETS_URL . $name : false;
	    	}
	    	
	    	return $file;
	    }


	    public static function find( $args ){
	    	$dfault = array(
	    		'post_type' => self::post_type,
	    		'posts_per_page' => -1
	    	);
	    	$args = wp_parse_args( $args, $dfault );
	    	$q = new WP_Query();
	    	$q->query( $args );
	    	return $q;
	    }

	    /**
	     * @since 1.1 update support theme
	     * @param  string $name name path
	     * @param  string $src  theme or plug
	     * @return object 
	     */
	    public static function get_libs( $name='', $src='plug' ){
	    	$min = SMSD_LIVE ? '.min' : '';
	    	if( $src === 'theme' ){
	    		$file = get_template_directory() ."/smslider/{$name}/data{$min}.json";
	    	}else{
	    		$file = SMSD_PLUGIN_DIR ."/assets/libs/{$name}/data{$min}.json";
	    	}
	    	if( ! file_exists( $file ) )
	    		return false;
	    	return json_decode( file_get_contents( $file ) );
	    }

	    static function get_slide( $slide = null, $filter = false ) {
		    if( empty( $slide ) && isset( $_GET['slide'] ) ){
		        $slide = $_GET['slide'];
		    }

		    if ( is_object( $slide ) ) {
		        if ( empty( $slide->filter ) ) {
		            $_slide = self::sanitize_slide( $slide, true );
		            $_slide = new SMSD_Detail( $_slide );
		        } elseif ( true === $slide->filter ) {
		            $_slide = new SMSD_Detail( $slide );
		        } else {
		            $_slide = SMSD_Detail::get_instance( $slide->ID );
		        }
		    }else {
		        $_slide = SMSD_Detail::get_instance( $slide );
		    }

		    if ( ! $_slide )
		        return null;

		    $_slide = $_slide->filter( $filter );

		    return $_slide;
		}

	    /**
	     * Sanitize every slide field
	     * @param  object  $slide The slide object.
	     * @param  boolean $raw   Optional. How to sanitize post fields. Accept: true/false.
	     * @return object         The slide object.
	     */
	    static function sanitize_slide( $slide, $context = false ){
	        // Check if post already filtered for this context.
	        if ( isset($slide->filter) && $context == $slide->filter ){
	            echo 'contexts ';
	            return $slide;
	        }
	        if ( !isset($slide->ID) )
	            $slide->ID = 0;
	        foreach ( array_keys(get_object_vars($slide)) as $field )
	            $slide->$field = self::sanitize_slide_field($field, $slide->$field, $context);
	        $slide->filter = $context;

	        return $slide;
	    }

	    /**
	     * Sanitize slide field
	     * @param  mixed $field The slide field name.
	     * @param  mixed $value The slide field value.
	     * @return mixed        Sanitize value.
	     */
	    static function sanitize_slide_field( $field, $value, $context ){
	        $edit_fields = array('title','alias');

	        if( $field === 'ID'){
	            $value = (int) $value;
	        }elseif( $field === 'params' ){
	            if( $context == 'raw' ){
	                $value = json_decode( $value );
	            }
	        }elseif( in_array( $field, $edit_fields ) ){
	            $value = esc_attr( $value );
	        }

	        return $value;
	    }

	    public static function get_slide_id( $alias ){

	        $setting = self::get_option( 'type' );

	        if( isset( $setting->$alias ) ){
	            return $setting->$alias;
	        }
	        
	        return 0;
	    }

	    /**
	     * Include template file
	     * @access public
	     * 
	     * @param  string $temp template name
	     * @param  array  $args array of arguments
	     * @return void
	     */
	    public static function view( $temp, array $args = array() ){
	        $args = apply_filters( 'smslider/views-arguments', $args, $temp );
	    
	        foreach ( $args AS $key => $val ) {
	            $$key = $val;
	        }

	        $file = SMSD_PLUGIN_DIR . '/views/'. $temp . '.php';

	        include( $file );
	    }

	}
	$smslider = SMSlider::instance();
}
 ?>