<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.


/**
 * Upgrade code for qtype_code
 *
 * @package     qtype_code
 * @copyright   2023 Stefan Wagner
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Upgrade function
 *
 * @param int $oldversion old version
 * @return void
 */
function xmldb_qtype_code_upgrade($oldversion) {

    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2023040701) {

        // Define table qtype_code_options to be created.
        $table = new xmldb_table('qtype_code_options');

        // Adding fields to table qtype_code_options.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('language', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('responsetemplate', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table qtype_code_options.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('questionid', XMLDB_KEY_FOREIGN_UNIQUE, ['questionid'], 'question', ['id']);

        // Conditionally launch create table for qtype_code_options.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Code savepoint reached.
        upgrade_plugin_savepoint(true, 2023040701, 'qtype', 'code');
    }

    if ($oldversion < 2023041400) {
        // Define field intellisense to be added to qtype_code_options.
        $table = new xmldb_table('qtype_code_options');
        $field = new xmldb_field('intellisense', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '2', 'responsetemplate');

        // Conditionally launch add field intellisense.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Code savepoint reached.
        upgrade_plugin_savepoint(true, 2023041400, 'qtype', 'code');
    }

    if ($oldversion < 2023041401) {

        // Changing type of field intellisense on table qtype_code_options to text.
        $table = new xmldb_table('qtype_code_options');
        $field = new xmldb_field('intellisense', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'responsetemplate');

        // Launch change of type for field intellisense.
        $dbman->change_field_type($table, $field);

        // Code savepoint reached.
        upgrade_plugin_savepoint(true, 2023041401, 'qtype', 'code');
    }
}
