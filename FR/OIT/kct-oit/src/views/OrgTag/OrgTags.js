import React, {useRef, useState, useEffect} from 'react'

import _, {uniqueId} from 'lodash';
import {connect} from 'react-redux';
import {confirmAlert} from 'react-confirm-alert';
import Chip from '@material-ui/core/Chip';
import './OrgTag.css';
import CreateIcon from '@material-ui/icons/Create';
import {Button, TextField, Container, Grid} from '@material-ui/core';
import CloseIconWhite from './../Svg/CloseIconWhite.js';
import {
    Field,
    reduxForm,
} from "redux-form";
import Validation from '../../functions/ReduxFromValidation';
import Helper from '../../Helper';
import groupAction from '../../redux/action/apiAction/group';
import {useAlert} from 'react-alert';
import {useParams} from 'react-router-dom';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description  Redux's text field reusable component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {Boolean} touched To indicate if the input box is touched or not
 * @param {String} error Error message from input box
 * @param {String} warning Warning message from input box
 * @return {JSX.Element}
 * @constructor
 */
const InputComponant = ({input, meta: {touched, error, warning}}) => {
    return (
        <div className="entertagNameDiv">
            <TextField
                name="tag_name"
                id="filled-start-adornment"
                className="form-control"
                variant="outlined"
                size="small"
                placeholder={'Enter name of the Tag'}
                {...input} />
            {
                touched &&
                ((error && <div><span className="text-danger">{error}</span></div>) ||
                    (warning && <div><span>{warning}</span></div>))
            }
        </div>
    )
}

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This Component is used in Manage Organization Tags page through which an organizer(role) can view all
 * the added organization tags in the account for that specific group. This page also has the feature of adding more
 * organization tags and edit/delete existing organization tags for the current organizer account.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} getRecord To fetch all Event tags of a specific group
 * @param {Function} addTag To add a tag new tag
 * @param {Function} updateTag To update an existing tag
 * @param {Function} deleteTag To remove a tag from a group
 * @param {Function} handleSubmit To prepare the data and make API call to perform CRUD on Event tags
 * @param {Function} initialize To clear the selected tag name so that the tag name can be updated
 * @param {User} user_badge User related data
 * @return {JSX.Element}
 * @constructor
 */
