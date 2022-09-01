<?php

namespace local_marketplace\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;

/**
 * Marketplace renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class products implements renderable, templatable {
    protected $context;

    public function __construct($context) {
        $this->context = $context;
    }

    public function export_for_template(renderer_base $output) {
        return [];
    }
}
