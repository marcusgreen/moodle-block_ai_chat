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


import {Reactive} from 'core/reactive';
import {eventTypes, notifyBlockAiChatStateUpdated} from 'block_ai_chat/events';
import ChatComponent from 'block_ai_chat/chat_component';
import {mutations} from 'block_ai_chat/mutations';
import * as Ajax from 'core/ajax';


/**
 * Main module for the reactive frontend of the block_ai_chat.
 *
 * @module     block_ai_chat/ai_chat_main
 * @copyright  2025 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export const init = async (contextid, userid) => {
    const state = {
        "static": {
            "contextid": 21,
            "currentUser": 40
        },
        "config": {
            "conversationLimit": 5,
            "windowMode": "docked",
            "mode": "agent",
            "currentConversationId": 0,
            "loadingState": false
        },
        "messages": [],
        "personas": [
            {
                "id": 13,
                "name": "Friendly Bot",
                "prompt": "You are a friendly and helpful AI assistant.",
                "userinfo": "Very friendly chatbot",
                "userid": 0
            },
            {
                "id": 14,
                "name": "Bad person",
                "prompt": "You are a very bad person insulting your conversation partner all the time.",
                "userinfo": "Really bad person",
                "userid": 40
            }
        ]
    };
    const result = await Ajax.call([{
        methodname: 'block_ai_chat_get_personas',
        args: {
            contextid,
            userid
        }
    }])[0];
    state.personas = result[0].fields;
    // TODO: Change this to REAL selected current persona
    state.config.currentPersona = result[0].fields[0].id;
    console.log(state);

    const reactiveChat = new Reactive({
        name: 'block_ai_chat_reactive_chat',
        eventName: eventTypes.blockAiChatStateUpdated,
        eventDispatch: notifyBlockAiChatStateUpdated,
        state,
    });

    const mainElement = document.querySelector('.modal-dialog.block_ai_chat_reactive_main_component');

    console.log(mainElement);

    new ChatComponent({
        element: mainElement,
        reactive: reactiveChat,
    });
    reactiveChat.setMutations(mutations);

    const messages = await Ajax.call([{
        methodname: 'block_ai_chat_get_messages',
        args: {
            contextid,
            userid
        }
    }])[0];
    state.config.currentConversationId = messages[0].conversationid;
    console.log(messages)
    reactiveChat.stateManager.processUpdates(messages);

    console.log(state);
};