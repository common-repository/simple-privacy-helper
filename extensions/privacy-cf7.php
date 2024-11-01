<?php

class PrivacyHelperCF7 extends PrivacyHelper {
    
    private $options;
    private $recheck = false;
    
    public function __construct($recheck) {
        parent::__construct();
        
        $this->recheck = $recheck;
        $this->check_condition();
   
        
    }
    
    
    private function check_condition() {
        
        //If checked replace youtube url´s
        if ($this->recheck) {
            
            $this->ph_cf7_privacy();
            
        } else {
            
            //Execute when ph_cf7 option is updated
            add_action('update_option_privacy_cf7', array($this, 'ph_cf7_privacy')); 
            //Execute when WordPress Privacy Pages changes
            add_action('update_option_wp_page_for_privacy_policy', array($this, 'ph_cf7_privacy'));
        }
        
    }
    
    public function ph_cf7_privacy() {
        
        //get all forms with cf7 post_type
        $CFPosts = $this->ph_cf7_find_contact_forms();
        
        //Get Options
        $this->options = get_option( 'privacy_cf7' );
        
        //If checked replace youtube url´s
        if ( class_exists('wpcf7') && !empty( $CFPosts ) ) {

            //get option from settings page
            $acceptanceReplaced = $this->ph_replace_placeholder($this->options['message']);


            //iterate through found forms
            foreach ( $CFPosts as $post ) {

                $post_id        = $post->ID;
                $formContent    = get_post_meta($post_id, '_form', true);

                if($this->recheck) {

                    $this->ph_cf7_add_acceptance( $formContent, $acceptanceReplaced, $post_id );

                } else {

                    $this->ph_cf7_update_acceptance( $formContent, $acceptanceReplaced, $post_id );

                }

                //Add Checkbox to mail, if form already contains checkbox
                if ($this->ph_cf7_check_acceptance( $formContent )) {
                    $this->ph_cf7_add_acceptance_mail( $post_id );
                }

            } //end iteration
            
        } //end if statement

    } //end function
    
    
    //Override parent function to add bbcodes and html
    public function ph_replace_placeholder( $message ) {
        
        //Get Overwrite Privacy Policy from Parent Class to execute correctly when wp_page_for_privacy_policy is updated 
        $this->privacyPostObject    = get_post( get_option('wp_page_for_privacy_policy'), OBJECT );  
        
        //html link
        $replacement = '<a href="' . $this->privacyPostObject->guid . '">'. $this->privacyPostObject->post_title .'</a>';

            if( preg_match('/###Privacy-Policy###/', $message ) ) {
                return '[acceptance acceptance-999]<small>'. preg_replace('/###Privacy-Policy###/', $replacement, $message ) .'</small>[/acceptance]';
            } else {
                return '';
            }
    }
    
    
    private function ph_cf7_add_acceptance_mail( $post_id ) {
        $mailContent = get_post_meta($post_id, '_mail', true);
        $replacement = '[acceptance-999]';
        
        if (!empty($mailContent)) {
            $body = $mailContent['body'];
            
            //Checks if acceptance box already exists
            if ( empty(preg_match('/\[acceptance-999\]/', $body, $matches)) ) {
                
                if ( !empty(preg_match('/(-{2,})/', $body, $matches)) ) {
                    $body = str_replace( $matches[0], "$replacement\n$matches[0]", $body );
                } else {
                    $body = $body . "\n$replacement";
                }
            }
            $mailContent['body'] = $body;
            update_post_meta($post_id, '_mail', $mailContent);
        }
    }
    

    private function ph_cf7_find_contact_forms() {
        $args = array(
            'post_type'        => 'wpcf7_contact_form'
        );
        $myposts = get_posts( $args );
        return $myposts;
    }
    

    private function ph_cf7_add_acceptance( $formContent, $acceptanceReplaced, $post_id ) {
		//If there already is an acceptance box, return unchanged content
        $newContent = $formContent;
        
        //Check if there is already an acceptance box
        if( empty($this->ph_cf7_check_acceptance( $formContent )) ) {
            $match = $this->ph_cf7_check_submit($formContent);

            //check if a submit button exists
            if( !empty($match) ) {
                $concat = $acceptanceReplaced . "\r\n" . "\r\n" . $match;
                $newContent = str_replace( $match, $concat, $formContent );
            }
        }
		
        //Save new Form Content to Database
        update_post_meta($post_id, '_form', $newContent);
    }
    
    
    private function ph_cf7_update_acceptance( $formContent, $acceptanceReplaced, $post_id ) {
        //If there is no acceptance field with id 999 return unchanged content
        $newContent = $formContent;
        
        if ( preg_match('/(\[acceptance acceptance-999\])(.*)(\[\/acceptance\])/', $formContent, $matches) )
            $newContent = str_replace( $matches[0], $acceptanceReplaced, $formContent );

        //Save new Form Content to Database
        update_post_meta($post_id, '_form', $newContent);
    }
    

    private function ph_cf7_check_submit( $formContent ) {
        if ( preg_match('/\[submit.*\]/', $formContent, $matches) )
            return $matches[0];
    }
    

    private function ph_cf7_check_acceptance( $formContent ) {
        if ( preg_match('/\[acceptance\s.+\]/', $formContent) )
            return true;
    }
    
    
    private function ph_cf7_check_acceptance_mail( $mailContent ) {
        if ( preg_match('/\[acceptance-999\]/', $mailContent) )
            return true;
    }
}