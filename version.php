<?php

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_marketplace
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_marketplace';
$plugin->release = '0.6.0';
$plugin->version = 2023031000;
$plugin->requires = 2022041200;
$plugin->maturity = MATURITY_STABLE;
$plugin->dependencies = [
    'local_evokegame' => 2022071800
];
