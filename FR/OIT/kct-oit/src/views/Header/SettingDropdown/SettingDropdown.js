import React from "react";
import { Button } from "@material-ui/core";
import ClickAwayListener from "@material-ui/core/ClickAwayListener";
import Grow from "@material-ui/core/Grow";
import Paper from "@material-ui/core/Paper";
import Popper from "@material-ui/core/Popper";
import MenuItem from "@material-ui/core/MenuItem";
import MenuList from "@material-ui/core/MenuList";
import SettingIcon from "../../Svg/SettingIcon";
import "./SettingDropdown.css";
import { useParams } from "react-router-dom";
import { useSelector, useDispatch } from "react-redux";
import _ from 'lodash'

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a dropdown component by which user can assess 5 pages which are:
 * 1. Design setting page
 * 2. Technical setting page
 * 3. Manage Organizer page
 * 4. Manage Tags page
 * 5. Manage Users page
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Route related props to handle page navigation for eg - history, location, match
 * @returns {JSX.Element}
 * @constructor
 */
function SettingDropdown(props) {
  const [open, setOpen] = React.useState(false);
  const anchorRef = React.useRef(null);
  const { gKey } = useParams();
  const [isSuperGroup, setIsSuperGroup] = React.useState(false);

  const showManagingPilot = useSelector(data=> _.has(data.Auth.userMetaData,["allow_manage_pilots_owner"]) && data.Auth.userMetaData.allow_manage_pilots_owner)

  const showManagingDesign = useSelector(data=> _.has(data.Auth.userMetaData,["allow_design_setting"]) && data.Auth.userMetaData.allow_design_setting)


  const  isSuperPilot = useSelector(data=> _.has(data.Auth.userMetaData,["is_super_pilot"]) && data.Auth.userMetaData.is_super_pilot)

  const isSuperOwner = useSelector(data=> _.has(data.Auth.userMetaData,["is_super_owner"]) && data.Auth.userMetaData.is_super_owner)

  // allow_manage_pilots_owner
// const reduxdata = useSelector(data=> data.Auth)
// console.log( "settttttttttttt",reduxdata ,showManagingPilot , showManagingDesign )
// allow_design_setting




  //Method to manage dropdown's open state
  const handleToggle = () => {
    setOpen((prevOpen) => !prevOpen);
  };

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function will take dropdown current state(open) and return null to close the dropdown.
   * <br>
   * This function will trigger when dropdown is opened and user clickd outside of the dropdown anywhere in
   * the application.
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   * @param {Object} event Javascript event object
   */
  const handleClose = (event) => {
    if (anchorRef.current && anchorRef.current.contains(event.target)) {
      return;
    }
    setOpen(false);
  };

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function is also handeling close state of menu dropdown.This function will listen an event('Tab'
   * key press event) and change dropdown menu state to 'close'.
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   * @param {Object} event  javascript event object
   */
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

    const localData = localStorage.getItem("Current_group_data");
    if(localData){
      const parseLocalData = JSON.parse(localData);
      const isSuperGroup = parseLocalData.is_super_group;
      // console.log("isSuperGroup", isSuperGroup);
      setIsSuperGroup(isSuperGroup == 1 ? true : false);
    }else{
      setIsSuperGroup(false);
    }




  }, [open]);

  return (
    <>
      <Button
        ref={anchorRef}
        aria-controls={open ? "menu-list-grow" : undefined}
        aria-haspopup="true"
        disableRipple="true"
        onClick={handleToggle}
        className="header_settingIcon"
      >
        <SettingIcon/> 
        {/* Settings{" "}
        <ExpandMoreIcon className="px-6" /> */}
      </Button>
      <Popper
        open={open}
        anchorEl={anchorRef.current}
        role={undefined}
        transition
        disablePortal
        placement={'top-end'}
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
                  onKeyDown={handleListKeyDown}
                  className="rem_1"
                >
                  {showManagingDesign !=0 &&
                  <MenuItem
                    onClick={() => {
                      props.history.push(`/${gKey}/event-setting`);
                      handleToggle();
                    }}
                  >
                    Design Settings
                  </MenuItem>
                  }
                  {isSuperGroup  && (
                    <MenuItem
                      onClick={() => {
                        props.history.push(
                          `/${gKey}/event-setting/technical-setting`
                        );
                        handleToggle();
                      }}
                    >
                      Broadcast Settings
                    </MenuItem>
                  )}
                 { showManagingPilot !=0  &&
                  <MenuItem
                    onClick={() => {
                      props.history.push(`/${gKey}/manage-org`);
                      handleToggle();
                    }}
                  >
                    Manage Pilots & Owners
                  </MenuItem>
                  }
                  <MenuItem
                    onClick={() => {
                      props.history.push(`/${gKey}/org-tags`);
                      handleToggle();
                    }}
                  >
                    Manage Tags
                  </MenuItem>
                  <MenuItem
                    onClick={() => {
                      props.history.push(`/${gKey}/user-setting`);
                      handleToggle();
                    }}
                  >
                    Manage Users
                  </MenuItem>
                </MenuList>
              </ClickAwayListener>
            </Paper>
          </Grow>
        )}
      </Popper>
    </>
  );
}

export default SettingDropdown;