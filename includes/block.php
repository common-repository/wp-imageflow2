<?php
/*
 ** WP Flow Plus Gutenberg Block
 **
 ** This code is included during the "init" action.
 **
 ** Copyright Spiffy Plugins
 **
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (!class_exists("WPFPBlock")) {
class WPFPBlock {

	/*
	** Construct the block
	*/
	function __construct () {
		global $wpdb, $wpimageflow2;
		
		// Get featured category list
		$categories = get_categories();
		$cats_array = array();
		$cats_array[] = array ( 'value' => '', 'label' => '');
		if(is_array($categories)) {
			foreach($categories as $category) {
				$cats_array[] = array( 'value' => $category->cat_ID,
										'label' => esc_html(stripslashes($category->cat_name))
									);
			}
		}
			
		// Get folder list
		$folders_array = array();
		$folders_array[] = array ( 'value' => '', 'label' => '');
		$wp_options = $wpimageflow2->getAdminOptions();
		$galleries_path = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $wpimageflow2->get_path($wp_options['gallery_url']);
		if (file_exists($galleries_path)) {
			$handle	= opendir($galleries_path);
			while ($dir=readdir($handle)) {
				if ($dir != "." && $dir != "..")
					$folders_array[] = array ( 'value' => $dir, 'label' => $dir );
			}
			closedir($handle);								
		}					
		
		// Get NextGen gallery list
		$ngg_array = array();
		if (class_exists('nggLoader')) {
			$ngg_active = true;
			$categories = get_categories();
			$ngg_array[] = array ( 'value' => '', 'label' => '');
			$gallerylist = $wpdb->get_results("SELECT * FROM $wpdb->nggallery ORDER BY gid ASC");

			if(is_array($gallerylist)) {
				foreach($gallerylist as $gallery) {
					$ngg_array[] = array( 'value' => $gallery->gid,
											'label' => $gallery->title
										);
				}
			}
		} else {
			$ngg_active = false;
		}
						
		// Register our scripts and associated data
		//$bonus_active = is_plugin_active( 'wp-imageflow2-addons/wp-imageflow2-addons.php');
		$bonus_active = true;
		// Note - the gallery script struggles in a Gutenberg block, so don't support backend display
		//wp_enqueue_script('wpif2_flowplus', WPIF2_PLUGIN_URL . '/js/imageflowplus.js', array('jquery'), filemtime( WPIF2_PLUGIN_DIR . 'js/imageflowplus.js') );
		//wp_enqueue_style( 'wpflowpluscss',  WPIF2_PLUGIN_URL . 'css/screen.css');
		wp_register_script(
			'wpfp-block',
			plugins_url( '/js/block.js', __DIR__ ),
			array( 
				'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n',
				//'wpif2_flowplus'
			),
			filemtime( plugin_dir_path(__DIR__) . 'js/block.js'));
		wp_add_inline_script( 
			'wpfp-block', 
'/* <![CDATA[ */
' . 'var wpfp_bonus="'.$bonus_active.'";
'. 'var wpfp_cats='. json_encode($cats_array, JSON_PRETTY_PRINT) . ';
'. 'var wpfp_folders='. json_encode($folders_array, JSON_PRETTY_PRINT) . ';
' . 'var wpfp_nextgen="'.$ngg_active.'";
'. 'var wpfp_galleries='. json_encode($ngg_array, JSON_PRETTY_PRINT) . ';
' . '/* ]]> */',
			'before' );
		
		$ret = register_block_type( 'wpfp/main-block', array(
			'attributes' => array(
								'source' => array(
										'type' => 'string',
										'default' => 'attached',
									),
								'post_id' => array(
										'type' => 'string',
										//'default' => isset($_REQUEST['post'])? $_REQUEST['post'] : '',
									),
								'category' => array (
										'type' => 'string'
									),
								'dir' => array (
										'type' => 'string',
									),
								'ngg_id' => array (
										'type' => 'string',
									),
								'order' => array(
										'type' => 'string',
									),
								'orderby' => array(
										'type' => 'string',
									),
								'include' => array(
										'type' => 'string',
									),
								'exclude' => array(
										'type' => 'string',
									),
								'samewindow' => array(
										'type' => 'string',
									),
								'startimg' => array(
										'type' => 'number',
									),
								'rotate' => array(
										'type' => 'string',
									),
								'style' => array(
										'type' => 'string',
									),
								'nocaptions' => array(
										'type' => 'string',
									),
								'captions' => array(
										'type' => 'string',
									),
								),
			'editor_script' => 'wpfp-block',
			//'editor_style' => 'wpflowpluscss',
			'render_callback' => array($this, 'block_render'),
		) );	
	}

	/**
	 * Render the block.
	 *
	 * @param array $attributes The attributes that were set on the block.
	 */
	public function block_render( $attributes ) {

		
		// Remove attributes that don't apply to the image source option
		switch ($attributes['source']) {
			case 'attached':
					unset ($attributes['category']);
					unset ($attributes['dir']);
					unset ($attributes['ngg_id']);
					break;
			case 'featured':
					unset ($attributes['post_id']);
					unset ($attributes['dir']);
					unset ($attributes['ngg_id']);
					break;
			case 'folder':
					unset ($attributes['post_id']);
					unset ($attributes['category']);
					unset ($attributes['ngg_id']);
					unset ($attributes['order']);
					unset ($attributes['orderby']);
					unset ($attributes['include']);
					unset ($attributes['exclude']);
					unset ($attributes['samewindow']);
					break;
			case 'medialib':
					unset ($attributes['post_id']);
					unset ($attributes['category']);
					unset ($attributes['dir']);
					unset ($attributes['ngg_id']);
					break;
			case 'nextgen':
					unset ($attributes['post_id']);
					unset ($attributes['category']);
					unset ($attributes['dir']);
					unset ($attributes['order']);
					unset ($attributes['orderby']);
					unset ($attributes['include']);
					unset ($attributes['exclude']);
					unset ($attributes['samewindow']);
					break;
		}

		// Encode the shortcode attributes
		$shortcode_atts = '';
		foreach ($attributes as $key => $value) {
			if ($value != '') {
				$shortcode_atts .= ' ' . $key . '="' . ((is_array($value))? implode(',', $value) : $value) . '"';
			}
		}
		
		// Render the output appropriately
		//return do_shortcode('[wp-flowplus' . $shortcode_atts . ']');
		return '[wp-flowplus' . $shortcode_atts . ']';
	}

} // end of class
}

if (class_exists("WPFPBlock")) {
	$wpfp_block = new WPFPBlock();
}

?>