import React, {useEffect, useState} from "react";
import {Field} from "redux-form";
import "./Scheduling.css";
import {FormControl, Grid, MenuItem, Select, TextField} from "@material-ui/core";
import moment from "moment-timezone";
import ModalBox from '../../../Common/ModalBox/ModalBox'
import {KeyboardDatePicker, MuiPickersUtilsProvider} from "@material-ui/pickers";
import DateFnsUtils from "@date-io/moment";
import Constants from "../../../../Constants";
import {Checkbox, FormControlLabel, Radio, RadioGroup} from "@mui/material";
import Helper from "../../../../Helper";
import "./RecurrencePopup.css"

/**
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method returns the datepicker component
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} input the input props to the component
 * @param {String} input.name Name of the input field
 * @param {Function} input.onChange The on change handler for the component
 * @param {String} label Label to display the for the component if any
 * @param {moment} defaultValue Default date value to render on fallback condition
 * @param {Boolean} touched To indicate if the input box is touched or not
 * @param {Boolean} error Error message from input box
 * @param {Object} custom Custom props to pass to component
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const renderDatePicker = (
    {
        input,
        label,
        defaultValue,
        meta: {touched, error},
        ...custom
    }
) => {
    return (
        <React.Fragment>
            <MuiPickersUtilsProvider utils={DateFnsUtils}>
                <KeyboardDatePicker
                    name={input.name}
                    variant="inline"
                    size="small"
                    inputVariant="outlined"
                    defaultValue={defaultValue}
                    onChange={input.onChange}
                    errorText={touched && error}
                    error={touched && error}
                    disablePast={true}
                    value={"2021-09-28"}
                    format={"YYYY-MM-DD"}
                    {...input}
                />
            </MuiPickersUtilsProvider>
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method returns number field component for redux form
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} input the input props to the component
 * @param {String} input.name Name of the input field
 * @param {Function} input.onChange The on change handler for the component
 * @param {String} label Label to display the for the component if any
 * @param {moment} defaultValue Default date value to render on fallback condition
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message from input box
 * @param {Object} custom Custom props to pass to component
 * @param {String} value Value of the input box
 * @param {Object} inputProps Input props for input box
 * @param {Object} invalid Enter value is invalid
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const renderNumberField = (
    {input, value, inputProps, label, defaultValue, meta: {invalid, touched, error}, ...custom},
) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={value}
                type="number"
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error && invalid}
                inputProps={inputProps}

                {...input}
                {...custom}
            />
            {touched && error && <span className={'text-danger'}>{error}</span>}
        </React.Fragment>
    );
}

const renderRadioField = (
    {input, value, inputProps, label, defaultValue, meta: {invalid, touched, error}, ...custom},
) => {
    return (
        <React.Fragment>
            <RadioGroup
                {...input}
                {...custom}
                valueSelected={input.value}
                onChange={(event, value) => input.onChange(value)}
            />
            {/*<TextField*/}
            {/*    name={input.name}*/}
            {/*    value={value}*/}
            {/*    type="radio"*/}
            {/*    onChange={input.onChange}*/}
            {/*    errorText={touched && error}*/}
            {/*    error={touched && error && invalid}*/}
            {/*    inputProps={inputProps}*/}

            {/*    {...input}*/}
            {/*    {...custom}*/}
            {/*/>*/}
            {touched && error && <span className={'text-danger'}>{error}</span>}
        </React.Fragment>
    );
}


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
const RecurrencePopup = (props) => {
    const weekDayLabel = {
        mon: {short: 'M', mid: 'Mon', long: 'Monday'},
        tue: {short: 'T', mid: 'Tue', long: 'Tuesday'},
        wed: {short: 'W', mid: 'Wed', long: 'Wednesday'},
        thu: {short: 'T', mid: 'Thu', long: 'Thursday'},
        fri: {short: 'F', mid: 'Fri', long: 'Friday'},
        sat: {short: 'S', mid: 'Sat', long: 'Saturday'},
        sun: {short: 'S', mid: 'Sun', long: 'Sunday'},
    }
    const [startDate, setStartDate] = useState(new moment());
    const [endDate, setEndDate] = useState(new moment());
    const [recInterval, setRecInterval] = useState(1);
    const [recType, setRecType] = useState(Constants.recurrenceType.MONTHLY);
    const [selectedWeekDays, setSelectedWeekDays] = useState({
        mon: false, tue: false, wed: false, thu: false, fri: false, sat: false, sun: false,
    });
    const [onDayOfMonth, setOnDayOfMonth] = useState(1);
    const [monthType, setMonthType] = useState(Constants.recurrenceMonthType.ON_DAY);
    const [onTheMonthWeek, setOnTheMonthWeek] = useState(Constants.recurrenceMonthWeek.FIRST);
    const [onTheMonthWeekDay, setOnTheMonthWeekDay] = useState(weekDayLabel.mon.long);
    const [recDetailText, setRecDetailText] = useState('Day');
    const [dateValidation, setDateValidation] = useState(null);

    useEffect(() => {
        let start = props.startDate ? new moment(props.startDate) : startDate;
        let end = props.endDate ? new moment(props.endDate) : endDate;
        setStartDate(start);
        setEndDate(end);

        if (end.format('YYYY-MM-DD') < start.format('YYYY-MM-DD')) {
            setDateValidation(true);
            return;
        } else {
            setDateValidation(false);
        }
        setRecInterval(props.recInterval ? props.recInterval : recInterval);
        if (props.recType === Constants.recurrenceType.WEEKDAY) {
            // if recurrence type is weekday the store it as weekly and on submit by selected weekdays type will set
            setRecType(Constants.recurrenceType.WEEKLY);
            setSelectedWeekDays(Helper.convertNumberToWeekDay(Constants.recWeekDayBinary));
        } else {
            setRecType(props.recType ? props.recType : recType);
            setSelectedWeekDays(props.weekDays ? Helper.convertNumberToWeekDay(props.weekDays) : selectedWeekDays);
            console.log('dddddddddddd week days', props.weekDays);
        }
        setOnDayOfMonth(props.onDay)
        setMonthType(props.recurrence_month_type ? props.recurrence_month_type : monthType);
        setOnTheMonthWeek(props.recurrence_on_month_week ? props.recurrence_on_month_week : onTheMonthWeek);
        setOnTheMonthWeekDay(props.recurrence_on_month_week_day ? props.recurrence_on_month_week_day : onTheMonthWeekDay);
    }, []);

    useEffect(() => {
        updateRecLine();
    }, [recInterval, selectedWeekDays, recType, onDayOfMonth, monthType, onTheMonthWeek, onTheMonthWeekDay, endDate]);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the date input component with provided field name and handler
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} label Label of the field
     * @param {String} fieldName Name of the field to match with redux form
     * @param {Function} onChange Handler of field when its value is changed
     * @param {Boolean} validate To indicate if the field is validated or not
     * @returns {JSX.Element}
     */
    const prepareDateInputComponent = (label, fieldName, onChange, validate = false) => {
        return <>
            <Grid item lg={5}>
                <p className="customPara">{label}</p>
            </Grid>
            <Grid item lg={7}>
                <div className="createEventDivTextMain">
                    <div className="SubDivTextMain-1">
                        <Field
                            name={fieldName}
                            disabled={props.disabled}
                            onChange={onChange}
                            variant="outlined"
                            className="ThemeInputTag-2"
                            component={renderDatePicker}
                        />
                        {
                            validate
                            && <span className={`text-danger ${dateValidation ? '' : 'hidden'}`}
                                     style={{fontSize: '14px'}}>{dateValidation && "Date must be after start date"}
                            </span>
                        }
                    </div>
                </div>
            </Grid>
        </>
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the recurrence popup line which describe the current recur details in terms of interval
     * and selected days if its weekly or weekend
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} weekDays Weekdays which contains the day(s) selected for recur the event
     * @param {Boolean} weekDays.mon To indicate if Monday is selected day or not for recurring the event
     * @param {Boolean} weekDays.tue To indicate if Tuesday is selected day or not for recurring the event
     * @param {Boolean} weekDays.wed To indicate if Wednesday is selected day or not for recurring the event
     * @param {Boolean} weekDays.thu To indicate if Thursday is selected day or not for recurring the event
     * @param {Boolean} weekDays.fri To indicate if Friday is selected day or not for recurring the event
     * @param {Boolean} weekDays.sat To indicate if Saturday is selected day or not for recurring the event
     * @param {Boolean} weekDays.sun To indicate if Sunday is selected day or not for recurring the event
     */
    const updateRecLine = (weekDays = null) => {
        weekDays = weekDays ? weekDays : selectedWeekDays;
        setRecDetailText(
            Helper.prepareRecurrenceLine(
                recInterval,
                recType,
                endDate.format('YYYY-MM-DD'),
                onDayOfMonth,
                weekDays,
                monthType,
                onTheMonthWeek,
                onTheMonthWeekDay
            )
        )
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To perform the set of action before saving the popup data, this method will prepare the data of
     * recurring popup fields and will send to parent props so all the data can be handled at a time for further steps
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handleSaveBtn = () => {
        if (endDate.format('YYYY-MM-DD') < startDate.format('YYYY-MM-DD')) {
            setDateValidation(true);
            return;
        } else {
            setDateValidation(false);
        }
        props.onSave({
            startDate,
            endDate,
            recInterval,
            recType,
            weekDays: Helper.convertWeekDayToNumber(selectedWeekDays),
            onDayOfMonth,
            monthType,
            onTheMonthWeek,
            onTheMonthWeekDay,
        });
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the recurring interval value update and store it in local state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} e Javascript event object
     */
    const handleRecIntervalUpdate = (e) => {
        setRecInterval(Number(e.target.value > 99 ? 99 : (e.target.value < 1 ? 1 : e.target.value)));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the recurring days of week value update and store it in local state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} e Javascript event object
     */
    const handleRecTypeUpdate = (e) => {
        setRecType(e.target.value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the week day update, here the single day key will be send as param and in selectedWeekDays
     * that key will set to true so that day will be marked as selected to user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} weekDay Key value which is present in selected week days that will be set to true in weekdays
     */
    const handleWeekDayUpdate = (weekDay) => {
        // on changing updating the current selected day to opposite
        let current = {...selectedWeekDays};
        current[weekDay] = !selectedWeekDays[weekDay];
        setSelectedWeekDays(current);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the recurring date of month value update and store it in local state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} e Javascript event object
     */
    const handleOnDayMonthUpdate = (e) => {
        setOnDayOfMonth(e.target.value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the update of week NUMBER from the month type recur
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} e Javascript event object
     */
    const handleMonthTypeUpdate = (e) => {
        setMonthType(Number(e.target.value));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the update of week NUMBER from the month type recur
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} e Javascript event object
     */
    const handleMonthWeekUpdate = (e) => {
        setOnTheMonthWeek(e.target.value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the update of week DAY from the month type recur
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} e Javascript event object
     */
    const handleMonthWeekDayUpdate = (e) => {
        setOnTheMonthWeekDay(e.target.value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handler the start date change and validate if its after the end date or not to show the
     * validation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {moment} val Data related to the start date of the recurrence
     */
    const onStartDateChange = (val) => {
        let newDate = startDate.clone();
        newDate.set({year: val.year(), month: val.month(), date: val.date()});
        setStartDate(newDate);
        console.log('dddddddddddddd ', {
            newDate: newDate.format('YYYY-MM-DD'),
            endDate: endDate.format('YYYY-MM-DD'),
            isAfer: newDate.isAfter(endDate)
        })
        if (newDate.format('YYYY-MM-DD') > endDate.format('YYYY-MM-DD')) {
            setDateValidation(true);
        } else {
            setDateValidation(false);
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handler the end date change and validate if its after the end date or not to show the
     * validation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} val Data related to end date of the recurrence
     */
    const onEndDateChange = (val) => {
        let newDate = endDate.clone();
        newDate.set({year: val.year(), month: val.month(), date: val.date()});
        setEndDate(newDate);
        if (newDate.format('YYYY-MM-DD') < startDate.format('YYYY-MM-DD')) {
            setDateValidation(true);
        } else {
            setDateValidation(false);
        }
    }

    return (
        <ModalBox
            ModalHeading="Set recurrence"
            showFooter
            handleCloseModal={props.closePopup}
            saveBtnHandler={handleSaveBtn}
            hideTopCloseIcon={true}
        >
            <Grid container spacing={3} className="recurrencePopupSpecing">
                {prepareDateInputComponent('Start Date:', 'rec_start_date', onStartDateChange)}
                <Grid item lg={5}>
                    <p className="customPara">Repeat every: </p>
                </Grid>
                <Grid item lg={7}>
                    <Field
                        name="rec_interval"
                        id="max_capacity"
                        placeholder={""}
                        variant="outlined"
                        className="repeat-interval-field"
                        size={"small"}
                        component={renderNumberField}
                        onChange={handleRecIntervalUpdate}
                        inputProps={{min: 1, max: 99}}
                    />
                    &nbsp;&nbsp;
                    <FormControl variant="outlined" className="SelectRepeatType" size={"small"}>
                        <Select
                            labelId="demo-simple-select-outlined-label"
                            value={recType}
                            onChange={handleRecTypeUpdate}
                        >
                            <MenuItem value={Constants.recurrenceType.DAILY}>Day</MenuItem>
                            <MenuItem value={Constants.recurrenceType.WEEKLY}>Week</MenuItem>
                            <MenuItem value={Constants.recurrenceType.MONTHLY}>Month</MenuItem>
                        </Select>
                    </FormControl>
                </Grid>

                {/* When user select MONTHLY - recurrence type from dropdown */}
                {
                    recType === Constants.recurrenceType.MONTHLY &&
                    <>
                        {/* adding empty space as there is no label for week day selection */}
                        {/* <Grid item lg={5}>
                        </Grid> */}
                        <Grid item lg={12}>
                            <RadioGroup
                                defaultValue="1"
                                name="recurrence_month_type"
                                value={monthType}
                                onChange={handleMonthTypeUpdate}
                            >
                                <Grid item container lg={12}>
                                    <Grid item lg={5} className="rightRadioBtn">
                                        <FormControlLabel value={Constants.recurrenceMonthType.ON_DAY}
                                                          control={<Radio />}
                                                          label="On Day" />
                                    </Grid>
                                    <Grid item lg={7}>
                                        <Field
                                            name="recurrence_onDay"
                                            id="recOnDay"
                                            placeholder={""}
                                            variant="outlined"
                                            className="repeat-interval-field"
                                            size={"small"}
                                            component={renderNumberField}
                                            onChange={handleOnDayMonthUpdate}
                                            value={onDayOfMonth}
                                            disabled={monthType !== Constants.recurrenceMonthType.ON_DAY}
                                            // type="number"
                                            inputProps={{min: 1, max: 31}}
                                        />
                                    </Grid>
                                </Grid>

                                <Grid item container lg={12}>
                                    <Grid item lg={5} className="rightRadioBtn">

                                        <FormControlLabel value={Constants.recurrenceMonthType.ON_THE}
                                                          control={<Radio />}
                                                          label="On The" />
                                    </Grid>

                                    <Grid item lg={7}>
                                        <FormControl variant="outlined" className="SelectRepeatType" size={"small"}>
                                            <Select
                                                labelId="demo-simple-select-outlined-label"
                                                value={onTheMonthWeek}
                                                onChange={handleMonthWeekUpdate}
                                                disabled={monthType !== Constants.recurrenceMonthType.ON_THE}
                                            >
                                                <MenuItem value={Constants.recurrenceMonthWeek.FIRST}>First</MenuItem>
                                                <MenuItem value={Constants.recurrenceMonthWeek.SECOND}>Second</MenuItem>
                                                <MenuItem value={Constants.recurrenceMonthWeek.THIRD}>Third</MenuItem>
                                                <MenuItem value={Constants.recurrenceMonthWeek.FOURTH}>Fourth</MenuItem>
                                                <MenuItem value={Constants.recurrenceMonthWeek.LAST}>Last</MenuItem>
                                            </Select>
                                        </FormControl>
                                        &nbsp;&nbsp;
                                        <FormControl variant="outlined" className="SelectRepeatType" size={"small"}>
                                            <Select
                                                labelId="demo-simple-select-outlined-label"
                                                value={onTheMonthWeekDay}
                                                onChange={handleMonthWeekDayUpdate}
                                                disabled={monthType !== Constants.recurrenceMonthType.ON_THE}
                                            >
                                                <MenuItem value={weekDayLabel.mon.long}>Monday</MenuItem>
                                                <MenuItem value={weekDayLabel.tue.long}>Tuesday</MenuItem>
                                                <MenuItem value={weekDayLabel.wed.long}>Wednesday</MenuItem>
                                                <MenuItem value={weekDayLabel.thu.long}>Thursday</MenuItem>
                                                <MenuItem value={weekDayLabel.fri.long}>Friday</MenuItem>
                                                <MenuItem value={weekDayLabel.sat.long}>Saturday</MenuItem>
                                                <MenuItem value={weekDayLabel.sun.long}>Sunday</MenuItem>
                                            </Select>
                                        </FormControl>
                                    </Grid>
                                </Grid>
                            </RadioGroup>

                        </Grid>
                    </>
                }


                {
                    (recType === Constants.recurrenceType.WEEKLY) &&
                    <>
                        {/* adding empty space as there is no label for week day selection */}
                        <Grid item lg={5} />
                        <Grid item lg={7}>
                            <div>
                                {/* iterating through each day of week and preparing the check box */}
                                {Object.keys(weekDayLabel).map(weekDay => {
                                    return <Checkbox
                                        onChange={() => handleWeekDayUpdate(weekDay)}
                                        icon={
                                            <span style={{fontWeight: 'bold'}}>{weekDayLabel[weekDay].short}</span>
                                        }
                                        checkedIcon={
                                            <span style={{fontWeight: 'bold'}}
                                                  sx={{bgcolor: 'primary.main'}}>{weekDayLabel[weekDay].short}</span>
                                        }
                                        checked={selectedWeekDays[weekDay]}
                                    />
                                })}
                            </div>
                        </Grid>
                    </>
                }
                {prepareDateInputComponent('End Date:', 'recurrence_end_date', onEndDateChange, true)}

                <Grid item lg={12}>
                    <p className="occurLabel customPara">
                        {recDetailText}
                    </p>
                </Grid>
            </Grid>
        </ModalBox>

    );
};

export default RecurrencePopup;
