import {Button, Grid} from '@mui/material'
import React from 'react'
import SpaceManageForm from './SpaceManageForm'
import "./SpaceManage.css"
import SpacePreview from './SpacePreview'
import {submit} from "redux-form";
import {connect} from "react-redux";
import {useTranslation} from "react-i18next";
import eventAction from "../../../redux/action/reduxAction/event";
import {getFormValues, change} from 'redux-form'

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a popup component in which we are using 2 sub components-<br>
 * 1.SpaceManageForm(Redux form to create a space)<br>
 * 2.SpacePreview(To preview real time data of a space)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {props} props In props we are getting createSpace form value data from redux-form.
 * @returns {JSX.Element}
 */
let SpaceManage = (props) => {

    const {t} = useTranslation("eventCreate");

    const handleOnSave = () => {
        if (props.spacePopupIsEditMode) {
            editCurrentSpace()
        } else {
            props.submitSpaceForm();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will take current space modified data and update main space data in main form.
     * -----------------------------------------------------------------------------------------------------------------
     * 
     * @method
     */
    const editCurrentSpace = () => {
        props.submitSpaceForm();
        props.createSpaceMode()
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is for handle discard button of space popup.
     * -----------------------------------------------------------------------------------------------------------------
     * 
     * @method
     */
    const handleDiscard = () => {
        props.closeSpacePopup();
        if (props.spacePopupIsEditMode) {
            props.createSpaceMode()
        }
    }

    return (
        <Grid container specing={0} lg={12}>
            <Grid item lg={6}>
                <SpaceManageForm spaceIndex={props.spaceIndex} />
            </Grid>
            <Grid item lg={6} className='addSpacePopupWrap'>
                <SpacePreview />
            </Grid>
            <Grid item lg={6}>
            </Grid>
            <Grid item lg={6} className="addSpaceActionBtn">
                <Button variant="outlined" color="primary" onClick={handleDiscard}>{t("discard")}</Button>
                <Button variant="contained" color="primary" onClick={handleOnSave}>{t("save")}</Button>
            </Grid>
        </Grid>

    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        closeSpacePopup: () => dispatch(eventAction.closeSpacePopup()),
        submitSpaceForm: () => dispatch(submit("createSpaceForm")),
        createSpaceMode: () => dispatch(eventAction.createSpaceMode()),
        updateEventForm: (field, value) => dispatch(change('eventManageForm', field, value)),
    }
}
const mapStateToProps = (state) => {
    return {
        spacePopupIsEditMode: state.Event.space_form_status.is_modified,
        spaceFormValues: getFormValues('createSpaceForm')(state),
        eventFormValues: getFormValues('eventManageForm')(state),
    }
}

SpaceManage = connect(mapStateToProps, mapDispatchToProps)(SpaceManage);

export default SpaceManage;