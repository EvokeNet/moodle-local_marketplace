<?php

namespace local_marketplace\util;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use navigation_node;

/**
 * Course utility class.
 *
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class course {
    public function get_all_select() {
        global $DB;

        $sql = 'SELECT id, shortname, fullname FROM {course} WHERE id > 1 ORDER BY shortname';
        $records = $DB->get_records_sql($sql);

        $courses = [
            '' => get_string('selectanoption', 'local_marketplace')
        ];

        if (!$records) {
            return $courses;
        }

        foreach ($records as $record) {
            $courses[$record->id] = $record->shortname;
        }

        return $courses;
    }
}
