<?php
/*
Plugin Name: Soundninja 
Plugin URI:  http://soundninja.com/
Description: The Wordpress plug-in Spotify wish they made
Version:     0.1.7
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
    
    add_filter( 'script_loader_tag', function ( $tag, $handle ) {
    if ( 'soundninja' !== $handle )
        return $tag;
    return str_replace( ' src', ' id="soundninja" src', $tag );
    }, 10, 2 );
}

function soundninja_enqueue($hook) {
    $type = get_post_type();

    // checking whether page or post
    //
    // Sergey aka Takereal. 2015-10-20 update. By categories.
    //
    if (is_singular('post') && $type=='post')  {

      $option = get_option('soundninja_show_on_categories');

      if(!empty($option)) {

        foreach(wp_get_post_categories(get_the_ID()) as $category_id) {

          if(in_array($category_id, $option)) {

            soundninja_enqueue_init();
            break;
          }
        }
      }

    }
    elseif(is_page()) {
    	$option = get_option('soundninja_show_on_pages');
      	if(!empty($option) && in_array(get_the_ID(),$option) || !empty($option) && in_array('all',$option)) {
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
        function dropdown_change(obj) {
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
                            <th scope="row">
                                <?php _e( 'Site ID', 'soundninja-plugin' ) ?>:</th>
                            <td>
                                <textarea name="soundninja_client_id" cols="85" rows="1">
                                    <?php echo esc_attr( get_option('soundninja_client_id') ); ?>
                                </textarea>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <?php _e( 'Access Token', 'soundninja-plugin' ) ?>:</th>
                            <td>
                                <textarea name="soundninja_access_token" cols="85" rows="2">
                                    <?php echo esc_attr( get_option('soundninja_access_token') ); ?>
                                </textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <?php

//
// Sergey aka Takereal update begins here.
//
?>
                                <th scope="row">
                                    <?php _e( 'Categories', 'soundninja-plugin' ) ?>:</th>
                                <td>
                                    <?php _e( 'Choose which post categories to use Soundninja on:' ); ?>
                                        <br/>
                                        <ul style="list-style: none;">
                                            <?php $selected = get_option('soundninja_show_on_categories','no'); ?>
                                                <?php
						$categories = get_categories(array('orderby' => 'name', 'parent' => 0));
						foreach ($categories as $category) {
							// loading all categories and showing in dropdown
							$id = $category->cat_ID;
							$selected_option  = (is_array($selected) && in_array($id,$selected)) ? ' checked="checked" ' : '';
						?>

                                                    <li style="width: auto; float: left; margin-right: 15px;">
                                                        <input class="ncheckbox" type="checkbox" name="soundninja_show_on_categories[]" value="<?php echo $id; ?>" ctitle="<?php echo $category->category_nicename; ?>" <?php echo $selected_option; ?> />
                                                        <?php echo $category->category_nicename; ?>
                                                    </li>

                                                    <?php } ?>
                                        </ul>
                                </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <?php _e( 'Pages', 'soundninja-plugin' ) ?>:</th>
                            <td>
                                <?php _e( 'Choose which pages to use Soundninja on:' ); ?>
                                    <br/>
                                    <ul style="list-style: none;">
                                        <?php $selected = get_option('soundninja_show_on_pages','a'); ?>
                                            <?php
							// loading all pages and showing in dropdown
							query_posts( 'posts_per_page=-1&post_type=page&orderby=title&order=ASC' );
							while ( have_posts() ) : the_post();
								$id =  get_the_ID();
								$selected_option  = (is_array($selected) && in_array($id,$selected)) ? ' checked="checked" ' : '';
						?>
                                                <li style="width: auto; float: left; margin-right: 15px;">
                                                    <input class="ncheckboxpage" type="checkbox" name="soundninja_show_on_pages[]" value="<?php echo $id ?>" ctitle="<?php the_title(); ?>" <?php echo $selected_option; ?> />
                                                    <?php the_title(); ?>
                                                </li>
                                                <?php endwhile; ?>

                                                    <?php wp_reset_query(); ?>
                                    </ul>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( 'Show on home page', 'soundninja-plugin' ) ?>:</th>
                            <td>

                                <input type="checkbox" id="show_home" name="soundninja_show_on_home" value="1" <?php echo checked(1, get_option( 'soundninja_show_on_home'), false); ?> />
                                <label for="show_home">
                                    <?php _e('Run Soundninja on Homepage', 'soundninja-plugin' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit">
                    </p>
        </form>
    </div>

    <?php
}


add_action('admin_init', 'register_ninja');
function register_ninja() {
	// registering settings

	register_setting('soundninja-settings-group', 'soundninja_client_id');
	register_setting('soundninja-settings-group', 'soundninja_access_token');
register_setting('soundninja-settings-group', 'soundninja_show_on_categories');	// Sergey aka Takereal 2015-10-20
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