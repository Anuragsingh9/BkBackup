import React from 'react';
import {Field, reset, FieldArray, reduxForm, SubmissionError} from 'redux-form';
import Validation from '../../../../../functions/ReduxFromValidation';
import _ from 'lodash';
import Grid from '@material-ui/core/Grid';
import {connect} from 'react-redux';
import AddIcon from '@material-ui/icons/Add';
import Helper from '../../../../../Helper';
// import EyeIcon from '../../Svg/EyeIcon.js';
// import HighlightOffIcon from '@material-ui/icons/HighlightOff';
import {sizing} from '@material-ui/system';
import Button from '@material-ui/core/Button';
import {withAlert} from 'react-alert';
// import './AddUser.css';
import TextField from '@material-ui/core/TextField';
import userAction from '../../../../../redux/action/apiAction/user';
import CloseIcon from '../../../../Svg/closeIcon.js';
import EmailAutoComplete from './EmailAutoComplete';
import User from '../../../../../Models/User'
import {withRouter} from "react-router-dom";
import eventAction from "../../../../../redux/action/reduxAction/event";

// common function to do upper case
const upper = value => value && Helper.jsUcfirst(value)

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method returns input component
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} value Entered input value
 * @param {String} placeholder Placeholder for the input
 * @param {Boolean} disabled To check the disabled mode of input box
 * @param {Object} input  Html input box
 * @param {String} type Type of the input
 * @param {Boolean} touched To check if the input field is touched or not
 * @param {String} error Error message
 * @param {String} warning Warning message
 * @return {JSX.Element}
 * @constructor
 */
const InputBox = ({value, placeholder, disabled, input, type, meta: {touched, error, warning}}) => {
    return (<>
            <TextField size="small"
                // {...input}
                       name={input.name}
                       onChange={input.onChange}
                       value={input.value}
                       disabled={disabled}
                       type={type}
                       label={placeholder}
                       {...input}
                       className="no-border form-control"
                       variant="outlined" />

            {touched &&
            ((error && <span className="text-danger">{error}</span>) ||
                (warning && <span>{warning}</span>))}
        </>
    )
}


const autocompleteBox = ({value, userData, placeholder, disabled, input, type, meta: {touched, error, warning}}) => {
    return (<>
            {/* <TextField size="small"
      // {...input}
      name={input.name}
      onChange={input.onChange}
      value={input.value}
      disabled= {disabled}
      type={type}
      label={placeholder}
      {...input}
      className="no-border form-control"
      variant="outlined" /> */}
            <EmailAutoComplete {...input} userData={userData} error={error} touched={touched} warning={warning} />
            {/* {touched &&
      ((error && <span className="text-danger">{error}</span>) ||
        (warning && <span>{warning}</span>))} */}
        </>
    )
}

/**
 *
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Common component used for adding users manually.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent
 * @param {User} props.user_badge [State] User's data from redux store
 * @param {Function} props.addInvite [Dispatcher] Action to add user
 * @return {JSX.Element}
 */
class AddUser extends React.Component {
    constructor(props) {
        super(props);
        // Don't call this.setState() here!
        this.state = {
            userId: '',
            fname: '',
            lname: '',
            email: "",
            disable: false
        };

    }


