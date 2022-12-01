import React from 'react'


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to manage actions after dropping an element into it (eg.- fields from left
 * to right in 2nd step of import ser process).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {String} props.category Category name
 * @param {Function} props.onDropHandler Function is used for drop handler
 * @param {Number} props.nodeId Id of node
 * @param {Object} props.styles Style used for design
 * @param {React.Component} props.children Child component to render
 * @returns {JSX.Element}
 * @constructor
 */
const Droppable = (props) => {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user drop some element into dropbox component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const drop = (e) => {
        e.preventDefault();
        if (e.target.getAttribute('id') === e.currentTarget.getAttribute('id') || 1) {
            const category = props.category || "Text";
            const data = e.dataTransfer.getData(category);
            try {
                if (props.onDropHandler) {
                    props.onDropHandler(JSON.parse(data))
                }
            } catch (err) {
                console.error(err)
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function makes component allow dropping.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const allowDrop = (e) => {
        e.preventDefault();
    }
    const {nodeId, styles} = props;
    return (
        <div id={nodeId} onDrop={drop} onDragOver={allowDrop} style={styles}>
            {props.children}
        </div>
    )
}
export default Droppable