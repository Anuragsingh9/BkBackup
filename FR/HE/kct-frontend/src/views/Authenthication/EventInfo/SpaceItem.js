import React, {useRef} from 'react';
import _ from 'lodash';
import {connect} from 'react-redux';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import {useTranslation} from 'react-i18next';
import {useLocation} from "react-router-dom";
import {KeepContact as KCT} from '../../../redux/types';
import Helper from '../../../Helper.js';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component to render all available spaces(normal/VIP) in select space section in quick
 * registration page.Form this component user can perform click to join specific space action.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getData To get the data of single space
 * @param {Function} props.addJoin To join the selected space if space has limit
 * @param {String} props.videoLink Video link of the grid video if not networking is there
 * @param {GraphicsData} props.graphic_data [State] This variable holds the current graphics data set in redux
 * @param {Function} props.getUserInfoData To fetch the user badge data
 * @param {Function} props.setUserInfoData To update the user badge data in redux store
 * @param {String} props.invite_attendee Id of invite attendee
 * @param {String} props.video_explainer Image url of video explainer if any
 * @param {String} props.video_explainer_alternative_image Alternate Image url of video explainer if any
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const SpaceItem = (props) => {
    // Initialisation fo message / alert ref to show alerts on success or error.
    const msg = useAlert(null);
    const {t} = useTranslation('notification')
    const {search} = useLocation();
    const query = React.useMemo(() => new URLSearchParams(search), [search]);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to perform click to join space action in quick select space
     * section  in quick registration page.This will get clicked space UUID from its parameter.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} space_uuid Space uuid to which user joining
     */
    const joinSpaces = (space_uuid) => {
        try {
            const formData = new FormData();
            const {event_uuid} = props;
            formData.append('space_uuid', space_uuid)
            formData.append('event_uuid', event_uuid)
            let accessCode = props.event_data.accessCode;

            if (!accessCode) {
                accessCode = query.get('access_code');
            }
            if (!accessCode) {
                accessCode = localStorage.getItem("accessCode");
            }
            if (accessCode) {
                formData.append('access_code', accessCode);
            }
            props.spaceJoin(formData)
                .then(res => {
                    if (res.data.status) {
                        props.setCurrentSpace(space_uuid)
                    }
                })
                .catch(err => {
                    if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data;
                        props.msg && props.msg.show(er["msg"], {type: 'error'});
                    } else {
                        props.msg && props.msg.show(Helper.handleError(err), {type: 'error'})
                    }
                })
        } catch (err) {
            props.msg && props.msg.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function validates the vip user or space host user for changing of space and call API handler
     * (joinSpaces) function
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const handleSpace = () => {
        if (active_space != val.space_uuid && val.is_duo_space != 1) {
            joinSpaces(val.space_uuid)
        }
    }
    // space data object and active space
    const {val, active_space} = props;
    // shortname of space
    let shortName = '';
    if (val.space_short_name) {
        shortName = val.space_short_name;
    }

    // conditional rendering of spaces vip / active vip / normal / normal selected
    return (
        <div onClick={handleSpace}
             className={`spance-slide-bx ${active_space == val.space_uuid ? "selected-width" : ""}`}>
            {" "}
            {val.is_vip_space == 1 ?
                <div
                    className={
                        `one-spaces 
           ${active_space == val.space_uuid
                            ? 'selected-space  vip-selected-space-bg '
                            : 'vip-space-bg  unselected-space'
                        }`
                    }
                >
                    <div className={`vip-space every-space vip-selected-space`}>
                        {active_space == val.space_uuid && val.is_vip_space == 1 &&
                        <h6>{props.welcome_txt == "future" ? props.t("You will be in") : props.t("You are here")}</h6>}
                        <h3 className="heading-color" title={val.space_name}>{val.space_name}</h3>
                        {
                            active_space == val.space_uuid
                            && val.is_vip_space == 1
                            && props.welcome_txt == "future"
                            && <h6 className="heading-color non_ellipsis_txt">{props.t("Space will be open")}</h6>
                        }
                        {
                            shortName != ''
                            && <h3 className="heading-color" title={val.space_short_name}>{val.space_short_name}</h3>
                        }
                    </div>
                </div>
                : val.is_duo_space == 1 ?
                    <div className={`one-spaces  unselected-space blue-space-bg`}>
                        <div className="blue-space every-space">
                            <h3 className="heading-color" title={val.space_name}>{val.space_name}</h3>
                            {
                                shortName != ''
                                &&
                                <h3 className="heading-color" title={val.space_short_name}>{val.space_short_name}</h3>
                            }
                        </div>
                    </div>
                    : (active_space == val.space_uuid) ?
                        <div className={`one-spaces selected-space two-rings-space-bg`}>
                            <div className="two-rings-space every-space">
                                <h6 className="heading-color">
                                    {props.welcome_txt == "future" ? props.t("You will be in") : props.t("You are here")}
                                </h6>
                                <h3 className="heading-color" title={val.space_name}>{val.space_name}</h3>
                                {
                                    props.welcome_txt == "future"
                                    &&
                                    <h6 className="heading-color non_ellipsis_txt">{props.t("Space will be open")}</h6>
                                }
                                {
                                    shortName != ''
                                    && <h3 className="heading-color"
                                           title={val.space_short_name}>{val.space_short_name}</h3>
                                }
                            </div>
                        </div>
                        :
                        <div className={`one-spaces unselected-space white-space-bg`}>
                            <div className="white-space every-space">
                                <h3 className="heading-color" title={val.space_name}>{val.space_name}</h3>
                                {
                                    shortName != ''
                                    && <h3 className="heading-color"
                                           title={val.space_short_name}>{val.space_short_name}</h3>
                                }
                            </div>
                        </div>
            }
            <AlertContainer ref={msg}{...Helper.alertOptions} />
        </div>
    )
}

const mapStateToProps = (state) => {
    return {
        graphics_data: state.NewInterface.interfaceGraphics,
        event_data: state.NewInterface.interfaceEventData,
        spaces_data: state.NewInterface.interfaceSpacesData,
        event_badge: state.NewInterface.interfaceBadgeData,
        auth: state.NewInterface.interfaceAuth,
        sliderData: state.NewInterface.interfaceSliderData,
        userInfo: state.NewInterface.userInfoData,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        resetSpace: (data) => dispatch({type: KCT.NEW_INTERFACE.RESET_SPACE, payload: data}),
    }
}


export default connect(mapStateToProps, mapDispatchToProps)(SpaceItem);

