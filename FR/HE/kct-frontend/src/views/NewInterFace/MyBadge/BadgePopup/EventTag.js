import React, {useRef, useState} from 'react'
import ProgressButton from 'react-progress-button';
import {connect} from 'react-redux'
import {Provider as AlertContainer, useAlert } from 'react-alert';
import "./EventTag.css";
import Helper from '../../../../Helper';
import {confirmAlert} from 'react-confirm-alert';
import {Field, reduxForm,} from "redux-form";
import {getAlphaValidator, Validation} from '../../../../functions/CustomValidators';
import eventActions from '../../../../redux/actions/eventActions';
import newInterfaceActions from '../../../../redux/actions/newInterfaceAction';
import EventTagList from './EventTagList';
import {useTranslation} from 'react-i18next';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common structure for a input field to render input box for add tags.This
 * will take data(from parameter where it called) which is necessary to render relative text fields.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} input Redux form passed state
 * @param {String} input.name Redux form field name
 * @param {OrgTag[]} data List of tags to render
 * @param {Boolean} touched To indicate if the field is focused
 * @param {String} error The error string message for the respective field
 * @param {String} warning The warning message for the respective field
 *
 * @returns {JSX.Element}
 * @constructor
 */
const InputComponant = ({input, data, meta: {touched, error, warning}}) => {
    const {t} = useTranslation(['myBadgeBlock', 'notification']);
    return (<>
            <select {...input} className="form-control">
                <option value={''}>{t("Select Tag")}</option>
                {data && data.map((o, k) => (
                    <option value={o.id} key={k}>{o.name}</option>
                ))
                }
            </select>
        </>
    )
}


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to add event tags from badge editor component.This is including an input
 * field in which once user start typing to add tag it will show related tags suggestions(as per already added event
 * tags).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} getRecord To get the event tags
 * @param {UserBadge} event_badge User badge details
 * @param {String} eventUuid Current event uuid
 * @param {Function} updateProfileTrigger To update the profile data on backend server
 * @param {Function} setBadge To update the badge details of user
 * @param {Function} addTag To add the new tag
 * @param {Function} updateTag To update the existing tag relation
 * @param {Function} deleteTag To remove the tag relation from the profile with server
 * @param {Function} handleSubmit To submit the user changes and update the event tags with server
 * @param {Function} initialize Redux initialized to bind the state with redux fields
 * @param {Object} alert Reference object for displaying notification popup
 * @returns {JSX.Element}
 * @constructor
 */
