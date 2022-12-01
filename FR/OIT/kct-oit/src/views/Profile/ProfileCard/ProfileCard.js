import React, {useEffect, useState, useRef} from 'react';
import {connect} from 'react-redux';
import {useAlert} from 'react-alert'
import userAction from '../../../redux/action/apiAction/user';
import ImageEditing from './ImageEditing';
import CloseIcon from '../../Svg/closeIcon.js';
import {TextValidator, ValidatorForm} from 'react-material-ui-form-validator';
import EntityAutoComplete2 from '../../Common/EntityAutoComplete/EntityAutoComplete2';

import LoadingContainer from '../../Common/Loading/Loading';
import {confirmAlert} from 'react-confirm-alert';
import Helper from '../../../Helper'
import _ from 'lodash';
import {
    Grid,
    Button,
    FormControl,
    OutlinedInput, FormLabel, RadioGroup, FormControlLabel, Radio, Select, InputLabel,
} from '@material-ui/core';
import './ProfileCard.css';
import userReduxAction from '../../../redux/action/reduxAction/user';
import {useTranslation} from 'react-i18next';
import MuiPhoneNumber from 'material-ui-phone-number';
import FileObject from '../../../Models/FileObject';
import MenuItem from "@mui/material/MenuItem";
import LoadingSkeleton from '../../Common/Loading/LoadingSkeleton';
import TechnicalSettingSkeleton from '../../v4/Skeleton/TechnicalSettingSkeleton';
import ProfilePageSkeleton from '../../v4/Skeleton/ProfilePageSkeleton';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This page/component is used for viewing and modifying the details associated with a single user
 * present in the system.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props
 * @param {Number} props.id Current loggedIn user's ID.
 * @param {Function} props.updateProfileImage Function to update profile image.
 * @param {Function} props.updateUser Function to update user details.
 * @return {JSX.Element}
 */
