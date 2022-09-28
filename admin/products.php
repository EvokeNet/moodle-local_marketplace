<?php

/**
 * Products admin page.
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

$url = new moodle_url('/local/marketplace/admin/products.php', $params);

$PAGE->set_url($url);
$PAGE->set_title(get_string('products', 'local_marketplace'));
$PAGE->set_heading(get_string('products', 'local_marketplace'));
$PAGE->set_context($context);

\local_marketplace\util\menu::fill_secondary_menu_with_admin_items();

$renderer = $PAGE->get_renderer('local_marketplace', 'admin');

if (!$action) {
    $contentrenderable = new \local_marketplace\output\admin\products($context);

    echo $OUTPUT->header();

    echo $renderer->render($contentrenderable);

    echo $OUTPUT->footer();

    exit;
}

$redirecturl = new moodle_url('/local/marketplace/admin/products.php');

$productentity = new \local_marketplace\local\entities\product();

$dbproduct = null;
if ($action == 'update' || $action == 'delete') {
    $dbproduct = $DB->get_record('marketplace_products', ['id' => $id], '*', MUST_EXIST);
}

if ($action == 'delete') {
    try {
        if (!confirm_sesskey()) {
            redirect($redirecturl, get_string('invaliddata', 'error'), null, \core\output\notification::NOTIFY_ERROR);
        }

        $id = required_param('id', PARAM_INT);

        if ($productentity->has_children($id)) {
            redirect($redirecturl, get_string('deleteitem_itemwithchildren', 'error'), null, \core\output\notification::NOTIFY_WARNING);
        }

        list($success, $message) = $productentity->delete($id);

        if ($success) {
            redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
        }

        redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_ERROR);

    } catch (\Exception $e) {
        redirect($redirecturl, get_string('invaliddata', 'error'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

$form = new \local_marketplace\forms\admin\products($url, $dbproduct);

if ($form->is_cancelled()) {
    redirect($redirecturl);
}

if ($formdata = $form->get_data()) {
    $stock = null;
    if ($formdata->stock == 0 || $formdata->stock != '') {
        $stock = (int) $formdata->stock;
    }

    $product = new \stdClass();
    $product->categoryid = $formdata->categoryid;
    $product->courseid = $formdata->courseid ?: null;
    $product->name = $formdata->name;
    $product->description = $formdata->description;
    $product->price = $formdata->price;
    $product->type = $formdata->type;
    $product->stock = $stock;
    $product->timemodified = time();

    $redirecturl = new moodle_url('/local/marketplace/admin/products.php');

    $success = false;
    $message = '';
    if ($action == 'create') {
        $product->timecreated = time();

        list($success, $message) = $productentity->create($product);
    }

    if ($action == 'update') {
        $product->id = $id;

        list($success, $message) = $productentity->update($product);
    }

    if ($success) {
        redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
    }

    redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_ERROR);
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();
