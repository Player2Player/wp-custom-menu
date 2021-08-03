<?php

/*
Plugin Name: P2P Logout
Plugin URI: https://github.com/Player2Player/wp-plugins
Description: Logout current user
Version: 1.0
Author: p2p
Author URI: https://player2player.com/
Text Domain: p2p
*/

namespace P2P\Logout;

class Plugin {
  
  public static function init() {
    static $instance = null;

    if ( is_null( $instance ) ){
      $instance = new self;
    }

    $instance->register();
  }

  private function register() {
    add_action( 'wp_logout', array($this, 'redirectToHome'));
    add_shortcode('p2plogout', array($this, 'logout'));
  }

  public function logout() {
    ob_start();

?>
    <h1>Logout</h1>
    <a href="<?php echo wp_logout_url(home_url()) ?>">Logout</a>

<?php    
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
  }

  public function redirectToHome() { 
    $user = wp_get_current_user();
    if ($user->has_cap('administrator')) return;

    $redirect_url = site_url();
    wp_safe_redirect($redirect_url);
    exit;
  }

}

Plugin::init();