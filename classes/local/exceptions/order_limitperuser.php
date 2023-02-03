<?php

namespace local_marketplace\local\exceptions;

use moodle_url;
use moodle_exception;

/**
 * Product out of stock exception class.
 *
 * @package     local_marketplace
 * @copyright   2023 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class order_limitperuser extends moodle_exception {
    public function __construct($errorcode, $courseid) {
        $url = new moodle_url('/local/marketplace/index.php', ['id' => $courseid]);

        parent::__construct($errorcode, 'local_marketplace', $url, null, null);
    }
}
