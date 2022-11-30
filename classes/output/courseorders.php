<?php

namespace local_marketplace\output;

defined('MOODLE_INTERNAL') || die();

use local_marketplace\local\entities\product;
use local_marketplace\util\user;
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
class courseorders implements renderable, templatable {
    protected $context;
    protected $course;

    public function __construct($context, $course) {
        $this->context = $context;
        $this->course = $course;
    }

    public function export_for_template(renderer_base $output) {
        $orderentity = new order();
        $productentity = new product();

        $orders = $orderentity->get_orders_with_users_data($this->course->id);

        if (!$orders) {
            return [];
        }

        $userutil = new user();

        foreach ($orders as $order) {
            $order->image = current($productentity->get_images($order->productid));
            $order->humantimecreated = userdate($order->timecreated);
            $order->fullname = fullname($order);
            $order->userimage = $userutil->get_user_avatar_or_image($order->userid);
        }

        return [
            'orders' => $orders
        ];
    }
}
