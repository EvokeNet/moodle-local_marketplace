<?php

namespace local_marketplace\util;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use local_marketplace\local\entities\product;
use local_marketplace\local\exceptions\product_outofstock;

/**
 * Evocoin utility class.
 *
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class seller {
    public function sell($id, $courseid, $userid = null) {
        global $DB, $CFG, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $productentity = new product();

        $product = $productentity->find($id);

        if (!$productentity->instock($product)) {
            throw new product_outofstock('product_outofstock', $courseid);
        }

        $url = new moodle_url('/local/marketplace/index.php', ['id' => $courseid]);

        $evocoin = new evocoin();

        $usercoins = $evocoin->get_user_coins($userid);

        if (!$usercoins || $product->price > $usercoins->coins) {
            redirect($url, get_string('nocoinstobuy', 'local_marketplace'), null, \core\output\notification::NOTIFY_WARNING);
        }

        try {
            $order = new \stdClass();
            $order->productid = $product->id;
            $order->userid = $userid;
            $order->price = $product->price;
            $order->status = 'delivered';
            $order->timecreated = time();
            $order->timemodified = time();

            $orderid = $DB->insert_record('marketplace_orders', $order);

            $evctransaction = new \stdClass();
            $evctransaction->courseid = $courseid;
            $evctransaction->userid = $userid;
            $evctransaction->source = 'local_marketplace';
            $evctransaction->sourcetype = 'order_product';
            $evctransaction->sourceid = $orderid;
            $evctransaction->coins = $product->price;
            $evctransaction->action = 'out';
            $evctransaction->timecreated = time();

            $DB->insert_record('evokegame_evcs_transactions', $evctransaction);

            $usercoins->coins -= $product->price;

            $DB->update_record('evokegame_evcs', $usercoins);

            redirect($url, get_string('buy_success', 'local_marketplace'), null, \core\output\notification::NOTIFY_SUCCESS);
        } catch (\Exception $e) {
            if ($CFG->debugdisplay) {
                throw $e;
            }

            redirect($url, get_string('errorbuy', 'local_marketplace'), null, \core\output\notification::NOTIFY_ERROR);
        }
    }
}
