import React, {useEffect, useState} from "react";
import {change, getFormAsyncErrors, getFormError, getFormSyncErrors, getFormValues, isValid} from "redux-form";
import {connect} from "react-redux";
import {FormControl, Grid, MenuItem} from "@material-ui/core";
import ModalBox from '../../../Common/ModalBox/ModalBox'
import Constants from "../../../../Constants";
import Helper from "../../../../Helper";
import "../../../CreateEvent/EventPreparation/Scheduling/RecurrencePopup.css"
import "./RecurrencePopup.css";
import DateInput from "../FormInput/DateInput";
import NumberInput from "../FormInput/NumberInput";
import SelectField from "../FormInput/SelectField";
import WeekDaysSelect from "./WeekDaysSelect";
import MonthTypeSelect from "./MonthTypeSelect";
import Validation from "../../../../functions/ReduxFromValidation";
import _ from 'lodash';

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
let RecurrencePopup = (props) => {
    const [recDetailText, setRecDetailText] = useState('Day');
    const [dateValidation, setDateValidation] = useState(null);

    useEffect(() => {
        updateRecLine();
    }, [props.formValues]);

    useEffect(() => {
        let recStart = props.formValues.event_start_date.isValid()
            ? props.formValues.event_start_date
            : props.formValues.event_rec_start_date;
        props.updateEventForm(
            'event_rec_start_date',
            recStart
        );
        if (props.formValues && props.formValues.event_rec_type === Constants.recurrenceType.WEEKDAY) {
            props.updateEventForm('event_rec_type', Constants.recurrenceType.WEEKLY);
            props.updateEventForm('event_rec_selected_weekday', {
                mon: true, tue: true, wed: true, thu: true, fri: true, sat: false, sun: false,
            });
        }
        if (props.formValues.event_start_date.format('YYYY-MM-DD') >= props.formValues.event_rec_end_date.format('YYYY-MM-DD')) {
            props.updateEventForm('event_rec_end_date', recStart.clone().add(1, 'd'));
        }
    }, []);


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
    const prepareDateInputComponent = (label, fieldName, validate = false) => {
        return <>
            <Grid item lg={5}>
                <p className="customPara">{label}</p>
            </Grid>
            <Grid item lg={7} className={`v4_colorTheme endDate ${fieldName}`}>
                <div className="createEventDivTextMain">
                    <div className="SubDivTextMain-1">
                        <DateInput
                            name={fieldName}
                            id={"recurrenceDate"}
                            disabled={props.disabled}
                            // onChange={onChange}
                            variant="outlined"
                            validate={validate ? [Validation.dateAfterStartDate, Validation.validDate] : [Validation.validDate]}
                            // className="ThemeInputTag-2"
                        />
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
    const updateRecLine = () => {
        setRecDetailText(Helper.prepareRecurrenceLine(
                props.formValues.event_rec_interval,
                props.formValues.event_rec_type,
                props.formValues.event_rec_end_date.format('YYYY-MM-DD'),
                props.formValues.event_rec_month_date,
                props.formValues.event_rec_selected_weekday,
                props.formValues.event_rec_month_type,
                props.formValues.event_rec_on_month_week,
                props.formValues.event_rec_on_month_week_day,
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
        if (!_.has(props.formSyncErrors, ['event_rec_end_date']) && !_.has(props.formSyncErrors, ['event_rec_start_date'])) {
            props.onSave();
        }
    };

    return (
        <ModalBox
            ModalHeading="Set recurrence"
            showFooter
            handleCloseModal={props.closePopup}
            saveBtnHandler={handleSaveBtn}
            hideTopCloseIcon={true}
            isShowSaveBtn={props.isShowSaveBtn}
            maxWidth={"500px"}
            leftCssVal={"250px"}
        >
            <Grid container spacing={3} className="recurrencePopupSpecing manageEventRecPopup">
                {prepareDateInputComponent('Start Date:', 'event_rec_start_date')}
                <Grid item lg={5}>
                    <p className="customPara">Repeat every: </p>
                </Grid>
                <Grid item lg={7} className="v4_colorTheme recTypeRow">
                    <NumberInput
                        name="event_rec_interval"
                        inputProps={{min: 1, max: 90}}
                    />
                    &nbsp;&nbsp;
                    <FormControl variant="outlined" className="SelectRepeatType" size={"small"}>
                        <SelectField
                            name="event_rec_type"
                            labelId="demo-simple-select-outlined-label"
                        >
                            <MenuItem value={Constants.recurrenceType.DAILY}>Day</MenuItem>
                            <MenuItem value={Constants.recurrenceType.WEEKLY}>Week</MenuItem>
                            <MenuItem value={Constants.recurrenceType.MONTHLY}>Month</MenuItem>
                        </SelectField>
                    </FormControl>
                </Grid>

                {/* When user select MONTHLY - recurrence type from dropdown */}
                {
                    props.formValues.event_rec_type === Constants.recurrenceType.MONTHLY &&
                    <MonthTypeSelect
                        name={"event_rec_month_type"}
                        formValues={props.formValues}
                    />
                }


                {
                    (props.formValues.event_rec_type === Constants.recurrenceType.WEEKLY) &&
                    <WeekDaysSelect
                        name={"event_rec_selected_weekday"}
                    />

                }
                {prepareDateInputComponent('End Date:', 'event_rec_end_date', true)}

                <Grid item lg={12}>
                    <p className="occurLabel customPara">
                        {recDetailText}
                    </p>
                </Grid>
            </Grid>
        </ModalBox>

    );
};

const mapStateToProps = (state) => {
    return {
        formValues: getFormValues('eventManageForm')(state),
        formSyncErrors: getFormSyncErrors('eventManageForm')(state),
        
    }
}


const mapDispatchToProps = (dispatch) => {
    return {
        updateEventForm: (field, value) => dispatch(change('eventManageForm', field, value)),

    }
};

RecurrencePopup = connect(mapStateToProps, mapDispatchToProps)(RecurrencePopup);
export default RecurrencePopup;
