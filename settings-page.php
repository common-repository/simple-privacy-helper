<?php

class SettingsPagePrivacyHelper {
    
    public static $optionGroup = 'privacy_helper_option_group';
    private static $optionTitle = 'Simple Privacy Helper';
    
    /*
     * Default Settings for Options
     */
    private $commentsDefaults;
    private $regDefaults;
    private $cf7Defaults;
    private $ytDefaults;
    private $sslDefaults;
    
    /*
     * Constructor
     */
    public function __construct( PrivacyHelperCF7 $privacyCF7 ) {        
        
        /** Default Options **/
        $this->commentsDefaults = array(
            'check'             => 1,
            'message'           => sprintf(__('I consent to having this website store my submitted information to process my comment. Further details can be found in our %s.', 'simple-privacy-helper'), '###Privacy-Policy###'),
            'error'             => __('You have to accept the conditions.', 'simple-privacy-helper'),
            'consent_check'     => 0,
        );
    
        $this->regDefaults = array(
            'check'             => 1,
            'message'           => sprintf(__('I consent to having this website store my submitted information to process my registration. Further details can be found in our %s.', 'simple-privacy-helper'), '###Privacy-Policy###'),
            'error'             => __('You have to accept the conditions.', 'simple-privacy-helper')
        );
    
        $this->cf7Defaults = array(
            'check'             => 1,
            'message'           =>sprintf(__('I consent to having this website store my submitted information to process my inquiry. Further details can be found in our %s.', 'simple-privacy-helper'), '###Privacy-Policy###'),
        );
    
        $this->ytDefaults = array(
            'check'             => 1,
        );
        
        $this->sslDefaults = array(
            'check'             => 0,
        );
        
        //Get CF7 Object for check methods
        $this->privacyCF7 = $privacyCF7;
        
        $this->set_privacyHelper_plugin_defaults();
        
        // create custom plugin settings menu
        add_action('admin_menu', array( $this, 'plugin_create_menu') );
        
        //call register settings function
        add_action( 'admin_init', array( $this, 'register_privacy_helper_plugin_settings' ) );
     }
    
    
    /*
     * Initial default Settings
     */
    private function set_privacyHelper_plugin_defaults() {
        
        //privacyComments
        if(!get_option('privacy_comments')) {
            add_option( 'privacy_comments', $this->commentsDefaults );
        }
        
        //Reg
        if(!get_option('privacy_registration')) {
            add_option( 'privacy_registration', $this->regDefaults );
        }
        
        //CF7
        if(!get_option('privacy_cf7')) {
            add_option( 'privacy_cf7', $this->cf7Defaults );
        }
        
        //YT
        if(!get_option('privacy_yt')) {
            add_option( 'privacy_yt', $this->ytDefaults );
        }
        
        //SSL
        if(!get_option('privacy_ssl')) {
            add_option( 'privacy_ssl', $this->sslDefaults );
        }
        
    }
    
    
    public function plugin_create_menu() {

        //create new top-level menu
        add_options_page(
            self::$optionTitle,             //Page Title
            self::$optionTitle,             //Menu Title
            'manage_privacy_options',       //required Capabilities
            'simple-simple-privacy-helper',        //Slug
            array( $this, 'privacy_helper_settings_page')
        );
        
    }
    
    
    /*
     * Register Plugin Settings
     */
    public function register_privacy_helper_plugin_settings() {
        //register settings
        register_setting( self::$optionGroup, 'privacy_comments', array( $this, 'privacy_comments_validate' ) );
        register_setting( self::$optionGroup, 'privacy_registration', array( $this, 'privacy_registration_validate' ) );
        register_setting( self::$optionGroup, 'privacy_cf7', array( $this, 'privacy_cf7_validate' ) );
        register_setting( self::$optionGroup, 'privacy_yt', array( $this, 'privacy_yt_validate') );
        register_setting( self::$optionGroup, 'privacy_ssl', array( $this, 'privacy_ssl_validate') );
    }
    
	
	/*
     * Validate Plugin Settings
     */    
    public function privacy_comments_validate( $input ) {
		// Create our array for storing the validated options
		$output = array();
        
        if ( isset($input['check']) )
            $output['check'] = filter_var($input['check'], FILTER_SANITIZE_NUMBER_INT);
        else
            $output['check'] = filter_var(0, FILTER_SANITIZE_NUMBER_INT);
		
        if( !empty( $input['message'] ) ) {
			$output['message'] = sanitize_textarea_field ( $input['message'] );
        } else {
            $output['message'] = sanitize_textarea_field ( $this->privacyCommentsDefaults['message'] );
        }
        
        if( !empty( $input['error'] ) ) {
			$output['error'] = sanitize_textarea_field ( $input['error'] );
        } else {
            $output['error'] = sanitize_textarea_field ( $this->privacyCommentsDefaults['error'] );
        }
        
        if ( isset($input['consent_check']) )
            $output['consent_check'] = filter_var($input['consent_check'], FILTER_SANITIZE_NUMBER_INT);

		// Return the array processing any additional functions filtered by this action
		return $output;
	}
    
