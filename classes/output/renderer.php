<?php

/**
 * Marketplace main renderer
 *
 * @package     local_marketplace
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

namespace local_marketplace\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;
use renderable;

class renderer extends plugin_renderer_base {
    public function render_admin(renderable $page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_marketplace/admin', $data);
    }

    public function render_categories(renderable $page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_marketplace/categories', $data);
    }

    public function render_products(renderable $page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_marketplace/products', $data);
    }
}