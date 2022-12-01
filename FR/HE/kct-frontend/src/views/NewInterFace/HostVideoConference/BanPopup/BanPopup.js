import React, {useRef, useState} from 'react'
import {confirmAlert} from 'react-confirm-alert';
import PopupClose from "../../../../images/cross.svg";
import {connect} from 'react-redux';
import socketManager from "../../../../socket/socketManager";
import eventActions from '../../../../redux/actions/eventActions';
import './BanPopup.css';
import Helper from '../../../../Helper.js';
import {useTranslation} from 'react-i18next'
import {useAlert} from 'react-alert';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying ban popup in HostVideoConference.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param props
 * @param {String} props.eventId Event uuid of current event
 * @param {Function} props.toggleBan Function to toggle the visibility of ban popup
 * @param {Boolean} props.banPopup Current visibility value of ban popup
 * @param {Object} props.msg Alert Container for displaying the notifications
 * @param {Number} props.user_id Target user id
 * @param {Function} props.userBan Redux action to update the user ban status in local redux store
 * @param {EventData} props.event_data Redux store state variable to provide the event data
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const BanPopup = (props) => {
    let styles = props.banPopup ? {display: "block", opacity: '1'} : {display: "none", opacity: '0'};
    const [sev, setSev] = useState("");
    const [res, setRes] = useState("Guidline Voilation");
    const [showInput, setInput] = useState(false);
    const msg = useAlert();
    const {t} = useTranslation('popup')

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler for severity changes
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const onChangeR1 = (e) => {
        setSev(e.target.value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler method for reason field change
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     **/
    const onReasonChange = (e) => {
        if (e.target.value == "Other") {

            setInput(true);
            setRes("");
        } else {

            setRes(e.target.value);
        }
    }

    const {eventId, space_id, space_name, banUser} = props;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the user ban data on server
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const setBanUser = () => {
        const formData = new FormData();
        formData.append("_method", "post");
        formData.append("event_uuid", eventId)
        formData.append("user_id", props.user_id);
        formData.append("severity", sev);
        formData.append('ban_reason', res);
        let accessCode = props.event_data.accessCode;
        if (!accessCode) {
            accessCode = localStorage.getItem("accessCode");
        }
        if (accessCode) {
            formData.append('access_code', accessCode);
        }
        try {
            props.userBan(formData)
                .then((response) => {
                    if (response.data.status) {
                        const data = {
                            namespace: Helper.getNameSpace(),
                            eventId: eventId,
                            targetUserId: props.user_id,

                        }

                        socketManager.emitEvent.BANNED_USER(data);
                        props.msg && props.msg.show(t("banning"), {type: "success"});
                        props.toggleBan()


                    }
                })
                .catch((err) => {
                    console.error("user ban error", err)
                    props.msg && props.msg.show(Helper.handleError(err), {type: 'error'})
                })
        } catch (err) {
            console.error("user ban fetch error", err)
            props.msg && props.msg.show(Helper.handleError(err), {type: 'error'})
        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Submit button handler when user click on submit this will confirm user with popup before submitting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const submit = () => {
        confirmAlert({

            message: t("BAN the person "
            ),
            buttons: [
                {
                    label: t("Yes"),
                    onClick: () => {
                        setBanUser()
                    }
                },
                {
                    label: t("No"),
                    onClick: () => {
                        return
                    }
                }
            ]
        });
    };


    return (
        <div>
            <div className="modal fade in member-ban-popup" role="dialog" style={styles}>
                <div className="modal-dialog no-texture">
                    <div className="banpop-inner position-relative">
                        <div className="modal-header">
                            <h4>{t("User Ban")}</h4>
                            <button type="button" onClick={props.toggleBan} className={"close"}><img src={PopupClose} />
                            </button>
                        </div>
                        <div className="modal-body ban-content px-0">
                            <div className="mb-10">
                                <div className='row'>
                                    <div className='col-sm-4'>
                                        <h4 className="d-inline-block"> {t("Reason")} : </h4>
                                    </div>
                                    <div className='col-sm-8'>
                                        <select name="reason" onChange={onReasonChange} lable="Please Select"
                                                className="custom_select form-control form-control-sm d-inline-block">
                                            {/* <option>Please Select</option> */}
                                            <option value="Guidline Voilation">{t("Guidline Voilation")}</option>
                                            <option
                                                value="User is using foul language">{t("User is using foul language")}</option>
                                            <option value="Suspicious Activity">{t("Suspicious Activity")}</option>
                                            <option value="Other">{t("Other")}</option>
                                        </select>
                                    </div>
                                </div>


                            </div>

                            {showInput &&
                            <div className="row other-field mb-30">
                                <div className='col-sm-4'></div>
                                <div className='col-sm-8'>
                                    <textarea className="form-control form-control-sm custom_textarea form-check-input"
                                              type="text" autocapitalize="none" onChange={(e) => onReasonChange(e)}
                                              style={{border: ".1rem solid black"}} />
                                </div>
                            </div>

                            }

                            <div onChange={onChangeR1} className='row'>
                                <div className='col-sm-4'>
                                    <h4 className="d-inline-block">{t("Severity")} : </h4>
                                </div>
                                <div className='col-sm-8'>
                                    <ul className="d-inline-block ml-10">
                                        <li>
                                            <InputField type={"radio"} name={"radioButton"} value={"1"} id={"r1"}
                                                        label={"1"} />
                                        </li>
                                        <li>
                                            <InputField type={"radio"} name={"radioButton"} value={"2"} id={"r2"}
                                                        label={"2"} />
                                        </li>
                                        <li>
                                            <InputField type={"radio"} name={"radioButton"} value={"3"} id={"r3"}
                                                        label={"3"} />
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <br />
                            <div className="text-center">
                                {/* <p>{KCTLocales.KCT_EVENT.SURE_BAN}</p> */}
                                {sev != '' &&
                                <React.Fragment>
                                    <button className="btn ban-con" onClick={submit}>{t("Confirm")}</button>
                                    {" "}
                                    <button className="btn ban-can" onClick={props.toggleBan}>{t("Cancel")}</button>
                                </React.Fragment>
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );


}
const mapDispatchToProps = (dispatch) => {
    return {
        userBan: (data) => dispatch(eventActions.Event.userBan(data))
    }
}
const mapStateToProps = (state) => {
    return {
        event_data: state.NewInterface.interfaceEventData,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(BanPopup);

export const InputField = ({value, label, name, placeholder, type, onChange, id}) => (
    <div className="form-group">

        <input
            class="form-check-input"
            type={type}
            value={value}
            name={name}
            placeholder={placeholder}
            onChange={onChange}
            id={id}
        />
        {label && <label className="form-check-label" htmlFor={id}>{label}</label>}
    </div>
);
