<?php

/**
 * Configure course badges.
 *
 * @package     mod_evokeportfolio
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../../config.php');

$action = optional_param('action', null, PARAM_ALPHANUMEXT);
$id = optional_param('id', null, PARAM_INT);

require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$params = [];
if ($action) {
    $params['action'] = $action;
}

if ($id) {
    $params['id'] = $id;
}

$url = new moodle_url('/local/marketplace/admin/categories.php', $params);

$PAGE->set_url($url);

$PAGE->set_title(get_string('categories', 'local_marketplace'));
$PAGE->set_heading(get_string('categories', 'local_marketplace'));
$PAGE->set_context($context);

\local_marketplace\util\menu::fill_secondary_menu_with_admin_items();

$renderer = $PAGE->get_renderer('local_marketplace', 'admin');

if (!$action) {
    $contentrenderable = new \local_marketplace\output\admin\categories($context);

    echo $OUTPUT->header();

    echo $renderer->render($contentrenderable);

    echo $OUTPUT->footer();

    exit;
}

$redirecturl = new moodle_url('/local/marketplace/admin/categories.php');

if ($action == 'delete') {
    try {
        if (!confirm_sesskey()) {
            redirect($redirecturl, get_string('invaliddata', 'error'), null, \core\output\notification::NOTIFY_ERROR);
        }

        $id = required_param('id', PARAM_INT);

        $DB->get_record('marketplace_categories', ['id' => $id], '*', MUST_EXIST);

        if ($DB->count_records('marketplace_products', ['categoryid' => $id])) {
            redirect($redirecturl, get_string('deleteitem_itemwithchildren', 'error'), null, \core\output\notification::NOTIFY_WARNING);
        }

        $DB->delete_records('marketplace_categories', ['id' => $id]);

        redirect($redirecturl, get_string('category_delete_success', 'error'), null, \core\output\notification::NOTIFY_SUCCESS);
    } catch (\Exception $e) {
        redirect($redirecturl, get_string('invaliddata', 'error'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

$customdata = null;
if ($id && $action == 'update') {
    $customdata = $DB->get_record('marketplace_categories', ['id' => $id], '*', MUST_EXIST);
}
$form = new \local_marketplace\forms\admin\categories($url, $customdata);

if ($form->is_cancelled()) {
    redirect($redirecturl);
}

if ($formdata = $form->get_data()) {
    $category = new \stdClass();
    $category->name = $formdata->name;

    $redirecturl = new moodle_url('/local/marketplace/admin/categories.php');

    if ($action == 'create') {
        $category->timecreated = time();
        $category->timemodified = time();

        $DB->insert_record('marketplace_categories', $category);

        redirect($redirecturl, get_string('category_create_success', 'local_marketplace'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

    if ($action == 'update') {
        $category->id = $id;
        $category->timemodified = time();

        $DB->update_record('marketplace_categories', $category);

        redirect($redirecturl, get_string('category_update_success', 'local_marketplace'), null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();

