import React, {useEffect, useState} from 'react';
import EntitySelectInput from './EntitySelectInput';
import "../../../Index/VideoConference/BadgeInterface/BadgeInterface.css";
import {connect} from 'react-redux';
import eventActions from '../../../../redux/actions/eventActions';
import newInterfaceActions from '../../../../redux/actions/newInterfaceAction';
import Helper from '../../../../Helper';
import KeepContactagent from '../../../../agents/KeepContactagent';
import RoundedCrossIcon from '../../../Svg/RoundedCrossIcon.js';
import {ReactComponent as EyesOpen} from "../../../../images/eye-variant-with-enlarged-pupil.svg";
import {ReactComponent as EyeCross} from '../../../../images/eye-cross2.svg'
import {KeepContact as KCT} from '../../../../redux/types';
import BadgeTextInput from './BadgeTextInput.js';
import ReactTooltip from 'react-tooltip';
import Description from './Description/index'
import {useTranslation} from 'react-i18next';
import "./BadgePopUp2.css";
import {Entity, UserBadge} from "../../../../Model/index.js";

const _ = require('lodash');


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying BadgePopup3 in MyBadgeBlock. When user clicks on badge button
 * Here the data of user will be loaded in real time and user will have popup displayed from where user can modify the
 * profile related data or the visibility of fields to other
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.alert Reference object for displaying notification popup
 * @param {Function} props.togglePopup To update the visibility of badge popup
 * @param {String} props.event_uuid Event UUID of current event
 * @param {Function} props.toggleReset To toggle the popup position to reset i.e. closed
 * @param {Boolean} props.modal Variable to hold the current popup open state, true for opened
 * @param {Function} props.getBadge  Redux Dispatcher to get badge data
 * @param {Function} props.updateProfile Redux Dispatcher to update profile single field
 * @param {Function} props.updateProfileData Redux Dispatcher to update profile all data
 * @param {Function} props.updateInitName Redux Dispatcher to update init name
 * @param {Function} props.setBadge Redux Dispatcher to set badge data in redux
 * @param {Function} props.setVisibility Redux Dispatcher to update the field visibility
 * @param {Function} props.setVisible Redux Dispatcher to update the visibility in redux store
 * @param {Function} props.updateProfileTrigger Redux Dispatcher to update the personal info
 * @param {Function} props.updateInfo Redux Dispatcher to update user info
 * @param {UserBadge} props.event_badge Redux store mapped variable for holding user badge data
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */

