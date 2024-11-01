<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

class SMSD_Detail{
    /**
     * Slide id
     * 
     * @var int
     */
    public $ID;

    /**
     * The side's name
     * 
     * @var string
     */
    public $title='';

    /**
     * The slide's alias name
     * 
     * @var string
     */
    public $alias;

    /**
     * The slide's params
     * 
     * @var string
     */
    public $params;

    /**
     * The size of slide
     * @var array
     */
    public $size = array( 800, 450 );

    /**
     * The slide's local updated time
     * 
     * @var string
     */
    public $updated_at = '0000-00-00 00:00:00';

    /**
     * The slide's local created time
     * 
     * @var string
     */
    public $created_at = '0000-00-00 00:00:00';

    /**
     * Stores the slide object's sanitization level.
     *
     * @var string
     */
    public $filter;

    public function __construct( $_slide ){

        foreach( get_object_vars( $_slide ) as $key=>$value ){
            $this->$key = $value;

            if( $key === 'params' ){
                $size = isset( $this->params->size ) ? $this->params->size : 'SMSD_origin';
                $this->size = SMSLider::get_image_size( $size );
            }
        }
    }

    /**
     * Retrieve WP_Post instance
     * @access private
     *
     * @param int $slide_id
     * @return object|boolean         The slide object or false
     */
    public static function get_instance( $slide_id ){
        global $wpdb;

        if( ! is_numeric( $slide_id ) ){
            $slide_id = SMSlider::get_slide_id( $slide_id );
        }

        $slide_id = (int)$slide_id;

        if( !$slide_id )
            return false;

        $_slide = wp_cache_get( $slide_id, 'smsd' );
        
        if( ! $_slide ){
            $table = SMSlider::get_table();
            $_slide = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$table} WHERE ID = %d", $slide_id ) );

            if( !$_slide )
                return false;

            $_slide = SMSLider::sanitize_slide( $_slide, 'raw' );

            wp_cache_add( $_slide->ID, $_slide, 'smsd' );
        } elseif ( empty( $_slide->filter ) ) {
            $_slide = SMSLider::sanitize_slide( $_slide, 'raw' );
        }

        return new SMSD_Detail( $_slide );
    }


    /**
     * {@Missing Summary}
     *
     * @param string $filter Filter.
     * @return self|bool|object
     */
    public function filter( $filter ) {
        if ( $this->filter === $filter ){
            return $this;
        }

        if ( $filter == 'raw' )
            return self::get_instance( $this->ID );

        return sanitize_post( $this, $filter );
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

?>