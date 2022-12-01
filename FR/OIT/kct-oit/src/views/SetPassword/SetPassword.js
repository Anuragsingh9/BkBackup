import React, {useState} from 'react';
import {Button, Grid, IconButton, InputAdornment, TextField} from '@material-ui/core';
import {useAlert} from 'react-alert';
import {Field, reduxForm} from 'redux-form';
import Visibility from '@material-ui/icons/Visibility';
import LockIcon from '@material-ui/icons/Lock';
import VisibilityOff from '@material-ui/icons/VisibilityOff';
import {useDispatch} from 'react-redux';
import userAction from '../../redux/action/apiAction/user';
import Helper from '../../Helper'
import './SetPassword.css';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will validate entered password by the user and handles the error.
 * <br>
 * 1. New password must be at least 5 characters length
 * 2. Confirmation password must be same as new password
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} values Validate the set password values
 * @param {String} values.newPassword To new password
 * @param {String} values.confirmPassword Confirm new password
 * @returns {{errors:Object}}
 */
const validate = (values) => {
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
        errors['newPassword'] = `password must be equle or greater than 6`
    }
    if (values['confirmPassword'] && values['confirmPassword'].length < 5) {
        errors['confirmPassword'] = `password must be equle or greater than 6`
    }

    if (values['newPassword'] !== values['confirmPassword']) {
        errors['confirmPassword'] = `The password should not be the same as old`
    }
    return errors;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a redux's common component used for rendering input text field in UI.
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
 * @description This is a child component which displays the set password form consisting of new password, confirm
 * password input fields and a submit button.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.handleSubmit Function is used to submit form
 * @param {Function} props.initialize Function is used to initialize the input data
 * @returns {JSX.Element}
 * @constructor
 */
const SetPassword = (props) => {
    const {handleSubmit, pristine, reset, submitting, initialize, accessMode} = props;
    const [inputData, setInputData] = useState({
        newPassword: '',
        confirmPassword: ''
    })

    // state for handling visibility of password in input field
    const [showPassword, setShowPassword] = useState(false);
    // state for handling visibility of confirmation password in input field
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const handleClickShowNewPassword = () => setShowPassword(!showPassword);
    const handleMouseDownNewPassword = () => setShowPassword(!showPassword);
    const handleClickShowConfirmPassword = () => setShowConfirmPassword(!showConfirmPassword);
    const handleMouseDownConfirmPassword = () => setShowConfirmPassword(!showConfirmPassword);
    const dispatch = useDispatch();
    const alert = useAlert()

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will handle the change in the input field and accordingly updates the input value by
     * setting the state(setInputData).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} prop prop is used to handle new password and confirm password
     * @param {String} prop.newPassword New password is used to set new password
     * @param {String} prop.confirmPassword confirm password is used to confirm the user new password
     * @returns {Function}
     */
    const handleChange = (prop) => (event) => {
        setInputData({...inputData, [prop]: event.target.value});
    };

    const userData = JSON.parse(localStorage.user_data)

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the form data for sending the request to set new password and handles the API response.
     * <br>
     * After successful response user will be redirected to dashboard page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onSubmit = () => {
        const userEmail = userData.email
        if (userEmail) {
            const data = {
                "_method": "POST",
                email: userEmail,
                password: inputData.newPassword,
                password_confirmation: inputData.confirmPassword
            }
            try {
                dispatch(userAction.setPassword(data)).then((res) => {
                    setInputData({
                        newPassword: "",
                        confirmPassword: ""
                    })
                    alert.show(res.data.data, {type: "success"})
                    let data = localStorage.getItem('user_data');
                    if (data) {
                        let userData = JSON.parse(data);
                        let key = userData.current_group_key;
                        window.location.replace(`/oit/${key}/dashboard`)
                    }
                    initialize(inputData)
                }).catch((err) => {
                    const error = err.response.data.errors
                    const errMsg = err.response.data.message
                    if (err.response.data) {
                        if (error) {
                            for (let key in error) {
                                alert.show(error[key], {type: 'error'})
                            }
                        } else {
                            alert.show(errMsg, {type: 'error'})
                        }
                    } else {
                        alert.show(Helper.handleError(err), {type: 'error'})
                    }
                    console.log(err)
                })
            } catch (err) {
                alert.show("somthing went wrong", {type: "error"})
            }
        }
    }
    return (
        <div className="pswd_div">
            <Grid xs={4}>
                <form onSubmit={handleSubmit(onSubmit)}>
                    <Grid>
                        <Field
                            name="newPassword"
                            variant="outlined"
                            placeholder="New Password"
                            className="ThemeInputTag ThemeInputTag_setpswd"
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
                    </Grid>
                    <Grid>
                        <Field
                            name="confirmPassword"
                            variant="outlined"
                            placeholder="Confirm Password"
                            className="ThemeInputTag ThemeInputTag_setpswd"
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
                    </Grid>
                    <Grid>
                        <Grid>
                            <Button
                                type="submit"
                                variant="contained"
                                color="primary"
                                className="long_btn"
                            >Set Password</Button>
                        </Grid>
                    </Grid>
                </form>
            </Grid>
        </div>
    )
}
export default reduxForm({
    form: 'SetPasswordForm', // a unique identifier for this form
    validate,
})(SetPassword)