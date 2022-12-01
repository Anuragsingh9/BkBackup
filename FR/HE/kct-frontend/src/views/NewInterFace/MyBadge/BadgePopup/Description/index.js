import React, {useRef, useState} from 'react'
import DesInput from './DesInput';
import {connect} from "react-redux";
import "./discription.css";
import eventActions from '../../../../../redux/actions/eventActions';
import newInterfaceActions from '../../../../../redux/actions/newInterfaceAction';
import Helper from '../../../../../Helper';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import {confirmAlert} from 'react-confirm-alert';
import UserBadge from '../UserBadge';
import {useTranslation} from 'react-i18next';
import EventTag from '../EventTag';
import ReactTooltip from 'react-tooltip';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying Description in BadgePopUp2.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.eventUuid Current event uuid
 * @param {UserBadge} props.badgeData User badge details
 * @param {Function} props.visibility Handler to update the visibility of badge section
 * @param {Function} props.restore Handler to reset the the visibility of field
 * @param {Boolean} props.eyeForfield_1 Field visibility to other for field1
 * @param {Boolean} props.eyeForfield_2 Field visibility to other for field2
 * @param {Boolean} props.eyeForfield_3 Field visibility to other for field3
 * @param {Object} props.alert Reference object for displaying notification popup
 * @param {Function} props.updateInfo To update the profile data on backend server
 * @param {Function} props.setBadge To update the badge details of user
 * @param {Function} props.updateProfileTrigger To update the profile data on backend server
 *
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const Description = (props) => {

    const [badgeData, setBadgeData] = useState(props.badgeData)
    const msg = useAlert()
    const {t} = useTranslation(['myBadgeBlock', 'notification'])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles update data for about yourself section
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data that needs to be send in api
     * @param {String} data.field Field name that is updated
     * @param {String|Number} data.value Value of that respective fields that needs to be updated
     * @param {Function} callback Callback method when the field is successfully updated
     * @param {Function} blurCallback Callback when user get blur from the field
     */
    const updateData = (data, callback, blurCallback) => {
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("field", data.field);
        formData.append("value", data.value);
        formData.append("event_uuid", props.eventUuid);

        try {
            props.updateInfo(formData)
                .then((res) => {
                    const data = res.data.data

                    props.restore(data)
                    callback && callback();
                    blurCallback && blurCallback();
                    const event_uuid = props.eventUuid;
                    props.updateProfileTrigger(data, event_uuid);

                    props.setBadge(res.data.data);
                    msg && msg.show(t("notification:rec add 1"), {type: 'success'});
                })
                .catch((err) => {
                    msg && msg.show(Helper.handleError(err), {type: "error"});

                })
        } catch (err) {
            msg && msg.show(Helper.handleError(err), {type: "error"});
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles delete data for about yourself section
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data that needs to be send in api
     * @param {String} data.field Field name that is updated
     * @param {String|Number} data.value Value of that respective fields that needs to be updated
     * @param {Function} callback Callback method when the field is successfully updated
     * @param {Function} blurCallback Callback when user get blur from the field
     */
    const deleteData = (data, callback, blurCallback) => {
        data.value = '';

        confirmAlert({
            message: t("Are you sure want to remove?"), //Localization[lang].CONFIRM_REMOVE,
            confirmLabel: t("Confirm"),
            cancelLabel: t("Cancel"),
            buttons: [
                {
                    label: t("Yes"),
                    onClick: () => {
                        updateData(data, callback, blurCallback)
                    }
                },
                {
                    label: t("No"),
                    onClick: () => {
                        return null
                    }
                }
            ],

        })

    }
    // check for previous filled data
    if (badgeData) {
        const field_1 = badgeData.personal_info.field_1 ? badgeData.personal_info.field_1 : '';
        const field_2 = badgeData.personal_info.field_2 ? badgeData.personal_info.field_2 : '';
        const field_3 = badgeData.personal_info.field_3 ? badgeData.personal_info.field_3 : '';


        return (
            <div className="row">
                <AlertContainer ref={msg} {...Helper.alertOptions} />
                <div>
                    {/* <hr/> */}
                    <div className="WhiteBgDiv">
                        <div className="modify-badge modify-badge-marginBottom">
                            {t("WHO I AM")}
                        </div>
                    </div>
                    {/* <hr/> */}
                    <div className="row p-0">
                        <div className='wigywig-block4'>
                            <lable className="describelabelTxt">{t("Describe")}</lable>
                            <DesInput
                                value={field_1}
                                field="field_1"
                                onBlur={updateData}
                                name="description"
                                visibility={props.visibility}
                                visibilityType="p_field_1"
                                eyeState={props.eyeForfield_1}
                                delete={deleteData}
                                dataTip={t("Hide your description")}

                            />
                        </div>
                    </div>
                    <div className="WhiteBgDiv">
                        <div className="modify-badge modify-badge-marginBottom">
                            {t('MY INTERESTS FOR THE EVENT')}
                        </div>
                    </div>
                    <div className="row mt-6 p-0">
                        <div className='col-md-12 p-0 wigywig-block3'>
                            <div className="row wigywig-block12">
                                <EventTag eventUuid={props.eventUuid} alert={props.alert} />
                            </div>
                        </div>
                    </div>

                </div>
                <div>
                    <UserBadge eventUuid={props.eventUuid} />
                </div>
                <div className="WhiteBgDiv">
                    <div className="modify-badge modify-badge-marginBottom">
                        {t('MY SEARCHES')}
                    </div>
                </div>
                <div>
                    <div className="row p-0">
                        <div className='wigywig-block4'>
                            <lable className="describelabelTxt">{t("Looking")}</lable>
                            <DesInput
                                value={field_2}
                                field="field_2"
                                onBlur={updateData}
                                name=" askToHelp"
                                visibility={props.visibility}
                                eyeState={props.eyeForfield_2}
                                visibilityType="p_field_2"
                                delete={deleteData}
                                dataTip={t("Hide your Searches")}

                            />
                        </div>
                    </div>

                </div>
                <div className="WhiteBgDiv">
                    <div className="modify-badge modify-badge-marginBottom">
                        {t('QUESTIONS I HAVE')}
                    </div>
                </div>
                <div>
                    <div className="row p-0">
                        <div className='wigywig-block4'>
                            <lable className="describelabelTxt">{t("Ask")}</lable>
                            <DesInput
                                value={field_3}
                                field="field_3"
                                onBlur={updateData}
                                visibility={props.visibility}
                                eyeState={props.eyeForfield_3}
                                visibilityType="p_field_3"
                                name="que"
                                delete={deleteData}
                                dataTip={t("Hide your Questions")}

                            />
                        </div>
                    </div>

                </div>
                {/* <div>
                    <UserBadge eventUuid={props.eventUuid} />
                    </div> */}
                <ReactTooltip type="dark" effect="solid" />
            </div>
        )
    }
}

const mapDispatchToProps = (dispatch) => {

    return {
        updateInfo: (data) => dispatch(eventActions.Event.updateInfo(data)),
        setBadge: (data) => dispatch(newInterfaceActions.NewInterFace.setBadgeData(data)),
        updateProfileTrigger: (data, id) => dispatch(newInterfaceActions.NewInterFace.updateProfileTrigger(data, id)),

    }
}

export default connect(null, mapDispatchToProps)(Description);