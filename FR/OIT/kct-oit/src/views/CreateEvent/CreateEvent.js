import React from 'react';
import './CreateEvent.css';
import Analytics from "../CreateEvent/Analytics/index.js";
import EventPreparation from "../CreateEvent/EventPreparation/index.js";
import InvitationPlan from "../CreateEvent/InvitationPlan/index.js";
import EventMedia from "../CreateEvent/Live/index.js";
import EventNoteIcon from '@material-ui/icons/EventNote';
import Rehearsal from "./Rehearsal/RehearsalDiv";
import NavTabs from '../Common/NavTabs/NavTabs.js';
import _ from 'lodash';
import Container from '@material-ui/core/Container';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is wrapper component used to render Event Preparation, Invitation Plan, Event Links, Live and
 * Event Analytics pages in horizontal nav tab form.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @return {JSX.Element}
 */
const CreateEvent = (props) => {
    const [state, setState] = React.useState(null)
    const [showEventLinks, setShowEventLinks] = React.useState(false);
    const [showLiveTab, setShowLiveTab] = React.useState(false);
    const [isAutoCreated, setIsAutoCreated] = React.useState(false);
    var params = props.match.params;

    const tabData = [
        {
            label: 'EVENT PREPARATION',
            href: '/contacts',
            child: <EventPreparation
                {...props}
                setShowEventLinks={setShowEventLinks}
                setShowLiveTab={setShowLiveTab}
                setIsAutoCreated={setIsAutoCreated}
                isAutoCreated={isAutoCreated}
            />
        },
        {
            label: 'INVITATION PLAN',
            href: '',
            disable: !_.has(params, ['event_uuid']),
            child: <InvitationPlan
                {...props}
                setShowEventLinks={setShowEventLinks}
                setShowLiveTab={setShowLiveTab}
            />
        },
        {
            label: 'EVENT LINKS',
            href: '/manage-org',
            disable: !_.has(params, ['event_uuid']) || !showEventLinks,
            child: <Rehearsal {...props} />
        },
        {
            label: 'LIVE',
            href: '/manage-org',
            disable: !_.has(params, ['event_uuid']) || !showLiveTab,
            child: <EventMedia {...props} />
        },
        {
            label: 'ANALYTICS',
            href: '/manage-org',
            disable: true,
            child: <Analytics />
        },
    ]

    return (
        <>
            <div className="CreateEventWrap">
                <EventNoteIcon />
                <NavTabs tabId="user-tabs" tabData={tabData} {...props} />
            </div>
        </>
    )
}
export default CreateEvent