    public function privacy_registration_validate( $input ) {
		// Create our array for storing the validated options
		$output = array();
        
        if ( isset($input['check']) )
            $output['check'] = filter_var($input['check'], FILTER_SANITIZE_NUMBER_INT);
        else
            $output['check'] = filter_var(0, FILTER_SANITIZE_NUMBER_INT);
		
        if( !empty( $input['message'] ) ) {
			$output['message'] = sanitize_textarea_field ( $input['message'] );
        } else {
            $output['message'] = sanitize_textarea_field ( $this->privacy_registration['message'] );
        }
        
        if( !empty( $input['error'] ) ) {
			$output['error'] = sanitize_textarea_field ( $input['error'] );
        } else {
            $output['error'] = sanitize_textarea_field ( $this->privacy_registration['error'] );
        }
        

		// Return the array processing any additional functions filtered by this action
		return $output;
	}
    
    public function privacy_cf7_validate( $input ) {
		// Create our array for storing the validated options
		$output = array();
		
		if( !empty( $input['message'] ) ) {
			$output['message'] = sanitize_textarea_field ( $input['message'] );
        } else {
            $output['message'] = sanitize_textarea_field ( $this->cf7Defaults['message'] );
        }

		// Return the array processing any additional functions filtered by this action
		return $output;
	}
    
    public function privacy_yt_validate( $input ) {
		// Create our array for storing the validated options
		$output = array();
        
        if ( isset($input['check']) )
            $output['check'] = filter_var($input['check'], FILTER_SANITIZE_NUMBER_INT);
        else
            $output['check'] = filter_var(0, FILTER_SANITIZE_NUMBER_INT);
        
		// Return the array processing any additional functions filtered by this action
		return $output;
	}
    
