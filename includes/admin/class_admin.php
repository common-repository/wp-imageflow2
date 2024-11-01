<?php
/*
** Admin class
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!class_exists("WPFlowPlus_Admin")) {
class WPFlowPlus_Admin {
	
	/*
	** __construct function.
	*/
	public function __construct() {

		// Settings tabs
		add_action('admin_menu', array($this, 'settings_page_add'));
		add_filter("plugin_action_links_".plugin_basename(__FILE__), array($this, 'plugin_settings_link' ) );
		add_filter('wpflowplus_settings_tabs_array', array($this, 'settings_tabs_array_default'));
		add_action('wpfp_settings_tab_general', array($this, 'settings_tab_general'));
		add_action('wpfp_settings_update_general', array($this, 'settings_update_general'));
		add_action('wpfp_settings_tab_format', array($this, 'settings_tab_format'));
		add_action('wpfp_settings_update_format', array($this, 'settings_update_format'));
		add_action('wpfp_settings_tab_help', array($this, 'settings_tab_help'));
		add_action('wpfp_settings_update_help', array($this, 'settings_update_help'));
		
		// Image link support
		add_filter("attachment_fields_to_edit", array($this, 'image_links'), null, 2);
		add_filter("attachment_fields_to_save", array($this, 'image_links_save'), null , 2);
		
		// Actions
		add_action('admin_enqueue_scripts', array($this, 'add_admin_scripts'));			
	}

	/*
	** Admin scripts
	*/
	function add_admin_scripts() {
		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script('wpif2_utility_js', WPIF2_PLUGIN_URL . 'js/wpif2_utility.js', array('wp-color-picker', 'jquery'),
							filemtime( WPIF2_PLUGIN_DIR . 'js/wpif2_utility.js') );
	}	

	/*
	** Image links in media library
	*/
	function image_links($form_fields, $post) {	
		$form_fields["wpif2-image-link"] = array(
			"label" => __("WP Flow Plus Link", 'wp-imageflow2'),
			"input" => "", // this is default if "input" is omitted
			"value" => get_post_meta($post->ID, "_wpif2-image-link", true),
      	 	"helps" => __("To be used with carousel added via [wp-flowplus] shortcode.", 'wp-imageflow2'),
		);
	   return $form_fields;
	}

	/*
	** Image link update function
	*/
	function image_links_save($post, $attachment) {	
		if( isset($attachment['wpif2-image-link']) ){
			update_post_meta($post['ID'], '_wpif2-image-link', $attachment['wpif2-image-link']);
		}
		return $post;
	}

	/*
	** Add settings link on plugin page
	*/
	function plugin_settings_link($links = array()) { 
		$settings_link = '<a href="options-general.php?page=wpFlowPlus">'.__('Settings', 'wp-imageflow2').'</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	

	/*
	** Create the settings page
	*/
	function settings_page_add() { 
		add_options_page( __('WP Flow Plus Options', 'wp-imageflow2'), __('WP Flow Plus', 'wp-imageflow2'), 
					'manage_options', 'wpFlowPlus', array($this, 'settings_page_output'));
	}	

	/*
	** Construct the admin settings page for the plugin
	*/
	function settings_page_output() {
		global $options_page, $wpimageflow2;

		// verify user has permission
		if (!current_user_can('manage_options'))
			wp_die(__('Sorry, but you have no permission to change settings.','wp-imageflow2'));	
			
		// update the settings for the current tab
		if ( isset($_POST['save_wpflowplus']) && ($_POST['save_wpflowplus'] == 'true') && 
					check_admin_referer('update_wpflowplus_options')) {
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
			do_action ( 'wpfp_settings_update_' . $current_tab);
		}

		// Get tabs for the settings page
		if (has_filter('wpflowplus_settings_tabs_array')) {
			$tabs = apply_filters( 'wpflowplus_settings_tabs_array', array() );
		}
		
		// proceed with the settings form
		$options = $wpimageflow2->getAdminOptions();
		include 'admin-settings.php';
	}
	
	/*
	** Add the default tabs to the settings tab array
	*/
	function settings_tabs_array_default ($settings_tabs ) {
		$default_tabs = array (
							'general' =>  __( 'General', 'wp-imageflow2' ),
							'format' => __( 'Format', 'wp-imageflow2' ),
							'help' => __( 'Help', 'wp-imageflow2' ));
		
        //print_r($default_tabs + $settings_tabs);
		return $default_tabs + $settings_tabs;
	}
	
	/*
	** Output the admin settings page for the "Format" tab
	*/
	function settings_tab_format() {
		global $wpimageflow2;
		
		$options = $wpimageflow2->getAdminOptions();
		include 'admin-settings-tab-format.php';
	}
	
	/*
	** Output the admin settings page for the "General" tab
	*/
	function settings_tab_general() {
		global $wpimageflow2;
		
		$options = $wpimageflow2->getAdminOptions();
		include 'admin-settings-tab-general.php';
	}

	/*
	** Output the admin settings page for the "Help" tab
	*/
	function settings_tab_help() {
		global $wpimageflow2;
		
		$options = $wpimageflow2->getAdminOptions();
		include 'admin-settings-tab-help.php';
	}
	
	/*
	** Save the "Format" tab updates
	*/
	function settings_update_format() {
		global $wpimageflow2;
		
		$options = $wpimageflow2->getAdminOptions();
		$errors = '';
		$error_count = 0;

		/*
		** Validate the background colour
		*/
		if (isset($_POST['wpimageflow2_bgc'])) {
			if ((preg_match('/^#[a-f0-9]{6}$/i', $_POST['wpimageflow2_bgc'])) || ($_POST['wpimageflow2_bgc'] == 'transparent')) {
				$options['bgcolor'] = $_POST['wpimageflow2_bgc'];
			} else {
				$error_count++;
				$errors .= "<p>".__('Invalid background color, not saved.','wp-imageflow2')."</p>";	
			}
		}
		
		/*
		** Validate the text colour
		*/
		if (isset($_POST['wpimageflow2_txc'])) {
			if (preg_match('/^#[a-f0-9]{6}$/i', $_POST['wpimageflow2_txc'])) {
				$options['txcolor'] = $_POST['wpimageflow2_txc'];
			} else {
				$error_count++;
				$errors .= "<p>".__('Invalid text color, not saved.','wp-imageflow2')."</p>";	
			}
		}
		
		/* 
		** Look for disable captions option
		*/
		if (isset($_POST['wpimageflow2_nocaptions']) && ($_POST['wpimageflow2_nocaptions'] == 'nocaptions')) {
			$options['nocaptions'] = true;
		} else {
			$options['nocaptions'] = false;
		}
		
		/*
		** Validate the slider color
		*/
		if (isset($_POST['wpimageflow2_slc'])) {
			if (($_POST['wpimageflow2_slc'] == 'black') || ($_POST['wpimageflow2_slc'] == 'white')) {
				$options['slcolor'] = $_POST['wpimageflow2_slc'];
			} else {
				$error_count++;
				$errors .= "<p>".__('Invalid slider color, not saved.','wp-imageflow2')."</p>";	
			}
		}
		
		/* 
		** Look for disable slider option
		*/
		if (isset($_POST['wpimageflow2_noslider']) && ($_POST['wpimageflow2_noslider'] == 'noslider')) {
			$options['noslider'] = true;
		} else {
			$options['noslider'] = false;
		}
		
		/*
		** Accept the container width
		*/
		if (isset($_POST['wpimageflow2_width'])) {
			$options['width'] = sanitize_text_field($_POST['wpimageflow2_width']);
		}
		
		/*
		** Accept the container aspect ratio
		*/
		if (isset($_POST['wpimageflow2_aspect'])) {
			$options['aspect'] = sanitize_text_field($_POST['wpimageflow2_aspect']);
		}
		
		/*
		** Accept the carousel image size
		*/
		if (isset($_POST['wpimageflow2_image_size'])) {
			$options['imgsize'] = sanitize_text_field($_POST['wpimageflow2_image_size']);
		}
				
		/*
		** Done validation, update whatever was accepted
		*/
		$this->settings_update_save ($options, $errors, $error_count);
	}

	/*
	** Save the "General" tab updates
	*/
	function settings_update_general() {
		global $wpimageflow2;
		
		$options = $wpimageflow2->getAdminOptions();
		$errors = '';
		$error_count = 0;

		/* 
		** Look for link to new window option
		*/
		if (isset($_POST['wpimageflow2_samewindow']) && ($_POST['wpimageflow2_samewindow'] == 'same')) {
			$options['samewindow'] = true;
		} else {
			$options['samewindow'] = false;
		}
		
		/* 
		** Look for reflect option
		*/
		if (isset ($_POST['wpimageflow2_reflect'])) {
			$options['reflect'] =  $_POST['wpimageflow2_reflect'];
		}
		
		/* 
		** Look for auto rotate option
		*/
		if (isset ($_POST['wpimageflow2_autorotate']) && ($_POST['wpimageflow2_autorotate'] == 'autorotate')) {
			$options['autorotate'] = 'on';
		} else {
			$options['autorotate'] = 'off';
		}

		/*
		** Accept the pause value
		*/
		if (isset($_POST['wpimageflow2_pause'])) {
			$pause = intval($_POST['wpimageflow2_pause']);
			if ( (string) $pause == $_POST['wpimageflow2_pause']) {
				$options['pause'] = $pause;
			} else {
				$error_count++;
				$errors .= "<p>".__('Invalid Auto rotation pause, must be an integer. Not saved.','wp-imageflow2')."</p>";	
			}
		}
		
		/*
		** Accept the path URL
		*/
		if (isset($_POST['wpimageflow2_path'])) {
			$options['gallery_url'] = sanitize_text_field($_POST['wpimageflow2_path']);
		}
		
		/*
		** Done validation, update whatever was accepted
		*/
		$this->settings_update_save ($options, $errors, $error_count);
	}
	
		/*
	** Save the "Help" tab updates
	*/
	function settings_update_help() {
		// the help tab has no settings to update
	}
	
	function settings_update_save($options, $errors = '', $error_count = 0) {
		update_option(WPFP_OPTIONS_NAME, $options);
		if ($errors == '') {
			echo "<div id='message' class='updated'>";	
			echo '<p>'.__('Settings were saved.','wp-imageflow2').'</p></div>';	
		} else {
			echo "<div id='message' class='error'>" . $errors;	
			if ($error_count == 1) {
				echo '<p>'.__('The above setting was not saved.','wp-imageflow2');
			} else {
				echo '<p>'.__('The above settings were not saved.','wp-imageflow2');
			}
			echo __(' Other settings were successfully saved.','wp-imageflow2').'</p></div>';
		}
	}
		
}
}