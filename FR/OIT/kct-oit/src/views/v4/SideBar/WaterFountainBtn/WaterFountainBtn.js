import React, {useEffect} from 'react';
import {useParams} from "react-router-dom";
import {Button} from "@mui/material";
import IconButton from "@mui/material/IconButton";
import Popover from "@mui/material/Popover";
import Constants from "../../../../Constants";
import {connect} from "react-redux";
import groupAction from "../../../../redux/action/reduxAction/group";
import eventAction from "../../../../redux/action/reduxAction/event";
import WaterFountainIcon from "../../Svg/WaterFountainIcon";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is responsible for rendering the Water Fountain Icon in Side bar component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent
 * @param {Object} props.user_meta_data Redux store data for holding the user metadata
 * @param {Object} props.all_day_event Redux store data for holding the water fountain event's data
 * @param {Object} props.all_day_event_enabled Redux store data for holding the data for water fountain event enabled or
 * not
 * @param {Function} props.updateAllDayEventEnabled Redux store function for updating water fountain event's data
 * @returns {JSX.Element}
 * @constructor
 */
let WaterFountainBtn = (props) => {
    const {gKey} = useParams();
    const event_uuid = props.all_day_event.event_uuid;
    const [anchorEl, setAnchorEl] = React.useState(null);

    useEffect(() => {
        if (props.user_meta_data?.all_day_event?.event_uuid) {
            props.updateAllDayEventEnabled(props.user_meta_data.all_day_event_enabled);
            props.updateAllDayEvent(props.user_meta_data.all_day_event);
        }
    }, [props.user_meta_data])

    const handleClick = (event) => {
        setAnchorEl(event.currentTarget);
    };

    const handleClose = () => {
        setAnchorEl(null);
    };
    const open = Boolean(anchorEl);
    const id = open ? 'simple-popover' : undefined;

    const SubMenus = [
        {
            name: 'Access',
            href: `/${gKey}/v4/event-update/${event_uuid}`,
            formMode: Constants.eventFormType.ALL_DAY,
            icon: '',
            disable: false
        },
        {
            name: 'Media',
            href: `/${gKey}/v4/event/media/${event_uuid}`,
            formMode: Constants.eventFormType.ALL_DAY,
            icon: '',
            disable: false
        },
        {
            name: 'Users',
            href: `/${gKey}/v4/event/user/${event_uuid}`,
            formMode: Constants.eventFormType.ALL_DAY,
            icon: '',
            disable: false
        },
        {
            name: 'Analytics',
            href: `/${gKey}/v4/event/analytics/${event_uuid}`,
            formMode: Constants.eventFormType.ALL_DAY,
            icon: '',
            disable: false
        },
    ]

    return (
        <>
            <div>
                <IconButton
                    aria-describedby={id}
                    onClick={handleClick}
                    variant="contained"
                    color="primary"
                    size='small'
                    disabled={props.all_day_event_enabled === 0 || gKey !== 'default'}
                    className={`${(props.all_day_event_enabled === 0 || gKey !== 'default') && "customDisableBtnColor"}`}
                >
                    {/*<UpdateIcon />*/}
                    <WaterFountainIcon />
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
                    <small className='customPara'>Water Fountain Event</small>
                    {
                        SubMenus.map((data, index) => (
                            <Button
                                variant="contained"
                                color={data.disable ? "secondary" : "primary"}
                                size='small'
                                key={index}
                                disabled={data.disable}
                                onClick={() => {
                                    // setAnchorEl(null);
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
    );
}
const mapStateToProps = (state) => {
    return {
        user_meta_data: state.Auth.userMetaData,
        all_day_event_enabled: state.Group.all_day_event_enabled,
        all_day_event: state.Event.all_day_event
    }
}
const mapDispatchToProps = (dispatch) => {
    return {
        updateAllDayEventEnabled: (data) => dispatch(groupAction.updateAllDayEventEnabled(data)),
        updateAllDayEvent: (allDayEventData) => dispatch(eventAction.updateAllDayEvent(allDayEventData)),
    }
}

WaterFountainBtn = connect(mapStateToProps, mapDispatchToProps)(WaterFountainBtn);

export default WaterFountainBtn;