    // this.setState({userId :!_.isEmpty(data.space_hosts) ? data.space_hosts[0].id : ''})

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for updates the state form props values
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Data for adding user
     * @param {Number} data.id Id of the user
     * @param {String} data.fname First name of the user
     * @param {String} data.lname Last name of the user
     * @param {String} data.email Email of the user
     */
    setPropsValue = (data) => {
        if (data && data.id) {
            this.setState({
                userId: data.id,
                fname: data.fname,
                lname: data.lname,
                email: data.email,
                disable: true
            })
            this.props.initialize(data)


        } else if (data == null) {
            this.setState({
                userId: "",
                fname: "",
                lname: "",
                email: "",
                disable: false
            })
        } else {
            if (data) {
                this.setState({
                    email: data.email
                })
                this.props.initialize(data)
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method updates first name value in the state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    handleFnameChange = (e) => {
        this.setState({
            fname: e.target.value,
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method updates last name value in the state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    handleLnameChange = (e) => {
        this.setState({
            lname: e.target.value
        })
    }

    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method renders fix  fields for add users first row that contains first name,last name and
     * email field
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @return {JSX.Element}
     */
    fixEventField = () => (

        <Grid container className="addedUserRow" justify="center">
            <Grid xs={12} md={12} lg={12}>

                <div className="row">
                    <div className="InputFlex-Div autoCompleteInput_wrap">
                        <Field
                            name="email"
                            gKey={this.props.gKey}
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
                            inputProps={{
                                value: this.state.fname

                            }}
                            // value={this.state.fname}
                            width={1 / 3}
                            placeholder={'First Name'}
                            normalize={upper}
                            onChange={this.handleFnameChange}
                            disabled={this.state.disable}
                            component={InputBox}
                            validate={[Validation.required, Validation.min2, Validation.alpha]} />
                    </div>
                    <div className="InputFlex-Div">
                        <Field
                            name={`lname`}
                            type="text"
                            placeholder={'Last Name'}
                            normalize={upper}
                            onChange={this.handleLnameChange}
                            component={InputBox}
                            disabled={this.state.disable}
                            validate={[Validation.required, Validation.min3, Validation.alpha]} />
                    </div>


                </div>
            </Grid>
        </Grid>
    )

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method renders dynamic fields array for add users  that contains first name last name and
     * email field
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} fields Name of the field
     * @param {Boolean} touched To indicate if the field is touched or not
     * @param {String} error Error message
     * @return {JSX.Element}
     */
    renderEventMembers = ({fields, meta: {touched, error}}) => {

        const {invites} = this.props
        return (<div className='additional_addedUser'>

                {fields.map((member, index) => {
                        return <div key={index}>
                            <Grid container className="addedUserRow" justify="center">
                                <Grid xs={12} md={12} lg={12}>
                                    <div className='row'>
                                        <div className="InputFlex-Div autoCompleteInput_wrap">
                                            <Field
                                                name={`${member}.email`}
                                                type="text"
                                                placeholder="Email"

                                                inputProps={{
                                                    value: "check@mailinator.com"

                                                }}
                                                component={EmailAutoComplete}
                                                // component={autocompleteBox}

                                                validate={[Validation.required, Validation.email]}
                                            />
                                            {/* <EmailAutoComplete userData={(data) => this.setPropsValue(data)} /> */}
                                        </div>
                                        <div className="InputFlex-Div">
                                            <Field
                                                name={`${member}.fname`}
                                                type="text"
                                                placeholder="First name"
                                                normalize={upper}
                                                component={InputBox}

                                                validate={[Validation.required, Validation.min2, Validation.alpha]} />
                                        </div>

                                        <div className="InputFlex-Div">
                                            <Field
                                                name={`${member}.lname`}
                                                type="text"
                                                placeholder="Last name"
                                                normalize={upper}
                                                component={InputBox}
                                                validate={[Validation.required, Validation.min3, Validation.alpha]} />
                                        </div>

                                        <div className="removeBtnDiv">
                                            <div className="theme-btn" type="button" onClick={() => fields.remove(index)}>
                                                <CloseIcon />
                                                {/* <HighlightOffIcon  color="primary"/> */}
                                            </div>
                                        </div>

                                    </div>
                                </Grid>
                            </Grid>

                        </div>
                    }
                )}
                <div className="black-btn AddUserBtns">
                    {/* {fields.length != undefined && fields.length < 9 &&
          <Button variant="contained" className="theme-btn" color="primary" type="button" onClick={() => fields.push({})}><AddIcon /></Button>
        } */}

                    <Button variant="contained" className="theme-btn" color="primary" type="submit">Invite</Button>
                </div>
            </div>
        )
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for fetching api that sends users data to server for invitation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} val Data of user
     * @param {String} val.fname First name of the user
     * @param {String} val.email Email of the user
     * @param {String} val.lname Last name of the user
     * @param {Function} dispatch Function to manage redux data
     */
    addInvite = (val, dispatch) => {

        let postData = []
        postData.push({fname: val.fname, email: val.email, lname: val.lname})
        if (val.members !== undefined) {
            _.map(val.members, (o => postData.push(o)))
        }

        const user_badge = this.props.user_badge;
        const group_id = _.has(user_badge, ['current_group', 'id']) ? user_badge.current_group.id : null;

        const group_role = this.props.org ? 2 : 1;

        var data = {};
        if (this.props.event_uuid) {
            data = {
                user: postData,
                group_id: group_id,
                group_role: group_role,
                event_uuid: this.props.event_uuid,
                allow_update: 1,
                group_key: this.props.match.params.gKey,
            }

        } else {
            data = {
                user: postData,
                group_id: group_id,
                group_role: group_role,
                allow_update: 0,
                groupKey: this.props.match.params.gKey,
            }
        }

        return this.props.addInvite(data).then((response) => {
            if (response.data.data) {
                /* insert here */
                // this.props.setInvites(response.data.data)
                this.props.initialize({})
                this.props.alert.show('Users Added', {type: "success"})
                this.props.updateAddUserPopUpDisplay(false,0,true)

                dispatch(reset("fieldArrays"));
                this.props.org && this.props.addSubmit && this.props.addSubmit();
                this.props.callBack && this.props.callBack();
            } else {
                /* insert here */
                this.props.alert.show(Helper.handleError({msg: 'Something went Wrong'}), {type: "error"})

            }
        }).catch((err) => {

            if (_.has(err, ['response', 'status']) && err.response.status == 422) {

                const {errors} = err.response.data;
                let errorData = {};
                let members = [];
                Object.keys(errors).map((keys) => {
                    if (keys.includes('0')) {
                        const newKey = keys.split('0.')[1];
                        errorData[newKey] = errors[keys][0]
                        this.props.alert.show(errors[keys][0], {type: "error"});
                    } else {
                        const newIndex = keys.split('.')[1];
                        const newItem = keys.split('.')[2];

                        if (typeof members[newIndex] == 'undefined') {
                            members[newIndex - 1] = {[newItem]: errors[keys][0]}
                        } else {
                            members[newIndex - 1] = {...members[newIndex - 1], [newItem]: errors[keys][0]}
                        }
                    }
                })

                if (!_.isEmpty(errors)) {
                    throw new SubmissionError({...errorData, members: members})
                }
            } else {
                this.props.alert.show(Helper.handleError(err), {type: "error"});
            }
        })

    }

    render() {
        const {handleSubmit, pristine, reset, submitting, invites, event_uuid, formValues} = this.props
        return (
            <div className="addUserModalMain">
                {invites && invites.map((o) => {
                    return (<div className='row mb-10'>
                        <div className="col-sm-3 heading-color">{`${_.upperFirst(o.first_name)} ${o.last_name}`}</div>
                        <div className="col-sm-3 heading-color">{o.email}</div>
                        <div className="col-sm-3 heading-color"></div>
                    </div>)
                })
                }
                <form onSubmit={handleSubmit(this.addInvite)}>
                    {event_uuid ? this.fixEventField() : this.fixField()}
                    {/* {this.fixField()} */}
                    <FieldArray name="members"
                                component={event_uuid !== undefined ? this.renderEventMembers : this.renderMembers} />
                </form>
            </div>
        )
    }

}

const afterSubmit = (result, dispatch) =>
    dispatch(reset('ordersTradesSearchForm'));

AddUser = reduxForm({
    form: 'fieldArrays',     // a unique identifier for this form
    Validation
})(AddUser)

const mapDispatchToProps = (dispatch) => {
    return {
        addInvite: (id) => dispatch(userAction.addMultiple(id)),
        updateAddUserPopUpDisplay : (display,mode,fetch) => dispatch(eventAction.updateAddUserPopUpDisplay(display,mode,fetch)),
    }
}

const mapStateToProps = (state) => {
    return {
        user_badge: state.Auth.userSelfData
    }
}
AddUser = connect(
    mapStateToProps,
    mapDispatchToProps
)(AddUser);
export default withAlert()(withRouter(AddUser));