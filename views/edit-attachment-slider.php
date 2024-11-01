<?php
self::view('hidden-form');
$s_page = '';
$slide = self::get_slide();
$settings = $slide->params->setting;

$sizes = self::get_image_size( $slide->params->size );

$sm_uniq = SMSD_Admin::get_uniq();

$alias = SMSD_Admin::$copy ? SMSD_Admin::$copy : $slide->alias;

$q = self::get_edit_slide( $alias );
?>
<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
        <div id="smsd-toolbar" data-added="false" class="smsd-fixed hidden">
            <h3><?php _e("SM Slider","smslider");?></h3>
            <div id="major-publishing" class="aliginright">
            </div>
            <a href="#" class="insert-media-button button alignright" title="<?php _e('Add Slide','smslider');?>"><i class="dashicons dashicons-plus"></i></a>
        </div>
        <div id="post-body-content" style="position: relative;">
            <div id="postdivrich">
                <table class="widefat">
                    <thead id="smsd-tbhead">
                        <tr>
                            <th style="width: 164px;">
                                <h3><?php _e("SM Slider", "smslider");?></h3>
                            </th>
                            <th class="sm-head"><a href="#" id="sm-att-undo" data-range="" class="disabled dashicons dashicons-undo"></a>
                                <a href="#" id="insert-media-button" class="insert-media-button button alignright" title="<?php _e( 'Add Slide', 'smslider');?>"><i class="dashicons dashicons-plus"></i></a>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="sm-mangslide" class="sm-ui-sortable">
                        <tr id="slide-empty" class="slide-empty hidden"><td colspan="2"><?php _e('No slider found!', 'smslider');?></td></tr>
                        <?php if( $q->have_posts() ):
                            $n = 1;
                            while( $q->have_posts() ): $q->the_post();
                            global $post;
                            $post_id = $post->ID;
                            $type = self::get_post_meta( $post_id, 'type' );
                            $type = $type==2 ? 'video' : 'image';
                            $sm_post = $sm_uniq . $post_id;
                            $$type = SMSD_Admin::render_name( $type, $sm_post, $n);
                            $post_details = array(
                                'img'=>smsd_get_thumbnail('thumbnail'),
                                'permalink'=>esc_url( $post->guid ),
                                'target'=>self::get_post_meta( $post_id, 'target' ),
                                'image'=>SMSD_Admin::render_name( 'image', $sm_post, $n),
                                'video'=>SMSD_Admin::render_name( 'video', $sm_post, $n)
                            );
                        ?>
                        <tr id="slide-<?php echo $n;?>" class="slide">
                        <?php self::view( "temp-{$type}-smsd", compact('post_details', 'post_id','n','sm_post') );?>
                        </tr>
                        <?php $n++; endwhile;wp_reset_postdata();
                            else:
                            $sm_att_name = SMSD_Admin::render_name("image","{$sm_uniq}0",1);
                            $sm_vi_name = SMSD_Admin::render_name("video","{$sm_uniq}0",1);
                        ?>
                        <tr id="slide-1" class="slide sm-default">
                            <td class="col-1">
                                <div class="thumb" style="background-image: url('<?php echo smsd_get_no_image( array(150, 150), 'Image Slide');?>')">
                                    <a data-target="edit-<?php echo $sm_uniq;?>0" title="Edit image slide" class="sm-edit alignright dashicons"><?php _e("Edit image slide","smslider");?></a>
                                    <a data-target="del-<?php echo $sm_uniq;?>0" title="Delete slide" class="sm-trash dashicons"><?php _e("Delete slide","smslider");?></a>
                                    <a data-target="copy-<?php echo $sm_uniq;?>0" title="Clone slide" class="sm-clone dashicons"><?php _e("Clone slide","smslider");?></a>
                                </div>
                            </td>
                            <td class="col-2">
                                <div class="smsd-tab-image smsd-tabs">
                                    <ul class="category-tabs">
                                        <li><a class="tab-item" href="#image-smsd-1"><?php _e("Image","smslider");?></a></li>
                                        <li><a class="tab-item" href="#video-smsd-1"><?php _e("Video","smslider");?></a></li>
                                    </ul>
                                    <div id="image-smsd-1" class="tabs-panel media-detail">
                                        <div class="image-wrap">
                                            <p>
                                                <label for="caption"><?php _e( 'Image caption', 'smslider');?></label>
                                                <textarea class="widefat" name="<?php echo $sm_att_name;?>[caption]" placeholder="<?php _e('Caption...','smslider');?>"></textarea>
                                            </p>
                                            <p class="sm-type-url">
                                                <label for="caption"><?php _e( 'Link text', 'smslider');?></label>
                                                <input class="widefat" name="<?php echo $sm_att_name;?>[url]" type="text" placeholder="<?php _e('URL...','smslider');?>" value="">
                                                <input type="checkbox" name="<?php echo $sm_att_name;?>[target]" value="1">
                                            </p>
                                            <a class="sm-more"><?php _e( 'More', 'smslider');?></a>
                                            <div class="sm-more-seo hidden">
                                                <p>
                                                    <label><?php _e( 'Image title', 'smslider');?></label>
                                                    <input class="widefat" name="<?php echo $sm_att_name;?>[title]" type="text" size="50" placeholder="<?php _e('Title...','smslider');?>" value="">
                                                </p>
                                                <p>
                                                    <label><?php _e( 'Image alt', 'smslider');?></label>
                                                    <input class="widefat" name="<?php echo $sm_att_name;?>[alt]" type="text" placeholder="<?php _e('Alt...','smslider');?>" size="50" value="">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="video-smsd-1" class="tabs-panel media-detail">
                                        <div class="image-wrap">
                                            <p>
                                                <label for="caption"><?php _e( 'Video link', 'smslider');?></label>
                                                <textarea class="widefat" disabled name="<?php echo $sm_vi_name;?>[caption]" placeholder="<?php _e('Video link...','smslider');?>"></textarea>
                                            </p>
                                            <p class="sm-type-url">
                                                <label for="url"><?php _e( 'Link text', 'smslider');?></label>
                                                <input class="widefat" name="<?php echo $sm_vi_name;?>[url]" type="text" disabled placeholder="<?php _e('URL...','smslider');?>" value="">
                                                <input type="checkbox" name="<?php echo $sm_vi_name;?>[target]" value="1">
                                            </p>
                                            <a class="sm-more"><?php _e( 'More', 'smslider');?></a>
                                            <div class="sm-more-seo hidden">
                                                <p>
                                                    <label><?php _e( 'Video title', 'smslider');?></label>
                                                    <input class="widefat" name="<?php echo $sm_vi_name;?>[title]" type="text" disabled size="50" placeholder="<?php _e('Title...','smslider');?>" value="">
                                                </p>
                                                <p>
                                                    <label for="caption"><?php _e( 'Video description', 'smslider');?></label>
                                                    <textarea class="widefat" disabled name="<?php echo $sm_vi_name;?>[alt]" disabled placeholder="<?php _e('Description...','smslider');?>"></textarea>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                           </td>
                        </tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>

        </div>
        <!-- /post-body-content -->

        <div id="postbox-container-1" class="postbox-container">
            <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
                <div id="submitdiv" class="postbox ">
                    <h3 class="hndle ui-sortable-handle"><span><?php _e('Save &amp; Preview', 'smslider');?></span></h3>
                    <div class="inside">
                        <div class="submitbox" id="submitpost">
                            <div id="major-publishing-actions">
                                <div id="delete-action">
                                    <a class="preview button" href="#" id="post-preview"><?php _e('Preview','smslider');?></a>
                                    <input type="hidden" name="wp-preview" id="wp-preview" value="">
                                </div>

                                <div id="publishing-action">
                                    <a class="button button-secondary" href="<?php echo esc_url( add_query_arg( array('slide'=>$slide->ID, 'action'=>'setting'), SMSD_ADMIN_URL) );?>"><?php _e('Setting','smslider');?></a>
                                    <input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<?php _e('Save','smslider');?>">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div id="formatdiv" class="postbox ">
                    <div class="handlediv" title="Click to toggle">
                        <br>
                    </div>
                    <h3 class="hndle ui-sortable-handle"><span><?php _e('Setting for your loop','smslider');?></span></h3>
                    <div id="setting-loop" class="inside">
                        <div class="smsd-setting-tabs smsd-tabs">
                            <ul class="category-tabs">
                                <li><a href="#image-code"><?php _e('Image','smslider');?></a></li>
                                <li><a href="#video-code"><?php _e('Video','smslider');?></a></li>
                            </ul>
                            <div id="image-code" class="tabs-panel">
                                <div class="sm-toolbar">
                                    <a id="sm-caption" href="#"><?php _e('CAPTION', 'smslider');?></a><a href="#" id="sm-title"><?php _e('TITLE','smslider');?></a><a id="sm-alt" href="#"><?php _e('ALT','smslider');?></a><a href="#" id="sm-src"><?php _e('SRC','smslider');?></a>
                                </div>
                                <textarea class="smsd-setting widefat" rows="6" name="setting-image"><?php if( isset( $settings->s_image ) ){ echo esc_textarea( $settings->s_image ); }else{ echo SMSD_Public::default_image();}?></textarea>
                            </div>
                            <div id="video-code" class="tabs-panel">
                                <div class="sm-toolbar">
                                    <a id="sm-video" href="#"><?php _e('VIDEO', 'smslider');?></a><a href="#" id="sm-title"><?php _e('TITLE','smslider');?></a><a id="sm-description" href="#"><?php _e('DESCRIPTION','smslider');?></a>
                                </div>
                                <textarea class="smsd-setting widefat" rows="6" name="setting-video"><?php if( isset( $settings->s_video ) ){ echo esc_textarea( $settings->s_video ); }else{ echo SMSD_Public::default_video();}?></textarea>
                            </div>
                            <input type="hidden" id="sm-params" name="params" value="0">
                        </div>
                        <label>
                            <?php $empty_page = empty( $settings->s_page );?>
                            <input <?php if( !$empty_page ){ echo 'checked'; }?> id="sm-has-page" disabled type="checkbox" value=""> Has thumbnail
                        </label><br/>
                        <div id="sm-has-thumnbail" <?php if( $empty_page ){ echo 'style="display:none"' ;}?>>
                            <label><?php _e('Choose thumbnail size','smslider');?></label><br/>
                            <input type="text" disabled id="setting-thumb" <?php if( $empty_page ){echo 'disabled';}?> placeholder="<?php _e('Thumnbail or 150x150', 'smslider');?>" name="setting-page" value="<?php echo $s_page;?>">
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
                <div class="postbox ">
                    <div class="handlediv" title="Click to toggle">
                        <br>
                    </div>
                    <h3 class="hndle ui-sortable-handle"><span><?php _e('Usage','smslider');?></span></h3>
                    <div class="inside">
                        <div class="smsd-setting-tabs smsd-tabs">
                            <ul class="category-tabs">
                                <li><a href="#shortcode"><?php _e('Shortcode','smslider');?></a></li>
                                <li><a href="#php-code"><?php _e('PHP code','smslider');?></a></li>
                            </ul>
                            <div id="shortcode" class="tabs-panel">
                                <strong>[smslider <?php echo $alias;?>]</strong>
                            </div>
                            <div id="php-code" class="tabs-panel">
                                <strong>&lt;?php echo smsd_get_slider( '<?php echo $alias;?>' ); ?&gt;</strong>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="postbox ">
                    <div class="handlediv" title="Click to toggle">
                        <br>
                    </div>
                    <h3 class="hndle ui-sortable-handle"><span><?php _e('Is <strong><a href="https://wordpress.org/plugins/slider-options" target="_blank">Slider Options</a></strong> useful?','smslider');?></span></h3>
                    <div class="inside">
                        <p  style="font-size:16px;text-align: center"><?php _e('If <strong><a href="https://wordpress.org/plugins/slider-options" target="_blank">Slider Options</a></strong> is useful for you, can you make a little time to');?> <a href="https://wordpress.org/support/view/plugin-reviews/slider-options" target="_blank"><?php _e( 'vote' );?></a> <?php _e( 'it <br/>Thanks!'); ?></a></p>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- /post-body -->
    <div class="clear"></div>
    <div id="sm-preview">
        <div class="sm-inner" style="width:<?php echo intval( $sizes[0] );?>px;height: <?php echo intval( $sizes[1] );?>px">
            <?php echo smsd_get_slider( $alias );?>
        </div>
        <button type="button" id="close-preview">X</button>
    </div>
