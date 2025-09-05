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

/**
 * Main module for the reactive frontend of the block_ai_chat.
 *
 * @module     block_ai_chat/ai_chat_main
 * @copyright  2025 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export const init = () => {
    const state = {
        agentMode: {
            active: false,
        },
        messages: [
            {
                id: 1,
                text: "first message"
            }
        ],
        currentPersona: {
            userFacedText: "I'm a crazy professor",
            prompt: "You are a crazy professor and always answers in a crazy style"
        }
    };


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
};