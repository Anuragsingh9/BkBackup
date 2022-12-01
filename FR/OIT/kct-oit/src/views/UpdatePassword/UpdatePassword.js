import React, {useRef, useState} from 'react'
import Visibility from '@material-ui/icons/Visibility';
import LockIcon from '@material-ui/icons/Lock';
import VisibilityOff from '@material-ui/icons/VisibilityOff';
import Button from "@material-ui/core/Button";
import {FormControl, Grid, IconButton, InputAdornment} from '@material-ui/core';
import './UpdatePassword.css';
import {reactLocalStorage} from 'reactjs-localstorage';
import {useAlert} from 'react-alert'
import Helper from '../../Helper';
import {connect} from "react-redux";
import userAction from '../../redux/action/apiAction/user';
import {TextValidator, ValidatorForm} from 'react-material-ui-form-validator';
import {useTranslation} from 'react-i18next'
import _ from 'lodash';
import BreadcrumbsInput from "../v4/Common/Breadcrumbs/BreadcrumbsInput";
import Constants from "../../Constants";


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component/page allows users to modify password their user account. It is
 * accessed from the Header dropdown menu.
 * <br>
 * <br>
 * Users are required to enter their current password along with the new password twice in the respective input
 * fields to update the password.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Using props for redux action methods
 * @param {Function} props.updatePassword Function is used to update password
 * @returns {JSX.Element}
 * @constructor
 */
