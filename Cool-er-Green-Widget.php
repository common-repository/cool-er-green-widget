<?php
/*
Plugin Name: Cool-er Green Widget
Description: Adapted from Simple Image Widget.  Using this widget you can easily place the Cool-er Green with an image of the cover of the book you are reading and link in the sidebar. The book cover image will show up framed by the Cool-er Green.  Supports multiple instances, so you can use it multiple times in multiple sidebars. 
Version: 1.2
Author: Chris Vickio, Cool-er Green Widget Adaptation by RagingKitty.com
Author URI: http://www.kittyridge.com/freebies/
*/
?>
<?php
/*	Copyright 2008	Chris Vickio	(email : chris@vickio.net)   
		
		Cool-er Green Widget adapted 12/2010 by RagingKitty.com from Chris Vickio's Simple Image Widget.

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA	 02110-1301	 USA
		
		=== Cool-er Green Widget ===
		Contributors: vickio, Raging Kitty
		Donate link: http://www.kittyridge.com/freebies/
		Tags: image, sidebar, widget, photo, picture, book, cover, Cool-er, Green, ereader, ebook, book, read, novel
		Requires at least: 2.5
		Tested up to: 2.9.2
		Stable tag: 1.2
		
		The simple way to show what your reading on your Cool-er Green or e-reader.

		== Description ==
		
		Using this widget you can easily place the Cool-er Green containing an image of the cover of the book you are 			
		reading in the sidebar. You can also specify a URL to link to when clicking on the book cover image. 
		Supports multiple instances, so you can use it multiple times in multiple sidebars. (adapted from the
		Simple Image Widget)
		
		Once the plugin is enabled, the widget will be available in your widgets list as "Cool-er Green Widget". You 
		can add this widget to sidebars as many times as you need. The control interface allows you to 
		specify the following options for each instance of the widget:
		
		* Image URL: The full URL to the image file
		* Alternate Text: Shown by the browser if image cannot be displayed
		* Link URL: URL to open when the book cover image is clicked on (optional)
		* Open link in new window: If this is checked, the above link URL will open in a new browser window
		
		== Installation ==
		
		Installation is very simple:
		
		1. Copy/upload the `Cool-er-Green-Widget` folder to your `/wp-content/plugins/` directory
		1. Activate the plugin through the 'Plugins' menu in WordPress
		1. Add the "Cool-er Green Widget" plugin to a sidebar in 'Design' -> 'Widgets'
		
		== Frequently Asked Questions ==
		
		= How do I upload an image? =
		
		The Cool-er Green Widget does not provide a mechanism for uploading images or files. You can however upload an 		image in the 'Write' section of Wordpress. From there you can click on the 'Add an Image' icon (next 
		to 'Add Media' label). After uploading an image, copy the 'Link URL' for use in your Cool-er Green Widget.
		
		= How many images can be display? =
		
		Each instance of the widget can only display one image, but you can create as many instances as you 
		need.
		
		== Screenshots ==
		1. Cool-er Green Widget control interface
		2. Cool-er Green Widget in sidebar
*/
?>
<?php
// Displays an image in the sidebar
// $widget_args: number
//		number: which of the several widgets of this type do we mean
function widget_coolergreen( $args, $widget_args = 1 ) {
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	// Data is stored as array:	 array( number => data for that instance of the widget, ... )
	$options = get_option('widget_coolergreen');
	if ( !isset($options[$number]) )
		return;
		
	if ($options[$number]['link']) {
		if ($options[$number]['new_window'])
			$before_image = "<a href=\"".$options[$number]['link']."\" target=\"_blank\">";
		else
			$before_image = "<a href=\"".$options[$number]['link']."\">";

		$after_image = "</a>";
	}
	
	if ($options[$number]['image'])
		$title = preg_replace('/\?.*/', "", basename($options[$number]['image']));
	
?>
	<?php echo $before_widget; ?>
	<div style="display:block;width:145px;height:227px;background:url(http://lh3.ggpht.com/_VsAfsZ_1hVo/S6WyjfW4GsI/AAAAAAAAAGA/_nEqNl4Pgd0/s800/Cool-er-Green.png) no-repeat top; text-align:center;" class="coolergreen">
		<?php // Using HTML comments here, the admin interface is tricked into displaying the title, but it's not actually displayed on the site ?>
		<?php if ( !empty( $title ) ) { echo "<!-- Control Title: " . $before_title . $title . $after_title . " -->"; } ?>
		<?php echo $before_image; ?>
		<p><img style="width:109px;height:149px;background:transparent;border:0;padding:0;margin:26px 18px 52px 18px;" src="<?php echo $options[$number]['image']; ?>" alt="<?php echo $options[$number]['alt']; ?>" /></p>
		<?php echo $after_image; ?>
	</div>
	<?php echo $after_widget; ?>
<?php
}

