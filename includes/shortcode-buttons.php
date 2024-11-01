<?php
/*
 ** WP Flow Plus Shortcode Buttons
 **
 ** Copyright Spiffy Plugins
 **
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (!class_exists("WPIF2Shortcode")) {

class WPFlowPlus_Shortcode {

	function __construct () {

		/* Add the shortcode handler to the admin page */
		add_action('media_buttons', array($this, 'add_encoder'));

		/* Add the scripts */
		add_action('admin_head', array ($this, 'add_scripts'));
	}

	function add_encoder(){
		/* Place the form in the footer */
		add_action('admin_footer', array ($this, 'add_form') );

		/* Add the button to the media buttons */
		echo '&nbsp;<a href="#TB_inline?width=640&height=847&inlineId=wpif2_sc_form" class="thickbox" id="add_wpif2_button" title="WP Flow Plus Shortcode"><img src="' . WPIF2_PLUGIN_URL . 'img/wpif2-16.png" alt="WP Flow Plus Shortcode" /></a>';
	}

	/*
	** Define the popup form
	*/
	function add_form() {
		global $wpimageflow2, $wpdb;
		
		$disabled = is_plugin_active('wp-imageflow2-addons/wp-imageflow2-addons.php')? '' : 'disabled';
?>

<style type="text/css">

#wpif2_sc_wrap h2.popup-header { background: url(<?php echo WPIF2_PLUGIN_URL; ?>/img/wpif2-32.png) top left no-repeat;
		padding: 8px 0 5px 36px; height: 32px; }
#wpif2_sc_wrap .hide { display: none; }
#wpif2_sc_wrap .button-primary { color: #fff !important; }

.wpif2_sc_section { margin-top: 20px; }
.wpif2_sc_section label { display: inline-block; width: 150px; }
.wpif2_sc_section span.wpif2_sc_caption { font-size: .85em; color: #666; font-style: italic; width: 270px;
		display: inline-block; vertical-align: top; padding-left: 10px; }

</style>

<div id="wpif2_sc_form" style="display:none;">
  <div id="wpif2_sc_wrap">
	<div class="wpif2-sc-header">
		<h2 class="popup-header"><?php _e( 'WP Flow Plus Shortcode Embed', 'wp-imageflow2' ); ?></h2>
	</div>

	<form id="wpif2_sc_form_element">
		<div class="wpif2-select">
			<label for="wpif2_sc_source"><?php _e( 'Where are the images coming from?', 'wp-imageflow2' ); ?></label>

			<select id="wpif2_sc_source">
					<option value="attached" SELECTED><?php _e( 'Attachments to a page/post', 'wp-imageflow2' ); ?></option>
					<option value="featured"><?php _e( 'Featured posts', 'wp-imageflow2' ); ?></option>
					<option value="folder"><?php _e( 'A subfolder', 'wp-imageflow2' ); ?></option>
					<option value="medialib"><?php _e( 'Media Library', 'wp-imageflow2' ); ?></option>
					<option value="mediatag"><?php _e( 'Media Tags collection', 'wp-imageflow2' ); ?></option>
					<option value="nextgen"><?php _e( 'NextGen Gallery', 'wp-imageflow2' ); ?></option>
				</select>
			</div>

			<div class="wpif2_sc_section wpif2_attached">
				<div>
					<label for="wpif2_sc_id"><?php _e('Page/Post ID', 'wp-imageflow2'); ?></label>
					<input type="text" name="wpif2_sc_id" value="" id="wpif2_sc_id" size="8" />
					<span class="wpif2_sc_caption"><?php _e('Default: The images attached to the current page/post', 'wp-imageflow2'); ?></span>
				</div>
			</div>

			<div class="wpif2_sc_section wpif2_medialib hide">
				<div>
					<span class="wpif2_sc_caption"><?php _e('Select the images to include below', 'wp-imageflow2'); ?></span>
				</div>
			</div>
			
			<div class="wpif2_sc_section wpif2_mediatags hide">
				<div>
					<label for="wpif2_sc_mediatag"><?php _e('Media Tag Slug', 'wp-imageflow2'); ?></label>
