import React, {useEffect, useRef, useState} from 'react'
import './login.css'
import {NavLink, Navigate, useParams} from 'react-router-dom'
import {Field, reduxForm} from 'redux-form'
import Validation from '../../../functions/ReduxFromValidation';
import ProgressButton from 'react-progress-button';
import {connect} from 'react-redux'
import Helper from '../../../Helper';
import {Provider as AlertContainer,useAlert } from 'react-alert';
import authActions from '../../../redux/actions/authActions';
import {KeepContact as KCT} from '../../../redux/types';
import {useTranslation} from 'react-i18next';
import _ from 'lodash';
import KeepContactagent from "../../../agents/KeepContactagent";
import eventActions from "../../../redux/actions/eventActions";
import routeAgent from "../../../agents/routeAgent";
import Constants from "../../../Constants";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component used for rendering input box in UI.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} placeholder Placeholder value for the field
 * @param {Object} input Extra props passed from parent component by redux form
 * @param {String} type Type of the field
 * @param {Boolean} touched To indicate if the field is focused
 * @param {String} error The error message to show if any with respective field
 * @param {String} warning The warning message to show if any with respective field
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const InputBox = ({placeholder, input, type, meta: {touched, error, warning}}) => {
    return (<>
            <input {...input} type={type} placeholder={placeholder} className="form-control" />
            {touched &&
            ((error && <span className="text-danger">{error}</span>) ||
                (warning && <span>{warning}</span>))}
        </>
    )
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for Simple Login form and child Login page component. It gets rendered when user have event
 * uuid in url.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getEventDetails [Dispatcher|API] Method used to get the event details from api
 * @param {Function} props.setEventDetailsData [Dispatcher] This method updates the redux to store the event data
 * @param {Function} props.login Api method to trigger the login api
 * @param {Function} props.getUsersData Api method to fetch the user details with token
 * @param {EventData} props.eventDetailsData To provide the event basic data
 * @param {Boolean} props.eventDetailsLoad To indicate if event details are fetched or not
 * @param {Function} props.setEventGroupLogo To update the logo of the header with respect to event group settings
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let Login = (props) => {
    const {t} = useTranslation(['qss', 'notification', 'validation']);

    // Initialisation fo message / alert ref to show alerts on success or error.
    const msg = useAlert();

    //button control and button state for progress button
    const [buttonControl, setButton] = useState(true);
    const [buttonState, setButtonState] = useState('');
    const [loadingState, setLoadingState] = useState(true);

    // eye button state for password show and hide
    const [showPass, setPass] = useState(false);
    // redirect to event dashboard page state
    const [redirectToevent, setRedirect] = useState(false);

    const {event_uuid} = useParams();


    useEffect(() => {
        let token = localStorage.getItem('accessToken');
        if (!_.isEmpty(token)) {
            // setting this item so if token is invalid then axios will not redirect to /
            localStorage.setItem('ignore401', 'true');
            props.getBadge(props.event_uuid).then((res) => {
                localStorage.removeItem('ignore401');
                if (res.status === 200) {
                    props.history.push(routeAgent.routes.EVENT_DASHBOARD(event_uuid));
                }
            }).catch(err => {
                localStorage.removeItem('ignore401');
                if (err?.response?.status === 403 && err?.response.data?.redirect_code === 1002) {
                    // user is logged in but account is not verified
                    props.history.push({
                        pathname: routeAgent.routes.QUICK_REGISTER(event_uuid),
                        state: {
                            formMode: Constants.SIGN_UP_FORM_MODE.OTP_VERIFY,
                            fname: err.response.data.user.fname,
                            lname: err.response.data.user.lname,
                            email: err.response.data.user.email,
                        }
                    })
                } else {
                    // user badge api is not valid so removing the access token
                    localStorage.removeItem('accessToken');
                    setLoadingState(false);
                }
            });

            // props.history.push(`/dashboard/${event_uuid}`);
        } else {
            setLoadingState(false);
        }
        getEventGraphicData(event_uuid ? event_uuid : '')

        return () => {
            Helper.implementGraphicsHelper(props.main_color)
            props.setEventGroupLogo(null);
        }
    }, []);

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
     * @description This method handles api call and response of getting user data like active event id and current
     * language and storing it to localstorage.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getUsersData = () => {
        try {
            props.getUsersData()
                .then((res) => {
                    const {active_event_uuid, lang} = res.data;
                    if (active_event_uuid) {
                        localStorage.setItem('active_event_uuid', active_event_uuid);
                    }
                    localStorage.setItem('current_lang', lang.current.toUpperCase());
                })
                .catch((err) => {
                    msg && msg.show(Helper.handleError(err), {
                        type: "error"
                    })

                })
        } catch (err) {
            msg && msg.show(Helper.handleError(err), {
                type: "error"
            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method prepares request data for login and handles API call and response for login.
     * CASES:-
     * 1.Account not verified- If user account not verified then user will be redirected to quick OTP page with event uuid
     * 2.Not participant- If user is not participant then user will be redirected to quick-user-info page with event uuid
     * 3.Account verified and is participant- User will be redirected to the dashboard page with event uuid
     *
     * On successful login access token and user basic data will be stored in local storage
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} data Data object which can container multiple fields data.
     * @param {String} data.email Email field value.
     * @param {String} data.password Password field value.
     * @method
     */
    const doLogin = (data) => {
        let postData = new FormData();
        Object.keys(data).map((keyName) => {
            postData.append(keyName, data[keyName])
        })
        postData.append('event_uuid', event_uuid)
        postData.append('lang', localStorage.getItem("current_lang") ? localStorage.getItem("current_lang") : 'EN');
        const accessCode = localStorage.getItem('accessCode');
        if (accessCode) {
            postData.append('access_code', accessCode);
        }

        setButtonState('loading');
        setButton(false)

        props.login(postData).then((response) => {
            setButtonState('');
            setButton(true)

            if (response.data.status) {
                const data = response.data.data

                localStorage.setItem('accessToken', response.data.data.access_token)
                localStorage.setItem('fname', response.data.data.fname)
                localStorage.setItem('lname', response.data.data.lname)
                localStorage.setItem('email', response.data.data.email);

                if (!data.validated) {
                    return msg.show(t("Account not varified"), {
                        type: "error", onClose: () => {
                            props.history.push({
                                pathname: `/quick-register/${event_uuid}`,
                                state: {
                                    formMode: true,
                                    fname: response.data.data.fname,
                                    lname: response.data.data.lname,
                                    email: response.data.data.email,
                                }
                            })
                        }
                    })
                } else if (!data.is_participant) {
                    msg.show(t("You are not part of this event"), {
                        type: "success",
                        onClose: () => {
                            props.history.push(`/quick-user-info/${event_uuid}`)
                        }
                    })

                } else {
                    setRedirect(true);
                }
                getUsersData();

            } else {
                msg.show(t("notification:something worng"), {
                    type: "error"
                })
            }
        }).catch((err) => {
            setButtonState('');
            setButton(true)
            msg && msg.show(Helper.handleError(err), {type: "error"})
        })
    }

    // redux form props
    let {handleSubmit, eventDetailsLoad} = props

    // conditional redirection to event dashboard page
    if (redirectToevent) {
        return <Navigate to={`/dashboard/${event_uuid}`} />
    } else if (eventDetailsLoad === false) {
        // till data is not loaded or in case of api response error
        return <Helper.pageLoading />
    }

    return loadingState ? <></> :
        (<div className="form-login">
                <AlertContainer ref={msg} {...Helper.alertOptions} />
                <div className="row">
                    <div className="resilience-login-form kct-login-form">
                        <form className="form-style2 clearfix" onSubmit={handleSubmit(doLogin)}>
                            <div className="col-md-12  form-group p-relative login-email">
                                <img src={require("../../../images/login-user-icon.png")}
                                     className="img-responsive field-img" />
                                <Field id="email" name="email" type="email" autocapitalize="none" placeholder={"Email"}
                                       validate={[Validation.required, Validation.email]} component={InputBox} />
                            </div>
                            <div className="col-md-12  form-group p-relative login-pass">
                                <img src={require("../../../images/login-secure-icon.png")}
                                     className="img-responsive field-img" />
                                <Field id="password" name="password" type={showPass ? "text" : "password"}
                                       placeholder={"******"}
                                       validate={[Validation.required]} component={InputBox} />
                                <div className="pass-toggle">
                                    {showPass ?
                                        <i aria-hidden="true" className="fa fa-eye-slash hide-pass" onClick={() => {
                                            setPass(false)
                                        }}></i>
                                        :
                                        <i aria-hidden="true" className="fa fa-eye show-pass" onClick={() => {
                                            setPass(true)
                                        }}></i>
                                    }
                                </div>
                            </div>
                            <div className="col-md-12 text-center uni-color-btn form-group">
                                <ProgressButton className="" controlled={buttonControl} type="submit"
                                                state={buttonState}>
                                    {t("Sign in")}
                                </ProgressButton>
                            </div>
                            <div className="col-md-12  pl-0 pr-0">
                                <div className="col-xs-12 col-sm-12 text-center">
                                    <div className="forget-pass">
                                        <NavLink to="/forget-password">{t("Forget Password")}</NavLink>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div className="col-md-12 ">
                            <div className="or-saprator-div">
                                <div className="or-saprator"><span>{t("Or")}</span></div>
                            </div>
                        </div>

                        <div className="col-md-12  text-center">
                            <div className="social-login-txt heading-color">
                                <p>{t("Please register")} </p>

                                <p>{t("For your first connection")}</p>
                            </div>
                            <div className="create-account-link heading-color d-inline w-100"><NavLink
                                className="heading-color" to={{
                                pathname: `/quick-register/${event_uuid}`,
                                state: {fromLogin: true}
                            }}> &gt;&gt; {t("Registration")} &lt;&lt; </NavLink></div>
                        </div>
                    </div>
                </div>
            </div>
        )
}

const mapDispatchToProps = (dispatch) => {
    return {
        getEventDetails: (id) => dispatch(authActions.Auth.getEventDetails(id)),
        setEventDetailsData: (data) => dispatch({type: KCT.AUTH.EVENT_DETAILS, payload: data}),
        login: (data) => dispatch(authActions.Auth.login(data)),
        getUsersData: () => dispatch(authActions.Auth.getUsersData()),
        setEventDesignSetting: (data) => dispatch({type: KCT.EVENT.SET_EVENT_DESIGN_SETTINGS, payload: data}),
        setEventGroupLogo: (data) => dispatch({type: KCT.EVENT.SET_EVENT_GROUP_LOGO, payload: data}),
        getBadge: (data) => dispatch(eventActions.Event.getBadge(data)),

    }
}

const mapStateToProps = (state) => {
    return {
        eventDetailsData: state.AuthReducer.eventDetailsData,
        eventDetailsLoad: state.AuthReducer.eventDetailsLoad,
        main_color: state.page_Customization.initData.graphics_data
    }
}
Login = reduxForm({
    form: 'user-logins', // a unique identifier for this form
    enableReinitialize: true,
})(Login)

Login = connect(
    mapStateToProps,
    mapDispatchToProps
)(Login);

export default Login