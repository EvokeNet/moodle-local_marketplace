<?php

namespace local_marketplace\output\admin;

defined('MOODLE_INTERNAL') || die();

use local_marketplace\local\entities\category;
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
class index implements renderable, templatable {
    protected $context;

    public function __construct($context) {
        $this->context = $context;
    }

    public function export_for_template(renderer_base $output) {
        $productentity = new product();
        $orderentity = new order();
        $categoryentity = new category();

        return [
            'total_products' => $productentity->count(),
            'total_orders' => $orderentity->count(),
            'total_categories' => $categoryentity->count()
        ];
    }
}
