import React, {useEffect, useState} from "react";
import {Button} from "@material-ui/core";
import ClickAwayListener from "@material-ui/core/ClickAwayListener";
import Grow from "@material-ui/core/Grow";
import Paper from "@material-ui/core/Paper";
import Popper from "@material-ui/core/Popper";
import MenuItem from "@material-ui/core/MenuItem";
import MenuList from "@material-ui/core/MenuList";
import ExpandMoreIcon from "@material-ui/icons/ExpandMore";
import {connect, useDispatch} from "react-redux";
import "../EventsDropdown/EventsDropdown";
import {useTranslation} from "react-i18next";
import _ from "lodash";
import groupAction from "../../../redux/action/apiAction/group";
// import StarIcon from '@mui/icons-material/Star';
import {useParams} from "react-router-dom";
import StarIcon from "@material-ui/icons/Star";
import Helper from "../../../Helper";
import {useAlert} from "react-alert";
import Tooltip from '@material-ui/core/Tooltip';
import "./DashboardDropdown.css"
import groupReduxAction from "../../../redux/action/reduxAction/group";

/**
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying sub header bar which consist 3 dropdowns
 * 1. GroupList
 * 2. EventList
 * 3. SettingList
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Object} props.appSettings Settings of drop down
 * @param {Function} props.getGroups To get the groups
 * @returns {JSX.Element}
 * @constructor
 */
