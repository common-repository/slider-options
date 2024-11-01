<td class="col-1">
    <div class="thumb video" style="background-image: url('<?php echo $post_details['img'];?>')">
        <a data-target="edit-<?php echo $sm_post;?>" title="Edit image slide" class="sm-edit alignright dashicons">Edit image slide</a>
    	<a data-target="del-<?php echo $sm_post;?>" title="Delete slide" class="sm-trash dashicons">Delete slide</a>
    	<a data-target="copy-<?php echo $sm_post;?>" title="Clone slide" class="sm-clone dashicons">Clone slide</a>
    </div>
</td>
<td class="col-2">
    <div class="smsd-tabs smsd-tab-video">
        <ul class="category-tabs">
            <li><a class="tab-item" href="#image-smsd-<?php echo $n;?>"><?php _e( 'Image', 'smslider' );?></a></li>
            <li><a class="tab-item" href="#video-smsd-<?php echo $n;?>"><?php _e( 'Video', 'smslider' );?></a></li>
        </ul>
        <div id="image-smsd-<?php echo $n;?>" class="tabs-panel media-detail">
            <div class="image-wrap">
                <p>
                    <label for="caption"><?php _e( 'Image Caption', 'smslider');?></label>
                    <textarea class="widefat" disabled name="<?php echo $post_details['image'];?>[caption]" placeholder="Image caption here"></textarea>
                </p>
                <p class="sm-type-url">
                    <label for="url"><?php _e( 'Image Url', 'smslider');?></label>
                    <input class="widefat" disabled name="<?php echo $post_details['image'];?>[url]" type="text" placeholder="Image URL here" value="">
                    <input type="checkbox" name="<?php echo $post_details['image'];?>[target]" title="<?php _e( 'Open new tab','smslider'); ?>" value="1">
                </p>
                <a class="sm-more"><?php _e( 'More', 'smslider');?></a>
                <div class="sm-more-seo hidden">
                    <p>
                        <label><?php _e( 'Image Title Text', 'smslider');?></label>
                        <input class="widefat" disabled name="<?php echo $post_details['image'];?>[title]" type="text" size="50" placeholder="Title here" value="">
                    </p>
                    <p>
                        <label><?php _e( 'Image Alt Text', 'smslider');?></label>
                        <input class="widefat" disabled name="<?php echo $post_details['image'];?>[alt]" type="text" placeholder="Your alt here" size="50" value="">
                    </p>
                </div>
            </div>
        </div>
        <div id="video-smsd-<?php echo $n;?>" class="tabs-panel media-detail">
            <div class="image-wrap">
                <p>
                    <label for="caption"><?php _e( 'Video link', 'smslider');?></label>
                    <textarea class="widefat" name="<?php echo $post_details['video'];?>[caption]" placeholder="Video link..."><?php echo esc_textarea( get_the_excerpt());?></textarea>
                </p>
                <p class="sm-type-url">
                    <label for="url"><?php _e( 'Video url', 'smslider');?></label>
                    <input class="widefat" name="<?php echo $post_details['video'];?>[url]" type="text" placeholder="URL..." value="<?php if( $post_details['permalink'] != '#smsd-link'){echo esc_url( $post_details['permalink'] );}?>">
                    <input type="checkbox" <?php checked( $post_details['target'], 1 );?> name="<?php echo $post_details['video'];?>[target]" title="<?php _e( 'Open new tab','smslider'); ?>" value="1">
                </p>
                <a class="sm-more"><?php _e( 'More', 'smslider');?></a>
                <div class="sm-more-seo hidden">
                    <p>
                        <label><?php _e( 'Video title', 'smslider');?></label>
                        <input class="widefat" name="<?php echo $post_details['video'];?>[title]" type="text" size="50" placeholder="Title..." value="<?php the_title();?>">
                    </p>
                    <p>
                        <label><?php _e( 'Video description', 'smslider');?></label>
                        <input class="widefat" name="<?php echo $post_details['video'];?>[alt]" type="text" placeholder="Description..." size="50" value="<?php echo esc_attr( get_the_content() );?>">
                    </p>
                </div>
            </div>
        </div>
    </div>
    <input class="smsd-pos-id" type="hidden" name="<?php echo $post_details['video'];?>[post_id]" value="<?php the_ID();?>">
</td>