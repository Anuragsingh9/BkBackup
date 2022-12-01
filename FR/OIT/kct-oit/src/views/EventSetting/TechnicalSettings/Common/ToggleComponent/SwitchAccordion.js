import React, {useEffect, useState} from 'react';
import Switch from '@material-ui/core/Switch';
import {Button, Grid} from '@material-ui/core';
import _ from 'lodash';
import './SwitchAccordion.css';
import {confirmAlert} from 'react-confirm-alert';
import {useTranslation} from "react-i18next"
import DummyZoomLicenseIcon from "../../../../Svg/DummyZoomLicenseIcon";
import Icon from '../../../../../Models/Icon';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common switch accordion component for all sections with toggle switch in technical settings
 * page and handles states for switch and updates value on server by using API call
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received technical setting related values and functions from it's parent component
 * @param {String} props.SubHeading Small heading placed at top.
 * @param {Function} props.child Function which return child components when main switch is ON.
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key
 * @param {String} props.heading Main heading text placed at top
 * @param {Icon} props.icon Icon to update the value
 * @param {String} props.info_txt Current section name to render reset color functionality into it
 * @param {Function} props.updateTechnicalSetting Function used to update technical setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const SwitchAccordion = (props) => {
    const {t} = useTranslation(["eventList", "confirm"])
    const [switchState, setSwitch] = useState(false);
    const [accordion, setAccordion] = useState({});

    useEffect(() => {
        if (props.dataKey) {
            const accordionData = props.getKeyData(props.dataKey);

            const {value} = accordionData;
            setAccordion(accordionData)
            if (value) {
                if (value.enabled) {
                    setSwitch(true)
                } else {
                    setSwitch(false)
                }
            }
        }
    }, [props])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle toggle switch state values if switch clicks this value will be
     * updated in state and also will be updated on server by updateTechnicalSetting() method which is coming from
     * parent component .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {boolean} e Javascript event object
     */
    const triggerData = (e) => {
        const value = {"enabled": e.target.checked ? 1 : 0}
        const accordionData = props.getKeyData(props.dataKey);
        const {field} = accordionData;
        setSwitch(e.target.checked)
        props.updateTechnicalSetting({field: field, value: value});
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is for checking if current user can enable disable the toggle or not.
     * <br>
     * Return true means check will be disabled
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {boolean}
     */
    const checkDisable = () => {
        // sending true will disable field
        // so checking either field is not present or check if user can modify is 0,
        // so if user can not modify send true
        return !_.has(accordion, ['value', 'user_can_update'])
            || accordion.value.user_can_update === 0
            || accordion.value.is_assigned === 0;
        // if not assigned the license don't allow user to change enable disable

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to remove the account that is linked to Zoom if user has permission to remove
     * account.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleAccLinkAndRemove = () => {
        // if user can modify the settings and license is assigned already, then perform the remove acc operation
        if (_.has(accordion, ['value', 'user_can_update'])
            && accordion.value.user_can_update === 1
            && accordion.value.is_assigned === 1
        ) {
            confirmRemoveAc(accordion)
        } else {
            window.open(accordion.value.authenticate_url, '_blank');
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to confirm to remove linked account. It shows a pop up upon clicking on
     * the button  click which has two option (confirm and cancel) on click on confirm button it removes the popup
     * account and on cancel button it hides the popup model .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} data Data of linked account
     * @param {String} data.field Field name
     * @param {String} data.value Field value
     * @method
     */
    const confirmRemoveAc = (data) => {
        if (data && data.value.is_assigned === 1) {
            confirmAlert({
                message: `${t('confirm:sure')}`,
                confirmLabel: t('confirm:confirm'),
                cancelLabel: t('confirm:cancel'),
                buttons: [
                    {
                        label: t('confirm:Remove'),
                        onClick: () => {
                            removeAcSettings(data)
                        }
                    },
                    {
                        label: t('confirm:Cancel'),
                        onClick: () => {
                            return null
                        }
                    }
                ],

            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to remove linked account and call api and handles its response to unlink/remove
     * the broadcasting account by passing data to updateTechnicalSetting() method which is coming from parent
     * component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} data Data of linked account
     * @param {String} data.field Field name
     * @param {String} data.value Field value
     * @method
     */
    const removeAcSettings = (data) => {
        const fieldValue = data && data.field
        const updatedValue = {"is_assigned": 0}
        props.updateTechnicalSetting({field: fieldValue, value: updatedValue})
    }

    return (
        <Grid container xs={12} className="SwitchDivRow">
            {/* Icon and Heading of section */}
            <Grid item xs={11} className="TogglerRow">
                <Grid container className="ToggleMainFlex">
                    <Grid item className="Flex-1"><DummyZoomLicenseIcon /></Grid>
                    <Grid item className="Flex-2">
                        {props.heading}
                        <span className="SmallSubHeading">
                            Connect License to experience Live Content Broadcasting
                        </span>
                    </Grid>
                </Grid>
            </Grid>

            {/* Enable Disable Section */}
            <Grid item xs={1}>
                <Switch
                    checked={switchState}
                    color="primary"
                    disabled={checkDisable()}
                    onChange={(e) => {
                        triggerData(e)
                    }}
                />
            </Grid>

            {switchState &&
            <div className="SwitchContentChildDiv">
                {props.child}
                <div className="SwitchContentChildDiv-2">
                </div>
            </div>
            }
            <Grid container xs={12}
                  className={accordion.value && accordion.value.is_assigned === 0 ? 'linkAcBtn' : 'removeAcBtn'}>
                <Grid item xs={accordion.value && accordion.value.is_assigned === 0 ? 12 : 3}></Grid>
                <Grid item className="subFlexRow">
                    {/* Link or remove account, if user can modify */}
                    {_.has(accordion, ['value', 'user_can_update']) && accordion.value.user_can_update === 1 &&
                    <>
                        < Button variant="contained" color="Primary"
                                 onClick={() => handleAccLinkAndRemove(accordion.field)}>
                            {accordion.value && accordion.value.is_assigned === 0 ? "Link A/C" : "Remove A/C"}
                        </Button>
                        {accordion.value && accordion.value.is_assigned === 0 &&
                        <p className="customPara">{props.info_txt}</p>}

                    </>
                    }

                </Grid>
            </Grid>
        </Grid>
    )

}

export default SwitchAccordion;

