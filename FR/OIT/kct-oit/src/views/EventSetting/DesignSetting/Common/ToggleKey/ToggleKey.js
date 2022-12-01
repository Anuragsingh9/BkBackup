import React, {useState, useEffect} from 'react';
import {Switch} from '@material-ui/core';
import {Grid} from '@material-ui/core';
import {useTranslation} from 'react-i18next';
import Tooltip from '@material-ui/core/Tooltip';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component to render a sub component of switch accordion which includes a toggle switch
 * to give accordion ON/OFF user experience and one heading with 1 subheading and icon.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received toggle key related values and functions from it's parent component
 * @param {String} props.dataSubKey Unique key for component.
 * @param {Function} props.setShowGridImageField Function to handle grid image
 * @param {String} props.toolTip Tooltip message
 * @param {React.Component} props.iconInfo Icon info component
 * @returns {JSX.Element}
 * @constructor
 */
const ToggleKey = (props) => {
    const [t, i18n] = useTranslation(['toggleKey', 'designSetting']);
    const [switchState, setSwitch] = useState(false);
    const [accordion, setAccordion] = useState({});

    useEffect(() => {
        if (props.dataSubKey) {
            const accordionData = props.getKeyData(props.dataSubKey)
            const {value} = accordionData;
            setAccordion(accordionData)
            if (value) {
                setSwitch(true)
                if (props.dataSubKey === "video_explainer") {

                    props.setShowGridImageField && props.setShowGridImageField(false)
                }
            } else {
                setSwitch(false)
                if (props.dataSubKey === "video_explainer") {
                    props.setShowGridImageField && props.setShowGridImageField(true)
                }
            }
        }
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user change state(ON/OFF) of toggle button in switch accordian.
     * This function will take button current state and save them in a state and call callback function to update
     * design setting for it.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const triggerData = (e) => {
        const value = e.target.checked ? 1 : 0;
        const {field} = accordion;
        setSwitch(e.target.checked)
        if (props.dataSubKey === "video_explainer") {
            props.setShowGridImageField && props.setShowGridImageField(e.target.checked == 0)
        }
        props.callBack({value, field});
    }


    return (
        <div>
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'>
                            {t(props.dataSubKey)}
                            : {props.iconInfo
                            ? <Tooltip arrow title={props.toolTip}>
                                <span>{props.iconInfo}</span>
                            </Tooltip>
                            : ''}
                        </p>
                    </Grid>
                    <Grid item xs={2} className="toggleSwitchDiv">
                        {props.isSpace ? 'Round' : ''}
                        <Switch
                            color="primary"
                            checked={switchState}
                            onChange={(e) => {
                                triggerData(e)
                            }}
                            disabled={props?.disabled}
                        />
                        {props.isSpace ? 'Square' : ''}
                    </Grid>
                </Grid>
            </Grid>

        </div>

    )
}

export default ToggleKey;