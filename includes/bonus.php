<?php
/*
 ** Bonus Settings Add-On
 **
 ** Copyright Spiffy Plugins
 **
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (!class_exists("WPFP_Bonus_Settings")) {
Class WPFP_Bonus_Settings {

	var $adminOptionsName = 'wpimageflow2_bonue_options';
	
	public function __construct() {
		add_filter('wpflowplus_settings_tabs_array', array($this, 'settings_tabs_array_bonus'));
		add_action('wpfp_settings_tab_bonus', array($this, 'settings_tab_bonus'));
		add_action('wpfp_settings_update_bonus', array($this, 'settings_update_bonus'));
		
		add_action('wp_enqueue_scripts', array($this, 'insert_bonus_styles'), 11);
		add_action('wp_enqueue_scripts', array($this, 'enqueue_bonus_scripts'), 11);
		
		add_filter('wpif2_js_options', array($this, 'insert_bonus_js_options'), 10, 2);
		add_filter('wpif2_after_image', array($this, 'insert_bonus_after_image'), 10, 3);
	}

	/*
	** Replace free script with bonus script
	*/
	function enqueue_bonus_scripts() {
		wp_dequeue_script('wpif2_flowplus');
		wp_enqueue_script('wpif2_wpflowplus', WPIF2_PLUGIN_URL . 'js/wpflowplus.js', array('jquery'), filemtime( WPIF2_PLUGIN_DIR . 'js/wpflowplus.js'));
	}

	/*
	** Get options array
	*/
	function getAdminOptions() {
		/*
		** Merge default options with the saved values
		*/
		$use_options = array(	
						'arrowcolor' => '#ffffff',	// Arrow color
						'arrowstyle' => 'image',	// Arrow style 'icon-open', 'icon-solid' or 'image'
						'circular'	 =>	false,		// Circular carousel
						'slide_up'	 => false,		// Slide-up captions
						'focus'		 => 4			// Number of images on each side of center image
					);
		$saved_options = get_option($this->adminOptionsName);
		if (!empty($saved_options)) {
			foreach ($saved_options as $key => $option)
				$use_options[$key] = $option;
		}

		return $use_options;
	}
	
	/*
	** Add the bonus image block code
	*/
	function insert_bonus_after_image ($output, $attr, $image_info) {
		global $wpimageflow2;
		
		$options = $this->getAdminOptions();
		$mainoptions = $wpimageflow2->getAdminOptions();
		$nocaptions = isset ($attr['nocaptions'])? $attr['nocaptions'] == 'true': $mainoptions['nocaptions'];
		$slideup = isset ($attr['captions'])? $attr['captions'] == 'slide-up': $options['slide_up'];
		if (!$nocaptions && $slideup) {	
			$captions = '<div class="wpif2-slideup-caption"><h4>' . $image_info['title'] . '</h4>';
			if ($image_info['desc'] != '') $captions .= '<p>' . wp_kses_post($image_info['desc'])	. '</p>';
			return $captions . '</div>';
		} else {
			return '';
		}
	}
	
	/*
	** Add the bonus javascript options to the embedded script
	*/
	function insert_bonus_js_options ($js_options, $attr) {
		$options = $this->getAdminOptions();
		if ($options['circular']) {	
			$js_options .= ', circular: true';
		}
		if ($options['focus']) {
			$js_options .= ', focus: ' . $options['focus'];
		}
		if (isset($attr['style'])) {
			$js_options .= ', sliderStyle: "' . $attr['style'] . '"';
		}
		return $js_options;
	}
	
	/*
	** Add the bonus styles to the WP Flow Plus stylesheet
	*/
	function insert_bonus_styles () {
		global $wpimageflow2;
		
		$options = $this->getAdminOptions();
		$mainoptions = $wpimageflow2->getAdminOptions();
		
		// Determine rotation origin
		if ($mainoptions['reflect'] == 'none') {
			$origin = 'bottom left';
		} else {
			$origin = 'center left';
		}
		
		// Bonus styles
		$custom_css = '
.wpif2-angled .wpif2_images {
	perspective: 1600px;
	transition: transform .5s, visibility .3s, width 0s;
	transform-origin: '.$origin.';
	ms-transform-origin: '.$origin.';	
}
.wpif2-angled .wpif2-left {
    transform: translate3d(0,0,0) rotateY( 45deg );
	ms-transform: translate3d(0,0,0) rotateY( 45deg );
}
.wpif2-angled .wpif2-right {
    transform: translate3d(0,0,0) rotateY( -45deg );
	ms-transform: translate3d(0,0,0) rotateY( -45deg );
}

.wpif2-topline .wpif2_image_block {
	top: 10px !important;
}

.wpif2-flip .wpif2_images, .wpif2-explode .wpif2_images {
	perspective: 1600px;
	transition: transform .5s, visibility .3s, width 0s;
	transform-origin: '.$origin.';
	ms-transform-origin: '.$origin.';	
}
.wpif2-flip .wpif2_image_block,
.wpif2-explode .wpif2_image_block {
	transform: rotateX(90deg);
	ms-transform: rotateX(90deg);
}	
.wpif2-flip .wpif2_image_block.wpif2-centered,
.wpif2-explode .wpif2_image_block.wpif2-centered {
	transform: translate3d(0,0,0) rotateX(0) rotateY(0);
	ms-transform: translate3d(0,0,0) rotateX(0) rotateY(0);
	transition: transform .5s, visibility .3s, opacity .3s, width 0s;
	display:none;
	opacity: 1;
}
.wpif2-flip .wpif2_image_block.wpif2-centered {
	transform-origin: '.$origin.';	
	ms-transform-origin: '.$origin.';	
}
.wpif2-flip .wpif2_image_block.wpif2-left {
	transform: translate3d(0,0,0) rotateX(-90deg);
	ms-transform: translate3d(0,0,0) rotateX(-90deg);
	transition: 0s;
	transform-origin: '.$origin.';	
	ms-transform-origin: '.$origin.';	
}
.wpif2-explode .wpif2_image_block.wpif2-left {
	transform: translate3d(0,400px,0) scale3d(4,4,4);
	ms-transform: translate3d(0,400px,0) scale3d(4,4,4);
	transition: 0s;
	opacity: 0;
}
.wpif2-explode .wpif2_image_block.wpif2-left .wpif2_reflection {
	display: none;
}
.wpif2-flip .wpif2_image_block.wpif2-right .wpif2_reflection,
.wpif2-explode .wpif2_image_block.wpif2-right .wpif2_reflection {
	opacity: 0;
}
.wpif2-flip .wpif2_image_block.wpif2-right,
.wpif2-explode .wpif2_image_block.wpif2-right {
	transform-origin: '.$origin.';
	ms-transform-origin: '.$origin.';
	transform: translate3d(0,0,0) rotateX( -90deg );
	ms-transform: translate3d(0,0,0) rotateX( -90deg );
	transition: transform .5s, visibility .3s, width 0s;
}
';

		// Hide basic captions if using slide-up captions
		if ($options['slide_up'] == true) {
			$custom_css .= "
.wpif2_captions {
	display: none !important;
}
";
		}

		// Add slide-up styles
		if ($mainoptions['reflect'] == 'none') {
			$bottom1 = '-30%';
			$bottom2 = '0';
		} else {
			$bottom1 = '0';
			$bottom2 = '49.9%';
		}

		$custom_css .= '
.wpif2-slideup-caption {
    position: absolute;
    background: black;
    background: rgba(0,0,0,0.75);
    color: #ccc;
    opacity: 0;
    -webkit-transition: all 0.6s ease;
    -moz-transition:    all 0.6s ease;
    -o-transition:      all 0.6s ease;
    width: 100%;
	left: 0; 
	bottom: '.$bottom1.';
	text-align: center;
	padding: 10px 0;
	line-height: normal;
}

.wpif2-centered .wpif2-slideup-caption { 
	bottom: '.$bottom2.';
	opacity: 1;
}
		
.wpif2_image_block .wpif2-slideup-caption h4 {
    font-size: 14px;
    text-transform: none;
	margin: 0;
	padding: 0;
	color: #ccc;
}
.wpif2_image_block .wpif2-slideup-caption p {
    font-size: 12px;
	margin: 8px 0 0 0;
	padding: 0;
	color: #ccc;
}
';

		// Optional arrow styles
		if ($options['arrowstyle'] == 'icon-open') {
			$custom_css .= "

#wpif2_topboxnext:before, #wpif2_topboxnext:visited:before,
#wpif2_topboxprev:before, #wpif2_topboxprev:visited:before {
background-image: url(data:image/gif;base64,AAAA); /* Trick IE into showing hover */
}			
#wpif2_topboxnext:hover, #wpif2_topboxnext:visited:hover,
#wpif2_topboxprev:hover, #wpif2_topboxprev:visited:hover {
background-image: url(data:image/gif;base64,AAAA); /* Trick IE into showing hover */
}	

