import React from 'react';
import {NavLink, useParams} from 'react-router-dom';
import './EventTags.css';
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import {Button} from '@material-ui/core';
import {makeStyles} from '@material-ui/core/styles';
import {Link} from '@material-ui/core';
import {useTranslation} from 'react-i18next';
import Tooltip from '@material-ui/core/Tooltip';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component of event tags which is including a link to go organizer tags page to add
 * tags for the event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component for event tags
 * @param {Function} props.handleNext This function will allow user to move to next step in event preparation
 * @returns {JSX.Element}
 * @constructor
 */

const EventTags = (props) => {
    const {t} = useTranslation("events");
    const {gKey} = useParams();

    return (
        <>
            <div className="GroupTagLinkStepper">
                <Link href={`/oit/${gKey}/org-tags`}
                      target="_blank"
                      underline='always'
                >
                    Manage Tags
                </Link>
                <Tooltip arrow title={t("modify_group_tags")}>
                    <InfoOutlinedIcon />
                </Tooltip>
            </div>
            <div className="BottomActionButtonDiv">
                <Button
                    variant="contained"
                    color="primary"
                    onClick={props.handleNext}
                >
                    Next
                </Button>
            </div>
        </>
    )
}

export default EventTags;