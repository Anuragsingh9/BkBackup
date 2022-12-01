import React, {Component} from 'react'
import {connect} from 'react-redux'
// import { CrmConstant as CRM } from '../../redux/actions/types';
// import Select from 'react-select';
import AsyncSelect from 'react-select/async';
// import 'react-select/dist/react-select.css';
// import Localization from '../../../../localization.js'
// import { cursor } from '../../constants/StyleConstants';
// import CrmAgent from '../../agents/CRMAgent'
import AlertContainer from 'react-alert'
import {confirmAlert} from 'react-confirm-alert';
import {KCTLocales} from '../../../../localization/index.js'
import Helper from '../../../../Helper';
import Svg from '../../../../Svg';
import KeepContactagent from '../../../../agents/KeepContactagent.js';
import 'react-confirm-alert/src/react-confirm-alert.css';
import {getAlphaValidator} from '../../../../functions/CustomValidators.js';
import _ from 'lodash';

const lang = Helper.currLang

/**
 * @deprecated
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description The entity selector component to provide the drop down from where user can search and select the entity
 * if entity is created it will visible in search list else it will provide option to create that name entity
 * ---------------------------------------------------------------------------------------------------------------------
 *
 *
 *
 * @class
 * @component
 * @constructor
 */
class EntitySelect extends Component {

