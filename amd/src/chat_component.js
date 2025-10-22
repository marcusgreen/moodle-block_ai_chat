import {BaseComponent} from 'core/reactive';
import * as helper from 'block_ai_chat/helper';

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
            PERSONA_SELECT_DROPDOWN: `[data-block_ai_chat-element='personaselectdropdown']`,
            INPUT_TEXTAREA: `[data-block_ai_chat-element='inputtextarea']`,
            SUBMIT_BUTTON: `[data-block_ai_chat-element='submitbutton']`,
            LOADING_SPINNER_MESSAGE: `[data-block_ai_chat-element='loadingspinner']`,
            TEMPORARY_PROMPT_MESSAGE: `[data-block_ai_chat-element='temporaryprompt']`,
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
        const personaDropdownContainer = this.getElement(this.selectors.PERSONA_SELECT_DROPDOWN);
        this.reactive.state.personas.forEach((persona) => {
            const optionElement = document.createElement('option');
            optionElement.value = persona.id;
            optionElement.text = persona.name;
            personaDropdownContainer.appendChild(optionElement);
        });
        const personaBanner = this.getElement(this.selectors.PERSONABANNER);
        personaBanner.innerText = `${this.reactive.state.personas.get(state.config.currentPersona).userinfo}`;
        this.addEventListener(
            this.getElement(this.selectors.PERSONA_SELECT_DROPDOWN),
            'change',
            this._selectCurrentPersonaListener
        );
        const textarea = this.getElement(this.selectors.INPUT_TEXTAREA);
        console.log(textarea)
        const sendRequestButton = this.getElement(this.selectors.SUBMIT_BUTTON);
        sendRequestButton.addEventListener('click', this._submitAiRequestListener.bind(this));
        this._refreshPersona({element: state.config});
    }

    /**
     * We want to update the person every time something in its state change. To do this we need
     * to define a watcher.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `messages:created`, handler: this._addMessageToChatArea},
            {watch: `config.currentPersona:updated`, handler: this._refreshPersona},
            {watch: `config.loadingState:updated`, handler: this._handleLoadingStateUpdated},
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
    async _addMessageToChatArea({element}) {
        // We have a convenience method to locate elements inside the component.
        console.log('MESSAGE WIRD HINZUGEFÜGT')
        console.log(element);
        // TODO Filter messages that do not belong to the current conversation

        let placeholder = document.createElement('div');
        placeholder.setAttribute('data-id', element.id);
        let node = this.element.querySelector('.block_ai_chat-output');
        node.appendChild(placeholder);
        const templateData = {
            id: element.id,
            senderai: element.sender === 'ai',
            content: element.content,
            loading: element.hasOwnProperty('loading') ? element.loading : false,
        };
        const newcomponent = await this.renderComponent(placeholder, 'block_ai_chat/message', templateData);
        const newelement = newcomponent.getElement();
        node.replaceChild(newelement, placeholder);

        // Add copy listener for question and reply.
        helper.attachCopyListenerLast();

        // Scroll the modal content to the bottom.
        helper.scrollToBottom();
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
        const newPersonaId = element.currentPersona;
        this.getElement(this.selectors.PERSONABANNER).innerText = `${this.reactive.state.personas.get(newPersonaId).userinfo}`;
    }


    _putPersonaListener() {
        // We don't want to submit the form.
        event.preventDefault();
        // Get the selected person id.
        console.log('Firing the event')
        const persona = {
            userid: 0,
            name: "new name",
            prompt: "New prompt",
            userinfo: "Das ist eine neue persona",
        };
        this.reactive.dispatch('putPersona', 21, persona);
    }

    _selectCurrentPersonaListener() {
        event.preventDefault();
        this.reactive.dispatch('selectCurrentPersona', this.reactive.state.static.contextid, event.target.value);
    }

    _submitAiRequestListener() {
        event.preventDefault();
        const textarea = this.getElement(this.selectors.INPUT_TEXTAREA);
        const prompt = textarea.value;
        this.reactive.dispatch('submitAiRequest', prompt);
    }

    async _handleLoadingStateUpdated({element}) {
        const loadingSpinnerMessage = {
            'id': 'loadingspinner',
            'sender': 'user',
            'loading': true
        };

        const temporaryPromptMessage= {
            'id': 'temporaryprompt',
            'sender': 'user',
            'content': this.getElement(this.selectors.INPUT_TEXTAREA).value,
        };

        if (element.loadingState) {
            await this._addMessageToChatArea({element: temporaryPromptMessage});
            await this._addMessageToChatArea({element: loadingSpinnerMessage});
            this.getElement(this.selectors.INPUT_TEXTAREA).value = '';
        }
    }
}

export default ChatComponent;