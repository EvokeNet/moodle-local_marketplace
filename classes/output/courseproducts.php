<?php

namespace local_marketplace\output;

defined('MOODLE_INTERNAL') || die();

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
class courseproducts implements renderable, templatable {
    protected $context;
    protected $course;

    public function __construct($context, $course) {
        $this->context = $context;
        $this->course = $course;
    }

    public function export_for_template(renderer_base $output) {
        $productentity = new product();

        return [
            'courseid' => $this->course->id,
            'products' => $productentity->get_all_course_products($this->course->id)
        ];
    }
}
