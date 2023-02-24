<?php

/**
 * Download course orders report
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

global $DB;

$id = required_param('id', PARAM_INT);
$format = optional_param('format', 'csv', PARAM_ALPHA);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

$context = context_course::instance($id);

require_course_login($id);

require_capability('moodle/course:update', $context);

$orderentity = new \local_marketplace\local\entities\order();
$productentity = new \local_marketplace\local\entities\product();

$orders = $orderentity->get_orders_with_users_data($course->id);

if (!$orders) {
    throw new \Exception('No orders found');
}

$headers = [
    'id' => 'ID',
    'name' => 'Product name',
    'description' => 'Product description',
    'price' => 'Purchase price',
    'purchasedatetime' => 'Purchase datetime',
    'fullname' => 'User full name',
    'email' => 'User e-mail',
    'usergroups' => 'User groups',
];

$userutil = new \local_marketplace\util\user();

$datatoexport = [];
foreach ($orders as $order) {
    $datatoexport[] = [
        'id' => $order->id,
        'name' => $order->name,
        'description' => $order->description,
        'price' => $order->price,
        'purchasedatetime' => userdate($order->timecreated),
        'fullname' => fullname($order),
        'email' => $order->email,
        'usergroups' => $userutil->get_user_groups_names($course->id, $order->userid)
    ];
}

$filename = strtolower(str_replace(' ', '_', trim($course->shortname)));

if (!$format || $format == 'csv') {
    generate_csv_file($datatoexport, $filename, $headers);
}

if ($format == 'excel') {
    generate_excel_file($datatoexport, $filename, $headers);
}

function generate_csv_file($datatoexport, $filename, $headers) {
    global $CFG;

    require_once($CFG->libdir . '/csvlib.class.php');

    $csvexport = new \csv_export_writer('semicolon');

    $csvexport->set_filename($filename);

    $csvexport->add_data($headers);

    foreach($datatoexport as $item) {
        $csvexport->add_data($item);
    }

    $csvexport->download_file();
}

function generate_excel_file($datatoexport, $filename, $headers) {
    global $CFG;

    require_once("$CFG->libdir/excellib.class.php");

    $filename = clean_filename($filename);

    $workbook = new \MoodleExcelWorkbook($filename);

    $worksheet[] = $workbook->add_worksheet(get_string('courseorders', 'local_marketplace'));

    array_unshift($datatoexport, $headers);

    $rowno = 0;
    foreach($datatoexport as $row) {
        $colno = 0;

        foreach($row as $col) {
            $worksheet[0]->write($rowno, $colno, $col);
            $colno++;
        }

        $rowno++;
    }

    $workbook->close();

    return $filename;
}
