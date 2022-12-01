import React from "react";
import AddToCalendar from "react-add-to-calendar";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a addToCalender component which is used to add an event reminder in calender.This component is
 * currently using in quick registration page.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {string} calenderData
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
function Calender({calenderData}) {
    // types of calender props of AddToCalendar
    let items = [
        {google: ' Google'},
        {outlook: ' Outlook'},
        {outlookcom: ' Outlook.com'},
    ];
    // icon for dropdown menu of calender
    let icon = {'calendar-plus-o': 'right'};

    return (
        <AddToCalendar
            event={calenderData}
            buttonLabel=""
            buttonTemplate={icon}
            listItems={items}
        />
    )
}

export default Calender;


