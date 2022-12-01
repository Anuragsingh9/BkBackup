import React, {useState} from 'react';
import './EventAgenda.css';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a child component that shows event agenda for that event and manage the show and hide state
 * for agenda section on "Events platform"
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {String} props.startDate Event start date
 * @param {String} props.endDate Event end date
 * @returns {JSX.Element}
 * @constructor
 */
const EventAgenda = (props) => {

    const [showInfo, setshowInfo] = useState("View All")

    const [visible, setvisible] = useState(0)

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle the states for show and hide agenda section and update the current
     * state on button click.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const showAll = () => {
        if (showInfo == 'View All') {
            setshowInfo("View Less");
            setvisible(1);
        } else {
            setshowInfo("View All");
            setvisible(0);
        }
    }

    return (
        <div className="viewAllWrap">
            <div className="viewAll_button">
                <p className="viewAll_txt" onClick={showAll}>{showInfo}</p>
                <div class="gray_saprator"></div>
            </div>
            {
                visible == 1 && <div className="fullInfoWrap">
                    <p className="customPara">Networking 1</p>
                    <p>{props.startDate}<span className="customPara"> - </span>{props.endDate}</p>
                    <p>Lorem ipsum dor velorLorem ipsum dor velor Lorem ipsum dor velor Lorem ipsum dor velor Lorem
                        ipsum dor velor Lorem ipsum dor velor Lorem ipsum dor velor Lorem ipsum dor velor
                    </p>
                </div>
            }
        </div>
    )
}

export default EventAgenda;