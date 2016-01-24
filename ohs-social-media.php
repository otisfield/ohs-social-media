<?php
/*
Plugin Name: OHS Social Media Plugin
Description: Enables the OHS API
Author: Derek Dorr
Version: 0.1.0
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

register_activation_hook( __FILE__, 'ohs_social_media_activate' );

add_action( 'admin_menu', 'ohs_social_media_menu' );

function ohs_social_media_menu() {
	add_options_page( 'OHS Social Media', 'OHS Social Media', 'manage_options', 'ohs-social-media-admin-page', 'ohs_social_media_admin_page'  );
}

function ohs_social_media_admin_page() {
	
	if (!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	$opt_name = 'ohs_social_media';
	$hidden_field_name = 'ohs_submit_hidden';
	$data_field_name = 'ohs_social_media';
	
	$opt_val = get_option( $opt_name );
	
	if (!isset($opt_val) || $opt_val == '') {
		$opt_val = array();
	}
	
	if ( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] === 'Y') {
		
		$opt_val = $_POST[ $data_field_name ];
		
		foreach ($opt_val as $key => $value) {
			if ($value == '') {
				unset($opt_val[$key]);
			} else {
				$opt_val[$key] = filter_var(str_replace(array('<','>'),'',$value), FILTER_SANITIZE_MAGIC_QUOTES);
			}
		}
		
		update_option( $opt_name, $opt_val );
		
		echo '<div class="updated"><p><strong>' . __( 'Settings saved.', 'ohs-social-media' ) . '</strong></p></div>';
		
	}
	
	$social_media_array = array(
		'swarm', 
		'istock', 
		'yammer', 
		'ello', 
		'stackoverflow', 
		'persona', 
		'triplej', 
		'rss',
		'paypal', 
		'airbnb', 
		'periscope', 
		'outlook', 
		'goodreads',
		'slideshare',
		'disqus',
		'whatsapp',
		'patreon',
		'mail',
		'blogger',
		'reddit',
		'wikipedia',
		'github',
		'twitter',
		'facebook',
		'googleplus',
		'pinterest',
		'foursquare',
		'yelp',
		'linkedin',
		'myspace',
		'youtube',
		'vimeo',
		'vine',
		'flickr',
		'instagram',
		'wordpress'
	);
	
	sort($social_media_array);
	
	echo '<div class="wrap">';
	echo '<h2>' . __( 'OHS Social Media', 'ohs-social-media') . '</h2>';
	echo '<form name="ohs_social_media_form" method="post" action="">';
	echo '<input type="hidden" name="' . $hidden_field_name . '" value="Y">';
	foreach ($social_media_array as $social) {
		echo '<p><span style="display:inline-block;width:100px;">' . __(ucfirst($social), 'ohs-social-media') . ':</span> <input type="text" name="ohs_social_media[' . $social . ']" value="' . (isset($opt_val[$social]) ? $opt_val[$social] : '') . '" placeholder="Enter URL"></p>';
	}
	echo '<p class="submit">';
	echo '<input type="submit" name="Submit" class="button-primary" value="' . __('Save Changes') . '">';
	echo '</p>';
	echo '</form>';
	echo '</div>';
}

function get_social_media_link($name) {
	$social_media_links = get_option('ohs_social_media');
	
	if(isset($social_media_links[$name])) {
		return $social_media_links[$name];
	} else {
		return false;
	}
}

/**
 * API
 */

function ohs_social_media_activate() {
	flush_rewrite_rules();
}

add_action( 'rest_api_init', 'ohs_social_media_register_api_hooks' );

function ohs_social_media_register_api_hooks() {
	$namespace = 'ohs/v1';
	
	register_rest_route( $namespace, '/social/', array(
		'methods' => 'GET',
		'callback' => 'ohsapi_get_social_media'
	) ); 
	
}

function ohsapi_get_social_media() {
	
	$social_media = get_option('ohs_social_media');
	$social_media_array = array();
	
	foreach ($social_media as $key => $value) {
		$social_media_array[] = array(
			'name' => $key,
			'url' => $value
		);
	}
	
	return $social_media_array;
}

?>
