import React, {useRef, useState} from 'react'
import {connect} from 'react-redux'
import {Provider as AlertContainer, useAlert } from 'react-alert';
import Helper from '../../../Helper';
import {reduxForm,} from "redux-form";
import {useTranslation} from 'react-i18next'
import eventActions from '../../../redux/actions/eventActions';
import newInterfaceActions from '../../../redux/actions/newInterfaceAction';
import './UserTags.css';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to manage event tags on quick registration page.Form this component user
 * can select(by clicking on each tag) tags to showcase his point of interest to other participants in the current
 * event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {OrgTag[]} tagsData Data of the tags for the user
 * @param {Function} addTag To add the new tag to list
 * @param {Function} deleteTag To delete the tag from the list of tags
 * @param {Function} handleSubmit To handle the submit of user for the tags manipulation
 * @returns {JSX.Element}
 * @constructor
 */
let UserTag = ({
                   event_badge,
                   eventUuid,
                   updateProfileTrigger,
                   setBadge,
                   tagsData,
                   addTag,
                   deleteTag,
                   handleSubmit,
                   initialize,
                   event_uuid
               }) => {
    // Initialisation fo message / alert ref to show alerts on success or error.
    let msg = useAlert();
    const [isLoading, setLoading] = useState(false) //loading
    // tag data state
    const [tagData, setTagData] = useState(tagsData)
    // button state for progress button
    const [buttonState, setButtonState] = useState({buttonState: '', controlled: false})
    const {t} = useTranslation('notification');


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle form submission for selected tags and call API handler function(
     * addApi) for it.
     * -----------------------------------------------------------------------------------------------------------------
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to perform tag select action and once the API call execute
     * successfully it will update states to show selected tag on interface.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     * @param {Number} tag_id Id of the tag to submit
     */
    const addApi = (e, tag_id) => {

        if (!e.target.classList.contains('disabled')) {
            e.target && e.target.classList.add("disabled")
            addTag({tag_id, event_uuid}).then((res) => {
                if (res.data.status) {
                    setTagData(res.data.data)
                    msg && msg && msg.show(t("rec add 1"), {
                        type: "success"
                    })
                } else {
                    msg && msg && msg.show(res.data.msg, {
                        type: "error"
                    })
                }
                e.target && e.target.classList.remove("disabled")
                setButtonState({buttonState: '', controlled: true})
            }).catch((err) => {
                console.error(err)
                e.target && e.target.classList.remove("disabled")
                setButtonState({buttonState: '', controlled: true})
                msg && msg && msg.show(Helper.handleError(err), {
                    type: "error",
                })
            })
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle form submission for unselected tags and call API handler function(
     * addApi) for it.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} o Object which contains the tag added
     * @param {OrgTag} o.tag Tag data to send
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const handleDelete = (e, o) => {
        deleteApi(e, o)
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to perform tag unselect action and once the API call execute
     * successfully it will update states to show unselected tag on interface.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     * @param {Number} tag_id Id of tag to delete
     */
    const deleteApi = (e, tag_id) => {

        if (!e.target.classList.contains('disabled')) {
            e.target && e.target.classList.add("disabled")
            deleteTag({tag_id, event_uuid}).then((res) => {
                if (res.data.status) {
                    setTagData(res.data.data)
                    msg && msg && msg.show(t("flash msg rec delete 1"), {
                        type: "success"
                    })
                } else {
                    msg && msg && msg.show(res.data.msg, {
                        type: "error"
                    })
                }
                e.target && e.target.classList.remove("disabled")
            }).catch((err) => {
                e.target && e.target.classList.remove("disabled")
                msg && msg && msg.show(Helper.handleError(err), {
                    type: "error",
                })
            })
        }
    }

    let mergeData = tagData;
    // tags filtering
    if (tagData) {
        mergeData.used_tag = tagData.used_tag.map(o => {
            o.used = 1;
            return o
        })
    }
    // tags merging
    mergeData = tagData ? [...mergeData.used_tag, ...mergeData.unused_tag] : [];
    // tags sorting
    mergeData.sort((a, b) => {
        return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
    })

    return (
        <form onSubmit={handleSubmit(formSubmit)} role="form" className="default-form">
            <EventRender isEditable={true} isLoading={isLoading} onDelete={handleDelete} onAdd={addApi}
                         data={mergeData} />
            <AlertContainer ref={msg} {...Helper.alertOptions} />
        </form>
    );
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a tag component which is developed to show all event tags with selected and unselected user
 * experience.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {OrgTag[]} data Tags data to render on list
 * @param {Function} onDelete Handler for the delete of tag
 * @param {Function} onAdd To add the tag to state from parent component
 * @returns {JSX.Element}
 * @constructor
 */
const EventRender = ({data, onDelete, onAdd}) => {
    return (
        <div id="preview-tag" className="event-tag-list">
            {data && data.map((o) => {
                return (
                    <div
                        key={o.id}
                        onClick={(e) => (!(o.used != undefined && o.used)) ? onAdd(e, o.id) : onDelete(e, o.id)}
                        className={`pop-tags no-border ${(!(o.used != undefined && o.used)) ? 'grey-tags' : ''}`}
                    >
                        {Helper.jsUcfirst(o.name)}
                    </div>)
            })}
        </div>
    )
}

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

UserTag = reduxForm({
    form: "UserTag", // a unique identifier for this form
    enableReinitialize: true,
    keepDirtyOnReinitialize: true
    // validate, // <--- validation function given to redux-form
    // warn // <--- warning function given to redux-form
})(UserTag);

UserTag = connect(
    mapStateToProps,
    mapDispatchToProps
)(UserTag);


export default UserTag
