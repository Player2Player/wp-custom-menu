<?php

namespace P2P\Amelia\ShortcodeService;

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

      $result['location']['name'] = 'Lake Travis';

      return $result;
    }

    protected static function force404() {
      status_header(404);
      nocache_headers();
      include( get_query_template( '404' ) );
      die();
    }

}
