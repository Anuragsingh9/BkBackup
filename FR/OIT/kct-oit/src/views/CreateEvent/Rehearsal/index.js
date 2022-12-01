import React, {useEffect, useState} from 'react';
import eventAction from '../../../redux/action/apiAction/event';
import _ from 'lodash';
import './Rehearsal.css';
import {Grid} from '@material-ui/core';
import {CopyToClipboard} from 'react-copy-to-clipboard';
import {useAlert} from 'react-alert';
import {IconButton} from '@material-ui/core';
import Helper from '../../../Helper';
import CopyBtnIcon from '../../Svg/CopyBtnIcon.js';
import LoadingContainer from '../../Common/Loading/Loading.js';
import LinkSharpIcon from '@material-ui/icons/LinkSharp';
import {useDispatch, useSelector} from 'react-redux'
import {useTranslation} from 'react-i18next';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component renders the different event links linked to an event as follows:
 * -> Participant's Link : URL generated to be shared with all the participant's of the Event. User's can use this link
 *    to login/register for a specific Event.
 * -> Moderator's Link : URL generated to be used by the Moderator to start a Zoom Meeting/Webinar.
 * -> Speaker's Link : URL generated to be used and shared by a participant to enter Zoom Meeting/Webinar as a Speaker.
 * -> Rehearsal Link : Special URL which allows any user with the link to enter into an Event in live mode.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @param {Object} props.match Match object that contains route related information such as url,path
 * @param {Object} props.history History object
 * @param {Object} props.location Location object
 * @param {String} props.name Current page name
 * @param {String} props.path Current page path
 */

const Rehearsal = (props) => {

    const dispatch = useDispatch();
    const [eventLinks, setLinks] = useState({});
    const [loading, setLoading] = useState(true);
    const alert = useAlert();
    const language = useSelector((state) => state.Auth.language);
    const {t} = useTranslation(["labels"]);


    const eventRoleLabels = useSelector(data=> data.Auth.eventRoleLabels.labels)
    const eventRolelabelCustomized = useSelector(data=> data.Auth.eventRoleLabels.label_customized);

    // this hook is used for get links method calls when language and role labels changes
    useEffect(()=>{
        var params = props.match.params;
        if (_.has(params, ['event_uuid'])) {
            getLink(params.event_uuid);
        }
    }, [language, eventRoleLabels, eventRolelabelCustomized])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to fetch links data with event id from the server by API call and displays
     * associated links with their labels.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Event id to fetch all event links(eg- Participatns link , Rehearsal Link etc.)
     */
    const getLink = (id) => {
        try {
            dispatch(eventAction.getEventLinks(id)).then((res) => {
                let links = {"participants_link": [], "moderator_links": [], "speaker_links": [], "manual_access": []}
                if (_.has(res.data.data, ['participants_link']) && !_.isEmpty(res.data.data.participants_link)) {
                    links['participants_link'].push({
                        label: `For ${eventRolelabelCustomized === 1
                            ? Helper.getLabel('participants', eventRoleLabels)
                            : t("labels:Participants")}  : `,
                        link: res.data.data.participants_link,
                    })
                }
                if (_.has(res.data.data, ['moderator_links']) && _.isArray(res.data.data.moderator_links)
                    && !_.isEmpty(res.data.data.moderator_links)) {
                    res.data.data.moderator_links.forEach(moment => {
                        links['moderator_links'].push({
                            label: `For ${eventRolelabelCustomized === 1
                                ? Helper.getLabel("moderator", eventRoleLabels)
                                : t("labels:Moderator")}  ( ${moment.moment_name} ) : `,
                            link: moment.link,
                        });
                    });
                }
                if (_.has(res.data.data, ['speaker_links']) && _.isArray(res.data.data.speaker_links)
                    && !_.isEmpty(res.data.data.speaker_links)) {
                    res.data.data.speaker_links.forEach(moment => {
                        links['speaker_links'].push({
                            label: `For ${eventRolelabelCustomized === 1
                                ? Helper.getLabel("speaker", eventRoleLabels)
                                : t("labels:Speaker")}  ( ${moment.moment_name} ) : `,
                            link: moment.link,
                        });
                    });
                }
                if (_.has(res.data.data, ['manual_access']) && !_.isEmpty(res.data.data.manual_access)) {
                    links['manual_access'].push({
                        label: `For Rehearsal Link : `,
                        link: res.data.data.manual_access,
                    })
                }

                setLinks(links);
                setLoading(false);
            }).catch((err) => {
                alert.show(Helper.handleError(err), {type: 'error'});
                setLoading(false);
            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'});
            setLoading(false);
        }
    }


    return (

        <LoadingContainer loading={loading}>
            {Object.keys(eventLinks).map((keyName, i) => {
                return !_.isEmpty(eventLinks[keyName]) ? eventLinks[keyName].map(link => {
                    return <div className="RehearsalFlexDiv">
                        <Grid container spacing={3}>
                            <Grid item lg={3}>
                                <span className="customPara">{link.label} </span>
                            </Grid>
                            <Grid item lg={5}>
                                <span className="linkCoppier">
                                    <span className="MainLinkSpan">{link.link}</span>
                                    <span className="iconCoppier">
                                            <CopyToClipboard text={link.link} onCopy={() => {
                                            alert.show('Copied to Clipboard', {type: 'success'})
                                        }}>
                                            <IconButton size="small">
                                                <CopyBtnIcon fontSize="inherit" />
                                            </IconButton>
                                        </CopyToClipboard>
                                        <a href={link.link} target="_blank"
                                            rel="noopener noreferrer">
                                            <LinkSharpIcon fontSize="small" color="secondary" />
                                        </a>
                                    </span>
                                </span>
                            </Grid>
                        </Grid>
                    </div>
                }) :
                    null
            })}
        </LoadingContainer>
    )
}

export default Rehearsal;