#wpif2_topboxprev:hover:before, #wpif2_topboxprev:visited:hover:before {
content: '<';
color: " . $options['arrowcolor'] . ";
font-size: 80px;
text-align: left;
width: 100%;
display: block;
margin-top: 50%;
font-family: narrow;
opacity: .5;
padding-left: 10px;
}

#wpif2_topboxnext:hover:before, #wpif2_topboxnext:visited:hover:before {
content: '>';
color: " . $options['arrowcolor'] . ";
font-size: 80px;
text-align: right;
width: 100%;
display: block;
margin-top: 50%;
font-family: narrow;
opacity: .5;
position: absolute;
right: 10px;
}";
		} elseif ($options['arrowstyle'] == 'icon-solid') {
			$custom_css .= "
#wpif2_topboxnext:before, #wpif2_topboxnext:visited:before,
#wpif2_topboxprev:before, #wpif2_topboxprev:visited:before {
background-image: url(data:image/gif;base64,AAAA); /* Trick IE into showing hover */
}			
#wpif2_topboxnext:hover, #wpif2_topboxnext:visited:hover,
#wpif2_topboxprev:hover, #wpif2_topboxprev:visited:hover {
background-image: url(data:image/gif;base64,AAAA); /* Trick IE into showing hover */
}

#wpif2_topboxprev:hover:before, #wpif2_topboxprev:visited:hover:before {
content: ' ';
display: block;
margin-top: 50%;
width: 0;
height: 0;
border-top: 40px solid transparent;
border-bottom: 40px solid transparent;
border-right: 40px solid " . $options['arrowcolor'] . ";
opacity: .5;
margin-left: 10px;
}

