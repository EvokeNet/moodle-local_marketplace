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

    public function get_orders_with_users_data($courseid = null) {
        global $DB;

        $sql = 'SELECT
                    o.id, o.timecreated,
                    p.id as productid, p.name, p.description, p.price, p.type,
                    u.id as userid, u.email, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.firstname, u.lastname
                FROM {marketplace_orders} o
                INNER JOIN {marketplace_products} p ON o.productid = p.id
                INNER JOIN {user} u ON u.id = o.userid';

        $params = [];

        if ($courseid) {
            $sql .= ' WHERE p.courseid = :courseid ';
            $params['courseid'] = $courseid;
        }

        $sql .= ' ORDER BY o.id DESC';

        $records = $DB->get_records_sql($sql, $params);

        if (!$records) {
            return false;
        }

        return array_values($records);
    }

    public function user_purchased_product($productid) {
        global $USER, $DB;

        $productorder = $DB->get_record($this->table, ['productid' => $productid, 'userid' => $USER->id]);

        if ($productorder) {
            return true;
        }

        return false;
    }

    public function count_user_product_purchases($productid, $userid = null) {
        global $USER, $DB;

        if (!$userid) {
            $userid = $USER->id;
        }

        return $DB->count_records($this->table, ['productid' => $productid, 'userid' => $userid]);
    }

    public function get_ordered_products_with_quantity() {
        global $DB;

        $sql = 'SELECT p.id, p.name, count(*) as quantity
                FROM {marketplace_orders} o
                INNER JOIN {marketplace_products} p ON p.id = o.productid
                GROUP BY p.id';

        $records = $DB->get_records_sql($sql);

        if (!$records) {
            return false;
        }

        return array_values($records);
    }

    public function get_ordered_products_by_month_with_quantity() {
        global $DB;

        $sql = 'SELECT 
                  MONTH(FROM_UNIXTIME(timecreated)) month, 
                  YEAR(FROM_UNIXTIME(timecreated)) year, 
                  COUNT(*) as quantity
                FROM {marketplace_orders}
                GROUP BY 
                  MONTH(FROM_UNIXTIME(timecreated)), 
                  YEAR(FROM_UNIXTIME(timecreated))';

        $records = $DB->get_records_sql($sql);

        if (!$records) {
            return false;
        }

        return array_values($records);
    }
}