    public function privacy_ssl_validate( $input ) {
		// Create our array for storing the validated options
		$output = array();
        
        if ( isset($input['check']) )
            $output['check'] = filter_var($input['check'], FILTER_SANITIZE_NUMBER_INT);
        else
            $output['check'] = filter_var(0, FILTER_SANITIZE_NUMBER_INT);
        
		// Return the array processing any additional functions filtered by this action
		return $output;
	}

    
    /*
     * Create Options Page
     */
    public function privacy_helper_settings_page() {
    ?>
    <div class="wrap">
    <h1><?php echo self::$optionTitle ?></h1>

    <form method="post" action="options.php">
       
        <!-- get Options from  WordPress to show them -->
        <?php settings_fields( self::$optionGroup );
        $privacyID  = get_option('wp_page_for_privacy_policy');
        $options = get_option( 'privacy_comments' );
        $optionsCF = get_option( 'privacy_cf7' );
        $optionsYT = get_option( 'privacy_yt' );
        $optionsReg = get_option( 'privacy_registration' );
        $optionsSSL = get_option( 'privacy_ssl' );
        do_settings_sections( self::$optionGroup ); ?>
        
        <table class="form-table">
          <tbody>
           
           <!-- Privacy Policy -->
           
            <tr valign="top">
            <th scope="row"><?php esc_attr_e( 'Privacy Policy', 'simple-privacy-helper' ); ?></th>
                <td>
                    <fieldset>
                       <p class="description">
                            <?php echo sprintf( wp_kses_post(__( 'This is the current Privacy Policy. You can change it <a href="%s">here</a>.', 'simple-privacy-helper' )), esc_url( get_site_url(null, '/wp-admin/privacy.php', 'admin') ) ); ?>
                        </p>

                        <input type="text" name="privacy_policy[title]" value="<?php esc_attr_e(get_post($privacyID)->post_title) ; ?>" size=50 readonly>

                        <input name="privacy_pageid" type="text" value="<?php esc_attr_e($privacyID); ?>" size=3 readonly>
                    </fieldset>
                </td>
            </tr>
            
            <!-- Comments -->
            
            <tr valign="top">
            <th scope="row"><?php esc_attr_e( 'Comments', 'simple-privacy-helper' );?></th>
                <td>
                    <fieldset>
                       <label>
                            <input type="checkbox" name="privacy_comments[consent_check]" value="1"<?php if ( (isset($options['consent_check'])) && (1 == $options['consent_check']) ) echo 'checked="checked"'; ?>/>
                            <?php esc_attr_e( 'Deactivate the native WordPress Checkbox for saving Cookies. If deactivated, no cookies will be saved.', 'simple-privacy-helper'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="privacy_comments[check]" value="1"<?php if ( (isset($options['check'])) && (1 == $options['check']) ) echo 'checked="checked"'; ?>/>
                            <?php esc_attr_e( 'Add a Checkbox in the comment form to express consent for saving data and accepting the Privacy Policy.', 'simple-privacy-helper'); ?>
                        </label>
                        <p class="description">
                            <?php echo sprintf (esc_attr__( 'Label of the Checkbox. %s will be replaced with the Privacy Policy title.', 'simple-privacy-helper'), '<b>###Privacy-Policy###</b>'); ?>
                        </p>
                        <textarea name="privacy_comments[message]" cols="75" rows="2"><?php 
                            echo $options['message'];
                        ?></textarea>
                        
                        <p class="description">
                            <?php esc_attr_e( 'Error Message if the checkbox is not checked', 'simple-privacy-helper'); ?>
                        </p>
                        <textarea name="privacy_comments[error]" cols="75" rows="1"><?php 
                            echo $options['error']; 
                        ?></textarea><br>
                        
                    </fieldset>
                </td>
            </tr>
            
            <!-- Registration -->
            
            <tr valign="top">
            <th scope="row"><?php esc_attr_e( 'Registration', 'simple-privacy-helper' );?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="privacy_registration[check]" value="1"<?php if ( (isset($optionsReg['check'])) && (1 == $optionsReg['check']) ) echo 'checked="checked"'; ?>/>
                            <?php esc_attr_e( 'Add a Checkbox in the registration form to express consent for saving data and accepting the Privacy Policy?', 'simple-privacy-helper').'<br>'; ?>
                        </label>
                        <p class="description">
                            <?php echo sprintf (esc_attr__( 'Label of the Checkbox. %s will be replaced with the Privacy Policy title.', 'simple-privacy-helper'), '<b>###Privacy-Policy###</b>'); ?>
                        </p>
                        <textarea name="privacy_registration[message]" cols="75" rows="2"><?php 
                            echo $optionsReg['message'];
                        ?></textarea>
                        
                        <p class="description">
                            <?php esc_attr_e( 'Error Message if the checkbox is not checked', 'simple-privacy-helper'); ?>
                        </p>
                        <textarea name="privacy_registration[error]" cols="75" rows="1"><?php 
                            echo $optionsReg['error']; 
                        ?></textarea><br>
                    </fieldset>
                </td>
            </tr>
            
            <!-- Contact Form 7 -->
            
            <?php if (class_exists('wpcf7')) { ?>
                <tr valign="top">
                <th scope="row"><?php esc_attr_e( 'Contact Form 7', 'simple-privacy-helper' );?></th>
                    <td>
                        <fieldset>
                            <p class="description">
                                <?php echo sprintf (esc_attr__( 'Label of the Checkbox. %s will be replaced with the Privacy Policy title.', 'simple-privacy-helper'), '<b>###Privacy-Policy###</b>'); ?>
                            </p>
                            
                            <textarea name="privacy_cf7[message]" cols="75" rows="2"><?php 
                                echo $optionsCF['message']; 
                            ?></textarea><br>
                            
                            <p class="description"><?php esc_html_e('You can copy this Code to add the acceptance box manually. The label will be synchronised with the one above.','simple-privacy-helper')?></p>
                            <code><?php esc_attr_e( $this->privacyCF7->ph_replace_placeholder( $optionsCF['message'] ) );?></code><br><br>
                            
                            <input type="submit" class="button" name="privacy_cf7_recheck" value="<?php esc_attr_e('Add checkbox to all forms', 'simple-privacy-helper' ) ?>"/>
                        </fieldset>
                    </td>
                </tr>
            <?php } ?>
            
            <!-- Youtube Privacy -->
               
                <tr valign="top">
                <th scope="row"><?php esc_attr_e( 'Youtube', 'simple-privacy-helper' );?></th>
                    <td>
                        <fieldset>
                            <label>
                               <input type="checkbox" name="privacy_yt[check]" value="1"<?php if ( (isset($optionsYT['check'])) && (1 == $optionsYT['check']) ) echo 'checked="checked"'; ?>/>
                                <?php esc_attr_e( 'Replace youtube.com with youtube-nocookie.com when a post or page is saved.', 'simple-privacy-helper').'<br>'; ?>
                            </label>
                            <br>
                            <input type="submit" class="button" name="privacy_yt_recheck" value="<?php _e('Replace URL in all posts', 'simple-privacy-helper' ) ?>"/>
                        </fieldset>
                    </td>
                </tr>
                
            <!-- SSL Encryption -->
            <?php if (!is_multisite()) { ?>

                <tr valign="top">
                <th scope="row"><?php esc_attr_e( 'SSL Encryption', 'simple-privacy-helper' );?></th>
                    <td>
                        <fieldset>
                            <label>
                               <input type="checkbox" name="privacy_ssl[check]" value="1"<?php if ( (isset($optionsSSL['check'])) && (1 == $optionsSSL['check']) ) echo 'checked="checked"'; ?>/>
                                <?php esc_attr_e( 'Should this site use SSL?', 'simple-privacy-helper').'<br>'; ?>
                            </label>
                            <div class="ph-warning" style="background-color: #e46b6b; padding: 10px; color: #fff; border-top: 4px solid #ae0000;">
                                <p><?php echo wp_kses_post( 'Activating this option could lock you out! <b>You must have a certificate set up!</b> Simple Privacy Helper will change your home and site URL, create a redirect in your .htaccess file and change the URLs in the post_content table to avoid mixed content warnings. <br> If anything goes wrong you will need to remove the redirection and change the home and site in the database to get access to your wp-admin again.', 'simple-privacy-helper' ); ?></p>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <?php } ?>
                
            </tbody>
        </table>

        <?php submit_button( esc_attr_e('Save Changes', 'simple-privacy-helper'), 'primary', 'juvo-save-settings' ); ?>

    </form>
    </div>
    <?php }
}