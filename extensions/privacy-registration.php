<?php

class PrivacyHelperRegistration extends PrivacyHelper {
    private $options;
    
    function __construct() {
        parent::__construct();
        
        $this->options = get_option( 'privacy_registration' );
        $this->ph_registration_check_condition();
    }
    
    
    private function ph_registration_check_condition() {
        
        if ( isset($this->options['check']) && !empty( $this->privacyUrl ) ) {
            
            add_action( 'register_form', array($this, 'ph_registration_registration_add_privacy_policy_field' ) );
            add_action( 'user_register', array($this, 'ph_registration_registration_privacy_policy_save') );
            add_filter( 'registration_errors', array($this, 'ph_registration_registration_privacy_policy_auth'), 10, 3 );
            
        }
        
    }
    
    
    // Add privacy policy field.
    function ph_registration_registration_add_privacy_policy_field() { ?>
      <p>
        <label for="registration_privacy_policy">
          <input type="checkbox" name="registration_privacy_policy" id="registration_privacy_policy" class="checkbox" value="true" />
          <?php echo $this->ph_replace_placeholder( $this->options['message'] ) ; ?>
        </label>
      </p>
    <?php 
    }

    
    //Check if Checkbox is set
    function ph_registration_registration_privacy_policy_auth( $errors, $sanitized_user_login, $user_email ) {

      if ( ! isset( $_POST['registration_privacy_policy'] ) ) :

        $errors->add( 'policy_error', '<strong>ERROR</strong>: ' . $this->options['error'] );
        return $errors;
      endif;
      return $errors;
    }
    
    
    // Lastly, save our extra registration user meta.
    function ph_registration_egistration_privacy_policy_save( $user_id ) {

      if ( isset( $_POST['registration_privacy_policy'] ) )
         update_user_meta( $user_id, 'privacy_accept', $_POST['registration_privacy_policy'] );
        
    }
     
}