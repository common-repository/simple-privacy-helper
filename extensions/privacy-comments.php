<?php

class PrivacyHelperComments extends PrivacyHelper {
    private $options;
    
    function __construct() {
        parent::__construct();
        
        $this->options = get_option( 'privacy_comments' );
        $this->ph_comments_check_condition();
    }
    
    
    function ph_comments_check_condition() {
        
        if ( isset($this->options['consent_check']) ) {
            add_filter( 'comment_form_default_fields', array( $this, 'ph_comments_remove_wp_comment_cookies_consent' ) );
        }
        
        if ( isset($this->options['check']) && !empty( $this->privacyUrl ) ) {
            
            add_filter( 'comment_form_defaults', array($this, 'ph_comments_change_comment_form_defaults' ) );
            add_filter( 'preprocess_comment', array($this, 'ph_comments_verify_comment_meta_data' ) );
            add_action( 'comment_post', array($this, 'ph_comments_save_comment_meta_data' ) );
            
        }
        
    }
    
    //Remove Cookie Consent Checkbox
    function ph_comments_remove_wp_comment_cookies_consent( $fields ) {
		unset($fields['cookies']);
		return $fields;
	}
    
    //Add Checkbox after url field
    function ph_comments_change_comment_form_defaults( $default ) {

        $message = $this->options['message'];

        $default[ 'fields' ][ 'url' ] .= '<p class="comment-form-privacy" >' .
            '<input id="privacy" name="privacy" type="checkbox" value="true" style="margin-right: 5px;" />'.
            '<label for="privacy" style="display: inline;">' . $this->ph_replace_placeholder( $message ) . '</label></p>';

        return $default;
    }

    
    //Check if not empty
    function ph_comments_verify_comment_meta_data( $commentdata ) {
        if ( ! isset( $_POST['privacy'] ) )
            wp_die( $this->options['error'] );
        return $commentdata;
    }
    
    
    //Save to comment meta in database
    function ph_comments_save_comment_meta_data( $comment_id ) {
        add_comment_meta( $comment_id, 'privacy_accept', $_POST[ 'privacy' ] );
    }
     
}