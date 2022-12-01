import React, {useEffect, useState} from 'react';
import styled from 'styled-components'
import Droppable from './Droppable.js';
import Draggable from './Draggable.js';
import Helper from '../../../../Helper.js';
import _ from 'lodash';
import CloseIcon from '../../../Svg/closeIcon.js';
import Grid from '@material-ui/core/Grid';
import {Button, Input} from '@material-ui/core';
import {useAlert} from 'react-alert';
import userAction from '../../../../redux/action/apiAction/user.js';
import {useSelector, useDispatch} from 'react-redux';
import "./DragList.css"

const Item = styled.div`
  padding: 8px;
  color: #333;
  background-color: white;
  border-radius: 3px;
`;
const droppableStyle = {
    // backgroundColor: '#f5f5f5',
    width: '100%',
    height: '40px',
    margin: '0px',
};
const draggleStyle = {
    margin: '0px'
};


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to manage the field area component where user can drop selected fields(from
 * left to right in 2nd step of import users process).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Object} props.staticFields Static data for user
 * @param {String} props.staticFields.db_name Database name
 * @param {String} props.staticFields.label Labels(fname,lname,email)
 * @param {Boolean} props.staticFields.required Field required status
 * @param {String[]} props.matchFields Fields to be matched
 * @param {String} props.fileName  Name of the file
 * @param {Function} props.setTemp Function for set the data
 * @param {Function} props.callBack Function used for users and error
 * @param {Function} props.handleBack Function used for remove the current state
 * @returns {JSX.Element}
 * @constructor
 */
