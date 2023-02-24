<?php

/**
 * Index admin page.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

global $DB, $CFG;

$id = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

$context = context_course::instance($id);

require_course_login($id);

require_capability('moodle/course:update', $context);

require_once($CFG->libdir . '/csvlib.class.php');

$csvexport = new \csv_export_writer('semicolon');

$filename = strtolower(str_replace(' ', '_', trim($course->shortname))) . '_csv';

$csvexport->set_filename(clean_filename($filename));

$orderentity = new \local_marketplace\local\entities\order();
$productentity = new \local_marketplace\local\entities\product();

$orders = $orderentity->get_orders_with_users_data($course->id);

if (!$orders) {
    throw new \Exception('No orders found');
}

$fieldnames = [
    'id' => 'ID',
    'name' => 'Product name',
    'description' => 'Product description',
    'price' => 'Purchase price',
    'purchasedatetime' => 'Purchase datetime',
    'fullname' => 'User full name',
    'email' => 'User e-mail',
    'usergroups' => 'User groups',
];

// Add the header line to the data.
$csvexport->add_data($fieldnames);

$userutil = new \local_marketplace\util\user();
foreach ($orders as $order) {
    $csvexport->add_data([
        'id' => $order->id,
        'name' => $order->name,
        'description' => $order->description,
        'price' => $order->price,
        'purchasedatetime' => userdate($order->timecreated),
        'fullname' => fullname($order),
        'email' => $order->email,
        'usergroups' => $userutil->get_user_groups_names($course->id, $order->userid)
    ]);
}

$csvexport->download_file();
