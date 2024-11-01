<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
if( ! class_exists( 'SMSD_Admin' ) ){
	class SMSD_Admin{
		private $plugin_slug, $version;
		private static $_action = 'nonce';
	    /**
	     * The slide clone
	     * 
	     * @var string
	     */
	    public static $copy = '';
	    CONST TAXONOMY = 'smj-slider';
		static $slide_type = array( 'attachment'=> 'Gallery', 'post'=>'List Post', 'specific_post'=>'Specific Posts' );
		static $lists_slider = array('bxslider'=>'Bx Slider','flexslider'=>'Flex Slider', 'nivoslider'=>'Nivo Slider', 'responsiveslides'=>'Responsive Slides', 'bootstrap'=>'Bootstrap');
		static $list_word = 'manager,admin,image,options,carousel,responsive,captions,plugins,slider,slide phones,youtube,vimeo,jquery slider,jquery slide,jquery slider demo,jquery ui slider,jquery image slider,jquery sliders,wordpress slider,jquery content slider,jquery slider plugin,jquery slider example,slider jquery,basic jquery slider,simple jquery slider,jquery mobile slider,responsive slider jquery,slider options,3d slider,best slider,best slider plugin,carousel slider,coin slider,coinslider,content slider,content slideshow,css3 slider,custom video slider,fast slide,fast slider,featured-content-slider,flex slider,free plugin,free slider,free video slider,free video slideshow,fullscreen slider,fullwidth slider,horizontal slider,html5 slider,image rotator,image slider,image slideshow,images,javascript slider,javascript slideshow,jquery,jquery slideshow,links,media,mobile slider,nivo slider,nivoslider,page,page slider,photo slider,picture,picture slider,pictures,plugin,post,post slider,posts,posts slider,recent post slider,responsive image slider,responsive slider,responsive slideshow,rotate,rotator,seo,sidebar,slide,slide show,slider image,slider plugin,slider shortcode,slider widget,slides,slideshow,slideshow manager,slideshow plugin,swipe,swipe slider,touch slider,vertical slider,vertical slides,video slider,video slideshow,vimeo slideshow,vimeo slider,widget,widget slider,widget slideshow,widgets,wordpress picture slider,wordpress responsive slider,wordpress seo,wordpress slideshow,wp slider,youtube slider,youtube slideshow';
		public function __construct( $plugin_slug, $version ){
			$this->plugin_slug = $plugin_slug;
			$this->version = $version;
			
			if( isset( $_GET['action']) ){
	    		self::$_action = esc_attr( $_GET['action'] );
	    	}

	        if( isset( $_REQUEST['clone']) ){
	            self::$copy = esc_attr( $_REQUEST['clone'] );
	        }

	        add_action( 'admin_init', array( &$this, 'manage_sliders' ) );
			add_action( 'admin_menu',array( &$this, 'admin_menu' ) );
			add_action( 'admin_post_smsd-act_add-new', array( &$this,'add_new_slide' ) );
			add_action( 'admin_post_smsd-act_setting', array( &$this, 'update_slide' ) );
			add_action( 'admin_post_smsd-act_edit-attachment', array( &$this, 'edit_attachment' ) );
			add_filter( 'set-screen-option', array( &$this, 'set_screen_option' ), 10, 3);
			add_action( 'wp_ajax_smsd-act_delete', array( &$this, 'delete_slide' ) );
			add_action( 'wp_ajax_smsd-act_copy', array( &$this, 'clone_slide' ) );
			add_action( 'wp_ajax_smsd-act_alias', array( &$this, 'exist_slide' ) );
			add_action( 'admin_post_smsd-act_settings', array( &$this, 'update_setting' ) );
			add_action( 'wp_ajax_smsd-button', array( &$this, 'editor'));
			add_action( 'admin_notices', array( &$this, 'response_notices' ) );
			add_action( 'admin_init', array( &$this, 'render_smsd_button' ) );

		}

		/**
		 * Add top, sub menu page, and define script and style or admin
		 * @access public
		 * @since 1.1 changed capability
		 * @return void
		 */
		public function admin_menu(){
	    	$hook = add_menu_page(
	    		__('SM Slider', 'smslider'),
	    		__('SM Slider', 'smslider'),
	    		'manage_slider',
	    		$this->plugin_slug,
	    		array($this, 'manage_page'),
	    		SMSD_ASSETS_URL .'icons/icon.gif'
	    	);

	    	add_submenu_page(
	    		$this->plugin_slug,
	    		__('New Slider','smslider'),
	    		__('New Slider', 'smslider'),
	    		'smsd_addnew',
	    		$this->plugin_slug .'&action=add-new',
	    		array($this, 'manage_slider')
	    	);

	    	add_submenu_page(
	    		$this->plugin_slug,
	    		__('Settings page','smslider'),
	    		__('Settings', 'smslider'),
	    		'manage_options',
	    		$this->plugin_slug .'&action=config',
	    		array($this, 'manage_setting')
	    	);

	    	add_action( "load-$hook", array( &$this, 'add_screen_option' ) );
	    	add_action( "admin_enqueue_scripts", array( &$this, 'wp_enqueue_script' ) );
	    }

	    /**
	     * Output setting
	     * @access public
	     * 
	     * @return void
	     */
	    public function manage_page(){
	    	if( 'nonce' !== self::$_action ){
	    		if( isset( $_GET['slide'] ) ){
	    			$slide = SMSlider::get_slide( $_GET['slide'] );

		    		if( null !== $slide ){
		    			if( self::$_action === 'setting' && current_user_can('smsd_config') ){
		    				$this->render_slider( 'Setting', $slide->title );
			    		}else if( self::$_action === 'preview' ){
			    			?>
			    			<div class="smsd-iframe">
			    				<div class="smsd-if-inner">
			    					<?php echo smsd_get_slider( $_GET['slide'] ); ?>
			    				</div>
			    			</div>
			    			<?php
			    		}else{
			    			if( !current_user_can('smsd_edit') ){
		    					die(__('Cheatin&#8217; uh?', 'smslider'));
			    			}
			    			$this->render_slider( "Edit attachment", $slide->title);
				    	}
		    		}else{
		    			if( !current_user_can('smsd_addnew') )
		    				die(__('Cheatin&#8217; uh?', 'smslider'));
		    			$this->render_slider( 'Add New' );
		    		}
		    	}elseif( self::$_action === 'config' && current_user_can('manage_options') ){
		    		$this->render_slider( 'Settings' );
		    	}else{
		    		if( !current_user_can('smsd_addnew') ){
		    			die(__('Cheatin&#8217; uh?', 'smslider'));
		    		}
		    		$this->render_slider( 'Add New' );
		    	}
	    	}else{
	    		do_action( 'smsd/required', 'includes', 'list-table');
		    	$smsd_slide = new SMSD_List_Table;
		    	?>
		    	<div id="smslider-box" class="wrap">
		    		<h2>
		    			SM Slider
		    			<a class="add-new-h2" href="<?php menu_page_url( 'sm-slider&action=add-new' );?>">Add new</a>
		    		</h2>
		    		<?php
		    			$smsd_slide->prepare_items();
		    			$smsd_slide->display();
		    		 ?>
		    	</div>
		    <?php }
	    }

	    /**
	     * Output setting for add new slide
	     * @access public
	     * 
	     * @return void
	     */
	    public function manage_slider(){
	    	$this->render_slider( 'Add New' );
	    }

	    /**
	     * Output setting for config
	     * @access public
	     * 
	     * @return void
	     */
	    public function manage_setting(){
	    	$this->render_slider( 'Settings' );
	    }

	    /**
	     * Add a screen option to the current page
	     * @access public
	     *
	     * @return void
	     */
	    public function add_screen_option(){
	    	$args = array(
	    		'label' => __('Slides per page','smslider'),
	    		'default' => 10,
	    		'option' => __('slides_per_page','smslider')
	    	);
	    	add_screen_option( 'per_page', $args );
	    }

	    /**
	     * Enquue scripts and styles
	     * @access public
	     * 
	     * @param  string $hook  the $hook_suffix for the current admin page
	     * @return void
	     */
	    public function wp_enqueue_script( $hook ){
	    	$out_smsd = strpos( $hook, $this->plugin_slug ) === false;
	    	$action = $out_smsd ? 'nonce' : self::$_action;
	    	$min = SMSD_LIVE ? '.min' : '';
	    	
	    	wp_enqueue_script( 'smsd-public', SMSD_ASSETS_URL ."js/smsd-public{$min}.js", array("jquery"), $this->version, true );
	    	wp_localize_script( 'smsd-public', '_smsd', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'sm_nonce' => wp_create_nonce( self::render_nonce( $action ) ) , 'smsd_path'=>SMSD_PLUGIN_URL ) );
		    if( $out_smsd ) return;

	    	// Enqueue style
	    	$types = SMSlider::get_option('types');
	    	if( !empty( $types ) ){
	    		foreach( $types as $lib=>$src ){
		    		$assets = SMSLider::get_asset( $lib );
		    		$libs = SMSLider::get_option( $lib );
		    		if( !isset( $libs->all_page ) || $libs->all_page === 1 ){
		    			wp_enqueue_style( $lib, $assets ."/{$libs->css}.min.css", array(), $libs->v );
		    			wp_enqueue_script( $lib, $assets ."/{$libs->js}.min.js",array(),$libs->v,true);
		    		}
		    	}
	    	}
	    	wp_enqueue_style( 'smslider-admin', SMSD_ASSETS_URL ."css/smsd-admin{$min}.css", array(), $this->version );

	    	// Enqueue script
	    	if( self::$_action === 'edit' ){
	    		wp_enqueue_media();
	    	}
	    	wp_enqueue_script( 'smslider-admin', SMSD_ASSETS_URL ."js/smsd-admin{$min}.js", array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-tabs', 'jquery-ui-droppable', 'jquery-ui-accordion'), $this->version, true );
	    }
	    
	    /**
	     * Add new slide to database
	     * @access public
	     *
	     * @return void
	     */
	    public function add_new_slide(){
	    	//@since 1.1
	    	if ( !current_user_can( 'smsd_addnew' ) ){
		     	die(__('Cheatin&#8217; uh?', 'smslider'));
	    	}
	    	// check admin
	    	self::check_admin_referer( 'add-new' );

	    	if( empty( $_POST['smsd-name'] ) || empty( $_POST['smsd-alias'] ) ){
	    		wp_safe_redirect( add_query_arg( array('action'=> 'add-new'), SMSD_ADMIN_URL ) );
	    		die();
	    	}
	    	global $wpdb;

	    	$params['options'] = "";

	    	$alias = $this->format_alias_name( $_POST['smsd-alias'] );

	    	if( SMSlider::get_slide_id( $alias ) ){
	    		wp_safe_redirect( add_query_arg( array('action'=> 'add-new'), SMSD_ADMIN_URL ) );
	    		die();
	    	}

	    	if( isset( $_POST['size'] ) ){
		    	$params['size'] = $this->update_size( $_POST['size'], $alias );
		    }

	    	if( isset($_POST['settings']) ){
	    		$params['setting'] = $this->render_field( $_POST['settings'], 's_');
	    	}

	    	if( isset( $_POST['options'] ) ){
	    		$params['options'] = $this->render_field( $_POST['options'] );
	    	}else{
	    		$params['options'] = '';
	    	}

	    	$slide_id = $this->add_slider( $_POST['smsd-name'], $alias, $params );

	    	if( ! $slide_id ){
	    		wp_safe_redirect( add_query_arg( array('action'=> 'add-new'), SMSD_ADMIN_URL ) );
	   			die();
	    	}

	    	wp_safe_redirect( add_query_arg( array('slide'=> $slide_id, 'action'=>'setting' ), SMSD_ADMIN_URL ) );
	    	die();
	    }

	    /**
	     * Retrieve field name with prefix
	     * @access private
	     * 
	     * @param  string $field  field raw
	     * @param  string $prefix prefix default empty
	     * @return string         field name formatted
	     */
	    private function render_field( $field, $prefix='', $ex = null ){
	    	foreach( $field as $k=>$v){
    			$k = $prefix.$k;
    			if( null === $ex || strpos( $ex, "$k," ) !== false )
    				$params[$k] = $v;
    		}
    		return $params;
	    }

	    private function update_size( $size, $alias ){
    		$size = trim( strip_tags( $size ) );
    		if( in_array( $size, get_intermediate_image_sizes() ) ){
    			return $size;
    		}else if( preg_match( '/(^[0-9]+)x([0-9]+)$/i', $size, $matches ) ){
    			$size = $alias;
	   			$_size = SMSlider::get_option('size');
	   			$_size->{$alias} = array( intval( $matches[1] ), intval( $matches[2] ) );
	   			$this->update_option( 'size', $_size );
	   		}else{
	   			$size = 'SMSD_origin';
	   		}
	   		return $size;
	    }

	    /**
	     * Update slide
	     * @access public
	     * 
	     * @return void
	     */
	    public function update_slide(){
	    	global $wpdb;
	   		// check admin
	   		self::check_admin_referer( 'setting' );

	    	$sm_header = SMSD_ADMIN_URL;
	    	if( !is_numeric( $_POST['slide_id'] ) )
	    		wp_safe_redirect( $sm_header );

	    	$slide = SMSlider::get_slide( $_POST['slide_id'] );

	    	if( $slide ){
	    		if( self::$copy !== '' && isset( $_POST['smsd-alias'] )  ){
	    			$newalias = $this->format_alias_name( $_POST['smsd-alias'] );
	    			// update setting
	    			if( SMSlider::get_slide_id( $newalias ) === 0 ){
	    				$setting = SMSlider::get_option( 'type' );
				    	$setting->{$newalias} = $slide->ID;
				    	unset( $setting->{$slide->alias} );
				    	$this->update_option( 'type', $setting );
				    	$slide->alias = $newalias;
	    			}
	    		}

		    	if( isset( $_POST['size'] ) ){
			    	$slide->params->size = $this->update_size( $_POST['size'], $slide->alias );
			    }

		    	if( isset($_POST['settings']) ){
		    		$slide->params->setting = wp_parse_args( $this->render_field( $_POST['settings'], 's_'), (array)$slide->params->setting );
		    	}

		    	if( isset( $_POST['options'] ) ){
		    		if( ! isset( $_POST['settings']['slide'] ) )
		    			return false;
		    		$setting = SMSlider::get_option( esc_attr( $_POST['settings']['slide'] ) );
		    		if( isset( $setting->default->dis ) ){
		    			$slide->params->options = wp_parse_args( $this->render_field( $_POST['options'], '', $setting->default->dis ));
		    		}else{
		    			$slide->params->options = $this->render_field( $_POST['options'] );
		    		}
		    	}else{
		    		$slide->params->options = '';
		    	}

		    	$slide->title = $_POST['smsd-name'];

		    	$row = $this->update_slider( $slide );

		    	if( $row ){
		    		$sm_header = array('slide'=>$slide->ID, 'action'=>'setting');
		    		if( self::$copy ){
		    			$sm_header['action'] = 'edit';
		    			$sm_header['clone'] = self::$copy;
		    		}
		    		$sm_header = add_query_arg( $sm_header, SMSD_ADMIN_URL );
		    	}
	    	}
	    	wp_safe_redirect( $sm_header );
	   	}

	    protected function delete_slider( $slide_id ){
	    	//@since 1.1
	    	if ( !current_user_can( 'smsd_delete' ) ){
		     	die(__('Cheatin&#8217; uh?', 'smslider'));
	    	}
	        global $wpdb;
	        return $wpdb->delete(
	            SMSlider::get_table(),
	            array(
	                'ID'=>$slide_id
	            )
	        );
	    }

	    private function update_slider( $slide ){
	    	global $wpdb;
	    	$table = SMSlider::get_table();
	    	return $wpdb->query( $wpdb->prepare("UPDATE $table SET title=%s, alias=%s, params=%s, updated_at=%s WHERE ID=%d", $slide->title, $slide->alias, json_encode( $slide->params ), current_time('mysql'), $slide->ID ) );
	    }

	    protected function add_slider( $title, $alias, $params ){
	        global $wpdb;
	        $created_at = current_time('mysql');
	        $wpdb->insert(
	            SMSlider::get_table(),
	            array(
	                'title'=>$title,
	                'alias'=>$alias,
	                'params'=>json_encode( $params ),
	                'updated_at'=> $created_at,
	                'created_at'=> $created_at
	            ),
	            array(
	                '%s',
	                '%s',
	                '%s'
	            )
	        );
	        $slide_id = $wpdb->insert_id;

	        // update setting
	        $type = SMSlider::get_option( 'type' );
	    	$type->$alias = $slide_id;
	    	$this->update_option( 'type', $type );

	        return $slide_id;
	    }

	   	/**
	   	 * Edit slide
	   	 * @access public
	   	 * 
	   	 * @return void
	   	 */
	   	public function edit_attachment(){
	   		//@since 1.1
	   		if ( !current_user_can( 'smsd_edit' ) ){
		     	die(__('Cheatin&#8217; uh?', 'smslider'));
	   		}
		    // check admin
	   		self::check_admin_referer( 'edit-attachment' );
	   		$slide = SMSlider::get_slide( $_POST['slide_id'] );
	   		$insert = array();
	   		if( isset( $_POST['params'] ) && $_POST['params'] == 1){
	   			$params = $slide->params;
	   			if( isset( $_POST['setting-image'] ) && !empty( $_POST['setting-image'] ) ){	   				
		   			$params->setting->s_image = stripcslashes( strtolower( $_POST['setting-image'] ) );
		   		}
		   		if( isset( $_POST['setting-video'] ) && !empty( $_POST['setting-video'] ) ){
		   			$params->setting->s_video = stripcslashes( strtolower( $_POST['setting-video'] ) );
		   		}
		   		// setting page
	   			$params->setting->s_page =  isset( $_POST['setting-page'] ) ? $_POST['setting-page'] : "";

	   			$slide->params = $params;

		   		$this->update_slider( $slide );
	   		}

	   		if( isset($_POST['image-type']) && !empty( $_POST['image-type']) ){
	    		$insert = $this->update_media( $_POST['image-type'], 1, $slide->alias );
	    	}

	    	if(isset( $_POST['video-type']) && !empty( $_POST['video-type']) ){
	    		$insert = $this->update_media( $_POST['video-type'], 2, $slide->alias, $insert );
	   		}

	    	if( isset( $_POST['delete'] ) ){
	    		global $wpdb;
	    		foreach( (array)$_POST['delete'] as $post_id ){
	    			if( !isset( $insert[ $post_id ] ) ){
	    				$wpdb->delete( $wpdb->posts, array( 'ID' => $post_id, 'post_type' => 'smsd-type' ), array( '%d', '%s' ) );
	    			}
	    		}
	    	}
	    	// @since 1.1.2
	    	$this->add_slide_setting( $slide->ID, $slide->alias );

	    	wp_safe_redirect( add_query_arg( array('slide' => $slide->ID, 'action' => 'edit'), SMSD_ADMIN_URL ) );
	   	}

	   	/**
	   	 * Set screen option for current page
	   	 * @access public
	   	 *
	   	 * @param boolean $status default false.
	   	 * @param string $option the option name.
	   	 * @param integer $value  the number of rows to use.
	   	 */
	   	public function set_screen_option($status, $option, $value){
			if ( 'slides_per_page' == $option ) return $value;
			return $status;
		}

		/**
		 * Update slide to database
		 * @access private
		 *
		 * @param  string $alias   The slide's alias name
		 * @param  array $post   List post field
		 * @param  string $type   Type of slider
		 * @param  array  $insert List slide id
		 * @return array         List slide id
		 */
	    private function update_media( $post, $type, $alias, array $insert = array() ){
	    	$post_gended = array();

    		foreach( $post as $k=>$v){
    			$post_id = $this->get_post_id( $k );
    			$i = 0;
    			foreach( $v as $o => $p){
    				$dfault = array(
						'url'     =>'#smsd-link',
						'title'   =>'',
						'caption' =>'',
						'alt'     =>''
    				);
    				$p = wp_parse_args( $p, $dfault );
    				$url = empty( $p['url'] ) ? '#smsd-link' : esc_url_raw( $p['url'] );
    				if( $type === 2 ){
    					$p['caption'] = esc_url_raw( $p['caption'] );
    				}
    				$smsd_post = array(
						'post_type'    => 'smsd-type',
						'post_title'   => $p['title'],
						'post_excerpt' => $p['caption'],
						'post_content' => $p['alt'],
						'menu_order'   => $o,
						'guid'         => $url,
						'post_status'  => "publish"
    				);

    				$term = true;
    				if( $post_id !== 0){
    					$parent = $this->get_ancestor($post_id);
						if( $i === 0 ){
							$post_old = isset( $p['post_id'] ) ? (int)$p['post_id'] : $post_id;
							if( has_term( $alias, self::TAXONOMY, $post_old ) ){
								$smsd_post['ID'] = $post_old;
								if( isset( $p['post_id'] ) && $post_old !== $post_id ){
									$smsd_post['post_parent'] = $parent;
								}
    							$id = wp_update_post( $smsd_post );
	    						$this->update_url( $id, $url );
    							$insert[$id] = 1;
    							$term = false;
							}else{
								$smsd_post['post_parent'] = $parent;
    							$id = wp_insert_post( $smsd_post );
    							$insert[$id] = 1;
							}
    					}else{	
    						$smsd_post['post_parent'] = $parent;
    						$id = wp_insert_post( $smsd_post );
    						$insert[$id] = 1;
    					}
    					if( $type === 1 && $insert[$id] = 1 && !isset( $post_gended[ $parent ] ) ){
    						$generated = $this->generate_attachment( $parent );
    						$post_gended[ $parent ] = 1;
    					}
					}else{
						$id = wp_insert_post( $smsd_post );
						$insert[$id] = 1;
					}

    				if( $id ){
    					$this->update_post_meta( $id, 'type', $type);
    					if( $url !== '#smsd-link' ){
    						$target = ( isset( $p['target'] ) && $p['target'] == 1 ) ? 1 : 0;
    						$this->update_post_meta( $id, 'target', $target);
    					}
						if( $term ){
							wp_set_object_terms( $id, $alias, self::TAXONOMY );
						}
					}

					$i++;
    			}
    		}
    		return $insert;
	    }

	   	/**
	   	 * Update new slide to setting
	   	 * @access private
	   	 * 
	   	 * @param int $slide_id Slide ID
	   	 * @param string $alias     Alias name
	   	 * @return void
	   	 */
	   	private function add_slide_setting( $slide_id, $alias ){
	   		$type = SMSlider::get_option( 'type' );
	   		if( isset( $type->$alias ) && $type->$alias === $slide_id ){
	   			return true;
	   		}
	    	$type->$alias = $slide_id;
	    	$this->update_option( 'type', $type );
	   	}

	    /**
	     * Delete a slide in the database
	     * @access public
	     * 
	     * @return mixed the number of rows effected or false
	     */
		public function delete_slide(){
			//@since 1.1
			if ( !current_user_can( 'smsd_delete' ) ){
		     	die(__('Cheatin&#8217; uh?', 'smslider'));
			}
			global $wpdb;
	   		// check nonce
	   		self::wp_verify_nonce( $_POST['sm_nonce'], 'nonce' );

	   		$slide = SMSlider::get_slide( $_POST['slide'] );
	   		
	   		if( $slide ){
	   			$row = $wpdb->delete(
		    		SMSlider::get_table(),
		    		array(
		    			'ID'=>$slide->ID
		    		)
		    	);
		    	if( $row ){
		    		$setting = SMSlider::get_option('type');
		    		$alias = $slide->alias;
		    		$args = array(
			    		'tax_query' => array(
			    			array(
								'taxonomy' => self::TAXONOMY,
								'field'    => 'slug',
								'terms'    => $alias
							)
			    		)
			    	);
			    	$query = SMSlider::find( $args );
			    	if( $query->found_posts ){
			    		$posts = $query->posts;
				    	foreach( $posts as $p ){
				    		delete_post_meta( $p->ID, '_smsd-meta_type');
				    		delete_post_meta( $p->ID, '_smsd-meta_target');
				    		wp_delete_object_term_relationships( $p->ID, self::TAXONOMY);
				    		wp_delete_post( $p->ID );
				    	}
				    	$term = get_term_by('slug', $alias, self::TAXONOMY );
				    	wp_delete_term( $term->term_id, self::TAXONOMY );
			    	}
			    	if( isset( $setting->$alias ) ){
			    		unset( $setting->$alias);
			    	}else{
			    		unset( $setting->{$slide->ID});
			    	}
			    	$this->update_option( 'type', $setting );
		    	}
		    	echo $row;
	   		}else{
	   			echo $slide;
	   		}
	    	wp_die();
	   	}

	   	/**
	   	 * Clone slide to the database
	   	 * @access public
	   	 * 
	   	 * @return string
	   	 */
	   	public function clone_slide(){
	   		//@since 1.1
	   		if ( !current_user_can( 'smsd_edit' ) ){
		     	die(__('Cheatin&#8217; uh?', 'smslider'));
	   		}
	   		global $wpdb;
	   		// check nonce
	   		self::wp_verify_nonce( $_POST['sm_nonce'], 'nonce' );

	   		$slide = SMSlider::get_slide( $_POST['slide'] );
	   		$alias = $slide->alias;
	   		$sm_header = 'admin.php?page=sm-slider';
	   		if( $slide ){
	   			if( isset( $_POST['alias'] ) ){
	   				$alias = $this->format_alias_name( $_POST['alias'] );
	   				$slide->title = ucfirst( str_replace( '-', ' ', $alias ) );
	   			}
		   		$aliasnew = SMSlider::get_slide_id( $alias ) === 0 ? $alias : $alias .'-copy-'. uniqid();
		   	
		    	$last_id = $this->add_slider( $slide->title, $aliasnew, $slide->params );
		    	if( $last_id ){
		    		if( $aliasnew === $slide->alias ){
		    			$sm_header = add_query_arg( array('slide'=>$last_id, 'action'=>'edit', 'clone'=> $slide->alias), $sm_header);
		    		}else{
		    			$sm_header = add_query_arg( array('slide'=>$last_id, 'action'=>'setting', 'clone'=>$slide->alias), $sm_header);
		    		}
		    	}
	   		}
	    	echo $sm_header;
	    	wp_die();
	   	}

   	    /**
	     * Retrieve alias name
	     * @access protected
	     * 
	     * @param  string $alias alias name raw
	     * @return string        alias name formatted
	     */
	    protected function format_alias_name( $alias ){
	        return str_replace(' ', '-', strtolower( trim( $alias ) ) );
	    }

	   	/**
	   	 * Check exist alias name
	   	 * @access public
	   	 * 
	   	 * @return integer
	   	 */
	   	public function exist_slide(){
	    	// check nonce
	   		self::wp_verify_nonce( $_POST['sm_nonce'], $_POST['act'] );
	   		$alias = str_replace( ' ','-', strtolower( trim( $_POST['alias'] ) ) );
	   		$slide_id = SMSlider::get_slide_id( $alias );
	   		if( $slide_id > 0 ){
	   			$slide = SMSlider::get_slide( esc_attr( $_POST['old'] ) );
	   			if( ! is_null( $slide ) ){
	   				$slide_id = $slide_id === $slide->ID ? 0 : $slide_id;
	   			}
	   		}

	   		if( defined('DOING_AJAX') && DOING_AJAX ){
		   		echo $slide_id;
		   		wp_die();
		   	}else{
		   		return $slide_id;
		   	}
	    }

	    /**
	     * Retrieve shortcode
	     * @access public
	     * 
	     * @return mixed
	     */
	    public function editor(){
	    	if( !current_user_can( 'edit_posts' ) || !current_user_can( 'edit_pages' ) )
	    		return;
	    	// check nonce
	   		self::wp_verify_nonce( $_POST['nonce'] );

	    	$type = SMSlider::get_option('type');
	    	$type = (array)$type;
	    	if( empty( $type )){
	    		echo 0;
	    	}else{
	    		$type = json_encode( array_keys($type) );
	    		echo $type;
	    	}
	    	wp_die();
	    }

	    /**
	     * Response notices
	     * @access public
	     * 
	     * @return void
	     */
	    public function response_notices(){
	    	global $hook_suffix;
    		if( isset( $_REQUEST['clone']) && self::$_action === 'setting' ){
    			add_settings_error( 'smsd-notices', 'smsd-clone-slide', __('Copy sucessfull, please update friendly alias name if you want, then it will be not able for edit.', 'smslider'), 'updated' );
    		}
	    	settings_errors( 'smsd-notices' );
	    }

	    public static function notices( array $notices ){
	    	if( empty( $notices ) ) return;
	    	$args = array(
	    		'setting'=>'smsd-notices',
	    		'code'=>'smsd-notices-slug',
	    		'msg'=>'',
	    		'type'=>'updated'
	    	);
	    	$notices = wp_parse_args( $notices, $args );
	    	if( empty( $notices['msg'] ) ) return;

    		add_settings_error( $notices['setting'], $notices['code'], $notices['msg'], $notices['type'] );
	    	settings_errors( $notices['setting'] );
	    }

	    /**
	     * Custom mce buton
	     * @access public
	     * 
	     * @return void
	     */
	    public function render_smsd_button() {
		    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) )
		     	return;
		    if( get_user_option('rich_editing') == 'true' ){
		    	add_filter( 'mce_external_plugins', array( &$this, 'smslider_button' ) );
		    	add_filter( 'mce_buttons', array( &$this, 'reg_smslider_button' ), 100 );
		    }
		}

		/**
		 * Register new mce button
		 * @access public
		 * 
		 * @param  array $buttons list buttons
		 * @return array          list buttons
		 */
		public function reg_smslider_button( $buttons ) {
			array_push( $buttons, "smslider" );
		    return $buttons;
		}

		/**
		 * Register script for button
		 * @access public
		 * 
		 * @param  array $plugin_array plugin array default
		 * @return array               list source of plugin
		 */
		public function smslider_button( $plugin_array ) {
		    $min = SMSD_LIVE ? '.min' : '';
		    $plugin_array['smslider_button'] = SMSD_ASSETS_URL ."js/smsdbutton{$min}.js";
		    return $plugin_array;
		}

		/**
		 * Update the meta value
		 * @access private
		 * 
		 * @param  integer $post_id post id
		 * @param  string $key     meta key
		 * @param  mixed $val     the new value
		 * @return mixed          
		 */
	   	private function update_post_meta( $post_id, $key, $val ){
	   		$key = "_smsd-meta_{$key}";
	   		return update_post_meta($post_id, $key, $val );
	   	}

	   	private function generate_attachment( $att_id ){
	   		$fullsizepath = get_attached_file( $att_id );

			if ( false === $fullsizepath || ! file_exists( $fullsizepath ) )
				return false;

			@set_time_limit( 300 ); // 5 minutes per image should be PLENTY

			$metadata = wp_generate_attachment_metadata( $att_id, $fullsizepath );

			if ( is_wp_error( $metadata ) || empty( $metadata ) )
				return false;

			return wp_update_attachment_metadata( $att_id, $metadata );
	   	}

	    /**
	     * Check exist term
	     * @access private
	     * 
	     * @param  string $term term name
	     * @param  integer $post_id The post's id
	     * @return boolean        true if term exist, other is false
	     */
	    private function exist_term( $term, $post_id ){

	    	return has_term( $term, self::TAXONOMY, $post_id );
	    }

	    /**
	     * add new term to the database
	     * @access private
	     * 
	     * @param  string $term The term to add
	     * @return array      see wp_insert_term
	     */
	    private function insert_term( $term ){
	    	return wp_insert_term( $term, self::TAXONOMY );
	    }

	    /**
	     * Set url for slider
	     *@access private
	     * 
	     * @param  integer $post post id
	     * @param  string $url  custom link of slider
	     * @return integer|boolean       number of rows affected or false
	     */
	    private function update_url( $post, $url ){
	    	global $wpdb;
	    	$sql = $wpdb->prepare( "UPDATE $wpdb->posts SET guid=%s WHERE ID=%d", $url, $post );
	    	return $wpdb->query( $sql );
	    }

	   	/**
	   	 * Check admin To avoid security exploits.
	   	 * @access private
	   	 * 
	   	 * @param  string $action name of action
	   	 * @return false|int
	   	 */
	   	private static function check_admin_referer( $action ){
	   		$nonce = "smsd-none_{$action}";
	   		$action = "smsd-act_{$action}";
	   		//@since 1.1
	   		if( !current_user_can( 'manage_slider' ) )
	    		wp_die( 'Insufficient privileges!' );
	    	// check nonce
	    	check_admin_referer( $action, $nonce );
	   	}

	   	/**
	   	 * Verify nonce
	   	 * @access private
	   	 * 
	   	 * @param  string $nonce  Nonce to verify
	   	 * @param  string $action Action name
	   	 * @return void
	   	 */
	   	private static function wp_verify_nonce( $nonce, $action=null ){
	   		if( ! wp_verify_nonce($nonce, self::render_nonce( $action ) ) )
	   			wp_die( 'Insufficient privileges!' );
	   	}

	   	/**
	   	 * Retrieve ancestor of post
	   	 * @access private
	   	 * 
	   	 * @param  integer $post_id post id
	   	 * @return integer the post_id of ancestor if exist, or return itself.
	   	 */
	   	private function get_ancestor( $post_id ){
	   		$ancestor = $this->get_post_ancestors( $post_id );
   			if( ! empty( $ancestor ) ){
   				return array_pop( $ancestor );
   			}

   			return $post_id;
	   	}

	   	function get_post_ancestors( $post ) {
			$post = get_post( $post );

			if ( ! $post || empty( $post->post_parent ) || $post->post_parent == $post->ID )
				return array();

			$ancestors = array();

			if( $post->post_type === 'attachment' )
				return array( $post->ID );

			$id = $ancestors[] = $post->post_parent;

			while ( $ancestor = get_post( $id ) ) {
				// Loop detection: If the ancestor has been seen before, break.t

				if ( $ancestor->post_type == 'attachment' || empty( $ancestor->post_parent ) || ( $ancestor->post_parent == $post->ID ) || in_array( $ancestor->post_parent, $ancestors ) )
					break;

				$id = $ancestors[] = $ancestor->post_parent;
			}
			return $ancestors;
		}

		/**
		 * @since 1.1
		 * Update role manage slider
		 * @access public
		 * @return void
		 */
		public function manage_sliders(){
			global $wp_roles;
			// @since 1.1.2.2 update for security
			if( !current_user_can('manage_options') ){
				die(__('Cheatin&#8217; uh?', 'smslider'));
			}
			$roles = SMSlider::get_option( 'manager' );
			if( empty( $roles ) ){
				wp_safe_redirect( add_query_arg( array('action'=>'config'), SMSD_ADMIN_URL ) );
				die();
			}
			foreach( $wp_roles->roles as $r=>$v ){
				$role = get_role( $r );
				if( isset( $roles->$r ) ){
					foreach( SMSLider::$caps as $cap=>$v ){
						if( in_array( $cap, $roles->$r ) ){
							$role->add_cap( $cap );
						}else{
							$role->remove_cap( $cap );
						}
					}
				}else{
					foreach( SMSLider::$caps as $cap=>$v ){
						$role->remove_cap( $cap );
					}
				}
			}
			return true;
		}

		/**
		 * @since 1.1
		 * Update settings
		 * @access public
		 * @return void
		 */
		public function update_setting(){
			global $wpdb;
			$old_types = (array)SMSlider::get_option( 'types' );
			$new_types = array();
			if( isset( $_POST['type'] ) ){
				$max = 2;
				if( !isset( $_POST['show-all']) ) return;
				$show_all = (array)$_POST['show-all'];
				foreach( $_POST['type'] as $src=>$types ){
					if( count( $new_types ) < $max ){
						foreach( $types as $type ){
							if( count( $new_types ) < $max ){
								$libs = SMSlider::get_libs();

								if( ! $libs ){
									wp_die( __('Sorry, update Failed', 'smslider') );
								}
								if( isset( $libs->libs->$type ) ){
									if( isset( $show_all[ $type ] ) && $show_all[ $type ] == 0 ){
										$libs->libs->$type->all_page = $show_all[ $type ];
									}
									$this->update_option( $type, $libs->libs->$type );
								}
								$new_types[ $type ] = $src;

								if( isset( $old_types[ $type ] ) ){
									unset( $old_types[ $type ] );
								}
							}
						}
					}
				}

				foreach( $old_types as $type=>$src ){
					$this->delete_option( $type );
				}

				$this->update_option( 'types', $new_types );
			}

			if( isset( $_POST['roles'] ) ){
				$roles = new stdClass;
				foreach( $_POST['roles'] as $role=>$caps ){
					$newroles= array();
					foreach( $caps as $cap=>$e ){
						if( $e === 'true' ){
							array_push( $newroles, $cap );
						}
					}
					if( !empty( $newroles ) && in_array( 'manage_slider', $newroles ) ){
						$roles->$role = $newroles;
					}
				}
				$this->update_option( 'manager', $roles );
			}

			wp_safe_redirect( add_query_arg( array('action'=> 'config'), SMSD_ADMIN_URL ) );
		}


	   	/**
	   	 * Retrieve post id
	   	 * @access private
	   	 * 
	   	 * @param  string $post_id
	   	 * @return integer          the post id after format
	   	 */
	   	private function get_post_id( $post_id ){
	   		return absint( substr( $post_id, 6, strlen( $post_id ) ) );
	   	}

	   	/**
	   	 * Update option
	   	 * @access private
	   	 * 
	   	 * @param  string $name  Option name
	   	 * @param  mixed $value  The new value of option
	   	 * @return boolean        True if option value has changed, false if not or if update faied.
	   	 */
	    private function update_option( $name, $value ){
	    	$name = "-smsd_{$name}";
	    	$value = json_encode( $value );
	    	return update_option( $name, $value );
	    }


	    /**
	     * @since 1.1
	   	 * Delete option
	   	 * @access private
	   	 * 
	   	 * @param  string $name  Option name
	   	 * @return boolean        True if option value has changed, false if not or if update faied.
	   	 */
	    private function delete_option( $name ){
	    	$name = "-smsd_{$name}";
	    	return delete_option( $name );
	    }

	    /**
	     * Retrieve unique string
	     * @access protected
	     * 
	     * @return string unique string
	     */
	   	public static function get_uniq(){
	    	return substr( wp_create_nonce( self::render_nonce() ), 0, 5) .'_';
	    }

	    private static function render_nonce( $action = null ){
	    	$action = is_null( $action ) ? self::$_action : $action;
	    	return "act_smsd-{$action}";
	    }

	    /**
	     * Render attribute name
	     * @access public
	     * 
	     * @param  string $type type name
	     * @param  string $name attribute name
	     * @param  int $n    index
	     * @return string       new attribute name
	     */
	    public static function render_name( $type, $name, $n ){
	    	return "{$type}-type[{$name}][{$n}]";
	    }

	    /**
	     * Render field
	     * @access public
	     * 
	     * @param  array $ops option of field
	     * @return string      field
	     */
	    public static function render_element( $ops, $active=0 ){
	    	$smsd_ops = array();
	    	$i = 0;
	    	foreach( $ops as $name=>$op ){
	    		$header = "<div id=\"{$op['id']}-{$name}\" data-parent=\"{$op['id']}-{$op['parent']}\" class='field-items dragable'><h4>". __( $name,'smslider') ."</h4>";
	    		$footer = "</div>";
	    		$names = "options[$name]";
	    		if( $op['type'] === 'select' ){
	    			$body = self::render_select( $names, $op['options'], $op['default']);
	    			$body = implode('', array( $header, $body, $footer ) );
	    			if( $i % 2 === 0 ){
	    				$smsd_ops['select'][] = $body;
	    			}else{
	    				$smsd_ops['text'][] = $body;
	    			}
	    			$i++;
	    		}elseif( $op['type'] === 'textarea' ){
	    			$body = self::render_textarea( $names, $op['default'] );
	    			$body = implode('', array( $header, $body, $footer ) );
	    			$smsd_ops['textarea'][] = $body;
	    		}elseif( 'boolean' === $op['type'] ){
	    			$body = self::render_boolean( $names, $op['default'] );
	    			$body = implode('', array( $header, $body, $footer ) );
	    			if( $active ){
	    				$disabled = ( $op['default'] === 'true' ) ? 'disabled' : '';
	    				$body .= "<input id='field_{$op['id']}-{$name}' name='{$names}' {$disabled} type='hidden' value='false'>";
	    			}
	    			if( $i % 2 === 0 ){
	    				$smsd_ops['select'][] = $body;
	    			}else{
	    				$smsd_ops['text'][] = $body;
	    			}
	    			$i++;
	    		}else{
	    			$body = self::render_input( $names, $op['type'], $op['default'] );
	    			$body = implode('', array( $header, $body, $footer ) );
	    			if( $i % 2 === 0 ){
	    				$smsd_ops['select'][] = $body;
	    			}else{
	    				$smsd_ops['text'][] = $body;
	    			}
	    			$i++;
	    		}
	    	}
	    	foreach( $smsd_ops as $k=>$op ){
	    		$smsd_ops[$k] = implode('', $op);
	    	}
	    	return $smsd_ops;
	    }

	    public static function scandir( $path, $src='theme', $folders=array() ){
	    	if( ! is_dir( $path ) ){
	    		return array();
	    	}
	    	$files = scandir( $path );
	    	foreach( $files as $file ){
	    		if( strpos( $file, '.' ) !== false ){
	    			continue;
	    		}
    			if( ! isset( $folders[ $file ] ) ){
    				$folders[ $file ] = $src;
    			}
	    	}
	    	return $folders;
	    }

	    /**
	     * Render select
	     * @access public
	     * 
	     * @param  string $name   name
	     * @param  string $ops    option select
	     * @param  string $dfault value default
	     * @return string         select
	     */
	    private static function render_select( $name, $ops, $dfault ){
	    	$select = "<select name='{$name}' class='item-select field-item widefat'>";
	    	foreach( $ops as $op ){
	    		$selected = $op === $dfault ? 'selected' : '';
	    		$trans = esc_attr__( $op, 'smslider' );
	    		$select .= "<option {$selected} value='{$op}'>{$trans}</option>";
	    	}
	    	$select .='</select>';
	    	return $select;
	    }

	    /**
	     * Render checkbox
	     * @access private
	     * 
	     * @param  string $name   name
	     * @param  string $dfault default value
	     * @return string         checkbox
	     */
	    private static function render_boolean( $name, $dfault ){
	    	$checked = $dfault === 'true' ? 'checked':'';
	    	return "<input name='{$name}' type='checkbox' value='true' class='item-checkbox field-item' {$checked}>";
	    }

	    /**
	     * Render textarea
	     * @access public
	     * 
	     * @param  string $name   name of textarea
	     * @param  string $dfault default value
	     * @return string         textarea
	     */
	    private static function render_textarea( $name, $dfault ){
	    	return "<textarea name='{$name}' rows='4' class='area-item field-item widefat'>{$dfault}</textarea>";
	    }

	    /**
	     * Render other input type
	     * @access public
	     * 
	     * @param  string $name   name of input
	     * @param  string $type   type of input
	     * @param  string $dfault default value
	     * @return string         input
	     */
	    private static function render_input( $name, $type, $dfault ){
	    	return "<input name='{$name}' type='{$type}' value='{$dfault}' class='item-input field-item widefat' >";
	    }

	    /**
	     * Output one or more strings
	     * @access public
	     * 
	     * @param  string $action Action name
	     * @param  array $args   list params
	     * @param  string $title  title for output
	     * @return void
	     */
	   	public function render_slider( $action, $title='' ){
	    	$sm_action = strtolower( str_replace( ' ', '-', $action ) );
	    	$_action = "smsd-act_{$sm_action}";
	    	?>
	    	<div id="smslider-box" class="wrap">
	    		<h2><?php _e( str_replace( array('attachment','term'), '', $action) .' '. $title, 'smslider' );?></h2>
	    		<form id="smsd-form-<?php echo $sm_action;?>"  method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
	    			<input type="hidden" name="action" value="<?php echo $_action; ?>">
            		<?php wp_nonce_field( $_action, "smsd-none_{$sm_action}", false );
            			$temp = "{$sm_action}-slider";
            			SMSlider::view( $temp );
            		?>
	    		</form>
	    	</div>
	    	<a class="hidden dashicons dashicons-arrow-up-alt" id="sm-to-top"></a>
	    	<?php
	    }

	}
}
?>