import React, {useState, useEffect} from "react";
import {Field, reduxForm} from "redux-form";
import {
    Grid,
    TextareaAutosize,
    TextField,
    FormControl,
    InputLabel,
    Select,
    MenuItem,
    Checkbox,
    Switch,
    Button,
} from "@material-ui/core";
import {useAlert} from "react-alert";
import Helper from "../../../Helper";
import _ from "lodash";
import MultiSelectUse from "../common/MultiSelectUse";
import {connect} from "react-redux";
import {useSelector, useDispatch} from "react-redux";
import groupAction from "../../../redux/action/apiAction/group";
import "./GroupCreation.css";
import {useParams, useHistory} from "react-router-dom";
import UserAutocomplete from "../../Common/UserAutoComplete/UserAutoComplete";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is used to monitor user inputs and throw error if the input values are not matching the
 * proper criteria(eg - no group name and description added)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the multi space values
 * @param {String} values.group_name Group name
 * @param {String} values.type_value Group type value
 * @param {String} values.description Group description text
 */
const validate = (values) => {
    const errors = {};

    const requiredFields = [
        "type_value",
        "groupName",
        // "group_key",
        "function",
        "topic",
        "group_name",
    ];
    var alphaNumaric =
        /^[0-9a-zA-Z\u00E0-\u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ_'\- \s]*$/i;
    requiredFields.forEach((field) => {
        if (!values[field]) {
            errors[field] = "Required";
        }
    });
    //for group key
    // if (values.group_key && values.group_key.length > 10) {
    //   errors["group_key"] = ` value must be less than or equal to 10`;
    // }
    // if (values.group_key && values.group_key.length < 3) {
    //   errors["group_key"] = ` value must be greater than or equal to 3`;
    // }
    // if (values.group_key && !alphaNumaric.test(values.group_key)) {
    //   errors["group_key"] = ` value must contain alpha-numaric values `;
    // }

    // for group name
    if (values.group_name && values.group_name.length > 100) {
        errors["group_name"] = ` value must be less than or equal to 100`;
    }
    if (values.group_name && values.group_name.length < 3) {
        errors["group_name"] = ` value must be greater than or equal to 3`;
    }
    // type for topics
    if (values.type_value && values.type_value.length > 100) {
        errors["type_value"] = ` value must be less than or equal to 100`;
    }
    if (values.type_value && values.type_value.length < 3) {
        errors["type_value"] = ` value must be greater than or equal to 3`;
    }
    // for Description
    if (values.description && values.description.length < 3) {
        errors["description"] = `value must be greater than or equal to 3`;
    }

    if (values.description && values.description.length > 300) {
        errors["description"] = `value must be less than or equal to 300`;
    }

    return errors;
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method returns TextareaAutosize component for redux form.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {String} value Value of the input box
 * @param {String} label Label of text field
 * @param {String} defaultValue Default value of input box
 * @param {Object} invalid Enter value is invalid
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message from input box
 * @param {Object} custom Custom number of input box
 * @returns {JSX.Element}
 */
const renderTextAreaField = ({
                                 input,
                                 value,
                                 label,
                                 defaultValue,
                                 meta: {invalid, touched, error},
                                 ...custom
                             }) => {
    return (
        <React.Fragment>
            <TextareaAutosize
                minRows={3}
                name={input.name}
                value={value}
                className="group_des_input"
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error && invalid}
                {...input}
                {...custom}
            />
            {/* <TextField
                name={input.name}
                value={value}
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error && invalid}

                {...input}
                {...custom}
            /> */}
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method returns renderTextField component for redux form
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {String} value Value of the input box
 * @param {String} label Label of text field
 * @param {String} defaultValue Default value of input box
 * @param {Boolean} invalid Enter value is invalid
 * @param {Boolean} touched To indicate if the input box is touched or not
 * @param {String} error Error message from input box
 * @param {String} custom Custom number of input box
 * @returns {JSX.Element}
 */
const renderTextField = ({
                             input,
                             value,
                             label,
                             defaultValue,
                             meta: {invalid, touched, error},
                             ...custom
                         }) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={value}
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error && invalid}
                {...input}
                {...custom}
            />
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for create group form page that takes information for group creation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Properties that require to submit group creation form in order to create a group.
 * @param {Function} props.handleSubmit This function will handle the form submission of group creation.
 * @param {Function} props.setTabName This function will handle tab name after group creation is completed.
 * @param {Function} props.initialize This function will handle the form's initial data handling.
 * @param {Function} props.setGroup To set the group data in parent component
 * @returns {JSX.Element}
 */
const GroupCraetion = (props) => {
    const {handleSubmit, pristine, reset, submitting, initialize, setTabName} = props;
    const dispatch = useDispatch();
    const {gKey} = useParams();
    const like = useParams();
    const [filterPilotData, setFilterPilotData] = useState()

    const [groupData, setGroupData] = useState({
        typeValue: "",

        groupName: "",
        groupKey: "",
        groupType: "",
        description: "",
    });

    // {id: 6, fname: "Try", lname: "Jack", email: "gourav4@mailinator.com"}
    const [allowManageUser, setAllowManageUser] = useState(false);
    const [allowManagePilot, setAllowManagePilot] = useState(false);
    const [allowDesignSetting, setAllowDesignSetting] = useState(false);
    const [event_type, setEventType] = useState("head_quarters_group");
    //set new pilots ids data
    const [coPilot, setCoPilot] = useState([]);
    //store pilots data from single group api response
    const [coPilotData, setCoPilotData] = useState([]);
    //for getting existing pilots id
    const [existingCoPilotsId, setExistingCoPilotsId] = useState([]);
    const [newPilotId, setNewPilotId] = useState([]);
    const [selectedData, setSelectedData] = useState({
        pilot: '',
        copilot: ''
    });
    const [newPilotName, setNewPilotName] = useState([]);
    const [currentUser, setCurrentUser] = useState([])

    let coPilotIds = [];
    const alert = useAlert();
    const history = useHistory();

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user add some value in group creation form.This function will save
     * updated value with their key name in a state called "setGroupData".
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} prop Name of the key
     * @returns {Function}
     */
    const handleChange = (prop) => (event) => {
        setGroupData({...groupData, [prop]: event.target.value});
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method  handle selected pilot data on select action and create a array of id.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Array} data Array of element keys
     */
    const handleCoPilotChange = (data) => {
        let coPilotsId = [];
        for (let key in data) {
            coPilotsId.push(data[key].id);
        }

        const arr = coPilotsId.filter((c, index) => {
            return coPilotsId.indexOf(c) === index;
        });

        console.log("filter array", arr);
        setCoPilot(coPilotsId);
    };


    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for if user not add new pilot then it adds existing pilots id to send data.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Array} data Array of element keys
     */
    const getCoPilotId = (data) => {
        coPilotIds = [];
        for (let key in data) {
            coPilotIds.push(data[key].id);
        }
        setExistingCoPilotsId(coPilotIds);
    };

    // this hook is used for getting group data form key
    useEffect(() => {
        if (gKey && props.mode !== 'create') {
            getSingleGroup(gKey);
        }
        // {id: 6, fname: "Try", lname: "Jack", email: "gourav4@mailinator.com"}

        const localData = localStorage.getItem("user_data");
        const parseLocalData = JSON.parse(localData);
        const localUserId = parseLocalData.id;
        const localUserFname = parseLocalData.fname;
        const localUserLname = parseLocalData.lname;
        const localUserEmail = parseLocalData.email;


        //  parseLocalData.current_group = props.groupData;
        let currentLocalUser = {
            id: localUserId,
            fname: localUserFname,
            lname: localUserLname,
            email: localUserEmail
        }
        console.log("localll", currentLocalUser)
        setCurrentUser([currentLocalUser])
        // getSingleGroup(1)
    }, []);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to get single group data from server using API call.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} gKey Group key
     */
    const getSingleGroup = (gKey) => {
        try {
            dispatch(groupAction.getSingleGroupData(gKey))
                .then((res) => {
                    const data = res.data.data;
                    props.setGroup  && props.setGroup(data);
                    console.log("get sigle grp", data);
                    setAllowManageUser(data.allow_user == 1 ? true : false);
                    setAllowManagePilot(
                        data.allow_manage_pilots_owner == 1 ? true : false
                    );
                    setAllowDesignSetting(data.allow_design_setting == 1 ? true : false);
                    setGroupData({
                        ...groupData,
                        typeValue: data.type_value ? data.type_value : "",
                        groupName: data.group_name ? data.group_name : "",
                        description: data.description ? data.description : "",
                        groupKey: data.group_key ? data.group_key : "",
                    });
                    setEventType(data.group_type);
                    setTabName(data.group_name ? data.group_name : "");
                    setCoPilotData(data.co_pilots);
                    getCoPilotId(data.co_pilots);
                    setNewPilotName(data.pilots);
                    initialize(data);
                })
                .catch((err) => {
                    if (err && _.has(err.response.data, ["errors"])) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data.msg;
                        for (let key in er) {
                            alert.show(er[key], {type: "error"});
                        }
                        // alert.show(err.response.data.msg,{type:'error'});
                    } else {
                        alert.show(Helper.handleError(err), {type: "error"});
                    }
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for create group and send data of basic details about group and post on server .
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const groupCreate = () => {
        const data = {
            _method: "POST",
            group_type: event_type,
            // group_key: groupData.groupKey,
            group_name: Helper.jsUcfirst(groupData.groupName),
            description: groupData.description,
            type_value: groupData.typeValue,
            pilot: !_.isEmpty(newPilotId) ? newPilotId : [currentUser[0].id],
            co_pilot: coPilot,
            allow_user: allowManageUser == true ? 1 : 0,
            allow_manage_pilots_owner: allowManagePilot == true ? 1 : 0,
            allow_design_setting: allowDesignSetting == true ? 1 : 0,
        };
        try {
            dispatch(groupAction.createGroup(data))
                .then((res) => {
                    const data = res.data.data;
                    alert.show("Group Created Successfully", {type: "success"});
                    history.push(`/${gKey}/manage-groups`);
                })
                .catch((err) => {
                    if (err && _.has(err.response.data, ["errors"])) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data.msg;
                        for (let key in er) {
                            alert.show(er[key], {type: "error"});
                        }
                        // alert.show(err.response.data.msg,{type:'error'});
                    } else {
                        alert.show(Helper.handleError(err), {type: "error"});
                    }
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for update group and send data of updated details about group and post on server
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const updateGroup = () => {
        console.log("innnn");
        const data = {
            _method: "PUT",
            group_type: event_type,
            group_key: groupData.groupKey,
            group_name: Helper.jsUcfirst(groupData.groupName),
            description: groupData.description,
            type_value: groupData.typeValue,
            pilot: _.isEmpty(newPilotId) ? [newPilotName[0].id] : newPilotId,
            co_pilot: _.isEmpty(coPilot) ? existingCoPilotsId : coPilot,
            allow_user: allowManageUser == true ? 1 : 0,
            allow_manage_pilots_owner: allowManagePilot == true ? 1 : 0,
            allow_design_setting: allowDesignSetting == true ? 1 : 0,
        };
        try {
            dispatch(groupAction.updateGroup(data))
                .then((res) => {
                    alert.show("Record updated successfully ");
                    history.push(`/${gKey}/manage-groups`);
                })
                .catch((err) => {
                    if (err && _.has(err.response.data, ["errors"])) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data.msg;
                        for (let key in er) {
                            alert.show(er[key], {type: "error"});
                        }
                        // alert.show(err.response.data.msg,{type:'error'});
                    } else {
                        alert.show(Helper.handleError(err), {type: "error"});
                    }
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    return (
        <div className="group_creation_wrap">
            {/* <div className='create_group_heading'>
                <p className='customPara'>Create your own group...</p>
            </div> */}
            <Grid container xs={12} className="group_creation_wrap_main">
                <form onSubmit={handleSubmit}>
                    <Grid container spacing={2} xs={12} className="FlexRow">
                        <Grid item xs={3}>
                            <p className="customPara">
                                Group Type<span className="text-danger-important">*</span>:
                            </p>
                        </Grid>
                        <Grid item xs={4}>
                            <FormControl variant="outlined" className="SelectEventType">
                                <InputLabel id="demo-simple-select-outlined-label"></InputLabel>
                                <Select
                                    labelId="demo-simple-select-outlined-label"
                                    value={event_type}
                                    disabled={gKey && props.mode !== 'create' ? true : false}
                                    onChange={(e) => {
                                        setEventType(e.target.value);
                                    }}
                                >
                                    {gKey && event_type === 'super_group' &&
                                    <MenuItem value={"super_group"}>
                                        Super Group
                                    </MenuItem>}
                                    <MenuItem value={"head_quarters_group"}>
                                        Head Quarters Group
                                    </MenuItem>
                                    <MenuItem value={"local_group"}>Local Group</MenuItem>
                                    <MenuItem value={"functional_group"}>Function Group</MenuItem>
                                    <MenuItem value={"topic_group"}>Topic Group</MenuItem>
                                    <MenuItem value={"spontaneous_group"}>
                                        Spontaneous Group
                                    </MenuItem>
                                    <MenuItem value={"water_fountain_group"}>
                                        Water Fountain
                                    </MenuItem>
                                </Select>
                            </FormControl>
                        </Grid>
                    </Grid>

                    {(event_type == "head_quarters_group" ||
                        event_type == "spontaneous_group" ||
                        event_type == "water_fountain_group") && (
                        <Grid container spacing={2} xs={12} className="FlexRow">
                            <Grid item xs={3}>
                                <p className="customPara">
                                    Audience<span className="text-danger-important">*</span>:
                                </p>
                            </Grid>
                            <Grid item xs={4}>
                                <Field
                                    name="type_value"
                                    placeholder="Audience"
                                    variant="outlined"
                                    size="small"
                                    component={renderTextField}
                                    onChange={handleChange("typeValue")}
                                    inputProps={{
                                        value: groupData.typeValue,
                                    }}
                                />
                            </Grid>
                        </Grid>
                    )}

                    {event_type?.toLowerCase() === "local_group" && (
                        <Grid container spacing={2} xs={12} className="FlexRow">
                            <Grid item xs={3}>
                                <p className="customPara">
                                    Location<span className="text-danger-important">*</span>:
                                </p>
                            </Grid>
                            <Grid item xs={4}>
                                <Field
                                    name="type_value"
                                    placeholder="Location"
                                    variant="outlined"
                                    size="small"
                                    component={renderTextField}
                                    onChange={handleChange("typeValue")}
                                    inputProps={{
                                        value: groupData.typeValue,
                                    }}
                                />
                            </Grid>
                        </Grid>
                    )}

                    {event_type == "functional_group" && (
                        <Grid container spacing={2} xs={12} className="FlexRow">
                            <Grid item xs={3}>
                                <p className="customPara">
                                    Function<span className="text-danger-important">*</span>:
                                </p>
                            </Grid>
                            <Grid item xs={4}>
                                <Field
                                    name="type_value"
                                    placeholder="Function"
                                    variant="outlined"
                                    size="small"
                                    component={renderTextField}
                                    onChange={handleChange("typeValue")}
                                />
                            </Grid>
                        </Grid>
                    )}
                    {/* function component */}

                    {event_type == "topic_group" && (
                        <Grid container spacing={2} xs={12} className="FlexRow">
                            <Grid item xs={3}>
                                <p className="customPara">
                                    Topic<span className="text-danger-important">*</span>:
                                </p>
                            </Grid>
                            <Grid item xs={4}>
                                <Field
                                    name="type_value"
                                    placeholder="Topic"
                                    variant="outlined"
                                    size="small"
                                    component={renderTextField}
                                    onChange={handleChange("typeValue")}
                                />
                            </Grid>
                        </Grid>
                    )}
                    {/* Topic component */}

                    <Grid container spacing={2} xs={12} className="FlexRow">
                        <Grid item xs={3}>
                            <p className="customPara">
                                Group Name<span className="text-danger-important">*</span>:
                            </p>
                        </Grid>

                        <Grid item xs={4}>
                            <Field
                                name="group_name"
                                placeholder="Name of your group"
                                size="small"
                                variant="outlined"
                                component={renderTextField}
                                onChange={handleChange("groupName")}
                                inputProps={{
                                    value: Helper.jsUcfirst(groupData.groupName),
                                }}

                                // value={groupData.groupName}
                            />

                        </Grid>
                    </Grid>
                    {/* <Grid container spacing={2} xs={12} className="FlexRow">
            <Grid item xs={3}>
              <p className="customPara">
                Group Key<span className="text-danger-important">*</span>:
              </p>
            </Grid>
            <Grid item xs={4}>
              <Field
                name="group_key"
                placeholder="Group Key"
                disabled={gKey ? true : false}
                // value={groupData.groupKey}
                size="small"
                variant="outlined"
                onChange={handleChange("groupKey")}
                component={renderTextField}
                inputProps={{
                  value: groupData.groupKey,
                }}
              />
            </Grid>
          </Grid> */}
                    <Grid container spacing={2} xs={12} className="FlexRow">
                        <Grid item xs={3}>
                            <p className="customPara">Description:</p>
                        </Grid>
                        <Grid item xs={4}>
                            <Field
                                name="description"
                                placeholder="Description"
                                onChange={handleChange("description")}
                                size="small"
                                variant="outlined"
                                component={renderTextAreaField}
                                inputProps={{
                                    value: groupData.description,
                                }}
                            />
                        </Grid>
                    </Grid>
                    <Grid container spacing={2} xs={12} className="FlexRow">
                        <Grid item xs={3}>
                            <p className="customPara">
                                Pilot<span className="text-danger-important">*</span>:
                            </p>
                        </Grid>
                        <Grid item xs={4}>
                            <UserAutocomplete
                                id={(id) => setNewPilotId([id])}
                                name={newPilotName[0] ? newPilotName[0] : currentUser[0]}
                                searchUser={true}
                                setSelectedData={setSelectedData}
                                selectedData={selectedData}
                            />

                            {/* <MultiSelectUse
                disabled={false}
                selectedSpeakers={pilotData}
                multiple={false}
                placeholder="Name of your group"
                onChange={handlePilotChange}
                id={(id) => console.log(id)}
                name={{ name: "", id: "" }}
              /> */}
                        </Grid>
                    </Grid>

                    <Grid container spacing={2} xs={12} className="FlexRow">
                        <Grid item xs={3}>
                            <p className="customPara">
                                Co-Pilot<span className="text-danger-important"></span>:
                            </p>
                        </Grid>
                        <Grid item xs={4}>
                            <MultiSelectUse
                                disabled={false}
                                selectedSpeakers={coPilotData}
                                multiple={true}
                                placeholder="Name of your group"
                                onChange={handleCoPilotChange}
                                id={(id) => console.log(id)}
                                name={{name: "", id: ""}}
                                setSelectedData={setSelectedData}
                                selectedData={selectedData}
                            />
                        </Grid>
                    </Grid>

                    {/* <Grid container spacing={2} xs={12} className="FlexRow">
            <Grid item xs={3}>
              <p className="customPara ">Allow Manage User:</p>
            </Grid>
            <Grid item xs={4}>
              <Switch
                color="primary"
                onChange={() => {
                  setAllowManageUser(!allowManageUser);
                }}
                inputProps={{ "aria-label": "controlled" }}
                checked={allowManageUser}
              />
            </Grid>
          </Grid> */}

                    <Grid container spacing={2} xs={12} className="FlexRow">
                        <Grid item xs={3}>
                            <p className="customPara ">Allow Manage Pilots and Owners:</p>
                        </Grid>
                        <Grid item xs={4}>
                            <Switch
                                color="primary"
                                onChange={() => {
                                    setAllowManagePilot(!allowManagePilot);
                                }}
                                inputProps={{"aria-label": "controlled"}}
                                checked={allowManagePilot}
                                disabled={event_type === 'super_group'}
                            />
                        </Grid>
                    </Grid>

                    <Grid container spacing={2} xs={12} className="FlexRow">
                        <Grid item xs={3}>
                            <p className="customPara ">Allow Design Setting Customisation:</p>
                        </Grid>

                        <Grid item xs={4}>
                            <Switch
                                color="primary"
                                onChange={() => {
                                    setAllowDesignSetting(!allowDesignSetting);
                                }}
                                inputProps={{"aria-label": "controlled"}}
                                checked={allowDesignSetting}
                                disabled={event_type === 'super_group'}
                            />
                        </Grid>
                    </Grid>

                    <Grid
                        container
                        spacing={2}
                        xs={12}
                        className="FlexRow save_create_group"
                    >
                        <Grid item xs={3}>
                            {" "}
                        </Grid>
                        <Grid item xs={4}>
                            {gKey && props.mode !== 'create' ? (
                                <Button
                                    variant="contained"
                                    color="primary"
                                    onClick={handleSubmit(updateGroup)}
                                    type="submit"
                                >
                                    update
                                </Button>
                            ) : (
                                <Button
                                    variant="contained"
                                    color="primary"
                                    onClick={handleSubmit(groupCreate)}
                                    type="submit"
                                >
                                    Create
                                </Button>
                            )}

                            <Button
                                onClick={() => {
                                    history.push(`/${gKey}/manage-groups`);
                                }}
                            >
                                cancel
                            </Button>
                        </Grid>
                    </Grid>
                </form>
            </Grid>
        </div>
    );
};

export default reduxForm({
    form: "groupForm", // unique key for form
    validate,
    keepDirtyOnReinitialize: true,
})(GroupCraetion);
