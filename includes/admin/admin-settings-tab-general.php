<?php
/**
 * Admin View: Settings tab "General"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<h3><?php echo __('Options for all Galleries','wp-imageflow2'); ?></h3>
<p><?php _e('The images in the carousel will by default link to a Lightbox enlargement of the image. Alternatively, you may specify
a URL to link to each image from the Media Library. This link address should be configured in the image uploader/editor of the Media Library.', 'wp-imageflow2'); ?></p>
<table class="form-table">
<tr>
	<th scope="row">
	<?php echo __('Open URL links in same window', 'wp-imageflow2'); ?>
	</th>
	<td>
	<input type="checkbox" name="wpimageflow2_samewindow" value="same" <?php if ($options['samewindow'] == 'true') echo ' CHECKED'; ?> /> 
		<em><?php _e('The default is to open links in a new window', 'wp-imageflow2'); ?></em>
	</td>
</tr>
<tr>
	<th scope="row">
	<?php echo __('Enable reflections', 'wp-imageflow2'); ?>
	</th>
	<td>
	<input type="radio" name="wpimageflow2_reflect" value="CSS" <?php if ($options['reflect'] == 'CSS') echo ' CHECKED'; ?> />
	<?php echo __('Enable CSS reflections, supported by all modern browsers (including IE8+).', 'wp-imageflow2'); ?>
	<br />
	<input type="radio" name="wpimageflow2_reflect" value="none" <?php if ($options['reflect'] == 'none') echo ' CHECKED'; ?> />
	<?php echo __('Disable reflections', 'wp-imageflow2'); ?>
	<br />
	</td>
</tr>
<tr>
	<th scope="row">
	<?php echo __('Enable auto rotation', 'wp-imageflow2'); ?>
	</th>
	<td>
	<input type="checkbox" name="wpimageflow2_autorotate" value="autorotate" <?php if ($options['autorotate'] == 'on') echo ' CHECKED'; ?> /> 
		<em><?php _e('This may be overridden in the shortcode', 'wp-imageflow2'); ?></em>
	</td>
</tr>
<tr>
	<th scope="row">
	<?php echo __('Auto rotation pause', 'wp-imageflow2'); ?>
	</th>
	<td>
	<input type="text" name="wpimageflow2_pause" value="<?php echo esc_html($options['pause']); ?>"> 
	</td>
</tr>
</table>

<h3><?php echo __('Galleries Based on Folders','wp-imageflow2'); ?></h3>
<a style="cursor:pointer;" title="Click for help" onclick="toggleVisibility('detailed_display_tip');"><?php _e('Click to toggle detailed help', 'wp-imageflow2'); ?></a>
<div id="detailed_display_tip" style="display:none; width: 600px; background-color: #eee; padding: 8px;
border: 1px solid #aaa; margin: 20px; box-shadow: rgb(51, 51, 51) 2px 2px 8px;">
<p<?php _e('>You can upload a collection of images to a folder and have WP Flow Plus read the folder and gather the images, without the need to upload through the Wordpress image uploader. Using this method provides fewer features in the gallery since there are no titles, links, or descriptions stored with the images. This is provided as a quick and easy way to display an image carousel.', 'wp-imageflow2'); ?></p>
<p><?php _e('The folder structure should resemble the following', 'wp-imageflow2'); ?>:</p>
<p>
- wp-content<br />
&nbsp;&nbsp;&nbsp;- galleries<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- gallery1<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- image1.jpg<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- image2.jpg<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- image3.jpg<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- gallery2<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- image4.jpg<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- image5.jpg<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- image6.jpg</p>

<p><?php _e('With this structure you would enter "wp-content/galleries/" as the folder path below', 'wp-imageflow2'); ?>.</p>
</div>

<table class="form-table">
	<tr>
	<th scope="row">
	<?php _e('Folder Path','wp-imageflow2'); ?>	
	</th>
	<td>
	<?php _e('This should be the path to galleries from homepage root path, or full url including http://.','wp-imageflow2'); ?>
	<br /><input type="text" size="35" name="wpimageflow2_path" value="<?php echo esc_html($options['gallery_url']); ?>">
	<br /><?php echo __('e.g.','wp-imageflow2'); ?> wp-content/galleries/
	<br /><?php echo __('Ending slash, but NO starting slash','wp-imageflow2'); ?>
	</td>
</tr>
	<tr>
	<th scope="row">
	<?php echo __('These folder galleries were found:','wp-imageflow2'); ?>	
	</th>
	<td>
	<?php
		$galleries_path = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . WPFlowPlus::get_path($options['gallery_url']);
		if (file_exists($galleries_path)) {
			$handle	= opendir($galleries_path);
			while ($dir=readdir($handle))
			{
				if ($dir != "." && $dir != "..")
				{									
					echo "[wp-flowplus dir=".$dir."]";
					echo "<br />";
				}
			}
			closedir($handle);								
		} else {
			echo "Gallery path doesn't exist " . $galleries_path;
		}					
	?>
	</td>
</tr>
</table>

<p class="submit"><input class="button button-primary" name="submit" value="<?php echo __('Save Changes','wp-imageflow2'); ?>" type="submit" /></p>

<?php
include "admin-settings-promo.php";
?>