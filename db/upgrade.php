<?php

/**
 * Upgrade file.
 *
 * @package     local_marketplace
 * @copyright   2023 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Upgrade code for the marketplace local plugin.
 *
 * @param int $oldversion - the version we are upgrading from.
 *
 * @return bool result
 *
 * @throws ddl_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_local_marketplace_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2023020200) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('marketplace_products');

        if ($dbman->table_exists($table)) {
                $field = new xmldb_field('limitperuser', XMLDB_TYPE_INTEGER, '10', true, null, null, null, 'stock');

            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2023020200, 'local', 'marketplace');
    }

    return true;
}