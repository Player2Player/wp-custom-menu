<?php

namespace P2P\Amelia\Repository;

class EventRepository
{
    /**
     * @param $slug
     * @return array
     */
    public function getBySlug($slug)
    {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}amelia_events WHERE slug = %s";
        $sql = $wpdb->prepare($sql, $slug);

        $result = $wpdb->get_row($sql, ARRAY_A);

        return $result;
    }
}