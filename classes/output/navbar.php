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
class navbar implements renderable, templatable {
    public function export_for_template(renderer_base $output) {
        return [];
    }
}
