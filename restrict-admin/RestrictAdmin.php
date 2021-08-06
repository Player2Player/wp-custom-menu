<?php

/*
Plugin Name: P2P Restrict admin access
Plugin URI: https://github.com/Player2Player/wp-plugins
Description: Restrict admin access with redirect to home page
Version: 1.0
Author: p2p
Author URI: https://player2player.com/
Text Domain: p2p
*/

namespace P2P\Login\RestrictAdmin;

class Plugin {
  
  const COACH_PANEL = 'coach-panel';
  const CUSTOMER_PANEL = 'customer-panel';

  public static function init() {
    static $instance = null;

    if ( is_null( $instance ) ){
      $instance = new self;
    }

    $instance->register();
  }

  private function register() {
    add_action('admin_init', array($this, 'restrictAdminWithRedirect' ), 1);
  }

  public function restrictAdminWithRedirect() {
    if ( wp_doing_ajax() || current_user_can('manage_options')) {
      return;
    }
    $user = wp_get_current_user();
    if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
      $url = null;
      if ($user->has_cap('wpamelia-provider')) {        
        $url = home_url('/' . self::COACH_PANEL);
      } else if ($user->has_cap('wpamelia-customer')) {
        $url = home_url('/'. self::CUSTOMER_PANEL);
      }
      if ($url) {
        wp_safe_redirect($url);
        exit();
      }
    }
  }  

}

Plugin::init();