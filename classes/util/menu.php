<?php

namespace local_marketplace\util;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use navigation_node;

class menu {
    public static function fill_secondary_menu_with_admin_items() {
        global $PAGE;

        $secondary = $PAGE->secondarynav;

        $secondary->add(
            get_string('marketplace', 'local_marketplace'),
            new moodle_url('/local/marketplace/admin/index.php'),
            navigation_node::TYPE_CUSTOM
        );

        $secondary->add(
            get_string('orders', 'local_marketplace'),
            new moodle_url('/local/marketplace/admin/orders.php'),
            navigation_node::TYPE_CUSTOM
        );

        $secondary->add(
            get_string('products', 'local_marketplace'),
            new moodle_url('/local/marketplace/admin/products.php'),
            navigation_node::TYPE_CUSTOM
        );

        $secondary->add(
            get_string('categories', 'local_marketplace'),
            new moodle_url('/local/marketplace/admin/categories.php'),
            navigation_node::TYPE_CUSTOM
        );
    }
}