</div>
<?php
    $sm_att_name = SMSD_Admin::render_name('image','smsd_id', 'smsd_i');
    $sm_vi_name = SMSD_Admin::render_name('video','smsd_id', 'smsd_i');
?>
<script type="text/javascript">
    var smsd_temp = '<td class="col-1"> <div class="thumb" style="background-image: url(smsd_url);"> <a data-target="edit-smsd_id" title="<?php _e( "Edit image slide","smslider" );?>" class="sm-edit alignright dashicons-edit dashicons"> <?php _e( "Edit image slide", "smslider");?> </a> <a data-target="del-smsd_id" title="<?php _e( "Delete slide", "smslider" );?>" class="sm-trash dashicons dashicons-trash"> <?php _e( "Delete slide", "smslider" );?> </a> <a data-target="copy-smsd_id" title="<?php _e( "Clone slide", "smslider" );?>" class="sm-clone dashicons-admin-page dashicons"> <?php _e( "Clone slide", "smslider" );?> </a> </div> </td> <td class="col-2"> <div class="smsd-type-tabs smsd-tabs smsd-tab-video"> <ul class="category-tabs"> <li><a class="tab-item" href="#image-smsd-smsd_i"><?php _e( "Image", "smslider" );?></a></li> <li><a class="tab-item" href="#video-smsd-smsd_i"><?php _e( "Video", "smslider" );?></a></li> </ul> <div id="image-smsd-smsd_i" class="tabs-panel media-detail"> <div class="image-wrap"> <p> <label for="caption"><?php _e( "Image Caption", "smslider" );?></label> <textarea class="widefat" name="<?php echo $sm_att_name;?>[caption]" placeholder="<?php _e( "Caption...", "smslider" );?>">smsd_caption</textarea> </p> <p class="sm-type-url"> <label for="url"><?php _e( "Image Url", "smslider" );?></label> <input class="widefat" type=text name="<?php echo $sm_att_name;?>[url]" placeholder="<?php _e( "URL...", "smslider");?>"> <input type=checkbox name="<?php echo $sm_att_name;?>[target]" value="1" title="<?php _e( "Open new tab", "smslider");?>"> </p> <a class="sm-more"> <?php _e("More","smslider");?> </a> <div class="sm-more-seo hidden"> <p> <label> <?php _e( "Image Title Text", "smslider");?> </label> <input class="widefat" type="text" size="50" name="<?php echo $sm_att_name;?>[title]" placeholder="<?php _e( "Title...", "smslider");?>" value="smsd_title"> </p> <p> <label> <?php _e( "Image Alt Text", "smslider");?> </label> <input class="widefat" type="text" size="50" name="<?php echo $sm_att_name;?>[alt]" placeholder="<?php _e( "Alt...", "smslider");?>" value="smsd_alt"> </p> </div> </div> </div> <div id="video-smsd-smsd_i" class="tabs-panel media-detail"> <div class="image-wrap"> <p> <label for="video"> <?php _e( "Video link", "smslider");?> </label> <textarea class="widefat" disabled name="<?php echo $sm_vi_name;?>[video]" placeholder="<?php _e("Video link... ","smslider");?>"></textarea> </p> <p class="sm-type-url"> <label for="url"> <?php _e( "Link text", "smslider" );?> </label> <input class="widefat" type="text" disabled name="<?php echo $sm_vi_name;?>[url] " placeholder="<?php _e( "URL...", "smslider");?>"> <input type=checkbox name="<?php echo $sm_vi_name;?>[target]" value="1" title="<?php _e( "Open new tab", "smslider");?>"> </p> <a class="sm-more"> <?php _e("More","smslider");?> </a> <div class="sm-more-seo hidden"> <p> <label> <?php _e( "Video title", "smslider");?> </label> <input class="widefat" type="text" disabled size="50" name="<?php echo $sm_vi_name;?>[title]" placeholder="<?php _e( "Title...", "smslider");?>"> </p> <p> <label> <?php _e( "Video description", "smslider");?> </label> <textarea class="widefat" disabled name="<?php echo $sm_vi_name;?>[desc]" placeholder="<?php _e("Description... ","smslider");?>"></textarea> </p> </div> </div> </div> </td>';
</script>
