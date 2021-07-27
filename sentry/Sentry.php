<?php

/*
Plugin Name: P2P Sentry settings
Plugin URI: https://github.com/Player2Player/sentry-cfg
Description: Setup wp pluging for sentry
Version: 1.0
Author: p2p
Author URI: https://player2player.com/
Text Domain: p2p
*/

namespace P2P\Sentry;

define('WP_SENTRY_PHP_DSN', 'https://623dae2636a64ffb8e6810311967a838@o931388.ingest.sentry.io/5880364');
define( 'WP_SENTRY_ERROR_TYPES', E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_USER_DEPRECATED );
define( 'WP_SENTRY_SEND_DEFAULT_PII', true );
define( 'WP_SENTRY_VERSION', 'v4.5.0' );
define( 'WP_SENTRY_ENV', 'production' );

class Plugin {
  
  public static function init() {
    static $instance = null;

    if ( is_null( $instance ) ){
      $instance = new self;
    }

    $instance->register();
  }

  private function register() {
    add_filter( 'wp_sentry_options', array($this, 'setSentryOptions' ));
  }

  public function setSentryOptions(\Sentry\Options $options){
    $options->setBeforeSendCallback(array($this, 'setBeforeSendCallback'));
  }

  public function setBeforeSendCallback(\Sentry\Event $event){
    $exceptions = $event->getExceptions();

		// No exceptions in the event? Send the event to Sentry, it's most likely a log message
		if ( empty( $exceptions ) ) {
			return $event;
		}

		$stacktrace = $exceptions[0]->getStacktrace();

		// No stacktrace in the first exception? Send it to Sentry just to be safe then
		if ( $stacktrace === null ) {
			return $event;
		}

		// Little helper and fallback for PHP versions without the str_contains function
		$strContainsHelper = function ( $haystack, $needle ) {
			if ( function_exists( 'str_contains' ) ) {
				return str_contains( $haystack, $needle );
			}

			return $needle !== '' && mb_strpos( $haystack, $needle ) !== false;
		};

		foreach ( $stacktrace->getFrames() as $frame ) {
			// Check the the frame happened inside our theme or plugin
			if ( $strContainsHelper( $frame->getFile(), 'plugins/ameliabooking' )) {
				// Send the event to Sentry
				return $event;
			}
		}

		// Stacktrace contained no frames in our theme and/or plugin? We send nothing to Sentry
		return null;    
  }

}

Plugin::init();