import React, {useEffect, useRef, useState} from 'react';
import AsyncSelect from 'react-select/async';
import {connect} from 'react-redux';
import {Provider as AlertContainer,useAlert } from 'react-alert';
import {confirmAlert} from 'react-confirm-alert';
import _ from 'lodash';
import EventTagList from './EventTagList';
import Helper from '../../../../Helper';
import KeepContactagent from '../../../../agents/KeepContactagent.js';
import eventActions from '../../../../redux/actions/eventActions';
import newInterfaceActions from '../../../../redux/actions/newInterfaceAction';
import {ReactComponent as EyesOpen} from "../../../../images/eye-variant-with-enlarged-pupil.svg";
import {ReactComponent as EyeCross} from '../../../../images/eye-cross2.svg';
import "./UserTagStyle.css";
import {useTranslation} from 'react-i18next';
import ReactTooltip from 'react-tooltip';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render tags(professional & personal) in badge editor component.From this
 * component we can add tags under professional tags & personal tags category and delete tags by clicking on it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {OrgTag[]} props.tagsData Data of the tags for the user
 * @param {Function} props.addTag To add the new tag to list
 * @param {Function} props.deleteTag To delete the tag from the list of tags
 * @param {Function} props.handleSubmit To handle the submit of user for the tags manipulation
 *
 * @returns {JSX.Element}
 * @constructor
 */
