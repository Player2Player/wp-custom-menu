<?php

/*
Plugin Name: P2P Redirect login
Plugin URI: https://github.com/Player2Player/wp-plugins
Description: Redirect on login
Version: 1.0
Author: p2p
Author URI: https://player2player.com/
Text Domain: p2p
*/

namespace P2P\Login\Redirect;

class Plugin {
  
  public static function init() {
    static $instance = null;

    if ( is_null( $instance ) ){
      $instance = new self;
    }

    $instance->register();
  }

  private function register() {
    add_filter('login_redirect', array($this, 'redirect' ), 10, 3);
  }

  public function redirect($url, $request, $user) {
    if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
      if ( $user->has_cap( 'administrator' ) ) {
        $url = admin_url();
      } else if ($user->has_cap('wpamelia-provider')) {
        $url = home_url( '/coach-panel/' );
      } else if ($user->has_cap('wpamelia-customer')) {
        $url = home_url( '/customer-panel/' );
      }
    }
    return $url;
  }

}

Plugin::init();