<?php

class PrivacyHelperYT extends PrivacyHelper {
    
    private $options;
    
    public function __construct() {
        parent::__construct();
        
        $this->options = get_option( 'privacy_yt' );
        $this->check_condition();
    }
    
    private function check_condition() {
        
        //If checked replace youtube url´s
        if (isset($this->options['check'])) {
            add_action( 'save_post', array( $this, 'ph_yt_replace_on_update'));
            add_filter( 'embed_oembed_html', array( $this, 'ph_yt_oembed_youtube_nocookie'), 10, 4);
        }
        
    }
    
    public function ph_yt_replace_on_update($post_id){
        $post = get_post($post_id);
        $content = $post->post_content;
        $my_post = array(
          'ID'           => $post_id,
          'post_content' => $this->ph_yt_replace_url_iframe( $content ),
        );


        if ( !empty($this->ph_yt_check_noprivacy_iframe( $content )) ) {

            // unhook this function so it doesn't loop infinitely
            remove_action( 'save_post', array( $this, 'ph_yt_replace_on_update'));

            // update the post, which calls save_post again
            wp_update_post( $my_post );

            // re-hook this function
            add_action( 'save_post', array( $this, 'ph_yt_replace_on_update'));

            return;

        }
    }

    private function ph_yt_check_noprivacy_iframe( $content ) {
        //find <iframe> with non privacy url und return
        if ( preg_match('/<iframe.*?(https:)?\/\/(www.)?youtube\.com.*\<\/iframe>/', $content, $matches) )
            return $matches[0];
    }

    private function ph_yt_replace_url_iframe( $content ) {
        //replace youtube.com with youtube-nocookie.com
        return preg_replace('/(<iframe.*?)(https?:\/\/www\.)?youtube\.com(.*<\/iframe>)/','$1$2youtube-nocookie.com$3', $content);

    }

    /**
    * OEmbed Filter
    * Code by Sergej Müller
    */
    public function ph_yt_oembed_youtube_nocookie( $original, $url, $attr, $post_ID ) {
        if ( preg_match('#https?://(www\.)?youtu#i', $url) ) {
            return preg_replace(
                '#src=(["\'])(https?:)?//(www\.)?youtube\.com#i',
                'src=$1$2//$3youtube-nocookie.com',
                $original
            );
        }
        return $original;
    }


    /**
     * Check All Pages
     */
    public function ph_yt_check_all_pages_yt_privacy() {
        $args = array(
            'post_type'        => array('post', 'pages'),
            'post_status'      => 'publish'
        );
        $myposts = get_posts( $args );

        foreach ( $myposts as $post ) {

            $content = $post->post_content;
            $post_id = $post->ID;

                $my_post = array(
                  'ID'           => $post_id,
                  'post_content' => $this->ph_yt_replace_url_iframe( $content ),
                );

                if ( !empty($this->ph_yt_check_noprivacy_iframe( $content )) ) {

                    // update the post, which calls save_post again
                    wp_update_post( $my_post );
                }

        }

    }
}