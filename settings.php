<?php

/**
 * This adds the custom fields management page.
 *
 * @package     local_marketplace
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('root', new admin_category('evoke_core', 'Evoke'));
$ADMIN->add('evoke_core', new admin_externalpage('evoke_marketplace', get_string('marketplace', 'local_marketplace'),
    new moodle_url('/local/marketplace/admin/index.php')));
