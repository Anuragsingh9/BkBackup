import React, {useState, useEffect} from 'react';
import {Grid} from '@material-ui/core';
import './DummyZoomLicense.css';
import {useTranslation} from "react-i18next"
import _ from 'lodash';
import AssignLicense from '../Common/AssignLicense/AssignLicense';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used to create license list. Its takes data of license and put data in array and returns
 * list of license data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Inherited from parent component
 * @param {Object} props.licenseData License details
 * @param {Number} props.license Numbers of license
 * @returns {Array}
 * @constructor
 */
const HandleBox = (props) => {

    const {license, licenseData} = props;
    const licenseList = [];
    for (var i = 0; i < license; i++) {

        if (_.isNil(licenseData[i])) {
            licenseList.push(<AssignLicense getNo={props.getNO} number={i + 1} getId={props.getId} />)
        } else {
            licenseList.push(<AssignLicense getNo={props.getNO} getId={props.getId} licenseInfo={licenseData[i]}
                                            number={i + 1} deleteItem={props.deleteItem} />)
        }
    }
    return licenseList;
}

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component allows Super Admin to link a Zoom license which can be used by a Pilot to
 * alternatively create a C+N Event without having the need to link his own Zoom account.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Object} props.dataKey Used for data
 * @param {Function} props.getKeyData Function used for get data key
 * @param {Function} props.updateTechnicalSetting Function used for update technical setting
 * @returns {JSX.Element}
 * @constructor
 */
const DummyZoomLicense = (props) => {
    const {t} = useTranslation(["eventList", "confirm"])
    const [webinarData, setWebinarData] = useState({
        max_participants: '',
        number_of_licenses: '',
        license: [],
        is_locked: 0,
        is_assigned: 1,
        authenticate_url: "",
        is_visible: '',

    })

    useEffect(() => {
        getData();
    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to get Zoom default webinar accounts data by using getKeyData that calls API
     * if response is successful then it updates state with maximum participants , number of licenses, assigned or not,
     * is locked and authenticate_url .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getData = () => {
        var data = props.getKeyData('zoom_default_webinar_settings')
        setWebinarData({
            max_participants: _.has(data, ["value", "max_participants"]) ? data.value.max_participants : '',
            number_of_licenses: _.has(data, ["value", "number_of_licenses"]) ? data.value.number_of_licenses : '',
            license: !_.isEmpty(data.value.licenses) ? data.value.licenses : [],
            is_locked: _.has(data.value, ["is_locked"]) ? data.value.is_locked : 0,
            authenticate_url: _.has(data.value, ["authenticate_url"]) ? data.value.authenticate_url : '',
            is_assigned: _.has(data.value, ["is_assigned"]) ? data.value.is_assigned : 1,
            is_visible: _.has(data.value, ["is_visible"]) ? data.value.is_visible : "",
        })
    }

    // this hook is used for call function for fetch data on first time component loads
    useEffect(() => {
        getData();
    }, []);

    const [place, setPlace] = useState();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to get numbers of licenses and updates state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} value Number of licenses
     */
    const getNo = (value) => {
        setPlace(value)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to get Zoom default webinar accounts data by using getKeyData that calls API if
     * response is successful then it updates state with maximum participants , number of licenses, assigned or not,
     * is locked and authenticate_url .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Webinar id
     */
    const getId = (id) => {
        var list = webinarData.license;
        const selecteUsers = []
        list[place - 1] = id;
        setWebinarData({
            ...webinarData,
            license: list
        })
        const updatedValue = {"licenses": list}
        handleUpdate(updatedValue)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for removing linked default account license and call api and handles its
     * response to remove the license by passing data to handleUpdate() method  and it updates list and state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} dltObj Object used in license list
     */
    const handleRemoveLicense = (dltObj) => {
        var newList = []

        if (!_.isEmpty(webinarData.license)) {
            var list = webinarData.license;
            newList = list.filter(function (e, i) {
                return list[i] != dltObj
            });
            setWebinarData({
                ...webinarData,
                license: newList

            })
            // return(newList)
            //for update value field in api call
            const updatedValue = {"licenses": newList}

            handleUpdate(updatedValue)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is a wrapper method for handling the default webinars account data and sends in
     * parent component by using  updateTechnicalSetting with field and value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} updatedValue Value is used for updated technical setting
     */
    const handleUpdate = (updatedValue) => {
        const fieldValue = props.dataKey
        props.updateTechnicalSetting({field: fieldValue, value: updatedValue})
    }

    /**
     * @deprecated
     */
    const handleAcSetting = () => {
        if (webinarData.is_assigned == 1) {
            confirmRemoveAc()
        } else {

            window.open(webinarData.authenticate_url, '_blank');
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for confirmation model for removing ac settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const confirmRemoveAc = () => {
        if (webinarData.is_assigned == 1) {
            confirmAlert({
                message: `${t('confirm:sure')}`,
                confirmLabel: t('confirm:confirm'),
                cancelLabel: t('confirm:cancel'),
                buttons: [
                    {
                        label: t('confirm:Remove'),
                        onClick: () => {
                            removeAcSettings()
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
     * @description This method is used for remove settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const removeAcSettings = () => {

        const fieldValue = props.dataKey
        const updatedValue = {"is_assigned": 0}
        props.updateTechnicalSetting({field: fieldValue, value: updatedValue})

    }

    return (
        <div>
            <Grid container xs={12}>

                {
                    webinarData.is_visible == 1 &&
                    <Grid container xs={12}>
                        {webinarData.is_assigned == 1 &&
                        <Grid container xs={12}>

                            <Grid container xs={12} className="FlexRow">
                                <Grid item xs={3}>
                                    <p className='customPara customPara-2'> Max. number of participants :</p>
                                </Grid>
                                <Grid item xs={4}>
                                    <p className='customPara customPara-2'>{webinarData.max_participants}</p>
                                </Grid>
                            </Grid>
                            <Grid container xs={12} className="FlexRow">
                                <Grid item xs={3}>
                                    <p className='customPara customPara-2'> Number of Licenses :</p>
                                </Grid>
                                <Grid item xs={4}>
                                    <p className='customPara customPara-2'>{webinarData.number_of_licenses}</p>
                                </Grid>
                            </Grid>
                            <Grid container xs={12} className="FlexRow">
                                <Grid item xs={4}>
                                    {webinarData.number_of_licenses &&
                                    <HandleBox getNO={(v) => getNo(v)} getId={(id) => getId(id)}
                                               deleteItem={handleRemoveLicense} license={webinarData.number_of_licenses}
                                               licenseData={webinarData.license} />

                                    }
                                </Grid>
                            </Grid>
                        </Grid>
                        }

                    </Grid>
                }
            </Grid>

        </div>

    )
}

export default DummyZoomLicense;