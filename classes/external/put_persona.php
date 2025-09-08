<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This is the external method for getting the information needed to present an attempts report.
 *
 * @package    mod_nosferatu
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_ai_chat\external;

use block_ai_chat\manager;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_value;
use moodle_exception;
use context_module;
use stdClass;

/**
 * Get the initial state.
 *
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class put_persona extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
                [
                        'contextid' => new external_value(PARAM_INT, 'The block_ai_chat context id'),
                        'fields' => manager::get_persona_structure(),
                ]
        );
    }

    /**
     * Return user attempts information in an activity.
     *
     * @param  int $contextid The activity id
     * @param  stdClass $fields the entry fields to update
     * @return stdClass report data
     *@throws  moodle_exception if the user cannot see the report
     */
    public static function execute(int $contextid, array $fields): stdClass {
        global $USER;

        $params = external_api::validate_parameters(self::execute_parameters(), [
                'contextid' => $contextid,
                'fields' => $fields,
        ]);
        $contextid = $params['contextid'];
        $fields = (object)$params['fields'];

        $context = \context_helper::instance_by_id($contextid);
        self::validate_context($context);

        $manager = new manager($contextid);
        return $manager->put_persona($fields);
    }

    /**
     * Describes the get_h5pactivity_access_information return value.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return manager::get_update_structure();
    }

}