// Displays form for image and link.	Also updates the data after a POST submit
// $widget_args: number
//		number: which of the several widgets of this type do we mean
function widget_coolergreen_control( $widget_args = 1 ) {
	global $wp_registered_widgets;
	static $updated = false; // Whether or not we have already updated the data after a POST submit

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	// Data is stored as array:	 array( number => data for that instance of the widget, ... )
	$options = get_option('widget_coolergreen');
	if ( !is_array($options) )
		$options = array();

	// We need to update the data
	if ( !$updated && !empty($_POST['sidebar']) ) {
		// Tells us what sidebar to put the data in
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			if ( 'widget_coolergreen' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "coolergreen-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed
					unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['widget-coolergreen'] as $widget_number => $widget_coolergreen ) {
			if ( !isset($widget_coolergreen['image']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
				
			$image = wp_specialchars( $widget_coolergreen['image'] );
			$alt = wp_specialchars( $widget_coolergreen['alt'] );
			$link = wp_specialchars( $widget_coolergreen['link'] );
			$new_window = isset( $widget_coolergreen['new_window'] );
			$options[$widget_number] = compact('image', 'alt', 'link', 'new_window');
		}

		update_option('widget_coolergreen', $options);
		$updated = true; // So that we don't go through this more than once
	}


	// Here we echo out the form
	if ( -1 == $number ) { // We echo out a template for a form which can be converted to a specific form later via JS
		$image = '';
		$alt = '';
		$link = '';
		$new_window = '';
		$number = '%i%';
	} else {
		$image = attribute_escape($options[$number]['image']);
		$alt = attribute_escape($options[$number]['alt']);
		$link = attribute_escape($options[$number]['link']);
		$new_window = attribute_escape($options[$number]['new_window']);
	}

?>
		<p>
			<label for="coolergreen-image-<?php echo $number; ?>">
				<?php _e('Image URL Of Book Cover You Want Displayed In Your Cool-er Green:'); ?>
				<input class="widefat" id="coolergreen-image-<?php echo $number; ?>" name="widget-coolergreen[<?php echo $number; ?>][image]" type="text" value="<?php echo $image; ?>" />
			</label>
		</p>

		<p>
			<label for="coolergreen-alt-<?php echo $number; ?>">
				<?php _e('Alternate Text:'); ?>
				<input class="widefat" id="coolergreen-alt-<?php echo $number; ?>" name="widget-coolergreen[<?php echo $number; ?>][alt]" type="text" value="<?php echo $alt; ?>" />
				<br />
				<small><?php _e( 'Shown if image cannot be displayed' ); ?></small>
			</label>
		</p>

		<p>
			<label for="coolergreen-link-<?php echo $number; ?>">
				<?php _e('Link URL (optional):'); ?>
				<input class="widefat" id="coolergreen-link-<?php echo $number; ?>" name="widget-coolergreen[<?php echo $number; ?>][link]" type="text" value="<?php echo $link; ?>" />
			</label>
		</p>

		<p>
			<label for="coolergreen-new-window-<?php echo $number; ?>">
				<input id="coolergreen-new-window-<?php echo $number; ?>" name="widget-coolergreen[<?php echo $number; ?>][new_window]" type="checkbox" <?php if ($new_window) echo 'checked="checked"'; ?> />
				<?php _e('Open link in new window'); ?>
			</label>
		</p>

		<input type="hidden" id="widget-coolergreen-submit-<?php echo $number; ?>" name="widget-coolergreen[<?php echo $number; ?>][submit]" value="1" />
<?php
}

// Registers each instance of widget on startup
function widget_coolergreen_register() {
	if ( !$options = get_option('widget_coolergreen') )
		$options = array();

	$widget_ops = array('classname' => 'widget_coolergreen', 'description' => __('Display an image'));
	$control_ops = array( 'id_base' => 'coolergreen');
	$name = __('Cool-er Green Widget');

	$registered = false;
	foreach ( array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['image']) )
			continue;

		$id = "coolergreen-$o"; // Never never never translate an id
		$registered = true;
		wp_register_sidebar_widget( $id, $name, 'widget_coolergreen', $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, 'widget_coolergreen_control', $control_ops, array( 'number' => $o ) );
	}

	// If there are none, we register the widget's existance with a generic template
	if ( !$registered ) {
		wp_register_sidebar_widget( 'coolergreen-1', $name, 'widget_coolergreen', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'coolergreen-1', $name, 'widget_coolergreen_control', $control_ops, array( 'number' => -1 ) );
	}
}

// Hook for the registration
add_action( 'widgets_init', 'widget_coolergreen_register' )

?>