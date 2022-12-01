import React, {useEffect, useState} from 'react'
import {Grid} from "@mui/material";
import {connect} from "react-redux";
import './OverviewCard.css';
import {useTranslation} from 'react-i18next';
import Constants from "../../../../../Constants";
import {Typography} from '@material-ui/core';
import eventAction from '../../../../../redux/action/reduxAction/event';
import CafeteriaFilled from '@mui/icons-material/Coffee';
import ExecutiveFilled from '@mui/icons-material/SupervisorAccount';
import ManagerFilled from '@mui/icons-material/EventSeat';
import WaterFountainFilled from "../../../Svg/FilledIcon/WaterFountainFilled"
import _ from "lodash";
import Tooltip from "@material-ui/core/Tooltip";
import {useLocation} from "react-router-dom";
import moment from "moment-timezone";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is responsible for showing the event analytics data related to
 * current online users count/attendance count,total registration count and total media played count(videos and images
 * played during the event).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Object} props.current_event Current event data
 * @param {Object} props.recurrences_analytics Current event recurrence analytics data
 * @returns {JSX.Element}
 * @constructor
 */
let OverviewCard = (props) => {
    const location = useLocation();
    const query = new URLSearchParams(location.search);
    const from = query.get('from_date');
    const to = query.get('to_date');
    const start = moment();
    const today = start.format('YYYY-MM-DD');
    const Icons = ['', <CafeteriaFilled />, <ExecutiveFilled />, <ManagerFilled />, <WaterFountainFilled />]
    const IconsLabel = ['', "cafeteria event", "executive event" ,"manager event ", "waterfountain event" ]
    const {t} = useTranslation(['analyticsData', 'common']);

    const [overviewData, setOverviewData] = useState({
        tRegs: 0,
        tAttendee: 0,
        tImages: 0,
        tVideos: 0,
        tOnlineUsers: 0,
    });
    const [cardIcon, setCardIcon] = useState(Icons[0]);
    const [cardIconlabel, setCardIconLabel] = useState(Icons[0]);
    const [eventType, setEventType] = useState(null);
    useEffect(() => {
        if (_.has(props, ["current_event"]) && !isNaN(props.current_event?.event_type)) {
            setCardIcon(Icons[props.current_event?.event_type])
            setCardIconLabel(IconsLabel[props.current_event?.event_type])
        }
    }, [props.current_event])


    useEffect(() => {
        countOverviewData();
        setEventType(props.recurrences_analytics ? props.recurrences_analytics[0]?.event_type : null)
    }, [props.recurrences_analytics])
    console.log('shprops', props)
    const countOverviewData = () => {
        let totalRegs = 0;
        let totalAttendee = 0;
        let totalImages = 0;
        let totalVideos = 0;
        let totalOnlineUsers = 0;
        props.recurrences_analytics?.forEach(analytics => {
            totalRegs += analytics.total_registration;
            totalAttendee += analytics.total_attendance;
            totalImages += analytics.media_image;
            totalVideos += analytics.media_video;
            totalOnlineUsers += analytics.total_current_online;
        });

        setOverviewData({
            ...overviewData,
            tRegs: totalRegs,
            tAttendee: totalAttendee,
            tImages: totalImages,
            tVideos: totalVideos,
            tOnlineUsers: totalOnlineUsers,
        });

    }
    return (
        <Grid container justifyContent={"center"}>
            <div className='bgIconWrap'>
                <Tooltip arrow title={t(`common:${cardIconlabel}`)} placement="right-center">
                    <div>
                        {cardIcon}
                    </div>
                </Tooltip>
            </div>
            <Grid
                container
                lg={12}
                direction="row"
                justifyContent="space-around"
                alignItems="center"
                className="bg_aliseBlue"
            >
                <Grid
                    container
                    alignItems={"center"}
                    justifyContent={"space-evenly"}
                    direction={"row"}
                    lg={9}
                >
                    {/* Handling current online users column in OverView card conditionally. If selected date is today's
                        date then Column value will be "Current Online Users" else "Total Attendee" */}
                    {eventType === Constants.eventFormType.ALL_DAY && (from === today && to === today)  ?
                        <div className="overview-item">
                            <p>{t('analyticsData:tOnlineUsers')}</p>
                            <Typography variant="h2" gutterBottom>
                                {overviewData.tOnlineUsers}
                            </Typography>
                        </div>
                        :
                        <div className="overview-item">
                            <p>{t('analyticsData:tAttendee')}</p>
                            <Typography variant="h2" gutterBottom>
                                {overviewData.tAttendee}
                            </Typography>
                        </div>
                    }
                    <div className="overview-item">
                        <p>{t('tReg')}</p>
                        <Typography variant="h2" gutterBottom>
                            {overviewData.tRegs}
                        </Typography>
                    </div>
                    <div className="overview-item">
                        <p>{t('analyticsData:tVidPlayed')}</p>
                        <Typography variant="h2" gutterBottom>
                            {overviewData.tVideos}
                        </Typography>
                    </div>
                    <div className="overview-item">
                        <p>{t('analyticsData:tImgPlayed')}</p>
                        <Typography variant="h2" gutterBottom>
                            {overviewData.tImages}
                        </Typography>
                    </div>
                </Grid>
            </Grid>
        </Grid>
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_analytics: state.Analytics.recurrences_analytics,
        current_event: eventAction.getCurrentEvent(state),
    }
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

OverviewCard = connect(mapStateToProps, mapDispatchToProps)(OverviewCard)

export default OverviewCard