import React, {useEffect, useState} from 'react';
import {Breadcrumbs, Link} from '@mui/material';
import Constants from "../../../../Constants";
import {useParams} from "react-router-dom";
import eventAction from "../../../../redux/action/reduxAction/event";
import {connect} from "react-redux";
import _ from 'lodash';
import {getFormValues} from "redux-form";
import './Breadcrumbs.css';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component renders the breadcrumbs with respect to the parameters passed
 * in props pass the links in a order to display and if that link is available (from redux or direct) then it will be
 * displayed, the order of links matter, from parent the order passed for links will be displayed as breadcrumbs
 *
 * The links must be from the constant defined for the breadcrumbs in Constants.breadcrumbsOptions
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent
 * @param {EventModel} props.formValues Event form fields
 * @param {EventModel} props.current_event Current event model
 * @param {GroupObj} props.current_group Current group of user
 * @param {String} props.organisation_name Name of the organisation
 * @param {Object} props.group_data If there is any custom group data passed
 * @param {String} props.group_data.group_name If there is any custom group data passed then its name
 * @param {Number[]} props.links Parameter which passed from parent and contains the links to display in breadcrumbs
 * @returns {JSX.Element}
 * @constructor
 */
let BreadcrumbsInput = (props) => {

    let availableLinks = {}
    let {gKey} = useParams();

    const [links, setLinks] = useState([]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the available links, here data will be checked from redux store if store contains the
     * data then the link will be added in available links so the e.g. if parent component needs the events name in
     * breadcrumb until the event is fetch the link will be not displayed
     * -----------------------------------------------------------------------------------------------------------------
     */
    const updateAvailableLinks = () => {
        // adding group in available link if its data is fetched
        if (props.current_group || props.group_data) {
            availableLinks[Constants.breadcrumbsOptions.GROUP_NAME] = {
                label: props.group_data ? props.group_data.group_name : props.current_group.group_name,
                // label: `Group - ${props.current_group.group_name}`,
                href: `/oit/${gKey}/dashboard`,
                underline: "hover",
            }
        }
        // adding events breadcrumb if its fetched
        if (props.current_event && props.formValues?.event_title) {
            availableLinks[Constants.breadcrumbsOptions.EVENT_NAME] = {
                label: props.formValues?.event_title || props.current_event.event_title,
                // label: `Event - ${props.formValues?.event_title || props.current_event.event_title}`,
                // href: `/${gKey}/v4/event-update/${props.current_event.event_uuid}`,
                underline: "none",
            }
        }

        // for event list nothing needs to be fetch so adding it directly
        availableLinks[Constants.breadcrumbsOptions.EVENTS_LIST] = {
            label: 'Events',
            href: `/oit/${gKey}/event-list`,
            underline: "hover",
        }

        // for showing new event label nothing needs to be fetched
        availableLinks[Constants.breadcrumbsOptions.NEW_EVENT] = {
            label: 'New Event',
        }

        // for showing all groups list page
        availableLinks[Constants.breadcrumbsOptions.GROUPS_LIST] = {
            label: "Groups",
            href: `/oit/${gKey}/manage-groups`,
            underline: "hover",
        }

        availableLinks[Constants.breadcrumbsOptions.MEDIA_TAB] = {
            label: "Media",
            underline: "none",
        }

        availableLinks[Constants.breadcrumbsOptions.USERS_TAB] = {
            label: "Users",
            underline: "none",
        }

        availableLinks[Constants.breadcrumbsOptions.GROUP_CREATE] = {
            label: "Group Create",
            underline: "none",
        }

        availableLinks[Constants.breadcrumbsOptions.DESIGN_SETTINGS] = {
            label: "Design Settings",
            underline: "none",
        }

        availableLinks[Constants.breadcrumbsOptions.TECHNICAL_SETTINGS] = {
            label: "Technical Settings",
            underline: "none",
        }

        if (gKey) {
            availableLinks[Constants.breadcrumbsOptions.USERS_LIST] = {
                label: 'Users',
                href: `/oit/${gKey}/user-setting`,
                underline: "hover",
            }
        }
        availableLinks[Constants.breadcrumbsOptions.DASHBOARD] = {
            label: "Dashboard",
            underline: "none",
        }

        availableLinks[Constants.breadcrumbsOptions.ANALYTICS] = {
            label: "Analytics",
            underline: "none",
        }

        availableLinks[Constants.breadcrumbsOptions.ENGAGEMENT] = {
            label: "Engagement",
            underline: "none",
        }

        if (props.organisation_name) {
            availableLinks[Constants.breadcrumbsOptions.ORGANISATION_NAME] = {
                label: props.organisation_name,
                href: '/oit/',
                underline: 'hover',
            }
        }

        if (props.authUser) {
            availableLinks[Constants.breadcrumbsOptions.SELF_USER] = {
                label: `${props.authUser.fname} ${props.authUser.lname}`,
                underline: 'none',
            }
        }
        availableLinks[Constants.breadcrumbsOptions.CHANGE_UPDATE_PASSWORD] = {
            label: `Update Password`,
            underline: 'none',
        }

        availableLinks[Constants.breadcrumbsOptions.MANAGE_PILOT_AND_OWN] = {
            label: `Manage Pilots And Owners`,
            underline: 'none',
        }

        if (gKey) {
            availableLinks[Constants.breadcrumbsOptions.MANAGE_USERS] = {
                label: 'Manage Users',
                href: `/oit/${gKey}/user-setting`,
                underline: "hover",
            }
        }

        availableLinks[Constants.breadcrumbsOptions.ALL] = {
            label: `All`,
            underline: 'none',
        }


        availableLinks[Constants.breadcrumbsOptions.ADD_USER] = {
            label: `Add User`,
            underline: 'none',
        }

        availableLinks[Constants.breadcrumbsOptions.IMPORT_USER] = {
            label: `Import Users`,
            underline: 'none',
        }

        if (props.otherUser?.fname || props.otherUser?.lname) {
            availableLinks[Constants.breadcrumbsOptions.OTHER_USER_NAME] = {
                label: `${props.otherUser.fname} ${props.otherUser.lname}`,
                underline: 'none',
            }
        }

        availableLinks[Constants.breadcrumbsOptions.TAGS] = {
            label: `Tags`,
            underline: 'none',
        }

    }

    useEffect(() => {
        // preparing the links for the breadcrumbs
        updateAvailableLinks();

        let linksInOrder = [];

        // looping through the parent links so as the order passed from parent should be considered
        // so the order passed from parent will be displayed if the link is available to display
        props.links.forEach(link => {
            // checking if available links have the link that is sent from parent
            if (_.has(availableLinks, [link])) {
                linksInOrder.push(availableLinks[link]);
            }
        })

        setLinks(linksInOrder);
    }, [props.links, props.current_event, props.current_group, props.organisation_name,]);

    return (
        <Breadcrumbs aria-label="breadcrumb" className={"breadcrumb-top"}>
            {links.map(link => {
                return <Link underline={link.underline || "none"} color="inherit" href={link.href}>{link.label}</Link>
            })}
        </Breadcrumbs>
    )
}


const mapStateToProps = (state) => {
    return {
        formValues: getFormValues('eventManageForm')(state),
        current_event: eventAction.getCurrentEvent(state),
        current_group: state.Group.current_group,
        organisation_name: state.Group.organisation_name,
        authUser: state.Auth.userSelfData,
    };
};

BreadcrumbsInput = connect(mapStateToProps, null)(BreadcrumbsInput);

export default BreadcrumbsInput;