let EventTag = ({
                    getRecord, addTag, updateTag, deleteTag, handleSubmit, initialize, user_badge
                }) => {

    let msg = useAlert() //for alert container msg
    const inputRef = useRef(null);
    const [tagName, setTagName] = useState('')
    const [tagRow, setTagRow] = useState(null)
    const [isLoading, setLoading] = useState(false)
    const [data, setData] = useState([])
    const [buttonState, setButtonState] = useState({buttonState: '', controlled: true})
    const {gKey} = useParams();

    useEffect(() => {
        fetchData()
    }, [])


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description API handler to fetch data(tags data of current group)
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const fetchData = () => {
        const group_id = _.has(user_badge, ['current_group', 'id']) ? user_badge.current_group.id : null;
        //enable loading
        setLoading(true)
        //fetch current id record
        getRecord(gKey).then((res) => {
            if (res.data.status) {
                setData(res.data.data)
            } else {
                // msg && msg && msg.show(res.data.msg, {
                //   type: "error"
                // })
            }
            setLoading(false)
        }).catch((err) => {
            setLoading(false)
        })
    }

    /**
     * ------------------------------------------------------------------------------------------------------------------
     * @description Method to submit data(tag's name) in order to create a tag
     * ------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} o Object for the new tag
     * @param {String} o.tag_name Name of the tage for adding or updating
     */
    const formSubmit = (o) => {
        if (tagRow) {
            updateApi(tagRow, true, o)
        } else {
            addApi(o)
        }
    }

    /**
     * ------------------------------------------------------------------------------------------------------------------
     * @description API handler to update data(existing tag's content)
     * ------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} object Object for updating the tag
     * @param {String} object.tag_name Tag name which need to be updated
     * @param {String} object.tag_id Tag id which need to be updated
     * @param {String} object.is_display Visibility of the tag
     * @param {Boolean} isNameUpdate To check if the name of the tag is updated or not
     * @param {Object} o Object for the tag to be updated
     * @param {String} o.tag_name Entered tag name
     */
    const updateApi = (object, isNameUpdate = false, o = null) => {

        let formData = new FormData()
        formData.append('tag_name', (isNameUpdate) ? o.tag_name : object.tag_name)
        formData.append('tag_id', object.tag_id)
        if (isNameUpdate) {
            formData.append('is_display', object.is_display)
        } else {
            formData.append('is_display', object.is_display ? 0 : 1)
        }

        formData.append('_method', 'PUT')

        setButtonState({buttonState: 'loading', controlled: false})

        updateTag(formData).then((res) => {
            if (res.data.status) {
                initialize({tag_name: ''})
                setTagRow(null)
                updateData(res.data.data);
                // fetchData();
                msg && msg.show('Record Updated', {
                    type: "success"
                })
            } else {
                msg && msg.show(res.data.msg, {
                    type: "error"
                })
            }
            setButtonState({buttonState: '', controlled: true})
        }).catch((err) => {

            msg && msg.show(Helper.handleError(err), {
                type: "error",
            })
            setButtonState({buttonState: '', controlled: true})
        })
    }

    /**
     * ------------------------------------------------------------------------------------------------------------------
     * @description API handler to add data(add new tags)
     * ------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} o Object for the new tag
     * @param {String} o.tag_name Tag name
     */
    const addApi = (o) => {
        setButtonState({buttonState: 'loading', controlled: false})
        const group_id = _.has(user_badge, ['current_group', 'id']) ? user_badge.current_group.id : null;
        let formData = new FormData();
        formData.append('tag_name', o.tag_name)
        formData.append('group_key', gKey)

        updateTag(formData).then((res) => {
            if (res.data.status) {
                initialize({tag_name: ''});
                addNewData(res.data.data);
                // fetchData();
                msg && msg && msg.show('Record Added Successfully', {
                    type: "success"
                })
            } else {
                msg && msg && msg.show(res.data.msg, {
                    type: "error"
                })
            }
            setButtonState({buttonState: '', controlled: true})
        }).catch((err) => {
            console.log(err)
            setButtonState({buttonState: '', controlled: true})
            msg && msg && msg.show(Helper.handleError(err), {
                type: "error",
            })

        })

    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method to call deleteApi and show modal confirmation box before deleting.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} o Object for tag delete
     * @param {Number} o.tag_id Id of the tag which need to be deleted
     */
    const handleDelete = (o) => (e) => {
        confirmAlert({
            message: 'Are you sure want to delete tag?',
            confirmLabel: 'Confirm',
            cancelLabel: 'Cancel',
            buttons: [
                {
                    label: 'Confirm',
                    onClick: () => {
                        deleteApi(o.tag_id)
                    }
                },
                {
                    label: 'Cancel',
                    onClick: () => {
                        return null
                    }
                }
            ],
        })
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method of delete specific manage organizer tag.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id Id of tag to be deleted
     */
    const deleteApi = (id) => {

        let formData = new FormData();
        formData.append('_method', 'DELETE')
        formData.append('tag_id', id)
        updateTag(formData).then((res) => {
            if (res.data.status) {
                filterData(id);
                // fetchData();
                msg && msg && msg.show('Deleted Successfully !', {
                    type: "success"
                })
            } else {
                msg && msg && msg.show(res.data.msg, {
                    type: "error"
                })
            }

        }).catch((err) => {
            msg && msg && msg.show(Helper.handleError(err), {
                type: "error",
            })
        })
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method of update enable/disable condition of a tag which trigger while user click on a specific tag.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} object Object for Toggling the visibility of the tag
     * @param {String} object.tag_name Name of the tag
     * @param {Number} object.tag_id Id of the tag
     * @param {Number} object.is_display Visibility value of the tag
     */
    const toggleDisable = (object) => (e) => {
        let formData = new FormData()
        formData.append('tag_name', object.tag_name)
        formData.append('tag_id', object.tag_id)
        formData.append('is_display', object.is_display ? 0 : 1)
        formData.append('_method', 'PUT')
        updateTag(formData).then((res) => {
            if (res.data.status) {
                updateData(res.data.data);
                msg && msg && msg.show('Record Updated SuccessFully', {
                    type: "success"
                })
            } else {
                msg && msg && msg.show(res.data.msg, {
                    type: "error"
                })
            }
        }).catch((err) => {
            msg && msg && msg.show(Helper.handleError(err), {
                type: "error",
            })
        })
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method which trigger when user click on edit icon of a tag and save current tag data in a state.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} o Object of tag which need to be edited
     * @param {String} o.tag_name Name of the tag
     */
    const handleEdit = (o) => (e) => {
        initialize({tag_name: o.tag_name});
        setTagRow(o);
    }

    /**
     *
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method which is returning a list of customized component of available tags(if any).
     * -------------------------------------------------------------------------------------------------------------------
     * @method
     * @return {JSX.Element}
     */
    const renderData = () => {
        if (isLoading) {
            return <div />
        }

        data.sort((a, b) => a.tag_name.localeCompare(b.tag_name))
        return <ul className="tag-space">

            {data.map((o) => {

                const icon = <CreateIcon onClick={handleEdit(o)} />
                return (
                    <div className="tagName">
                        <CreateIcon onClick={handleEdit(o)} />
                        <Chip
                            deleteIcon={<CloseIconWhite className="WhiteCross" />}
                            className={o.is_display ? `orgTagEnabled` : 'orgTagDisabled'}
                            label={o.tag_name}
                            onDelete={handleDelete(o)}
                            onClick={toggleDisable(o)}
                        />
                    </div>
                )
            })}

        </ul>
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method which is used to update state for  current tags data and previous tags data.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {OrgTag} tagData Object of new tag data
     */
    const addNewData = (tagData) => {
        let newData = [...data];
        newData.push(tagData);
        setData(newData);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description Method which is used to filter tags with their id.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id Id of the tag
     */
    const filterData = (id) => {
        const newData = data.filter((item) => {
            if (item.tag_id != id) {
                return item
            }
        });
        setData(newData);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when API call('updateApi' & 'toggleDisable') will successfully completed
     * to update a tag(name , display state - enable/disable). This will take updated tag's data(from parameter) and
     * update state('setData') so that user can see latest updated tags just after update action.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {OrgTag} tagData Object of the tag which needs to be updated
     */
    const updateData = (tagData) => {
        const {tag_id} = tagData;

        const newData = data.map((item) => {
            if (tag_id == item.tag_id) {
                return {
                    ...tagData,
                    is_display: parseInt(tagData.is_display)
                }
            } else {
                return item;
            }
        });
        setData(newData);

    }

    return (
        <>
            <div className="OrgtagDivMain">
                <form onSubmit={handleSubmit(formSubmit)} role="form">

                    <div className="row">
                        <div className="col-md-6">
                            <div className="form-group">
                                <Field
                                    name="tag_name"
                                    props={{
                                        infoLabel: "",
                                    }}
                                    inputRef={inputRef}
                                    component={InputComponant}
                                    type="text"
                                    validate={[Validation.required, Validation.min2, Validation.max30,]}

                                />


                            </div>
                        </div>
                        <div className="col-md-3 loading-button">
                            <Button
                                type="submit"
                                variant="contained" color="primary"
                                className="mt-0"
                                controlled={buttonState.controlled}
                                // onClick={this.handleEvent}
                                state={buttonState.buttonState}
                            >
                                {(tagRow) ? 'UPDATE' : 'ADD'}
                            </Button>
                        </div>
                        <div className="col-md-6">
                        </div>
                    </div>
                    {renderData()}

                </form>

            </div>
        </>
    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        getRecord: (id) => dispatch(groupAction.getTags(id)),
        updateTag: (id) => dispatch(groupAction.updateTag(id)),
    }
}

const mapStateToProps = (state) => {
    return {
        user_badge: state.Auth.userSelfData
    }
}

EventTag = reduxForm({
    form: "EventTag", // a unique identifier for this form
    enableReinitialize: true,
    keepDirtyOnReinitialize: true
})(EventTag);

EventTag = connect(
    mapStateToProps,
    mapDispatchToProps
)(EventTag);

export default EventTag