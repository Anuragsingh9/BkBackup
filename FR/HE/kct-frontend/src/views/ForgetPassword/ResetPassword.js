import React, {useRef, useState} from 'react';
import {useTranslation} from 'react-i18next';
import {connect} from 'react-redux';
import {Field, reduxForm} from 'redux-form';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import ProgressButton from 'react-progress-button';
import authActions from '../../redux/actions/authActions';
import MailIcon from "../../images/login-secure-icon.png";
import Validation from '../../functions/ReduxFromValidation';
import Helper from '../../Helper';
import {useParams} from "react-router-dom";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common structure for a input field to render input box for email on set password page. This
 * will take data(from parameter where it called) which is necessary to render relative text fields.
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
 * @description This component is used to change current password of an account(Event side).On this page user will
 * have to enter a password(new password) and repeat it in confirm password input box to change their password.
 * This page will open when user click on the link(sent from the forget password page on the entered email
 * address).
 * Once the user changed their current password(by clicking on submit button) it will redirect to login page.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {GraphicsData} props.graphics_data [State] This variable holds the current graphics data set in redux
 * @param {Function} props.handleSubmit To submit the user changes and update the event tags with server
 * @param {Function} props.resetPassword To trigger the api to reset the password
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let ResetPassword = (props) => {
    const msg = useAlert();
    const {t} = useTranslation(['qss', 'notification']);
    //state to manage controlled/uncontrolled state of a button 
    const [buttonControl, setButton] = useState(true);
    const [buttonState, setButtonState] = useState('');
    //eye button states for password and confirm password input fields
    const [showPass, setPass] = useState(false);
    const [confirmShow, setConfirm] = useState(false);

    const {email, key} = useParams();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to change the password for the current logged in account(HCT
     * event).Once the API executed successfully then it will redirect to login page.
     * New password length should be more then and equals to 6 characters.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data of the form entered by the user
     * @param {String} data.password Password entered by the user
     * @param {String} data.password_confirmation Confirmed password entered by the user to match with password
     **/
    const passwordSubmit = (data) => {
        const postData = new FormData();
        postData.append('email', email);
        postData.append('identifier', key)
        Object.keys(data).map((keyName) => {
            postData.append(keyName, data[keyName])
        })
        setButtonState('loading');
        setButton(false)
        props.resetPassword(postData)
            .then((res) => {
                if (res.data.status) {
                    msg.show(t("notification:rec add 1"), {
                        type: "success", onClose: () => {
                            props.history && props.history.push("/quick-login");
                        }
                    });
                    setButtonState('');
                    setButton(true)
                }
            })
            .catch((err) => {
                setButtonState('');
                setButton(true);
                msg && msg.show(Helper.handleError(err), {type: "error"})
            })
    }

    const {handleSubmit} = props;

    return (
        <div className="main-content-wrap">
            <div className="container">
                <div className="welcome-sign">
                    <h2 className="enter-note heading-color">{t('Welcome')}</h2>
                    <div className="row">
                        <div className="col-lg-12 mt-20">
                        </div>
                        <div className="col-lg-12 px-0 form-regi">
                            <div className="form-login">
                                <div className="row">
                                    <div className="resilience-login-form kct-login-form">
                                        <AlertContainer ref={msg} {...Helper.alertOptions} />
                                        <div className="col-xs-12 col-sm-12 mb-30">
                                            <form className="form-style2 reset-ac"
                                                  onSubmit={handleSubmit(passwordSubmit)}>
                                                <h4 className="mb-30 heading-color text-center">
                                                    {t("Set Password")}
                                                </h4>
                                                <div className="form-group p-relative pass-toggle-wrap">
                                                    <img src={MailIcon} className="img-responsive" />
                                                    <Field
                                                        id="password"
                                                        name="password"
                                                        type={showPass ? "text" : "password"}
                                                        placeholder={t("Password")}
                                                        validate={[Validation.required]}
                                                        component={InputBox}
                                                    />
                                                    <div className="pass-toggle">
                                                        <i aria-hidden="true"
                                                           onClick={() => {
                                                               setPass(!showPass)
                                                           }}
                                                           className={showPass
                                                               ? "fa fa-eye-slash hide-pass"
                                                               : "fa fa-eye show-pass"}
                                                        >
                                                        </i>
                                                    </div>
                                                </div>
                                                <div className="form-group p-relative pass-toggle-wrap">
                                                    <img src={MailIcon} className="img-responsive" />
                                                    <Field
                                                        id="password_confirmation"
                                                        name="password_confirmation"
                                                        type={confirmShow ? "text" : "password"}
                                                        placeholder={t("Confirm Password")}
                                                        validate={[Validation.required]}
                                                        component={InputBox}
                                                    />
                                                    <div className="pass-toggle">
                                                        <i aria-hidden="true"
                                                           onClick={() => {
                                                               setConfirm(!confirmShow)
                                                           }}
                                                           className={confirmShow
                                                               ? "fa fa-eye-slash hide-pass"
                                                               : "fa fa-eye show-pass"}>
                                                        </i>
                                                    </div>
                                                </div>
                                                <div className="mb-20 uni-color-btn text-center">
                                                    <ProgressButton
                                                        className=""
                                                        controlled={buttonControl}
                                                        type="submit"
                                                        state={buttonState}
                                                    >
                                                        {t("Submit")}
                                                    </ProgressButton>
                                                </div>
                                            </form>
                                        </div>
                                        <div className="col-md-12 text-center">
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
        resetPassword: (data) => dispatch(authActions.Auth.resetPassword(data)),
    }
}
ResetPassword = reduxForm({
    form: 'registerBasic', // a unique identifier for this form
    enableReinitialize: true,
})(ResetPassword)

// connect for redux connection as HOC
ResetPassword = connect(
    null,
    mapDispatchToProps
)(ResetPassword);

export default (ResetPassword);
