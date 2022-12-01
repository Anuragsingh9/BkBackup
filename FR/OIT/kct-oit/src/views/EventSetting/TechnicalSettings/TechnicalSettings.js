import React, {useEffect, useState} from 'react';
import SwitchAccordion from './Common/ToggleComponent/SwitchAccordion';
import {useSelector, useDispatch} from 'react-redux';
import groupAction from '../../../redux/action/apiAction/group';
import {useAlert} from 'react-alert';
import Helper from '../../../Helper.js';
import DummyZoomLicenseIcon from '../../Svg/DummyZoomLicenseIcon.js';
import './TechnicalSettings.css';
import LoadingContainer from '../../Common/Loading/Loading';
import _ from 'lodash';
import LicenseManagement from "./LicenseManagement/LicenseManagement";
import {Grid} from "@material-ui/core";
import {useParams} from "react-router-dom";
import LoadingSkeleton from '../../Common/Loading/LoadingSkeleton';
import TechnicalSettingSkeleton from '../../v4/Skeleton/TechnicalSettingSkeleton';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for Technical Setting page . Technical setting page basically handles accounts
 * of Zoom licenses for webinar and meetings.
 * Pilot and super admin can only access this page and link their Zoom account this page also shows information about
 * license and no. of license and available licenses
 * here all the license fetched from back side will be displayed only
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {JSX.Element}
 * @constructor
 */
const TechnicalSettings = () => {
    // alert hook
    const alert = useAlert();
    // graphic settings state
    const [techSettings, setTechSettings] = useState([]);
    // temp state for update api data
    const [newState, updateState] = useState([]);
    // loading state
    const [loading, setLoading] = useState(true);
    // user data to get group id
    const user_badge = useSelector((data) => data.Auth.userSelfData)
    // dispatch hook from redux
    const dispatch = useDispatch();

    const {gKey} = useParams();

    const [propsChang, setPropsChange] = useState(false)
    // useEffect hook to trigger get api
    useEffect(() => {
        if (_.has(user_badge, ['current_group', 'id'])) {
            getSettings(gKey);
        }
    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle the response of update api, and provides latest updated data to
     * setTechSettings() method.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} data Data of technical setting licenses
     * @method
     */
    const setUpdatedData = (data) => {
        setTechSettings(data);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handles api call for getting technical settings data and response handling
     * for getting graphics data and updates state for this other wise it returns error massages.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} gKey  Group Key.
     */
    const getSettings = (gKey) => {
        try {
            dispatch(groupAction.getTechnicalSettings(gKey)).then((res) => {
                setTechSettings(res.data.data);
                setTimeout(() => {
                    setLoading(false);
                }, 400)
            }).catch((err) => {
                setLoading(false);
                alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            setLoading(false);
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to get data of technical settings by using key. If key in settings object
     * is matched it returns that item of key and value pair .
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {string} key  Technical setting data key.
     * @returns {object} Settings object with key value pair.
     */
    const getKeyData = (key) => {
        const data = !_.isEmpty(techSettings) && techSettings.filter((item) => {
            if (item.field == key) {
                return item;
            }
        });
        return !_.isEmpty(data) ? data[0] : {};
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle api call and response for updating technical settings data on
     * server if response is successful then it updates the state other wise it will return error massage.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {object} updatedData  Updated technical setting licenses data.
     */
    const updateTechnicalSetting = (updatedData) => {
        if (_.isEmpty(newState) && !_.has(updatedData, ['field'])) {
            return alert.show('Nothing to update here')
        }
        const updateData = {
            "key": `${_.has(updatedData, ['field']) ? updatedData.field : ''}`,
            "data": _.has(updatedData, ['value']) ? updatedData.value : '',
            "group_id": user_badge.current_group.id,
            "group_key": gKey,
            "_method": "PUT"
        }

        try {
            dispatch(groupAction.updateTechnicalSetting(updateData)).then(res => {
                updateState([]);
                setUpdatedData(res.data.data);
                alert.show('Record Updated Successfully', {type: 'success'})
            }).catch((err) => {
                alert.show(Helper.handleError(err), {type: 'error'})

            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    return (

        <div className="TechnicalSettingsContentDiv">
            <LoadingSkeleton loading={loading} skeleton={<TechnicalSettingSkeleton/>}>
                <SwitchAccordion
                    dataKey={'default_zoom_settings'}
                    getKeyData={getKeyData}
                    updateTechnicalSetting={updateTechnicalSetting}
                    child={<LicenseManagement
                        updateTechnicalSetting={updateTechnicalSetting}
                        getKeyData={getKeyData}
                        dataKey={'default_zoom_settings'}
                    />}
                    heading={'Demo License'}
                    info_txt={"To Mange Licences, Kindly log in to your Zoom Account"}
                    SubHeading={"Connect License to experience Live Content Broadcasting"}
                    icon={<DummyZoomLicenseIcon />}
                />
                <SwitchAccordion
                    dataKey={'custom_zoom_settings'}
                    getKeyData={getKeyData}
                    updateTechnicalSetting={updateTechnicalSetting}
                    child={<LicenseManagement
                        updateTechnicalSetting={updateTechnicalSetting}
                        getKeyData={getKeyData}
                        dataKey={'custom_zoom_settings'}
                    />}
                    heading={'Zoom Account'}
                    info_txt={"To Mange Licences, Kindly log in to your Zoom Account"}
                    SubHeading={"Customise your Event's Content Provider"}
                    icon={<DummyZoomLicenseIcon />}
                />
            </LoadingSkeleton>
        </div>
    )
}

export default TechnicalSettings;