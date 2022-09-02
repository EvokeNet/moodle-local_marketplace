<?php

/**
 * Marketplace main renderer
 *
 * @package     local_marketplace
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

namespace local_marketplace\output\admin;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;
use renderable;

/**
 * Marketplace renderable class.
 *
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class renderer extends plugin_renderer_base {
    public function render_index(renderable $page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_marketplace/admin/index', $data);
    }

    public function render_categories(renderable $page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_marketplace/admin/categories', $data);
    }

    public function render_products(renderable $page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('local_marketplace/admin/products', $data);
    }
}