const BadgePopup = (props) => {

    const [company, setCompany] = useState(null);
    const [badgeData, setBadgeData] = useState({});
    const [avatar, setAvatar] = useState(null);
    const [isloading, setIsloading] = useState(true)
    const [unionCount, setUnionCount] = useState(0);
    const [unions, setUnions] = useState(null);
    const [unionPosition, setUnionPosition] = useState('');
    const [companyPosition, setCompanyPosition] = useState('');
    const [unionMemberType, setUnionMemberType] = useState('');
    const [linkedin, setLinkedin] = useState('');
    const [facebook, setFacebook] = useState('');
    const [instagram, setInstagram] = useState('');
    const [twitter, setTwitter] = useState('');
    const [addUnion, setAddUnion] = useState(false);
    const [eyeEnabled, setEyeEnabled] = useState(true);
    const [openEyeForCompany, setOpenEyeForCompany] = useState('');
    const [openEyeForLastName, setOpenEyeForLastName] = useState('');
    const [openEyeForUnions, setOpenEyeForUnions] = useState('');
    const [fname, setFname] = useState('');
    const [lname, setLname] = useState('');
    const [tagsData, setTagsData] = useState(null);
    const [openEyeForfield_1, setOpenEyeForfield_1] = useState('');
    const [openEyeForfield_2, setOpenEyeForfield_2] = useState('');
    const [openEyeForfield_3, setOpenEyeForfield_3] = useState('');
    const [userTxt, setUserTxt] = useState('');
    const [nstanceName, setInstanceName] = useState();
    const [email, setEmail] = useState('');

    const {t} = useTranslation(['myBadgeBlock', "notification"]);

    useEffect(() => {
        props.getBadge(props.event_uuid).then((res) => {
            const data = res.data.data
            const text = data.user_fname.charAt(0) + data.user_lname.charAt(0);
            let firstUnion = _.last(data.unions)

            setBadgeData(data);
            setFname(data.user_fname);
            setInstanceName(data.instance)
            setLname(data.user_lname)
            setEmail(data.user_email)
            setAvatar(data.user_avatar);
            setCompany((data.company) ? {value: data.company.entity_id, label: data.company.long_name} : null);
            setCompanyPosition((data.company) ? data.company.position : null)
            setUnions((firstUnion) ? {value: firstUnion.entity_id, label: firstUnion.long_name} : null)
            setUnionPosition((firstUnion) ? firstUnion.position : '')
            setUnionMemberType(
                (firstUnion && firstUnion.membership_type !== undefined)
                    ? firstUnion.membership_type
                    : ''
            )
            setUnionCount(data.unions.length)
            setTagsData(data.tags_data)
            setLinkedin(data.social_links.linkedin)
            setFacebook(data.social_links.facebook)
            setInstagram(data.social_links.instagram)
            setTwitter(data.social_links.twitter)
            setOpenEyeForCompany(data.visibility.company)
            setOpenEyeForLastName(data.visibility.user_lname)
            setOpenEyeForUnions(data.visibility.unions)
            setIsloading(false)
            setOpenEyeForfield_1(data.visibility.p_field_1)
            setOpenEyeForfield_2(data.visibility.p_field_2)
            setOpenEyeForfield_3(data.visibility.p_field_3)
        })

    }, [])

    /**
     * @deprecated
     */
    const updateState = (data) => {
        props.setBadge(data);
        const event_uuid = props.event_uuid;
        props.updateProfileTrigger(data, event_uuid);
        setFname(data.user_fname);
        setBadgeData(data)
        setFname(data.user_fname);
        setInstanceName(data.instance)
        setLname(data.user_lname)
        setEmail(data.user_email)
        setAvatar(data.user_avatar);
        setUserTxt(data.user_fname.charAt(0) + data.user_lname.charAt(0))
        setIsloading(true)
        setUnionCount(data.unions.length)
        setLinkedin(data.social_links.linkedin)
        setFacebook(data.social_links.facebook)
        setInstagram(data.social_links.instagram)
        setTwitter(data.social_links.twitter)
        setAddUnion(false)

    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function handles the visibility state for different user details(user last name, company,
     * unions) in badge editor component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} type Contains the specific field name for updating the visibility
     */
    const visibilityHandle = (type) => {
        setEyeEnabled(true)
        var {visibility} = badgeData

        const data = {
            field: type,
            value: visibility[type] == 0 ? 1 : 0,
        }

        switch (data.field) {
            case "user_lname":
                setOpenEyeForLastName(data.value);
                break;
            case "company":
                setOpenEyeForCompany(data.value);
                break;
            case "unions":
                setOpenEyeForUnions(data.value);
                break;
            case "p_field_1":
                setOpenEyeForfield_1(data.value);
                break;
            case "p_field_2":
                setOpenEyeForfield_2(data.value);
                break;
            case "p_field_3":
                setOpenEyeForfield_3(data.value);
                break;
            default:
                return data.field;
        }
        if (eyeEnabled) {
            setVisibility(data);
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to manage hide/unhide user details(user last name, company,
     * unions) for badge editor component or user badge component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data to be updated for single field
     * @param {String} data.field Field name which visibility needs to be updated
     * @param {Number} data.value Value of visibility for respective field name
     */
    const setVisibility = (data) => {
        setEyeEnabled(false);
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("field", data.field);
        formData.append("value", data.value);
        formData.append('event_uuid', props.event_uuid);

        try {
            props.setVisible(formData)
                .then((res) => {
                    const badge_Data = res.data.data;

                    setBadgeData(res.data.data);
                    props.setBadge(res.data.data);
                    const eventUuid = props.event_uuid;

                    props.updateProfileTrigger(res.data.data, eventUuid);

                    props.alert.show(t("Record Updated"), {type: "success"});

                    if (res.data !== "") {

                        setEyeEnabled(true);
                    }
                }).catch((err) => {

                props.alert.show(Helper.handleError(err), {
                    type: "error"
                });
            })
        } catch (err) {

            props.alert.show(Helper.handleError(err), {
                type: "error"
            });
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Function will update the data for first name and last name values of current logged in user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {object} data Data to be updated for single field
     * @param {String} data.field Field name which visibility needs to be updated
     * @param {Number} data.value Value of visibility for respective field name
     * @param {Function} data.resetFunc Function to reset the badge popup visibility
     */
    const updateProfileData = (data) => {
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("field", data.field);
        formData.append("value", data.value);
        formData.append('event_uuid', props.event_uuid);

        try {
            props.updateProfileData(formData)
                .then((res) => {
                    const badgeData = res.data.data;
                    props.setBadge(badgeData);
                    const event_uuid = props.event_uuid;
                    props.updateProfileTrigger(badgeData, event_uuid);
                    setBadgeData(badgeData);
                    setAvatar(badgeData.user_avatar)
                    if (_.has(data, ['resetFunc']) && data.resetFunc) {
                        data.resetFunc(true);
                    }
                    data.field === 'fname' && props.updateInitName(badgeData.user_fname)
                    props.alert && props.alert.show(t("Record Updated"), {type: "success"});
                })
                .catch((err) => {
                    console.error(err)
                    props.alert && props.alert.show(Helper.handleError(err), {type: "error"});
                    if (_.has(data, ['resetFunc']) && data.resetFunc) {
                        data.resetFunc(true);
                    }
                })
        } catch (err) {
            props.alert && props.alert.show(Helper.handleError(err), {type: "error"});
        }
    }


    /**
     * @deprecated
     */
    const getPersonBelongs = (type, id, callback) => {
        setIsloading(false)
    }

    /**
     * @deprecated
     */
    const updateSocialLink = (field) => {
        const postData = new FormData()
        postData.append('field', field)
        postData.append('value', this.state[field])
        postData.append('_method', 'PUT')
        KeepContactagent.Event.updateSocialLink(postData).then((res) => {
                if (res.data.status) {
                    setBadgeData(res.data.data)
                    props.alert.show(t("notification:flash msg rec update 1"), {
                        type: 'success', onClose: () => {
                        }
                    });
                }
            }
        ).catch((err) => {
            props.alert.show(Helper.handleError(err));
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to update  union and company details.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data needs to send for updating
     * @param {Entity} data.company Company data to be updated
     * @param {Entity[]} data.unions Unions data to be updated as there can be more than one union possible
     * @param {String} state Key to indicate which entity is being updated, ex. company or union
     */
    const updateCompany = (data, state) => {
        let stateData = data[state]
        if (state === 'unions') {
            let lastUnion = _.last(stateData)
            stateData = (lastUnion) ? {value: lastUnion.entity_id, label: lastUnion.long_name} : null
            setUnions(stateData);
            setUnionPosition((lastUnion) ? lastUnion.position : '')
        }
        if (state === 'company') {
            stateData = stateData ? {value: stateData.entity_id, label: stateData.long_name} : null
            setCompany(stateData);
            setCompanyPosition((data[state]) ? data[state].position : '');
        }
        props.setBadge(data);
        const event_uuid = props.event_uuid;
        props.updateProfileTrigger(data, event_uuid);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user start typing in first name and last name input fields and this
     * will take input values (from its parameter) to update in state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} value Value of the entity name
     * @param {String} key Key to be updated in badge popup
     */
    const onChangeHandler = (value, key) => {
        let {event_badge} = props
        if (event_badge) {
            event_badge = {...event_badge, [key + '_change']: value}
        }
        props.setBadge(event_badge)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will take added text value from input fields and save it into a state('restoreState')
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {UserBadge} data Badge data
     */
    const restoreState = (data) => {
        setBadgeData(data)
    }


    let userText = _.upperCase(_.first(fname)) + '' + _.upperCase(_.first(lname));
    const event_uuid = props.event_uuid;

    let styles = props.modal ? {display: "block", opacity: '1'} : {display: "none", opacity: '0'};

    return (
        <div style={{background: '#fff'}}>
            <div className="modal-body badge2-body text-left no-texture">
                <div className="bagdeWhiteWrapDiv">
                    <div className="back-white-popup">
                        <div className="container-fluid">

                            {(isloading) ?
                                <Helper.pageLoading />
                                :
                                <>
                                    <div className='row'>
                                        <div className='col-md-12 p-0'>
                                            <div className="WhiteBgDiv">
                                                <div className="modify-badge">
                                                    {t("modify my badge")}
                                                </div>
                                            </div>
                                            <hr></hr>
                                            <div className="row wigywig-block1">
                                                <div className="form-group">
                                                    <div className="row">
                                                        <div className="col-md-9 col-sm-9">
                                                            <BadgeTextInput
                                                                event_badge={props.event_badge}
                                                                setBadge={props.setBadge}
                                                                onChangeHandler={onChangeHandler}
                                                                field="fname"
                                                                value={fname}
                                                                name="First name"
                                                                placeholder={t("Enter First name")}
                                                                onBlur={updateProfileData}
                                                            />

                                                        </div>
                                                    </div>
                                                    <div className="row">
                                                        <div className='col-md-9 col-sm-9'>
                                                            <BadgeTextInput
                                                                event_badge={props.event_badge}
                                                                setBadge={props.setBadge}
                                                                onChangeHandler={onChangeHandler}
                                                                field="lname"
                                                                value={lname}
                                                                name="Last name"
                                                                placeholder={t("Enter last name")}
                                                                onBlur={updateProfileData}
                                                            />
                                                        </div>
                                                        <div className="col-md-3 col-sm-3">
                                                            <span className="eyepop eyeposition1"
                                                                  data-tip={t("Hide your last name")}
                                                                  onClick={() => visibilityHandle('user_lname')}
                                                            >
                                                                {(openEyeForLastName) ?
                                                                    <EyesOpen /> : <EyeCross />
                                                                }
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="row mt-6">
                                                    <div className="col-xs-10">
                                                        <label>{t("Your company")}:</label>
                                                        <EntitySelectInput
                                                            alert={props.alert}
                                                            updateCompany={updateCompany}
                                                            eyeState={openEyeForCompany}
                                                            visibility={visibilityHandle}
                                                            position={companyPosition}
                                                            entityType="company"
                                                            visibilityType="company"
                                                            entityTypeId={2}
                                                            selectedEntity={company} />
                                                    </div>
                                                    {/* hide when company deleted */}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="row mt-6 p-0">
                                        <div className='col-md-10 col-sm-10 wigywig-block2'>
                                            <div className="row">
                                                <label>{t("Your union")}:</label>

                                                <EntitySelectInput
                                                    alert={props.alert}
                                                    eyeState={openEyeForUnions}
                                                    visibility={visibilityHandle}
                                                    visibilityType="unions"
                                                    entityType="union" updateCompany={updateCompany}
                                                    entityTypeId={3} selectedEntity={unions}
                                                    position={unionPosition}
                                                    unionMemberType={unionMemberType}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div className='col-md-12 p-0'>
                                        <Description
                                            eventUuid={event_uuid}
                                            badgeData={badgeData}
                                            visibility={visibilityHandle}
                                            restore={restoreState}
                                            eyeForfield_1={openEyeForfield_1}
                                            eyeForfield_2={openEyeForfield_2}
                                            eyeForfield_3={openEyeForfield_3}
                                            alert={props.alert}
                                        />
                                    </div>
                                    <ReactTooltip type="dark" effect="solid" />
                                </>
                            }
                        </div>


                        <div className="pop-close">
                            <button type="button" className="pop-close-btn" onClick={props.togglePopup}>
                                <RoundedCrossIcon />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        getBadge: (data) => dispatch(eventActions.Event.getBadge(data)),
        updateProfile: (data) => dispatch(eventActions.Event.updateProfile(data)),
        updateProfileData: (data) => dispatch(eventActions.Event.updateProfileData(data)),
        updateInitName: (data, index) => dispatch({type: KCT.EVENT.UPDATE_INIT_NAME, payload: data}),
        setBadge: (data) => dispatch(newInterfaceActions.NewInterFace.setBadgeData(data)),
        setVisibility: (data) => dispatch(newInterfaceActions.NewInterFace.setVisibility(data)),
        setVisible: (data) => dispatch(newInterfaceActions.NewInterFace.setVisible(data)),
        updateProfileTrigger: (data, id) => dispatch(newInterfaceActions.NewInterFace.updateProfileTrigger(data, id)),
        updateInfo: (data) => dispatch(eventActions.Event.updateInfo(data))
    }
}

const mapStateToProps = (state) => {
    return {
        event_badge: state.NewInterface.interfaceBadgeData
    };
};


export default connect(mapStateToProps, mapDispatchToProps)(BadgePopup);
// export default (MyEventBadge);