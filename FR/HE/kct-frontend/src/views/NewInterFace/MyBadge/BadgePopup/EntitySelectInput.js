import React, {useEffect, useRef, useState} from 'react'
import _ from 'lodash';
import {connect} from 'react-redux'
import AsyncSelect from 'react-select/async';
import {ReactComponent as EyesOpen} from "../../../../images/eye-variant-with-enlarged-pupil.svg";
import {ReactComponent as EyeCross} from '../../../../images/eye-cross2.svg'
import {Provider as AlertContainer } from 'react-alert';
import {confirmAlert} from 'react-confirm-alert';
import Helper from '../../../../Helper';
import KeepContactagent from '../../../../agents/KeepContactagent.js';
import 'react-confirm-alert/src/react-confirm-alert.css';
import {getAlphaValidator} from '../../../../functions/CustomValidators.js';
import newInterfaceActions from '../../../../redux/actions/newInterfaceAction';
import ReactTooltip from 'react-tooltip';
import {useTranslation} from 'react-i18next';
import useForceUpdate from 'use-force-update';
import {useParams} from "react-router-dom";

const lang = Helper.currLang;

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to update union, union position, and company details from badge editor
 * component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} props.t To provide the locale translation from i18n
 * @param {Boolean} props.hideEye To indicate to hide or show the eye icon
 * @param {Boolean} props.userView To indicate if other user is viewing this badge so hide the false visible field
 * @param {Boolean} props.visibility Visibility value for the current entity field
 * @param {String} props.visibilityType Type of field for the visibility
 * @param {String} props.entityType Type of the entity
 * @param {Function} props.updateCompany To update the current selected company
 * @param {Boolean} props.entityTypeId Database value for current entity type
 * @param {Object} props.alert Reference object for displaying notification popup
 * @param {Entity} props.selectedEntity Current entity object
 * @param {String} props.position Position of the user in current entity
 * @param {Function} props.updateProfileTrigger To update the profile data on backend server
 * @returns {JSX.Element}
 * @constructor
 */
