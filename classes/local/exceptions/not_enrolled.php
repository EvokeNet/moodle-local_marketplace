<?php

namespace local_marketplace\local\exceptions;

use moodle_url;
use moodle_exception;

/**
 * Not enrolled exception class.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class not_enrolled extends moodle_exception {
    public function __construct($errorcode, $courseid) {
        $url = new moodle_url('/local/marketplace/index.php', ['id' => $courseid]);

        parent::__construct($errorcode, 'local_marketplace', $url, null, null);
    }
}
