<?php $slide = self::get_slide();?>
<input type="hidden" name="slide_id" value="<?php echo $slide->ID;?>">
<input type="hidden" name="slide_alias" value="<?php echo $slide->alias;?>">
<?php if( SMSD_Admin::$copy ):?>
    <input type="hidden" name="clone" value="<?php echo SMSD_Admin::$copy;?>">
<?php endif;