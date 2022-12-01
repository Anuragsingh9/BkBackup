import React, {useState, useEffect} from 'react';
import {
    Grid,
    TextField,
    Button,
    InputAdornment,
    InputLabel,
    NativeSelect,
    Checkbox,
    IconButton
} from '@material-ui/core';
import {useAlert, withAlert} from 'react-alert';
import {Field, reduxForm} from 'redux-form';
import _ from 'lodash';
import Visibility from '@material-ui/icons/Visibility';
import LockIcon from '@material-ui/icons/Lock';
import VisibilityOff from '@material-ui/icons/VisibilityOff';
import {useSelector, useDispatch} from 'react-redux';
import { useLocation } from "react-router-dom"
import userAction from '../../redux/action/apiAction/user';
import HeaderLogo from '../Header/HeaderLogo'
import Helper from '../../Helper';
import './ResetPassword.css';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the entered new password and password confirmation field <br/>
 * 1. New password must be equal or greater than 6
 * 2. Password confirmation should be same as new password
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the reset password values
 * @param {String} values.newPassword To new password
 * @param {String} values.confirmPassword Confirm new password
 * @return errors
 */
const validate = (values) => {
    console.log('values', values)
    const errors = {};
    const requiredFields = [
        'newPassword',
        'confirmPassword'
    ];
    requiredFields.forEach(field => {
        if (!values[field]) {
            errors[field] = 'Required';
        }
    });

    if (values['newPassword'] && values['newPassword'].length < 5) {
        errors['newPassword'] = `password must be equal or greater than 6`
    }
    if (values['confirmPassword'] && values['confirmPassword'].length < 5) {
        errors['confirmPassword'] = `password must be equal or greater than 6`
    }

    if (values['newPassword'] !== values['confirmPassword']) {
        errors['confirmPassword'] = `passwords must be same`
    }
    return errors;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is common component that returns the text field component in UI.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {String} value Value of the input box
 * @param {String} label Label of text field
 * @param {String} defaultValue Default value of input box
 * @param {Object} invalid Enter value is invalid
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message from input box
 * @param {Object} custom Custom number of input box
 * @return {JSX.Element}
 */
const renderTextField = (
    {input, value, label, defaultValue, meta: {invalid, touched, error}, ...custom},
) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={value}
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error && invalid}

                {...input}
                {...custom}
            />
            {touched && error && <span className={'text-danger'}>{error}</span>}
        </React.Fragment>
    );
}

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is responsible for preparing the data for creating new password and sending the data to
 * API through request.After creating new password user will be redirected to SignIn page.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This prop object consist handle submit function for submit button.
 * @param {Function} props.handleSubmit Function to submit email for reset password.
 * @param {Function} props.initialize Function to show initial data in the fields.
 * @returns {JSX.Element}
 */
const ResetPassword = (props) => {
    const {handleSubmit, pristine, reset, submitting, initialize, accessMode} = props;
    const [inputData, setInputData] = useState({
        newPassword: '',
        confirmPassword: ''
    })
    // state handling for visibility(hide/show) of password
    const [showPassword, setShowPassword] = useState(false);
    // state handling for visibility(hide/show) of password
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const handleClickShowNewPassword = () => setShowPassword(!showPassword);
    const handleMouseDownNewPassword = () => setShowPassword(!showPassword);
    const handleClickShowConfirmPassword = () => setShowConfirmPassword(!showConfirmPassword);
    const handleMouseDownConfirmPassword = () => setShowConfirmPassword(!showConfirmPassword);
    const dispatch = useDispatch();
    const alert = useAlert()

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method handles the change in the state of inputs by handling the state(setInputData)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} prop Input password value by the user
     */
    const handleChange = (prop) => (event) => {
        setInputData({...inputData, [prop]: event.target.value});
    };

    // extracting the query string included in the url
    const search = useLocation().search;
    const email = new URLSearchParams(search).get('email'); // extracting email from url
    const identifier = new URLSearchParams(search).get('i'); // extracting identifier from url

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the form data for creating new password and handles the API response.
     * After creating the new password user will be redirected to SignIn page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onSubmit = () => {

        const data = {
            "_method": "POST",
            "email": email,
            "password": inputData.newPassword,
            "password_confirmation": inputData.confirmPassword,
            "identifier": identifier
        }
        try {
            dispatch(userAction.resetPassword(data)).then((res) => {
                alert.show("Record updated succesfully", { type: "success" })
                props.history.push("/signin")
                setInputData({
                    newPassword: "",
                    confirmPassword: ""
                })
                // props.history.push("/signin")
                initialize(inputData)
            }).catch((err) => {
                const error = err.response.data.errors
                const errMsg = err.response.data.message
                if (err.response.data) {
                    if (error) {
                        for (let key in error) {

                            alert.show(error[key], { type: 'error' })
                        }
                    } else {
                        alert.show(errMsg, { type: 'error' })
                    }
                } else {
                    alert.show(Helper.handleError(err), { type: 'error' })
                }
            })
        } catch (err) {
            alert.show(Helper.handleError(err), { type: 'error' })

        }

    }
    return (
        <>
            <Grid container>
                <Grid xs={12} className="rePswd_div">
                    <Grid xs={6} md={4} lg={4}>
                        <HeaderLogo />
                        <div className="from-heading">
                            <h1>Reset Password</h1>
                        </div>
                        <form onSubmit={handleSubmit(onSubmit)}>
                            <Field
                                name="newPassword"
                                variant="outlined"
                                placeholder="New Password"
                                className="ThemeInputTag"
                                component={renderTextField}
                                onChange={handleChange("newPassword")}
                                type={showPassword ? "text" : "password"}
                                value={inputData.newPassword}
                                InputProps={{ // <-- This is where the toggle button is added.
                                    startAdornment: <LockIcon />,
                                    endAdornment: (
                                        <InputAdornment position="end">
                                            <IconButton
                                                aria-label="toggle password visibility"
                                                onClick={handleClickShowNewPassword}
                                                onMouseDown={handleMouseDownNewPassword}
                                            >
                                                {showPassword ? <Visibility /> : <VisibilityOff />}
                                            </IconButton>
                                        </InputAdornment>
                                    )
                                }}
                            />
                            <Field
                                name="confirmPassword"
                                variant="outlined"
                                placeholder="Confirm Password"
                                className="ThemeInputTag"
                                component={renderTextField}
                                onChange={handleChange("confirmPassword")}
                                type={showConfirmPassword ? "text" : "password"}
                                value={inputData.confirmPassword}
                                InputProps={{ // <-- This is where the toggle button is added.
                                    startAdornment: <LockIcon />,
                                    endAdornment: (
                                        <InputAdornment position="end">
                                            <IconButton
                                                aria-label="toggle password visibility"
                                                onClick={handleClickShowConfirmPassword}
                                                onMouseDown={handleMouseDownConfirmPassword}
                                            >
                                                {showConfirmPassword ? <Visibility /> : <VisibilityOff />}
                                            </IconButton>
                                        </InputAdornment>
                                    )
                                }}
                            />
                            <Button
                                type="submit"
                                variant="contained"
                                color="primary"
                                className="long_btn"
                            >Reset Password</Button>
                        </form>
                    </Grid>
                </Grid>
            </Grid>
        </>
    )
}
export default reduxForm({
    form: 'ResetPasswordForm', // a unique identifier for this form
    validate,
})(ResetPassword)