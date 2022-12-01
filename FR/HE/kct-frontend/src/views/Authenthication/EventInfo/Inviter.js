import React from 'react';
import {Field, FieldArray, reduxForm, reset} from 'redux-form';
import Validation from '../../../functions/ReduxFromValidation';
import {Provider as AlertContainer } from 'react-alert';
import {connect} from 'react-redux';
import RoundedCrossIcon from '../../Svg/RoundedCrossIcon.js';
import _ from 'lodash'
import authActions from '../../../redux/actions/authActions';
import Helper from '../../../Helper';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method will convert the string to upper case
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {String} value The target string to convert to upper case
 * @returns {String}
 */
const upper = value => value && Helper.jsUcfirst(value);

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
const InputBox = ({placeholder, input, type, value, meta: {touched, error, warning}}) => {
    return (<>
            <input {...input} type={type} placeholder={placeholder} className="no-border form-control" />
            {touched &&
            ((error && <span className="text-danger">{error}</span>) ||
                (warning && <span>{warning}</span>))}
        </>
    )
}

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to invite other user for the specific event from quick registration page.
 * This component can be invite multiple users by entering their detail(name , email) in the form and send a invite
 * email in just one click.
 * This component includes input fields for first name, last name, email and 2 buttons. One for add more input
 * fields  and one for submit  the form(to perform invite action).
 * ---------------------------------------------------------------------------------------------------------------------
 */
class InviterForm extends React.Component {
    fixField = () => (
        <div className="row">
            <div className="form-group col-sm-3 outline-fix dark-invite">
                <Field
                    name="fname"
                    type="text"
                    placeholder={'First Name'}
                    normalize={upper}
                    component={InputBox}
                    validate={[Validation.required, Validation.min3]} />
            </div>
            <div className="form-group col-sm-3 outline-fix dark-invite">
                <Field
                    name={`lname`}
                    type="text"
                    placeholder={'Last Name'}
                    normalize={upper}
                    component={InputBox}
                    validate={[Validation.required, Validation.min3]} />
            </div>
            <div className="form-group col-sm-3 outline-fix dark-invite">
                <Field
                    name={`email`}
                    type="email"
                    placeholder="Email"
                    component={InputBox}
                    validate={[Validation.required, Validation.email]} />
            </div>
        </div>
    )

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will return JSX(more row for invite user details).It will trigger when user click
     * on '+' icon from invite user section in quick registration page.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {Object[]} fields Name of the fields to update
     * @param {Boolean} touched To indicate if the field is focused
     * @param {String} error The error string message for the respective field
     * @returns {JSX.Element}
     */
    renderMembers = ({fields, meta: {touched, error}}) => {
        const {invites} = this.props
        return (<div>
                {fields.map((member, index) =>
                        <div key={index}>
                            <div className='row'>
                                <div className="form-group col-sm-3 dark-invite">
                                    <Field
                                        name={`${member}.fname`}
                                        type="text"
                                        placeholder="First name"
                                        normalize={upper}
                                        component={InputBox}
                                        validate={[Validation.required, Validation.min3]} />
                                </div>
                                <div className="form-group col-sm-3 dark-invite">
                                    <Field
                                        name={`${member}.lname`}
                                        type="text"
                                        placeholder="Last name"
                                        normalize={upper}
                                        component={InputBox}
                                        validate={[Validation.required, Validation.min3]} />
                                </div>
                                <div className="form-group col-sm-3 dark-invite">
                                    <Field
                                        name={`${member}.email`}
                                        type="text"
                                        placeholder="Email"
                                        component={InputBox}

                                        validate={[Validation.required, Validation.email]} />
                                </div>
                                <div className="form-group col-sm-1 col-md-1">
                                    <button type="button" className="invite-cross" onClick={() => fields.remove(index)}>
                <span>
                  <RoundedCrossIcon />
                </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                )}
                <div className="black-btn">
                    {fields.length < (9 - invites.length) &&
                    <button type="button" className="pluss mr-10 no-border" onClick={() => fields.push({})}>+</button>}
                    <button type="submit" className="binvite no-border">Invite</button>
                </div>
            </div>
        )
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will take input email object(from parameter) and check for duplicate email.If exist
     * then it will throw an error.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} postData Data to send to backend for api
     * @param {String} postData.fname First name of user
     * @param {String} postData.lname Last name of user
     * @param {String} postData.email Email of user to invite
     * @return {Boolean}
     */
    checkEmailFields = (postData) => {
        let finalFlag = [];
        postData.filter((item, key) => {
            postData.filter((val, index) => {
                if (item.email == val.email && key != index) {
                    finalFlag.push(item);
                }
            })
        });
        return !_.isEmpty(finalFlag);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to perform invite user action.Before invitation it will check
     * duplicate email should not be present in the invitation data.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} val Data to send to backend for api
     * @param {String} val.fname First name of user
     * @param {String} val.lname Last name of user
     * @param {String} val.email Email of user to invite
     * @param {Function} dispatch React dispatcher
     */
    addInvite = (val, dispatch) => {
        let postData = []
        postData.push({fname: val.fname, email: val.email, lname: val.lname})
        if (val.members !== undefined) {
            _.map(val.members, (o => postData.push(o)))
        }
        if (this.checkEmailFields(postData)) {
            return this.msg.show("Duplicate Emails Exist", {type: "error"})
        }
        this.props.addInvite({user: postData, event_uuid: this.props.event_uuid}).then((response) => {
            if (response.data.data) {
                /* insert here */
                this.props.setInvites(response.data.data)
                this.props.initialize({})
                dispatch(reset("fieldArrays"));
                this.msg.show(Helper.alertMsg.FLASH_MSG_REC_ADD_1, {type: "success"})
            } else {
                /* insert here */
                this.msg.show(Helper.alertMsg.FLASH_MSG_REC_ADD_0, {type: "error"})
            }
        }).catch((err) => {
            this.msg.show(Helper.handleError(err), {type: "error"})
        })
    }

    render() {
        const {handleSubmit, pristine, reset, submitting, invites} = this.props
        return (
            <>
                {invites && invites.map((o) => {
                    return (<div className='row mb-10'>
                        <div className="col-sm-3 heading-color">{`${_.upperFirst(o.first_name)} ${o.last_name}`}</div>
                        <div className="col-sm-3 heading-color">{o.email}</div>
                        <div className="col-sm-3 heading-color"></div>
                    </div>)
                })
                }
                <form onSubmit={handleSubmit(this.addInvite)}>
                    <AlertContainer ref={(a) => this.msg = a} {...Helper.alertOptions} />
                    {this.fixField()}
                    <FieldArray name="members" component={this.renderMembers} />

                </form>
            </>
        )
    }

}

const afterSubmit = (result, dispatch) => dispatch(reset('ordersTradesSearchForm'));

InviterForm = reduxForm({
    form: 'fieldArrays',     // a unique identifier for this form
    Validation
})(InviterForm)

const mapDispatchToProps = (dispatch) => {
    return {
        addInvite: (data) => dispatch(authActions.Auth.addInvite(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        event_badge: state.NewInterface.interfaceBadgeData,
    }
}
InviterForm = connect(
    mapStateToProps,
    mapDispatchToProps
)(InviterForm);
export default InviterForm; 