const ProfileCard = (props) => {

    // const classes = useStyles();
    const alert = useAlert()
    const {t} = useTranslation(['profile', "confirm", "notification"])
    const [values, setValues] = React.useState({
        id: '',
        first_name: '',
        last_name: '',
        country_code: '',
        company: '',
        designation: '',
        email: '',
        phone: '',
        mobile: '',
        avatar: '',
        union: '',
        union_pos: '',
        showPassword: false,
        isOrganiser: '',
        oldUniId: '',
        internalId: '',
        login_count: '',
        gender: '',
        grade: ''
    });
    const ref = useRef("form");
    const [user_id, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [uniName, setUniName] = useState();
    const [companyId, setCompanyId] = useState(null);
    const [unionId, setUnionId] = useState(null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is for handling the modification done in input fields like fname , lname , company
     * name, union name and update their states in the form.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} prop Entered value by the user in order to update profile.
     */
    const handleChange = (prop) => (event) => {
        if (prop === 'first_name') {
            props.setTargetUserData({...props.targetUserData, fname: event.target.value});
        } else if (props === 'last_name') {
            props.setTargetUserData({...props.targetUserData, lname: event.target.value});
        }

        setValues({...values, [prop]: event.target.value});
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This is a common method for updating the state of mobile and phone number fields.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} prop It contains the value of entered phone number of the user
     */
    const handleNumber = (prop) => (value) => {
        setValues({...values, [prop]: value});
    };

    useEffect(() => {
        let user_badge = {};
        const data = localStorage.getItem('user_data');
        const userId = localStorage.getItem("userId")
        if (data) {
            user_badge = JSON.parse(data);
        }
        if (_.has(props, ['id'])) {
            getProfileData(props.id);
        } else if (_.has(user_badge, ['id'])) {
            getProfileData(user_badge.id);
        }

    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is for getting the initial data of user details (ex - user name, user email, company,
     * union, profile image , phone number) from server by using API call if response is successful then stores data
     * and update states and show in fields
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id LoggedIn user's ID
     */
    const getProfileData = (id) => {
        setUser(id);
        setLoading(true);
        try {
            props.getUserData(id).then((res) => {
                const {data} = res.data
                // if set password not done redirect to set- password page
                if (res.data.status == 403) {
                    Helper.replaceSetPassword(res.data.data)
                }
                props.setTargetUserData && props.setTargetUserData(data);
                setValues({
                    ...values,
                    id: data.id,
                    first_name: data.fname,
                    last_name: data.lname,
                    email: data.email,
                    isOrganiser: data.is_organiser ? data.is_organiser : 0,
                    avatar: data.avatar ? data.avatar : '',
                    country_code: data.phone_code ? data.phone_code : '',
                    designation: data.company !== null && _.has(data.company, ['position']) ? data.company.position : "",
                    company: data.company ? data.company : "",
                    phone: !_.isEmpty(data.phones) ? data.phones[0].country_code !== null
                        ? `${data.phones[0].country_code}${data.phones[0].number}`
                        : `${data.phones[0].number}` : '',
                    union: !_.isEmpty(data.unions) ? data.unions : '',
                    union_pos: !_.isEmpty(data.unions) ? data.unions[0].position : '',
                    mobile: !_.isEmpty(data.mobiles) ? data.mobiles[0].country_code !== null
                        ? `${data.mobiles[0].country_code}${data.mobiles[0].number}`
                        : `${data.mobiles[0].number}` : "",
                    oldUniId: !_.isEmpty(data.unions) ? data.unions[0].id : '',
                    internalId: data.internal_id,
                    login_count: data.login_count ? data.login_count : '',
                    gender: data.gender ? data.gender : '',
                    grade: data.grade ? data.grade : '',
                })
                setCompanyId(_.has(data.company) ? data.company.id : '')
                setUnionId(!_.isEmpty(data.unions) ? data.unions[0].id : '')
                setUniName(!_.isEmpty(data.unions) ? data.unions[0] : "")
                setLoading(false);
                if (data.is_organiser && data.is_organiser && _.has(data, ['is_self']) && data.is_self === 1) {
                    props.setUser(res.data.data)
                }

            }).catch((err) => {
                setLoading(false);
                alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            setLoading(false);
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for saving the image data on server and show in profile image field of users
     * profile page and update state value for image file
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {FileObject} file JavaScript file object
     */
    const saveImage = (file) => {
        const Id = values.id;
        const formData = new FormData()
        formData.append("_method", "POST");
        formData.append("user_id", Id)
        formData.append("field", "avatar");
        formData.append("avatar", file);
        try {
            props.updateProfileImage(formData).then((res) => {
                const data = res.data.data;
                setValues({
                    ...values,
                    avatar: data.avatar,
                })
                alert.show(t("notification:rec add 1"), {type: 'success'})
                if (values.isOrganiser && values.isOrganiser === 1) {
                    // props.setUser(res.data.data)
                }
                getProfileData(values.id)
            }).catch((err) => {
                alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method provides confirmation required for deleting the profile image. It renders a pop up which
     * have two options (confirm and cancel). Upon clicking on the confirm button it will trigger removeImage() method.
     * Upon clicking on the cancel button it hides the pop up.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const deleteConfirm = () => {
        confirmAlert({
            message: t('confirm:sure'),
            confirmLabel: t('confirm:confirm'),
            cancelLabel: t('confirm:cancel'),
            buttons: [
                {
                    label: t('confirm:yes'),
                    onClick: () => {
                        removeImage();
                    }
                },
                {
                    label: t('confirm:no'),
                    onClick: () => {
                        return null
                    }
                }
            ],

        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method invokes on confirm button and calls the API to delete  profile image and on
     * successful response it stores the data of avatar and updates state of image.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const removeImage = () => {
        const Id = values.id
        const formData = new FormData()
        formData.append("_method", "POST");
        formData.append("user_id", Id)
        formData.append("field", "avatar");
        formData.append("avatar", "");
        try {
            props.updateProfileImage(formData).then((res) => {
                const data = res.data.data;
                setValues({
                    ...values,

                    avatar: data.avatar,
                })
                alert.show(t("notification:rec add 1"), {type: 'success'})
                getProfileData(values.id)

            }).catch((err) => {
                alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * ------------------------------------------------------------------------------------------------------------------
     * @description This method is used for updating users basic profiles details like name, email, phone and mobile
     * number, company name and role, union name and role, and updates this data on server by post API call .
     * ------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const updateUser = () => {
        const data = {
            "_method": "PUT",
            "id": values.id,
            "fname": values.first_name,
            "lname": values.last_name,
            "email": values.email,
            "phone_code": '',
            "phone_number": values.phone,
            "mobile_code": "",
            "mobile_number": values.mobile,
            "company_id": companyId ? companyId : null,
            "company_name": values.company.long_name,
            "c_position": values.designation,
            "unions": [{
                "union_id": unionId ? unionId : null,
                "union_old_id": values.oldUniId ? values.oldUniId : null,
                "union_name": uniName.long_name,
                "position": values.union_pos
            }],
            "internal_id": values.internalId,
            'gender': values.gender,
            'grade': values.grade
        }

        try {
            props.updateUser(data).then((res) => {
                alert.show(t("notification:rec add 1"), {type: 'success'})

                if (values.isOrganiser && values.isOrganiser === 1) {
                    props.setUser(res.data.data)
                }
            }).catch((err) => {
                alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to invoke updateUser method on clicking the submit button and it also prevents
     * reloading of the page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e JavaScript event object
     */
    const handleSubmit = (e) => {
        e.preventDefault();
        updateUser();
    }
    ValidatorForm.addValidationRule('ismax', (value) => {
        if (value && value.length > 10) {
            return false;
        }
        return true;
    });
    ValidatorForm.addValidationRule('ismin', (value) => {
        if (value && value.length < 3) {
            return false;
        }
        return true;
    });
    var regex = /^[a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ_'\-’À-ÿ ]*$/i;
    ValidatorForm.addValidationRule('isChar', (value) => {
        if (value && !regex.test(value)) {
            return false;
        }
        return true;
    });

    var alpha = /^[[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ_'\- À-ÿ ]*$/i;
    ValidatorForm.addValidationRule('isAlpha', (value) => {
        if (value && !alpha.test(value)) {
            return false;
        }
        return true;
    });
    const gradeTypes = {executive: "Executive", manager: "Manager", employee: "Employee", other: "Other", };
    return (
        <>
            <LoadingSkeleton loading={loading} skeleton={<ProfilePageSkeleton />}>
                <div className="profileCardDiv">
                    <Grid container spacing={3}>
                        <Grid className="profileImgDiv" item xs={12} lg={12}>
                            <div className="ImgDivRelative">
                                < ><ImageEditing onSaveImage={saveImage} avatar={values.avatar}
                                    userTxt={Helper.nameProfile(values.first_name, values.last_name)} /></>
                                {values.avatar && <CloseIcon onClick={deleteConfirm}>remove</CloseIcon>}
                            </div>
                        </Grid>
                        <ValidatorForm ref={ref} onSubmit={handleSubmit} onError={errors => console.log(errors)}>
                            <FormControl variant="outlined">
                                {/* <form onSubmit={handleSubmit}> */}
                                <Grid item xs={12}>
                                    <Grid container spacing={3}>
                                        <Grid className="leftProfileInfo" item xs={6} lg={6}>
                                            <div className="inputFlexDiv">
                                                {/* <div className="leftLabel"><p>{t("First name")}:</p></div> */}
                                                <TextValidator
                                                    label={t("First Name")}
                                                    className={OutlinedInput}
                                                    value={Helper.jsUcfirst(values.first_name)}
                                                    onChange={handleChange('first_name')}
                                                    aria-describedby="outlined-first_name-helper-text"
                                                    validators={['required', 'isChar']}
                                                    errorMessages={[`${t('required')}`, `${t("ischar")}`]}
                                                    labelWidth={0}
                                                    variant="outlined"
                                                    margin="dense"
                                                />
                                            </div>
                                            <div className="inputFlexDiv AutoCompleteDiv">
                                                <EntityAutoComplete2
                                                    name={values.company}
                                                    type={1}
                                                    CompanyId={(id) => setCompanyId(id)}
                                                    selectName={(value) => setValues({
                                                        ...values,
                                                        company: {long_name: value}
                                                    })}
                                                />
                                            </div>
                                            <div className="inputFlexDiv">
                                                <TextValidator
                                                    // This condition restricts Pilot to modify the Email address of the
                                                    // User after the User has logged in once into the system and
                                                    // resetting their password.
                                                    disabled={values.login_count == 0 ? false : true}
                                                    label={t("Email")}
                                                    value={values.email}
                                                    onChange={handleChange('email')}
                                                    aria-describedby="outlined-email-helper-text"
                                                    inputProps={{
                                                        'aria-label': 'email',
                                                    }}
                                                    labelWidth={0}
                                                    validators={['required', 'isEmail']}
                                                    errorMessages={[`${t('required')}`, `${t('email is not correct')}`]}
                                                    variant="outlined"
                                                    margin="dense"
                                                />
                                            </div>
                                            <div className="inputFlexDiv AutoCompleteDiv">

                                                {/* <EntityAutoComplete
                                                    name={uniName && uniName !== undefined ? uniName : ""}
                                                    CompanyId={(id) => setUnionId(id)}
                                                    type={2}
                                                    selectName={(value) => setUniName({ long_name: value })}
                                                /> */}
                                                <EntityAutoComplete2
                                                    name={uniName && uniName !== undefined ? uniName : ""}
                                                    CompanyId={(id) => setUnionId(id)}
                                                    type={2}
                                                    selectName={(value) => setUniName({long_name: value})}
                                                />
                                            </div>
                                            <div className="inputFlexDiv internalIdDiv">
                                                {/* <div className="leftLabel"><p>Union:</p></div> */}
                                                <TextValidator
                                                    // disabled={true}
                                                    label={t("InternalId")}
                                                    name="internalId"
                                                    aria-describedby="outlined-email-helper-text"
                                                    inputProps={{
                                                        'aria-label': 'email',
                                                    }}
                                                    labelWidth={0}
                                                    onChange={handleChange('internalId')}
                                                    variant="outlined"
                                                    margin="dense"
                                                    value={values.internalId}
                                                    validators={['isAlpha', "ismax", "ismin"]}
                                                    errorMessages={[`${t('isAlpha')}`, `${t("ismax")}`, `${t("ismax")}`]}
                                                />
                                            </div>
                                        </Grid>
                                        <Grid className="rightProfileInfo" item xs={6} lg={6}>
                                            <div className="inputFlexDiv">
                                                {/* <div className="leftLabel"><p>{t("Last name")}:</p></div> */}
                                                <TextValidator
                                                    label={t("Last Name")}
                                                    value={Helper.jsUcfirst(values.last_name)}
                                                    onChange={handleChange('last_name')}
                                                    aria-describedby="outlined-last_name-helper-text"
                                                    // inputProps={{
                                                    // 'aria-label': 'first_name',
                                                    // }}
                                                    labelWidth={0}
                                                    validators={['required', 'isChar']}
                                                    errorMessages={[`${t('required')}`, `${t("ischar")}`]}
                                                    variant="outlined"
                                                    margin="dense"
                                                />
                                            </div>
                                            <div className="inputFlexDiv">
                                                {/* <div className="leftLabel"><p>{t("Designation")}:</p></div> */}
                                                <TextValidator
                                                    label={t("ComPos")}
                                                    value={Helper.jsUcfirst(values.designation)}
                                                    onChange={handleChange('designation')}
                                                    aria-describedby="outlined-last_name-helper-text"
                                                    // aria-describedby="outlined-designation-helper-text"
                                                    inputProps={{
                                                        'aria-label': 'designation',
                                                    }}
                                                    labelWidth={0}

                                                    variant="outlined"
                                                    margin="dense"
                                                />
                                            </div>
                                            <div className="inputFlexDiv">

                                                <div className="numberField">
                                                    <MuiPhoneNumber
                                                        name='phone'
                                                        defaultCountry={'fr'}
                                                        label="Phone"
                                                        variant="outlined"
                                                        onChange={handleNumber('phone')}
                                                        // onChange={(value, code) => handlePhone(value, code)} 
                                                        value={values.phone}

                                                    />
                                                </div>
                                            </div>
                                            <div className="inputFlexDiv">
                                                {/* <div className="leftLabel"><p>Union position :</p></div> */}
                                                <TextValidator
                                                    label={t("uniPos")}
                                                    value={Helper.jsUcfirst(values.union_pos)}
                                                    onChange={handleChange('union_pos')}
                                                    aria-describedby="outlined-email-helper-text"
                                                    inputProps={{
                                                        'aria-label': 'email',
                                                    }}
                                                    labelWidth={0}
                                                    // validators={['required', 'isEmail']}
                                                    // errorMessages={[`${t('required')}`, 'email is not correct']}
                                                    variant="outlined"
                                                    margin="dense"
                                                />
                                            </div>
                                            <div className="inputFlexDiv">
                                                <div className="numberField">
                                                    <MuiPhoneNumber
                                                        name='mobile'
                                                        defaultCountry={'fr'}
                                                        label="Mobile"
                                                        variant="outlined"
                                                        onChange={handleNumber('mobile')}
                                                        value={values.mobile}
                                                    />
                                                </div>
                                            </div>
                                            {/*//! This code can be used in future */}
                                            {/* <div className="selectGenderDiv">
                                                <FormLabel id="demo-row-radio-buttons-group-label">{t("gender")}</FormLabel>
                                                <RadioGroup
                                                    row
                                                    aria-labelledby="demo-row-radio-buttons-group-label"
                                                    name="row-radio-buttons-group"
                                                >
                                                    <FormControlLabel
                                                        value="male"
                                                        control={
                                                            <Radio
                                                                onChange={handleChange('gender')}
                                                                checked={values.gender === "male"}
                                                            />
                                                        }
                                                        label={t("male")}
                                                    />
                                                    <FormControlLabel
                                                        value="female"
                                                        control={
                                                            <Radio
                                                                onChange={handleChange('gender')}
                                                                checked={values.gender === "female"}
                                                            />
                                                        }
                                                        label={t("female")}
                                                    />
                                                    <FormControlLabel
                                                        value="other"
                                                        control={
                                                            <Radio
                                                                onChange={handleChange('gender')}
                                                                checked={values.gender === "other"}
                                                            />
                                                        } label={t("other")}
                                                    />
                                                </RadioGroup>
                                            </div> */}

                                            <div className="inputFlexDiv">
                                                <FormControl>
                                                    <InputLabel id="test-select-label">Grade</InputLabel>
                                                    <Select
                                                        displayEmpty
                                                        labelId="test-select-label"
                                                        id="simple-select"
                                                        label="Grade"
                                                        value={values.grade}
                                                        onChange={handleChange('grade')}
                                                        variant="outlined"
                                                        margin='dense'
                                                        renderValue={(selected) => {
                                                            if (selected.length === 0) {
                                                                return <em>Grade</em>;
                                                            }
                                                            return gradeTypes[selected];
                                                        }}
                                                    >
                                                        <MenuItem value={"executive"}>Executive</MenuItem>
                                                        <MenuItem value={"manager"}>Manager</MenuItem>
                                                        <MenuItem value={"employee"}>Employee</MenuItem>
                                                        <MenuItem value={"other"}>Other</MenuItem>
                                                    </Select>
                                                </FormControl>
                                            </div>
                                        </Grid>
                                    </Grid>
                                    <Grid className="profileButtonDiv" item xs={12} lg={12}>
                                        <Button type="submit" variant="contained" color="primary" className="updateBtn">
                                            {t("Update")}
                                        </Button>
                                    </Grid>
                                </Grid>
                                {/* </form> */}
                            </FormControl>
                        </ValidatorForm>
                    </Grid>
                </div>
            </LoadingSkeleton>
        </>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        getUserData: (id) => dispatch(userAction.getUserData(id)),
        updateUser: (data) => dispatch(userAction.updateUser(data)),
        updateProfileImage: (data) => dispatch(userAction.updateProfileImage(data)),
        setUser: (data) => dispatch(userReduxAction.setUserData(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        user_badge: state.Auth.userSelfData
    };
};
export default connect(mapStateToProps, mapDispatchToProps)(ProfileCard);