import React from 'react'


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to give draggable user experience to add fields in 2nd step of import user
 * process.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Object} props.nodeData Node data
 * @param {String} props.nodeData.db_name Database name
 * @param {String} props.nodeData.label Label first name
 * @param {Boolean} props.nodeData.required Field required status
 * @param {String} props.category Category name
 * @param {React.Component} props.children Child component to render
 * @returns {JSX.Element}
 * @constructor
 */
const Draggable = (props) => {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Since for Compatibility Issue with IE 11.
     * e.dataTransfer.setData( category, data);
     * we can only use first parameter(category) in setData is "Text" other categories
     * will be discarded by IE11 but work in others check out this StackOverflow answer
     * link: https://stackoverflow.com/a/28740710/8093912
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e javascript event object
     */
    const drag = (e) => {
        const category = props.category || "Text";
        e.dataTransfer.setData(category, JSON.stringify({id: e.target.id, data: props.nodeData}));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will restrict overlapping of draggable components(eg - fields).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e javascript event object
     */
    const noAllowDrop = (e) => {
        e.preventDefault();
        e.stopPropagation();
    }
    const {nodeId, styles, nodeData} = props;
    return (
        <div id={nodeId} draggable="true" onDragStart={drag} onDragOver={noAllowDrop} style={styles}>
            {props.children}
        </div>
    )
}
export default Draggable