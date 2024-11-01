<?php
/**
 * Admin View: Settings tab "Help"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<table class="form-table">
<tr><td>
<h3>Shortcode</h3>
<p>The basic shortcode is <strong>[wp-flowplus]</strong>. For backwards compatibility you may also use [wp-imageflow2].</p>
<p>With no options, the shortcode will create a gallery from the images that are attached to the current page/post.</p>

<h3>Shortcode Options</h3>
<h4>orderby</h4>
<p>Specify how to sort the display thumbnails. The default is "menu_order". This supports the standard WordPress values.</p>

<h4>order</h4>
<p>Specify the sort order used to display thumbnails. ASC or DESC. For example, to sort by ID, DESC:</p>

<p>[wp-flowplus order="DESC" orderby="ID"]</p>
<h4>id</h4>

<p>Specify the post ID. The gallery will display images which are attached to that post. The default behavior, if no ID is specified, is to display images attached to the current post. For example, to display images attached to post 123:</p>

<p>[wp-flowplus id="123"]</p>
<h4>include</h4>

<p>Comma separated attachment IDs to show only the images from these attachments.</p>

<p>[wp-flowplus include="23,39,45"]</p>
<h4>exclude</h4>

<p>Comma separated attachment IDs excludes the images from these attachments. Please note that include and exclude cannot be used together.</p>

<p>[wp-flowplus exclude="21,32,43"]</p>
<h4>dir</h4>

<p>Specify a subdirectory name. The path to the subdirectory must be configured on the settings page.</p>

<h4>mediatag</h4>

<p>Works with the <a href="https://wordpress.org/plugins/media-tags/">Media Tags plugin</a>. This option will pull matching media out of your media library and include it in the gallery. Specify the media slug to select. Example use: mediatag=mymedia</p>

<h4>startimg</h4>

<p>Gives the starting slide number to center in the gallery, the default is 1.</p>

<h4>rotate</h4>

<p>Turns on/off auto-rotation for this instance (overrides the setting from the admin panel). Values are 'on' or 'off'.</p>

<h4>samewindow</h4>

<p>Specifies if links open in the same window or a new window. Set true or false, overrides the default value from the settings page.</p>

<h4>category</h4>

<p>Select featured posts by category. The featured image of each post will be placed in the carousel, and each image will link to the post. If a post has no featured image, the first image found attached to the post will be used instead, if there is no attached image a default image will be used.</p>

<h4>ngg_id</h4>

<p>Create carousel from a NextGen gallery. With the NextGen plugin you can perform bulk image resizing, and regenerate thumbnails in any size. The WP Flow Plus carousel will use the thumbnail size for the carousel images, and the full size for the Lightbox images.</p>

<h4>captions</h4>

<p>Specify the style of the captions in the carousel. Values are:</p>
<ul style="list-style-type: disc; margin-left: 20px;">
<li><strong>slide-up</strong>: grey box slides up when centered, containing the title and description</li>
</ul>

<h4>style</h4>

<p>Specify the carousel animation and display style. Values are:</p>
<ul style="list-style-type: disc; margin-left: 20px;">
<li><strong>baseline</strong>: the default basic carousel provided with the free version of the plugin</li>
<li><strong>angled</strong>: side images are rotated 45 degrees</li>
<li><strong>flip</strong>: flipbook animation</li>
<li><strong>explode</strong>: exploding image animation</li>
<li><strong>topline</strong>: images are top aligned</li>
<li><strong>midline</strong>: images are center aligned</li>
</ul>
</td>
<td style="vertical-align: top;">
<?php
include "admin-settings-promo.php";
?>
</td>
</tr>
</table>

