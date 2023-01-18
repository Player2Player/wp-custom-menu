<?php

namespace P2P\Amelia\ShortcodeService;

use P2P\Amelia\Infrastructure\Container;
use P2P\Amelia\Repository\ProviderRepository;
use P2P\Amelia\Repository\LocationRepository;

class CoachProfileShortcodeService {
    
    /**
     * @return string
    */
    public static function shortcodeHandler($atts)
    {                    
      $atts = shortcode_atts(
        [
          'coachSlug' => get_query_var('coachSlug')              
        ],
        $atts
      );

      if (empty($atts['coachSlug'])) {
        self::force404();
      }

      $data = self::getData($atts);

      ob_start();
      include P2P_AMELIA_PATH . '/view/frontend/coach.inc.php';
      $html = ob_get_contents();
      ob_end_clean();
      return $html;
    }

    protected static function getData($atts) {
        $result = [];
        $providerSlug = $atts['coachSlug'];
        /** @var ProviderRepository $providerRepository */      
        $providerRepository = Container::instance()->get('provider.repository');
        /** @var LocationRepository $locationRepository */
        $locationRepository = Container::instance()->get('location.repository');
        try {
          $genericCoachImage = 'https://player2player.com/wp-content/uploads/2021/07/coach-icon-png-4.png';
          /** @var Provider @coach */        
          $coach = $providerRepository->getProfile($providerSlug);

          /** @var Location @location */
          $location = $locationRepository->getById($coach['locationId']);
          $result['id'] = $coach['id'];
          $result['fullName'] = "{$coach['firstName']} {$coach['lastName']}";
          $result['picture'] = $coach['pictureFullPath'] ? $coach['pictureFullPath'] : $genericCoachImage;
          $result['location'] = $location;
          $result['notes'] = $coach['description'] ? $coach['description'] : '';
          $categories = [];
          foreach($coach['services'] as $service) {
            $serviceId = $service['id'];  
            $categoryId = $service['categoryId']; 
            if (!array_key_exists($categoryId, $categories)) {
              $categories[$categoryId] = [];
              $categories[$categoryId]['category'] = $service;
              $categories[$categoryId]['services'] = [];
            }
            if (!array_key_exists($serviceId, $categories[$categoryId]['services'])) {
              $categories[$categoryId]['services'][$serviceId] = $service['name'];
            }
          }
          $result['categories'] = $categories;
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

