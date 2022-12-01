import React from "react";
import {FormControl, Grid, MenuItem} from "@material-ui/core";
import {FormControlLabel, Radio} from "@mui/material";
import "../../../CreateEvent/EventPreparation/Scheduling/RecurrencePopup.css"
import "./RecurrencePopup.css";
import RadioButtonInput from "../FormInput/RadioButtonInput";
import Constants from "../../../../Constants";
import NumberInput from "../FormInput/NumberInput";
import SelectField from "../FormInput/SelectField";

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
let MonthTypeSelect = (props) => {
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
            <Grid item lg={12}>
                <RadioButtonInput
                    name={props.name}
                >
                    <Grid item container lg={12} className="noPadding">
                        <Grid item lg={5} className="rightRadioBtn">
                            <FormControlLabel
                                value={Constants.recurrenceMonthType.ON_DAY}
                                control={<Radio />}
                                label="On Day"
                            />
                        </Grid>
                        <Grid item lg={7} className="v4_colorTheme radioBtnRow">
                            <NumberInput
                                name="event_rec_month_date"
                                id="recOnDay"
                                placeholder={""}
                                variant="outlined"
                                className="repeat-interval-field"
                                size={"small"}
                                inputProps={{min: 1, max: 31}}
                                disabled={
                                    props.formValues.event_rec_month_type !== Constants.recurrenceMonthType.ON_DAY
                                }
                            />
                        </Grid>
                    </Grid>

                    <Grid item container lg={12} className="noPadding">
                        <Grid item lg={5} className="rightRadioBtn">
                            <FormControlLabel
                                value={Constants.recurrenceMonthType.ON_THE}
                                control={<Radio />}
                                label="On The"
                            />
                        </Grid>

                        <Grid item lg={7} className="v4_colorTheme recTypeRow">
                            <FormControl variant="outlined" className="SelectRepeatType" size={"small"}>
                                <SelectField
                                    name="event_rec_on_month_week"
                                    labelId="demo-simple-select-outlined-label"
                                    disabled={
                                        props.formValues.event_rec_month_type !== Constants.recurrenceMonthType.ON_THE
                                    }
                                >
                                    <MenuItem value={Constants.recurrenceMonthWeek.FIRST}>First</MenuItem>
                                    <MenuItem value={Constants.recurrenceMonthWeek.SECOND}>Second</MenuItem>
                                    <MenuItem value={Constants.recurrenceMonthWeek.THIRD}>Third</MenuItem>
                                    <MenuItem value={Constants.recurrenceMonthWeek.FOURTH}>Fourth</MenuItem>
                                    <MenuItem value={Constants.recurrenceMonthWeek.LAST}>Last</MenuItem>
                                </SelectField>
                            </FormControl>
                            &nbsp;&nbsp;
                            <FormControl variant="outlined" className="SelectRepeatType" size={"small"}>
                                <SelectField
                                    name="event_rec_on_month_week_day"
                                    labelId="demo-simple-select-outlined-label"
                                    disabled={
                                        props.formValues.event_rec_month_type !== Constants.recurrenceMonthType.ON_THE
                                    }
                                >
                                    <MenuItem value={weekDayLabel.mon.long}>Monday</MenuItem>
                                    <MenuItem value={weekDayLabel.tue.long}>Tuesday</MenuItem>
                                    <MenuItem value={weekDayLabel.wed.long}>Wednesday</MenuItem>
                                    <MenuItem value={weekDayLabel.thu.long}>Thursday</MenuItem>
                                    <MenuItem value={weekDayLabel.fri.long}>Friday</MenuItem>
                                    <MenuItem value={weekDayLabel.sat.long}>Saturday</MenuItem>
                                    <MenuItem value={weekDayLabel.sun.long}>Sunday</MenuItem>
                                </SelectField>
                            </FormControl>
                        </Grid>
                    </Grid>
                </RadioButtonInput>

            </Grid>
        </>
    )
};

export default MonthTypeSelect;
