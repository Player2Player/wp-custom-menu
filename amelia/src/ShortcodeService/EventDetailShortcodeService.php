<?php

namespace P2P\Amelia\ShortcodeService;

use P2P\Amelia\Infrastructure\Container;
use P2P\Amelia\Repository\EventRepository;

class EventDetailShortcodeService
{

    /**
     * @return string
     */
    public static function shortcodeHandler($atts)
    {
        $atts = shortcode_atts(
            [
                'eventSlug' => get_query_var('eventSlug')
            ],
            $atts
        );

        if (empty($atts['eventSlug'])) {
            self::force404();
        }

        $data = self::getData($atts);

        ob_start();
        include P2P_AMELIA_PATH . '/view/frontend/event-detail.inc.php';
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    protected static function getData($atts) {
        $result = [];

        /** @var EventRepository $eventRepository */
        $eventRepository = Container::instance()->get('event.repository');

        try {
            $event = $eventRepository->getBySlug($atts['eventSlug']);
        }
        catch(NotFoundException $exc) {
            self::force404();
        }

        $result['event'] = $event;

        return $result;
    }

    protected static function force404() {
        status_header(404);
        nocache_headers();
        include( get_query_template( '404' ) );
        die();
    }
}