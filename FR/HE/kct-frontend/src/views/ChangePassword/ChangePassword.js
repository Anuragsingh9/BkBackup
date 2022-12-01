import React, {useRef, useState, useEffect} from 'react';
import ProgressButton from 'react-progress-button';
import MailIcon from "../../images/login-secure-icon.png";
import {connect} from 'react-redux';
import authActions from '../../redux/actions/authActions';
import {Field, reduxForm} from 'redux-form';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import Helper from '../../Helper';
import {useTranslation} from 'react-i18next';




/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the password entered by the user and handle the error.
 * Different types of validation for changing password are as follows-
 * 1. New password must be at least 5 characters in length
 * 2. Confirmation password must be same as new password
 * 3. New password cannot be same as Old password
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} values Values of the
 * @param {String} values.current_password Current password of user to send on server
 * @param {String} values.password New Password of user to send on server
 * @param {String} values.password_confirmation Confirmed New Password of user to send on server
 * @returns {Object} Object where keys indicate the field and on that key value will represent the error message
 * @method
 */
const validate = values => {
    const errors = {};
    const requiredFields = [

        'current_password',
        'password',
        'password_confirmation'
    ];
    requiredFields.forEach(field => {
        if (!values[field]) {
            errors[field] = 'Required';
        }
    });
    if (values['current_password'] && values['current_password'].length < 5) {
        errors['current_password'] = `password must be equle or greater than 6`
    }
    if (values['password'] && values['password'].length < 5) {
        errors['password'] = `password must be equle or greater than 6`
    }
    if (values['password_confirmation'] && values['password_confirmation'].length < 5) {
        errors['password_confirmation'] = `password must be equle or greater than 6`
    }
    if (values['password'] !== values['password_confirmation']) {
        errors['password_confirmation'] = `passwords must be same as new password `
    }
    if ((values['password'] && values['current_password']) && values['password'] === values['current_password']) {
        errors['password'] = ` new passwords must not be same as current password`
    }
    return errors;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component used for rendering input fields in UI.It accepts params like placeholder,
 * input,type,meta etc  and returns errors if any.
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
 * @description This component is responsible for rendering input fields in UI which helps user to change his/her
 * old password in HE(attendee) side.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {GraphicsData} props.graphics_data Graphics data response from props
 * @param {Function} props.changePassword Method to update the user password with api
 * @param {Boolean} props.anyTouched Variable to indicate if the fields have been modified or not to perform validation
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let ChangePassword = (props) => {
    const msg = useAlert();
    const {t} = useTranslation(['qss', 'notification']);
    //state handling for progress button
    const [buttonControl, setButton] = useState(true);
    const [buttonState, setButtonState] = useState('');

    // state handling for visibility(hide/show) of current password
    const [showCurrentPass, setCurrentPass] = useState(false);

    // state handling for visibility(hide/show) of new password
    const [showPass, setPass] = useState(false);

    // state handling for visibility(hide/show) of password confirmation
    const [confirmShow, setConfirm] = useState(false);


    // useEffect(() => {
    //     if (props.getOrganisation) {
    //         props.getOrganisation();
    //     }
    // })
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method prepares form data for sending request to change password on server by calling
     * changePassword(from props) method and handles the API response.
     * On successful API response,the user will be redirected to Quick login page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {object} data Data object which can container multiple fields data.
     * @param {string} data.password Password field data.
     * @param {string} data.password_confirmation Confirm password field data.
     */
    const passwordSubmit = (data) => {
        const postData = new FormData();
        Object.keys(data).map((keyName) => {
            // postData.append('email', email);
            // postData.append('identifier', key)
            postData.append(keyName, data[keyName])
        })
        setButtonState('loading');
        setButton(false)
        props.changePassword(postData)
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
                console.error("err", err.response.data)
                const {errors, message} = err.response.data;
                if (errors) {
                    Object.keys(errors).map((keys) => {
                        msg && msg.show(errors[keys], {type: "error"})
                    })
                } else {
                    msg && msg.show(message, {type: "error"})
                }
                setButtonState('');
                setButton(true);
            })
    }

    const {handleSubmit} = props;

    return (
        <div className="main-content-wrap">
            <div className="container">
                <div className="welcome-sign">

                    <div className="row" style={{"padding-top": "50px"}}>

                        <div className="col-lg-12 px-0 form-regi">
                            <div className="form-login">
                                <div className="row">
                                    <div className="resilience-login-form kct-login-form">
                                        <AlertContainer ref={msg} {...Helper.alertOptions} />
                                        <div className="col-xs-12 col-sm-12 mb-30">
                                            <form className="form-style2 reset-ac"
                                                onSubmit={handleSubmit(passwordSubmit)}>
                                                <div className="form-group p-relative pass-toggle-wrap">
                                                    <img src={MailIcon} className="img-responsive" />
                                                    <Field
                                                        id="current_password"
                                                        name="current_password"
                                                        type={showCurrentPass ? "text" : "password"}
                                                        placeholder={t("Current Password")}
                                                        // validate={[Validation.required]}
                                                        component={InputBox} />
                                                    <div className="pass-toggle">
                                                        <i aria-hidden="true"
                                                            onClick={() => {
                                                                setCurrentPass(!showCurrentPass)
                                                            }}
                                                            className={showCurrentPass
                                                                ? "fa fa-eye-slash hide-pass"
                                                                : "fa fa-eye show-pass"}>

                                                        </i>
                                                    </div>
                                                </div>
                                                <div className="form-group p-relative pass-toggle-wrap">
                                                    <img src={MailIcon} className="img-responsive" />
                                                    <Field id="password"
                                                        name="password"
                                                        type={showPass ? "text" : "password"}
                                                        placeholder={t("New Password")}
                                                        // validate={[Validation.required]}
                                                        component={InputBox} />
                                                    <div className="pass-toggle">
                                                        <i aria-hidden="true"
                                                            onClick={() => {
                                                                setPass(!showPass)
                                                            }}
                                                            className={showPass
                                                                ? "fa fa-eye-slash hide-pass"
                                                                : "fa fa-eye show-pass"}>
                                                        </i>
                                                    </div>
                                                </div>
                                                <div className="form-group p-relative pass-toggle-wrap">
                                                    <img src={MailIcon} className="img-responsive" />
                                                    <Field id="password_confirmation"
                                                        name="password_confirmation"
                                                        type={confirmShow ? "text" : "password"}
                                                        placeholder={t("Confirm Password")}
                                                        // validate={[Validation.required]}
                                                        component={InputBox} />
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
                                                    <ProgressButton className="" controlled={buttonControl}
                                                        type="submit" state={buttonState}>
                                                        {t("Submit")}
                                                    </ProgressButton>
                                                </div>
                                            </form>
                                        </div>
                                        <div className="col-md-12 text-center">
                                            {/* <div className="or-saprator-div">
                                        <div className="or-saprator"><span>Or</span></div>

                                    </div> */}
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
        changePassword: (data) => dispatch(authActions.Auth.changePassword(data)),
    }
}

ChangePassword = reduxForm({
    form: 'changepassword', // a unique identifier for this form
    validate,
    enableReinitialize: true,
})(ChangePassword)

// connect for redux connection as HOC
ChangePassword = connect(
    null,
    mapDispatchToProps
)(ChangePassword);

export default (ChangePassword);
