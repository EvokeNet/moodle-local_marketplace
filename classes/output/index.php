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
class index implements renderable, templatable {
    protected $courseid;
    protected $context;

    public function __construct($courseid, $context) {
        $this->courseid = $courseid;
        $this->context = $context;
    }

    public function export_for_template(renderer_base $output) {
        $productentity = new product();

        return [
            'courseid' => $this->courseid,
            'products' => $productentity->get_site_and_course_products($this->courseid)
        ];
    }
}
