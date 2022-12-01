import React from 'react';
import Badge from 'react-bootstrap/Badge';
import Svg from '../../../Svg';
import ReactTooltip from 'react-tooltip';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render an icon(with number badge) for event agenda(no of moments will be
 * there in an event) in the event list - past/future
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {EventData} props.agenda Event data with agenda data included in that
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const ShowAgenda = (props) => {
    return (
        <div
            className="show_agenda_div"
            data-tip={`No of Moments: ${props.agenda.agenda.length}`}
            data-for='agenda_number'
        >
			<span
                dangerouslySetInnerHTML={{__html: Svg.ICON.agenda_Icon}}
            ></span>
            <Badge
                className="agenda_count_badge"
                pill='true'
            >
                <small>{props.agenda.agenda.length}</small>
            </Badge>
            <ReactTooltip type="dark" effect="solid" id='agenda_number' />
        </div>
    );
}
export default ShowAgenda;

