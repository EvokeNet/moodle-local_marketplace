<?php

/**
 * Categories admin page.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
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

$categoryentity = new \local_marketplace\local\entities\category();

$dbcategory = null;
if ($action == 'update' || $action == 'delete') {
    $dbcategory = $DB->get_record('marketplace_categories', ['id' => $id], '*', MUST_EXIST);
}

if ($action == 'delete') {
    try {
        if (!confirm_sesskey()) {
            redirect($redirecturl, get_string('invaliddata', 'error'), null, \core\output\notification::NOTIFY_ERROR);
        }

        $id = required_param('id', PARAM_INT);

        if ($categoryentity->has_children($id)) {
            redirect($redirecturl, get_string('deleteitem_itemwithchildren', 'error'), null, \core\output\notification::NOTIFY_WARNING);
        }

        list($success, $message) = $categoryentity->delete($id);

        if ($success) {
            redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
        }

        redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_ERROR);

    } catch (\Exception $e) {
        redirect($redirecturl, get_string('invaliddata', 'error'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

$form = new \local_marketplace\forms\admin\categories($url, $dbcategory);

if ($form->is_cancelled()) {
    redirect($redirecturl);
}

if ($formdata = $form->get_data()) {
    $category = new \stdClass();
    $category->name = $formdata->name;
    $category->timemodified = time();

    $redirecturl = new moodle_url('/local/marketplace/admin/categories.php');

    $success = false;
    $message = '';
    if ($action == 'create') {
        $category->timecreated = time();

        list($success, $message) = $categoryentity->create($category);
    }

    if ($action == 'update') {
        $category->id = $id;

        list($success, $message) = $categoryentity->update($category);
    }

    if ($success) {
        redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
    }

    redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_ERROR);
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();
