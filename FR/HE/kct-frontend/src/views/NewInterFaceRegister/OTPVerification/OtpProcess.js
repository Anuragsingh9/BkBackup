import React, {useEffect, useRef, useState} from 'react'
import './OtpVerification.css'
import {Field, reduxForm} from 'redux-form'
import Validation from '../../../functions/ReduxFromValidation';
import ProgressButton from 'react-progress-button';
import {connect} from 'react-redux'
import {Provider as AlertContainer, useAlert } from 'react-alert';
import Helper from '../../../Helper'
import authActions from '../../../redux/actions/authActions';
import {KCTLocales} from '../../../localization';
import {useTranslation} from 'react-i18next'
import {reactLocalStorage} from 'reactjs-localstorage';
import _ from 'lodash';
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
 * It loads the otp component when user comes without event-uuid in url.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getOtpData To get the otp data by providing the encrypted key
 * @param {Function} props.resendOtp To resend the otp to user
 * @param {Function} props.registerOtp To submit the otp for verification
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let OtpWithoutId = (props) => {
    const {t} = useTranslation(['qss', 'notification'])

    // Initialisation fo message / alert ref to show alerts on success or error.
    const msg = useAlert();

    //button control and button state for progress button
    const [buttonControl, setButton] = useState(true);
    const [buttonState, setButtonState] = useState('');

    const [eventUuid, setEventUuid] = useState(null);
    const [userEmail, setUserEmail] = useState(null);

    const {event_uuid, key} = useParams();

    useEffect(() => {
        if (key) {
            props.getOtpData({key: key}).then(res => { // fetching otp data
                if (res.data.status) {
                    setEventUuid(res.data.event_data.event_uuid);
                    setUserEmail(res.data.email);
                }
            })
        }
    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To resend otp to the user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const resendOtp = () => {
        let formData = new FormData();
        event_uuid !== undefined && formData.append('event_uuid', event_uuid)
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
     * @description This method will prepare request data for verifying OTP and handles the API response.
     * If entered OTP is correct and have event_uuid then user will be redirected to the dashboard and if event_uuid is
     * undefined then user will be redirected to the event list page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {object} data - Data object which can contain multiple fields data.
     * @param {string} data.otp - OTP field value.
     */
    const doRegister = (data) => {
        let formData = new FormData()
        setButton(false);
        setButtonState('loading');

        formData.append('fname', localStorage.getItem('fname'))
        formData.append('lname', localStorage.getItem('lname'))
        formData.append('otp', data.otp)
        eventUuid && formData.append('event_uuid', eventUuid)
        userEmail && formData.append('email', userEmail);
        props.registerOtp(formData).then((response) => {
            setButton(true);
            setButtonState('');
            if (response.data.status) {
                if (eventUuid !== undefined && eventUuid) {
                    msg && msg.show(t("notification:success message"), {
                        type: "success", onClose: () => {
                            let accessCode = localStorage.getItem('accessCode');
                            if (accessCode) {
                                // if access code is present in local storage then redirect with access code
                                props.history.push({
                                    pathname: `/dashboard/${eventUuid}`
                                })
                            }
                            let url = `/dashboard/${eventUuid}`
                            props.history.push(url);
                        }
                    })
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
                let message = Helper.handleError(err);
                if (_.has(err.response.data, ['message'])) {
                    message = err.response.data.message;
                }
                msg && msg.show(message, {type: "error"})
            })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will allow user to login with different user account.
     * @warning It will remove the access token from local storage and redirect to Quick-login page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const redirectInitial = () => {
        localStorage.removeItem('accessToken');
        props.history && props.history.push('/quick-login');
    }

    let {handleSubmit} = props
    return (
        <div className="container">
            <div className="welcome-sign">
                <h2 className="enter-note heading-color">{KCTLocales.WELCOME}</h2>
                <div className="row">
                    <div className="col-lg-12 mt-20">
                    </div>
                    <div className="col-lg-12 px-0 form-regi">

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
                                                <ProgressButton className="otp-pro" controlled={buttonControl}
                                                                type="submit" state={buttonState}>
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
                                    <div className="col-md-12 text-center">
                                        <div className="or-saprator-div">
                                            <div className="or-saprator"><span>or</span></div>
                                        </div>
                                    </div>
                                    <div className="col-md-12 text-center otp-login-link">
                                        <div className="social-login-txt font-13">
                                        </div>
                                        <div className="create-account-link d-inline font-14 w-100"
                                             onClick={redirectInitial}> {t("Login with other account")} </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        registerOtp: (data) => dispatch(authActions.Auth.registerOtp(data)),
        resendOtp: (data) => dispatch(authActions.Auth.resendOtp(data)),
        getOtpData: (data) => dispatch(authActions.Auth.getOtpData(data))
    }
}

const mapStateToProps = (state) => {
    return {}
}

OtpWithoutId = connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'user-verification', // a unique identifier for this form
    enableReinitialize: true,
})(OtpWithoutId));

export default OtpWithoutId
