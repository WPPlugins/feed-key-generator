<?php
/*
Plugin Name: Feed Key Generator
Description: Generates feed keys for private blogs using the "Network Privacy" plugin
Version: 1.0.8
Author: Aleksandar Arsovski
License: GPL2
*/

/*  Copyright 2011  Aleksandar Arsovski  (email : alek_ars@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Internationalization setup
$wpfkg_plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'feed-key-generator', false, $wpfkg_plugin_dir );

// Get site URL and title
$site_url = get_bloginfo( 'url' );
$site_title = get_bloginfo( 'title' );

// Set feedkey_valid variable to false initially
// Used for feed redirection conditional statement
$feedkey_valid = FALSE;

// Array of error messages for invalid/missing feeds
$feed_error_messages = array(
	'feedkey_invalid' => __( 'The Feed Key you used is incorrect or it has been replaced by an administrator.
							Administrators can find the valid Feed Key on the "Privacy Settings" page.', 'feed-key-generator' ),
	'feedkey_missing' => __( 'You need to use a Feed Key to access feeds on this site. Administrators can find
							the valid Feed Key on the "Privacy Settings" page.', 'feed-key-generator' ),
	'feedurl_notgen' => __( 'URL is available once a Feed Key has been generated', 'feed-key-generator' )
);


// Add action for redirection of feeds dependant on whether key is active or not
add_action( 'template_redirect', 'wpfkg_feed_redirection' );

// For main feed key generator function
add_action( 'init', 'wpfkg_feed_key_gen_init' );

// For setup of settings
add_action( 'admin_init', 'wpfkg_feed_key_generator_admin_init' );

// Active/unactive status of feed key option
add_option( 'wpfkg_feed_key_status' );

// For deactivation of feed key when plugin is disabled
register_deactivation_hook( __FILE__, 'wpdkg_feed_key_deactivate' );


/** Settings set up
 *
 * Setting up a settings section in the Privacy Settings page on the Dashboard
 * 
 * @return void
 */
function wpfkg_feed_key_generator_admin_init() {
	
	// Register the feed key's status option with the privacy section
	register_setting(
		'privacy',
		'wpfkg_feed_key_status',
		'wpfkg_feed_key_status_validate'
	);
	
	// Set up the feed key options section under the Privacy Settings
	add_settings_section(
		'feedkey_options_section',
		__( 'Feed Key Generator', 'feed-key-generator' ),
		'feedkey_options_section_text',
		'privacy'
	);
	
	// Set up the feed key options field
	add_settings_field(
		'feedkey_options',
		__( 'Feed Key', 'feed-key-generator' ),
		'wpfkg_feed_key_options_function',
		'privacy',
		'feedkey_options_section'
	);
	
	/**********************************************************************/
	// Deleting options with bad option names from version 1.0.1 and earlier
	$plugin_data = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data[ 'Version' ];
	
	if( $plugin_version != '1.0.0' && $plugin_version != '1.0.1' ) {
		// Check if feed_key is still in DB
		if( get_option( 'feed_key' ) ) {
			// Delete old feed key option stored in DB 
			delete_option( 'feed_key' );
		}
		// Check if feed_key_status is still in DB
		if( get_option( 'feed_key_status' ) ) {
			// Delete old feed key status option stored in DB
			delete_option( 'feed_key_status' );
		}
	}
	/**********************************************************************/
}


/** Random feed key generator function
 *
 * Generating a random feed key, hashing it and then returning it
 * 
 * @return string	$feedkey	Returns the feed key
 */
function wpfkg_generate_feed_key() {

	// Defining variables
	$feedkey ='';
	$salt ='';

	// Character set to be used
	$character_set = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	
	//Length of character set
	$character_set_length = strlen( $character_set );
	
	// Generating a random "feedkey" character set using the existing character set
	for ( $i = 0; $i < $character_set_length; $i++ ) {
		$feedkey .= $character_set[ mt_rand( 0, $character_set_length - 1 ) ];
	}
	
	// Generating a random "salt" character set using the existing character set
	for ( $i = 0; $i < $character_set_length; $i++ ) {
		$salt .= $character_set[ mt_rand( 0, $character_set_length - 1 ) ];
	}
	
	// Hashing the combined set of the "feedkey" and the "salt"
	$feedkey = sha1( $salt . $feedkey );

	// Return the hashed feed key
	return $feedkey;
}


/** Options section description text
 *
 * Echoes the options section description text
 * 
 * @return void
 */
