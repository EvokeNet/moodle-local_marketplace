<?php

/**
 * Index admin page.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

$id = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

$context = context_course::instance($id);

require_course_login($id);

require_capability('moodle/course:update', $context);

$title = get_string('pluginname', 'local_marketplace') . ' : ' . $course->shortname;

$PAGE->set_url('/local/marketplace/courseproducts.php');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_context($context);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('local_marketplace', 'admin');

$contentrenderable = new \local_marketplace\output\courseproducts($context, $course);

echo $renderer->render($contentrenderable);

echo $OUTPUT->footer();
