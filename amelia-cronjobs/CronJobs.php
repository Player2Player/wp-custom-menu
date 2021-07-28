<?php

/*
Plugin Name: P2P Amelia cron jobs
Plugin URI: https://github.com/Player2Player/wp-plugins
Description: Execute Amelia cron jobs
Version: 1.0
Author: p2p
Author URI: https://player2player.com/
Text Domain: p2p
*/

namespace P2P\CronJobs;


if (!defined('P2P_SCHEDULED_ACTION_URL')) {
  define('P2P_SCHEDULED_ACTION_URL', get_site_url() . '/wp-admin/admin-ajax.php?action=wpamelia_api&call=/notifications/scheduled/send');
}

class Plugin {
  
  public static function init() {
    static $instance = null;

    if ( is_null( $instance ) ){
      $instance = new self;
    }

    $instance->register();
  }

  private function register() {
    add_action('wpb_amelia_reminders', array($this, 'dispatchCronJobs' ));
  }

  public function dispatchCronJobs() {
    try {
      $response = wp_remote_get(P2P_SCHEDULED_ACTION_URL);
      $responseBody = wp_remote_retrieve_body($response);
      if (!is_wp_error($response)) {
        // We are using wp_sentry_safe to make sure this code runs even if the Sentry plugin is disabled
        if (function_exists( 'wp_sentry_safe' )) {
          wp_sentry_safe(function(\Sentry\State\HubInterface $client) use($responseBody) {
            $json = json_decode($responseBody);
            $client->captureMessage($json->message, \Sentry\Severity::info());
          });
        }
      }
    }
    catch(\Exception $e) {
      if (function_exists( 'wp_sentry_safe' )) {
        wp_sentry_safe( function (\Sentry\State\HubInterface $client) use ($e) {
          $client->captureException($e);
        });
      }
    }
  }
}

Plugin::init();