const UpdatePassword = (props) => {
    const [current, setCurrent] = useState('');
    const [newPass, setNewPass] = useState('');
    const [conPass, setConPass] = useState("");

    // Add these variables to your component to track the state for password values
    const [showPassword, setShowPassword] = useState(false);
    const [showNewPassword, setNewShowPassword] = useState(false);
    const [showConPassword, setShowConPassword] = useState(false);
    // this methods are for show and hide password
    const handleClickShowPassword = () => setShowPassword(!showPassword);
    const handleMouseDownPassword = () => setShowPassword(!showPassword);

    const alert = useAlert();
    const {t} = useTranslation(["auth", "notification"])
    //https://www.npmjs.com/package/react-material-ui-form-validator

    const ref = useRef("form")

    /**
     * ------------------------------------------------------------------------------------------------------------------
     * @description This method is for current password input field that updates the state and displays its latest
     * value.
     * ------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e  Javascript event object
     */
    const currentChange = (e) => {
        setCurrent(e.target.value)
    }

    /**
     * ------------------------------------------------------------------------------------------------------------------
     * @description This method is for new password input field that updates the state and displays its latest value.
     * ------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const newChange = (e) => {
        setNewPass(e.target.value)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is for confirm password input field that updates the state and displays its latest
     * value.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const confirmChange = (e) => {
        setConPass(e.target.value)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This updatePassword method is for updating password by fetching api and send data on server to
     * update with new updated password data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const updatePassword = () => {
        const id = reactLocalStorage.get("userId");
        const newpassword = conPass.toString()
        const currentPassword = current.toString();
        const confirmPassword = conPass.toString();
        const formData = new FormData();
        formData.append("_method", "POST");
        formData.append("user_id", id)
        formData.append("field", "password");
        formData.append("value", newpassword);
        formData.append("current_password", currentPassword);
        formData.append("password_confirmation", confirmPassword)
        try {
            props.updatePassword(formData)
                .then((res) => {
                    alert.show(t("notification:flash msg rec update 1"), {type: 'success'})
                    // window.location.reload(true);
                    setConPass('');
                    setCurrent('')
                    setNewPass('')
                    ValidatorForm.removeValidationRule('isPasswordPatternMatch');
                    ValidatorForm.removeValidationRule('isPasswordMatch');
                    ValidatorForm.removeValidationRule('required');

                })
                .catch((err) => {
                    if (_.has(err, ['response', 'status']) && err.response.status == 422) {
                        if (_.has(err.response.data.errors, ["current_password"])) {
                            alert.show(err.response.data.errors.current_password[0], {type: 'error'})
                        } else if (_.has(err.response.data.errors, ["password_confirmation"])) {
                            alert.show(err.response.data.errors.password_confirmation[0], {type: 'error'})
                        }
                    } else {
                        alert.show(Helper.handleError(err), {type: 'error'})
                    }
                })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for invoking updatePassword method upon clicking on the Change Password button
     * and prevents from reloading of the page
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleSubmit = (e) => {
        e.preventDefault()
        updatePassword()
    }
    // custom rule will have name 'isPasswordMatch'
    ValidatorForm.addValidationRule('isOldPasswordMatch', (val) => {
        if (current === val) {
            return false;
        }
        return true;
    });
    // custom rule will have name 'isPasswordMatch' 
    ValidatorForm.addValidationRule('isPasswordMatch', (val) => {
        if (val !== newPass) {
            return false;
        }
        return true;
    });
    // custom rule will have name 'isPasswordPatternMatch' 
    ValidatorForm.addValidationRule('isPasswordPatternMatch', (val) => {
        if (val.length < 6) {
            return false;
        }
        return true;
    });
    ValidatorForm.addValidationRule('isRequire', (val) => {
        if (val) {
            return true;
        }
        return false;
    });

    return (
        <>
            <BreadcrumbsInput
                links={[
                    // Constants.breadcrumbsOptions.GROUPS_LIST,
                    Constants.breadcrumbsOptions.GROUP_NAME,
                    Constants.breadcrumbsOptions.USERS_LIST,
                    Constants.breadcrumbsOptions.CHANGE_UPDATE_PASSWORD,
                ]}
            />
            <div className="from_middle">
                <Grid container>
                    <Grid className="h-centeredDiv" xs={6} md={4} lg={4}>
                        <ValidatorForm ref={ref} onSubmit={handleSubmit} onError={errors => console.log(errors)}>
                            <FormControl variant="outlined">
                                <TextValidator
                                    className="pswd-input-box"
                                    variant="outlined" id="filled-required"
                                    // label="Password"
                                    size="small"
                                    type={showPassword ? "text" : "password"}
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
                                    value={current}
                                    name="currentPassword"
                                    // component={RenderTextField}
                                    label={t("Current Password")}
                                    onChange={(e) => currentChange(e)}
                                    validators={['isRequire', 'isPasswordPatternMatch']}
                                    errorMessages={[`${t('required')}`, `${t('password invalid')}`]}
                                />
                                <TextValidator
                                    className="pswd-input-box"
                                    variant="outlined"
                                    // placeholder="Password"
                                    size="small"
                                    type={showNewPassword ? "text" : "password"}
                                    InputProps={{ // <-- This is where the toggle button is added.
                                        startAdornment: <LockIcon />,
                                        endAdornment: (
                                            <InputAdornment position="end">
                                                <IconButton
                                                    aria-label="toggle password visibility"
                                                    onClick={() => setNewShowPassword(!showNewPassword)}
                                                    onMouseDown={() => setNewShowPassword(!showNewPassword)}
                                                >
                                                    {showNewPassword ? <Visibility /> : <VisibilityOff />}
                                                </IconButton>
                                            </InputAdornment>
                                        )
                                    }}
                                    value={newPass}
                                    name="newPassword"
                                    // component={RenderTextField}
                                    label={t("New Password")}
                                    onChange={(e) => {
                                        newChange(e)
                                    }}
                                    validators={['isRequire', 'isPasswordPatternMatch', "isOldPasswordMatch"]}
                                    errorMessages={[`${t('required')}`, `${t('password invalid')}`, `${t("isOldPasswordMatch")}`]}
                                />
                                <TextValidator
                                    className="pswd-input-box"
                                    variant="outlined"
                                    type={showConPassword ? "text" : "password"}
                                    size="small"
                                    InputProps={{ // <-- This is where the toggle button is added.
                                        startAdornment: <LockIcon />,
                                        endAdornment: (
                                            <InputAdornment position="end">
                                                <IconButton
                                                    aria-label="toggle password visibility"
                                                    onClick={() => setShowConPassword(!showConPassword)}
                                                    onMouseDown={() => setShowConPassword(!showConPassword)}
                                                >
                                                    {showConPassword ? <Visibility /> : <VisibilityOff />}
                                                </IconButton>
                                            </InputAdornment>
                                        )
                                    }}
                                    value={conPass}
                                    name="confirmPassword"
                                    // component={RenderTextField}
                                    label={t("Confirm password")}
                                    onChange={(e) => {
                                        confirmChange(e)
                                    }}
                                    validators={['isPasswordMatch', 'isRequire']}
                                    errorMessages={[`${t('password mismatch')}`, `${t('required')}`]}
                                />
                                <div className="theme-btn">
                                    <Button variant="contained" color="primary"
                                            type="submit">{t("ChangePassword")}</Button>
                                </div>
                                {/* </form>     */}
                            </FormControl>
                        </ValidatorForm>
                    </Grid>
                </Grid>
            </div>
        </>

    )
}

const mapStateToProps = (state) => {
    return {};
};

const mapDispatchToProps = (dispatch) => {
    return {
        updatePassword: (data) => dispatch(userAction.updatePassword(data)),
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(UpdatePassword);