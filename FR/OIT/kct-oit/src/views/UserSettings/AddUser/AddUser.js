import React from 'react';
import {Field, reset, FieldArray, reduxForm, SubmissionError} from 'redux-form';
import Validation from '../../../functions/ReduxFromValidation';
import _ from 'lodash';
import Grid from '@material-ui/core/Grid';
import {connect} from 'react-redux';
import AddIcon from '@material-ui/icons/Add';
import Helper from '../../../Helper';
import {sizing} from '@material-ui/system';
import Button from '@material-ui/core/Button';
import {withAlert} from 'react-alert';
import './AddUser.css';
import TextField from '@material-ui/core/TextField';
import userAction from '../../../redux/action/apiAction/user';
import CloseIcon from '../../Svg/closeIcon.js';
import {withRouter} from "react-router-dom";
import EmailAutoComplete from "../../CreateEvent/EventPreparation/ManagingRoles/AddUserParticipants/EmailAutoComplete";

const upper = (value) => value && Helper.jsUcfirst(value);

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common structure for an input field to render input box for first name, last name, email.This
 * will take data(from parameter where it called) which is necessary to render relative text fields.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} placeholder Placeholder for the input box
 * @param {Input} input Actual input box with its default properties
 * @param {String} type Type of the input box like password or text or email etc
 * @param {Boolean} touched To indicate if the input box is touched or not
 * @param {String} error Error message from input box
 * @param {String} warning Warning message from input box
 * @return {JSX.Element}
 */const InputBox = ({
                         placeholder,
                         input,
                         type,
                         value,
                         meta: {touched, error, warning},
                     }) => {
        return (
            <>
                <TextField
                    size="small"
                    {...input}
                    type={type}
                    label={placeholder}
                    className="no-border form-control"
                    variant="outlined"
                />

                {touched &&
                ((error && <span className="text-danger">{error}</span>) ||
                    (warning && <span>{warning}</span>))}
            </>
        );
    }
;

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to add users(for application uses) by sending them an invitation email.This
 * also provides add multiple users(by sending them an invitation email) feature.
 * <br>
 * Maximum of 10 users can be added at once.
 * ---------------------------------------------------------------------------------------------------------------------
 */
