<?php

/**
 * Configure course badges.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

$id = required_param('id', PARAM_INT);

require_login($id);

$context = context_course::instance($id);

$PAGE->set_url(new moodle_url('/local/marketplace/mypurchases.php', ['id' => $id]));
$PAGE->set_title(get_string('pluginname', 'local_marketplace'));
$PAGE->set_heading(get_string('pluginname', 'local_marketplace'));
$PAGE->set_context($context);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('local_marketplace');

$contentrenderable = new \local_marketplace\output\mypurchases($id, $context);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
