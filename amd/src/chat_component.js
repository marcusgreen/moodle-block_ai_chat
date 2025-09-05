import {BaseComponent} from 'core/reactive';

class ChatComponent extends BaseComponent {

    /**
     * It is important to follow some conventions while you write components. This way all components
     * will be implemented in a similar way and anybody will be able to understand how it works.
     *
     * All the component definition should be initialized on the "create" method.
     */
    create() {
        // This is an optional name for the debugging messages.
        this.name = 'ChatComponent';
        // We will always define our component HTML selectors and classes this way so we only define
        // once and we don't contaminate our logic with tags and other stuff.
        this.selectors = {
            MESSAGES: `[data-block_ai_chat-element='messages']`,
            PERSONABANNER: `[data-block_ai_chat-element='personabanner']`,
            PERSONA_UPDATE_BUTTON: `[data-block_ai_chat-element='personaupdatebutton']`,
        };/*
        this.classes = {
            BITTEN: `bitten`,
        };*/
        // If you need local attributes like ids os something it should be initialized here.
    }

    /**
     * Initial state ready method.
     *
     * Note in this case we want our stateReady to be async.
     *
     * @param {object} state the initial state
     */
    stateReady(state) {
        console.log(state)
        this.addEventListener(
            this.getElement(this.selectors.PERSONA_UPDATE_BUTTON),
            'click',
            this._selectPersonaListener
        );
        this._refreshMessages({element: state});
        this._refreshPersona({element: state});
    }

    /**
     * We want to update the person every time something in its state change. To do this we need
     * to define a watcher.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `state.messages:updated`, handler: this._refreshMessages},
            {watch: `state.currentPersona:updated`, handler: this._refreshPersona},
        ];
    }

    /**
     * We will trigger that method any time a person data changes. This method is used by stateReady
     * but, most important, to watch the state. Any watcher receive an object with:
     * - element: the afected element (a person in this case)
     * - state: the full state object
     *
     * @param {object} param the watcher param.
     * @param {object} param.element the person structure.
     */
    _refreshMessages({element}) {
        // We have a convenience method to locate elements inside the component.
        console.log(element);
        //const target = this.getElement(this.selectors.MESSAGES, element.id);
    }

    /**
     * We will trigger that method any time a person data changes. This method is used by stateReady
     * but, most important, to watch the state. Any watcher receive an object with:
     * - element: the afected element (a person in this case)
     * - state: the full state object
     *
     * @param {object} param the watcher param.
     * @param {object} param.element the person structure.
     */
    _refreshPersona({element}) {
        // We have a convenience method to locate elements inside the component.
        console.log(element)
        console.log(element.currentPersona);
        console.log('PERSONA WIRD REFRESHED')
        console.log(this.getElement(this.selectors.PERSONABANNER))
        this.getElement(this.selectors.PERSONABANNER).innerText = `userFacedText: ${element.currentPersona.userFacedText}`;
        //const target = this.getElement(this.selectors.MESSAGES, element.id);
    }


    _selectPersonaListener() {
        // We don't want to submit the form.
        event.preventDefault();
        // Get the selected person id.
console.log('Firing the event')
        this.reactive.dispatch('selectCurrentPersona');
    }
}

export default ChatComponent;