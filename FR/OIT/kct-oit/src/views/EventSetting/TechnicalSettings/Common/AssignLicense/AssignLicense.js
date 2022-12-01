import React, {useState, useEffect} from 'react';
import UserAutocomplete from "../../../../Common/UserAutoComplete/UserAutoComplete"
import {TextField, Button, Grid} from '@material-ui/core';
import {confirmAlert} from 'react-confirm-alert';
import {useTranslation} from "react-i18next"
import './AssignLicense.css'
import CloseIcon from '../../../../Svg/closeIcon';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This Component is a common component that shows assigned license . this component is used to show
 * license data for Zoom webinar and Zoom meeting license , and super admin can link and unlink Zoom account and
 * license is for moderator role and speaker role users for meeting and webinar purpose.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent for Assigning license
 * @param {User} props.licenseInfo User data
 * @param {Function} props.deleteItem This method is used for removing linked default account license
 * @param {Function} props.getNo This method is used to get numbers of licenses and updates state
 * @param {Function} props.getId This method is used to get Zoom default meeting accounts
 * @return {JSX.Element}
 * @constructor
 */
const AssignLicense = (props) => {
    const {t} = useTranslation(["eventList", "confirm"])
    const [showButton, setShowButton] = useState(true);
    const [showBox, setShowBox] = useState(false)

    // this hook handles visibility for box and button is license data is there then it will shows box
    useEffect(() => {
        if (props.licenseInfo) {
            setShowBox(true);
            setShowButton(false)
        }
    }, [props])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is shows a popup model for confirmation pop up and upon clicking on the confirm button
     * it invokes handleRemove() method and it deletes data on server using API call and upon clicking of cancel button
     * it hides popup and do nothing.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const confirmDelete = () => {
        confirmAlert({
            message: `${t('confirm:sure')}`,
            confirmLabel: t('confirm:confirm'),
            cancelLabel: t('confirm:cancel'),
            buttons: [
                {
                    label: t('confirm:yes'),
                    onClick: () => {
                        handleRemove()
                    }
                },
                {
                    label: t('confirm:no'),
                    onClick: () => {
                        return null
                    }
                }
            ],

        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to remove/unlink Zoom account for meeting and webinar by passing
     * licenseInfo into deleteItem() method which is coming from parent component and updates state for showBox false
     * value and showButton true value for handling labels according to state value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleRemove = () => {
        if (props.licenseInfo) {
            props.deleteItem(props.licenseInfo)
        }
        setShowBox(false);
        setShowButton(true)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for updating states of showBox and showButton on "Assign License" button
     * click and handles labels according to state values.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleAssign = () => {
        setShowBox(true);
        setShowButton(false)
    }

    const label = `License ${props.number ? props.number : ''}`

    return (
        <>
            <Grid container xs={12} className="py-10">
                <Grid item xs={2} className="RightCustomParaDiv">
                    <div className=''> {label}</div>
                </Grid>
                <Grid item xs={1}>
                </Grid>
                {showBox &&

                <Grid item xs={4} className="assignlicButton">
                    <div onClick={() => props.getNo(props.number)}>
                        <UserAutocomplete id={props.getId} name={props.licenseInfo ? props.licenseInfo : ""} />
                    </div>

                    <CloseIcon onClick={confirmDelete}>remove</CloseIcon>
                </Grid>
                }
                {showButton &&
                <Grid>
                    <Button variant="contained" color="Primary" onClick={handleAssign}>Assign License</Button>
                </Grid>}

            </Grid>
        </>
    )
}

export default AssignLicense;
