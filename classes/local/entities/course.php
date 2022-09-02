<?php

namespace local_marketplace\local\entities;

/**
 * Category entity class.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class course {
    protected $table = 'course';

    public function get_all() {
        global $DB;

        $sql = 'SELECT id, fullname FROM {'.$this->table.'} WHERE id > 1 AND visible = 1';

        $categories = $DB->get_records_sql($sql);

        if (!$categories) {
            return [];
        }

        return array_values($categories);
    }
}