let EventTag = ({
                    getRecord,
                    event_badge,
                    eventUuid,
                    updateProfileTrigger,
                    setBadge,
                    addTag,
                    updateTag,
                    deleteTag,
                    handleSubmit,
                    initialize,
                    alert
                }) => {
    const {t} = useTranslation('myBadgeBlock');
    const validator = getAlphaValidator();
    let msg = useAlert(); //for alert container msg
    const [isLoading, setLoading] = useState(false) //loading
    let tagData = event_badge.tags_data
    const [buttonState, setButtonState] = useState({buttonState: '', controlled: false})

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will update state to enable loader.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const fetchData = () => {
        //enable loading
        setLoading(true)
        //fetch current id record
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This is a wrapper function to call 'addApi' API handler function.THis will trigger when user submit
     * the value to add event tag from badge editor component.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} o Object which contains the tag added
     * @param {OrgTag} o.tag Tag data to send
     */
    const formSubmit = (o) => {
        setButtonState({buttonState: 'loading', controlled: false})
        addApi(o.tag)
    }


    /**
     * --------------------------------------------------------------------------------------------------------------------
     * @description This is an API handler function to update selected event tags.
     * --------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} tag_id Id of the tag to submit
     */
    const addApi = (tag_id) => {
        if (!tag_id) {
            return alert && alert.show("Tag id is required", {
                type: "error", onClose: () => {
                    setButtonState({buttonState: '', controlled: true})
                }
            })
        }
        const event_uuid = eventUuid;
        addTag({tag_id,event_uuid}).then((res) => {
            if (res.data.status) {
                initialize({tag: ''});
                setBadge({...event_badge, 'tags_data': res.data.data})
                updateProfileTrigger({...event_badge, 'tags_data': res.data.data}, eventUuid)

                alert && alert.show(t("notification:rec add 1"), {
                    type: "success"
                })
            } else {
                alert && alert.show(res.data.msg, {
                    type: "error"
                })
            }
            setButtonState({buttonState: '', controlled: true})
        }).catch((err) => {
            console.error(err)
            setButtonState({buttonState: '', controlled: true})
            alert && alert.show(Helper.handleError(err), {
                type: "error",
            })

        })
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will open a popup component and take confirmation to perform delete
     * tag action. That popup component contains 2 button('Yes', 'No'). If user click on 'Yes' then it will
     * pass tag's data(tag id) to 'deleteApi' function otherwise it will close the popup if user clicks on 'Cancel'
     * button.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} o Object which contains the tag added
     * @param {OrgTag} o.tag Tag data to send
     */
    const handleDelete = (o) => {
        confirmAlert({
            message: t("Are you sure want to remove?"), //Localization[lang].CONFIRM_REMOVE,
            confirmLabel: t("Confirm "),
            cancelLabel: t("Cancel"),
            buttons: [
                {
                    label: t("Yes"),
                    onClick: () => {
                        deleteApi(o)
                    }
                },
                {
                    label: t("No"),
                    onClick: () => {
                        return null
                    }
                }
            ],
        })
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to delete a tag and once the call is successfully completed
     * then it will remove data(tag) and update states(setBadge,updateProfileTrigger) to show instant reflection.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} tag_id Id of the tag to submit
     */
    const deleteApi = (tag_id) => {
        const event_uuid = eventUuid;
        deleteTag({tag_id,event_uuid }).then((res) => {
            if (res.data.status) {
                setBadge({...event_badge, 'tags_data': res.data.data})
                updateProfileTrigger({...event_badge, 'tags_data': res.data.data}, eventUuid)
                alert && alert.show(t("notification:flash msg rec delete 1"), {
                    type: "success"
                })
            } else {
                alert && alert.show(res.data.msg, {
                    type: "error"
                })
            }
        }).catch((err) => {
            console.error(err)
            alert && alert.show(Helper.handleError(err), {
                type: "error",
            })
        })
    }


    return (
        <form onSubmit={handleSubmit(formSubmit)} role="form" className="default-form wigywig-block13">
            <EventTagList isEditable={true} isLoading={isLoading} onDelete={handleDelete} data={tagData.used_tag} />
            <AlertContainer ref={msg} {...Helper.alertOptions} />
            <div className="col-md-12 col-sm-12 proTag-p">
                <p>{t("many interest")}</p>
            </div>
            <div className="col-md-9 col-sm-8">
                <div className="row">
                    <div className="form-group">
                        <Field
                            name="tag"
                            props={{
                                infoLabel: "",
                                data: (tagData) ? tagData.unused_tag : []
                            }}
                            component={InputComponant}
                            type="text"
                            autocapitalize="none"
                            validate={[Validation.required]}
                        />
                    </div>
                </div>
            </div>
            <div className="col-md-2 col-sm-2  p-0 plus-select">
                <ProgressButton
                    type="submit"

                    controlled={true}
                    // onClick={this.handleEvent}
                    state={buttonState.buttonState}
                >
                    <span className="text-white" data-for="badge-tooltip" data-tip={t("Add a new tag")}>+</span>
                </ProgressButton>
            </div>
        </form>


    );
};

const mapDispatchToProps = (dispatch) => {
    return {
        setData: (data) => dispatch(newInterfaceActions.NewInterFace.setTagsData(data)),
        updateProfileTrigger: (data, id) => dispatch(newInterfaceActions.NewInterFace.updateProfileTrigger(data, id)),
        getRecord: () => dispatch(eventActions.Event.getTag()),
        deleteTag: (data) => dispatch(eventActions.Event.deleteTag(data)),
        addTag: (data) => dispatch(eventActions.Event.addTag(data)),
        setBadge: (data) => dispatch(newInterfaceActions.NewInterFace.setBadgeData(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        event_badge: state.NewInterface.interfaceBadgeData,
    }
}

EventTag = reduxForm({
    form: "EventTag", // a unique identifier for this form
    enableReinitialize: true,
    keepDirtyOnReinitialize: true
    // validate, // <--- validation function given to redux-form
    // warn // <--- warning function given to redux-form
})(EventTag);

EventTag = connect(
    mapStateToProps,
    mapDispatchToProps
)(EventTag);


export default EventTag