#wpif2_topboxnext:hover:before, #wpif2_topboxnext:visited:hover:before {
content: ' ';
display: block;
margin-top: 50%;
width: 0;
height: 0;
border-top: 40px solid transparent;
border-bottom: 40px solid transparent;
border-left: 40px solid " . $options['arrowcolor'] . ";
opacity: .5;
margin-right: 10px;
float: right;
}";
		}
		wp_add_inline_style( 'wpflowpluscss', $custom_css );
	}

	/*
	** Add the bonus tab to the settings tab array
	*/
	function settings_tabs_array_bonus ($settings_tabs ) {
        $settings_tabs['bonus'] = __( 'Advanced', 'wp-flow-plus' );
        return $settings_tabs;
	}

	/*
	** Output the admin settings page for the "Advanced" tab
	*/
	function settings_tab_bonus() {
		
		$options = $this->getAdminOptions();
		include 'admin/admin-settings-tab-bonus.php';
	}

	/*
	** Save the "Bonus" tab updates
	*/
	function settings_update_bonus() {

		$options = $this->getAdminOptions();
		$errors = '';
		$error_count = 0;

		/*
		** Validate the arrow style
		*/
		if (isset($_POST['wpimageflow2_arrowstyle'])) {
			if (($_POST['wpimageflow2_arrowstyle'] == 'image') || 
				($_POST['wpimageflow2_arrowstyle'] == 'icon-open') ||
				($_POST['wpimageflow2_arrowstyle'] == 'icon-solid')) {
				$options['arrowstyle'] = $_POST['wpimageflow2_arrowstyle'];
			} else {
				$error_count++;
				$errors .= "<p>".__('Invalid arrow style, not saved.','wp-flow-plus'). " - " . $_POST['wpimageflow2_arrowstyle'] ."</p>";	
			}
		}
		
		/*
		** Validate the arrow colour
		*/
		if (isset($_POST['wpimageflow2_arrowc'])) {
			if ((preg_match('/^#[a-f0-9]{6}$/i', $_POST['wpimageflow2_arrowc'])) || ($_POST['wpimageflow2_arrowc'] == 'transparent')) {
				$options['arrowcolor'] = $_POST['wpimageflow2_arrowc'];
			} else {
				$error_count++;
				$errors .= "<p>".__('Invalid arrow color, not saved.','wp-flow-plus-addons'). "</p>";	
			}
		}

		/*
		** Validate the circular carousel option
		*/
		if (isset($_POST['wpimageflow2_circular']) && ($_POST['wpimageflow2_circular'] == 'circular')) {
			$options['circular'] = true;
		} else {
			$options['circular'] = false;
		}
		
		/*
		** Validate the slide-up captions option
		*/
		if (isset($_POST['wpimageflow2_slide_up']) && ($_POST['wpimageflow2_slide_up'] == 'slide_up')) {
			$options['slide_up'] = true;
		} else {
			$options['slide_up'] = false;
		}
		
		/*
		** Validate the focus setting
		*/
		if (isset($_POST['wpimageflow2_focus'])) {
			$focus = intval($_POST['wpimageflow2_focus']);
			if ( ((string) $focus == $_POST['wpimageflow2_focus']) &&
				  ($focus >= 1) && ($focus <= 4) ) {
					$options['focus'] = $focus;
			} else {
				$error_count++;
				$errors .= "<p>".__('Invalid focus, must be an integer between 1 and 4. Not saved.','wp-flow-plus')."</p>";	
			}			
		}
		
		/*
		** Done validation, update whatever was accepted
		*/
		$this->settings_update_save ($options, $errors, $error_count);
	}
	
	function settings_update_save($options, $errors = '', $error_count = 0) {
		update_option($this->adminOptionsName, $options);
		if ($errors == '') {
			echo "<div id='message' class='updated'>";	
			echo '<p>'.__('Settings were saved.','wp-flow-plus-addons').'</p></div>';	
		} else {
			echo "<div id='message' class='error'>" . $errors;	
			if ($error_count == 1) {
				echo '<p>'.__('The above setting was not saved.','wp-flow-plus-addons');
			} else {
				echo '<p>'.__('The above settings were not saved.','wp-flow-plus-addons');
			}
			echo __(' Other settings were successfully saved.','wp-flow-plus-addons').'</p></div>';
		}
	}
	
}

	$wpfp_bonus_settings = new WPFP_Bonus_Settings();
}


?>