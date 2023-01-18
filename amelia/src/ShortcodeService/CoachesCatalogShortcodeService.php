<?php


namespace P2P\Amelia\ShortcodeService;

use P2P\Amelia\Infrastructure\Container;
use P2P\Amelia\Repository\LocationRepository;
use P2P\Amelia\Repository\CategoryRepository;
use P2P\Amelia\Repository\ProviderRepository;

/**
 * Class CoachesCatalogShortcodeService
 *
 * @package P2P\Amelia\ShortcodeService
 */
class CoachesCatalogShortcodeService
{

    /**
     * @return string
     */
    public static function shortcodeHandler($atts)
    {
      $atts = shortcode_atts(
        [
          'category' => get_query_var('category'),
          'location' => get_query_var('location')
        ],
        $atts
      );

      if (empty($atts['location'])) {
        self::force404();
      }

      $data = self::getData($atts);

      ob_start();
      include P2P_AMELIA_PATH . '/view/frontend/coaches.inc.php';
      $html = ob_get_contents();
      ob_end_clean();

      return $html;
    }

    protected static function getData($atts) {
      $result = [
              'location' => [],
              'category' => [],
              'coaches'  => []
        ];
      $locationSlug = $atts['location'];
      $categorySlug = $atts['category'];

      /** @var LocationRepository $locationRepository  */
      $locationRepository = Container::instance()->get('location.repository');

      /** @var CategoryRepository $categoryRepository  */
      $categoryRepository = Container::instance()->get('category.repository');

      /** @var ProviderRepository $providerRepository  */
      $providerRepository = Container::instance()->get('provider.repository');

      try {
        $location = $locationRepository->getBySlug($locationSlug);        
        
        $categoryId = null;
        $result['category'] = null;
        if (!empty($categorySlug)) {
          $category = $categoryRepository->getBySlug($categorySlug);
          if (!$category || empty($category)) {
            self::force404();
          }  
          $result['category'] = $category;
          $categoryId = $result['category']['id'];
        }
        
        $result['location'] = $location;    
        $locationId = $result['location']['id'];
        $criteria = array('location' => $locationId, 'category' => $categoryId);
        $coaches = $providerRepository->getWithServices($criteria);
        $result['coaches'] = $coaches;
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