<?php
					if (function_exists('get_mediatags')) {
						$tags = get_mediatags();
?>
					<select id="wpif2_sc_mediatag" />
						<option value="" SELECTED></option>
<?php
						foreach($tags as $tag_item) {
							echo '<option value="' . $tag_item->name . '">' . $tag_item->name . '</option>
';
						}
						echo '<select>
';
					} else {
					echo '<div class="updated inline">';
					echo '<p>' . __('The <em>Media Tags</em> plugin is required for this option', 'wp-imageflow2') . '</p>';
					echo "</div>";
					}
?>
				</div>
			</div>

			<div class="wpif2_sc_section wpif2_featured hide">
				<div>
					<label for="wpif2_sc_featured"><?php _e('Select a category', 'wp-imageflow2'); ?></label>
					<select id="wpif2_sc_featured">
						<option value="" SELECTED></option>
<?php
						$categories = get_categories();

						if(is_array($categories)) {
							foreach($categories as $category) {
								echo '<option value="' . $category->cat_ID . '">' . $category->cat_name . '</option>
';
						      }
						}
?>
					</select>
				</div>
			</div>

			<div class="wpif2_sc_section wpif2_folder hide">
				<div>
					<label for="wpif2_sc_folder"><?php _e('Select a folder', 'wp-imageflow2'); ?></label>
					<select id="wpif2_sc_folder" />
						<option value="" SELECTED></option>
<?php
						$wp_options = $wpimageflow2->getAdminOptions();
						$galleries_path = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $wpimageflow2->get_path($wp_options['gallery_url']);
						if (file_exists($galleries_path)) {
							$handle	= opendir($galleries_path);
							while ($dir=readdir($handle))
							{
								if ($dir != "." && $dir != "..")
									echo '<option value="' . $dir . '">' . $dir . '</option>
';
							}
							closedir($handle);								
						} else {
							echo __('Gallery path doesn\'t exist', 'wp-imageflow2');
						}					
?>
					</select>
					<span class="wpif2_sc_caption"><?php _e('Configure the gallery path on the', 'wp-imageflow2'); ?> <a href="<?php echo admin_url('options-general.php?page=wpFlowPlus'); ?>"><?php _e('WP Flow Plus settings page', 'wp-imageflow2'); ?></a></span>
				</div>
			</div>

			<div class="wpif2_sc_section wpif2_nextgen hide">
				<div>
<?php				if (class_exists('nggLoader')) {
?>
					<label for="wpif2_sc_ngg_id"><?php _e('Select a gallery', 'wp-imageflow2'); ?></label>
					<select id="wpif2_sc_ngg_id">
						<option value="" SELECTED></option>
<?php
						$gallerylist = $wpdb->get_results("SELECT * FROM $wpdb->nggallery ORDER BY gid ASC");

						if(is_array($gallerylist)) {
							foreach($gallerylist as $gallery) {
								echo '<option value="' . $gallery->gid . '">' . $gallery->title . '</option>
';
						      }
						}
					echo '</select>
';
				  } else {
					echo '<div class="updated inline">';
					echo '<p>' . __('The <em>NextGen Gallery</em> plugin is required for this option', 'wp-imageflow2') . '</p>';
					echo '</div>';
				  }
