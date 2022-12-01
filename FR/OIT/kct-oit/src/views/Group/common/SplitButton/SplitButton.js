import * as React from "react";
import {useState, useEffect} from "react";
import Button from "@mui/material/Button";
import ButtonGroup from "@mui/material/ButtonGroup";
import ArrowDropDownIcon from "@mui/icons-material/ArrowDropDown";
import ClickAwayListener from "@mui/material/ClickAwayListener";
import Grow from "@mui/material/Grow";
import Paper from "@mui/material/Paper";
import Popper from "@mui/material/Popper";
import MenuItem from "@mui/material/MenuItem";
import MenuList from "@mui/material/MenuList";
import {useHistory} from "react-router-dom";
import GroupCreation from "../../GroupCreation/GroupCreation";
import {BrowserRouter as Router, Switch, Route, Link} from "react-router-dom";
import groupAction from "../../../../redux/action/apiAction/group";
import {useSelector, useDispatch} from "react-redux";
import Helper from "../../../../Helper";
import _ from "lodash";
import {useAlert} from "react-alert";
import ModalBox from "../../../Common/ModalBox/ModalBox";
import {TextField} from "@mui/material";
import DeleteExportContent from "./ModalContent/DeleteExportContent.js";
import DeletePermanent from "./ModalContent/DeletePermanent.js";
import {connect} from "react-redux";

const options = [
    "Access",
    "Modify",
    "Manage Users",
    "Manage Pilots & Owners",
    "Design Settings",
    "Manage Tags",
    "Delete",
];

const defaultGrpKey = "default";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common reusable component to provide a button with a dropdown menu component.Generally this
 * component is used in all list to provide many actions for a specific row of the list.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Object that contains all information to show in split button component.
 * @param {Function} props.deleteGroup Function to delete a group.
 * @param {Function} props.key Unique key for the specific group.
 * @param {GroupObj} props.group_data All available groups data.
 * @param {Function} props.reloadGroup Function to reload data as per pagination.
 * @returns {JSX.Element}
 */
