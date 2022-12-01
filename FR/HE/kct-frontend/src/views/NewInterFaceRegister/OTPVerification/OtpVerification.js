import React, {useEffect, useRef, useState} from 'react'
import './OtpVerification.css'
import {Field, reduxForm} from 'redux-form'
import Validation from '../../../functions/ReduxFromValidation';
import ProgressButton from 'react-progress-button';
import {connect} from 'react-redux'
import {Provider as AlertContainer, useAlert } from 'react-alert';
import Helper from '../../../Helper'
import authActions from '../../../redux/actions/authActions';
import {useTranslation} from 'react-i18next'
import {reactLocalStorage} from 'reactjs-localstorage';
import {KeepContact as KCT} from '../../../redux/types';
import KeepContactagent from "../../../agents/KeepContactagent";
import {useParams} from "react-router-dom";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component used for rendering input box in UI.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} placeholder Placeholder value for the field
 * @param {Object} input Extra props passed from parent component by redux form
 * @param {Boolean} touched To indicate if the field is focused
 * @param {String} error The error message to show if any with respective field
 * @param {String} warning The warning message to show if any with respective field
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const InputBox = ({placeholder, input, meta: {touched, error, warning}}) => {
    return (<>
            <input {...input} placeholder={placeholder} className="form-control" />
            {touched &&
            ((error && <span className="text-danger">{error}</span>) ||
                (warning && <span>{warning}</span>))}
        </>
    )
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for Otp verification form and child otp page component.
 * This component loads when user comes with event uuid in url.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 * @deprecated
 */
let OtpWithId = (props) => {

    // Initialisation fo message / alert ref to show alerts on success or error.
    const msg = useAlert();
    const {t} = useTranslation(['qss', 'notification']);

    //button control and button state for progress button
    const [buttonControl, setButton] = useState(true);
    const [buttonState, setButtonState] = useState('');

    const [eventUuid, setEventUuid] = useState(null);
    const [email, setEmail] = useState('');

    const {key} = useParams();
    const {event_uuid} = useParams()
    useEffect(() => {

        if (!localStorage.getItem('accessToken')) {
            props.history && props.history.push('/')
        }
        getEventGraphicData(event_uuid ? event_uuid : '')
        return () => {
            Helper.implementGraphicsHelper(props.main_color)
        }
    }, []);

    // get event design setting
    const getEventGraphicData = (data) => {
        try {
            return KeepContactagent.Event.getEventGraphicData(data).then((res) => {
                props.setEventDesignSetting(res.data.data);
                Helper.implementGraphicsHelper(res.data.data.graphic_data)

            }).catch((err) => {
                console.error("err in event graphic fetch", err)
            })
        } catch (err) {
            console.error("err in event graphics fetch", err)
        }
    }

    // const resendOtp=()=>{
    //   let formData = new FormData();
    //   event_uuid !== undefined &&  formData.append('event_uuid', event_uuid)
    //   props.resendOtp(formData).then((response) => {
    //     if (response.data.status) {
    //      msg && msg.show(t("notification:success message"), {
    //         type: "success",
    //       })
    //     }
    //   })
    //   .catch((err) => {
    //     msg && msg.show(Helper.handleError(err), { type: "error" })
    //   })
    // }

    // const doRegister = (data) => {
    //   let formData = new FormData()
    //   setButton(false);
    //   setButtonState('loading');

    //   formData.append('fname', localStorage.getItem('fname'))
    //   formData.append('lname', localStorage.getItem('lname'))
    //   formData.append('otp', data.otp)
    //  event_uuid !== undefined &&  formData.append('event_uuid', event_uuid)
    //   props.registerOtp(formData).then((response) => {
    //     setButton(true);
    //     setButtonState('');
    //     if (response.data.status) {
    //     if(event_uuid !== undefined){
    //       if(!response.data.data.is_participant){
    //         msg && msg.show(t("notification:success message"), {
    //           type: "success", onClose: () => {
    //             props.history.push(`/dashboard/${event_uuid}`)
    //           }
    //         }
    //       }
    //     }
    //   }

    useEffect(() => {
        setEventUuid(key);
        props.getOtpData({key}).then(res => {
            if (res.data.status) {
                setEventUuid(res.data.event_data.event_uuid);
                setEmail(res.data.email);
            }
        });
    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for resending the OTP to the user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const resendOtp = () => {
        const event_uuid = eventUuid;
        let formData = new FormData();
        event_uuid !== undefined && formData.append('event_uuid', event_uuid);
        props.resendOtp(formData).then((response) => {
            if (response.data.status) {
                msg && msg.show(t("notification:success message"), {
                    type: "success",
                })
            }
        })
            .catch((err) => {
                msg && msg.show(Helper.handleError(err), {type: "error"})
            })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Function handles triggering api call of registration for completing otp process.
     * -----------------------------------------------------------------------------------------------------------------
     *
     *
     * @param {object} data Data object which can container multiple fields data.
     * @param {string} data.otp Otp field value.
     * @method
     **/
    const doRegister = (data) => {
        const event_uuid = eventUuid;
        let formData = new FormData()
        setButton(false);
        setButtonState('loading');
        formData.append('otp', data.otp)
        event_uuid !== undefined && formData.append('event_uuid', event_uuid)
        formData.append('email', email);
        props.registerOtp(formData).then((response) => {
            setButton(true);
            setButtonState('');
            if (response.data.status) {
                if (event_uuid !== undefined) {
                    if (!response.data.data.is_participant) {
                        msg && msg.show(t("notification:success message"), {
                            type: "success", onClose: () => {
                                props.history.push(`/dashboard/${event_uuid}`)
                            }
                        })
                    } else {
                        msg && msg.show(t("notification:success message"), {
                            type: "success", onClose: () => {
                                props.history.push(`/dashboard/${event_uuid}`)
                            }
                        })
                    }
                } else {
                    msg && msg.show(t("notification:success message"), {
                        type: "success", onClose: () => {
                            props.history.push(`/event-list`)
                        }
                    })
                }
            }
        })
            .catch((err) => {
                setButton(true);
                setButtonState('');
                msg && msg.show(Helper.handleError(err), {type: "error"})
            })
    }

    let {handleSubmit} = props
    return (
        <div className="otp-main">
            <h4>{t("6-digit validation code")} at {reactLocalStorage.get('email') ? reactLocalStorage.get('email') : ''}</h4>
            <div className="otp-head">
                <div className="col-xs-12 col-sm-12 mt-30">
                    <AlertContainer ref={msg} {...Helper.alertOptions} />
                    <form className="form-style2" onSubmit={handleSubmit(doRegister)}>
                        <div className="clearfix">
                            <div className="form-group otp-ac">
                                {/* <img src={require("../assets/images/login-secure-icon.png")} className="img-responsive" /> */}
                                <Field id="code" name="otp" type="text" autocapitalize="none"
                                       placeholder={t("Enter OTP")} validate={[Validation.required]}
                                       component={InputBox} />
                            </div>
                            <div className="form-group otp-pbtn uni-color-btn">
                                <ProgressButton className="otp-pro" controlled={buttonControl} type="submit"
                                                state={buttonState}>
                                    {t("Process")}
                                </ProgressButton>
                            </div>
                            <div className="clearfix w-100 text-center">
                                <button onClick={resendOtp} type="button" class="resend-otp">
                                    {t("Resent OTP")}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    )

}


const mapDispatchToProps = (dispatch) => {
    return {
        registerOtp: (data) => dispatch(authActions.Auth.registerOtp(data)),
        resendOtp: (data) => dispatch(authActions.Auth.resendOtp(data)),
        setEventDesignSetting: (data) => dispatch({type: KCT.EVENT.SET_EVENT_DESIGN_SETTINGS, payload: data}),
        getOtpData: (data) => dispatch(authActions.Auth.getOtpData(data))
    }


}

const mapStateToProps = (state) => {
    return {
        // initialValues: resilience.sprint.data,
        main_color: state.page_Customization.initData.graphics_data

    }
}
OtpWithId = reduxForm({
    form: 'user-verification', // a unique identifier for this form
    enableReinitialize: true,

})(OtpWithId)

OtpWithId = connect(
    mapStateToProps,
    mapDispatchToProps
)(OtpWithId);


export default OtpWithId

