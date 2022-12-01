import React from 'react'
import Popover from '@mui/material/Popover';
import IconButton from '@mui/material/IconButton';
import AddIcon from '@mui/icons-material/Add';
import {Button} from '@mui/material';
import CoffeeIcon from '@mui/icons-material/Coffee';
import EventSeatIcon from '@mui/icons-material/EventSeat';
import SupervisorAccountOutlinedIcon from '@mui/icons-material/SupervisorAccountOutlined';
import GroupsOutlinedIcon from '@mui/icons-material/GroupsOutlined';
import {useParams} from 'react-router-dom';
import Constants from "../../../../Constants";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is responsible for render the create event Icon in the Side bar component.
 * Upon clicking on this icon a popover is open with types of event options in it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent
 * @returns {JSX.Element}
 * @constructor
 */
const CreateEventDropdownBtn = (props) => {
    const {gKey} = useParams();
    const [anchorEl, setAnchorEl] = React.useState(null);

    const handleClick = (event) => {
        setAnchorEl(event.currentTarget);
    };

    const handleClose = () => {

        setAnchorEl(null);
    };
    const DropdownBtns = [
        {
            name: "Cafeteria Event",
            href: `/${gKey}/v4/event-create`,
            formMode: Constants.eventFormType.CAFETERIA,
            icon: <CoffeeIcon />,
            disable: false,
        },
        {
            name: "Executive Event",
            href: `/${gKey}/v4/event-create`,
            formMode: Constants.eventFormType.EXECUTIVE,
            icon: <SupervisorAccountOutlinedIcon />,
            disable: false,
        },
        {
            name: "Manager Event",
            href: `/${gKey}/v4/event-create`,
            formMode: Constants.eventFormType.MANAGER,
            icon: <EventSeatIcon />,
            disable: false,
        },
        {
            name: "Group",
            href: "",
            icon: <GroupsOutlinedIcon />,
            disable: true,
        },
    ]

    const open = Boolean(anchorEl);
    const id = open ? 'simple-popover' : undefined;

    return (
        <>
            <div>
                <IconButton
                    aria-describedby={id}
                    onClick={handleClick}
                    variant="contained"
                    color="primary"
                    size='small'
                >
                    <AddIcon />
                </IconButton>
                <Popover
                    id={id}
                    open={open}
                    className="sideBarPopOver"
                    anchorEl={anchorEl}
                    onClose={handleClose}
                    anchorOrigin={{
                        vertical: 'top',
                        horizontal: 'right',
                    }}
                    transformOrigin={{
                        vertical: 'top',
                        horizontal: 'left',
                    }}
                >
                    <small className='customPara'>Create a new...</small>
                    {
                        DropdownBtns.map((data, index) => (
                            <Button
                                variant="contained"
                                color={data.disable ? "secondary" : "primary"}
                                size='small'
                                key={index}
                                disabled={data.disable}
                                onClick={() => {
                                    setAnchorEl(null);
                                    return props.history.push(`${data.href}`, {formMode: data.formMode})
                                }
                                }
                            >
                                {data.name}
                                {data?.icon}
                            </Button>
                        ))
                    }
                </Popover>
            </div>
        </>
    )
}

export default CreateEventDropdownBtn