?>
				</div>
			</div>

			<h3><?php _e('Optionally enter values below to override the default settings', 'wp-imageflow2'); ?></h3>

			<div class="wpif2_sc_section wpif2_library">
				<h4><?php _e('Media Library Image Options', 'wp-imageflow2'); ?></h4>
				<div>
					<label for="wpif2_sc_order"><?php _e('Order', 'wp-imageflow2'); ?></label>
					<select id="wpif2_sc_order">
						<option value="" SELECTED></option>
						<option value="desc">DESC</option>
						<option value="asc">ASC</option>
					</select>
				</div>
				<div>
					<label for="wpif2_sc_orderby"><?php _e('Order By', 'wp-imageflow2'); ?></label>
					<select id="wpif2_sc_orderby">
						<option value="" SELECTED></option>
						<option value="menu_order">menu_order</option>
						<option value="title">title</option>
						<option value="post_date">post_date</option>
						<option value="rand">rand</option>
						<option value="id">id</option>
					</select>
				</div>
				<div>
					<label for="wpif2_sc_include"><?php _e('Include', 'wp-imageflow2'); ?>:</label>
					<input type="text" name="wpif2_sc_include" value="" id="wpif2_sc_include" class="media-input" /><button class="media-button"><?php _e('Select images to include', 'wp-imageflow2'); ?></button>
					<span class="wpif2_sc_caption"><?php _e('Comma separated list of attachment IDs', 'wp-imageflow2'); ?></span>
				</div>
				<div>
					<label for="wpif2_sc_exclude"><?php _e('Exclude', 'wp-imageflow2'); ?>:</label>
					<input type="text" name="wpif2_sc_exclude" value="" id="wpif2_sc_exclude" class="media-input" /><button class="media-button"><?php _e('Select images to exclude', 'wp-imageflow2'); ?></button>
					<span class="wpif2_sc_caption"><?php _e('Comma separated list of attachment IDs', 'wp-imageflow2'); ?></span>
				</div>
				<div>
					<label for="wpif2_sc_samewindow"><?php _e('Where should image links open', 'wp-imageflow2'); ?>?</label>
					<select id="wpif2_sc_samewindow" />
						<option value="" SELECTED></option>
						<option value="true"><?php _e('Same Window', 'wp-imageflow2'); ?></option>
						<option value="false"><?php _e('New Window', 'wp-imageflow2'); ?></option>
					</select>
				</div>
			</div>

			<div class="wpif2_sc_section wpif2_general">
				<h4><?php _e('General Options', 'wp-imageflow2'); ?></h4>
				<div>
					<label for="wpif2_sc_startimg"><?php _e('Starting Image', 'wp-imageflow2'); ?></label>
					<input type="number" name="wpif2_sc_startimg" value="" id="wpif2_sc_startimg" />
				</div>
				<div>
					<label for="wpif2_sc_rotate"><?php _e('Automatic Rotation', 'wp-imageflow2'); ?></label>
					<select id="wpif2_sc_rotate" />
						<option value="" SELECTED></option>
						<option value="on"><?php _e('On', 'wp-imageflow2'); ?></option>
						<option value="off"><?php _e('Off', 'wp-imageflow2'); ?></option>
					</select>
				</div>
				<div>
					<label for="wpif2_sc_carousel"><?php _e('Carousel Style', 'wp-imageflow2'); ?></label>
					<select id="wpif2_sc_carousel" />
						<option value="baseline" SELECTED><?php _e('Bottom Aligned', 'wp-imageflow2'); ?></option>
						<option value="topline" <?php echo $disabled; ?>><?php _e('Top Aligned', 'wp-imageflow2'); ?></option>
						<option value="midline"<?php echo $disabled; ?>><?php _e('Vertical Centered', 'wp-imageflow2'); ?></option>
						<option value="angled"<?php echo $disabled; ?>><?php _e('Angled', 'wp-imageflow2'); ?></option>
						<option value="flip"<?php echo $disabled; ?>><?php _e('Flipbook', 'wp-imageflow2'); ?></option>
						<option value="explode"<?php echo $disabled; ?>><?php _e('Exploding', 'wp-imageflow2'); ?></option>
					</select>
				</div>
				<div>
					<label for="wpif2_sc_nocaptions"><?php _e('Hide Captions', 'wp-imageflow2'); ?></label>
					<select id="wpif2_sc_nocaptions" />
						<option value="" SELECTED></option>
						<option value="true"><?php _e('True', 'wp-imageflow2'); ?></option>
						<option value="false"><?php _e('False', 'wp-imageflow2'); ?></option>
					</select>
				</div>
					<label for="wpif2_sc_caption_style"><?php _e('Caption Style', 'wp-imageflow2'); ?></label>
					<select id="wpif2_sc_caption_style" />
						<option value="" SELECTED></option>
						<option value="slide-up"<?php echo $disabled; ?>><?php _e('Slide-Up', 'wp-imageflow2'); ?></option>
					</select>
				</div>
			</div>

			<div>	
				<a href="#" class="button-primary" id="wpif2_sc_insert"><?php _e('Insert', 'wp-imageflow2'); ?></a>
			</div>
	</form>
  </div>
