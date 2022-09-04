<?php

namespace local_marketplace\util;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use navigation_node;

/**
 * Evocoin utility class.
 *
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class evocoin {
    public function get_user_coins($userid) {
        global $DB;

        $record = $DB->get_record('evokegame_evcs', ['userid' => $userid]);

        if (!$record) {
            return false;
        }

        return $record;
    }
}
