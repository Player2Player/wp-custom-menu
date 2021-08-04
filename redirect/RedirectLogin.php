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
    add_filter('login_redirect', array($this, 'redirect' ), 10, 3);
    add_shortcode('p2predirect', array($this, 'redirectIfNotLoggedIn'));    
  }

  public function redirect($url, $request, $user) {
    if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
      if ( $user->has_cap( 'administrator' ) ) {
        $url = admin_url();
      } else if ($user->has_cap('wpamelia-provider')) {
        $url = home_url('/' . self::COACH_PANEL);
      } else if ($user->has_cap('wpamelia-customer')) {
        $url = home_url('/'. self::CUSTOMER_PANEL);
      }
    }
    return $url;
  }

  public function redirectIfNotLoggedIn($atts, $content) {
    if (!is_user_logged_in()) {
      wp_safe_redirect(home_url('/wp-login.php'));
      exit();
    }
    return do_shortcode($content);
  }

}

Plugin::init();