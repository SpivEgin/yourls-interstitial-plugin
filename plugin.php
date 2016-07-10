<?php
/*
Plugin Name: Joel’s Interstitial Plugin for YOURLs
Plugin URI: http://joel.gratcyk.com/yourls
Description: Add an interstitial ad before url redirection
Version: 1.0
Author: Ozh
Author URI: http://ozh.org/
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Global variables - default state is "redirect with no interstitial"
global $joegra_inter;
$joegra_inter['do'] = false;
$joegra_inter['keyword'] = '';

// When a redirection to a shorturl is about to happen, register variables to state that an interstitial should be displayed
yourls_add_action( 'redirect_shorturl', 'joegra_inter_add' );
function joegra_inter_add( $args ) {
    global $joegra_inter;
    $joegra_inter['do'] = true;
    $joegra_inter['keyword'] = $args[1];
}

// On redirection, check if this is an interstitial and draw it if needed
yourls_add_action( 'pre_redirect', 'joegra_inter_do' );
function joegra_inter_do( $args ) {
    global $joegra_inter;
    
    // Does this redirection need an interstitial? IF no, exit here and resume normal flow of operations
    if( !$joegra_inter['do'] )
        return;

    // Array to hold all variables needed in the interstitial template
    $vars = array();
    
    // Get URL and page title
    $vars['url'] = $args[0];
    $vars['pagetitle'] = yourls_get_keyword_title( $joegra_inter['keyword'] );

    // Plugin URL (no URL is hardcoded)
    $vars['pluginurl'] = YOURLS_PLUGINURL . '/'.yourls_plugin_basename( dirname(__FILE__) );
    
    // Ad content
    $vars['ad'] = joegra_get_ad();
    if( empty( $vars['ad'] ) or !$vars['ad'] ) {
        $vars['ad'] = '<p>Your ad goes here (to be configured in the plugin admin page)</p>';
    }
    
    // Make sure browsers don't cache the page
    if( !headers_sent() ) {
        header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
        header( "Cache-Control: post-check=0, pre-check=0", false );
        header( "Pragma: no-cache" );
    }

    // All set. Draw the interstitial page
    $template = file_get_contents( dirname( __FILE__ ) . '/template.html' );
    // Replace all %stuff% in the template with variable $stuff
    $template = preg_replace_callback( '/%([^%]+)?%/', function( $match ) use( $vars ) { return $vars[ $match[1] ]; }, $template );
    echo $template;
    
    // Don't forget to die, to interrupt the flow of normal events (ie redirecting to long URL)
    die();
}

// Register our plugin admin page
yourls_add_action( 'plugins_loaded', 'joegra_inter_add_page' );
function joegra_inter_add_page() {
    yourls_register_plugin_page( 'joegra_inter', 'Interstitial Ad', 'joegra_inter_do_page' );
    // parameters: page slug, page title, and function that will display the page itself
}

// Display the plugin admin page
function joegra_inter_do_page() {

    // Check if a form was submitted
    if( isset( $_POST['ad_content'] ) ) {
        // Check nonce
        yourls_verify_nonce( 'joegra_inter' );
        
        // Process form
        joegra_inter_update_option();
    }

    // Get value from database
    $ad_content = joegra_get_ad( true );
    
    // Create nonce
    $nonce = yourls_create_nonce( 'joegra_inter' );

    echo <<<HTML
        <h2Interstitial Administration Page</h2>
        <form method="post">
        <input type="hidden" name="nonce" value="$nonce" />
        <p><label for="ad_content">Enter here your ad content. Can be any HTML and/or Javascript</label></p>
        <p><textarea id="ad_content" name="ad_content" rows="5" cols="80">$ad_content</textarea></p>
        <p><input type="submit" value="Update value" /></p>
        </form>

HTML;
}

// Get ad content. Set optional paramater $escape to true if you need to escape the HTML (eg in an input field)
function joegra_get_ad( $escape = false ) {
    $ad = yourls_get_option( 'joegra_inter_ad_content' );
    if( $escape ) {
        $ad = yourls_esc_html( $ad );
    }
    return $ad;
}

// Update option in database if needed
function joegra_inter_update_option() {
    $in = $_POST['ad_content'];
    if( $in ) {
        // Update value in database
        yourls_update_option( 'joegra_inter_ad_content', $in );
    }
}

