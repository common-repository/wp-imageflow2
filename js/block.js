/*
** WP Flow Plus Gutenberg Block
**
** External data:
** wpfp_bonus - true if bonus addons active
** wpfp_cats - category choices
** wpfp_folders - folder choices
** wpfp_nextgen - true if NextGen gallery plugin is active
** wpfp_galleries - NextGen gallery choices
*/	
( function( blocks, editor, i18n, element, components ) {
	var el = element.createElement,
		// createBlock = blocks.createBlock,
		registerBlockType = blocks.registerBlockType,
		BlockControls = wp.blockEditor.BlockControls,
		MediaUpload = wp.blockEditor.MediaUpload,
		InspectorControls = wp.blockEditor.InspectorControls,
		ServerSideRender = wp.serverSideRender,
		PanelBody = components.PanelBody,
		PanelRow = components.PanelRow,
		TextControl = components.TextControl,
		SelectControl = components.SelectControl,
		RangeControl = components.RangeControl,
		CheckboxControl = components.CheckboxControl,
		Toolbar = components.Toolbar,
		Button = components.Button;
	var __ = i18n.__;
		
	const iconEl = el('svg', { width: 512, height: 512 },
	  el ('path', { d: "M2,6.297h1.445v7.852v5H2v-5V6.297z M20.555,6.297v7.852v5H22v-5V6.297H20.555z M4.852,4.857v1.429v7.863v5.078v0.925v1.146 	h1.445v-1.146v-0.925v-5.078V6.286V4.857V3.445H4.852V4.857z M17.703,4.857v1.429v7.863v5.078v0.925v1.146h1.445v-1.146v-0.925 	v-5.078V6.286V4.857V3.445h-1.445V4.857z M7.703,3.429v1.428v1.429v7.863v5.078v0.925v0.925V22h8.594v-0.924v-0.925v-0.925v-5.078 V6.286V4.857V3.429V2H7.703V3.429z" } )
	);

	/*
	** Define our options
	*/
	var wpfp_source = [
		{ value: 'attached', label: __('Attachments to a page/post') },
		{ value: 'featured', label: __( 'Featured posts' ) },
		{ value: 'folder', label: __( 'A subfolder' ) },
		{ value: 'medialib', label: __( 'Media Library' ) },
	];
	if (wpfp_nextgen) {
		var new1 = { value: 'nextgen', label: __( 'NextGen Gallery' ) };
		wpfp_source.push(new1);
	}

	var wpfp_order = [
		{ value: '', label: '' },
		{ value: 'desc', label: __( 'DESC' ) },
		{ value: 'asc', label: __( 'ASC' ) },
	];
	
	var wpfp_orderby = [
		{ value: '', label: '' },
		{ value: 'menu_order', label: __( 'menu_order' ) },
		{ value: 'title', label: __( 'title' ) },
		{ value: 'post_date', label: __( 'post_date' ) },
		{ value: 'rand', label: __( 'rand' ) },
		{ value: 'id', label: __( 'id' ) },
	];
	
	var wpfp_samewindow = [
		{ value: '', label: '' },
		{ value: 'true', label: __( 'Same Window' ) },
		{ value: 'false', label: __( 'New Window' ) },
	];
	
	var wpfp_rotate = [
		{ value: '', label: '' },
		{ value: 'on', label: __( 'On' ) },
		{ value: 'off', label: __( 'Off' ) },
	];
	
	var wpfp_style = [
		{ value: 'baseline', label: __( 'Bottom Aligned' ) },
		{ value: 'topline', label: __( 'Top Aligned' ) },
		{ value: 'midline', label: __( 'Vertical Centered' ) },
		{ value: 'angled', label: __( 'Angled' ) },
		{ value: 'flip', label: __( 'Flipbook' ) },
		{ value: 'explode', label: __( 'Exploding' ) },
	];
	
	var wpfp_truefalse = [
		{ value: '', label: '' },
		{ value: 'true', label: __( 'True' ) },
		{ value: 'false', label: __( 'False' ) },
	];

	var wpfp_captions = [
		{ value: '', label: '' },
		{ value: 'slide-up', label: __( 'Slide-Up' ) },
	];
	
	/*
	 * Register the block in JavaScript.
	 */
	// blocks.registerBlockType( 'wpfp/main-block', {
	registerBlockType( 'wpfp/main-block', {
		title: __('WP Flow Plus Block'),
		icon: iconEl,
		category: 'common',
		supports: { html: false },

		/*
		 * In most other blocks, you'd see an 'attributes' property being defined here.
		 * We've defined attributes in the PHP, that information is automatically sent
		 * to the block editor, so we don't need to redefine it here.
		 */

		edit: function( props ) {
				var onSelectIncludes = function( media ) {
					var sel_media = '';
					jQuery.each(media, function (index, value) {
						if (sel_media != '') sel_media += ',';
						sel_media += value.id;
					});
					return props.setAttributes( { include: sel_media } );
				};
				var onSelectExcludes = function( media ) {
					var sel_media = '';
					jQuery.each(media, function (index, value) {
						if (sel_media != '') sel_media += ',';
						sel_media += value.id;
					});
					return props.setAttributes( { exclude: sel_media } );
				};
				// var onRefresh = function() {
					// console.log('time to resize');
					// var flowplus_1 = new flowplus(1);
					// flowplus_1.init( {autoRotate: "on", autoRotatepause: 3000, startImg: 3, sameWindow: false, aspectRatio: 1.9, reflectPC: 1, focus: 2, sliderStyle: "midline"} );
					// flowplus_1.refresh(true);
				// };
				// onRefresh();
				
				var isAttached = ( props.attributes.source === 'attached' );
				var isMedialib = ( (props.attributes.source != 'folder') && (props.attributes.source != 'nextgen') );
				var isFeatured = ( props.attributes.source === 'featured' );
				var isFolder = ( props.attributes.source === 'folder' );
				var isNextGen = ( props.attributes.source === 'nextgen' );

				return [
				/* Rendering */
				el( ServerSideRender, {
					block: 'wpfp/main-block',
					attributes: props.attributes,
				} ),

			
				/* Block sidebar */
				el( InspectorControls, {},
					el( SelectControl, { 
						label: __('Where are the images coming from?'),
						value: props.attributes.source,
						onChange: ( value ) => { props.setAttributes( { source: value } ); },
						options: wpfp_source,
					} ),
					isAttached && el( TextControl, {
						label: __('Page/Post ID'),
						value: props.attributes.post_id,
						onChange: ( value ) => { props.setAttributes( { post_id: value } ); },
					} ),
					isFeatured && el( SelectControl, { 
						label: __('Select a category'),
						multiple: false,
						value: props.attributes.category,
						onChange: ( value ) => { props.setAttributes( { category: value } ); },
						options: wpfp_cats,
					} ),
					isFolder && el( SelectControl, { 
						label: __('Select a folder'),
						multiple: false,
						value: props.attributes.dir,
						onChange: ( value ) => { props.setAttributes( { dir: value } ); },
						options: wpfp_folders,
						help: __('Configure the gallery path on the WP Flow Plus settings page'),
					} ),
					isNextGen && el( SelectControl, { 
						label: __('Select a gallery'),
						multiple: false,
						value: props.attributes.ngg_id,
						onChange: ( value ) => { props.setAttributes( { ngg_id: value } ); },
						options: wpfp_galleries,
					} ),
					isMedialib && el ( PanelBody, {
						title: __('Media Library Image Options'),
						},
						el( SelectControl, { 
							label: __('Order'),
							value: props.attributes.order,
							onChange: ( value ) => { props.setAttributes( { order: value } ); },
							options: wpfp_order,
						} ),
						el( SelectControl, { 
							label: __('Order By'),
							value: props.attributes.orderby,
							onChange: ( value ) => { props.setAttributes( { orderby: value } ); },
							options: wpfp_orderby,
						} ),
						el ( PanelRow, {}, el( MediaUpload, {
							onSelect: onSelectIncludes,
							type: 'image',
							multiple: true,
							value: (props.attributes.include) ? props.attributes.include.split(',') : '',
							render: function( obj ) {
								return el( components.Button, {
										className: 'button button-large',
										onClick: obj.open
									},
									__( 'Include Images' ) 
								);
							}
						} ) ),
						el ( PanelRow, {}, el( MediaUpload, {
							onSelect: onSelectExcludes,
							type: 'image',
							multiple: true,
							value: (props.attributes.exclude) ? props.attributes.exclude.split(',') : '',
							render: function( obj ) {
								return el( components.Button, {
										className: 'button button-large',
										onClick: obj.open
									},
									__( 'Exclude Images' ) 
								);
							}
						} ) ),
						el ( PanelRow, {}, el( SelectControl, { 
							label: __('Where should image links open?'),
							value: props.attributes.samewindow,
							onChange: ( value ) => { props.setAttributes( { samewindow: value } ); },
							options: wpfp_samewindow,
						} ) ),
					),
					 el ( PanelBody, {
						title: __('General Options'),
						},
						el( TextControl, {
							label: __('Starting Image'),
							type: 'number',
							value: props.attributes.startimg,
							onChange: ( value ) => { props.setAttributes( { startimg: value } ); },
						} ),
						el( SelectControl, { 
							label: __('Automatic Rotation'),
							value: props.attributes.rotate,
							onChange: ( value ) => { props.setAttributes( { rotate: value } ); },
							options: wpfp_rotate,
						} ),
						wpfp_bonus && el( SelectControl, { 
							label: __('Carousel Style'),
							value: props.attributes.style,
							onChange: ( value ) => { props.setAttributes( { style: value } ); },
							options: wpfp_style,
						} ),
						el( CheckboxControl, {
							label: __('Hide Captions'),
							checked: props.attributes.nocaptions,
							onChange: ( value ) => { props.setAttributes( { nocaptions: value } ); },
						} ),
						wpfp_bonus && el( SelectControl, { 
							label: __('Caption Style'),
							value: props.attributes.captions,
							onChange: ( value ) => { props.setAttributes( { captions: value } ); },
							options: wpfp_captions,
						} ),
					),
				),
			];
		},

		// We're going to be rendering in PHP, so save() can just return null.
		save: function() {
			return null;
		},		
	} );
}(
	window.wp.blocks,
	window.wp.editor,
	window.wp.i18n,
	window.wp.element,
	window.wp.components,
) );

