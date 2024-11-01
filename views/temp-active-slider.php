<div id="<?php echo $id;?>" class="tabs-active tabs-panel">
    <div class="tab-tool">
    
    	<div class="sm-col-3 col-left sm-dragable">
    		 <?php
                $smsd_ops = array();
                $slide = self::get_slide();

				foreach( $smsdslider as $slider ){
					$ids = $slider->id;
					?>
					<div class="smop-box">
            			<h3><?php _e($slider->name,'smslider');?></h3>
            			<div id="<?php echo "$id-"; echo $ids;?>" class="smop-item">
            				<select class="smop-select widefat" multiple>
            					<?php
            					foreach( $slider->fields as $field ){
            						$name = esc_attr( $field->name );
            						$set = $field->set;
                                    $desc = '';
                                    if( isset( $set->desc ) ){
                                        $desc = 'data-desc="'. strip_tags( $set->desc, '<br>' ) .'"';
                                    }
            						$disabled = '';
                                    $smsd_visibledis = true;
                                    if( isset( $set->disabled ) ){
                                        $disabled = 'disabled';
                                    }
            						if( isset( $slide->params->options->{$name} ) ){
            							$disabled = 'disabled';
                                        $smsd_ops[$name]['id'] = $id;
            							$smsd_ops[$name]['type'] = $set->type;
            							$smsd_ops[$name]['parent'] = $ids;
            							$smsd_ops[$name]['default'] = $slide->params->options->{$name};
            							if( isset( $set->options ) ){
            								$smsd_ops[$name]['options'] = $set->options;
            							}
            						}
                                    if( $smsd_visibledis ):
            					?>
            					<option data-type="<?php echo esc_attr( $set->type );?>" <?php if( $set->type === 'select' ){ echo 'data-value="'. implode( ',', $set->options ) .'"';}?> data-default="<?php echo esc_attr( $set->default );?>" value="<?php echo $name;?>" <?php echo $disabled;?> <?php echo $desc;?>><?php _e( $name, 'smslider');?></option>
            					<?php endif; };?>
            				</select>
            			</div>
            		</div>
					<?php
				}
    		?>                    		
    	</div>
    	<div id="" class="sm-field-ops sm-col-7 col-right">

    	<?php $smsd_fields = SMSD_Admin::render_element( $smsd_ops, 1 ); ?>
    		<div class="sm-col-5 col-left sm-dropable">
    		<?php if( isset( $smsd_fields['select'] ) ){
    			echo $smsd_fields['select'];
    		}else{?>
    			<span class="empty-default"><?php _e("I'm hungry, help me. ;)","smslider");?></span>
    		<?php };?>
    		</div>
    		<div class="sm-col-5 col-right sm-dropable">
    			<?php if( isset( $smsd_fields['text'] ) ){
        			echo $smsd_fields['text'];
        		}else{?>
        			<span class="empty-default"><?php _e("Nice to meet you!","smslider");?></span>
        		<?php };?>
    		</div>
    		<div class="sm-col-10 sm-dropable">
        		<?php if( isset( $smsd_fields['textarea'] ) ){
        			echo $smsd_fields['textarea'];
        		}else{?>
        			<span class="empty-default"><?php _e("This area is very useful for callback(textarea)","smslider");?></span>
        		<?php };?>
    		</div>
            <div class="sm-col-10">
                <h4><?php _e('Custom your style here:', 'smslider' );?></h4>
                <textarea class="widefat smsd-setting" name="settings[custom_css]" placeholder="<?php _e('Custom style');?>"><?php echo esc_attr( $s_custom_css );?></textarea>
            </div>
    	</div>
    </div>
</div> 
