<?php

/**
 * Configure course badges.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

require_login();

$context = context_system::instance();

$PAGE->set_url('/local/marketplace/products.php');
$PAGE->set_title(get_string('products', 'local_marketplace'));
$PAGE->set_heading(get_string('products', 'local_marketplace'));
$PAGE->set_context($context);

\local_marketplace\util\menu::fill_secondary_menu_with_admin_items();

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('local_marketplace');

$contentrenderable = new \local_marketplace\output\products($context);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