    /**
     * @param {Object} props Props passed from parent component
     * @param {String} props.entityType Type of entity
     * @param {Entity} props.personBelongsTo Current entity data
     * @param {Function} props.step Method to execute on next step
     * @param {Function} props.mainType Type of the main entity
     * @param {Number} props.entityTypeId Type of entity union-1 company-2
     * @param {Function} props.updateState To update the entity data of user to parent component
     * @param {Number} props.entityId Id of entity
     * @param {Function} props.getPersonBelongs To get the current updated entity of user
     * @param {Function} props.handleSelectChange Parent component handler when selected entity is updated
     * @param {Entity} props.entityData Current entity data
     * @param {String} props.paddingRight Design apply passed padding value
     */
    constructor(props) {
        super(props);
        this.validator = getAlphaValidator();
        this.state = {
            selectedEntity: null,
            editMode: true,
            type: this.props.entityType,
            entityId: '',
            entityData: this.props.personBelongsTo,
            companyDependancy: {},
            currentData: [],
            step: this.props.step,
            isAddNewCompanyShow: false,
            newCompanyNameString: '',
            componant: null,
            mainType: this.props.mainType,
            entityChanged: false,
            position: '',
            member_type: 0,
            initialPosition: '',
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Component life cycle method when the component is mounted set the data from props to local state
     * -----------------------------------------------------------------------------------------------------------------
     * @method
     */
    componentDidMount() {
        // getPersonBelongs={this.getPersonBelongs} personBelongsTo={this.state.personBelongsTo}
        if (this.props.entityId != undefined) {
            this.setState({
                entityId: this.props.entityId,
                entityData: this.props.personBelongsTo,
                componant: (this.props.componant) ? this.props.componant : null
            }, () => {
                this.setSelect();
            })

        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the company data in local component state
     * -----------------------------------------------------------------------------------------------------------------
     * @param {Entity} data Entity data to update
     */
    updatingCompData = (data) => {
        if (this.props.entityId != undefined) {
            this.setState({
                entityId: this.props.entityId,
                entityData: data,
                componant: (this.props.componant) ? this.props.componant : null
            }, () => {
                this.setSelect()
            })

        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Set select box on existing data
     * -----------------------------------------------------------------------------------------------------------------
     */
    setSelect = () => {
        let updateData = [];
        if (this.state.entityData != undefined) {
            if (this.state.type == 'Company') {
                updateData = (this.state.entityData != null) ? this.state.entityData.company : []

                this.setState({
                    selectedEntity: updateData === null ? null : {
                        value: updateData.entity_id,
                        label: updateData.long_name,
                        data_id: updateData.data_id,
                        position: updateData.position
                    },
                    currentData: updateData,
                    editMode: updateData === null ? true : updateData.entity_id !== undefined ? false : true,
                    position: (updateData === null ? null : !_.isEmpty(updateData.position) /*!== undefined*/) ? updateData.position : '',
                })
            } else if (this.state.type == 'Union') {

                updateData = (this.state.entityData != null) ? this.state.entityData.unions : []
                if (updateData.length > 0 && (updateData[this.state.step] != undefined)) {
                    let data = updateData[this.state.step];
                    data.position = (updateData.length > 0 && (updateData[this.state.step].position)) ? updateData[this.state.step].position : '';
                    let currentData = this.state.currentData
                    currentData.push(data)
                    let selectedEntity = {
                        value: data.id,
                        label: data.long_name,
                        data_id: updateData[this.state.step].data_id
                    }
                    this.setState({
                        selectedEntity,
                        currentData,
                        editMode: updateData[this.state.step].entity_id !== undefined ? false : true,
                        position: (updateData.length > 0 && (updateData[this.state.step].position)) ? updateData[this.state.step].position : '',
                    })
                }
            } else if (this.state.type == 'Instance') {

                updateData = (this.state.entityData.personBelongsTo != null) ? this.state.entityData.personBelongsTo.instance : []
                if (updateData.length > 0 && (updateData[this.state.step] != undefined)) {
                    let data = updateData[this.state.step];
                    data.position = (updateData.length > 0 && (updateData[this.state.step].position)) ? updateData[this.state.step].position : '';
                    let currentData = this.state.currentData
                    currentData.push(data)
                    let selectedEntity = {
                        value: data.id,
                        label: data.long_name,
                        data_id: updateData[this.state.step].data_id
                    }
                    this.setState({
                        selectedEntity,
                        currentData,
                        editMode: false,
                        position: (updateData.length > 0 && (updateData[this.state.step].position)) ? updateData[this.state.step].position : '',
                    })
                }
            } else if (this.state.type == 'Press') {
                updateData = (this.state.entityData.personBelongsTo != null) ? this.state.entityData.personBelongsTo.press : []
                this.setState({
                    selectedEntity: (updateData.length > 0) ? {
                        value: updateData[this.state.step].id,
                        label: updateData[this.state.step].long_name,
                        data_id: updateData[this.state.step].data_id
                    } : null,
                    currentData: updateData,
                    editMode: (updateData.length > 0) ? false : true,
                    position: (updateData.length > 0 && (updateData[this.state.step].position)) ? updateData[this.state.step].position : '',
                })
            }
        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the delete entity
     * -----------------------------------------------------------------------------------------------------------------
     * @param {SyntheticEvent} e Javascript Event Object
     * @deprecated
     */
    handleDelete = (e) => {
        let data = null
        if (this.state.type == 'Company') {
            data = (this.state.entityData != null) ? this.state.entityData.company : null
        } else if (this.state.type == 'Union') {
            data = (this.state.entityData != null) ? this.state.entityData.unions[this.state.step] : null
        } else if (this.state.type == 'Press') {
            data = (this.state.entityData != null) ? this.state.entityData.personBelongsTo.press[this.state.step] : null
        } else {
            data = (this.state.entityData.personBelongsTo != null) ? this.state.entityData.personBelongsTo.instance[this.state.step] : null
        }
        const postData = new FormData();
        postData.append('entity_id', data.entity_id);
        postData.append('_method', 'DELETE')

        if (data == null) {
            return this.setSelect();
        } else {
            confirmAlert({
                message: KCTLocales.CONFIRM_REMOVE, //Localization[lang].CONFIRM_REMOVE,
                confirmLabel: KCTLocales.CONFIRM_OK,
                cancelLabel: KCTLocales.CONFIRM_CANCEL,
                buttons: [
                    {
                        label: KCTLocales.COMMONS.YES,
                        onClick: () => {
                            this.props.getPersonBelongs(null, null, this.updatingCompData)
                            KeepContactagent.Event.removeEntityUser(postData).then((res) => {
                                if (res.data.status) {
                                    //   const { entityId } = this.props;
                                    //   const type = this.props.entityData.type;
                                    this.props.updateState(res.data.data)
                                    //   this.updatingCompData(res.data.data)
                                    //   ;
                                } else {
                                    this.msg.show(Helper.alertMsg.FLASH_MSG_REC_DELETE_0, {
                                        type: 'error', onClose: () => {

                                        }
                                    });
                                }
                            }).catch((err) => {
                                this.msg && this.msg.show(Helper.handleError(err));

                            })
                            this.setSelect();
                        }
                    },
                    {
                        label: KCTLocales.COMMONS.NO,
                        onClick: () => {
                            return null
                        }
                    }
                ],

            })

        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Component life cycle method when the error is thrown
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Error} err Error event from Javascript
     */
    componentDidCatch(err) {
        console.error(err);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the current selected entity value, this will update the new selected entity to current
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param e
     */
    updateSelectedEntity = (e) => {
        let {selectedEntity, type, entityId} = this.state
        const eventId = this.state.selectedEntity.value != undefined
            ? this.state.selectedEntity.value
            : this.state.entityData.unions[this.state.step].entity_id

        var data = {entity_type: this.props.entityTypeId, entity_id: selectedEntity.value, person_id: entityId}

        if (this.validator.allValid()) {

            if (this.state.type == 'Company') {
                if (this.state.selectedEntity.addNew) {
                    var data = {
                        entity_type: 2,
                        entity_name: selectedEntity.value,
                        // entity_id: (this.state.selectedEntity.value != undefined ? this.state.selectedEntity.value : this.state.entityData.unions[this.state.step].entity_id),
                        position: this.state.position === null ? "" : this.state.position,
                        entity_old_id: (
                            this.state.entityData != null
                            && this.state.entityData.unions[this.state.step] != undefined
                        )
                            ? this.state.entityData.unions[this.state.step].entity_id
                            : this.state.selectedEntity.value,
                    }
                } else {
                    var data = {
                        entity_type: 2,
                        entity_id: selectedEntity.value,
                        position: this.state.position,
                    }
                }
            }
            if (this.state.type == 'Union') {
                if (this.state.selectedEntity.addNew) {
                    var data = {
                        entity_type: 3,
                        entity_name: (
                            this.state.selectedEntity.value != undefined
                                ? this.state.selectedEntity.value
                                : this.state.entityData.unions[this.state.step].entity_id
                        ),
                        position: this.state.position,
                        entity_old_id: (
                            this.state.entityData != null
                            && this.state.entityData.unions[this.state.step] != undefined
                        )
                            ? this.state.entityData.unions[this.state.step].entity_id
                            : this.state.selectedEntity.value,
                    }
                } else {
                    var data = {
                        entity_type: 3,
                        entity_id: (
                            this.state.selectedEntity.value != undefined
                                ? this.state.selectedEntity.value
                                : this.state.entityData.unions[this.state.step].entity_id
                        ),
                        position: this.state.position,
                        entity_old_id: (
                            this.state.entityData != null
                            && this.state.entityData.unions[this.state.step] != undefined
                        )
                            ? this.state.entityData.unions[this.state.step].entity_id
                            : this.state.selectedEntity.value,
                    }
                }
            }
            if (this.state.type == 'Instance') {
                var data = {
                    type: this.props.entityData.type,
                    entity_type: type.toLowerCase(),
                    entity_id: selectedEntity.value, person_id: entityId,
                    entity_old_id: (
                        this.state.entityData.personBelongsTo != null
                        && this.state.entityData.personBelongsTo.instance[this.state.step] != undefined
                    ) ? this.state.entityData.personBelongsTo.instance[this.state.step].id : 0,
                    position: this.state.position,
                }
            }
            if (this.state.type == 'Press') {
                var data = {
                    type: this.props.entityData.type,
                    entity_type: type.toLowerCase(),
                    entity_id: selectedEntity.value,
                    person_id: entityId,
                    entity_old_id: (
                        this.state.entityData.personBelongsTo != null
                        && this.state.entityData.personBelongsTo.press[this.state.step] != undefined
                    ) ? this.state.entityData.personBelongsTo.press[this.state.step].id : 0,
                    position: this.state.position,
                }
            }
            const postData = new FormData();
            Object.keys(data).map((keyName) => {
                postData.append(keyName, data[keyName])
            })
            postData.append('_method', 'PUT')
            KeepContactagent.Event.addEntityUser(postData).then((res) => {
                if (res.data.status == true) {
                    const {entityId} = this.props;
                    this.props.updateState(res.data.data)
                    this.updatingCompData(res.data.data)
                    this.msg.show(Helper.alertMsg.FLASH_MSG_REC_ADD_1, {type: 'success'});
                } else {
                    this.msg && this.msg.show(Helper.alertMsg.FLASH_MSG_REC_ADD_0, {type: 'error'});
                }
            }).catch((err) => {
                this.msg && this.msg.show(Helper.handleError(err), {type: 'error'});
            })
        } else {
            this.validator.showMessages();
            this.forceUpdate();
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Handler when the selected entity is changed
     * -----------------------------------------------------------------------------------------------------------------
     * @param {Entity} selectedEntity New selected entity object
     */
    handleSelectChange = selectedEntity => {
        if (selectedEntity.addNew) {
            let select = {...selectedEntity, label: selectedEntity.label.split('?')[1]}
            this.setState({
                selectedEntity: select,
                entityChanged: true,
            })
        } else {
            this.setState(
                {
                    selectedEntity: selectedEntity,
                    entityChanged: true,
                }
                , () => {
                    this.props.handleSelectChange && this.props.handleSelectChange(selectedEntity);
                });
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Method generate option for select box after data is fetched from API of User filter
     * -----------------------------------------------------------------------------------------------------------------
     * @param {String} val Value of the name to search to load
     * @param {String} type Type of option to load
     */
    loadOptions = (val, type) => {
        if (!val) {
            return Promise.resolve({options: [{value: "12", label: "mukesh"}]});
        }
        // else {
        let inputVal = val //.replace(/\W/g, '');
        if (inputVal.length > 2) {
            //   return Promise.resolve({ options: [{value:"12", label:"mukesh"}] });
            // if (this.state.mainType != undefined && (this.state.mainType == "user" || this.state.mainType == "contact")) {
            return KeepContactagent.EntityTypeSearch.searchEntityUser(inputVal, this.props.entityTypeId).then((res) => {
                if (res.data.length > 0) {
                    let options = res.data.map((value) => {
                        return {
                            value: value.id,
                            label: value.long_name,
                        };
                    })
                    return options;
                } else {
                    let options =
                        [{
                            value: inputVal,//value.id,
                            label: `${KCTLocales.COMMONS.CONFIRM} ${inputVal}`,
                            addNew: true
                        }];
                    return options;
                }
            })
                .catch((err) => {
                    return Promise.reject({options: []});
                })
            // }
            // else {

            //     return KeepContactagent.EntityTypeSearch.searchEntity(inputVal,this.props.entityTypeId).then((res) => {
            //        const data = [{id:"12",long_name:"long_name" }]
            //         let options = res.data.map((value) => {
            //             return {
            //                 value: value.id,
            //                 label: value.long_name,
            //             };
            //         })
            //         this.props.updateState(res.data)
            //         // callback({ options});
            //         return Promise.resolve({ options: [{value:"12", label:"mukesh"}] });

            //     }).catch((err) => {
            //         return Promise.reject({ options: [{value:"12", label:"mukesh"}] });
            //     })
            // }
        }
        // }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the state when resetting the state
     * -----------------------------------------------------------------------------------------------------------------
     */
    handleReset = () => {
        if (this.state.currentData.length) {
            let currentData = {value: this.state.currentData[0].id, label: this.state.currentData[0].long_name}
            let updateData = this.state.currentData;
            this.setState({
                editMode: false,
                selectedEntity: currentData,
                position: (updateData.length > 0 && (updateData[0].position)) ? updateData[0].position : '',
                member_type: (updateData.length > 0 && (updateData[0].membership_type)) ?
                    (updateData[0].membership_type == "Member") ?
                        0
                        : (updateData[0].membership_type == "Staff") && 1
                    : '',
            })
        }
    }

    render() {
        const {selectedEntity} = this.state;
        const {paddingRight} = this.props.paddingRight
        return (
            <div className="editable-select skill-inputfield">
                <AlertContainer ref={a => this.msg = a} {...Helper.alertOptions} />
                <div className={`clearfix p-relative ${paddingRight && 'pr-10'}`}>
                    <div className="input-group form-group">
                        <AsyncSelect
                            cache={false}
                            autoBlur={true}
                            autoLoad={true}
                            autoSize={false}
                            matchPos='any'
                            name='entitySearch'
                            value={selectedEntity}
                            onChange={this.handleSelectChange}
                            loadOptions={(val) => this.loadOptions(val, this.state.type)}
                        />
                        {
                            (this.state.isAddNewCompanyShow == true) &&
                            <span>{`${this.state.newCompanyNameString} will be created as a new company`}</span>
                        }

                        {this.renderControllButton()}
                    </div>
                    {/* <div className="skill-action-btns">
                        <div className="edit-rfield">
                            <span  className="svgicon" onClick={this.handleReset} dangerouslySetInnerHTML={{ __html: Svg.ICON.close }}></span>
                        </div>
                    </div> */}
                </div>

                {/* {this.state.type == 'Union' &&
                    <div className="clearfix p-relative w-100">
                            <div className="row">
                                <div className="col-xs-12 col-sm-3 pt-5">{KCTLocales.COMMONS.TYPE}:</div>
                                <div className="col-xs-12 col-sm-9 form-group pr-60">
                                    <div className="select-cover">
                                        <i className="fa fa-chevron-down site-color" aria-hidden="true"></i>
                                        <select className="form-control" value={this.state.member_type} onChange={(e) => { this.setState({ member_type: e.target.value }) }} >
                                            <option value={0}>{KCTLocales.COMMONS.IS_MEMBER}</option>
                                            <option value={1}>{KCTLocales.COMMONS.IS_STAFF}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                } */}
                <div className="form-group child-form-group pr-50 clearfix">
                    <div className="clearfix p-relative w-100">
                        <div className="row">
                            <label className="control-label col-xs-12 col-sm-12 mt-5"
                                   htmlFor="instance">{KCTLocales.COMMONS.POSITION}:</label>
                            <div className="col-xs-12 col-sm-12">
                                <input className="form-control form-control-xs" autocapitalize="none" type="text"
                                       value={this.state.position} name="position" onChange={(e) => {
                                    this.setState({position: Helper.jsUcfirst2(e.target.value)})
                                }} />
                            </div>
                        </div>
                    </div>
                </div>
                <span className="text-danger">
                    {this.validator.message(
                        "position",
                        this.state.position,
                        "max:60",
                        // false,
                        // {
                        //   required:
                        //     '"' +
                        //     "maxx" +
                        //     // EventLocale.EVENT_TITLE +
                        //     '" ' 
                        //     // Localization.NOT_EMPTY,
                        // }
                    )}
                </span>
            </div>
        )


    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To render the control button
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @returns {JSX.Element}
     */
    renderControllButton = () => {
        return (
            <div className="control-btns input-group-btn">
                <button type="button" className="btn btn-primary" onClick={(e) => {
                    this.state.selectedEntity === null ? e.preventDefault() : this.updateSelectedEntity()
                }}>
                    {/* {KCTLocales.SAVE} */}
                    <span className="svgicon svg-16" dangerouslySetInnerHTML={{__html: Svg.ICON.check}}></span>
                </button>

                {/* <span className={`glyphicon glyphicon-ok`} onClick={(e) => { this.state.selectedEntity === null ? e.preventDefault() : this.updateSelectedEntity() }} style={cursor}></span> */}
                {/* <span style={cursor} className="svgicon" onClick={this.handleReset} dangerouslySetInnerHTML={{__html: Svg.ICON.close}}></span> */}
            </div>
        )
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        // addEnityUser:(entityId, type) => dispatch(CrmAction.Menu.Entity.fetchEntityData(entityId, type)),
        // addPersonData: (data) => dispatch({ type: CRM.ENTITY.PERSON.ADD_PERSON, payload: data }),
    }
}

const mapStateToProps = (state, ownProps) => {

    return {
        // entityData: state.crm.Menu.Entity.entityData,
        selectType: ownProps.entityType,
        // step: ownProps.step,
    }
}
EntitySelect.defaultProps = {
    paddingRight: true
}
export default connect(mapDispatchToProps, mapStateToProps)(EntitySelect);
