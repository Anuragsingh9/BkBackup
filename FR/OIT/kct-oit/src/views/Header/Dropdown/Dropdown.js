import React from 'react'
import ClickAwayListener from '@material-ui/core/ClickAwayListener';
import Grow from '@material-ui/core/Grow';
import Paper from '@material-ui/core/Paper';
import Popper from '@material-ui/core/Popper';
import MenuItem from '@material-ui/core/MenuItem';
import MenuList from '@material-ui/core/MenuList';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import userAction from '../../../redux/action/apiAction/user';
import {useAlert} from 'react-alert';
import Helper from '../../../Helper';
import {useParams} from "react-router-dom";
import {
  Grid,
  Avatar,
  Button
} from '@material-ui/core';
import {connect} from 'react-redux';
import './Dropdown.css';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a dropdown component currently using in navbar(header) component to render logged in user's
 * profile image (Initials if no profile is uploaded) and a dropdown menu to navigate to change password page, profile
 * page component and a option to logout from the application.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.logOut This method is responsible for allowing user to logout from App
 * @param {User} props.user_badge All user related data of logged in user
 * @returns {JSX.Element}
 * @constructor
 */
function MenuListComposition(props) {
  const [open, setOpen] = React.useState(false);
  const anchorRef = React.useRef(null);
  const alert = useAlert();
  const {gKey} = useParams();

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function will handle the on/off state of dropdown component.
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   */
  const handleToggle = () => {
    setOpen((prevOpen) => !prevOpen);
  };

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function will take dropdown current state(open) and return null to close the dropdown.
   * <br>
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
   * @description This function is also handling close state of menu dropdown.This function will listen an event('Tab'
   * key press event) and change dropdown menu state to 'close'.
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   * @param {Object} event Javascript event object
   */
  function handleListKeyDown(event) {
    if (event.key === 'Tab') {
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

  let user_badge = props.user_badge;
  // const data = localStorage.getItem('user_data');
  // if (data) {
  //   user_badge = JSON.parse(data);
  // }

  //helper method to convert first letter in capital
  const fullName = Helper.jsUcfirst(user_badge.fname + " " + user_badge.lname);
  // let firstNameClip = ;
  // let lastNameClip = ;

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function will trigger when user click on 'logout' option from the dropdown in navbar component.
   * This function will remove the OIT token from the local storage and logout the user from the application.
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   * @param {Object} e Javascript event object
   */
  const handleLogOut = (e) => {
    try {
      props.logOut().then((res) => {
        handleClose(e);
        window.location.replace(`${window.location.href.split('/oit')[0]}`)
        localStorage.removeItem('oitToken');
        localStorage.removeItem('user_data');
        localStorage.removeItem('userId');

      }).catch((err) => {
        alert.show(Helper.handleError(err), {type: 'error'})
      })
    } catch (err) {
      alert.show(Helper.handleError(err), {type: 'error'})
    }
  }

  return (
    <>
      <Button
        ref={anchorRef}
        aria-controls={open ? 'menu-list-grow' : undefined}
        aria-haspopup="true"
        disableRipple="true"
        className="ProfileHeaderBtnDrop"
        onClick={handleToggle}
      >
        <Grid container lg={12} className="ProfileParent" spacing={0}>
          <Grid item xs={12} lg={12}>
            <div className="profilePictureRow" >
              {user_badge.avatar ?
                <Avatar src={user_badge.avatar ? user_badge.avatar : ""} sx={{width: 56, height: 56}} />
                :
                <div className="NoProfileDiv circularProfile">
                  <p>
                    {Helper.nameProfile(user_badge.fname, user_badge.lname)}
                  </p>
                </div>
              }
              {/* <div className="loggedInUserName"><p >{Helper.limitText(fullName, 20)} </p></div> */}
              {/* {window.location.pathname !== "/oit/set-password" && <ExpandMoreIcon />} */}
            </div>
          </Grid>
        </Grid>

      </Button>
      {window.location.pathname !== "/oit/set-password" &&
        <Popper open={open} anchorEl={anchorRef.current} role={undefined} transition disablePortal
        placement={'top-end'}>
          {({TransitionProps, placement}) => (
            <Grow
              {...TransitionProps}
            >
              <Paper>
                <ClickAwayListener onClickAway={handleClose}>
                  <MenuList
                    autoFocusItem={open}
                    id="menu-list-grow"
                    className="customMenuListDropDown rem_1"
                    onKeyDown={handleListKeyDown}
                  >
                    <MenuItem
                      onClick={(e) => {props.history.push(`/${gKey}/profile`); handleClose(e)}}
                    >
                      Profile
                    </MenuItem>
                    <MenuItem
                      onClick={(e) => {props.history.push(`/${gKey}/update-password`); handleClose(e)}}
                    >
                      Change password
                    </MenuItem>
                    <MenuItem
                      onClick={handleLogOut}
                    >
                      Logout
                    </MenuItem>
                  </MenuList>
                </ClickAwayListener>
              </Paper>
            </Grow>
          )}
        </Popper>}
    </>
  );
}

const mapDispatchToProps = (dispatch) => {
  return {
    logOut: () => dispatch(userAction.logOut()),
  }
}

const mapStateToProps = (state) => {
  return {
    user_badge: state.Auth.userSelfData
  };
};
export default connect(mapStateToProps, mapDispatchToProps)(MenuListComposition);