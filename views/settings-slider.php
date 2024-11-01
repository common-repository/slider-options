<?php
	global $wp_roles;
	$types = (array)self::get_option( 'types' );
	$roles = self::get_option( 'manager');
 ?>
<table class="form-table">
<tr>
	<th scope="row"><label><?php _e('Choose type slider:','smslider');?></label></th>
	<td>
		<?php
			$libs_theme = SMSD_Admin::scandir( get_template_directory() .'/smslider' );
			$libs = SMSD_Admin::scandir( SMSD_ASSETS_DIR .'/libs', 'plug', $libs_theme );
			foreach( $libs as $lib=>$src ){
				$checked = isset( $types[ $lib ] );
				$selected = 1;
				if( $checked ){
					$type = self::get_option( $lib );

					if( isset( $type->all_page ) && $type->all_page == 0 ){
						$selected = 0;
					}
				}
				?>
				<div id="smsd-<?php echo $lib;?>" class="smsd-lib-type <?php if( $checked ){echo 'active'; }?> smsd-<?php echo $src;?>">
					<label>
						<input <?php if( $checked ){echo 'checked';};?> type="checkbox" name="type[<?php echo $src;?>][]" value="<?php echo $lib;?>">
						<?php echo ucfirst( $lib );?>
					</label>
					<p class="description">
						<label>
							<input type="radio" <?php checked( $selected, 1 );?> name="show-all[<?php echo $lib;?>]" value="1"> Add to all page (on header) | <input type="radio" name="show-all[<?php echo $lib;?>]" <?php checked( $selected, 0 );?> value="0"> Add only slide page
						</label>
					</p>
				</div>
				<?php
			}
		?>
	</td>
</tr>
<tr>
	<th scope="row"><label><?php _e('Role Manager:','smslider');?></label></th>
	<td>
		<div id="accordion">
			<?php
				foreach( $wp_roles->roles as $role=>$caps ):
				$cap = array();
				if( isset( $roles->$role ) ){
					$cap = $roles->$role;
				}
			?>
				<h3><?php echo esc_attr( $role, 'smslider');?></h3>
				<div class="smsd-table">
					<?php foreach( self::$caps as $c=>$v ):
						$enabled = in_array( $c, $cap );
					?>
					<p>
					<label for="<?php echo $c;?>"><?php echo esc_attr( $v, 'smslider');?></label>
					<select class="<?php echo $c;?>" name="roles[<?php echo $role;?>][<?php echo $c;?>]">
						<option <?php selected( true, $enabled );?> value="true"><?php _e('Enabled', 'smslider');?></option>
						<option <?php selected( false, $enabled ); ?> value="false"><?php _e('Disabled', 'smslider');?></option>
					</select>
					</p>
					<?php endforeach;?>
				</div>
			<?php endforeach; ?>
	  </div>
	</td>
</tr>
<tr>
	<td><?php
		submit_button( 'Update', 'primary', '', false, array('id'=>'sm-submit') );
	?></td>
</tr>
</table>
<style type="text/css" media="screen">
	#adminmenu #toplevel_page_sm-slider .wp-submenu a{
		color:rgba(240,245,250,.7);
	}
	#adminmenu #toplevel_page_sm-slider .wp-submenu a:hover{
		color:#00b9eb;
	}
	#adminmenu #toplevel_page_sm-slider .wp-submenu li:last-child a{
		color:#fff;
	}
</style>