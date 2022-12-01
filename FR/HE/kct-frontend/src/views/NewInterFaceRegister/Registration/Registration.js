import React, {useEffect, useRef, useState} from "react";
import {change, Field, getFormValues, reduxForm} from "redux-form";
import Validation from "../../../functions/ReduxFromValidation";
import ProgressButton from 'react-progress-button';
import Helper from "../../../Helper";
import {Provider as AlertContainer, useAlert } from 'react-alert';
import {connect} from "react-redux";
import authActions from "../../../redux/actions/authActions";
import LoginUserIcon from "../../../images/login-user-icon.png";
import LoginSecure from "../../../images/login-secure-icon.png";
import MailIcon from "../../../images/mail-icon.png";
import './Registration.css';
import {NavLink, useParams} from "react-router-dom";

import {useTranslation} from "react-i18next";
import i18n from "i18next";
import {KeepContact as KCT} from '../../../redux/types';
import KeepContactagent from "../../../agents/KeepContactagent";
import _ from 'lodash';
import {Button} from "react-bootstrap";
import ImportantIcon from "../../Svg/ImportantIcon";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common method for rendering input boxes in UI.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} placeholder Placeholder value for the field
 * @param {Object} input Extra props passed from parent component by redux form
 * @param {String} type Type of the field
 * @param {Boolean} touched To indicate if the field is focused
 * @param {String} error The error message to show if any with respective field
 * @param {String} warning The warning message to show if any with respective field
 * @param {Function} onChange Handler method when the field value is changed
 * @param {String} value Value of field to show user
 * @return {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const InputBox = ({placeholder, input, disabled, onChange, type, value, meta: {touched, error, warning}}) => {
    return (<>
            <input {...input} value={input.value} disabled={disabled} onChange={input.onChange} type={type}
                   placeholder={placeholder}
                   className="form-control" />
            {touched &&
            ((error && <span className="text-danger">{error}</span>) ||
                (warning && <span>{warning}</span>))}
        </>
    )
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate password must match password confirmation value
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {String}
 * @param {String} value Value of the password
 * @param {Object} allValues All passwords stored here
 * @param {String} allValues.password Confirm password value
 * @method
 */
const passwordsMustMatch = (value, allValues) =>
    value !== allValues.password ? i18n.t('qss:Passwords do not match') : undefined

/**
 *----------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying RegisterForBasicDetails in routes/index
 *----------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.registerBasic To trigger the api to save the user data
 * @param {Function} props.handleSubmit To handle the submit to perform the api
 * @param {Function} props.setEventGroupLogo To update the logo of the header with respect to event group settings
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let RegisterForBasicDetails = (props) => {

    const [buttonControl, setButtonControl] = useState(true);
    const [buttonState, setButtonState] = useState('');
    const [showPass, setShowPass] = useState(false);
    const [showConfirmPass, setShowConfirmPass] = useState(false);
    const [fname, setFname] = useState('');
    const [lname, setLname] = useState('');
    const [email, setEmail] = useState('');
    // const [password, setPassword] = useState('');
    const [formMode, setFormMode] = useState(false);
    const [sentOtpEmail, setSentOtpEmail] = useState('');
    const [showOtpSection, setShowOtpSection] = useState(true)

    const msg = useAlert()
    const {t} = useTranslation(['qss', 'notification']);
    const {event_uuid} = useParams();

    const [disableFields, setDisableFields] = useState(false);


    const urlSearchParams = new URLSearchParams(window.location.search);
    const params = Object.fromEntries(urlSearchParams.entries());
    useEffect(() => {
        getEventGraphicData(event_uuid ? event_uuid : '')
        if (Object.keys(params).length !== 0) {
            setFormMode(true);
            props.updateRegisterForm('fname', params.fname);
            props.updateRegisterForm('lname', params.lname);
            props.updateRegisterForm('email', params.email);
            props.updateRegisterForm('password', params.email);
            props.updateRegisterForm('password_confirmation', params.email);
        }
        return () => {
            Helper.implementGraphicsHelper(props.main_color)
            props.setEventGroupLogo(null);
        }
    }, [])

    useEffect(() => {
        if (formMode) {
            setDisableFields(true);
            setShowOtpSection(false)
            setSentOtpEmail(props?.formValues?.email || params.email)
        }
    }, [formMode])


    useEffect(() => {
        if (props.location.state?.formMode !== undefined) {
            setFormMode(props.location.state.formMode);
            setFname(props.location.state.fname)
            setLname(props.location.state.lname)
            setEmail(props.location.state.email)

            props.updateRegisterForm('fname', props.location.state.fname);
            props.updateRegisterForm('lname', props.location.state.lname);
            props.updateRegisterForm('email', props.location.state.email);
            props.updateRegisterForm('password', props.location.state.email);
            props.updateRegisterForm('password_confirmation', props.location.state.email);

            setDisableFields(true);
        }
    }, [props.location.state?.formMode])


// get event design setting
    const getEventGraphicData = (data) => {

        try {
            return KeepContactagent.Event.getEventGraphicData(data).then((res) => {
                props.setEventDesignSetting(res.data.data);
                props.setEventGroupLogo(Helper.prepareEventGroupLogo(res.data.data));
                Helper.implementGraphicsHelper(res.data.data.graphic_data)

            }).catch((err) => {
                console.error("err in graphics fetch", err)
            })
        } catch (err) {
            console.error("err in graphics fetch", err)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for sending request for registering a new user in HE(Attendee) side and handle
     * the API response.If the response is true user will be redirected to Quick OTP page.
     *
     * if props data have event_uuid then user will be automatically register to event also.
     *
     * If user email is already taken then throw error
     * Else new user is created and user token is saved to local storage.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} data Data to send to backend for forget password procedure
     * @param {String} data.email Email of user who forgot the password
     * @method
     */
    const doRegister = (data) => {
        if (formMode) {
            let formData = new FormData()
            // setButton(false);
            setButtonState('loading');
            formData.append('otp', data.otp)
            event_uuid && formData.append('event_uuid', event_uuid)
            props.registerOtp(formData).then((response) => {
                // setButton(true);
                setButtonState('');

                if (response.data.status) {
                    if (event_uuid !== undefined && event_uuid) {
                        msg && msg.show(t("notification:success message"), {
                            type: "success", onClose: () => {

                                let accessCode = localStorage.getItem('accessCode');
                                if (accessCode) {
                                    // if access code is present in local storage then redirect with access code
                                    props.history.push({
                                        pathname: `/dashboard/${event_uuid}`
                                    })
                                }
                                let url = `/dashboard/${event_uuid}`
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
                    // setButton(true);
                    setButtonState('');
                    let message = Helper.handleError(err);
                    if (_.has(err.response.data, ['message'])) {
                        message = err.response.data.message;
                    }
                    msg && msg.show(message, {type: "error"})
                })
        } else {

            let postData = new FormData();

            Object.keys(data).map((keyName) => {
                postData.append(keyName, data[keyName])
            })
            postData.append('event_uuid', event_uuid)
            postData.append('lang', localStorage.getItem("current_lang") ? localStorage.getItem("current_lang") : 'EN');

            const accessCode = localStorage.getItem('accessCode');
            if (accessCode) {
                // appending access_code to form data if present in local storage
                postData.append('access_code', accessCode);
            }
            setButtonControl(false);
            setButtonState("loading")
            props.registerBasic(postData).then((response) => {
                setButtonControl(true);
                setButtonState("")
                setFormMode(true);
                if (response.data.status) {
                    if (response.data.data.already_exists) {
                        msg && msg.show(t("qss:User exist"), {
                            type: "error"
                        })
                    } else {
                        localStorage.setItem('accessToken', response.data.data.access_token)
                        localStorage.setItem('fname', response.data.data.fname)
                        localStorage.setItem('lname', response.data.data.lname)
                        localStorage.setItem('email', response.data.data.email);
                        // props.history.push(`/quick-otp/${event_uuid}`)
                    }
                } else {
                    setButtonControl(true);
                    setButtonState("")
                    msg && msg.show(t("qss:something_worng"), {
                        type: "error"
                    })
                }
            }).catch((err) => {
                setButtonControl(true);
                setButtonState("")
                const error = err.response.data.errors
                const msgg = err.response.data.message
                if (err.response.data) {
                    if (error) {
                        for (let key in error) {
                            if (error[key]) {
                                msg && msg.show(error[key], {type: "error "})
                            }
                        }
                    } else {
                        if (msgg) {
                            msg && msg.show(msgg, {type: "error "})
                        } else {
                            msg && msg.show(Helper.handleError(err), {type: "error "})
                        }
                    }
                } else {
                    msg && msg.show(Helper.handleError(err), {type: "error "})
                }
            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method handles the change in the state of inputs of first name and last name
     * by handling the state(setFname and setLname)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const handleChange = (e) => {
        props.change(`${[e.target.name]}`, `${Helper.jsUcfirst(e.target.value)}`)
        if (e.target.name === "fname") {
            setFname(Helper.jsUcfirst(e.target.value))
        } else {
            setLname(Helper.jsUcfirst(e.target.value))
        }
    }
    const {handleSubmit} = props;

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

    return (
        <div className="clearfix w-100 d-inline">
            <div className="col-xs-12 px-0 col-sm-12">
                <AlertContainer ref={msg} {...Helper.alertOptions} />

                <form className="form-style2 register-ac" onSubmit={handleSubmit(doRegister)}>
                    <div className="row">
                        <div className="col-md-6">
                            <div className="form-group p-relative">
                                <img src={LoginUserIcon} className="img-responsive field-img" />
                                <Field
                                    id="fname"
                                    name="fname"
                                    type="text"
                                    autocapitalize="none"
                                    value={fname}
                                    onChange={handleChange}
                                    placeholder={t("qss:First name")}
                                    props={{
                                        onChange: handleChange,
                                        value: fname
                                    }}
                                    validate={[Validation.required]}
                                    component={InputBox}
                                    disabled={disableFields}
                                />
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-group p-relative">
                                <img src={LoginUserIcon} className="img-responsive field-img" />
                                <Field
                                    id="lname"
                                    name="lname"
                                    type="text"
                                    autocapitalize="none"
                                    value={lname}
                                    onChange={handleChange}
                                    props={{
                                        onChange: handleChange,
                                        value: lname
                                    }}
                                    placeholder={t("qss:Last name")}
                                    validate={[Validation.required]}
                                    component={InputBox}
                                    disabled={disableFields}
                                />
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6">
                            <div className="login-pass pass-toggle-wrap">
                                <div className="form-group p-relative">
                                    <img src={LoginSecure} className="img-responsive field-img" />
                                    <Field
                                        name="password"
                                        props={{
                                            infoLabel: "",
                                        }}
                                        value={""}
                                        type={showPass ? "text" : "password"}
                                        component={InputBox}
                                        placeholder={t("qss:Password")}
                                        validate={[Validation.required]}
                                        disabled={disableFields}
                                    />
                                    <div className="pass-toggle">
                                        <i aria-hidden="true" onClick={() => {
                                            !disableFields && setShowPass(!showPass)
                                        }}
                                           className={showPass ? "fa fa-eye-slash hide-pass" : "fa fa-eye show-pass"}></i>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="login-pass pass-toggle-wrap">
                                <div className="form-group p-relative">
                                    <img src={LoginSecure} className="img-responsive field-img" />
                                    <Field
                                        name="password_confirmation"
                                        props={{
                                            infoLabel: "",
                                        }}
                                        type={showConfirmPass ? "text" : "password"}
                                        value={""}
                                        component={InputBox}
                                        placeholder={t("qss:Confirm Password")}
                                        disabled={disableFields}
                                        validate={[Validation.required, passwordsMustMatch]}
                                    />
                                    <div className="pass-toggle">
                                        <i aria-hidden="true" onClick={() => {
                                            !disableFields && setShowConfirmPass(!showConfirmPass)
                                        }}
                                           className={showConfirmPass ? "fa fa-eye-slash hide-pass" : "fa fa-eye show-pass"}></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-12">
                            <div className="form-group p-relative">

                                <img src={MailIcon} className="img-responsive field-img" />
                                <Field
                                    id="email"
                                    name="email"
                                    type="email"
                                    autocapitalize="none"
                                    placeholder={"Email"}
                                    validate={[Validation.required, Validation.email]}
                                    component={InputBox}
                                    disabled={disableFields}

                                />
                            </div>
                        </div>
                    </div>
                    {formMode &&

                    <div className="form-group otp-ac">
                        <div className="emailInstructionDiv">
                            <ImportantIcon />
                            <p>{t("qss:sent email instruction")}&nbsp;{sentOtpEmail}</p>
                            <div>
                                <Button onClick={resendOtp}>Resend email</Button>
                                <Button onClick={() => setShowOtpSection(true)}>Manually enter OTP</Button>
                            </div>
                        </div>
                        {
                            showOtpSection &&
                            <Field id="code" name="otp" type="text" autocapitalize="none"
                                   placeholder={t("qss:Enter OTP")} validate={[Validation.required]}
                                   component={InputBox} />
                        }
                    </div>
                    }

                    <div className="nxt-btn  mb-20 uni-color-btn text-center">
                        {
                            showOtpSection &&
                            <ProgressButton controlled={buttonControl} type="submit" state={buttonState}>
                                {
                                    !formMode ? t("qss:Sign up") : t("qss:enter otp")
                                }
                            </ProgressButton>
                        }
                    </div>
                    <div className="have-log font-13 text-center">
                        <p>--- <br />{t("Log in to an account")}</p>
                        <NavLink onClick={() => localStorage.removeItem('accessToken')} to={`/quick-login/${event_uuid}`} className="font-14">
                            {t("qss:Log in if you already have an account")}
                        </NavLink>
                    </div>
                </form>
            </div>
        </div>
    )
}


const mapDispatchToProps = (dispatch) => {
    return {
        registerBasic: (data) => dispatch(authActions.Auth.registerBasic(data)),
        setEventDesignSetting: (data) => dispatch({type: KCT.EVENT.SET_EVENT_DESIGN_SETTINGS, payload: data}),
        setEventGroupLogo: (data) => dispatch({type: KCT.EVENT.SET_EVENT_GROUP_LOGO, payload: data}),
        registerOtp: (data) => dispatch(authActions.Auth.registerOtp(data)),
        updateRegisterForm: (field, value) => dispatch(change('newRegister', field, value)),
        resendOtp: (data) => dispatch(authActions.Auth.resendOtp(data)),
    }
}

const mapStateToProps = (state, dispatch) => {
    return {
        formValues: getFormValues('newRegister')(state),
        main_color: state.page_Customization.initData.graphics_data,
    }
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is used for redux form
 * ---------------------------------------------------------------------------------------------------------------------
 */
RegisterForBasicDetails = reduxForm({
    form: 'newRegister', // a unique identifier for this form
    enableReinitialize: true,
    keepDirtyOnReinitialize: true,
})(RegisterForBasicDetails)


RegisterForBasicDetails = connect(
    mapStateToProps,
    mapDispatchToProps
)(RegisterForBasicDetails);

export default RegisterForBasicDetails;