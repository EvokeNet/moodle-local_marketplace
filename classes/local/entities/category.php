<?php

namespace local_marketplace\local\entities;

/**
 * Category entity class.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class category extends base {
    protected $table = 'marketplace_categories';

    public function has_children($id) {
        global $DB;

        if ($DB->count_records('marketplace_products', ['categoryid' => $id])) {
            return true;
        }

        return false;
    }

    public function get_all_select() {
        $categories = $this->get_all();

        if (!$categories) {
            return [];
        }

        $records = [
            0 => get_string('selectanoption', 'local_marketplace')
        ];

        foreach ($categories as $category) {
            $records[$category->id] = $category->name;
        }

        return $records;
    }
}