const EntitySelectInput = (props) => {

    const {event_uuid} = useParams();

    const [selectedEntity, setSelectedEntity] = useState(null);
    const [type, setType] = useState(props.entityType);
    const [entityId, setEntityId] = useState('');
    const [position, setPosition] = useState(props.position ? props.position : '')
    const [position_old, setPosition_old] = useState(props.position ? props.position : '')
    const [member_type, setMember_type] = useState(props.unionMemberType);
    const [newCompanyNameString, setNewCompanyNameString] = useState('');
    const [entityChanged, setEntityChanged] = useState(false);
    const [entityData, setEntityData] = useState();
    const [currentData, setCurrentData] = useState();
    const [editMode, setEditMode] = useState();
    const [step, setStep] = useState();
    const [isAddNewCompanyShow, setIsAddNewCompanyShow] = useState()

    const validator = useRef(getAlphaValidator());
    const forceUpdate = useForceUpdate();
    const msg = useRef(null);

    const {t} = useTranslation(['myBadgeBlock', 'notification'])

    useEffect(() => {
        if (props.selectedEntity) {
            setSelectedEntity(props.selectedEntity);
            setPosition((props.position) ? props.position : '');
            setPosition_old((props.position) ? props.position : '');
            setMember_type((props.memberType) ? props.memberType : '')
        }

    }, [])


    useEffect(() => {
        setSelectedEntity(props.selectedEntity);
        // setPosition((props.position) ? props.position : '');
        setPosition_old((props.position) ? props.position : '');
    }, [props, props.position, props.selectedEntity])


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle the focused input fields(from union and company fields) and update its value
     * as per it's type.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const setSelect = () => {
        let updateData = [];
        if (entityData != undefined) {
            if (type == 'company') {
                updateData = (entityData != null) ? entityData.company : []
                setSelectedEntity(updateData === null
                    ? null
                    : {
                        value: updateData.entity_id,
                        label: updateData.long_name,
                        data_id: updateData.data_id,
                        position: updateData.position
                    }
                );
                setCurrentData(updateData);
                setEditMode(updateData === null ? true : updateData.entity_id !== undefined ? false : true);
                setPosition(updateData === null ? null : !_.isEmpty(updateData.position) ? updateData.position : '');

            } else if (type == 'union') {

                updateData = (entityData != null) ? entityData.unions : []
                if (updateData.length > 0 && (updateData[step] != undefined)) {
                    let data = updateData[step];
                    data.position = (updateData.length > 0 && (updateData[step].position)) ? updateData[step].position : '';
                    let currentData = currentData
                    currentData.push(data)
                    let selectedEntity = {value: data.id, label: data.long_name, data_id: updateData[step].data_id}

                    setSelectedEntity(selectedEntity)
                    setCurrentData(currentData)
                    setEditMode(updateData[step].entity_id !== undefined ? false : true)
                    setPosition(updateData.length > 0 && (updateData[step].position) ? updateData[step].position : '')
                }
            }
        }
    }


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to remove company and union details(if user added already) from
     * badge editor component.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const handleDelete = (e) => {
        const postData = new FormData();
        postData.append('entity_id', selectedEntity.value);
        postData.append('_method', 'DELETE')
        postData.append('event_uuid', event_uuid);
        confirmAlert({
            message: t("Are you sure want to remove?"), //Localization[lang].CONFIRM_REMOVE,
            confirmLabel: t("Confirm"),
            cancelLabel: t("Cancel"),
            buttons: [
                {
                    label: t("Yes"),
                    onClick: () => {
                        KeepContactagent.Event.removeEntityUser(postData).then((res) => {
                            if (res.data.status) {
                                props.updateCompany(res.data.data, (type == 'union') ? 'unions' : _.lowerCase(type))
                            } else {
                                props.alert.show(t("notification:flash msg rec delete 0"), {
                                    type: 'error', onClose: () => {
                                    }
                                });
                            }
                        }).catch((err) => {
                            props.alert && props.alert.show(Helper.handleError(err));

                        })
                        setSelect();
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
     * @description This function will handle updates in union and company details and make an API call to save updates.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} updateType Type of field to update e.g. position
     * @returns {Boolean}
     */
    const updateSelectedEntity = (updateType) => {

        let data = {}
        // stop blank position update
        if (updateType == 'position' && (position_old == position)) {
            return false
        }
        if (validator.current.allValid()) {

            if (_.lowerCase(type) == 'company') {
                if (selectedEntity.addNew) {
                    data = {
                        entity_type: 2,
                        entity_name: selectedEntity.value,
                        position: "",
                        entity_old_id: (
                            entityData != null
                            && entityData.unions[step] != undefined
                        )
                            ? entityData.unions[step].entity_id
                            : selectedEntity.value,
                    }
                } else {
                    data = {
                        entity_type: 2,
                        entity_id: selectedEntity.value,
                        position: position,
                    }

                }
            }
            if (type == 'union') {

                if (selectedEntity.addNew) {
                    data = {
                        entity_type: 3,
                        entity_name: (
                            selectedEntity.value != undefined
                                ? selectedEntity.value
                                : entityData.unions[step].entity_id
                        ),

                        position: '',
                        entity_old_id: (
                            entityData != null
                            && entityData.unions[step] != undefined
                        )
                            ? entityData.unions[step].entity_id
                            : selectedEntity.value,
                    }
                } else {
                    data = {
                        entity_type: 3,
                        entity_id: (
                            selectedEntity.value != undefined
                                ? selectedEntity.value
                                : entityData.unions[step].entity_id
                        ),
                        position: position,
                        entity_old_id: (
                            entityData != null
                            && entityData.unions[step] != undefined
                        )
                            ? entityData.unions[step].entity_id
                            : selectedEntity.value,
                    }
                }
            }
            // fatch api for upload data
            const postData = new FormData();
            Object.keys(data).map((keyName) => {
                postData.append(keyName, data[keyName])
            })
            postData.append('_method', 'PUT')
            postData.append('event_uuid', event_uuid);
            KeepContactagent.Event.addEntityUser(postData).then((res) => {
                if (res.data.status == true) {
                    const {entityId} = props;

                    props.updateCompany(res.data.data, (type == 'union') ? 'unions' : _.lowerCase(type))
                    props.alert.show(t("notification:rec add 1"), {type: 'success'});
                } else {
                    props.alert && props.alert.show(t("notification:flash msg rec add 0"), {type: 'error'});
                }
            }).catch((err) => {
                console.error(err)
                props.alert && props.alert.show(Helper.handleError(err), {type: 'error'});
                setSelectedEntity(null)
            })
        } else {
            validator.current.showMessages();
            forceUpdate();
        }
    }


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on a field(union & company) to enter details.This function
     * will change entity type as per selected input field and start updating it's value.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Entity} selectedEntity Current selected entity to store in state
     */
    const handleSelectChange = selectedEntity => {
        if (selectedEntity.addNew) {
            let select = {...selectedEntity, label: selectedEntity.label.split('?')[1]}
            select = {...select, value: select.value.charAt(0).toUpperCase() + select.value.slice(1)};
            setSelectedEntity(select);
            setEntityChanged(true);
        } else {
            setSelectedEntity(selectedEntity);
            setEntityChanged(true)
        }
    }

    useEffect(() => {
        if (selectedEntity) {
            updateSelectedEntity('entity')
        }
    }, [entityChanged])


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This Method generate option/suggestions(if added already) for select box after data is fetched from
     * API of User filter.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} val Value of the name to search to load
     * @param {String} type Type of option to load
     * @return {Entity}
     */
    const loadOptions = (val, type) => {
        if (!val) {
            return Promise.resolve({options: [{value: "12", label: "mukesh"}]});
        }
        // else {
        let inputVal = val //.replace(/\W/g, '');
        if (inputVal.length > 2) {
            return KeepContactagent.EntityTypeSearch.searchEntityUser(inputVal, props.entityTypeId).then((res) => {
                if (res.data.data.length > 0) {
                    let count = 0;
                    let flag = true
                    let options = res.data.data.map((value) => {

                        if (value.long_name && value.long_name == val) {
                            flag = false;
                        }
                        if (selectedEntity && selectedEntity.label.toLowerCase() == val.toLowerCase()) {
                            flag = false;
                        }

                        if (value.long_name && val.length <= value.long_name.length) {
                            count = count + 1;
                        }

                        return {
                            value: value.id,
                            label: value.long_name,
                            old_id: (selectedEntity) ? selectedEntity.id : 0
                        };
                    })
                    if (options.length == count && flag) {
                        options.push({
                            value: inputVal,//value.id,
                            label: `${t("Are You Sure?")} ${inputVal}`,
                            addNew: true
                        });
                    }

                    return options;
                } else {
                    let options =
                        [{
                            value: inputVal,//value.id,
                            label: `${t("Are You Sure?")} ${inputVal}`,
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
     * -------------------------------------------------------------------------------------------------------------------
     * @description function handles selected position and update the state by calling 'updateSelectedEntity' function.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const savePosition = () => {
        updateSelectedEntity('position');
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function handles selected position changes in input field and update
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {SyntheticEvent} e Javascript Event Object
     */
    const handlePositionChange = (e) => {
        const position = Helper.jsUcfirst2(e.target.value);
        setPosition(position)
    }

    if (props.userView) {
        return (<div className="editable-select skill-inputfield">
                <AlertContainer ref={msg} {...Helper.alertOptions} />
                <div className={`clearfix d-flex bform-head p-relative ${'pr-10'}`}>
                    <AsyncSelect
                        maxMenuHeight="120px"
                        cache={false}
                        autoBlur={true}
                        autoLoad={true}
                        autoSize={false}
                        matchPos='any'
                        name='entitySearch'
                        value={selectedEntity}
                        onChange={handleSelectChange}
                        loadOptions={(val) => loadOptions(val, type)}
                    />
                    {(isAddNewCompanyShow == true) &&
                    <span>{`${newCompanyNameString} will be created as a new company`}</span>
                    }
                    {(selectedEntity) &&
                    <div className="form-group black-form child-form-group clearfix">
                        <div className="clearfix p-relative w-100 pr-10">
                            <div className="bform">
                                <input
                                    onBlur={savePosition}
                                    placeholder={t("Position")}
                                    className="form-control no-border form-control-xs"
                                    type="text"
                                    autocapitalize="none"
                                    value={position}
                                    name="position"
                                    onChange={handlePositionChange}
                                />
                            </div>
                        </div>
                    </div>
                    }
                    {(selectedEntity) &&
                    <span onClick={handleDelete} className="fa trash-btn fa-times "></span>
                    }
                </div>
            </div>
        )
    }
    return (
        <div className="editable-select skill-inputfield">
            <AlertContainer ref={msg} {...Helper.alertOptions} />
            <div className={`clearfix d-flex p-relative ${'pr-10'}`}>
                <AsyncSelect
                    maxMenuHeight="120px"
                    cache={false}
                    autoBlur={true}
                    autoLoad={true}
                    autoSize={false}
                    matchPos='any'
                    name='entitySearch'
                    value={selectedEntity}
                    onChange={handleSelectChange}
                    loadOptions={(val) => loadOptions(val, type)}
                />
                {(isAddNewCompanyShow == true) &&
                <span>{`${newCompanyNameString} will be created as a new company`}</span>
                }
                {(selectedEntity && !props.hideEye) &&
                <>
                    <div className="eyeposition2">
              <span
                  className="eyepop "
                  data-for="eye-icon"
                  data-tip={
                      props.entityType == 'company'
                          ? t("Hide your company information")
                          : t("Hide your union information")
                  }
                  onClick={() => props.visibility(props.visibilityType)}
              >
                {(props.eyeState) ?
                    <EyesOpen /> : <EyeCross />
                }
              </span>
                    </div>
                    <ReactTooltip type="dark" id="eye-icon" effect="solid" />
                </>
                }
                {(selectedEntity) &&
                <>
            <span
                onClick={handleDelete}
                data-for="trash-icon"
                data-tip={
                    props.entityType == 'company'
                        ? t("Remove your company information")
                        : t("Remove your union information")
                }
                className="fa trash-btn fa-trash "
            ></span>
                    <ReactTooltip type="dark" id="trash-icon" effect="solid" />
                </>
                }
            </div>

            {(selectedEntity) &&
            <div className="form-group child-form-group clearfix">
                <div className="clearfix p-relative w-100 pr-10">
                    <div className="row">
                        <div className="col-xs-12 col-sm-12 badge2-position">
                            <input
                                placeholder={t("Position")}
                                onBlur={savePosition}
                                className="form-control no-border form-control-xs"
                                type="text"
                                autocapitalize="none"
                                value={position}
                                name="position"
                                onChange={handlePositionChange}
                            />
                        </div>
                    </div>
                </div>
            </div>
            }
        </div>
    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        updateProfileTrigger: (data, id) => dispatch(newInterfaceActions.NewInterFace.updateProfileTrigger(data, id)),
    }
}

const mapStateToProps = (state, ownProps) => {
    return {
        selectType: ownProps.entityType,
    }
}

EntitySelectInput.defaultProps = {
    paddingRight: true
}

export default connect(mapDispatchToProps, mapStateToProps)(EntitySelectInput);
