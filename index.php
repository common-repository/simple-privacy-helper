<?php
/**
Plugin Name: Simple Privacy Helper
Description: Helps to improve privacy settings to meet the gdpr requirements
Version:     1.4.3.1
Author: JUVO Webdesign
Author URI: http://juvo-design.de/
Text Domain: simple-privacy-helper
Domain Path: /languages/
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Include all Plugin files    
include_once( plugin_dir_path( __FILE__ ) . "settings-page.php" );
include_once( plugin_dir_path( __FILE__ ) . "extensions/privacy-comments.php" );
include_once( plugin_dir_path( __FILE__ ) . "extensions/privacy-registration.php" );
include_once( plugin_dir_path( __FILE__ ) . "extensions/privacy-cf7.php" );
include_once( plugin_dir_path( __FILE__ ) . "extensions/privacy-yt.php" );
include_once( plugin_dir_path( __FILE__ ) . "extensions/privacy-ssl.php" );


/** Plugin Version and Upgrade **/

//Plugin Version
define( 'PRIVACY_HELPER_VERSION', '1.4.3.1' );

//Initiate Plugin Version in Database
add_option( 'privacyHelperVersion', '0' );

add_action( 'admin_init', 'ph_upgrade' );
function ph_upgrade() {
    $old_ver = get_option( 'privacyHelperVersion', '0' );
    $new_ver = PRIVACY_HELPER_VERSION;

    //If Version are equal then exit function
    if ( $old_ver == $new_ver ) {
        return;
    }

    //Execute function when old version is not equal to new version
    PrivacyHelper::ph_upgrade();
    
    //Save new Tag to database
    update_option('privacyHelperVersion', $new_ver);
}



/** Instances **/

//Fire when Plugins are Loaded is loaded
add_action( 'plugins_loaded', 'create_ph_instances_wp_loaded' );
function create_ph_instances_wp_loaded() {
    //Instance for Privacy Registration
    $privacyRegistration = new PrivacyHelperRegistration();

    //Instance for Privacy Comments
    $privacyComments = new PrivacyHelperComments();

    //Instance for Privacy Contact Form 7
    //@Param boolean Check all CF7 Forms and insert the snippet
    $privacyCF7 = new PrivacyHelperCF7(false);

    //Instance for Privacy Youtube
    $privacyYT = new PrivacyHelperYT();

    //Instance for Privacy SS
    $privacySS = new PrivacyHelperSSL();

    //Instance for Settings Page
    $privacySettingsPage = new SettingsPagePrivacyHelper($privacyCF7);
}


/** Privacy Helper Main Class **/

class PrivacyHelper {
    
    protected $privacyUrl;
    protected $privacyTitle;
    protected $privacyPostObject;
    
    function __construct() {
        
        //Set Class Variable for Policy
        self::ph_set_class_vars();
        
        add_action( 'init', array($this, 'ph_simple_privacy_buttons'), 11 );
        
    }
    
    private function ph_set_class_vars() {
        if (get_option('wp_page_for_privacy_policy') != false) {
            
            //Get ID of Privacy Policy from native WordPress function
            $privacyID                  = get_option('wp_page_for_privacy_policy');
            
            //retrieve Post Object with the ID
            $this->privacyPostObject    = get_post( $privacyID, OBJECT );
            
            $this->privacyUrl   = $this->privacyPostObject->guid;
            $this->privacyTitle = $this->privacyPostObject->post_title;
        }
    }
    
    
    /** Backend Buttons **/
    function ph_simple_privacy_buttons() {

        //Button CF7 check
        if( isset( $_REQUEST['privacy_cf7_recheck'] )) {
            new PrivacyHelperCF7(true);
        }
        
        //Button Youtube
        if( isset( $_REQUEST['privacy_yt_recheck'] )) {
            $this->privacyYT->ph_yt_check_all_pages_yt_privacy();
        }

    }
    

    // Search for ###Privacy-Policy### and replace with link to privacy policy
    protected function ph_replace_placeholder( $message ) {
        //html link
        $replacement = '<a href="' . $this->privacyUrl . '">'. $this->privacyTitle .'</a>';

            if( preg_match('/###Privacy-Policy###/', $message ) )
                return preg_replace('/###Privacy-Policy###/', $replacement, $message );
    }
    
    
    public static function ph_upgrade() {
        $optionsGroup = SettingsPagePrivacyHelper::$optionGroup;
        
        if (!empty(get_option('privacy_wc'))) {
            unregister_setting(  $optionsGroup, 'privacy_wc');
            delete_option('privacy_wc');
        }
        
        if (!empty(get_option('cf7_add_in'))) {
            unregister_setting(  $optionsGroup, 'cf7_add_in');
            delete_option('cf7_add_in');
        }
        
        if (!empty(get_option('privacy_policy_title'))) {
            unregister_setting(  $optionsGroup, 'privacy_policy_title');
            delete_option('privacy_policy_title');
        }
        
        if (!empty(get_option('privacy_policy_comments'))) {
            unregister_setting(  $optionsGroup, 'privacy_policy_comments');
            delete_option('privacy_policy_comments');
        }
        
        if (!empty(get_option('privacy_policy'))) {
            unregister_setting(  $optionsGroup, 'privacy_policy');
            delete_option('privacy_policy');
        }
        
        if (!empty(get_option('privacy_policy_yt'))) {
            unregister_setting(  $optionsGroup, 'privacy_policy_yt');
            delete_option('privacy_policy_yt');
        }
    }
}