import React from 'react';
import TimePicker from '../../../../../Common/TimePicker/index.js';
import { Field, reduxForm } from 'redux-form';
import './TimeInputs.css';
import DoneAllIcon from '@mui/icons-material/DoneAll';
import DoneIcon from '@mui/icons-material/Done';
import ErrorOutlineIcon from '@mui/icons-material/ErrorOutline';
import CloseIcon from '../../../../../Svg/closeIcon.js';
import RightArrowIcon from '../../../../../Svg/RightArrowIcon';

/**
 * @functional 
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component returns there time input fields for key moments functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} date Date value
 * @param {Object} data Data for the event related time
 * @param {Number} index Index of the field
 * @param {Function} convertDateToTime Method to convert date to time
 * @param {Function} onDelete Function to handle on delete action
 * @param {Boolean} validated To check if the form is validated
 * @param {Boolean} disabled To check if the field is disabled
 * @param {Boolean} autoCreate To check if the event is auto created
 * @return {JSX.Element}
 * @constructor
 */
const TimeInput = ({date,data,index,convertDateToTime,onDelete,validated,disabled, autoCreate}) => {
    return(
        <div className="startEndTimeDiv">
              <Field
                name={`start_time-${index}`}
                type="time"
                onChange={(val)=>{convertDateToTime(val,'start_time',index)}}
                variant="outlined"
                className="ThemeInputTag-2"
                values={`${date} ${data.start_time}`}
                component={TimePicker}
                disabled={disabled}

            />
            <div className='common_TimePicker_div '><RightArrowIcon className="right_arr"/></div>
            <Field
                name={`end_time-${index}`}
                type="time"
                onChange={(val)=>{convertDateToTime(val,'end_time',index)}}
                variant="outlined"
                className="ThemeInputTag-2 endTime_subDiv"
                values={`${date} ${data.end_time}`}
                component={TimePicker}
                disabled={disabled}

            />
            <div className="IconWrapDiv">
                {validated ? <DoneAllIcon color="primary"/> : <ErrorOutlineIcon color="error"/>}
                { autoCreate === false  && 
                <CloseIcon color="secondary" style={{cursor:"pointer"}} onClick={onDelete}/>
            }
            </div>
        </div>
    )
}

export default TimeInput;