function feedkey_options_section_text() {
	echo '<span class="description">' . __( 'Generate a feed key for your private site/blog to 
		  protect your feed. NOTE: An ACTIVE feed key is presented as a link.', 'feed-key-generator' ) . '</span>';
}


/** Settings field function
 *
 * Deals with settings such as saving activation
 * status options and resetting the feed key
 * 
 * @return void
 */
function wpfkg_feed_key_options_function() {

	global $feed_error_messages;
	
	// Get the feed key from database
	$feedkey = get_option('wpfkg_feed_key');
	
	// Get the status of the feed key (active/inactive)
	$feed_key_status = get_option('wpfkg_feed_key_status');
	
	// Get the privacy level of the site/blog
	$privacy = get_option('blog_public');
	
	// Feed key status values array
	$feedkey_status_options = array(
		__( 'Activate Feed Key', 'feed-key-generator' ) => 'feedkey-active',
		__( 'Deactivate Feed Key', 'feed-key-generator' ) => 'feedkey-unactive'
	);
	
	$feedkey_activation_options = '';
	
	// Set up the dropdown menu
	foreach ( $feedkey_status_options as $option => $value ) {
		
		// Conditional for determining which dropdown option should be selected by default in each scenario
		// Keep "Activate Feed Key" selected if feed key status is not set in the database yet
		if( $feed_key_status == "" && $value == 'feedkey-active' ) {
			$selected = 'selected';
		}
		// Keep "Activate Feed Key" selected if feed key status is active
		elseif( $feed_key_status == 'feedkey-active' && $value == 'feedkey-active' ) {
			$selected = 'selected';
		}
		// Keep "Deactivate Feed Key" selected if feed key status is unactive
		elseif( $feed_key_status == 'feedkey-unactive' && $value == 'feedkey-unactive' ) {
			$selected = 'selected';
		}
		// Other cases are irrelevant
		else
			$selected = ''; // end of if...
		
		// Set up the dropdown option tags
		$feedkey_activation_options .= "\n\t<option value='$value' $selected>$option</option>";

	} // end of foreach...
	
	// If "Reset Key" button is clicked while the feed key is active, generate new feed key and update database
	if( isset( $_GET[ 'reset' ] ) && $_GET[ 'reset' ] == $feedkey && $feed_key_status == 'feedkey-active' ) {
		$feedkey = wpfkg_generate_feed_key();
		update_option( 'wpfkg_feed_key', $feedkey );
	}
	
	// If site/blog is private, allow for feed key options
	if( $privacy < 0 ) { ?>
		<table class="form-table">
			<tr>
				<td>
					<?php empty($feedkey) ? _e( $feed_error_messages[ 'feedurl_notgen' ] ) : wpfkg_provide_feed_link(); // if feed key exists, go to feed link generator function?>
				</td>
			</tr>
			<tr>
				<td>
					<select name="wpfkg_feed_key_status" id="wpfkg_feed_key_status" ><?php echo $feedkey_activation_options ?></select><br />
					<span class="description"><?php _e( 'Select option and click on "Save Changes" to activate or deactivate feed key', 'feed-key-generator' ) ?></span>
				</td>
			</tr>
			<tr>
				<td><?php
					// Generate reset link front
					$reset_front = admin_url( 'options-privacy.php' );
					// Generate reset link ending
					$reset_append = '?reset=' . $feedkey;
					
					// If feed key's active generate complete link; if feed key's inactive then simply link back to options page
					if( $feed_key_status == 'feedkey-active' ) {
						// Generate complete link
						$reset_link = ( $reset_front . $reset_append );
						// Add nonce to URL
						$reset_link = ( function_exists( 'wp_nonce_url' ) ) ? wp_nonce_url( $reset_link, 'feedkeygenerator-mainoptions-reset' ) : $reset_link;
					}
					else
						$reset_link = $reset_front;
						
					// When "Reset Key" button is clicked and feed key is active, reset the feed key ?>
					<a href="<?php echo $reset_link ?>" class="button" name="reset"><?php _e( 'Reset Key', 'feed-key-generator' ) ?></a><br />
					<span class="description"><?php _e( 'Click button while feed key is ACTIVE to generate NEW feed key<br />WARNING: Old feed key will be overwritten!', 'feed-key-generator' ) ?></span>
				</td>
			</tr>
		</table><?php
	}
	// If site/blog is not private, give the following message
	else{ 
		// If site/blog is not private set feed key as unactive
		update_option( 'wpfkg_feed_key_status', 'feedkey-unactive'); ?>
		<table class="form-table">
			<tr>
				<td>
					<span class="description">
						<?php _e( 'A feed key will be generated for private sites and blogs.
						Private sites and blogs are defined as having a lower site
						visibility than the "I would like to block search engines,
						but allow normal visitors" option.', 'feed-key-generator' ) ?>
					</span>
				</td>
			</tr>
		</table><?php
	}
}


/** Generate feed URL
 *
 * Called by the main options function to
 * generate a feed URL depending on whether
 * the feed key is active or not 
 * 
 * @return void
 */
function wpfkg_provide_feed_link() {

	global $site_url;
	
	$feedkey = get_option( 'wpfkg_feed_key' );
	$feed_key_status = get_option( 'wpfkg_feed_key_status' );
	$permalink_structure = get_option( 'permalink_structure' );
	
	// Determining the permalink structure to be used
	empty( $permalink_structure ) ? $feedjoin = '?feed=rss2&feedkey=' : $feedjoin = '/feed/?feedkey=';
	
	// The feed URL (with feed key) in plain text
	$feedurl_text = $site_url . $feedjoin . $feedkey;
	
	// The feed URL (with feed key) as a link
	$feedurl = '<a href="' . $feedurl_text . '">' . $feedurl_text . '</a>';
	
	// If the feed key is active, display feed URL as link; otherwise display as a grayed out text
	if( $feed_key_status == 'feedkey-active' ) {
		echo $feedurl; ?><br />
		<span class="description"><?php _e( 'The feed key is active; select "Deactivate Feed Key" and click on "Save Changes" button to deactivate', 'feed-key-generator' ) ?></span> <?php
	}
	else {
		echo "<font color='999999'>$feedurl_text</font>"; ?><br />
		<span class="description"><?php _e( 'The feed key is unactive; select "Activate Feed Key" and click on "Save Changes" button to activate', 'feed-key-generator' ) ?></span><?php
	}
}


/** Feed redirection
 *
 * Redirects improper feed URLs if the
 * feed key is active 
 * 
 * @return void
 */
function wpfkg_feed_redirection()
{
	global $feedkey_valid, $feed_error_messages, $ra_network_privacy;
	
	$feed_key_status = get_option( 'wpfkg_feed_key_status' );
	
	// If URL is a feed and feed key is active...
	if ( is_feed() && $feed_key_status == 'feedkey-active' )
	{
		// If the URL does not have a feed key then redirect to missing URL feed
		if( empty( $_GET[ 'feedkey' ] ) )
		{
			$feed = wpfkg_create_feed( __( 'No Feed Key Found', 'feed-key-generator' ), $feed_error_messages[ 'feedkey_missing' ] );
			header( "Content-Type: application/xml; charset=ISO-8859-1" );
			echo $feed;
			exit;
		}
		// If the URL does not match the feed key in the database redirect to invalid feed key feed
		elseif( $feedkey_valid == FALSE ) 
		{
			$feed = wpfkg_create_feed( __( 'Feed Key is Invalid', 'feed-key-generator' ), $feed_error_messages[ 'feedkey_invalid' ] ) ;
			header( "Content-Type: application/xml; charset=ISO-8859-1" );
			echo $feed;
			exit;
		}
		// Case where URL matches...
		elseif( $feedkey_valid == TRUE )
		{
			// Action set by "RA Network Privacy" plugin needs to be removed
			// for the feed key to work when the user is logged out
			foreach( array( 'rss', 'rss2', 'commentsrss2', 'atom') as $f ) {
				remove_action( $f . '_head', array( $ra_network_privacy, 'feed_authenticator' ) );
			}
			remove_action( 'rdf_header', array( $ra_network_privacy, 'feed_authenticator' ) );
		} // end inner if...
	} // end outer if...
}


/** Feed key status validation function
 *
 * Generates feed key for the first
 * time and stores it in database
 * 
 * @param	string	$feed_key_status	The status of the feedkey (active/unactive)
 * @return	string	$feed_content		Returns the status of the feed key
 */
function wpfkg_feed_key_status_validate($feed_key_status) {

	global $userdata;
	
	$privacy = get_option( 'blog_public' );
	
	// If user is logged in
	if ( !empty( $userdata->ID ) )
	{	
		// If the site/blog is private
		if ( $privacy < 0 ) {
		
			$feedkey = get_option( 'wpfkg_feed_key' );
			
			//If there is no feed key generate one and update database
			if ( empty( $feedkey ) ) {
				$feedkey = wpfkg_generate_feed_key();
				update_option( 'wpfkg_feed_key', $feedkey );
			}
		}	
	}

	return $feed_key_status;
}


/** Init function
 *
 * Checks if feed key is active
 * and valid for the redirection function
 * 
 * @return void
 */
function wpfkg_feed_key_gen_init()
{
	global $feedkey_valid;
	
	$feedkey = get_option( 'wpfkg_feed_key' );
	
	// If there is a feed key in the URL
	if( isset( $_GET[ 'feedkey' ] ) )
	{
		// Compare feed key in URL to feed key in database
		if( $feedkey == $_GET[ 'feedkey' ] )
			$feedkey_valid = TRUE;
	}
}


/** Create feed
 *
 * Generates feed for error feed pages
 * 
 * @param 	string	$item_title			The title of the RSS item
 * @param	string	$item_description	The description of the RSS item
 * @return	string	$feed_content		Returns the generated feed content as a string
 */
function wpfkg_create_feed( $item_title, $item_description )
{	
	global $site_title, $site_url;
	
	$today = date( 'F j, Y G:i:s T' );
	
	$feed_content = '<?xml version="1.0" encoding="ISO-8859-1" ?> 
					<rss version="2.0"> 
						<channel> 
							<title>'.$site_title.'</title>
							<link>'.$site_url.'</link>
							<item>
								<title>'.$item_title.'</title>
								<link>'.$site_url.'</link>
								<description>'.$item_description.'</description>
								<pubDate>'.$today.'</pubDate>
							</item>
						</channel>
					</rss>';
					
	return $feed_content;
}


/** Feed Key Deactivate
 *
 * Sets feed key status to unactive if the plugin is disabled but not deleted
 * 
 * @return	void
 */
function wpdkg_feed_key_deactivate() {
	update_option( 'wpfkg_feed_key_status', 'feedkey-unactive' );
}