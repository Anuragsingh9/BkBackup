import React, {useEffect, useRef, useState} from 'react'
import "./Userbadge.css";
import UserTag from './UserTag';
import {connect} from 'react-redux';
import eventActions from '../../../../redux/actions/eventActions';
import newInterfaceActions from '../../../../redux/actions/newInterfaceAction';
import Helper from '../../../../Helper';
import {Provider as AlertContainer,useAlert } from 'react-alert';
import {useTranslation} from 'react-i18next'
import ReactTooltip from 'react-tooltip';
import { useParams } from 'react-router-dom';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component to render personal & professional tags section in the badge editor component
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {UserBadge[]} props.data
 * @param {Boolean} props.isEditable To indicate if badge is editable or not
 * @param {Boolean} props.isLoading To show the loader until data is fetched
 * @param {Boolean} props.isSlider To indicate if popup can be slide or not
 * @param {Boolean} props.paginate To indicate if pagination needs to be applied or not
 *
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const UserBadge = (props) => {
    const [badgeData, setBadgeData] = useState('');
    const [openForPersonal, setOpenForPersonal] = useState('');
    const [openForProffesional, setOpenForProffesional] = useState('');
    const msg = useAlert()
    const {t} = useTranslation('myBadgeBlock')

    const { event_uuid} = useParams();

    useEffect(() => {
        props.getBadge(props.eventUuid).then((res) => {
            const data = res.data.data
            setBadgeData(data);
            setOpenForPersonal(data.visibility.personal_tags);
            setOpenForProffesional(data.visibility.professional_tags)
        })
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle the hide/un hide action for professional & personal tags in badge editor
     * component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} type Type of the tag to update the visibility for
     */
    const visibility = (type) => {
        let tagType = (type == '1' ? 'professional_tags' : 'personal_tags')
        var {visibility} = badgeData;
        const data = {
            field: tagType,
            value: visibility[tagType] == 1 ? 0 : 1
        }
        setVisibilty(data);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to manage hide/un hide action for professional & personal tags.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data to be updated for single field
     * @param {String} data.field Field name which visibility needs to be updated
     * @param {Number} data.value Value of visibility for respective field name
     */
    const setVisibilty = (data) => {
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("field", data.field);
        formData.append("value", data.value);
        formData.append('event_uuid',event_uuid)
        try {
            props.setVisible(formData)
                .then((res) => {
                    const badgeData = res.data.data;
                    setBadgeData(badgeData);
                    setOpenForPersonal(badgeData.visibility.personal_tags);
                    setOpenForProffesional(badgeData.visibility.professional_tags)
                    props.setBadge(res.data.data);
                    const event_uuid = props.eventUuid;
                    props.updateProfileTrigger(badgeData, event_uuid);
                    msg && msg.show(t("Record Updated"), {type: "success"});
                }).catch((err) => {
                msg && msg.show(Helper.handleError(err), {
                    type: "error"
                });
            })
        } catch (err) {
            msg && msg.show(Helper.handleError(err), {
                type: "error"
            });
        }
    }


    return (
        <div>
            <AlertContainer ref={msg} {...Helper.alertOptions} />
            <div className="ProPerDiv">
                <div className="WhiteBgDiv">
                    <div className="WhiteBgDiv">
                        <div className="modify-badge modify-badge-marginBottom">
                            {t('MY PROFESSIONAL INTERESTS')}
                        </div>
                    </div>
                </div>
                <div className="position-relative ProtagTextColor wigywig-block10">
                    <h5 className="ProtagHeadingUserbadge">{t("Professional tag")}</h5>
                    <UserTag
                        type='1'
                        line={t("add pro tags")}
                        eventUuid={props.eventUuid}
                        eyeState={openForProffesional}
                        visibility={visibility}
                        dataTip={t("Hide your Professional tag")}
                    />
                </div>
                <div className="WhiteBgDiv">
                    <div className="WhiteBgDiv">
                        <div className="WhiteBgDiv">
                            <div className="WhiteBgDiv">
                                <div className="modify-badge modify-badge-marginBottom">
                                    {t('MY PERSONAL INTERESTS')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="PertagHeadingUserbadgeDiv position-relative PertagTextColor wigywig-block10">
                    <h5 className="PertagHeadingUserbadge">{t("Personal tag")}</h5>

                    <UserTag
                        type='2'
                        line={t("add per tags")}
                        eventUuid={props.eventUuid}
                        eyeState={openForPersonal}
                        visibility={visibility}
                        dataTip={t("Hide your Personal tag")}
                    />
                </div>

            </div>
            <ReactTooltip type="dark" effect="solid" />
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        getBadge: (data) => dispatch(eventActions.Event.getBadge(data)),
        setVisible: (data) => dispatch(newInterfaceActions.NewInterFace.setVisible(data)),
        setBadge: (data) => dispatch(newInterfaceActions.NewInterFace.setBadgeData(data)),
        updateProfileTrigger: (data, id) => dispatch(newInterfaceActions.NewInterFace.updateProfileTrigger(data, id)),
    }
}


export default connect(null, mapDispatchToProps)(UserBadge);