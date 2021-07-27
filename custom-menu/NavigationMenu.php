<?php

/*
Plugin Name: P2P Custom menu
Plugin URI: https://github.com/Player2Player/wp-custom-menu
Description: Custom menu for getting locations and categories from amelia plugin
Version: 1.0
Author: p2p
Author URI: https://player2player.com/
Text Domain: p2p
*/

namespace P2P;

// Const for path root
if (!defined('P2P_PATH')) {
  define('P2P_PATH', __DIR__);
}

class Plugin {

  /**
	 * Track that hooks have been registered w/ WP
	 * @var bool
	*/
	protected $hasRegistered = false;


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

		$instance->register();
	}

  private function register() {
		if ( !is_admin() && !$this->hasRegistered ){
			$this->hasRegistered = true;

			add_filter( 'wp_get_nav_menu_items', array($this, 'addMenuItems' ), 20, 2 );
		}
  }

  /**
   * Simple helper function for make menu item objects
   * 
   * @param $title      - menu item title
   * @param $url        - menu item url
   * @param $order      - where the item should appear in the menu
   * @param int $parent - the item's parent item
   * @return \stdClass
  */ 
  private function createMenuItem( $title, $url, $order, $parent = 0 ){
    $item = new \stdClass();
    $item->ID = 1000000 + $order + $parent;
    $item->db_id = $item->ID;
    $item->title = $title;
    $item->url = $url;
    $item->menu_order = $order;
    $item->menu_item_parent = $parent;
    $item->type = '';
    $item->object = '';
    $item->object_id = '';
    $item->classes = array();
    $item->target = '';
    $item->attr_title = '';
    $item->description = '';
    $item->xfn = '';
    $item->status = '';
    return $item;
  }
  
  private function getAmeliaLocations() {			
    global $wpdb;
    $locations = $wpdb->get_results( $wpdb->prepare( 
      "
          SELECT      `id`, `name`, `slug`
          FROM        `wp_amelia_locations`
          WHERE       `status` = %s
          ORDER BY    `id` ASC
      ",
          'visible'
    )); 	
    return $locations;
  }
  
  private function getAmeliaCategories($locationId) {			
    global $wpdb;
    $categories = $wpdb->get_results( $wpdb->prepare(
      "select DISTINCT b.name as name, b.slug as slug from
      wp_amelia_providers_to_locations pl 
      inner join wp_amelia_providers_to_services ps on pl.userId = ps.userId
      inner join wp_amelia_services a on a.id = ps.serviceId 
      inner join wp_amelia_categories b on b.id = a.categoryId
      where pl.locationId = %d
      order by b.position     
      ",
          $locationId
    )); 	
    return $categories;
  }

  public function addMenuItems( $items, $menu ) {    
    // only add item to a specific menu  		
    if ( $menu->slug !== 'main-menu' ) {    
      return $items;
    }
    $searchMenu = $items[0]->ID;
    $bookingMenu = $items[1]->ID;
    $locations = $this->getAmeliaLocations();  
    $i=10;
    foreach($locations as $menuItem) {
      $top = $this->createMenuItem( $menuItem->name, "/coaches/{$menuItem->slug}", $i++, $searchMenu);
      $items[] = $top;	  
      $categories = $this->getAmeliaCategories($menuItem->id);	
      $items[] = $this->createMenuItem('see all', "/coaches/{$menuItem->slug}", $i++, $top->ID);
      foreach($categories as $menuCategory) {
        $items[] = $this->createMenuItem( $menuCategory->name, "/coaches/{$menuItem->slug}/{$menuCategory->slug}", $i++, $top->ID);
      }  
      $items[] = $this->createMenuItem( $menuItem->name, "/sports/{$menuItem->slug}", $i++, $bookingMenu);
    }	  
    return $items;	
  }

}

Plugin::init();

