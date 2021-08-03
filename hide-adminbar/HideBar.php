<?php

/*
Plugin Name: P2P Hide admin bar
Plugin URI: https://github.com/Player2Player/wp-plugins
Description: Hide admin bar for not admin users 
Version: 1.0
Author: p2p
Author URI: https://player2player.com/
Text Domain: p2p
*/

namespace P2P\HideAdminBar;

class Plugin {
  
  public static function init() {
    static $instance = null;

    if ( is_null( $instance ) ){
      $instance = new self;
    }

    $instance->register();
  }

  private function register() {
    add_action('after_setup_theme', array($this, 'hideAdminBar' ));
  }

  public function hideAdminBar() {
    if (!current_user_can('administrator') && !is_admin()) {
      show_admin_bar(false);
    }
  }

}

Plugin::init();