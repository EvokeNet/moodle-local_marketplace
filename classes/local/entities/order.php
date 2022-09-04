<?php

namespace local_marketplace\local\entities;

/**
 * Category entity class.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class order extends base {
    protected $table = 'marketplace_orders';

    public function get_user_orders($userid = null) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $sql = 'SELECT o.id, o.timecreated, p.id as productid, p.name, p.description, p.price, p.type
                FROM {marketplace_orders} o
                INNER JOIN {marketplace_products} p ON o.productid = p.id
                INNER JOIN {user} u ON u.id = o.userid
                WHERE u.id = :userid';

        $records = $DB->get_records_sql($sql, ['userid' => $userid]);

        if (!$records) {
            return false;
        }

        return array_values($records);
    }
}
