import React, {useState} from 'react'
import {change, getFormValues} from 'redux-form'
import {connect} from "react-redux";
import "./SpaceList.css";
import CreateOutlinedIcon from '@mui/icons-material/CreateOutlined';
import DeleteOutlinedIcon from '@mui/icons-material/DeleteOutlined';
import VIPIcon from "../../Svg/VIPIcon.js"
import {Button, IconButton} from '@mui/material';
import SpacePopup from './SpacePopup';
import {useTranslation} from "react-i18next";
import eventAction from "../../../../redux/action/reduxAction/event";
import SpaceManageHelper from "../../SpaceManage/SpaceManageHelper"
import _ from "lodash";
import {confirmAlert} from "react-confirm-alert";
import {useAlert} from 'react-alert';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a main add space component which consist create space component and a popup component to create
 * a space.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Received from data from redux-form
 * @param {createEventForm} //ToDO shubhamYa create modal for it.
 * @returns {JSX.Element}
 */
let SpaceList = (props) => {
    const [selectedSpaceIndex, setselectedSpaceIndex] = useState(0)
    const {t} = useTranslation(['eventCreate', 'notifications']);
    const alert = useAlert();

    // Use ternary operation in case we don't receive event space data
    let spaceViewData = props.eventFormValues?.event_space_data;



    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to open space create popup component.
     * -----------------------------------------------------------------------------------------------------------------
     */
    const openSpacePopup = () => {
        props.openSpacePopup();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to perform delete space action from event creation form.
     * -----------------------------------------------------------------------------------------------------------------
     * 
     * @method
     * @param {Number} index Selected space's index(for edit) 
     * @param {Object} props Object that contains redux-form update method to perform delete space action.
     */
    const deleteSpaceHandler = async (index, props) => {
        await SpaceManageHelper.handleDeleteSpace(index, props)
        alert.show(t('notifications:spaceDeleted'), {type: "success"})
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method shows a confirmation model before delete the data and on 'confirm' it delete the data
     * and on 'cancel'  it returns null value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} key Key of the moment
     * @param {String} content Content data
     * @param {String} index Index of the selected moment
     */
    const onDeleteSpace = (index) => {
        confirmAlert({
            message: t('notifications:deleteConfirm'),
            confirmLabel: t('notifications:confirm'),
            cancelLabel: t('notifications:cancel'),
            buttons: [
                {
                    label: t("notifications:yes"),
                    onClick: () => {
                        deleteSpaceHandler(index, props)
                    },
                },
                {
                    label: t("notifications:no"),
                    onClick: () => {
                        return null;
                    },
                },
            ],
        });
    };

    const handleEditSpace = (index) => {
        props.editSpaceMode();
        setselectedSpaceIndex(index)
        openSpacePopup();
    }

    return (
        <>
            {spaceViewData?.map((space, index) => (
                <div key={index} className={`spaceViewRow ${props.disabled && 'disabledRow'}`}>
                    {console.log('cccccc', space.space_is_default)}
                    <p>
                        {space?.space_line_1} {space?.space_line_2}
                        &nbsp;-&nbsp;
                        <b>{space?.space_host?.fname} {space?.space_host?.lname}</b>
                        ({space?.space_host?.email})
                    </p>
                    {
                        !props.disabled
                        && <div className='iconWrap'>
                            {(space?.space_is_vip === 1 || space?.space_is_vip === true) &&
                                <div className="vipIconWrap">
                                    <VIPIcon />
                                    &nbsp;
                                    <span>VIP</span>
                                </div>
                            }
                            <IconButton color="primary" onClick={() => handleEditSpace(index)}>
                                <CreateOutlinedIcon />
                            </IconButton>
                            {_.has(space, ['space_is_default']) && space.space_is_default == 0 &&
                                <IconButton color="primary" onClick={() => onDeleteSpace(index)}>
                                    <DeleteOutlinedIcon />
                                </IconButton>
                            }

                        </div>
                    }
                </div>
            ))}
            {!props.disabled &&
                <Button variant='text' className='addSpaceBtn' onClick={openSpacePopup}>
                    {t("addSpace")} +
                </Button>
            }
            {props.spacePopupIsOpen && <SpacePopup spaceIndex={selectedSpaceIndex}/>}
        </>
    )
}


const mapDispatchToProps = (dispatch) => {
    return {
        updateEventForm: (field, value) => dispatch(change('eventManageForm', field, value)),
        openSpacePopup: () => dispatch(eventAction.openSpacePopup()),
        editSpaceMode: () => dispatch(eventAction.editSpaceMode()),
        createSpaceMode: () => dispatch(eventAction.createSpaceMode()),
    }
};

const mapStateToProps = (state) => {
    return {
        spaceFormValues: getFormValues('createSpaceForm')(state),
        eventFormValues: getFormValues('eventManageForm')(state),
        spacePopupIsOpen: state.Event.space_form_status.is_open,
    }
}

SpaceList = connect(mapStateToProps, mapDispatchToProps)(SpaceList);

export default SpaceList