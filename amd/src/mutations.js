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

import ajax from 'core/ajax';

/**
 * Default mutation manager
 *
 * @module     block_ai_chat/mutations
 * @class      block_ai_chat/mutations
 * @copyright  2025 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Mutations {

    selectCurrentPersona(stateManager) {
        console.log('mutatino called')
        // The first thing we need to do is get the current state.
        const state = stateManager.state;
        // State is always on read mode. To change any value first we need to unlock it.
        stateManager.setReadOnly(false);
        // Now we do as many state changes as we need.
        state.currentPersona = {
            userFacedText: 'asd'
        };
        // All mutations should restore the read mode. This will trigger all the reactive events.
        stateManager.setReadOnly(true);
    }

    /**
     * Private method to call core_courseformat_update_course webservice.
     *
     * @method _callPutEntryWebservice
     * @param {Number} activityid the activity id
     * @param {Object} fields the entry fields
     * @return {Array} of state updates
     */
    async _callSendMessage() {

        /*let ajaxresult = await ajax.call([{
            methodname: 'mod_nosferatu_put_entry',
            args: {
                activityid,
                fields,
            }
        }])[0];
        return ajaxresult;*/
    }



}

export const mutations = new Mutations();