const DragList = (props) => {
    const alert = useAlert();
    const dispatch = useDispatch();
    const user_badge = useSelector((state) => state.Auth.userSelfData)
    const [resultData, setFileFields] = useState([]);
    const [staticData, setStatic] = useState(props.staticFields);

    /**
     * -------------------------------------------------------------------------------------------------
     * @description Collects data on first rendering from props
     * -------------------------------------------------------------------------------------------------
     *
     * @method
     */
    useEffect(() => {
        const {callBackData} = props;
        const fileField = props.matchFields;
        ;
        const totalData = fileField.map((val) => {
            return {value: val, connectedField: null};
        })
        setFileFields(totalData)
        if (_.has(callBackData, ['resultData']) && _.has(callBackData, ['staticData'])) {
            setFileFields(callBackData.resultData);
            setStatic(callBackData.staticData);
        }
    }, []);


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle the column values related to column key(get from parameter).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} key Key used for database name
     * @return {null}
     */
    const getCol = (key) => {
        let val = null;
        resultData.filter((item) => {
            if (item.connectedField != null && item.connectedField.data.db_name == key) {
                val = item.value;
            }
        });
        return val;
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will prepare a data object for selected type fields for 3rd step of import user process.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @return {{}}
     */
    const generateData = () => {
        let aliases = {};
        const totalData = props.staticFields.personal_tab;
        totalData.map((val) => {
            if (getCol(val.db_name)) {
                aliases[val.db_name] = getCol(val.db_name);
            }
        })
        return aliases;
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will validate the imported data and prepare an object of errors(if occurring any in
     * fields of data).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @return {boolean}
     */
    const validateData = () => {
        const data = props.staticFields.personal_tab;
        const remainingData = staticData.personal_tab;
        let errorData = [];
        data.map((val) => {
            remainingData.map((item) => {
                if (val.db_name == item.db_name && item.required) {
                    errorData.push(item);
                }
            })
        });

        if (!_.isEmpty(errorData)) {
            const newData = errorData[0];
            alert.show(`${newData.label} is required`, {type: 'error'})
            return false;
        } else {
            return true;
        }
    }


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to submit data after verification step(for 3rd step of import
     * user process).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleFormSubmit = () => {
        const group = _.has(user_badge, ['current_group']) && user_badge.current_group;
        if (validateData()) {
            const data = {
                file_name: props.fileName,
                aliases: generateData(),
                group_key: group.group_key
            }
            try {
                dispatch(userAction.userImportStep2(data)).then((res) => {
                    if (res.data.status) {
                        props.setTemp({resultData: resultData, staticData: staticData});
                        props.callBack({users: res.data.data})
                        alert.show('Successfully Submitted')
                    } else if (res.data.errors) {
                        props.callBack({error: res.data.errors})
                        alert.show('Given data is invalid', {type: 'error'})
                    }
                }).catch((err) => {
                    alert.show(Helper.handleError(err), {type: 'error'})
                })
            } catch (err) {
                alert.show(Helper.handleError(err), {type: 'error'})
            }
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function is used to render error component for specific lines error for imported data file.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} val Single field data
     * @param {String} val.db_name Name of the database
     * @param {Boolean} val.required Value is present or not
     * @return {JSX.Element}
     */
    const renderExcelItem = (val) => {
        if (val.db_name == 'membership_type') {
            return (
                <Item
                    className="matchDragItem">
                    {Helper.showToolTip(val.label + ' ', 'Union')}
                    <span className="text-danger valid-field">
            {(val.required) ? ' *' : ''}
          </span>
                </Item>
            )
        }
        if (val.db_name == 'entity_sub_type') {
            return (
                <Item
                    className="matchDragItem">
                    {Helper.showToolTip(val.label + ' ', 'Subtype')}
                    <span className="text-danger valid-field">
            {(val.required) ? ' *' : ''}
          </span>
                </Item>
            )

        }
        return (
            <Item
                className={`matchDragItem `}>
                {val.label}
                <span className="text-danger valid-field">
          {(val.required) ? ' *' : ''}
        </span>
            </Item>
        )
    }


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This component is used to render a dropbox component where user can drop fields item from left to
     * right section in step 2 'match fields' of import user process.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} prop Inherited from parent
     * @param {Number} prop.val.value Node ID
     * @param {Object} prop.val.connectedField Data of single field
     * @param {Object} prop.val.connectedField.data.label Labels(fname,lname,email)
     * @param {String} key Value for drop handler
     * @returns {JSX.Element}
     */
    const DropBox = (prop, key) => {
        const item = prop.val;
        return (
            <React.Fragment>
                <Droppable
                    onDropHandler={(formField) => addMatchDropField(item, formField, key)}
                    className="helo"
                    maxElement={1}
                    nodeId={`${item.value}-Droppable`}
                    styles={droppableStyle}
                >
                    {item.connectedField == null ?
                        <div className="itemDragDropHere">Drag Drop Here</div>
                        :
                        <div className="AfterDropHere">{item.connectedField.data.label}</div>
                    }
                </Droppable>
                {
                    item.connectedField != null
                    &&
                    <button type="button" className="transparent-btn" onClick={(e) => removeSelectedValue(item)}>
                        <CloseIcon />
                    </button>
                }
            </React.Fragment>
        )
    }


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle the remove fields action which is drag and dropped in file fields column in
     * 2nd step of import user process.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} item Single filed data
     * @param {Object} item.connectedField.data Single field details
     * @param {String} item.connectedField.data.db_name Database name
     * @param {String} item.connectedField.data.label Labels(fname, lname, email)
     * @param {Boolean} item.connectedField.data.required Field required status
     * @param {Number} item.connectedField.id Id of the field
     */
    const removeSelectedValue = (item) => {
        const data = staticData.personal_tab;
        data.push(item.connectedField.data);
        setStatic({personal_tab: data});
        const fileField = resultData.map((val) => {
            if (val.connectedField == null || (val.connectedField.id != item.connectedField.id)) {
                return val
            } else {
                return {
                    ...val,
                    connectedField: null
                }
            }
        });
        setFileFields(fileField)
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function is used to add fields which were dropped in the dropbox section from left in 2nd step of
     * import users process.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} item Filed container for comparing connected field
     * @param {String} item.connectedField Form field value
     * @param {String} item.value  Value of current item compare with connected field
     * @param {Object} formField  Connected field
     * @param {String} formField.data Connected field data
     * @param {String} key Value for drop field
     */
    const addMatchDropField = (item, formField, key) => {
        if (item.connectedField != null) {
            return;
        }
        const newData = resultData.map((val) => {
            if (item.value == val.value) {
                return {
                    ...val,
                    connectedField: formField
                }
            } else {
                return val
            }
        });
        setFileFields(newData);
        const stat = staticData.personal_tab.filter((data => {
            const field = formField.data;
            if (data.db_name != field.db_name) {
                return data;
            }
        }))
        setStatic({personal_tab: stat});
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This component is developed to render some static fields(required to add a user from the excel file).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @return {JSX.Element}
     */
    const renderStaticField = () => {
        let personalTab = _.has(staticData, ['personal_tab']) ? staticData['personal_tab'] : []

        let personalTabJsx = personalTab.map((val, key) => {

            return (
                <div key={key} className="matchField-right">
                    <Droppable
                        nodeData={val.label}
                        onDropHandler={(formField) => {/*removeMatchDropField(val.db_name, formField, key)*/
                        }}
                        maxElement={1}
                        nodeId={`${val.label}-Droppable`}
                        styles={droppableStyle}
                    >
                        <Draggable styles={draggleStyle} nodeId={`${val.db_name}-Dragger`} nodeData={val}
                                   nodeLabel={val.label}>
                            {renderExcelItem(val)}
                        </Draggable>
                    </Droppable>
                </div>
            )
        })

        return (
            <React.Fragment>
                {personalTabJsx}
            </React.Fragment>
        )
    }

    return (
        <div className="DragDropDiv">
            <Grid container spacing={0} md={10} xs={8}  className="DragDropDivSub">
                <Grid item  className="DragDiv">
                    <div className="drag-list-right-item">
                        <div className="matchDragItem HeadingMatchDragItem">HumannConnect Fields</div>
                        {renderStaticField()}
                    </div>
                </Grid>
                <Grid item  className="DropDiv">
                    <div className="matchDropItem HeadingMatchDropItem">File Fields</div>
                    {resultData.map((val, key) => {

                        return (
                            <div key={key} className="matchField-right">
                                <Grid container className="SubDropDiv">
                                    <Grid item className="SubDropDiv1">
                                        <Input disabled={true} value={val.value} />
                                    </Grid>
                                    <Grid item xs={6} className="SubDropDiv2">
                                        <DropBox val={val} key={key} />
                                    </Grid>
                                </Grid>
                            </div>
                        )
                    })
                    }
                </Grid>
            </Grid>
            <div className="BottomBtnGroup">
                <Button variant="contained" color="primary" onClick={handleFormSubmit}>Next Step</Button>
                <Button onClick={props.handleBack}>Cancel</Button>
            </div>
        </div>
    )
}

export default DragList;