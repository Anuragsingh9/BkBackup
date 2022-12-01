import React, {useState} from 'react';
import ClickAwayListener from '@material-ui/core/ClickAwayListener';
import Grow from '@material-ui/core/Grow';
import Paper from '@material-ui/core/Paper';
import Popper from '@material-ui/core/Popper';
import ButtonGroup from '@material-ui/core/ButtonGroup';
import MenuItem from '@material-ui/core/MenuItem';
import MenuList from '@material-ui/core/MenuList';
import MoreVertIcon from '@material-ui/icons/MoreVert';
import {map} from 'lodash';

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a three dot dropdown menu common component which is taking data(dropdown menu item and route
 * related function to navigate in our application) from parameter. Once user click on three dot it will show a dropdown
 * menu with some items in it and then user can click on any option from it to navigate.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Object that contain dropdown menu details.
 * @param {IconDropDownObj} props.data Array of menu item which contain menu item label and a route parameter to
 * navigation to specific page/component.
 * @returns {JSX.Element}
 */
const IconDropDown = (props) => {
    const [open, setOpen] = React.useState(false);
    const anchorRef = React.useRef(null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to manage open/close state of dropdown menu. This function will call when user
     * click on three dot button of the component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleToggle = () => {
        setOpen((prevOpen) => !prevOpen);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is handling the close state of the dropdown menu. This function will trigger when
     * user open dropdown from three dot icon and then click anywhere from the page instead of three dot button.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event  Javascript Event Object
     */
    const handleClose = (event) => {
        if (anchorRef.current && anchorRef.current.contains(event.target)) {
            return;
        }

        setOpen(false);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is also handling close state of menu dropdown.This function will listen an
     * event('Tab' key press event) and change dropdown menu state to 'close'.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Getting  object from parameter to listen key('Tab') press event.
     */
    function handleListKeyDown(event) {
        if (event.key === 'Tab') {
            event.preventDefault();
            setOpen(false);
        }
    }

    return (
        <div className="ThreeDotDiv">
            <ButtonGroup variant="text" color="primary" ref={anchorRef} aria-label="split button">
                <MoreVertIcon onClick={handleToggle}>
                </MoreVertIcon>
            </ButtonGroup>
            <Popper open={open} anchorEl={anchorRef.current} role={undefined} transition disablePortal>
                {({TransitionProps, placement}) => (
                    <Grow
                        {...TransitionProps}
                        style={{transformOrigin: placement === 'bottom' ? 'center top' : 'center bottom'}}
                    >
                        <Paper>
                            <ClickAwayListener onClickAway={handleClose}>
                                <MenuList autoFocusItem={open} id="menu-list-grow" onKeyDown={handleListKeyDown}>
                                    {props.data.map(item => {
                                        return (
                                            <MenuItem onClick={() => {
                                                item.callBack()
                                            }}>{item.name}</MenuItem>

                                        )
                                    })}
                                </MenuList>
                            </ClickAwayListener>
                        </Paper>
                    </Grow>
                )}
            </Popper>
        </div>
    )

}

export default IconDropDown;