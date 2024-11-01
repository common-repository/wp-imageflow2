<?php
/*
** Rendering functions
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (!class_exists("WPFlowPlus_Render")) {
class WPFlowPlus_Render {

	/* html div ids */
	var $flowplusdiv = 'wpif2_flowplus';
	var $loadingdiv   = 'wpif2_loading';
	var $imagesdiv    = 'wpif2_images';
	var $captionsdiv  = 'wpif2_captions';
	var $sliderdiv    = 'wpif2_slider';
	var $scrollbardiv = 'wpif2_scrollbar';
	var $noscriptdiv  = 'wpif2_flowplus_noscript';

	var $wpif2_instance = 0;

	/*
	** Constructor
	*/
	public function __construct() {
		
		// Shortcodes
		add_shortcode('wp-imageflow2', array($this, 'flow_func')); // @DEPRECATED
		add_shortcode('wp-flowplus', array($this, 'flow_func'));	

		// Image filters
		add_filter('wpif2_image_list', array($this, 'featured_posts'), 10, 2);
		add_filter('wpif2_image_list', array($this, 'nextgen_images'), 10, 2);

	}

	/*
	** Compile list of images from featured posts if specified in the shortcode attributes
	*/
	function featured_posts ($val, $attr) {
		global $wpimageflow2;
		
		/* Quit if another filter already filled the list */
		if (!empty($val)) return $val;

		/*
		** Generate a list of the images we are using from post category using the featured image of each post in the gallery
		*/
		/*
		** Standard gallery shortcode defaults that we support here	
		*/
		extract(shortcode_atts(array(
					'order'      => 'DESC',
					'orderby'    => 'date',
					'include'    => '',
					'exclude'    => '',
					'category'     => '',
		  ), $attr));

		if ($category == '') return '';
		
		if ( 'RAND' == $order )
			$orderby = 'none';

		if ( !empty($include) ) {
			$include = preg_replace( '/[^0-9,]+/', '', $include );
			$featured_posts = get_posts( array(
					'include' => $include,  
					'category' => $category, 
					'posts_per_page' => -1,
					'post_status' => 'publish', 
					'post_type' => array('post', 'page'), 
					'order' => $order, 
					'orderby' => $orderby) );
		} elseif ( !empty($exclude) ) {
			$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
			$featured_posts = get_posts( array(
					'exclude' => $exclude, 
					'category' => $category, 
					'posts_per_page' => -1,
					'post_status' => 'publish', 
					'post_type' => array('post', 'page'), 
					'order' => $order, 
					'orderby' => $orderby) );
		} else {
			$featured_posts = get_posts( array(
					'category' => $category, 
					'posts_per_page' => -1,
					'post_status' => 'publish', 
					'post_type' => array('post', 'page'), 
					'order' => $order, 
					'orderby' => $orderby) );
		}

		$image_list = array();
		foreach( $featured_posts as $this_post ) {
			// Use the post thumbnail in the carousel. If the post has no thumbnail try to find the first image in the post
			$post_thumbnail_id = get_post_thumbnail_id( $this_post->ID ); 
			if (!$post_thumbnail_id) {
				// Grab first image attachment in menu order
				$args = array(
					'order'          => 'ASC',
					'orderby'        => 'menu_order',
					'post_type'      => 'attachment',
					'post_parent'    => $this_post->ID,
					'post_mime_type' => 'image',
					'post_status'    => null,
					'numberposts'    => 1,
					);
				$attachments = get_posts($args);

				// Feature the first image
				if (!empty($attachments)) {
					$post_thumbnail_id = $attachments[0]->ID;
				}
			}
			
			if ($post_thumbnail_id) {
				$wpif2_options = $wpimageflow2->getAdminOptions();
				$small_image = wp_get_attachment_image_src($post_thumbnail_id, $wpif2_options['imgsize']);
			} else {
				$small_image[0] = WPIF2_PLUGIN_URL . 'img/default.jpg';
			}

			$image_list[] = array (
				'small' => $small_image[0],
				'large' => '',
				'link'  => get_permalink( $this_post->ID ),
				'title' => $this_post->post_title,
				'desc'  => '',
			);
		}
		return $image_list;
	}

	/*
	** Shortcode handler
	*/
	function flow_func($attr) {

		global $blog_id, $wpimageflow2;

		/* Increment the instance to support multiple galleries on a single page */
		$this->wpif2_instance ++;

		/* Get options */
		$options = $wpimageflow2->getAdminOptions();

		/* Prepare options */
		$bgcolor = $options['bgcolor'];
		$txcolor = $options['txcolor'];
		$slcolor = $options['slcolor'];
		$width   = $options['width'];
		$reflect = $options['reflect'];
		$nocaptions = isset ($attr['nocaptions'])? $attr['nocaptions'] == 'true': $options['nocaptions'];
		$slideup = isset ($attr['captions'])? $attr['captions'] == 'slide-up': false;
		if ($slideup) $nocaptions = true;
		if ( isset($attr['rotate']) && !in_array( $attr['rotate'], array("On","Off","on","off"), true) ) unset ($attr['rotate']);
		if ( isset($attr['startimg']) && !is_numeric($attr['startimg'])) unset ($attr['startimg']);
		if ( isset($attr['samewindow']) && !in_array( $attr['samewindow'], array("true","false"), true) ) unset ($attr['samewindow']);

		/* Determine reflection script to use */
		if ($reflect == 'CSS') {
			$reflect_script = 'CSS';
		} else {
			$reflect_script = '';
		}

		/* Produce the Javascript for this instance */
		$js  = "\n".'<script type="text/javascript">'."\n";
		$js .= 'jQuery(document).ready(function() { '."\n".'var flowplus_' . $this->wpif2_instance . ' = new flowplus('.$this->wpif2_instance.');'."\n";
		$js .= 'flowplus_' . $this->wpif2_instance . '.init( {';

		if ( !isset ($attr['rotate']) ) {
			$js_options = 'autoRotate: "' . $options['autorotate'] . '", ';
		} else {
			$js_options = 'autoRotate: "' . $attr['rotate'] . '", ';
		}
		$js_options .= 'autoRotatepause: ' . $options['pause'] . ', ';
		if ( !isset ($attr['startimg']) ) {
			$js_options .= 'startImg: 1' . ', ';
		} else {
			$js_options .= 'startImg: ' . $attr['startimg'] . ', ';
		}
		if ( !isset ($attr['samewindow']) ) {
			$js_options .= $options['samewindow']? 'sameWindow: true, ' : 'sameWindow: false, ';
		} else {
			$js_options .= 'sameWindow: ' . $attr['samewindow'] . ', ';
		}
		if ( isset ($options['aspect']) ) {
			$js_options .= 'aspectRatio: ' . $options['aspect'] . ', ';
		}
		if ($reflect == 'none') {
			$js_options .= 'reflectPC: 0';
		} else {
			$js_options .= 'reflectPC: 1';
		}

		if (has_filter('wpif2_js_options')) {
			$js_options = apply_filters ('wpif2_js_options', $js_options, $attr);
		}
		$js .= $js_options . '} );'."\n";
		$js .= '});'."\n";
		$js .= "</script>\n\n";

		/* Get the list of images */
		$image_list = apply_filters ('wpif2_image_list', array(), $attr);
		if (empty($image_list)) {
		 	if ( !isset ($attr['dir']) ) {
				$image_list = $this->images_from_library($attr, $options);
			} else {
				$image_list = $this->images_from_dir($attr, $options);
	  		}
		}

		/**
		* Start output
		*/
		$noscript = '<noscript><div id="' . $this->noscriptdiv . '_' . $this->wpif2_instance . '" class="' . $this->noscriptdiv . '">';	
		$output  = '<div id="' . $this->flowplusdiv . '_' . $this->wpif2_instance . '" class="' . $this->flowplusdiv . '" style="background-color: ' . $bgcolor . '; color: ' . $txcolor . '; width: ' . $width . '">' . "\n"; 
		$output .= '<div id="' . $this->loadingdiv . '_' . $this->wpif2_instance . '" class="' . $this->loadingdiv . '" style="color: ' . $txcolor . ';">' . "\n";
		$output .= '<b>';
		$output .= __('Loading Images','wp-imageflow2');
		$output .= '</b><br/>' . "\n";
		$output .= '<img src="' . WPIF2_PLUGIN_URL . 'img/loading.gif" width="208" height="13" alt="' . $this->loadingdiv . '" />' . "\n";
		$output .= '</div>' . "\n";
		$output .= '<div id="' . $this->imagesdiv . '_' . $this->wpif2_instance . '" class="' . $this->imagesdiv . '">' . "\n";	

		/**
		* Add images
		*/
		if (!empty ($image_list) ) {
		    $i = 0;
		    foreach ( $image_list as $this_image ) {

				/* What does the carousel image link to? */
				$linkurl 		= $this_image['link'];
				$rel 			= '';
				$dsc			= '';
				if ($linkurl === '') {
					/* We are linking to the popup - use the title and description as the alt text */
					$linkurl = $this_image['large'];
					$rel = ' data-style="wpif2_lightbox"';
					$alt = ' alt="'.$this_image['title'].'"';
					if ($this_image['desc'] != '') {
						
						$dsc = ' data-description="' . htmlspecialchars(str_replace(array("\r\n", "\r", "\n"), "<br />", $this_image['desc'])) . '"';
					}
				} else {
					/* We are linking to an external url - use the title as the alt text */
					$alt = ' alt="'.$this_image['title'].'"';
				}

				/* construct the image source */
				$pic_reflected = $this_image['small'];
								$output .= '<div class="wpif2_image_block">';
				$output .= '<img src="' . $pic_reflected . '" data-link="' . $linkurl . '"' . $rel . $alt . $dsc . ' />';
				if (has_filter('wpif2_after_image')) {
					$output .= apply_filters ('wpif2_after_image', $output, $attr, $this_image);
				}
				if ($reflect == 'CSS') {
					$output .= '<div class="wpif2_reflection" style="background-color:' . $bgcolor . '">';
					$output .= '<img src="' . $this_image['small'] . '" alt="" />';
					$rgba_transparent = $this->hex2rgba ($bgcolor, 0);
					$rgba_solid = $this->hex2rgba ($bgcolor, 1);
					$output .= '<div class="wpif2_overlay" style="
background: -moz-linear-gradient(top,  ' . $rgba_transparent . ' 0%, ' . $rgba_solid . ' 75%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,' . $rgba_transparent . '), color-stop(75%,' . $rgba_solid . ')); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  ' . $rgba_transparent . ' 0%,' . $rgba_solid . ' 75%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  ' . $rgba_transparent . ' 0%,' . $rgba_solid . ' 75%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  ' . $rgba_transparent . ' 0%,' . $rgba_solid . ' 75%); /* IE10+ */
background: linear-gradient(to bottom,  ' . $rgba_transparent . ' 0%,' . $rgba_solid . ' 75%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=#00' . substr($bgcolor, 1, 6) . ', endColorstr=' . $bgcolor . ',GradientType=0 ); /* IE6-9 */"></div></div>';

				}
				$output .= '</div>';

				/* build separate thumbnail list for users with scripts disabled */
				$noscript .= '<a href="' . $linkurl . '"><img src="' . $this_image['small'] .'" width="100"  alt="'.$this_image['title'].'" /></a>';
				$i++;
		    }
		}
					
		
		$output .= '</div>' . "\n";
		$output .= '<div id="' . $this->captionsdiv . '_' . $this->wpif2_instance . '" class="' . $this->captionsdiv . '"';
		if ($nocaptions) $output .= ' style="display:none !important;"';
		$output .= '></div>' . "\n";
		$output .= '<div id="' . $this->scrollbardiv . '_' . $this->wpif2_instance . '" class="' . $this->scrollbardiv;
		if ($slcolor == "black") $output .= ' black';
		$output .= '"';
		if ($options['noslider']) $output .= ' style="display:none !important;"';
		$output .= '><div id="' . $this->sliderdiv . '_' . $this->wpif2_instance . '" class="' . $this->sliderdiv . '">' . "\n";
		$output .= '</div>';
		$output .= '</div>' . "\n";
		$output .= $noscript . '</div></noscript></div>';	

		return $js . $output;

	}

	/* 
	** Convert hexdec color string to rgb(a) string
	*/
	function hex2rgba($color, $opacity) {
		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color))
			  return $default; 

		//Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Add opacity is set(rgba or rgb)
		if(abs($opacity) > 1)
			$opacity = 1.0;
		$output = 'rgba('.implode(",",$rgb).','.$opacity.')';

        //Return rgb(a) color string
        return $output;
	}

	/*
	** Get list of images to use from a directory
	*/
	function images_from_dir ($attr, $options) {
		/*
		** Generate the image list by reading a folder
		*/
		$image_list = array();

		$galleries_path = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . WPFlowPlus::get_path($options['gallery_url']);
		if (!file_exists($galleries_path))
			return '';

		/*
		** Gallery directory is ok - replace the shortcode with the image gallery
		*/
		$plugin_url = get_option('siteurl') . "/" . PLUGINDIR . "/" . plugin_basename(dirname(__FILE__)); 			
			
		$gallerypath = $galleries_path . $attr['dir'];
		if (file_exists($gallerypath))
		{	
			$handle = opendir($gallerypath);
			$skipTypes = ['index.php','Thumbs.db'];
			while ($image=readdir($handle)) {
				//if ($image == 'index.php') continue; // skip these
				if (in_array($image,$skipTypes)) continue; // skip these
				if (filetype($gallerypath."/".$image) != "dir" && !preg_match('/refl_/',$image)) {
					$pageURL = 'http';
					if (isset($_SERVER['HTTPS']) && ($_SERVER["HTTPS"] == "on")) {$pageURL .= "s";}
					$pageURL .= "://";
					if ($_SERVER["SERVER_PORT"] != "80") {
				   	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
				} else {
				   	$pageURL .= $_SERVER["SERVER_NAME"];
				}
				$imagepath = $pageURL . '/' . WPFlowPlus::get_path($options['gallery_url']) . $attr['dir'] . '/' . $image;
				$image_list[] = array (
					'small' => $imagepath,
					'large' => $imagepath,
					'link'  => '',
					'title' => $image,
					'desc'  => '',
			);
			    }
			}
			closedir($handle);
		}

		return $image_list;
	}

	/*
	** Get list of images to use from WP Media Library
	*/
	function images_from_library ($attr, $options) {
		/*
		** Generate a list of the images we are using from the Media Library
		*/
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] )
				unset( $attr['orderby'] );
		}
		
		/*
		** Standard gallery shortcode defaults that we support here	
		*/
		global $post;
		if ($post->ID > 0) {
			$post_id = $post->ID;
		} else if (isset($_REQUEST['post'])) {
			$post_id = $_REQUEST['post'];
		} else {
			$post_id = $attr['post_id'];
		}
		extract(shortcode_atts(array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post_id, // default to current post
				'include'    => '',
				'exclude'    => '',
				'mediatag'	 => '',	// corresponds to Media Tags plugin by Paul Menard
		  ), $attr));
	
		$id = intval($id);

		if ( !empty($mediatag) ) {
			$attachments = array();
			if ( function_exists('get_attachments_by_media_tags') ) {
				$mediaList = get_attachments_by_media_tags("media_tags=$mediatag&orderby=$orderby&order=$order");
				foreach ($mediaList as $key => $val) {
					$attachments[$val->ID] = $mediaList[$key];
				}
			}
		} elseif ( !empty($include) ) {
			$include = preg_replace( '/[^0-9,]+/', '', $include );
			$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( !empty($exclude) ) {
			$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
			$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		} else {
			$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		}

		$image_list = array();
		foreach ( $attachments as $id => $attachment ) {
			$small_image = wp_get_attachment_image_src($id, $options['imgsize']);
			$large_image = wp_get_attachment_image_src($id, "large");

			$link_url = '';
			$image_link = get_post_meta($id, '_wpif2-image-link', true);
			if (isset($image_link) && ($image_link != '')) $link_url = $image_link;

			$image_list[] = array (
				'small' => $small_image[0],
				'large' => $large_image[0],
				'link'  => $link_url,
				'title' => $attachment->post_title,
				'desc'  => $attachment->post_content,
			);
		}
		return $image_list;
	}

	/*
	** Compile list of images from a NextGen gallery if specified by the shortcode attributes
	*/
	function nextgen_images ($val, $attr) {
		global $wpdb;

		/* Quit if another filter already filled the list */
		if (!empty($val)) return $val;

		/*
		** Standard gallery shortcode defaults that we support here	
		*/
		extract(shortcode_atts(array(
				'ngg_id'     => '',
		  ), $attr));

		if ($ngg_id == '') return '';

		if (class_exists('nggLoader')) {
			$galleryID = $wpdb->get_var("SELECT gid FROM $wpdb->nggallery WHERE gid  = '".esc_attr($ngg_id)."' ");
		} else {
			return '';
		}
		
		// Get the pictures
		$image_list = array();    
		if ($galleryID) {
			$ngg_options = get_option('ngg_options');  
			$pictures    = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = '$galleryID' AND tt.exclude != 1 ORDER BY tt.$ngg_options[galSort] $ngg_options[galSortDir] ");
	  
			$base_url = get_option('siteurl');
			foreach($pictures as $picture) {
				$image_list[] = array(
					'small' => $base_url . "/" . $picture->path ."/thumbs/thumbs_" . $picture->filename,
					'large' => $base_url . "/" . $picture->path ."/" . $picture->filename,
					'link'  => '',
					'title' => $picture->alttext,
					'desc'  => $picture->description,
				);
			}
		   
		} else {
			 return array('title'=>'No images found');
		}

		return $image_list;
	  }
	
}
}