</div>

<?php
	}

	/*
	** Add scripts
	*/
	function add_scripts() {
?>

<script type="text/javascript">
  jQuery(function(){
	jQuery("#wpif2_sc_insert").click(function() {
		output = '[wp-flowplus';

		// Add non-blank, non-hidden options
		if (!jQuery(".wpif2_attached").hasClass ('hide')) {
			if (jQuery('#wpif2_sc_id').val()) output += ' id=' + jQuery('#wpif2_sc_id').val();
		}

		if (!jQuery(".wpif2_mediatag").hasClass ('hide')) {
			if (jQuery('#wpif2_sc_mediatag').val()) output += ' mediatag=' + jQuery('#wpif2_sc_mediatag').val();
		}

		if (!jQuery(".wpif2_featured").hasClass ('hide')) {
			if (jQuery('#wpif2_sc_featured').val()) output += ' category=' + jQuery('#wpif2_sc_featured').val();
		}

		if (!jQuery(".wpif2_folder").hasClass ('hide')) {
			if (jQuery('#wpif2_sc_folder').val()) output += ' dir=' + jQuery('#wpif2_sc_folder').val();
		}

		if (!jQuery(".wpif2_nextgen").hasClass ('hide')) { 
			if (jQuery("#wpif2_sc_ngg_id").length && jQuery('#wpif2_sc_ngg_id').val()) output += ' ngg_id=' + jQuery('#wpif2_sc_ngg_id').val();
		}

		if (!jQuery(".wpif2_library").hasClass ('hide')) {
			if (jQuery('#wpif2_sc_order').val()) output += ' order=' + jQuery('#wpif2_sc_order').val();
			if (jQuery('#wpif2_sc_orderby').val()) output += ' orderby=' + jQuery('#wpif2_sc_orderby').val();
			if (jQuery('#wpif2_sc_include').val()) output += ' include="' + jQuery('#wpif2_sc_include').val() + '"';
			if (jQuery('#wpif2_sc_exclude').val()) output += ' exclude="' + jQuery('#wpif2_sc_exclude').val() + '"';
			if (jQuery('#wpif2_sc_samewindow').val()) output += ' samewindow=' + jQuery('#wpif2_sc_samewindow').val();
		}

		if (jQuery('#wpif2_sc_startimg').val()) output += ' startimg=' + jQuery('#wpif2_sc_startimg').val();
		if (jQuery('#wpif2_sc_rotate').val()) output += ' rotate=' + jQuery('#wpif2_sc_rotate').val();
		if (jQuery('#wpif2_sc_carousel').val()) output += ' style=' + jQuery('#wpif2_sc_carousel').val();
		if (jQuery('#wpif2_sc_nocaptions').val()) output += ' nocaptions=' + jQuery('#wpif2_sc_nocaptions').val();
		if (jQuery('#wpif2_sc_caption_style').val()) output += ' captions="' + jQuery('#wpif2_sc_caption_style').val() + '"';

		output += ']';
		send_to_editor(output);
	});

	jQuery("#wpif2_sc_source").change(function(evt) {
            var val = jQuery(this).val();
            switch ( val ) {
                case 'attached':
					jQuery(".wpif2_attached").removeClass ('hide');
					jQuery(".wpif2_library").removeClass ('hide');
					jQuery(".wpif2_medialib").addClass ('hide');
					jQuery(".wpif2_mediatags").addClass ('hide');
					jQuery(".wpif2_featured").addClass ('hide');
					jQuery(".wpif2_folder").addClass ('hide');
					jQuery(".wpif2_nextgen").addClass ('hide');
                    break;
                case 'featured':
					jQuery(".wpif2_attached").addClass ('hide');
					jQuery(".wpif2_library").removeClass ('hide');
					jQuery(".wpif2_medialib").addClass ('hide');
					jQuery(".wpif2_mediatags").addClass ('hide');
					jQuery(".wpif2_featured").removeClass ('hide');
					jQuery(".wpif2_folder").addClass ('hide');
					jQuery(".wpif2_nextgen").addClass ('hide');
                    break;
                case 'folder':
					jQuery(".wpif2_attached").addClass ('hide');
					jQuery(".wpif2_library").addClass ('hide');
					jQuery(".wpif2_medialib").addClass ('hide');
					jQuery(".wpif2_mediatags").addClass ('hide');
					jQuery(".wpif2_featured").addClass ('hide');
					jQuery(".wpif2_folder").removeClass ('hide');
					jQuery(".wpif2_nextgen").addClass ('hide');
                    break;
                case 'medialib':
					jQuery(".wpif2_attached").addClass ('hide');
					jQuery(".wpif2_library").removeClass ('hide');
					jQuery(".wpif2_medialib").removeClass ('hide');
					jQuery(".wpif2_mediatags").addClass ('hide');
					jQuery(".wpif2_featured").addClass ('hide');
					jQuery(".wpif2_folder").addClass ('hide');
					jQuery(".wpif2_nextgen").addClass ('hide');
                    break;
				case 'mediatag':
					jQuery(".wpif2_attached").addClass ('hide');
					jQuery(".wpif2_library").removeClass ('hide');
					jQuery(".wpif2_medialib").addClass ('hide');
					jQuery(".wpif2_mediatags").removeClass ('hide');
					jQuery(".wpif2_featured").addClass ('hide');
					jQuery(".wpif2_folder").addClass ('hide');
					jQuery(".wpif2_nextgen").addClass ('hide');
                    break;
                case 'nextgen':
					jQuery(".wpif2_attached").addClass ('hide');
					jQuery(".wpif2_library").addClass ('hide');
					jQuery(".wpif2_medialib").addClass ('hide');
					jQuery(".wpif2_mediatags").addClass ('hide');
					jQuery(".wpif2_featured").addClass ('hide');
					jQuery(".wpif2_folder").addClass ('hide');
					jQuery(".wpif2_nextgen").removeClass ('hide');
                    break;
            }
	});

	var gk_media_init = function(selector, button_selector)  {
		var clicked_button = false;
	 
		jQuery(selector).each(function (i, input) {
			var button = jQuery(input).next(button_selector);
			button.click(function (event) {
				event.preventDefault();
				var selected_img;
				clicked_button = jQuery(this);
	 
				// check for media manager instance
				if(wp.media.frames.gk_frame) {
					wp.media.frames.gk_frame.open();
					return;
				}
				// configuration of the media manager new instance
				wp.media.frames.gk_frame = wp.media({
					title: 'Select images',
					multiple: true,
					library: {
						type: 'image'
					},
					button: {
						text: 'Use selected images'
					}
				});
	 
				// Function used for the image selection and media manager closing
				var gk_media_set_image = function() {
					var selection = wp.media.frames.gk_frame.state().get('selection');
					clicked_button.prev(selector).val('');
					
					// no selection
					if (!selection) {
						return;
					}
	 
					// iterate through selected elements
					selection.each(function(attachment) {
						//console.log(attachment);
					//var url = attachment.attributes.url;
						var id = attachment.id;
						var val = clicked_button.prev(selector).val();
						if (val == '') {
							clicked_button.prev(selector).val(id);
						} else {
							clicked_button.prev(selector).val(val+','+id);
						}
					});
				};
	 
				// closing event for media manger
				wp.media.frames.gk_frame.on('close', gk_media_set_image);
				// image selection event
				wp.media.frames.gk_frame.on('select', gk_media_set_image);
				// showing media manager
				wp.media.frames.gk_frame.open();
			});
	   });
	};
	gk_media_init('.media-input', '.media-button');
  });
</script>

<?php
	}

} // end of class

// Prevent duplicate shortcode generators due to mismatched plugin versions
class WPIF2Shortcode {
}
if (isset($wpimageflow2_sc)) unset ($wpimageflow2_sc);

$wpimageflow2_shortcode = new WPFlowPlus_Shortcode();
}
?>