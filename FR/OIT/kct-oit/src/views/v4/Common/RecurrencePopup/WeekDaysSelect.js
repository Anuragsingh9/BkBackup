import React, {useState} from "react";
import {Grid} from "@material-ui/core";
import "../../../CreateEvent/EventPreparation/Scheduling/RecurrencePopup.css"
import "./RecurrencePopup.css";
import CheckBoxInput from "../FormInput/CheckBoxInput";


/**
 * --------------------------------------------------------------------------------------------------------------------
 * @description This component is used for create event which takes start time , end time and date for events
 * and also set types for event
 * --------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.startDate Event start date linked with event actual start date
 * @param {Number} props.recInterval Interval of recurring with respect to type
 * @param {Number} props.recType Type of the recurring
 * @param {Number} props.weekDays Selected weekdays if recurring is weekly or weekdays type in number
 * @param {Number} props.onDay The value for the month data on which the event will recur
 * @param {String} props.endDate Recurrence end date
 * @param {Boolean} props.disabled To indicate if the fields are disabled or not
 * @param {Function} props.onSave The handler method for the saving of the recurrence details
 * @param {Function} props.onClose The handler for the closing popup
 * @returns {JSX.Element}
 * @constructor
 */
let WeekDaysSelect = (props) => {
    const weekDayLabel = {
        mon: {short: 'M', mid: 'Mon', long: 'Monday'},
        tue: {short: 'T', mid: 'Tue', long: 'Tuesday'},
        wed: {short: 'W', mid: 'Wed', long: 'Wednesday'},
        thu: {short: 'T', mid: 'Thu', long: 'Thursday'},
        fri: {short: 'F', mid: 'Fri', long: 'Friday'},
        sat: {short: 'S', mid: 'Sat', long: 'Saturday'},
        sun: {short: 'S', mid: 'Sun', long: 'Sunday'},
    }

    return (
        <>
            {/* adding empty space as there is no label for week day selection */}
            <Grid item lg={5} />
            <Grid item lg={7} className="v4_colorTheme">
                <div>
                    {/* iterating through each day of week and preparing the check box */}
                    {Object.keys(weekDayLabel).map((weekDay, index) => {
                        return <CheckBoxInput
                            name={`${props.name}[${Object.keys(weekDayLabel)[index]}]`}
                            // onChange={() => handleWeekDayUpdate(weekDay)}
                            icon={
                                <span style={{fontWeight: 'bold'}}>{weekDayLabel[weekDay].short}</span>
                            }
                            checkedIcon={
                                <span style={{fontWeight: 'bold'}}
                                    sx={{bgcolor: 'primary.main'}}>{weekDayLabel[weekDay].short}</span>
                            }
                        />
                    })}
                </div>
            </Grid>
        </>
    )
};

export default WeekDaysSelect;
