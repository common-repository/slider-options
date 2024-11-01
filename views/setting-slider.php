<?php
	self::view('hidden-form');
	$s_type = $s_slide = $s_page = $s_wrap = $s_size = $s_custom_css = '';
	$slide = self::get_slide();
	extract( (array)$slide->params->setting );
	if( isset( $slide->params->size ) ){
		$s_size = $slide->params->size;
	}
 ?>
<table class="form-table">
<tr>
	<th scope="row"><label for="smsd-name"><?php _e('Slider name','smslider');?></label></th>
	<td><input type="text" id="setting-name" data-act="setting" class="sm-max-210" value="<?php echo $slide->title;?>" maxlength="100" size="25" name="smsd-name"></td>
</tr>
<tr>
	<th scope="row"><label for="smsd-alias"><?php _e('Alias name','smslider');?></label></th>
	<td>
		<input type="text" id="smsd-alias" data-act="setting" class="sm-max-210" maxlength="100" <?php if( '' === SMSD_Admin::$copy ){ echo 'disabled'; } ?> size="25" value="<?php echo $slide->alias;?>" name="smsd-alias">
		<p class="description"><?php _e("It must unique, we'll use it as shortcode id.","smslider");?></p>
	</td>
</tr>
<tr>
	<th scope="row"><label for="type"><?php _e("Type slider","smslider");?></label></th>
	<td>
		<select name="settings[type]" class="sm-max-210">
			<?php foreach( SMSD_Admin::$slide_type as $v=>$type ): ?>
				<option <?php selected( $s_type, $v ); if( $v !== 'attachment' ) echo 'disabled';?> value="<?php echo $v;?>"><?php echo $type;?></option>
			<?php endforeach;?>
		</select>
	</td>
</tr>
<tr>
	<th scope="row"><?php _e("Slider Options","smslider");?></th>
	<td>
		<div class="sm-max-50 clearfix">
			<div class="sm-col-5">
				<label for="smsd-wrap"><?php _e("Style slider type");?></label>
				<select name="settings[wrap]" class="sm-max-210">
					<option <?php selected( $s_wrap, 'ul' );?> value="ul">List</option>
					<option <?php selected( $s_wrap, 'div' );?> value="div">Div</option>
				</select>
			</div>
			<div class="sm-col-5">
				<div id="sm-has-thumnbail">
                    <label><?php _e('Choose image size','smslider');?></label><br/>
                    <input type="text" id="setting-thumb" placeholder="<?php _e('Large or 900x500', 'smslider');?>" name="size" value="<?php echo $s_size;?>">
                    <div class="sm-relative">
	                    <ul class="list-thumb">
	                        <?php $thumbs = get_intermediate_image_sizes();
	                       
	                        foreach( $thumbs as $size):
	                        	list( $wid, $hei ) = SMSlider::get_image_size( $size );
	                        ?>
	                        <li><a href="#" id="<?php echo $size;?>"><?php echo ucfirst( $size );?> <small>(<?php echo intval( $wid );?>x<?php echo intval( $hei );?>)</small></a></li>
	                    <?php endforeach;?>
	                    </ul>
	                </div>
                </div>
			</div>
		</div>
		<div>
			<label for="smsd-slide-type"><?php _e("Choose slider type","smslider");?></label>
			<div class="smsd-tab-slide smsd-tabs">
			<?php $lists_slider = (array)self::get_option('types');
				if( ! isset( $lists_slider[$s_slide] ) ){
					$link = add_query_arg( array('action'=>'config'), SMSD_ADMIN_URL );
					SMSD_Admin::notices(
						array(
							'setting'=>'smsd-dont-exist-type',
							'code'=>'smsd-dont-exist-type',
							'msg'=> __( "Library <code>{$s_slide}</code> don't exist, please go to submenu <a href='{$link}' target='_blank'>settings</a> add <code>{$s_slide}</code>, thanks.", 'smslider' ),
							'type'=>'error'
						)
					);
				}
			?>
				<ul id="smsd-tab-slide" class="category-tabs">
				<?php foreach( $lists_slider as $id=>$src ){
					?>
	                    <li class="<?php if( $id == $s_slide ){echo 'smsd-active';}?>"><a class="tab-item" href="#<?php echo $id;?>"><?php _e(SMSD_Admin::$lists_slider[$id],"smslider");?></a></li>
					<?php
				}
				?>
					<li><a class="tab-item btn-disabled" href="#"><?php _e('Add more', 'smslider');?></a></li>
				</ul>
				<div id="smsd-tab-content">
					<?php foreach( $lists_slider as $id=>$slider ){
						$temp = $id == $s_slide ? 'active' : 'standard';
						$smsdslider = SMSlider::get_libs( $id );
						if( $smsdslider ){
							self::view("temp-{$temp}-slider", compact('id','slider','smsdslider', 's_custom_css'));
						}
					}
					?>
				</div>
                <input type="hidden" name="settings[slide]" value="<?php if( $s_slide ){echo $s_slide;}else{echo 'bxslider';}?>">
            </div>
		</div>
	</td>
</tr>
<tr>
	<td>
	<?php
		submit_button( 'Update', 'primary', '', false, array('id'=>'sm-submit') );
		$url = array('slide'=> $slide->ID, 'action'=>'edit' );
		if( SMSD_Admin::$copy ){
			$url['clone'] = SMSD_Admin::$copy;
		}
		$url = add_query_arg( $url, SMSD_ADMIN_URL );
	?>
	<a href="<?php echo $url;?>" title="Edit slide" class="button submit button-secondary">Edit slide</a>
	</td>
</tr>
</table>