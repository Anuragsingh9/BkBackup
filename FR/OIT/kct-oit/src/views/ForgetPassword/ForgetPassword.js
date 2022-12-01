import React, {useState} from 'react'
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
import {Link} from 'react-router-dom';
import HeaderLogo from '../Header/HeaderLogo'
import {useSelector, useDispatch} from 'react-redux';
import userAction from '../../redux/action/apiAction/user';
import EmailIcon from '@material-ui/icons/Email';
import "./ForgetPassword.css";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the entered email
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the forget password values
 * @param {String} values.email User's email address
 * @returns {Object} Error object that contains error message.
 */
const validate = (values) => {
    const errors = {};
    var emailRegex = /^$|^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    const requiredFields = [
        'email',
    ];
    requiredFields.forEach(field => {
        if (!values[field]) {
            errors[field] = 'Required';
        }
    });
    if (values['email'] && !emailRegex.test(values['email'])) {
        errors['email'] = `invalid email address`
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
 * @param {Boolean} invalid Enter value is invalid
 * @param {Boolean} touched To indicate if the input box is touched or not
 * @param {String} error Error message from input box
 * @param {String} custom Custom number of input box
 * @return {JSX.Element}
 */
const renderTextField = (
    {input, value, label, defaultValue, meta: {invalid, touched, error}, ...custom},
) => {
    console.log('input', input, value, label, defaultValue)
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
 * @description This method is responsible for sending user's email in request to send a forget password reset link
 * to the user's email.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This prop object consist handle submit function for submit button.
 * @param {Function} props.handleSubmit Function to submit email for forget password.
 * @returns {JSX.Element}
 * @constructor
 */
const ForgetPassword = (props) => {
    const {handleSubmit} = props;
    const dispatch = useDispatch();
    const [email, setEmail] = useState('')
    const alert = useAlert();
    const [showMsg, setShowMsg] = useState(false)

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method handles the change in the state of input by handling the state(setEmail)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e JavaScript event object.
     */
    const handleChange = (e) => {
        setEmail(e.target.value)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the form data for sending password reset link and handles the API response.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onSubmit = () => {
        const formData = new FormData();
        formData.append("_method", "POST")
        formData.append("email", `${email}`)
        try {
            dispatch(userAction.forgetPassword(formData)).then((res) => {
                if (res) {
                    alert.show("Password reset link sent successfully", {type: 'success'})
                    setShowMsg(true)
                }
            }).catch((err) => {
                alert.show("This email is not registered into our system", {type: 'error'})
            })
        } catch (err) {
            alert.show("Somthing went wrong", {type: 'error'})
        }
    }
    return (
        <>
            <Grid container>
                <Grid xs={12} className="forget_pswd_div">
                    <Grid xs={6} md={5} lg={4}>
                        <HeaderLogo />
                        <h1>Forget Password</h1>
                        <p>Enter your registerd email</p>
                        {showMsg &&
                        <h2>
                            Password reset link sent. Please check your email to reset your password
                        </h2>
                        }
                        <form onSubmit={handleSubmit(onSubmit)}>
                            <Field
                                name="email"
                                placeholder="Email"
                                variant="outlined"
                                className="ThemeInputTag"
                                component={renderTextField}
                                onChange={handleChange}
                                value={email}
                                InputProps={{
                                    startAdornment: <EmailIcon />,
                                }}

                            />
                            <Button
                                type="submit"
                                variant="contained"
                                color="primary"
                                className="long_btn"
                            >Send Verification Email</Button>
                            <p>Go back to sign in page ? <spam><Link to="signin">Click here</Link></spam></p>
                        </form>
                    </Grid>
                </Grid>
            </Grid>
        </>
    )
}

export default reduxForm({
    form: 'forForm', // a unique identifier for this form
    validate,
})(ForgetPassword)