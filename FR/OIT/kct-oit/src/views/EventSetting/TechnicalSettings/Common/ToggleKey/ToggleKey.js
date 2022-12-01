import React, {useState, useEffect} from 'react';
import {Switch} from '@material-ui/core';
import {Button, Grid} from '@material-ui/core';
import {useTranslation} from 'react-i18next';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is a common toggle switch component for switch in SwitchAccordion in technical settings.
 * and handles values of switch and returns.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received toggle key related values and functions from it's parent component
 * @param {String} props.dataSubKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key eg - get data for technical
 * setting section.
 * @param {Function} props.callBack Function to take current value.
 * @param {React.Component} props.iconInfo Icon info component
 * @param {Object} props.isSpace Is space or not
 * @returns {JSX.Element}
 * @constructor
 */
const ToggleKey = (props) => {
    const [t, i18n] = useTranslation('toggleKey');

    const [switchState, setSwitch] = useState(false);

    const [accordion, setAccordion] = useState({});

    // this hook handles switch value when first time component loads
    useEffect(() => {
        //Handles accordionData switch current state value
        if (props.dataSubKey) {
            const accordionData = props.getKeyData(props.dataSubKey)
            const {value} = accordionData;
            setAccordion(accordionData)
            if (value) {
                setSwitch(true)
            } else {
                setSwitch(false)
            }
        }
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling state of toggle switch and pass the value of field name and
     * switch value to parent component using callBack() method.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {object} e Javascript event object
     */
    const triggerData = (e) => {
        const value = e.target.checked ? 1 : 0;

        const {field} = accordion;
        setSwitch(e.target.checked)
        props.callBack({value, field});

    }
    return (

        <div>

            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'>{t(props.dataSubKey)} : {props.iconInfo}</p>
                    </Grid>
                    <Grid item xs={2} className="toggleSwitchDiv">
                        {props.isSpace ? 'Round' : ''}
                        <Switch
                            color="primary"
                            checked={switchState}
                            onChange={(e) => {
                                triggerData(e)
                            }}
                        />
                        {props.isSpace ? 'Square' : ''}
                    </Grid>
                </Grid>
            </Grid>

        </div>

    )
}

export default ToggleKey;