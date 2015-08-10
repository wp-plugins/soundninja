<?php
/*
Plugin Name: Soundninja 
Plugin URI:  http://soundninja.com/
Description: The embedded player Spotify wish they made
Version:     0.1.2
Author:      Web Three
Author URI:  http://soundninja.com/
Copyright (C) 2015  Web Three Inc.
*/

$soundninja_plugin_slug = 'soundninja';

add_action('wp_enqueue_scripts', 'soundninja_enqueue');
// enqueue soundninja javascript files in frontend
function soundninja_enqueue_init() {
	wp_enqueue_script('soundninja_settings', // id
	    'http://soundninja.github.io/demos/build/settings/' . get_option('soundninja_client_id') . '.js', // path
	    array('jquery','soundninja'), // dependencies
	    0, // appends ?ver=$wordpress_version
	    true // in_footer
	);

	wp_enqueue_script('soundninja', // id
	    'http://soundninja.github.io/demos/build/Soundninja.min.js', // path
	    array('jquery'), // dependencies
	    0, // appends ?ver=$wordpress_version
	    true // in_footer
	);
}

function soundninja_enqueue($hook) {
    $type = get_post_type();

    // checking whether page or post
    if(is_page() || is_singular('post')) {
    	$option = ($type=='post') ?  get_option('soundninja_show_on_posts') : get_option('soundninja_show_on_pages');
      	if(in_array(get_the_ID(),$option) || in_array('all',$option)) {
        	soundninja_enqueue_init();
      	}
    }
    // checking whether home or front page
 	elseif(get_option('soundninja_show_on_home') && (is_home() || is_front_page())){
    	soundninja_enqueue_init();
  	}
}


// adding sub menu "Soundninja" under Settings menu
add_action('admin_menu', 'admin_ninja_menu');
function admin_ninja_menu() {
	global $soundninja_plugin_slug;
	add_submenu_page('options-general.php', 'Soundninja Settings', 'Soundninja', 'manage_options', $soundninja_plugin_slug, 'soundninja_settings_page');
}

// Soundninja Settings page
function soundninja_settings_page() {
?>
	<!-- select all options of dropdown -->
	<script type="text/javascript">
	function dropdown_change( obj ) {
		var isAllSelected = jQuery(obj).find("option:first-child").prop('selected');
		if (isAllSelected)
			jQuery(obj).find("option:not(:first-child)").prop('selected', false);
		else
			jQuery(obj).find("option:first-child").prop('selected', false);
	}
	</script>
	<div class="wrap">
		<h2>Soundninja Settings</h2>
		<form method="post" action="options.php" id="soundninja_options">
      		<?php settings_fields( 'soundninja-settings-group' ); ?>
      		<?php do_settings_sections( 'soundninja-settings-group' ); ?>

			  <table class="form-table">
				<tr valign="top">
				  <th scope="row"><?php _e( 'Site ID', 'soundninja-plugin' ) ?>:</th>
				  <td>
				  <textarea name="soundninja_client_id" cols="85" rows="1"><?php echo esc_attr( get_option('soundninja_client_id') ); ?></textarea>
				  </td>
				</tr>

				<tr valign="top">
				  <th scope="row"><?php _e( 'Access Token', 'soundninja-plugin' ) ?>:</th>
				  <td>
					<textarea name="soundninja_access_token" cols="85" rows="2"><?php echo esc_attr( get_option('soundninja_access_token') ); ?></textarea>
				  </td>
				</tr>
				<tr>
				  <td colspan="2"><p>Select the posts and pages to run Soundninja on</p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Select Posts', 'soundninja-plugin' ) ?>:</th>
					<td>
						<?php $selected = get_option('soundninja_show_on_posts'); ?>

						<select multiple style="width:200px;height:300px" name="soundninja_show_on_posts[]" onchange="dropdown_change(this);">
							<option <?php echo (is_array($selected) && in_array('all',$selected)) ? ' selected="selected" ' : ''; ?>  value="all">All posts</option>
						<?php
							// loading all posts and showing in dropdown
							query_posts( 'posts_per_page=-1' );
							while ( have_posts() ) : the_post();
								$id =  get_the_ID();
								$selected_option  = (is_array($selected) && in_array($id,$selected)) ? ' selected="selected" ' : '';
						?>
						  		<option value="<?php echo $id ?>" <?php echo $selected_option; ?>><?php the_title(); ?></option>
						<?php endwhile; ?>
						</select>
						<br/><i><?php _e('Press Ctrl to select multiple items', 'soundninja-plugin' ); ?></i>

						<?php wp_reset_query(); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Select Pages', 'soundninja-plugin' ) ?>:</th>
					<td>
						<?php $selected = get_option('soundninja_show_on_pages'); ?>

						<select multiple style="width:200px;height:300px" name="soundninja_show_on_pages[]" onchange="dropdown_change(this);">
							<option <?php echo (is_array($selected) && in_array('all',$selected)) ? ' selected="selected" ' : ''; ?> value="all">All pages</option>
						<?php
							// loading all pages and showing in dropdown
							query_posts( 'posts_per_page=-1&post_type=page' );
							while ( have_posts() ) : the_post();
								$id =  get_the_ID();
								$selected_option  = (is_array($selected) && in_array($id,$selected)) ? ' selected="selected" ' : '';
						?>
						  		<option value="<?php echo $id ?>" <?php echo $selected_option; ?>><?php the_title(); ?></option>
						<?php endwhile; ?>
						</select>
						<br/><i><?php _e('Press Ctrl to select multiple items', 'soundninja-plugin' ); ?></i>

						<?php wp_reset_query(); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Show on home page', 'soundninja-plugin' ) ?>:</th>
					<td>
  						<input type="checkbox" id="show_home" name="soundninja_show_on_home" value="1" <?php echo checked(1, get_option('soundninja_show_on_home'), false); ?> />
  						<label for="show_home"> <?php _e('Run Soundninja on Homepage', 'soundninja-plugin' ); ?></label>
					</td>
				</tr>
			  </table>
			  <p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p>
		</form>
	</div>

<?php
}


add_action('admin_init', 'register_ninja');
function register_ninja() {
	// registering settings

	register_setting('soundninja-settings-group', 'soundninja_client_id');
	register_setting('soundninja-settings-group', 'soundninja_access_token');
    register_setting('soundninja-settings-group', 'soundninja_show_on_posts');
    register_setting('soundninja-settings-group', 'soundninja_show_on_pages');
  	register_setting('soundninja-settings-group', 'soundninja_show_on_home');
}

//Add the filter with your plugin information
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'soundninja_plugin_action_links' );

//The callback function to add the settings link
function soundninja_plugin_action_links( $links ) {
	global $soundninja_plugin_slug;
	return array_merge(
    	array(
      	'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $soundninja_plugin_slug ) . '">' . __( 'Settings', 'soundninja-plugin' ) . '</a>'
    	),
    	$links
  	);
}