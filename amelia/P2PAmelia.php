<?php

/*
Plugin Name: P2P Amelia customizations
Plugin URI: https://github.com/Player2Player/wp-plugins
Description: Customizations related with the amelia plugin
Version: 1.0.4
Author: p2p
Author URI: https://player2player.com/
Text Domain: p2p
*/

namespace P2P\Amelia;

// Const for path root
if (!defined('P2P_AMELIA_PATH')) {
    define('P2P_AMELIA_PATH', __DIR__);
}

require_once(P2P_AMELIA_PATH . '/inc/autoloader.php');

class Plugin {

    /**
	 * Singleton
	 *
	 * @return Plugin
	 */
	public static function init(){
        static $instance = null;

        if ( is_null( $instance ) ){
            $instance = new self;
        }
        require P2P_AMELIA_PATH . '/src/container.php';
		$instance->register();
    }

    private function register() {
        if (is_admin()) return;

        /* register shortcodes */
        add_action( 'init', array($this, 'registerShortcodes'));

        /** Register custom query vars for getting coaches by location or activity */
        add_filter( 'query_vars', array($this, 'registerQueryVars'));
        add_action('init', array($this, 'coachesRewriteTagRules'), 10, 0);
    }

    public static function registerShortcodes() {
        add_shortcode('p2pcoaches', array('P2P\Amelia\ShortcodeService\CoachesCatalogShortcodeService', 'shortcodeHandler'));
        add_shortcode('p2psports', array('P2P\Amelia\ShortcodeService\SportsCatalogShortcodeService', 'shortcodeHandler'));
        add_shortcode('p2pcoach', array('P2P\Amelia\ShortcodeService\CoachProfileShortcodeService', 'shortcodeHandler'));
        add_shortcode('p2pevent', array('P2P\Amelia\ShortcodeService\EventDetailShortcodeService', 'shortcodeHandler'));
    }

    //p2p: query vars
    public static function registerQueryVars($vars) {
        $vars[] = 'location';
        $vars[] = 'category';
        $vars[] = 'coachSlug';
        $vars[] = 'eventSlug';
        return $vars;
    }

    //p2p: coaches rewrite rules
    public static function coachesRewriteTagRules() {
        $options = json_decode(get_option('p2p_settings'));
        $tpl = $options->templates;
        add_rewrite_rule('^coaches/([^/]*)/?([^/]*)/?', "index.php?page_id={$tpl->coaches}&location=\$matches[1]&category=\$matches[2]", 'top');
        add_rewrite_rule('^coach/([^/]*)/?', "index.php?page_id={$tpl->coach}&coachSlug=\$matches[1]", 'top');
        add_rewrite_rule('^sports/([^/]*)/?', "index.php?page_id={$tpl->sports}&location=\$matches[1]", 'top');
        add_rewrite_rule('^event-detail/([^/]*)/?', "index.php?page_id={$tpl->event}&eventSlug=\$matches[1]", 'top');
    }

}

add_action('plugins_loaded', array('P2P\Amelia\Plugin', 'init'));
