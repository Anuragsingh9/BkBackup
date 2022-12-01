import React from 'react';
import {CopyToClipboard} from "react-copy-to-clipboard";
import {Grid, IconButton} from "@material-ui/core";
import CoPresentOutlinedIcon from '@mui/icons-material/CoPresentOutlined';
import PeopleAltOutlinedIcon from '@mui/icons-material/PeopleAltOutlined';
import CopyBtnIcon from "../../Svg/CopyBtnIcon";
import PersonIcon from '@mui/icons-material/Person';
import LaunchIcon from '@mui/icons-material/Launch';
import {Tooltip} from '@mui/material';
import {useTranslation} from 'react-i18next';
import {useAlert} from "react-alert";
import SpeakerLinkIcon from '../Svg/SpeakerLinkIcon';
import ModeratorIcon from '../Svg/ModeratorIcon';

/**
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is component is used for event links
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {Array} props.value Array of url and url type
 * @param {String} props.value.link Url of HE application
 * @return {JSX.Element}
 * @constructor
 */
const LinkComponent = (props) => {
    console.log('sdfsdf', props.value.type)
    const {type} = props.value;
    const {t} = useTranslation("eventCreate");
    const alert = useAlert();
    const linkIconObj = {
        "participants_link": <PeopleAltOutlinedIcon />,
        "manual_access": <CoPresentOutlinedIcon />,
        "moderator_links": <ModeratorIcon />,
        "speaker_links": <SpeakerLinkIcon />,
    }



    return (
        <div className='linkComponentWrap'>
            <Tooltip title={t(`${type}`)} arrow>
                <span>
                    {linkIconObj[`${type}`]}
                </span>
            </Tooltip>
            <span className="linkCoppierMenu">
                <span className="MainLinkSpan">{props.value.link}</span>

                <span className="iconCoppier">
                    <CopyToClipboard text={props.value.link} onCopy={() => {
                        alert.show('Copied to Clipboard', {type: 'success'})
                    }}>
                        <Tooltip title={t("copyLink")} arrow>
                            <IconButton size="small">
                                <CopyBtnIcon fontSize="inherit" />
                            </IconButton>
                        </Tooltip>
                    </CopyToClipboard>
                    <Tooltip title={t("redirectEvent")} arrow>
                        <a href={props.value.link} target="_blank" rel="noopener noreferrer">
                            <LaunchIcon fontSize="small" color="action" />
                        </a>
                    </Tooltip>
                </span>
            </span>
        </div>
    )
}

export default LinkComponent;