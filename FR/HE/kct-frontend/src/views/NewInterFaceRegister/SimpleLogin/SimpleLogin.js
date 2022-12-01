import React, {useEffect, useRef, useState} from 'react'
import '../Login/login.css'
import {NavLink, Navigate} from 'react-router-dom'
import {Field, reduxForm} from 'redux-form'
import Validation from '../../../functions/ReduxFromValidation';
import ProgressButton from 'react-progress-button';
import {connect} from 'react-redux'
import Helper from '../../../Helper';
import {Provider as AlertContainer,useAlert } from 'react-alert';
import authActions from '../../../redux/actions/authActions';
import {reactLocalStorage} from 'reactjs-localstorage';
import {KeepContact as KCT} from '../../../redux/types';
import {useTranslation} from 'react-i18next';
import routeAgent from "../../../agents/routeAgent";
import Constants from "../../../Constants";
import _ from 'lodash';
import eventActions from "../../../redux/actions/eventActions";

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
 * @description Component for Simple Login form and child Login page component. It gets rendered when user doesn't
 * have event uuid in url or access token in local storage.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getEventDetails [Dispatcher|API] Method used to get the event details from api
 * @param {Function} props.setEventDetailsData Props passed from parent component
 * @param {Function} props.login Api method to trigger the login api
 * @param {Function} props.getUsersData Api method to fetch the user details with token
 * @param {EventData} props.eventDetailsData To provide the event basic data
 * @param {Boolean} props.eventDetailsLoad To indicate if event details are fetched or not
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let SimpleLogin = (props) => {

    const {t} = useTranslation('qss')
    // Initialisation for message / alert ref to show alerts on success or error.
    const msg = useAlert();

    //button control and button state for progress button
    const [buttonControl, setButton] = useState(true);
    const [buttonState, setButtonState] = useState('');

    // eye button state for password show and hide
    const [showPass, setPass] = useState(false);
    // eye button state for confirm password show and hide
    const [redirectToevent, setRedirect] = useState(false);


    useEffect(() => {
        let token = localStorage.getItem('accessToken');
        if (!_.isEmpty(token)) {
            // setting this item so if token is invalid then axios will not redirect to /
            localStorage.setItem('ignore401', 'true');
            props.getBadge().then((res) => {
                localStorage.removeItem('ignore401');
                if (res.status === 200) {
                    props.history.push(routeAgent.routes.EVENT_LIST());
                }
            }).catch(err => {
                localStorage.removeItem('ignore401');
                if (err?.response?.status === 403 && err?.response.data?.redirect_code === 1002) {
                    // user is logged in but account is not verified
                    props.history.push({
                        pathname: routeAgent.routes.QUICK_REGISTER(err.response.data.last_event_uuid),
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
                }
            });

            // props.history.push(`/dashboard/${event_uuid}`);
        }
    }, []);
    // useEffect(() => {
    //   if(props.getOrganisation){
    //     props.getOrganisation();
    //   }
    // }, [])


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
     * If login is successful then and user account is verified then user will be redirected to the event list page
     * and if login is successful but account is not verified then user will be redirected to the quick-otp page.
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
        postData.append('lang', localStorage.getItem("current_lang") ? localStorage.getItem("current_lang") : 'EN');


        let lang = localStorage.getItem("current_lang") ? localStorage.getItem("current_lang") : 'EN';


        setButtonState('loading');
        setButton(false)

        props.login({...data, lang: lang}).then((response) => {
            setButtonState('');
            setButton(true)
            if (response.data.status) {
                const data = response.data.data;
                localStorage.setItem('accessToken', response.data.data.access_token)
                localStorage.setItem('fname', response.data.data.fname)
                localStorage.setItem('lname', response.data.data.lname)
                localStorage.setItem('email', response.data.data.email);
                let event_uuid = response.data.event_uuid
                if (!data.validated) { // checking if user's account is verified
                    msg && msg.show("Account not verified", {
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
                } else {
                    getUsersData();
                    setRedirect(true);
                }
            } else {
                msg.show(t("something_worng"), {
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
    let {handleSubmit} = props;

    // conditional redirection to event dashboard page
    if (redirectToevent && reactLocalStorage.get('accessToken')) {
        return <Navigate to={`/event-list`} />
    }

    return (
        <div className="container">
            <div className="welcome-sign">

                <h2 className="enter-note  heading-color">{t('Welcome')}</h2>

                <div className="row">
                    <div className="col-lg-12 mt-20">
                    </div>
                    <div className="col-lg-12 px-0 form-regi">
                        <div className="form-login">
                            <AlertContainer ref={msg} {...Helper.alertOptions} />


                            <div className="row">
                                <div className="resilience-login-form kct-login-form">
                                    <form className="form-style2 login-ac clearfix" onSubmit={handleSubmit(doLogin)}>
                                        <div className="col-md-12 form-group p-relative login-email">
                                            <img src={require("../../../images/login-user-icon.png")}
                                                 className="img-responsive field-img" />
                                            <Field id="email" name="email" type="email" autocapitalize="none"
                                                   placeholder={"Email"}
                                                   validate={[Validation.required, Validation.email]}
                                                   component={InputBox} />
                                        </div>
                                        <div className="col-md-12 form-group p-relative login-pass">
                                            <img src={require("../../../images/login-secure-icon.png")}
                                                 className="img-responsive field-img" />
                                            <Field id="password" name="password" type={showPass ? "text" : "password"}
                                                   placeholder={"******"}
                                                   validate={[Validation.required]} component={InputBox} />
                                            <div className="pass-toggle">
                                                {showPass ?
                                                    <i aria-hidden="true" className="fa fa-eye-slash hide-pass"
                                                       onClick={() => {
                                                           setPass(false)
                                                       }}></i>
                                                    :
                                                    <i aria-hidden="true" className="fa fa-eye show-pass"
                                                       onClick={() => {
                                                           setPass(true)
                                                       }}></i>
                                                }
                                            </div>
                                        </div>
                                        <div className="col-md-12 sign-btn text-center uni-color-btn form-group">
                                            <ProgressButton controlled={buttonControl} type="submit"
                                                            state={buttonState}>
                                                {t("Sign in")}
                                            </ProgressButton>
                                        </div>
                                        <div className="col-md-12 pl-0 pr-0">
                                            <div className="col-xs-12 col-sm-12 text-center">
                                                <div className="forget-pass font-14">
                                                    <NavLink to="/forget-password">{t("Forget Password")}</NavLink>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <div className="col-md-12 text-center">
                                        <div className="social-login-txt font-13">
                                        </div>
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
        getEventDetails: (id) => dispatch(authActions.Auth.getEventDetails(id)),
        setEventDetailsData: (data) => dispatch({type: KCT.AUTH.EVENT_DETAILS, payload: data}),
        login: (data) => dispatch(authActions.Auth.login(data)),
        getUsersData: () => dispatch(authActions.Auth.getUsersData()),
        getBadge: (data) => dispatch(eventActions.Event.getBadge(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        eventDetailsData: state.AuthReducer.eventDetailsData,
        eventDetailsLoad: state.AuthReducer.eventDetailsLoad
    }
}

SimpleLogin = reduxForm({
    form: 'user-logins', // a unique identifier for this form
    enableReinitialize: true,
})(SimpleLogin)

SimpleLogin = connect(
    mapStateToProps,
    mapDispatchToProps
)(SimpleLogin);


export default (SimpleLogin)