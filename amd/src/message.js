import {BaseComponent} from 'core/reactive';

/**
 * Component representing a column in a kanban board.
 */
export default class extends BaseComponent {

    /**
     * Function to initialize component, called by mustache template.
     * @param {*} target The id of the HTMLElement to attach to
     * @returns {BaseComponent} New component attached to the HTMLElement represented by target
     */
    static init(target) {
        let element = document.querySelector(target);
        return new this({
            element: element,
        });
    }

    create() {
        this.id = this.element.dataset.block_ai_chatMessageid;
    }

    /**
     * Watchers for this component.
     * @returns {array}
     */
    getWatchers() {
        return [
            {watch: `config.loadingState:updated`, handler: this._removeTemporaryMessage.bind(this)},
/*            {watch: `columns[${this.id}]:updated`, handler: this._columnUpdated},
            {watch: `columns[${this.id}]:deleted`, handler: this._columnDeleted},
            {watch: `cards:created`, handler: this._cardCreated}*/
        ];
    }

    destroy() {
        this.getElement().remove();
    }

    _removeTemporaryMessage({element}) {
        if (element.loadingState) {
            return;
        }
        if (this.id !== 'temporaryprompt' && this.id !== 'loadingspinner') {
            return;
        }
        this.destroy();
    }

}
