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
import Validation from '../../../functions/ReduxFromValidation'
import i18next from 'i18next';
import _ from 'lodash';
import {
    Container,
    Grid,
    Avatar,
    Button,
    FormControl,
    OutlinedInput,
    FormHelperText,
    InputAdornment,
    TextField
} from '@material-ui/core';
import './ProfileCard.css';
import LoggedinProfile from "../../../images/badge-host.png";
import userReduxAction from '../../../redux/action/reduxAction/user';
import {useTranslation} from 'react-i18next';
import MuiPhoneNumber from 'material-ui-phone-number';
import {
    Field,
    reduxForm,

} from "redux-form";
import LoadingSkeleton from '../../Common/Loading/LoadingSkeleton';
import ProfilePageSkeleton from '../../v4/Skeleton/ProfilePageSkeleton';


/**
 * @deprecated
 */
const validate = (values) => {
    const errors = {};
    const requiredFields = [
        'fname',
        'lname',
    ];
    var regex = /^[a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ_'\-’À-ÿ ]*$/i;
    var alpha = /^[[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ_'\- À-ÿ ]*$/i;

    requiredFields.forEach(field => {
        if (!values[field]) {
            errors[field] = 'Required';
        }
        if (values[field] && !regex.test(values[field])) {
            errors[field] = `${i18next.t("profile:ischar")}`;
        }
    });
    if (values['internal_id'] && values['internal_id'].length > 10) {
        errors['internal_id'] = `${i18next.t("profile:ismax")}`
    }
    if (values['internal_id'] && 3 > values['internal_id'].length) {
        errors['internal_id'] = `${i18next.t("profile:ismax")}`
    }
    if (values['internal_id'] && !alpha.test(values['internal_id'])) {
        errors['internal_id'] = `${i18next.t("profile:isAlpha")}`
    }
    // console.log("vv",values['id'] && values['id'].length)
    return errors;
}
const renderTextField = (
    {input, label, value, defaultValue, meta: {touched, error}, ...custom},
) => {
    return (
        <React.Fragment>
            <div className="inputFlexDiv">
                <TextField
                    name={input.name}
                    label={label}
                    value={value}
                    // defaultValue={defaultValue}
                    onChange={input.onChange}
                    errorText={touched && error}
                    error={touched && error}

                    {...input}
                    {...custom}
                />

                {touched && error && <span className={'text-danger'}>{error}</span>}
            </div>
        </React.Fragment>
    );
}

/**
 * @class
 * @component
 * @deprecated
 *
 * -----------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying User Profiles details in ProfileContainer.
 * -----------------------------------------------------------------------------------------------
 */
var ProfileCard1 = (props) => {
    // const classes = useStyles();
    const {handleSubmit, pristine, reset, submitting, initialize} = props;
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
        internalId: ''

    });
    const ref = useRef("form");
    const [user_id, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [uniName, setUniName] = useState();
    const [companyId, setCompanyId] = useState(null);
    const [unionId, setUnionId] = useState(null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description  handle the changing values in details field
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} prop Browser Event
     */
    const handleChange = (prop) => (event) => {
        setValues({...values, [prop]: event.target.value});
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description  Handle the changing values in details field for numbers
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} prop It contains the value of entered phone number of the user
     */
    const handleNumber = (prop) => (value, code) => {
        setValues({...values, [prop]: value});
    };

    const handlePhone = (value, code) => {
        // console.log("pho", value, code.dialCode)
    }

    const handleMouseDownPassword = (event) => {
        event.preventDefault();
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
     * @description It fetch the initial data of user details and show in fields
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getProfileData = (id) => {
        setUser(id);
        setLoading(true);
        try {
            props.getUserData(id).then((res) => {
                const {data} = res.data
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
                    internalId: data.internal_id
                })
                setCompanyId(_.has(data.company) ? data.company.id : '')
                setUnionId(!_.isEmpty(data.unions) ? data.unions[0].id : '')
                setUniName(!_.isEmpty(data.unions) ? data.unions[0] : "")
                setLoading(false);
                if (data.is_organiser && data.is_organiser) {
                    props.setUser(res.data.data)
                }
                initialize(data);

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
     * @description Save the image data and show in profile data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} file
     * @method
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
                    props.setUser(res.data.data)
                }
                // getProfileData(values.id)
            }).catch((err) => {
                alert.show(Helper.handleError(err), {type: 'error'})
            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'})
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method handles state to show  delete confirmation pop up for delete the data
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
     * @description  This method removes the user profile image from profile component
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
                // getProfileData(values.id)
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
     * @description Fetches the updated data api of user details and sets in states and show in fields
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const updateUser = () => {
        const data = {
            "_method": "PUT",
            "id": values.id,
            "fname": values.first_name,
            "lname": values.last_name,
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
            "internal_id": values.internalId
        }

        try {
            props.updateUser(data).then((res) => {
                alert.show(t("notification:rec add 1"), {type: 'success'})
                // getProfileData(values.id)
                // props.setUser(res.data.data)
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
     * @description It send the form data in update api method
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e
     */
    const submitForm = (e) => {
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

    return (
        <>
            <LoadingSkeleton loading={loading} skeleton={<ProfilePageSkeleton />}>
                <div className="profileCardDiv other_profile">
                    <Grid container spacing={3}>
                        <Grid className="profileImgDiv" item xs={12} lg={12}>
                            <div className="ImgDivRelative">
                                < ><ImageEditing onSaveImage={saveImage} avatar={values.avatar}
                                                 userTxt={Helper.nameProfile(values.first_name, values.last_name)} /></>
                                {values.avatar && <CloseIcon onClick={deleteConfirm}>remove</CloseIcon>}
                            </div>
                        </Grid>
                        {/* <ValidatorForm ref={ref} onSubmit={submitForm} onError={errors => console.log(errors)}> */}
                        {/* <FormControl variant="outlined"  > */}
                        <form onSubmit={handleSubmit(updateUser)}>
                            <Grid item xs={12}>
                                <Grid container spacing={3}>
                                    <Grid className="leftProfileInfo" item xs={6} lg={6}>
                                        <div className="inputFlexDiv">
                                            <Field
                                                name="fname"
                                                label={t("First Name")}
                                                value={Helper.jsUcfirst(values.first_name)}
                                                onChange={handleChange('first_name')}
                                                aria-describedby="outlined-email-helper-text"
                                                inputProps={{
                                                    'aria-label': 'email',
                                                }}
                                                labelWidth={0}
                                                variant="outlined"
                                                margin="dense"
                                                component={renderTextField}
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
                                            <Field
                                                name="email"
                                                disabled={true}
                                                label={t("Email")}
                                                value={values.email}
                                                aria-describedby="outlined-email-helper-text"
                                                inputProps={{
                                                    'aria-label': 'email',
                                                }}
                                                labelWidth={0}
                                                variant="outlined"
                                                margin="dense"
                                                component={renderTextField}
                                            />
                                        </div>
                                        <div className="inputFlexDiv AutoCompleteDiv">
                                            <EntityAutoComplete2
                                                name={uniName && uniName !== undefined ? uniName : ""}
                                                CompanyId={(id) => setUnionId(id)}
                                                type={2}
                                                selectName={(value) => setUniName({long_name: value})}
                                            />
                                        </div>
                                        <div className="inputFlexDiv">
                                            <Field
                                                name="internal_id"
                                                label={t("InternalId")}
                                                aria-describedby="outlined-email-helper-text"
                                                inputProps={{
                                                    'aria-label': 'email',
                                                }}
                                                labelWidth={0}
                                                onChange={handleChange('internalId')}
                                                variant="outlined"
                                                margin="dense"
                                                value={values.internalId}
                                                component={renderTextField}
                                            />
                                        </div>
                                    </Grid>
                                    <Grid className="rightProfileInfo" item xs={6} lg={6}>
                                        <div className="inputFlexDiv">
                                            <Field
                                                name="lname"
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
                                                component={renderTextField}
                                            />
                                        </div>
                                        <div className="inputFlexDiv">
                                            {/* <div className="leftLabel"><p>{t("Designation")}:</p></div> */}
                                            <TextField
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
                                            <TextField
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
                                    </Grid>
                                </Grid>
                                <Grid className="profileButtonDiv" item xs={12} lg={12}>
                                    <Button type="submit" variant="contained" color="primary" className="updateBtn">
                                        {t("Update")}
                                    </Button>
                                </Grid>
                            </Grid>

                        </form>
                        {/* </FormControl> */}
                        {/* </ValidatorForm> */}
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
ProfileCard1 = reduxForm({
    form: "EventTag", // a unique identifier for this form
    validate,
    keepDirtyOnReinitialize: true
})(ProfileCard1);
export default connect(mapStateToProps, mapDispatchToProps)(ProfileCard1);