class AddUser extends React.Component {
    constructor(props) {
        super(props);
        // Don't call this.setState() here!
        this.state = {
            userId: "",
            fname: "",
            lname: "",
            email: "",
        };

        this.gKey = this.props.match.params.gKey;
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method returns the email,first name,last name,fields
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {JSX.Element}
     */
    fixField = () => (
        <Grid container className="addedUserRow" justify="center">
            <Grid xs={12} md={12} lg={12}>
                <div className="row">
                    <div className="InputFlex-Div">
                        {/* <Field
                            name={`email`}
                            type="email"
                            placeholder="Email"
                            component={InputBox}
                            validate={[Validation.required, Validation.email]} /> */}
                        <Field
                            name="email"
                            gKey={this.gKey}
                            userData={(data) => this.setPropsValue(data)}
                            component={EmailAutoComplete}
                            placeholder="Enter Email"
                            // component={InputBox}
                            validate={[Validation.email, Validation.required]}
                        />
                    </div>
                    <div className="InputFlex-Div">
                        <Field
                            name="fname"
                            type="text"
                            width={1 / 3}
                            placeholder={"First Name"}
                            normalize={upper}
                            component={InputBox}
                            validate={[
                                Validation.required,
                                Validation.min2,
                                Validation.alpha,
                            ]}
                        />
                    </div>

                    <div className="InputFlex-Div">
                        <Field
                            name={`lname`}
                            type="text"
                            placeholder={"Last Name"}
                            normalize={upper}
                            component={InputBox}
                            validate={[
                                Validation.required,
                                Validation.min2,
                                Validation.alpha,
                            ]}
                        />
                    </div>

                    <div className="InputFlex-Div">
                        <Button
                            variant="contained"
                            className="theme-btn"
                            color="primary"
                            type="submit"
                        >
                            Invite
                        </Button>
                    </div>
                </div>
            </Grid>
        </Grid>
    )

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle(add extra from row) multiple forms row(empty form fields to take user's
     * first name, last name and email) to send an invitation email for all added row's users in one click.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} fields These are the input fields for entering user's details
     * @param {String} fields.fname User's fname
     * @param {String} fields.lname User's lname
     * @param {String} fields.email User's email
     * @param {Boolean} touched  To check if input field is touched or not
     * @param {String} error Error message below input fields
     * @return {JSX.Element}
     */
    renderMembers = ({fields, meta: {touched, error}}) => {
        const {invites} = this.props
        return (<div>

                {fields.map((member, index) => {
                    return <div key={index}>
                        <Grid container className="addedUserRow" justify="center">
                            <Grid xs={12} md={12} lg={12}>
                                <div className='row'>
                                    <div className="InputFlex-Div">
                                        <Field
                                            name={`${member}.fname`}
                                            type="text"
                                            placeholder="First name"
                                            normalize={upper}
                                            component={InputBox}
                                            validate={[Validation.required, Validation.min3, Validation.alpha]} />
                                    </div>

                                    <div className="InputFlex-Div">
                                        <Field
                                            name={`${member}.lname`}
                                            type="text"
                                            placeholder="Last name"
                                            normalize={upper}
                                            component={InputBox}
                                            validate={[
                                                Validation.required,
                                                Validation.min3,
                                                Validation.alpha,
                                            ]}
                                        />
                                    </div>

                                    <div className="InputFlex-Div">
                                        <Field
                                            name={`${member}.email`}
                                            type="text"
                                            placeholder="Email"
                                            component={InputBox}
                                            validate={[Validation.required, Validation.email]}
                                        />
                                    </div>
                                    <div className="removeBtnDiv">
                                        <div
                                            className="theme-btn"
                                            type="button"
                                            onClick={() => fields.remove(index)}
                                        >
                                            <CloseIcon />
                                            {/* <HighlightOffIcon  color="primary"/> */}
                                        </div>
                                    </div>
                                </div>
                            </Grid>
                        </Grid>
                    </div>
                })}
                <div className="black-btn AddUserBtns">
                    {fields.length != undefined && fields.length < 9 && (
                        <Button
                            variant="contained"
                            className="theme-btn"
                            color="primary"
                            type="button"
                            onClick={() => fields.push({})}
                        >
                            <AddIcon />
                        </Button>
                    )}

                    <Button
                        variant="contained"
                        className="theme-btn"
                        color="primary"
                        type="submit"
                    >
                        Invite
                    </Button>
                </div>
            </div>
        );
    };

    setPropsValue = (data) => {
        if (data && data.id) {
            this.setState({
                userId: data.id,
                fname: data.fname,
                lname: data.lname,
                email: data.email,
            });
        }
    };

    setPropsValue = (data) => {
        if (data && data.id) {
            this.setState({
                userId: data.id,
                fname: data.fname,
                lname: data.lname,
                email: data.email,
                disable: true,
            });
            this.props.initialize(data);
        } else if (data == null) {
            this.setState({
                userId: "",
                fname: "",
                lname: "",
                email: "",
                disable: false,
            });
        } else {
            if (data) {
                this.setState({
                    email: data.email,
                });
                this.props.initialize(data);
            }
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to invite users for our platform using there email. This function will get data
     * (added users data from parameter and a function to call an API for invite users).If the details are correct then
     * this will return alert of success message other wise will return error message alert.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} val The object contains entered value of user's details
     * @param {String} val.fname User's fname
     * @param {String} val.lname User's lname
     * @param {String} val.email User's email
     * @param {Function} dispatch It's a function which will call redux action to reset field array
     * @return {JSX.Element}
     */
    addInvite = (val, dispatch) => {
        console.log('val', val, dispatch)
        let postData = [];
        postData.push({fname: val.fname, email: val.email, lname: val.lname});
        if (val.members !== undefined) {
            _.map(val.members, (o) => postData.push(o));
        }
        //Get current logged in user's data
        const user_badge = this.props.user_badge;
        const group_id = _.has(user_badge, ["current_group", "id"])
            ? user_badge.current_group.id
            : null;
        const group_role =
            this.props.org == "pilot"
                ? 2
                : this.props.org == "owner"
                ? 3
                : this.props.org == "copilot"
                    ? 4
                    : 1;
        // console.log(this.props.org, "-ttt-",group_role);

        var data = {};
        if (this.props.event_uuid) {
            data = {
                user: postData,
                group_key: this.gKey,
                group_role: group_role,
                event_uuid: this.props.event_uuid,
                allow_update: 1,
            };
        } else {
            data = {
                user: postData,
                group_key: this.gKey,
                group_role: group_role,
                allow_update: 1,
            };
        }

        return this.props
            .addInvite(data)
            .then((response) => {
                if (response.data.data) {
                    /* insert here */
                    // this.props.setInvites(response.data.data)
                    this.props.initialize({});
                    this.props.alert.show("Users Added", {type: "success"});
                    this.props.setDropdown(0);
                    // console.log('this.props.setDropdown', this.props.setDropdown)

                    dispatch(reset("fieldArrays"));
                    this.props.org != undefined &&
                    this.props.addSubmit &&
                    this.props.addSubmit();
                    this.props.callBack && this.props.callBack();
                } else {
                    /* insert here */
                    this.props.alert.show(
                        Helper.handleError({msg: "Something went Wrong"}),
                        {type: "error"}
                    );
                }
            })
            .catch((err) => {
                // this.props.alert.show(Helper.handleError(err), {type: "error"});

                if (_.has(err, ["response", "status"]) && err.response.status == 422) {
                    const {errors} = err.response.data;
                    let errorData = {};
                    let members = [];

                    Object.keys(errors).map((keys) => {
                        if (keys.includes("0")) {
                            const newKey = keys.split("0.")[1];
                            errorData[newKey] = errors[keys][0];
                        } else {
                            const newIndex = keys.split(".")[1];
                            const newItem = keys.split(".")[2];

                            if (typeof members[newIndex] == "undefined") {
                                members[newIndex - 1] = {[newItem]: errors[keys][0]};
                            } else {
                                members[newIndex - 1] = {
                                    ...members[newIndex - 1],
                                    [newItem]: errors[keys][0],
                                };
                            }
                        }
                    });

                    if (!_.isEmpty(errors)) {
                        throw new SubmissionError({...errorData, members: members});
                    }
                }
            });
    };

    render() {
        const {handleSubmit, pristine, reset, submitting, invites, event_uuid} =
            this.props;
        console.log("statttttttt", this.state);
        return (
            <>
                {invites &&
                invites.map((o) => {
                    return (
                        <div className="row mb-10">
                            <div className="col-sm-3 heading-color">{`${_.upperFirst(
                                o.first_name
                            )} ${o.last_name}`}</div>
                            <div className="col-sm-3 heading-color">{o.email}</div>
                            <div className="col-sm-3 heading-color"></div>
                        </div>
                    );
                })}
                <form onSubmit={handleSubmit(this.addInvite)}>

                    {this.fixField()}
                    {/* <FieldArray name="members" component={this.renderMembers} /> */}
                </form>
            </>
        );
    }
}

const afterSubmit = (result, dispatch) => dispatch(reset("ordersTradesSearchForm"));

AddUser = reduxForm({
    form: 'fieldArrays',     // a unique identifier for this form
    Validation
})(AddUser)

const mapDispatchToProps = (dispatch) => {
    return {
        addInvite: (id) => dispatch(userAction.addMultiple(id)),
    }
}

const mapStateToProps = (state) => {
    return {
        user_badge: state.Auth.userSelfData,
    };
};
AddUser = connect(mapStateToProps, mapDispatchToProps)(AddUser);
export default withAlert()(withRouter(AddUser));
