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

namespace block_ai_chat\external;

use block_ai_chat\manager;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_value;

/**
 * Class reload_persona.
 *
 * @package    block_ai_chat
 * @copyright  2024 Tobias Garske, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_personas extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'Block contextid.', VALUE_REQUIRED),
            'userid' => new external_value(PARAM_INT, 'Persona id.', VALUE_REQUIRED),
        ]);
    }

    /**
     * Execute the service.
     *
     * @param int $contextid
     * @return array
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public static function execute(int $contextid, int $userid): array {
        [
            'contextid' => $contextid,
            'userid' => $userid,
        ] =
            self::validate_parameters(self::execute_parameters(), [
                'contextid' => $contextid,
                'userid' => $userid,
            ]);
        self::validate_context(\core\context_helper::instance_by_id($contextid));
        // TODO use correct capability
        require_capability('local/ai_manager:use', \context::instance_by_id($contextid));

        $manager = new manager($contextid);
        return $manager->get_personas($userid);
    }

    public static function execute_returns(): external_multiple_structure {
        return manager::get_update_structure(new external_multiple_structure(manager::get_persona_structure(), 'List of personas', VALUE_OPTIONAL));
    }
}
