import React, {useRef, useState} from 'react';
import Validation from '../../functions/ReduxFromValidation';
import authActions from '../../redux/actions/authActions';
import ProgressButton from 'react-progress-button';
import MailIcon from "../../images/mail-icon.png";
import {Field, reduxForm} from 'redux-form';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import {connect} from 'react-redux';
import Helper from '../../Helper';
import './ForgetPassword.css';
import {useTranslation} from 'react-i18next';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common structure for a input field to render input box for email on forget password page. This
 * will take data(from parameter where it called) which is necessary to render relative text fields.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {String} placeholder The placeholder value for the empty value field
 * @param {Object} input Redux form passed state
 * @param {String} input.name Redux form field name
 * @param {Boolean} touched To indicate if the field is focused
 * @param {String} error The error string message for the respective field
 * @param {String} warning The warning message to show if any with respective field
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const InputBox = ({placeholder, input, meta: {touched, error, warning}}) => {
    return (
        <>
            <input {...input} placeholder={placeholder} className="form-control" />
            {touched &&
            ((error && <span className="text-danger">{error}</span>) ||
                (warning && <span>{warning}</span>))}
        </>
    )
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to recover forgotten password using link(sent to registered email address,
 * once the user enter their email in the email input box and click on submit button).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {GraphicsData} props.graphics_data Graphics data response from props
 * @param {Function} props.handleSubmit To submit the user changes and update the event tags with server
 * @param {Function} props.sendForgetPasswordLink To send the forget password link to respective email
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let ForgetPassword = (props) => {
    //state to manage controlled/uncontrolled state of a button 
    const [buttonControl, setButton] = useState(true);
    const [buttonState, setButtonState] = useState('');
    const [showMsg, setShowMsg] = useState(false)
    const msg = useAlert()
    const {t} = useTranslation(["qss", "notification"])


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will send a forget password link on the entered email address(On forget password page)
     * by the user.
     * Sent email will contain a link "click here" by which user can go to set password page to recover their password.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data to send to backend for forget password procedure
     * @param {String} data.email Email of user who forgot the password
     */
    const emailSubmit = (data) => {
        const postData = new FormData();
        Object.keys(data).map((keyName) => {
            postData.append(keyName, data[keyName])
        })
        postData.append('lang', localStorage.getItem("current_lang") ? localStorage.getItem("current_lang") : 'EN');
        setButton(false);
        setButtonState('loading');
        props.sendForgetPasswordLink(postData).then((res) => {
            if (res.data.status) {
                msg.show(t("notification:success message"), {
                    type: "success",

                });
                setButton(true);
                setShowMsg(true)
                setButtonState('');
            } else {
                msg.show(t("notification:something worng"), {
                    type: "success",

                });
                setButton(true);
                setButtonState('');
            }
        })
            .catch((err) => {
                setButton(true);
                setButtonState('');
                // msg && msg.show(Helper.handleError(err.response.data.message), {type: "error"})
                msg && msg.show(err.response.data.message, {type: "error"})
            })
    }
    const {handleSubmit} = props
    return (
        <div className="container">
            <div className="welcome-sign">
                <h2 className="enter-note heading-color">{t("Welcome")}</h2>
                <div className="row">
                    <div className="resilience-login-form kct-login-form">
                        <div className="">
                            <AlertContainer ref={msg} {...Helper.alertOptions} />
                            <div className="col-md-12">
                                <div className="classic-margin">
                                    <form className="form-style2" onSubmit={handleSubmit(emailSubmit)}>
                                        <h4 className="mb-30 heading-color text-center">{t("Forget Password")}</h4>
                                        {showMsg &&
                                        <h4 className="enter-note heading-color">
                                            {t("reset link")}
                                        </h4>
                                        }
                                        <div className="form-group forgot-ac p-relative input-icon">
                                            <img src={MailIcon} className="img-responsive" />
                                            <Field
                                                id="email"
                                                name="email"
                                                type="text"
                                                autocapitalize="none"
                                                placeholder={"Email"}
                                                validate={[Validation.required, Validation.email]}
                                                component={InputBox}
                                            />
                                        </div>
                                        <div className="text-center forgot-pbtn uni-color-btn mb-30 mt-30">
                                            <ProgressButton
                                                controlled={buttonControl}
                                                type="submit"
                                                state={buttonState}
                                            >
                                                {t("Submit")}
                                            </ProgressButton>
                                        </div>
                                    </form>
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
        sendForgetPasswordLink: (data) => dispatch(authActions.Auth.sendForgetPasswordLink(data)),
    }
}
ForgetPassword = reduxForm({
    form: 'registerBasic', // a unique identifier for this form
    enableReinitialize: true,
})(ForgetPassword)

// connect for redux connection as HOC
ForgetPassword = connect(
    null,
    mapDispatchToProps
)(ForgetPassword);

export default ForgetPassword;