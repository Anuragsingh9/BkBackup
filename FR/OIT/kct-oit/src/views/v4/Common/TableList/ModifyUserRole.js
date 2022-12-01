import React, {useState} from 'react';
import ClickAwayListener from '@material-ui/core/ClickAwayListener';
import Grow from '@material-ui/core/Grow';
import Paper from '@material-ui/core/Paper';
import Popper from '@material-ui/core/Popper';
import ButtonGroup from '@material-ui/core/ButtonGroup';
import MenuItem from '@material-ui/core/MenuItem';
import MenuList from '@material-ui/core/MenuList';
import Button from "@material-ui/core/Button";
import ParticipantIcon from "../../Svg/ParticipantIcon";
import Tooltip from "@material-ui/core/Tooltip";
import {useTranslation} from "react-i18next";
import TeamAIcon from "../../Svg/TeamAIcon";
import TeamBIcon from "../../Svg/TeamBIcon";
import VIPRoleIcon from "../../Svg/VIPRoleIcon";

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
 * @param {Object} props.user Object of user data
 * @returns {JSX.Element}
 */
const IconDropDown = (props) => {
    const {t} = useTranslation(["roleIcons", "confirm", "labels"]);
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
                {props.user.is_moderator || props.user.is_organiser || props.user.is_presenter || props.user.is_space_host ? "---" :
                    <Button onClick={handleToggle} className="tableRoleDropdownMenuBtn">
                        Modify
                    </Button>
                }

            </ButtonGroup>
            <Popper open={open} anchorEl={anchorRef.current} role={undefined} transition disablePortal>
                {({TransitionProps, placement}) => (
                    <Grow
                        {...TransitionProps}
                        style={{transformOrigin: placement === 'bottom' ? 'center top' : 'center bottom'}}
                    >
                        <Paper>
                            <ClickAwayListener onClickAway={handleClose}>
                                <MenuList autoFocusItem={open} id="menu-list-grow" className='tableRoleDropdownMenu' onKeyDown={handleListKeyDown}>
                                    {props.data.map(item => {
                                        return (
                                            <>
                                                <MenuItem onClick={() => {
                                                    item.callBack()
                                                }}>
                                                    <span>{item.name}</span>
                                                    {item.role === 0 ? (
                                                        <Tooltip arrow title={t("labels:Participant")}>
                                                            <div className="role_icon_cell">
                                                                <ParticipantIcon />
                                                            </div>
                                                        </Tooltip>
                                                    ) : item.role === 1 ? (<Tooltip arrow title={t("labels:TeamA")}>
                                                            <div className="role_icon_cell">
                                                                <TeamAIcon />
                                                            </div>
                                                        </Tooltip>
                                                    ) : item.role === 2 ? (<Tooltip arrow title={t("labels:TeamB")}>
                                                            <div className="role_icon_cell">
                                                                <TeamBIcon />
                                                            </div>
                                                        </Tooltip>
                                                    ) : item.role === 3 ? (<Tooltip arrow title={t("labels:VIP")}>
                                                            <div className="role_icon_cell">
                                                                <VIPRoleIcon />
                                                            </div>
                                                        </Tooltip>
                                                    ) : ''}

                                                </MenuItem>
                                            </>
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