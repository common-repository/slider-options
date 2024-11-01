<div id="<?php echo $id;?>" class="tabs-panel">
    <div class="tab-tool">
    	<div class="sm-col-3 col-left sm-dragable">
    		 <?php
				foreach( $smsdslider as $slider ){
					?>
					<div class="smop-box">
            			<h3><?php _e( $slider->name,'smslider' );?></h3>
            			<div id="<?php echo "$id-"; echo $slider->id;?>" class="smop-item">
            				<select class="smop-select widefat" multiple>
            					<?php

            					foreach( $slider->fields as $field ){
            						$name = esc_attr( $field->name );
            						$set = $field->set;
            						$disabled = '';
                                    $desc = '';
                                    if( isset( $set->desc ) ){
                                        $desc = 'data-desc="'. strip_tags( $set->desc, '<br>' ) .'"';
                                    }
            						if( isset( $set->disabled ) ){
                                        $disabled = 'disabled';
                                    }
            					?>
            					<option data-type="<?php echo esc_attr( $set->type );?>" <?php if( $set->type === 'select' ){ echo 'data-value="'. implode( ',', $set->options ) .'"';}?> data-default="<?php echo esc_attr( $set->default );?>" value="<?php echo $name;?>" <?php echo $disabled;?> <?php echo $desc;?>><?php _e( $name, 'smslider');?></option>
            					<?php };?>
            				</select>
            			</div>
            		</div>
					<?php
				}
    		?>                    		
    	</div>
    	<div class="sm-field-ops sm-col-7 col-right">
    		<div class="sm-col-5 col-left sm-dropable">
    			<span class="empty-default"><?php _e("I`m hungry, help me. ;)","smslider");?></span>
    		</div>
    		<div class="sm-col-5 col-right sm-dropable">
        		<span class="empty-default"><?php _e("Nice to meet you!","smslider");?></span>
    		</div>
    		<div class="sm-col-10 sm-dropable">
        		<span class="empty-default"><?php _e("This area is very useful for callback(textarea)","smslider");?></span>
    		</div>
    	</div>
    </div>
</div> 