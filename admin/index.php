<?php

/**
 * Index admin page.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$context = context_system::instance();

$PAGE->set_url('/local/marketplace/admin/index.php');
$PAGE->set_title(get_string('pluginname', 'local_marketplace'));
$PAGE->set_heading(get_string('pluginname', 'local_marketplace'));
$PAGE->set_context($context);

\local_marketplace\util\menu::fill_secondary_menu_with_admin_items();

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('local_marketplace', 'admin');

$contentrenderable = new \local_marketplace\output\admin\index($context);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