const DashboardDropdown = (props) => {
    const {t} = useTranslation("headerDropDown");
    const [open, setOpen] = React.useState(false);
    const [groupData, setGroupData] = useState([]);
    const [groupValues, setGroupValues] = useState({});
    const [groupDropdownEnable, setGroupDropdownEnable] = useState(false);
    const anchorRef = React.useRef(null);
    const [currentGroup, setCurrentGroup] = React.useState({});
    const [anchorEl, setAnchorEl] = React.useState(null);
    const openbar = Boolean(anchorEl);
    const [favGroup, setFavGroup] = useState([]);
    const [groupName, setGroupName] = useState("");
    const dispatch = useDispatch();
    const {gKey} = useParams();

    const urlName = window.location.host;

    const alert = useAlert();
    const handleToggle = () => {
        setOpen((prevOpen) => !prevOpen);
        if (prevOpen) {
            getGroupList();
        }
    };
    // useEffect hook to set initial user data.
    //   useEffect(() => {
    //       const localData = localStorage.getItem("user_data");
    //       const parseLocalData = JSON.parse(localData);
    //       const localStorageGroupData = parseLocalData.current_group;
    //       // setGroupName(localStorageGroupData.group_name || localStorageGroupData.name);

    //       if (!_.isEmpty(props.appSettings)) {
    //           setGroupDropdownEnable((localStorageGroupData.group_name || localStorageGroupData.name)&& props.appSettings.is_multi_group_enable === 1);
    //       }
    //       console.log("normmmaalll")
    //       getSingleGroup(gKey)
    //   }, []);


    useEffect(() => {
        const localData = localStorage.getItem("user_data");
        const parseLocalData = JSON.parse(localData);
        const localStorageGroupData = parseLocalData.current_group;
        // setGroupName(localStorageGroupData.group_name || localStorageGroupData.name);

        if (!_.isEmpty(props.appSettings)) {
            setGroupDropdownEnable(
                (localStorageGroupData.group_name || localStorageGroupData.name) &&
                props.appSettings.is_multi_group_enable === 1
            );
        }
        console.log("callllllllll", gKey)
        getSingleGroup(gKey);
    }, [gKey]);
    const favData = [];
    const gData = [];

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for fetching the all groups data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getGroupList = () => {
        try {
            props
                .getGroups({filter: "pilot"})
                .then((res) => {
                    const data = res.data.data;
                    console.log("resssss", res.data.meta.group_settings.can_create_group);
                    // sample data
                    // const favData = [{group_name:"1",is_fav: 1 }, {group_name:"2" ,is_fav: 1},
                    //{group_name:"1", is_fav: 1},{group_name:"1", is_fav: 1}];

                    data.map((value) => {
                        if (_.has(value, ["is_fav"]) && value.is_fav == 1) {
                            favData.push(value);
                        } else {
                            gData.push(value);
                        }
                    });
                    setFavGroup(favData);
                    setGroupData(gData);
                    setCurrentGroup(res.data.meta.current_group);

                    // setGroupData(res.data.data);
                })
                .catch((err) => {
                    console.log(err);
                });
        } catch (err) {
            console.log(err);
        }
    };

    const handleClose = (event) => {
        if (anchorRef.current && anchorRef.current.contains(event.target)) {
            return;
        }

        setOpen(false);
    };

    function handleListKeyDown(event) {
        if (event.key === "Tab") {
            event.preventDefault();
            setOpen(false);
        }
    }

    // return focus to the button when we transitioned from !open -> open
    const prevOpen = React.useRef(open);
    React.useEffect(() => {
        if (prevOpen.current === true && open === false) {
            anchorRef.current.focus();
        }

        prevOpen.current = open;
    }, [open]);

    const accessGroupHandler = (e, groupData) => {
        handleClose(e);
        const localData = localStorage.getItem("user_data");
        const parseLocalData = JSON.parse(localData);
        parseLocalData.current_group = groupData;
        props.updateCurrentGroup(groupData);
        const jsonLocalData = JSON.stringify(parseLocalData);
        localStorage.setItem("user_data", jsonLocalData);
        setGroupName(groupData.group_name || groupData.name);
        getSingleGroup(groupData.group_key);
        props.history.push(`/${groupData.group_key}/dashboard`);
    };

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is used for api call for current active group and this will store data on server when user
     * login next time the last visited group will be shown
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} gKey Key of group
     */
    const getSingleGroup = (gKey) => {
        console.log('dddddddddddd getting single group data', gKey);
        try {
            dispatch(groupAction.getSingleGroupData(gKey))
                .then((res) => {
                    const data = res.data.data;
                    console.log("get sigle grp", data);
                    setGroupName(data.group_name);
                    // To store data
                    const jsonLocalData = JSON.stringify(data);
                    localStorage.setItem("Current_group_data", jsonLocalData);
                    props.updateCurrentGroup(data);
                    // To retrieve data
                    localStorage.getItem("Name");
                })
                .catch((err) => {
                    const errData = err.response.data;
                    if (_.has(errData, ["errors"])) {
                        var errors = errData.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(errData, ["msg"])) {
                        var er = errData.msg;

                        if (typeof er === "array" && er.length > 1) {
                            for (let key in er) {
                                alert.show(er[key], {type: "error"});
                            }
                        } else {
                            alert.show(er, {type: "error"});
                        }
                    } else {
                        alert.show(Helper.handleError(err), {type: "error"});
                    }

                    if (_.has(errData, ["code"]) && errData.code == 1001) {

                        if (_.has(errData, ["current_group_key"])) {
                            window.location.href = `/oit/${errData.current_group_key}/dashboard`;
                        }
                    }

                    // alert.show(err.response.data.msg,{type:'error'});

                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    return (
        <>
            {/* <Button
        onClick={() => {
          props.history.push(`/${gKey}/dashboard`);
        }}
        lg={6}
      > */}
            {/* <DashboardIcon className="px-6 " />{" "} */}
            {/* {groupDropdownEnable ? groupName : `Dashboard`} */}
            {/* </Button> */}
            {groupDropdownEnable ? (
                    <Button
                        ref={anchorRef}
                        aria-controls={open ? "menu-list-grow" : undefined}
                        aria-haspopup="true"
                        className=" EventlistIcoDrop header__dropdownButton"
                        disableRipple="true"
                        onClick={handleToggle}
                    >
                        Groups
                        <ExpandMoreIcon className="px-6 EventlistIcoDrop" onClick={() => {
                        }} />
                    </Button>
                )
                :
                (
                    <Tooltip title={t("groupDropDownDisabled")} arrow>
            <span>
              <Button
                  ref={anchorRef}
                  aria-controls={open ? "menu-list-grow" : undefined}
                  aria-haspopup="true"
                  className=" EventlistIcoDrop header__dropdownButton"
                  disableRipple="true"
                  onClick={handleToggle}
                  disabled={!groupDropdownEnable}
              >
                Groups
                <ExpandMoreIcon className="px-6 EventlistIcoDrop" onClick={() => {
                }} />
              </Button>
            </span>
                    </Tooltip>
                )}

            &nbsp;&nbsp;&nbsp;
            <Popper
                open={open}
                anchorEl={anchorRef.current}
                role={undefined}
                transition
                disablePortal
                placement={'top-center'}
            >
                {({TransitionProps, placement}) => (
                    <Grow
                        {...TransitionProps}
                    >
                        <Paper>
                            <ClickAwayListener onClickAway={handleClose}>
                                <MenuList
                                    autoFocusItem={open}
                                    id="menu-list-grow"
                                    className="GroupDropDownList"
                                    onKeyDown={handleListKeyDown}
                                >
                                    {/* {!_.isEmpty(currentGroup) &&
                                    <>
                                        <MenuItem
                                            className="EventListHeading"
                                            onClick={(e) => accessGroupHandler(e, currentGroup)}
                                        >
                                            <div>
                                                <MenuItem key={0}>
                                                    <span>{currentGroup.group_name} - [{Helper.groupTypeCapital(currentGroup.group_type)}] </span>{" "}
                                                </MenuItem>
                                            </div>
                                        </MenuItem>
                                        <hr/>
                                    </>
                                    } */}

                                    {!_.isEmpty(favGroup) &&
                                    favGroup.map((value, i) => (
                                        <div>
                                            <MenuItem
                                                key={i}
                                                onClick={(e) => accessGroupHandler(e, value)}
                                            >
                          <span>
                            {value.group_name} - [
                              {Helper.groupTypeCapital(value.group_type)}]
                          </span>
                                                <StarIcon />
                                            </MenuItem>
                                        </div>
                                    ))}
                                    {!_.isEmpty(favGroup) && <hr className='ListSaprator' />}

                                    {groupData &&
                                    groupData.map(
                                        (value, i) =>
                                            i < 3 && (
                                                <MenuItem
                                                    key={i}
                                                    onClick={(e) => {
                                                        accessGroupHandler(e, value);
                                                    }}
                                                >
                            <span>
                              {value.group_name} - [
                                {Helper.groupTypeCapital(value.group_type)}]{" "}
                            </span>{" "}
                                                </MenuItem>
                                            )
                                    )}
                                    <hr className='ListSaprator' />
                                    <MenuItem
                                        className="EventListHeading"
                                        onClick={(e) => {
                                            handleClose(e);

                                            props.history.push(`/${gKey}/manage-groups`);
                                        }}
                                    >
                                        View/Manage All Groups
                                    </MenuItem>
                                </MenuList>
                            </ClickAwayListener>
                        </Paper>
                    </Grow>
                )}
            </Popper>
        </>
    );
};
const mapStateToProps = (state) => {
    return {
        appSettings: state.Auth.appSettings,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        getGroups: (data) => dispatch(groupAction.getGroups(data)),
        updateCurrentGroup: (groupData) => dispatch(groupReduxAction.updateCurrentGroup(groupData)),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(DashboardDropdown);