const SplitButton = (props) => {
    const [open, setOpen] = React.useState(false);
    const [openModal, setOpenModal] = React.useState(false);
    const anchorRef = React.useRef(null);
    const [selectName, setSelectName] = React.useState("Access");

    const [showMangeDesignSettings, setShowMangeDesignSettings] = useState(false);
    const [showManagePilot, setShowManagePilot] = useState(false);
    const [showOptions, setShowOptions] = useState(false);
    const [showDeleteOption, setShowDeleteOption] = useState(false);

    const dispatch = useDispatch();
    // const [selectedIndex, setSelectedIndex] = React.useState(0);
    let history = useHistory();
    const alert = useAlert();
    const [isPermanentDelete, setIsPermanentDelete] = useState(false);

    const isSuperPilot = useSelector(
        data => _.has(data.Auth.userMetaData, ["is_super_pilot"]) && data.Auth.userMetaData.is_super_pilot
    )

    const isSuperOwner = useSelector(
        data => _.has(data.Auth.userMetaData, ["is_super_owner"]) && data.Auth.userMetaData.is_super_owner
    )

    // const reduxData = useSelector(data=>data.Auth.userMetaData)
    useEffect(() => {
        if (props.groupData) {
            setShowManagePilot(props.groupData.allow_manage_pilots_owner === 1 ? true : false)
            setShowMangeDesignSettings(props.groupData.allow_design_setting === 1 ? true : false)

        }
        const currentUser = localStorage.getItem("userId")
        // console.log("currentUser", currentUser, "pi", props.groupData.pilot.id)
        if (currentUser) {
            setShowOptions(props.groupData.allow_modify === 1)
            setShowDeleteOption(props.groupData.group_key !== 'default')
        }

    }, [])

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method update current group name on dashboard and updates data on localstorage for current
     * group data
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const accessGroupHandler = () => {
        const localData = localStorage.getItem("user_data");
        const parseLocalData = JSON.parse(localData);
        parseLocalData.current_group = props.groupData;
        const jsonLocalData = JSON.stringify(parseLocalData);
        localStorage.setItem("user_data", jsonLocalData);
        getSingleGroup(props.gkey);

    };

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method handles color data and updates state.
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleOpenModal = () => {
        setOpenModal(true);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method handles color data and updates state.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleCloseModal = () => {
        setOpenModal(false);
        setIsPermanentDelete(false);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for api call for current active group and this will store data on server when
     * user login next time the last visited group will be shown
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} gKey Group unique key.
     */
    const getSingleGroup = (gKey) => {
        try {
            dispatch(groupAction.getSingleGroupData(gKey))
                .then((res) => {
                    const data = res.data.data;
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
     * @description This function will trigger when user selects any option from split button's dropdown menu and
     * according to their selection a certain function will trigger using switch case.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} selectedIndex Dropdown menu's option index(top to bottom).
     */
    const handleClick = (selectedIndex) => {
        // console.info(`You clicked ${options[selectedIndex]}`);

        switch (options[selectedIndex]) {
            case "Modify":
                history.push(`/${props.gkey}/edit-group`);
                break;

            case "Access":
                accessGroupHandler();
                history.push(`/${props.gkey}/dashboard`);

                break;
            case "Manage Users":
                history.push(`/${props.gkey}/user-setting`);

                break;
            case "Manage Pilots & Owners":
                history.push(`/${props.gkey}/manage-org`);
                break;
            case "Design Settings":
                history.push(`/${props.gkey}/event-setting`);
                break;
            case "Manage Tags":
                history.push(`/${props.gkey}/org-tags`);
                break;
            case "Delete":
                break;
            default:
                return;
        }
    };

    // const handleMenuItemClick = (event, index) => {
    //   console.log("val event", event.target.value, index, options[index]);
    //   setSelectName(`${options[index]}`);
    //   setSelectedIndex(index);
    //   setOpen(false);
    // };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method handles model state to show
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleToggle = () => {
        setOpen((prevOpen) => !prevOpen);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method handles model state to close
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event JavaScript event object.
     */
    const handleClose = (event) => {
        if (anchorRef.current && anchorRef.current.contains(event.target)) {
            return;
        }

        setOpen(false);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method handles delete functionality of group in which pilot can delete paramanent of export
     * users in super group after delete group.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {GroupObj} data Group details object which needs to be deleted.
     */
    const deleteAndExportUsers = (data) => {
        try {
            props
                .deleteGroup(data)
                .then((res) => {
                    alert.show("Successfully deleted", {type: "success"});
                    const groupData = localStorage.getItem('Current_group_data');
                    const group = JSON.parse(groupData);
                    const groupKey = group.group_key;
                    // Checking if current accessed group and deleted group is same
                    // If yes from above check then user will be redirected to default group
                    if (groupKey === data.group_key) {
                        const currentPageUrl = window.location.href.split('/oit/');
                        const firstPartUrl = currentPageUrl[0];
                        window.location.replace && window.location.replace(`${firstPartUrl}/oit/default/manage-groups`)
                    }
                    props.reloadGroup();
                })
                .catch((err) => {
                    console.log("fav err", err);
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
            console.log("fav err", err);
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will render menu item component in split button's dropdown component.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} option Option name
     * @param {Number} index Index of options
     */
    const chackValues = (option, index) => {
        switch (option) {

            case "Design Settings":
                if (showMangeDesignSettings) {
                    return (
                        <MenuItem key={option} onClick={(event) => handleClick(index)}>
                            {option}
                        </MenuItem>
                    )
                }
                break;

            case "Manage Pilots & Owners":
                if (showManagePilot) {
                    return (
                        <MenuItem key={option} onClick={(event) => handleClick(index)}>
                            {option}
                        </MenuItem>
                    )
                }
                break;

            case "Modify":
                if (showOptions) {
                    return (
                        <MenuItem key={option} onClick={(event) => handleClick(index)}>
                            {option}
                        </MenuItem>
                    )
                }
                break;
            case "Delete":
                if (showDeleteOption) {
                    return (
                        <MenuItem key={option} onClick={(event) => handleClick(index)}>
                            {option}
                        </MenuItem>
                    )
                }
                break;

            default:
                return (<MenuItem key={option} onClick={(event) => handleClick(index)}>
                    {option}
                </MenuItem>)
        }
    };


    return (
        <React.Fragment>
            <ButtonGroup
                variant="contained"
                ref={anchorRef}
                aria-label="split button"
            >
                <Button
                    size="small"
                    style={{backgroundColor: "#0589B8", borderColor: "#20bbf3"}}
                    onClick={() => handleClick(0)}
                >
                    {selectName}
                </Button>
                <Button
                    size="small"
                    style={{backgroundColor: "#0589B8"}}
                    aria-controls={open ? "split-button-menu" : undefined}
                    aria-expanded={open ? "true" : undefined}
                    aria-label="select merge strategy"
                    aria-haspopup="menu"
                    onClick={handleToggle}
                >
                    <ArrowDropDownIcon />
                </Button>
            </ButtonGroup>
            <Popper
                open={open}
                anchorEl={anchorRef.current}
                role={undefined}
                transition
                style={{zIndex: 1}}
                disablePortal
            >
                {({TransitionProps, placement}) => (
                    <Grow
                        {...TransitionProps}
                        style={{
                            transformOrigin:
                                placement === "bottom" ? "center top" : "center bottom",
                        }}
                    >
                        <Paper>
                            <ClickAwayListener onClickAway={handleClose}>
                                <MenuList id="split-button-menu">
                                    {options
                                        .filter((option) => option !== "Delete")
                                        .map((option, index) => chackValues(option, index))}
                                    {props.groupData.group_key !== defaultGrpKey && showOptions && (
                                        <MenuItem
                                            key={"Delete"}
                                            onClick={handleOpenModal} // delete modal option
                                        >
                                            Delete
                                        </MenuItem>
                                    )}
                                </MenuList>
                            </ClickAwayListener>
                        </Paper>
                    </Grow>
                )}
            </Popper>
            {openModal && (
                <ModalBox
                    btn_txt="delete"
                    ModalHeading="Delete Group"
                    handleOpenModal={handleOpenModal}
                    handleCloseModal={handleCloseModal}
                    maxWidth={"500px"}
                    leftCssVal={"250px"}
                >
                    {isPermanentDelete == true ? (
                        <DeletePermanent
                            setIsPermanentDelete={setIsPermanentDelete}
                            group_key={props.gkey}
                            handleCloseModal={handleCloseModal}
                            reloadGroup={props.reloadGroup}
                            deleteGroup={deleteAndExportUsers}
                        />
                    ) : (
                        <DeleteExportContent
                            setIsPermanentDelete={setIsPermanentDelete}
                            group_key={props.gkey}
                            default_group_data={props.defaultGroup}
                            handleCloseModal={handleCloseModal}
                            reloadGroup={props.reloadGroup}
                            deleteGroup={deleteAndExportUsers}
                        />
                    )}
                </ModalBox>
            )}
        </React.Fragment>
    );
};

const mapStateToProps = () => {
    return {};
};

const mapDispatchToProps = (dispatch) => {
    return {
        deleteGroup: (data) => dispatch(groupAction.deleteGroup(data)),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(SplitButton);