const UserTag = (props) => {
    const [state, setState] = useState({
        selectedEntity: null,
        entityChanged: false
    })
    const [isLoading] = useState(false) //loading
    const [userTag, setUserTag] = useState(null);
    const [placeholder, setPlaceholder] = useState(false);
    const {event_badge, updateProfileTrigger, setBadge, type, eventUuid} = props;
    const msg = useAlert();
    const {t} = useTranslation(['myBadgeBlock', 'notification'])


    useEffect(() => {
        if (event_badge && type == 1)
            setUserTag(event_badge.professional_tags)
        else
            setUserTag(event_badge.personal_tags)
    }, [event_badge, type])


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Method generate option/suggestions(if added already) for select box after data is fetched from
     * API of User filter.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} val Value of the search text to search from the api response
     * @return {Object}
     */
    const loadOptions = (val) => {
        let key = val;
        const data = {
            key: val,
            tag_type: type
        };
        const searchParams = new URLSearchParams(data);//user for create query String
        if (key.length > 2) {
            return KeepContactagent.EntityTypeSearch.searchApi(searchParams).then((res) => {
                if (res.data.data.length > 0) {
                    let count = 0;
                    let flag = true
                    let options = res.data.data.map((value) => {

                        if (value.name && value.name == val) {
                            flag = false;
                        }
                        if (state.selectedEntity && state.selectedEntity.label.toLowerCase() == val.toLowerCase()) {
                            flag = false;
                        }
                        if (value.name && val.length <= value.name.length) {
                            count = count + 1;
                        }
                        return {
                            value: value.id,
                            label: value.name,
                            old_id: (state.selectedEntity) ? state.selectedEntity.id : 0
                        };
                    })
                    if (options.length == count && flag) {
                        options.push({
                            value: key,//value.id,
                            label: `${t("Confirm ")}? ${key}`,
                            addNew: true
                        });
                    }
                    return options;
                } else {
                    let options =
                        [{
                            value: key,//value.id,
                            label: `${t("Confirm ")}? ${key}`,
                            addNew: true
                        }];
                    return options;
                }
            })
                .catch((err) => {
                    return Promise.reject({options: []});
                })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will check the added tag's value in any category(professional & personal) is already
     * available or not,If it will added already then it will show it in suggestions other wise it will show an
     * option to add it.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} tag_name Name of the tag to check
     */
    const checkUsedTag = (tag_name) => {
        //mearge professional and personal tag data for check UsedTag.
        if (
            event_badge.professional_tags
            && event_badge.personal_tags
            && !_.isEmpty(event_badge.professional_tags)
            && !_.isEmpty(event_badge.professional_tags)
        ) {
            let allTags = [...event_badge.professional_tags, ...event_badge.personal_tags];
            let name = tag_name.trim();
            let res = !_.isEmpty(allTags)
                && allTags.some((data) => data.name.trim().toLowerCase() == name.toLowerCase());
            return res;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on a field(union & company) to enter details.This
     * function will change entity type as per selected input field and start updating it's value.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Entity} selectedEntity Current Selected entity by user
     */
    const handleSelectChange = selectedEntity => {
        if (selectedEntity.addNew) {
            let select = {...state, label: selectedEntity.label.split('?')[1]}
            setState({
                    selectedEntity: select,
                    entityChanged: true,
                },
            )
            let res = checkUsedTag(select.label);
            if (res) {
                msg && msg && msg.show(t("notification:check used tag"), {
                    type: "error"
                })
                setPlaceholder(true);
            } else {
                create(select.label);
            }
        } else {
            setState(
                {
                    selectedEntity: selectedEntity,
                    entityChanged: true,
                }
            )
            updateApi(selectedEntity.value);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to add tags under both category(personal & professional)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} tag_name Name of the tag
     */
    const create = (tag_name) => {
        let postData = new FormData();
        postData.append('tag_name', tag_name);
        postData.append('tag_type', type);
        props.createTag(postData).then((res) => {
            if (res.data.status) {
                if (type == 1) {
                    setBadge({
                        ...event_badge,
                        professional_tags: [...event_badge.professional_tags, res.data.data]
                    })
                    updateProfileTrigger({
                        ...event_badge,
                        professional_tags: [...event_badge.professional_tags, res.data.data]
                    }, eventUuid)
                } else {
                    setBadge({
                        ...event_badge,
                        personal_tags: [...event_badge.personal_tags, res.data.data]
                    })
                    updateProfileTrigger({
                        ...event_badge,
                        personal_tags: [...event_badge.personal_tags, res.data.data]
                    }, eventUuid)
                }
                msg && msg && msg.show(t("notification:rec add 1"), {
                    type: "success"
                })
                setPlaceholder(true);
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to update tags in both category(personal & professional).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} tag_id Id of the tag to update
     */
    const updateApi = (tag_id) => {
        let postData = new FormData();
        postData.append('tag_id', tag_id);
        postData.append('_method', 'PUT');
        return props.updateTag(postData).then((res) => {
            if (res.data.status) {
                if (type == 1) {
                    setBadge({...event_badge, 'professional_tags': res.data.data})
                    updateProfileTrigger({...event_badge, 'professional_tags': res.data.data}, eventUuid)
                } else {
                    setBadge({...event_badge, 'personal_tags': res.data.data})
                    updateProfileTrigger({...event_badge, 'personal_tags': res.data.data}, eventUuid)
                }
                msg && msg && msg.show(t("notification:rec add 1"), {
                    type: "success"
                })
                setPlaceholder(true);
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will open a popup component and take confirmation to perform delete
     * tag action. That popup component contains 2 button('Yes', 'No'). If user click on 'Yes' then it will
     * pass tag's data(tag id) to 'deleteApi' function otherwise it will close the popup if user clicks on 'Cancel'
     * button.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} o Tag id to delete
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to delete a tag and once the call is successfully completed
     * then it will remove data(tag) and update states(setBadge,updateProfileTrigger) to show instant reflection.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} tag_id Tag id to delete
     */
    const deleteApi = (tag_id) => {
        const postData = new FormData();
        postData.append('tag_id', tag_id);
        postData.append('_method', 'DELETE')
        return props.removeTag(postData).then((res) => {
            if (res.data.status) {
                if (type == 1) {
                    setBadge({...event_badge, 'professional_tags': res.data.data})
                    updateProfileTrigger({...event_badge, 'professional_tags': res.data.data}, eventUuid)
                } else {
                    setBadge({...event_badge, 'personal_tags': res.data.data})
                    updateProfileTrigger({...event_badge, 'personal_tags': res.data.data}, eventUuid)
                }

                msg && msg && msg.show(t("notification:flash msg rec delete 1"), {
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
    return (
        <>
            {userTag && !_.isEmpty(userTag) &&
            <EventTagList
                type={props.type}
                isEditable={true}
                isLoading={isLoading}
                onDelete={handleDelete}
                data={userTag}
            />
            }
            <div className="py-4">
                <p>{props.line}</p>
            </div>
            <AlertContainer ref={msg} {...Helper.alertOptions} />
            <div style={{display: 'flex', marginTop: '1rem'}}
            >
                <AsyncSelect
                    maxMenuHeight="120px"
                    cache={false}
                    autoBlur={true}
                    autoLoad={true}
                    autoSize={false}
                    matchPos='any'
                    name='entitySearch'
                    value={placeholder ? null : state.selectedEntity}
                    onChange={handleSelectChange}
                    loadOptions={(val) => loadOptions(val)}
                />
            </div>
            <div className="col-md-3 col-sm-3 pro-eye" data-tip={props.dataTip}>
                {
                    userTag
                    && !_.isEmpty(userTag)
                    && <span className="eyepop eyeposition1" onClick={() => props.visibility(type)}>
                        {props.eyeState ? <EyesOpen /> : <EyeCross />}
                    </span>
                }

            </div>
            <ReactTooltip type="dark" effect="solid" />
        </>
    )
}

const mapStateToProps = (state) => {
    return {
        event_badge: state.NewInterface.interfaceBadgeData,
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateProfileTrigger: (data, id) => dispatch(newInterfaceActions.NewInterFace.updateProfileTrigger(data, id)),
        setBadge: (data) => dispatch(newInterfaceActions.NewInterFace.setBadgeData(data)),
        createTag: (data) => dispatch(eventActions.Event.createTag(data)),
        removeTag: (tag_id) => dispatch(eventActions.Event.removeTag(tag_id)),
        updateTag: (tag_id) => dispatch(eventActions.Event.updateTag(tag_id)),

    }
}
export default connect(mapStateToProps, mapDispatchToProps)(UserTag);