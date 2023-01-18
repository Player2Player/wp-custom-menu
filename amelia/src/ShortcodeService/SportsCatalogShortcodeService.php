<?php

namespace P2P\Amelia\ShortcodeService;

use P2P\Amelia\Infrastructure\Container;
use P2P\Amelia\Repository\LocationRepository;

class SportsCatalogShortcodeService {

    /**
     * @return string
     */
    public static function shortcodeHandler($atts)
    {                    
      $atts = shortcode_atts(
        [
          'location' => get_query_var('location')              
        ],
        $atts
      );

      if (empty($atts['location'])) {
        self::force404();
      }

      $data = self::getData($atts);

      ob_start();
      include P2P_AMELIA_PATH . '/view/frontend/sports.inc.php';
      $html = ob_get_contents();
      ob_end_clean();
      return $html;
    }

    protected static function getData($atts) {
        $result = [];
        $locationSlug = $atts['location'];
        /** @var LocationRepository $locationRepository */
        $locationRepository = Container::instance()->get('location.repository'); 
        try {
          $location = $locationRepository->getBySlug($locationSlug);
          $result['location'] = $location;
        } 
        catch(\Exception $exc) {        
          self::force404();
        }
        return $result;
      }
  
      protected static function force404() {
        status_header(404);
        nocache_headers();
        include( get_query_template( '404' ) );
        die();
      }  

}
