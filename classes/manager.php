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
use local_ai_manager\ai_manager_utils;
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
        $returnpersona = [
            'id' => $personaobject->id,
            'userid' => $personaobject->userid,
            'name' => $personaobject->name,
            'prompt' => $personaobject->prompt,
            'userinfo' => $personaobject->userinfo,
        ];

        return [
            [
                //[
                'name' => 'personas',
                'action' => empty($data->id) ? 'put' : 'update',
                'fields' => $returnpersona,
                //]
            ]
        ];
    }

    public function get_personas(int $userid = 0): array {
        $personas = persona::get_all_personas($userid);
        if (empty($personas)) {
            return [];
        } else {
            return [
                [
                    'name' => 'personas',
                    'action' => 'put',
                    'fields' => $personas,
                ]
            ];
        }
    }

    public function get_messages(int $userid): array {
        // We limit to purpose 'chat' here because we do not want the requests from the integrated tiny_ai tools to be loaded
        // for displaying our conversations. This especially is a performance issue, because the field 'requestoptions' contains
        // base64 decoded images for purpose 'itt', for example, which slows down the database query extremely.
        $logentries = \local_ai_manager\ai_manager_utils::get_log_entries(
            'block_ai_chat',
            $this->context->id,
            $userid,
            0,
            false,
            '*',
            ['chat']
        );
        $messages = [];
        // Go over all log entries and create conversation items.
        foreach ($logentries as $logentry) {
            // Ignore values without itemid.
            if (empty($logentry->itemid)) {
                continue;
            }
            $messages = array_merge($messages, $this->convert_log_entry_to_messages($logentry));
        }
        return $messages;
    }

    public function select_persona(int $personaid): array {
        global $DB;
        $currentrecord = $DB->get_record('block_ai_chat_personas_selected', ['contextid' => $this->context->id]);
        if ($currentrecord) {
            $currentrecord->personasid = $personaid;
            $DB->update_record('block_ai_chat_personas_selected', $currentrecord);
        } else {
            $newrecord = new \stdClass();
            $newrecord->contextid = $this->context->id;
            $newrecord->personasid = $personaid;
            $DB->insert_record('block_ai_chat_personas_selected', $newrecord);
        }

        return [
            [
                'name' => 'config',
                'action' => 'update',
                'fields' => [
                    'currentPersona' => $personaid,
                ],
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
            'config' => new external_single_structure(
                [
                    'contextid' => new external_value(PARAM_INT, 'Activity ID'),
                    'currentPersona' => new external_value(PARAM_INT, 'id of the current persona', VALUE_OPTIONAL),
                    'conversationLimit' => new external_value(PARAM_INT, 'number of messages to pass to query', VALUE_OPTIONAL),
                    'windowMode' => new external_value(PARAM_TEXT, 'window mode', VALUE_OPTIONAL),
                    'mode' => new external_value(PARAM_TEXT, 'mode', VALUE_OPTIONAL)
                ],
                'AI chat general data'
            ),
            'messages' => new external_multiple_structure(
                self::get_messages_structure(),
                'The messages'
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
     * Return the structure for a message object
     *
     * @return external_single_structure
     */
    public static function get_message_structure(): external_single_structure {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_TEXT, 'persona id', VALUE_OPTIONAL),
                'conversationid' => new external_value(PARAM_INT, 'The conversation id'),
                'content' => new external_value(PARAM_RAW, 'The message content'),
                'sender' => new external_value(PARAM_TEXT, 'The sender (user or ai)'),
            ],
            'AI chat message data'
        );
    }

    public static function get_config_structure(): external_single_structure {
        return new external_single_structure(
            [
                'currentPersona' => new external_value(PARAM_INT, 'id of the current persona', VALUE_OPTIONAL),
                'conversationLimit' => new external_value(PARAM_INT, 'number of messages to pass to query', VALUE_OPTIONAL),
                'windowMode' => new external_value(PARAM_TEXT, 'window mode', VALUE_OPTIONAL),
                'mode' => new external_value(PARAM_TEXT, 'mode', VALUE_OPTIONAL),
            ],
            'AI chat config data'
        );
    }

    /**
     * Get the update structure.
     *
     * @return external_single_structure the update structure
     */
    public static function get_update_structure(external_single_structure|external_multiple_structure $fieldsstructure): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'name' => new external_value(PARAM_TEXT, 'The state element to update'),
                    'action' => new external_value(PARAM_TEXT, 'The action to perform'),
                    'fields' => $fieldsstructure,
                ]
            ),
            'Update structure for returning an update'
        );
    }

    public function request_ai(string $prompt, array $options): array {
        global $DB, $USER;
        $options['itemid'] = $options['conversationid'];
        $aimanager = new \local_ai_manager\manager('chat');
        $requestresult = $aimanager->perform_request($prompt, 'block_ai_chat', $this->context->id, $options);
        if ($requestresult->get_code() !== 200) {
            // TODO Proper error handling
            throw new \core\exception\moodle_exception('ERROR HANDLING STILL NEEDED');
        }
        // TODO So this is super inefficient of course. But we need to extend the AI manager to for example return the id of the log entry.
        $logentry = $DB->get_record('local_ai_manager_request_log', ['id' => $requestresult->get_logrecordid()]);

        return $this->convert_log_entry_to_messages($logentry);
    }

    public function convert_log_entry_to_messages(stdClass $logentry): array {
        $connectorfactory = \core\di::get(\local_ai_manager\local\connector_factory::class);
        $chatpurpose = $connectorfactory->get_purpose_by_purpose_string('chat');
        return [
            [
                'name' => 'messages',
                'action' => 'put',
                'fields' => [
                    'id' => $logentry->id . '-1',
                    'conversationid' => $logentry->itemid,
                    'content' => htmlspecialchars($logentry->prompttext),
                    'sender' => 'user',
                ]
            ],
            [
                'name' => 'messages',
                'action' => 'put',
                'fields' => [

                    'id' => $logentry->id . '-2',
                    'conversationid' => $logentry->itemid,
                    'content' => $chatpurpose->format_output($logentry->promptcompletion),
                    'sender' => 'ai',
                ],
            ],
        ];
    }
}
