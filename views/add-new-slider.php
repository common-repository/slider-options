<table class="form-table">
<tr valign="top">
	<th scope="row"><label for="smsd-name"><?php _e('Slider name','smslider');?></label></th>
	<td><input type="text" id="smsd-name" data-act="add-new"  class="smsd-name" maxlength="100" size="25" name="smsd-name"></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="smsd-alias"><?php _e('Alias name','smslider');?></label></th>
	<td>
		<input type="text" id="smsd-alias" data-act="add-new" maxlength="100" size="25" name="smsd-alias">
		<p class="description"><?php _e("It must unique, we'll use it as shortcode id.","smslider");?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="type"><?php _e("Type slider","smslider");?></label></th>
	<td>
		<select name="settings[type]" class="sm-max-210">
			<?php foreach( SMSD_Admin::$slide_type as $v=>$type ): ?>
				<option value="<?php echo $v;?>" <?php if( $v  !== 'attachment' ) echo 'disabled';?>><?php echo $type;?></option>
			<?php endforeach;?>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label><?php _e("Slider Options","smslider");?></label></th>
	<td>
		<div class="sm-max-50 clearfix">
			<div class="sm-col-5">
				<label for="smsd-wrap-type"><?php _e("Style slider type","smslider");?></label>
				<select name="settings[wrap]" class="sm-max-210">
					<option value="ul">List</option>
					<option value="div">Div</option>
				</select>
			</div>
			<div class="sm-col-5">
				<div id="sm-has-thumnbail">
                    <label><?php _e('Choose image size','smslider');?></label><br/>
                    <input type="text" id="setting-thumb" placeholder="<?php _e('Large or 900x500', 'smslider');?>" name="size" value="">
                    <div class="sm-relative">
	                    <ul class="list-thumb">
	                        <?php $thumbs = get_intermediate_image_sizes();

	                        foreach( $thumbs as $size):
	                        	list( $wid, $hei ) = self::get_image_size( $size );
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
			<?php
				$defaults = 'bxslider';
				$lists_slider = (array)self::get_option('types');
				$i = 0;
				?>
				<ul id="smsd-tab-slide" class="category-tabs">
				<?php foreach( $lists_slider as $id=>$src ){
					if( $i === 0 ){
						$defaults = $id;
					}
					?>
	                    <li><a class="tab-item" href="#<?php echo $id;?>"><?php _e(SMSD_Admin::$lists_slider[$id],"smslider");?></a></li>
					<?php
					$i++;
				}
				?>
					<li><a class="tab-item btn-disabled" href="#"><?php _e('Add more', 'smslider');?></a></li>
				</ul>
				<?php foreach( $lists_slider as $id=>$slider ){
					$smsdslider = self::get_libs( $id );
					if( $smsdslider ){
						self::view("temp-standard-slider", compact('id','slider','smsdslider'));
					}
				}
				?>
                <input type="hidden" name="settings[slide]" value="<?php echo $defaults;?>">
            </div>
		</div>
	</td>
</tr>
<tr valign="top">
	<td>
	<?php
		submit_button( 'Add New', 'primary', '', false, array('id'=>'sm-submit') );
	?>
	</td>
</tr>
<style type="text/css" media="screen">
	#adminmenu #toplevel_page_sm-slider .wp-submenu a{
		color:rgba(240,245,250,.7);
	}
	#adminmenu #toplevel_page_sm-slider .wp-submenu a:hover{
		color:#00b9eb;
	}
	#adminmenu #toplevel_page_sm-slider .wp-submenu li:nth-child(3) a{
		color:#fff;
	}
</style>
</table>