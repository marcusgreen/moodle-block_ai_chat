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

namespace block_ai_chat;

use block_ai_chat\local\persona;
use context_block;

use cm_info;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use stdClass;
use core_external\external_value;
use moodle_exception;

/**
 * Manager class for handling the reactive state of block_ai_chat
 *
 * @package    block_ai_chat
 * @copyright  2025 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** @var context_block the current context. */
    private $context;

    /**
     * Class contructor.
     *
     * @param cm_info $coursemodule course module info object
     * @param stdClass $instance instance object.
     */
    public function __construct(int $contextid) {
        $this->context = \context_helper::instance_by_id($contextid);
    }

    /**
     * Return the current context.
     *
     * @return context_block
     */
    public function get_context(): context_block {
        return $this->context;
    }

    /**
     * Create or update an entry.
     */
    public function put_persona(stdClass $data, int $userid = 0) {
        global $USER, $DB;
        if ($userid === 0) {
            require_admin();
        } else {
            require_capability('block/ai_chat:view', $this->context, $userid);
        }

        $personaobject = (object) [
                'userid' => $data->userid,
                'name' => $data->name,
                'prompt' => $data->prompt,
                'userinfo' => $data->userinfo,
                'timemodified' => time(),
        ];

        if (empty($data->id)) {
            $personaobject->timecreated = time();
            $personaobject->id = $DB->insert_record('block_ai_chat_personas', $personaobject);
        } else {
            $personaobject->id = $data->id;
            $DB->update_record('block_ai_chat_personas', $personaobject);
        }
        return (object) [
                [
                        [
                                'name' => 'personas',
                                'action' => empty($data->id) ? 'put' : 'update',
                                'fields' => $personaobject,
                        ]
                ]
        ];
    }

    /**
     * Return the current instance state object.
     *
     * @return stdClass the activity state.
     */
    public function get_state(): stdClass {
        global $DB;
        $state = new \stdClass();

        $currentpersona = $DB->get_record('block_ai_chat_personas_selected', ['contextid' => $this->context->id]);
        $state->aiChat = (object) [
                'contextid' => $this->context->id,
                'currentPersona' => $currentpersona->personasid,
        ];

        $personas = persona::get_all_personas();
        foreach ($personas as $persona) {
            $state->personas[] = (object) [
                    'id' => $persona->id,
                    'userid' => $persona->userid,
                    'name' => $persona->name,
                    'prompt' => $persona->prompt,
                    'userinfo' => $persona->userinfo,
            ];
        }
        return $state;
    }

    /**
     * Generate a entry state object.
     *
     * @param stdClass the entry record
     * @return stdClass the entry state object.
     */
    public function get_entry_state(stdClass $record): stdClass {
        return (object) [
                'id' => $record->id,
                'userid' => $record->userid,
                'current' => $record->current,
                'content' => $record->content,
        ];
    }

    /**
     * Generate a entry update object.
     *
     * @param stdClass the entry record
     * @return stdClass the entry update object.
     */
    public function get_entry_state_update(stdClass $record): stdClass {
        return (object) [
                'name' => 'entries',
                'action' => 'put',
                'fields' => $this->get_entry_state($record),
        ];
    }

    /**
     * Generate a entry delete object.
     *
     * @param int the entry id
     * @return stdClass the entry update object.
     */
    public function get_entry_state_delete(int $recordid): stdClass {
        return (object) [
                'name' => 'entries',
                'action' => 'delete',
                'fields' => (object) ['id' => $recordid],
        ];
    }

    /**
     * Get the state structure.
     *
     * @return external_single_structure
     */
    public static function get_state_structure(): external_single_structure {
        return new external_single_structure([
                'aiChat' => new external_single_structure(
                        [
                                'contextid' => new external_value(PARAM_INT, 'Activity ID'),
                                'currentPersona' => new external_value(PARAM_INT, 'id of the current persona')
                        ],
                        'AI chat general data'
                ),
                'personas' => new external_multiple_structure(
                        self::get_persona_structure(),
                        'The personas'
                ),
        ], 'AI chat state');
    }

    /**
     * Return the structure for a persona object
     *
     * @return external_single_structure
     */
    public static function get_persona_structure(): external_single_structure {
        return new external_single_structure(
                [
                        'id' => new external_value(PARAM_INT, 'persona id', VALUE_OPTIONAL),
                        'userid' => new external_value(PARAM_INT, 'The user id'),
                        'name' => new external_value(PARAM_RAW, 'The display name of the persona'),
                        'prompt' => new external_value(PARAM_RAW, 'Prompt of the persona'),
                        'userinfo' => new external_value(PARAM_RAW, 'The user info'),
                ]
        );
    }

    /**
     * Get the state structure.
     *
     * @return external_single_structure
     */
    public static function get_update_structure(): external_multiple_structure {
        return new external_multiple_structure(
                new external_single_structure(
                        [
                                'name' => new external_value(PARAM_INT, 'The state element to update'),
                                'action' => new external_value(PARAM_INT, 'The action to perform'),
                                'fields' => manager::get_persona_structure(),
                        ]
                ),
                'The activity entries list'
        );
    }
}
