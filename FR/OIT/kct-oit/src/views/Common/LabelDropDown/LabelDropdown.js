import React, {useState} from 'react'
import ClickAwayListener from '@material-ui/core/ClickAwayListener';
import Grow from '@material-ui/core/Grow';
import Paper from '@material-ui/core/Paper';
import Popper from '@material-ui/core/Popper';
import MenuItem from '@material-ui/core/MenuItem';
import MenuList from '@material-ui/core/MenuList';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import './Dropdown.css';
import {
    Grid,
    Avatar,
    Button
} from '@material-ui/core';

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used in to label dropdown to hold the label value
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Object that contain dropdown menu details
 * @param {Object} props.setSelectedKey Selected key value
 * @param {Object} props.list List of values that is used in dropdown
 * @returns {JSX.Element}
 * @constructor
 */
const LabelDropdown = (props) => {
    const [open, setOpen] = React.useState(false);
    const anchorRef = React.useRef(null);
    const [selectedName, setSeletedName] = useState('all')

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle close header dropdown when we click anywhere on the screen.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Javascript Event Object
     */
    const handleClose = (event) => {
        if (anchorRef.current && anchorRef.current.contains(event.target)) {
            return;
        }

        setOpen(false);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle open header dropdown.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleToggle = () => {
        setOpen((prevOpen) => !prevOpen);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle list which is used in drop down
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event Javascript Event Object
     */
    function handleListKeyDown(event) {
        if (event.key === 'Tab') {
            event.preventDefault();
            setOpen(false);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used on change drop down value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript Event Object
     * @param {Object} value Group related data
     * @param {String} value.group_name Name of group
     * @param {String} value.group_key Group key
     */
    const onChangeOption = (e, value) => {

        console.log('select', value)
        setSeletedName(value.group_name)
        props.setSelectedKey && props.setSelectedKey(value.group_key && value.group_key)
        setOpen(false);
    }

    const demoData = [{id: 1, name: "change 1"}, {id: 2, name: "change 2"}, {id: 3, name: "change 3"}]
    console.log("list", props.list)
    return (
        <>
            <p>{selectedName}</p>
            <Button
                ref={anchorRef}
                aria-controls={open ? 'menu-list-grow' : undefined}
                aria-haspopup="true"
                disableRipple="true"
                className="ProfileHeaderBtnDrop"
                onClick={handleToggle}
            >
                <ExpandMoreIcon />
            </Button>
            <Popper open={open} anchorEl={anchorRef.current} role={undefined} transition disablePortal>
                {({TransitionProps, placement}) => (
                    <Grow
                        {...TransitionProps}
                        style={{transformOrigin: placement === 'right' ? 'right top' : 'right bottom'}}
                    >
                        <Paper>
                            <ClickAwayListener onClickAway={handleClose}>
                                <MenuList autoFocusItem={open} id="menu-list-grow" className="customMenuListDropDown"
                                          onKeyDown={handleListKeyDown}>
                                    <MenuItem onClick={(e) => onChangeOption(e, {
                                        group_key: "",
                                        group_name: "all"
                                    })}>All</MenuItem>
                                    {props.list && props.list.map((value, i) => (
                                        <MenuItem key={value.group_key} value={value} onClick={
                                            (e) => onChangeOption(e, value)}>{value.group_name}</MenuItem>
                                    ))}
                                    {/* <MenuItem >Profile</MenuItem>
                    <MenuItem >Change password</MenuItem>
                    <MenuItem >Logout</MenuItem> */}
                                </MenuList>
                            </ClickAwayListener>
                        </Paper>
                    </Grow>
                )}
            </Popper>
        </>
    )
}

export default LabelDropdown;