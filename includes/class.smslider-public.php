<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
if( ! class_exists( 'SMSD_Public' ) ){
	class SMSD_Public{
		private $plugin_slug, $version, $slide;

		public function __construct( $plugin_slug, $version ){
			$this->plugin_slug = $plugin_slug;
			$this->version = $version;
			add_action( 'init', array( &$this, 'register_shortcode' ) );
		}

	    public function enqueue_public_script(){
	    	$min = SMSD_LIVE ? '.min' : '';
	    	$types = SMSlider::get_option('types');
	    	foreach( $types as $lib=>$src ){
	    		$assets = SMSLider::get_asset( $lib );
	    		$libs = SMSLider::get_option( $lib );
	    		if( !isset( $libs->all_page ) || $libs->all_page === 1 ){
	    			wp_enqueue_style( $lib, $assets ."/{$libs->css}.min.css", array(), $libs->v );
	    			wp_enqueue_script( $lib, $assets ."/{$libs->js}.min.js",array(),$libs->v,true);
	    		}
	    	}
	    	wp_enqueue_style( 'smsd-public', SMSD_ASSETS_URL ."css/smsd-public{$min}.css", array(), $this->version );
	    	wp_enqueue_script( 'smsd-public', SMSD_ASSETS_URL ."js/smsd-public{$min}.js", array("jquery"), $this->version, true );
	    }

	    public function register_shortcode(){
	    	// check support thumbnail
	   		if( !current_theme_supports('post-thumbnails') ) {
				add_theme_support('post-thumbnails');
			}

			$_size = SMSlider::get_option( 'size' );
			if(  $_size ){
				$crop = true;
				foreach( (array)$_size as $n=>$size ){
					$size = apply_filters( 'smslider/img_size_'. $n, $size );
					add_image_size( $n, $size[0], $size[1], $crop );
				}
			}

	    	add_shortcode( "smslider", array( &$this, 'slider_shortcode') );
	    	
	    	add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_public_script') );
	    }

	    public function slider_shortcode( $atts ){
	    	if( !is_array( $atts ) )
	    		return __('Sorry, slide not found','smslider');
	    	$alias = array_shift( $atts );
	    	$slide_id = is_numeric( $alias ) ? $alias : SMSlider::get_slide_id( $alias );

	    	if( ! $slide_id ){
	    		if( current_user_can('manage_options') )
	    			return __('Sorry, Please recheck slide <code>'. $slide_id .'</code> notfound, thanks.<br/>', 'smslider');
	    		else
	    			return __('Sorry, Slide notfound! <br/>', 'smslider');
	    	}

	    	$this->slide = $slide = SMSlider::get_slide( $slide_id );
	   		
	    	$setting = $slide->params->setting;
	    	$wrap = strtolower( apply_filters( 'smslider/wrap-tagname', $setting->s_wrap ) );
	    	
	    	$slide_lib = $this->get_slide_lib( $setting->s_slide, $wrap );
	    	
	    	if( is_null( $slide_lib ) ) return __('Sorry, this slide not working, please try other slide, thanks.<br/>', 'smslider');
	    	// include js
	    	if( isset( $slide_lib->all_page ) && $slide_lib->all_page == 0 ){
	    		$assets = SMSLider::get_asset( $setting->s_slide );
		    	if( $assets ){
		    		wp_enqueue_style( $setting->s_slide, $assets ."/{$slide_lib->css}.min.css", array(), $slide_lib->v );
		    		wp_enqueue_script( $setting->s_slide, $assets ."/{$slide_lib->js}.min.js",array(),$slide_lib->v,true);
		    	}
	    	}
	    	
	    	if( isset( $setting->s_custom_css ) && !empty( $setting->s_custom_css ) ){
	    		wp_add_inline_style( $setting->s_slide, $setting->s_custom_css );
	    	}

	    	// set attribute
	    	$attr_id = "smsd-". $slide->alias;
	    	$html_id = isset( $atts['html_id'] ) ? esc_attr( $atts['html_id'] ) : apply_filters( 'smslider/attribute-id', $attr_id );
	    	$html_class = isset( $atts['html_class'] ) ? esc_attr( $atts['html_class'] ) : (string)apply_filters( 'smslider/attribute-class', $attr_id );
	    	//@since 1.0.3
	    	$atts = '';
	    	if( isset($slide_lib->default->attr) ){
	    		foreach( $slide_lib->default->attr as $attr => $v ){
	    			if( $attr === 'class' ){
	    				$v .= ' smsd-wrapper '. $html_class;
	    			}
	    			$atts .= " {$attr}='{$v}'";
	    		}
	    	}

	    	$sm_image = isset( $setting->s_image ) ? $setting->s_image : self::default_image();

	    	$sm_video = isset( $setting->s_video ) ? $setting->s_video : self::default_video();

	    	$q = SMSLider::get_edit_slide( $slide->alias );

	    	$slider = '';
	    	$i = 1;
	    	if( $q->have_posts() ):
	    		// wrap slide
	    		$slider = "<div class='smslider' data-maxw='{$slide->size[0]}' max-height='{$slide->size[1]}px'><{$slide_lib->wrap} id='{$html_id}' {$atts}>";
	    		//@since 1.0.3
	    		$slider .= apply_filters( 'smslider/beforeloop/'. $setting->s_slide, '', $html_id );
	    		// inner wrap
	    		if( !empty( $slide_lib->inner ) ) $slider .= $slide_lib->inner[0];

	    		while( $q->have_posts()): $q->the_post();
	    			$type = (int)SMSLider::get_post_meta( get_the_ID(), 'type' );
	    			$links = get_post_field( 'guid', get_the_ID() );
	    			$before = $after = '';
	    			if( !empty( $links ) && $links != '#smsd-link' ){
	    				$target = SMSlider::get_post_meta( get_the_ID(), 'target' ) == 1 ? '_blank' : '_self';
	    				$before = "<a href='{$links}' target='{$target}'>";
	    				$after = "</a>";
	    			}
	    			$content = $type === 2 ? $sm_video : $sm_image;
	    			if( !empty( $slide_lib->child ) ){
	    				$slider .="<{$slide_lib->child} class='sm-item-{$i}'>{$before}";
	    			}
		    		$slider .= preg_replace_callback('/\[\s*([\w-]+)\s*\]/', array( &$this, 'update_content' ), $content);
		    		$slider .= $after;
		    		if( !empty( $slide_lib->child ) ){
		    			$slider .='</'. $slide_lib->child .'>';
		    		}
		    		$i++;
	    		endwhile;wp_reset_postdata();
	    		// end inner
	    		if( !empty( $slide_lib->inner ) ) $slider .= $slide_lib->inner[1];

	    		//@since 1.0.3
	    		$slider .= apply_filters( 'smslider/afterloop/'. $setting->s_slide, '', $html_id, $i );

	    		// end wrap
	    		$slider .='</'. $slide_lib->wrap .'></div>';
	    		$slider .= $this->create_script( $slide_lib, $html_id );
	    	endif;
	    	return $slider;
	    }

	    public function create_script( $setting, $id, $pager='' ){
	    	ob_start();

	    	?>
	    	<script type="text/javascript">
	    		(function($){
	    			var options = <?php echo preg_replace( array('/"false"/','/"true"/','/"(\d+)"/'),array("false","true",'$1'), json_encode( $this->slide->params->options ) );?>;
	    			if( options.length === 0) options = {};
	    			var slideType ="<?php echo $setting->name;?>";
	    			<?php
	    				if( $setting->ready === true )
	    					echo '$(document).ready( function(){';
		    			else
		    				echo '$(window).load(function() {';

		    			//@since 1.0.3
		    			do_action( "smslider/beforejs/{$setting->name}", $id );
		    		?>
		    			$( "#<?php echo esc_js( $id );?>" )[slideType]( options );
		    			<?php 
		    				//@since 1.0.3
		    				do_action( "smslider/afterjs/{$setting->name}", $id );
		    			?>
		    		});
	    		})(window.jQuery)
	    	</script>
	    	<?php
	    	return apply_filters("smslider/load_slider-". strtolower( $setting->name ), ob_get_clean()  );
	    }

	    public function update_content( $matches ){
	    	global $post;
	    	$field = strtolower( $matches[1] );
	    	$size = $size = $this->slide->size;
	    	if( $field === 'src' ){
	    		return SMSLider::get_thumbnail( $size );
	    	}elseif( $field === 'video'){
	    		return wp_oembed_get( $post->post_excerpt, array('width'=>$size[0],'height'=>$size[1]) );
	    	}else{
	    		if( $field ==='caption' ){
	    			$field = 'excerpt';
	    		}elseif( $field ==='alt' ){
	    			$field = 'content';
	    		}
		    	$accept = array('title'=>1, 'content'=>3, 'excerpt'=>2 );
		    	
		    	if( isset( $accept[ $field ] ) ){
		    		$func = "get_the_{$field}";
		    		return call_user_func( $func );
		    	}else{
		    		return "[{$field}]";
		    	}
	    	}
	    }

	    public function loop_shortcode( $atts ){
	    	extract( shortcode_atts( array('field'=>'title','_smsd_off_default'=>'true'), $atts) );
	    	if( $_smsd_off_default == 'false' ){
	    		$field = strtolower( $field );
	    		// get setting
	    		$excerpt = array('caption'=>1,'video'=>2);
	    		if( isset( $excerpt[$field] ) ){
	    			$field = 'title';
	    		}

		    	$accept = array('title'=>1, 'content'=>2, 'excerpt'=>2, 'author'=>1, 'time'=>5, 'category'=>5);
		    	if( !isset( $accept[ $field ] ) ){
		    		return "[smsd-loop field=\"{$field}\"]";
		    	}
		    	$func = "the_$field";
		    	ob_start();
		    		call_user_func( $func );
		    	return ob_get_contents();
	    	}
	    	return "[smsd-loop field=\"{$field}\"]";
	    }

	    public static function default_image(){
	    	$dfault = '<img src="[src]" alt="[alt]" title="[title]">';
	    	return apply_filters( 'smslider/loop-image', $dfault );
	    }

	    public static function default_video(){
	    	$dfault = '[video]';
	    	return apply_filters( 'smslider/loop-video', $dfault );
	    }

	    public function get_slide_lib( $lib, $wrap ){
	    	$libs = SMSlider::get_option( $lib );
	    	if( !isset( $libs->default->inner ) )
	    		return;
	    	$inner = $libs->default->inner;
	    	$is_empty = empty( $inner );
	    	$libs->inner = $is_empty ? '' : explode( $lib, str_replace('TAG', $wrap, $inner ) );
	    	$libs->child = $wrap === 'ul' ? 'li' : '';
	    	$libs->wrap = $is_empty ? $wrap : 'div';
	    	return $libs;
	    }
		
	}
}
?>