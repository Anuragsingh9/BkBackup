import React, {useEffect, useRef, useState} from "react";
import "./PilotPannel.css";
import Helper from "../../../Helper";
import {useTranslation} from "react-i18next";
import {CopyToClipboard} from 'react-copy-to-clipboard';
import CopyIcon from "../../../images/copyIcon.png";
import ReactTooltip from "react-tooltip";
import {Provider as AlertContainer, useAlert } from 'react-alert';
import Svg from "../../../Svg";
import moment from "moment";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to show pilot panel event information
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.event_meta Current event meta data
 * @param {String} props.event_meta.event_actual_date Event actual date
 * @param {Number} props.event_meta.is_rehearsal_mode Is event in rehearsal mode or not
 * @param {String} props.event_meta.event_actual_start_time Event actual start time
 * @param {String} props.event_meta.event_actual_end_time Event actual end time
 * @param {String} props.event_meta.join_link event join link
 * @param {EventData} props.event_data Current event data
 * @returns {JSX.Element}
 * @constructor
 */
const PanelTitle = (props) => {
    const msg = useAlert();
    const {t} = useTranslation('pilotPanel');
    const startTime = new Date(`${props.event_meta.event_actual_date.replace(/-/g, "/")} ${props.event_meta.event_actual_start_time}`);
    const endTime = new Date(`${props.event_meta.event_actual_date.replace(/-/g, "/")} ${props.event_meta.event_actual_end_time}`);
    const toolTipDescription = props.event_meta.is_rehearsal_mode ? t('REHEARSAL_MODE_DESC') : t('LIVE_MODE_DESC');
    const showAlert = () => {
        msg && msg.show(t("COPIED_TO_CLIP"), {type: "success"})
    }
    const LIVE = 1;
    const REHEARSAL = 2;
    const [rehearsalMode, setRehearsalMode] = useState(LIVE);
    const [joinLink, setJoinLink] = useState('');

    useEffect(() => {
        let event_data = props.event_data;
        const timeZone = 'Europe/Paris';

        let actualStartTime = moment(`${event_data.event_actual_date} ${event_data.event_actual_start_time}`).toDate();
        let actualEndTime = moment(`${event_data.event_actual_end_date} ${event_data.event_actual_end_time}`).toDate();

        let actualStartDiff = Helper.getTimeDifference(timeZone, actualStartTime);
        let actualEndDiff = Helper.getTimeDifference(timeZone, actualEndTime);
        if(actualStartDiff < 0 && actualEndDiff > 0 ) {
            setJoinLink(props.event_meta.short_join_link);
            setRehearsalMode(LIVE);
        } else if(props.event_meta.is_rehearsal_mode) {
            setRehearsalMode(REHEARSAL);
            setJoinLink(props.event_meta.join_link)
        }
    }, [props.event_data, props.event_meta]);


    return (

        <div className="pilotPannel_title">
            <ReactTooltip type="dark" effect="solid" id='isolation_btn' />
            <AlertContainer ref={msg} {...Helper.alertOptions} />
            <h3 className="pilotPannel_titleTxt">{props.event_data.event_title}</h3>
            <p className="pilotPannel_time">
                {Helper.dateTimeFormat(startTime, 'DD MMMM, YYYY')}
            </p>
            <p className="pilotPannel_time">
                {Helper.dateTimeFormat(startTime, 'hh:mm A')} {t('TO')}
                {Helper.dateTimeFormat(endTime, 'hh:mm A')}
            </p>
            <div className="pannelCopyBtn">
                <h5 className={rehearsalMode === REHEARSAL ? "rehearsal" : "live"}>
                    {rehearsalMode === REHEARSAL ? t('REHEARSAL_MODE') : t('LIVE_MODE')}
                </h5>
                <CopyToClipboard text={joinLink}>
                    <button type="button" className="copy-button" onClick={showAlert}>
                    <span
                        data-for='isolation_btn'
                        data-tip={toolTipDescription}
                        dangerouslySetInnerHTML={{__html: Svg.ICON.copyIcon}}>
                    </span>
                    </button>
                </CopyToClipboard>
            </div>

        </div>
    );
}

export default PanelTitle;