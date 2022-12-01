import React, {useState, useEffect} from 'react';
import {Grid} from '@material-ui/core';
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import './ZoomMeetings.css';
import _ from 'lodash';
import {useHistory} from "react-router-dom";

import AssignLicense from '../Common/AssignLicense/AssignLicense';
import {confirmAlert} from 'react-confirm-alert';
import {useTranslation} from "react-i18next"

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used to create license list Its takes data of license and put data in array and returns
 * list of license data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getNo This method is used to get numbers of licenses and updates state
 * @param {Function} props.getId This method is used to get Zoom default meeting accounts
 * @param {Function} props.deleteItem This method is used for removing linked default account license
 * @param {Object} props.licenseData License data
 * @param {Number} props.license Count of license
 * @returns {LicenseObj[]}
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
 * @description This child component is for displaying data of Zoom meeting details like maximum participants , number
 *  of licenses , assigned or not and is locked and these licenses can be use in Zoom meetings.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getKeyData This method is used to get data of technical settings by using key. If key in
 * settings object is matched it returns that item of key and value pair
 * @param {Object} props.dataKey Object of zoom setting keys
 * @param {String} props.dataKey.default_zoom_settings Key for default zoom settings
 * @param {String} props.dataKey.custom_zoom_settings Key for custom zoom settings
 * @param {Function} props.updateTechnicalSetting This method is used for updating technical settings data
 * @returns {JSX.Element}
 * @constructor
 */
const ZoomMeetings = (props) => {
    const [place, setPlace] = useState();
    const {t} = useTranslation(["eventList", "confirm"])
    const [webinarData, setWebinarData] = useState({
        max_participants: '',
        number_of_licenses: '',
        license: [],
        is_locked: 0,
        authenticate_url: '',
        is_assigned: 0

    })

    useEffect(() => {
        getData();

    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to get Zoom meeting accounts data by using getKeyData that calls API if
     * response is successful then it updates state with maximum participants , number of licenses, assigned or not,
     * is locked and authenticate_url .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getData = () => {
        var data = props.getKeyData('zoom_meeting_settings')
        setWebinarData({
            max_participants: _.has(data, ["value", "max_participants"]) ? data.value.max_participants : '',
            number_of_licenses: _.has(data, ["value", "number_of_licenses"]) ? data.value.number_of_licenses : '',
            // number_of_licenses: 3,
            license: !_.isEmpty(data.value.licenses) ? data.value.licenses : [],
            is_locked: _.has(data.value, ["is_locked"]) ? data.value.is_locked : 0,
            authenticate_url: _.has(data.value, ["authenticate_url"]) ? data.value.authenticate_url : '',

            is_assigned: _.has(data.value, ["is_assigned"]) ? data.value.is_assigned : '',
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to get numbers of licenses and updates state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} value Number of active licenses
     */
    const getNo = (value) => {
        setPlace(value)

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to add license id in already exist list and update state with new list and
     * pass list data to update on server using  handleUpdate method which is coming from parent componet as a
     * props.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id  License Id
     */
    const getId = (id) => {
        var list = webinarData.license;
        const selecteUsers = []
        list[place - 1] = id;

        const updatedValue = {"licenses": list}
        handleUpdate(updatedValue)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for removing linked account license and call api and handles its response to
     * remove the license by passing data to handleUpdate() method  and it updates list and state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} dltObj Id of the license to be removed
     */
    const handleRemoveLicense = (dltObj) => {
        var newList = []

        if (!_.isEmpty(webinarData.license)) {
            var list = webinarData.license;
            newList = list.filter(function (e, i) {
                return list[i] != dltObj
            });
            //for update value field in api call
            const updatedValue = {"licenses": newList}
            handleUpdate(updatedValue)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is a wrapper method for handling the license and webinars account data and sends in
     * parent component by using  updateTechnicalSetting with field and value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} updatedValue Licenses list
     * @param {User[]} updatedValue.licenses New list of licenses
     */
    const handleUpdate = (updatedValue) => {
        const fieldValue = props.dataKey
        props.updateTechnicalSetting({field: fieldValue, value: updatedValue})
    }

    return (
        <div>
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
                    {webinarData.number_of_licenses &&
                    <HandleBox getNO={(v) => getNo(v)} getId={(id) => getId(id)}
                               deleteItem={handleRemoveLicense} license={webinarData.number_of_licenses}
                               licenseData={webinarData.license} />
                    }
                </Grid>
                }
            </Grid>
        </div>
    )
}

export default ZoomMeetings;