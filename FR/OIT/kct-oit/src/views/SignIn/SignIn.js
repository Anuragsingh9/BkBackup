import React, {useEffect, useState} from 'react';
import {Button, Grid, IconButton, InputAdornment, TextField} from '@material-ui/core';
import {useAlert} from 'react-alert';
import {Field, reduxForm} from 'redux-form';
import {Link} from 'react-router-dom';
import Visibility from '@material-ui/icons/Visibility';
import LockIcon from '@material-ui/icons/Lock';
import VisibilityOff from '@material-ui/icons/VisibilityOff';
import EmailIcon from '@material-ui/icons/Email';
import "./SignIn.css";
import {useDispatch} from 'react-redux';
import userAction from '../../redux/action/apiAction/user';
import HeaderLogo from '../Header/HeaderLogo'
import Helper from '../../Helper';
import userReduxAction from '../../redux/action/reduxAction/user'
import _ from "lodash";
import {useTranslation} from 'react-i18next';
import i18next from 'i18next';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To validate the entered email and password
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the forget password values
 * @param {String} values.email User's email address
 * @param {String} values.password User's password
 */
const validate = (values) => {
    const errors = {};
    var emailRegex = /^$|^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    const requiredFields = [
        'email',
        'password'
    ];
    requiredFields.forEach(field => {
        if (!values[field]) {
            errors[field] = `${i18next.t("auth:required")}`;
        }
    });
    if (values['email'] && !emailRegex.test(values['email'])) {
        //if entered email not matched regex pattern
        errors['email'] = `${i18next.t('auth:email invalid')}`
    }
    if (values['password'] && values['password'].length < 5) {
        //if entered password is not equal or greater then 5 letter
        errors['password'] = `${i18next.t("auth:password lenght match")}`
    }
    return errors;
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component used for rendering text field component in UI.
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
 * @description To handle the signing process of the user. It includes setting token,user data in the local storage
 * and according to response redirect user to another page.It also sets label data if present in response meta key.
 * <br>
 * If user is logging for the very first time then user will be redirected to set-password page to set the
 * password again else user will be redirected to dashboard
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.handleSubmit Function is used When user is submit the sign in form
 * @returns {JSX.Element}
 * @constructor
 */
const SignIn = (props) => {
    const alert = useAlert();
    const {handleSubmit, pristine, reset, submitting, initialize, accessMode} = props;
    const [showPassword, setShowPassword] = useState(false);
    const [inputData, setInputData] = useState({
        email: ' ',
        password: ' '
    })
    const handleClickShowPassword = () => setShowPassword(!showPassword);
    const handleMouseDownPassword = () => setShowPassword(!showPassword);
    const dispatch = useDispatch();
    //state to manage redirection url after render
    const [isDev, setIsDav] = useState(false)
    const urlName = window.location.host
    const acName = window.location.host.split('.', 1)
    // const alert = useAlert()
    // console.log("url", acName)
    const {t} = useTranslation(["auth"])
    useEffect(() => {
        let token = localStorage.getItem('oitToken');
        if (!_.isEmpty(token)) {
            let userData = localStorage.getItem("user_data");
            if (userData) {
                userData = JSON.parse(userData);
                props.history.push(`/${userData.current_group_key}/dashboard`)
            }
        }
        const newLoc = window.location.href.split('/oit/');
        const newLocation = newLoc[0];
        setIsDav(newLocation.includes(".seque.in"))
    });

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method handles the change in the state of inputs by handling the state(setInputData)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} prop prop is used to handle the sign in the user
     * @param {String} prop.email User's email address
     * @param {String} prop.password User's password
     */
    const handleChange = (prop) => (event) => {
        setInputData({...inputData, [prop]: event.target.value});
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the form data for login and handles the API response.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onSubmit = () => {
        const formData = new FormData();
        formData.append("_method", "POST")
        formData.append("email", inputData.email)
        formData.append("password", inputData.password)
        try {
            dispatch(userAction.login(formData)).then((res) => {
                console.log("ddddddddddddd", res.data.data);
                const data = res.data.data
                const meta = res.data.meta
                if (res.data.status) {
                    // setting the token and user data in local storage
                    if (data.access_token) localStorage.setItem("oitToken", data.access_token)
                    if (data.user_data) localStorage.setItem("user_data", JSON.stringify(data.user_data))
                    if (data.current_group) localStorage.setItem("Current_group_data", JSON.stringify(data.current_group))

                    if (urlName === "localhost:3000" || urlName === 'localhost:3001') {

                        let a=  document.createElement('a');
                        a.href=data.redirect_url;
                        if(data.redirect_url) {
                            window.location.replace(`${window.location.origin}${a.pathname}`)
                        }
                        // props.history.push(`/${res.data.data.current_group.group_key}/dashboard`)
                    } else {
                        if (data.redirect_url) window.location.replace(data.redirect_url)
                    }
                }
                if (meta) {
                    dispatch(userReduxAction.setLabelData(meta))
                }
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
            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    return (
        <>
            <Grid container>
                <Grid xs={12} className="signIn_div">
                    <Grid xs={6} md={4} lg={4}>
                        <HeaderLogo />
                        <h1>{t("signin")} {Helper.jsUcfirst(`${acName}`)}</h1>
                        <p>{urlName}</p>
                        <form onSubmit={handleSubmit(onSubmit)}>
                            <Field
                                name="email"
                                placeholder="Email"
                                variant="outlined"
                                className="ThemeInputTag"
                                component={renderTextField}
                                onChange={handleChange("email")}
                                value={inputData.email}
                                InputProps={{
                                    startAdornment: <EmailIcon />,
                                }}

                            />
                            <Field
                                name="password"
                                variant="outlined"
                                placeholder={t("password")}
                                className="ThemeInputTag"
                                component={renderTextField}
                                onChange={handleChange("password")}
                                type={showPassword ? "text" : "password"}
                                value={inputData.password}
                                InputProps={{ // <-- This is where the toggle button is added.
                                    startAdornment: <LockIcon />,
                                    endAdornment: (
                                        <InputAdornment position="end">
                                            <IconButton
                                                aria-label="toggle password visibility"
                                                onClick={handleClickShowPassword}
                                                onMouseDown={handleMouseDownPassword}
                                            >
                                                {showPassword ? <Visibility /> : <VisibilityOff />}
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
                            >{t("SIGN IN")}</Button>
                        </form>
                        <p>{t("forget")} ? <Link to="/forget-password" style={{color: "blue"}}>{t("getHelp")}
                        </Link></p>

                        <p>{t("createNewAc")}
                            <spam
                                onClick={() => window.open("https://" + `${process.env.REACT_APP_OIT_ENVIRONMENT === "PRODUCTION"
                                    ? "auth." : ""}` + process.env.REACT_APP_HO_HOSTNAME + "/signup", "_blank")}
                                style={{color: "blue", cursor: "pointer"}}>
                                <u> {t("Click here")}</u></spam>
                        </p>
                    </Grid>
                </Grid>
            </Grid>
        </>
    )
}

export default reduxForm({
    form: 'MaterialUiForm', // a unique identifier for this form
    validate,
})(SignIn)