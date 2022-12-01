import React, {useState} from 'react';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render moment's description(if added already while creating and event).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.discription Description value to display on toggle
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const ToggleInfo = (props) => {
    const [viewDetails, setviewDetails] = useState(false);
    const [viewTxt, setviewTxt] = useState("More")

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle show/hide actions for description text(Description of a moment).
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    const toggle = () => {
        if (viewDetails == true) {
            setviewDetails(false);
            setviewTxt("More");
        } else {
            setviewDetails(true);
            setviewTxt("Less");
        }
    }
    return (
        <>
            <p onClick={toggle} className="more_detail_link">{viewTxt} Details ...</p>
            {viewDetails && <p className="more_detail_content">{props.discription}</p>}
        </>
    )
}
export default ToggleInfo
