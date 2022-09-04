<?php

namespace local_marketplace\output;

defined('MOODLE_INTERNAL') || die();

use local_marketplace\local\entities\order;
use local_marketplace\local\entities\product;
use renderable;
use templatable;
use renderer_base;

/**
 * Marketplace renderable class.
 *
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class mypurchases implements renderable, templatable {
    protected $courseid;
    protected $context;

    public function __construct($courseid, $context) {
        $this->courseid = $courseid;
        $this->context = $context;
    }

    public function export_for_template(renderer_base $output) {
        $productentity = new product();
        $orderentity = new order();

        $orders = $orderentity->get_user_orders();

        $data = ['courseid' => $this->courseid];

        if (!$orders) {
            return $data;
        }

        foreach ($orders as $order) {
            $order->image = current($productentity->get_images($order->productid));
            $order->humantimecreated = userdate($order->timecreated);

            $order->downloadurl = $order->type == $productentity::TYPE_DIGITAL_FILE;

            if ($order->downloadurl) {
                $order->downloadurl = $productentity->get_downloadable_file_url($order->productid);
            }
        }

        $data['orders'] = $orders;

        return $data;
    }
}
