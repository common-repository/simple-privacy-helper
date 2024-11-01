<?php

class PrivacyHelperSSL extends PrivacyHelper {
    
    private $siteURL;
    private $homeURL;
    
    function __construct() {
        parent::__construct();
        
        //Execute when Options is updated
        add_action('update_option_privacy_ssl', array($this, 'ph_ssl_check_condition')); 
    }
    
    
    public function ph_ssl_check_condition() {
        
        $this->options = get_option( 'privacy_ssl' );
        
        $this->siteURL = get_site_url();
        $this->homeURL = get_home_url();
        
        if (!is_multisite()) {
        
            if ( $this->options['check'] == 1 ) {
                $this->ph_ssl_change_url_ssl();
                $this->ph_ssl_redirect_htaccess(false);
                $this->ph_ssl_update_databse(false);
            } else {
                $this->ph_ssl_change_url_plain();
                $this->ph_ssl_redirect_htaccess(true);
                $this->ph_ssl_update_databse(true);
            }
            
        }
        
    }
    
    
    private function ph_ssl_change_url_plain() {
        update_option( 'siteurl',  $this->ph_ssl_replace_protocol($this->siteURL, 'plain') );
        update_option( 'home', $this->ph_ssl_replace_protocol($this->homeURL, 'plain') );
    }
    
    private function ph_ssl_change_url_ssl() {
        update_option( 'siteurl',  $this->ph_ssl_replace_protocol($this->siteURL, 'ssl') );
        update_option( 'home', $this->ph_ssl_replace_protocol($this->homeURL, 'ssl') );
    }
    
    
    private function ph_ssl_replace_protocol($url, $_protocol) {
        
        $protocolToSearch = '';
        $protocolToChangeTo = '';
        
        switch ($_protocol) {
            case 'ssl':
                $protocolToSearch = '/(http:\/\/)(.*)/';
                $protocolToChangeTo = 'https';
                break;
            case 'plain':
                $protocolToSearch = '/(https:\/\/)(.*)/';
                $protocolToChangeTo = 'http';
                break;
            default:
                $protocolToSearch = '/(https:\/\/)(.*)/';
                $protocolToChangeTo = 'http';
        }
        
        return preg_replace($protocolToSearch, $protocolToChangeTo.'://$2', $url);
    }
    
    
    private function ph_ssl_redirect_htaccess($remove) {
        
        if ($this->ph_ssl_server_software() == true) {
        
            $source = get_home_path().".htaccess";
            $marker = "Simple Privacy Helper";
            $contentLines = array(
                    "RewriteEngine On",
                    "RewriteCond %{HTTPS} !=on",
                    "RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]",
                );

            if (!$remove) {

                //worpress funtion that handels writing and updating with checks
                insert_with_markers($source, $marker, $contentLines);

            } else {            

                //check if file is writeable and exists
                if (is_writable($source)) {

                    $target= $source.'_temp';

                    //Open file for reading and writing
                    $read = fopen($source, "r");
                    $write = fopen($target, "w");

                    if (flock($read, LOCK_EX)) {

                        while (!feof($read)) {
                            $line = fgets($read); //current line
                            $lineReplacement = $line; //String the current line should be replaced with

                            //Check if current line is one of the lines that could have been added by the plugin if so replace with null
                            foreach( $contentLines as $contentLine ) {
                                if (stristr($line,$contentLine)) {
                                    //replace with ""
                                    $lineReplacement = null;
                                }
                            }

                            //If current line is one of the possible markers replace it with null
                            if (stristr($line,$marker)) {
                                $lineReplacement = null;
                            }

                            fwrite($write, $lineReplacement);
                        }
                        //Close both streams
                        fclose($read);
                        fclose($write);

                        //Delete Source file and check if this was successful
                        if (unlink ( $source )) {
                            //Rename Temp file to Sourcefile
                            rename ( $target , $source );
                        }

                    } //end if end of file &read
                } //end if is_writeable
            } //end if if($remove)
        } //end server software check
    } //end function
    
    
    private function ph_ssl_update_databse($remove) {
        global $wpdb;
        $url = $this->siteURL;
        $urlHttps = $this->ph_ssl_replace_protocol($this->siteURL, 'ssl');
        $urlHttp = $this->ph_ssl_replace_protocol($this->siteURL, 'plain');
        
        if (!$remove) {
            
            $wpdb->query( 
                $wpdb->prepare( 
                    "
                     UPDATE $wpdb->posts
                     SET post_content = REPLACE(post_content, %s, %s);
                    ",
                        $urlHttp, $urlHttps
                    )
            );
            
        } else {
            
            $wpdb->query( 
                $wpdb->prepare( 
                    "
                     UPDATE $wpdb->posts
                     SET post_content = REPLACE(post_content, %s, %s);
                    ",
                        $urlHttps, $urlHttp
                    )
            );
            
        }
            
    }
    
    private function ph_ssl_server_software() {
        $server = strtolower( filter_var( $_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING ) );
	
        //figure out what server they're using
        if ( strpos( $server, 'apache' ) !== false ) {

            return true;

        } elseif ( strpos( $server, 'nginx' ) !== false ) {

            return false;

        } else { //unsupported server

            return false;

        }
    }
    
}// end class