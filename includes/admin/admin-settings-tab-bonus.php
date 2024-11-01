<?php
/**
 * Admin View: Settings tab "Bonus"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<h3><?php echo __('Lightbox Arrows','wp-flow-plus-addons'); ?></h3>
<p><?php echo __('Replace the default Lightbox arrow image with CSS arrows','wp-flow-plus-addons'); ?></p>
<table class="form-table">
<tr>
	<th scope="row">
	<label for="wpimageflow2_arrowstyle"><?php echo __('Lightbox arrow style', 'wp-flow-plus-addons'); ?></label>
	</th>
	<td>
	<select name="wpimageflow2_arrowstyle">
	<option value="image"<?php if ($options['arrowstyle'] == 'image') echo ' SELECTED'; echo '>' . __('Image', 'wp-flow-plus'); ?></option>
	<option value="icon-open"<?php if ($options['arrowstyle'] == 'icon-open') echo ' SELECTED'; echo '>' . __('Open Arrow', 'wp-flow-plus'); ?></option>
	<option value="icon-solid"<?php if ($options['arrowstyle'] == 'icon-solid') echo ' SELECTED'; echo '>' . __('Solid Arrow', 'wp-flow-plus'); ?></option>
	</select>
	</td>
</tr>
<tr>
	<th scope="row">
	<label for="wpimageflow2_arrowc"><?php echo __('Lightbox arrow color', 'wp-flow-plus-addons'); ?></label>
	</th>
	<td>
	<input type="text" name="wpimageflow2_arrowc" id="wpimageflow2_arrowc" class="wpif2-color-field" value="<?php echo $options['arrowcolor']; ?>">
	</td>
</tr>
</table>

<h3><?php echo __('Carousel Options','wp-flow-plus-addons'); ?></h3>
<table class="form-table">
<tr>
	<th scope="row">
	<label for="wpimageflow2_circular"><?php _e('Circular carousel', 'wp-flow-plus-addons'); ?>: </label>
	</th>
	<td> 
	<input type="checkbox" name="wpimageflow2_circular" id="wpimageflow2_circular" value="circular" <?php if ($options['circular'] == 'true') echo ' CHECKED'; ?> />
	</td>
</tr>
<tr>
	<th scope="row">
	<label for="wpimageflow2_slide_up"><?php _e('Slide-up captions', 'wp-flow-plus-addons'); ?>: </label>
	</th>
	<td> 
	<input type="checkbox" name="wpimageflow2_slide_up" id="wpimageflow2_slide_up" value="slide_up" <?php if ($options['slide_up'] == 'true') echo ' CHECKED'; ?> />
	</td>
</tr>
<tr>
	<th scope="row">
	<label for="wpimageflow2_focus"><?php _e('Focus', 'wp-flow-plus-addons'); ?>: </label>
	</th>
	<td> 
	<input type="text" name="wpimageflow2_focus" id="wpimageflow2_focus" value="<?php echo $options['focus']; ?>">
	<br /><em><?php _e('How many images to show each side of the center image, from 1 to 4, default 4. You need at least 2 focussed images for a circular carousel.', 'wp-flow-plus-addons'); ?></em>
	</td>
</tr>
</table>

<p class="submit"><input class="button button-primary" name="submit" value="<?php echo __('Save Changes','wp-flow-plus-addons'); ?>" type="submit" /></p>

<?php
include "admin-settings-promo.php";
?>