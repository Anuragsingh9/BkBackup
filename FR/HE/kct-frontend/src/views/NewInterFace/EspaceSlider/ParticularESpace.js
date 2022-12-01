import React, {useRef} from 'react';
import _ from 'lodash';
import {connect} from 'react-redux';
import {useTranslation} from 'react-i18next';
import ReactTooltip from 'react-tooltip';
import {KeepContact as KCT} from '../../../redux/types';
import newInterfaceActions from '../../../redux/actions/newInterfaceAction';
import socketManager from '../../../socket/socketManager.js';
import Helper from '../../../Helper.js';
import {useAlert} from 'react-alert';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed for 'space' which are virtual rooms inside an Event created by the
 * Pilot, based on the different interests and scope of the Event. Each Space has almost one Space host which caters
 * the requests of the users present inside of it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.event_badge User badge details
 * @param {String} props.event_uuid Unique event uuid
 * @param {Object} props.graphics_data [State] This variable holds the current graphics data set in redux
 * @param {Number} props.index space index value
 * @param {Function} props.resetSpace To reset the space data
 * @param {Function} props.setSpaceHostData To update the space host data in redux store
 * @param {InterfaceSliderData} props.sliderData All spaces with pages required and sorted by type of spaces
 * @param {Array} props.space_host Space hosts of event
 * @param {Function} props.spaceJoin To trigger the join space method for redux and api
 * @param {Boolean} props.spacesActive To indicate if the space is currently joined or not
 * @param {InterfaceSpaceData} props.spaces_data Spaces data including conversations from redux store
 * @param {Object} props.style Style for user interaction
 * @param {SpaceData} props.active_space Space data
 * @param {Object} props.alert Reference object for displaying notification popup
 * @param {Function} props.changeSpace To trigger the api to change the space
 * @param {EventData} props.event_data Current event data
 * @param {Boolean} props.event_during To indicate if the event is live or not
 * @param {InterfaceSpaceData} props.event_space Current spaces data
 * @param {Function} props.sortSpaces To sort the available spaces in the event by type of space
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const SpaceItem = (props) => {
    const msg = useAlert();
    const {t} = useTranslation(['spaces', 'notification', 'qss']);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on a particular space  to join. This function will
     * handle an API call to manage space join process and once the call executed successfully it will update all the
     * related states and user can see the active participants on the user grid component for that specific space.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} space_uuid Unique space ID
     */
    const joinSpaces = (space_uuid) => {
        try {
            const formData = new FormData();
            const {event_uuid} = props;
            formData.append('space_uuid', space_uuid)
            formData.append('event_uuid', event_uuid)
            if (!_.isEmpty(props.event_space.current_joined_conversation)) {
                return props.msg && props.msg.show(t("notification:leave_conversation"), {type: 'error'})
            }
            let accessCode = props.event_data.accessCode;
            if (!accessCode) {
                accessCode = localStorage.getItem("accessCode");
            }
            if (accessCode) {
                formData.append('access_code', accessCode);
            }
            props.spaceJoin(formData)
                .then(res => {
                    if (res.data.status) {
                        const {space} = res.data.data;
                        props.setSpaceHostData(space.space_hosts);
                        props.resetSpace(false);
                        props.changeSpace(res.data.data);
                        const socketData = {
                            ...props,
                            spaces_data: {
                                ...props.spaces_data,
                                current_joined_space: space
                            }

                        }
                        socketManager.emitEvent.SPACE_CHANGE(socketData);
                        // after space change changing the grid pagination back to 1
                        props.triggerPagination({page: 1});
                    }
                })
                .catch(err => {
                    props.msg && props.msg.show(Helper.handleError(err), {type: 'error'})
                })

        } catch (err) {
            props.msg && props.msg.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is called when user click on any space to join.This will take that space's
     * uuid(unique id of a space) from its parameter and call method 'joinSpaces' to perform join space action.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} space_uuid Unique space ID
     */
    const handleSpace = (space_uuid) => {
        joinSpaces(space_uuid);
    }

    // space data object and active space
    const {val, active_space} = props;

    // shortname of space
    let shortName = '';
    if (val.space_short_name) {
        shortName = val.space_short_name;
    }


    return (
        <div onClick={() => {
            if (active_space != val.space_uuid && props.event_during && props.spacesActive) {
                handleSpace(val.space_uuid)
            }
        }} className={`spance-slide-bx ${active_space == val.space_uuid ? "selected-width" : ""}`}>

            {" "}
            {
                val.is_vip_space == 1 ?
                    <div
                        className={
                            `one-spaces 
                         ${active_space == val.space_uuid
                                ? 'selected-space  vip-selected-space-bg '
                                : 'vip-space-bg  unselected-space'
                            }`
                        }
                        data-for='enter_space_info_vip'
                        data-tip={active_space == val.space_uuid ? "" : t("Select Space")}
                    >
                        <div className={`vip-space every-space vip-selected-space`}>
                            {
                                active_space == val.space_uuid &&
                                <h6>
                                    {props.welcome_txt == "future" ? t("qss:You will be in") : t("You are here")}
                                </h6>
                            }
                            <h3>{val.space_name}</h3>
                            {
                                shortName != ''
                                && <h3>{val.space_short_name}</h3>
                            }
                            {
                                props.welcome_txt == "present"
                                && <h4>{val.users_count}</h4>
                            }

                            {
                                active_space == val.space_uuid
                                && props.welcome_txt == "future"
                                && <h6>{t("qss:Space will be open")}</h6>
                            }
                            {
                                props.welcome_txt == "present"
                                && <h5>{val.users_count > 1 ? t("guests") : t("guest")}</h5>
                            }
                        </div>
                        <ReactTooltip type="dark" effect="solid" id='enter_space_info_vip' />
                    </div>
                    : val.is_duo_space == 1 ?
                        <div
                            className={`one-spaces  unselected-space blue-space-bg`}
                            data-for='enter_space_info_unselected'
                            data-tip={t("Select Space")}
                        >
                            <div className="blue-space every-space">
                                <h3>{val.space_name}</h3>
                                {shortName != '' && <h3>{val.space_short_name}</h3>}
                                {props.welcome_txt == "present" && <><h4>{val.users_count}</h4>
                                    <h5>{val.users_count > 1 ? t("guests") : t("guest")}</h5></>}
                            </div>
                            <ReactTooltip type="dark" effect="solid" id='enter_space_info_unselected' />
                        </div>
                        : (active_space == val.space_uuid) ?
                            <div className={`one-spaces selected-space two-rings-space-bg`}>
                                <div className="two-rings-space every-space">
                                    {/* <h6>{t("You are here for")}:</h6> */}
                                    <h6>{props.welcome_txt == "future" ? t("qss:You will be in") : t("You are here")}</h6>
                                    <h3>{val.space_name}</h3>
                                    {shortName != '' && <h3>{val.space_short_name}</h3>}
                                    {props.welcome_txt == "present" && <h4>{val.users_count}</h4>}
                                    {props.welcome_txt == "future" &&
                                    <h6 style={{"max-width": "150px"}}>{t("qss:Space will be open")}</h6>}
                                    {props.welcome_txt == "present" &&
                                    <h5>{val.users_count > 1 ? t("guests") : t("guest")}</h5>}
                                </div>
                            </div>
                            :
                            <div className={`one-spaces unselected-space white-space-bg`} data-for='enter_space_info_1'
                                 data-tip={t("Select Space")}>
                                <div className="white-space every-space">
                                    <h3>{val.space_name}</h3>
                                    {shortName != '' && <h3>{val.space_short_name}</h3>}
                                    {props.welcome_txt == "present" && <><h4>{val.users_count}</h4>
                                        <h5>{val.users_count > 1 ? t("guests") : t("guest")}</h5></>}
                                </div>
                                <ReactTooltip type="dark" effect="solid" id='enter_space_info_1' />
                            </div>
            }
        </div>
    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        resetSpace: (data) => dispatch({type: KCT.NEW_INTERFACE.RESET_SPACE, payload: data}),
        setSpaceHostData: (data) => dispatch(newInterfaceActions.NewInterFace.setSpaceHostData(data)),//SapceHsot
    }
}

const mapStateToProps = (state) => {
    return {
        graphics_data: state.NewInterface.interfaceGraphics,
        event_data: state.NewInterface.interfaceEventData,
        spaces_data: state.NewInterface.interfaceSpacesData,
        event_badge: state.NewInterface.interfaceBadgeData,
        auth: state.NewInterface.interfaceAuth,
        sliderData: state.NewInterface.interfaceSliderData,
        spaceHost: state.NewInterface.interfaceSpaceHostData,
    };
};


export default connect(mapStateToProps, mapDispatchToProps)(SpaceItem);

