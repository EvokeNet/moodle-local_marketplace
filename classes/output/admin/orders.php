<?php

namespace local_marketplace\output\admin;

defined('MOODLE_INTERNAL') || die();

use local_marketplace\local\entities\product;
use renderable;
use templatable;
use renderer_base;
use local_marketplace\local\entities\order;

/**
 * Marketplace renderable class.
 *
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class orders implements renderable, templatable {
    protected $context;

    public function __construct($context) {
        $this->context = $context;
    }

    public function export_for_template(renderer_base $output) {
        $orderentity = new order();
        $productentity = new product();

        $orders = $orderentity->get_orders_with_users_data();

        if (!$orders) {
            return [];
        }

        foreach ($orders as $order) {
            $order->image = current($productentity->get_images($order->productid));
            $order->humantimecreated = userdate($order->timecreated);
            $order->fullname = fullname($order);
        }

        return [
            'orders' => $orders
        ];
    }
}
