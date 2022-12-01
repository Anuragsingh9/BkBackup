import {React, useState, useEffect} from 'react';
import {useDispatch, useSelector} from 'react-redux';
import _ from 'lodash';
import Helper from '../../../../Helper';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used in filtering the users of an event among the different roles
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Function} callBack This method is responsible for fetching event users of an event
 * @param {String} currentFilter To indicate which filter option is selected currently
 * @param {Event} newEvent Event related data
 * @param {Object} roleList All role list for the event participants filter
 * @param {String} roleList.space_host Search key for space host
 * @param {String} roleList.team Search key for team
 * @param {String} roleList.expert Search key for expert
 * @param {String} roleList.vip Search key for vip
 * @param {String} roleList.moderator Search key for moderator
 * @param {String} roleList.speaker Search key for speaker
 * @return {JSX.Element}
 * @constructor
 */
const FilterComp = ({callBack, current, currentFilter, newEvent, roleList}) => {
    const [showTeam, setShowTeam] = useState(false);
    const [showParticipants, setShowParticipants] = useState(true);
    const [activeTab, setactiveTab] = useState("");
    const language = useSelector((state) => state.Auth.language);

    useEffect(() => {

    }, [showTeam, language]);

    /**
     * @deprecated
     */
    const TabActive = () => {
        if (activeTab === "") {
            setactiveTab("selected_Heading");
        } else {
            setactiveTab("");
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle active state for "participant's List" filter option.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const showPartTeam = () => {
        if (showParticipants === false) {
            setShowParticipants(true);
            setShowTeam(false);
        } else {
            setShowParticipants(false);
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle active state for 'Event Team' filter option.If the 'Event team' is active
     * then all the categories(Space host, business team, vip, expert, moderator, speaker) will be display below it to
     * further categorization.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} props Inherited from parent component
     * @method
     */
    const showEventTeam = (props) => {
        // setShowTeam(true);
        if (showTeam === false) {
            setShowTeam(true);
            setShowParticipants(false);
        } else {
            setShowTeam(false);
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on "participant's List" option from side bar(In managing
     * participants & roles component) and this will call fetch user API to get simple user's(no other roles assigned)
     * for the list.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const participantClick = () => {
        callBack('event_user');
        showPartTeam();
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on 'Event Team' option from side bar(In managing
     * participants & roles component) and this will call fetch user API to get users related to selected category from
     * event team category option.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const eventTeamClick = () => {
        callBack();
        showEventTeam();
    }


    const eventRoleLabels = useSelector(data => data.Auth.eventRoleLabels.labels)

    /**
     * @deprecated
     */
    const list = [
        {
            key: 'space_host',
            label: !_.isEmpty(eventRoleLabels) ? Helper.getLabel('space_host', eventRoleLabels) : "Space Hosts"
        },
        {
            key: 'team',
            label: !_.isEmpty(eventRoleLabels) ? Helper.getLabel('business_team', eventRoleLabels) : "Team A"
        },
        {
            key: 'expert',
            label: !_.isEmpty(eventRoleLabels) ? Helper.getLabel('expert', eventRoleLabels) : "Team B"
        },
        {
            key: 'vip',
            label: !_.isEmpty(eventRoleLabels) ? Helper.getLabel('vip', eventRoleLabels) : "VIP"
        },
        {
            key: 'moderator',
            label: !_.isEmpty(eventRoleLabels) ? Helper.getLabel('moderator', eventRoleLabels) : "Moderators"
        },
        {
            key: 'speaker',
            label: !_.isEmpty(eventRoleLabels) ? Helper.getLabel('speaker', eventRoleLabels) : "Speakers"
        },
    ];

    const filteredTabs = roleList
        && roleList.filter(word => word.key !== "moderator").filter(word => word.key !== "speaker");


    return (
        <>
            <div className="HeadingSideDiv_1">
                <p
                    className={`heading participantsList ${showParticipants && " selected_Heading"}`}
                    onClick={participantClick}
                >
                    Participant's List
                </p>
                <p
                    className={`heading teamList ${showTeam && "selected_Heading"}`}
                    onClick={eventTeamClick}
                >
                    Event Team
                </p>
            </div>
            {showTeam &&
            <div className="HeadingSideDiv_2">
                {
                    newEvent.type === 1 ? filteredTabs.map((item) => {

                            return (
                                <p
                                    onClick={() => {
                                        callBack(item.key)
                                    }}
                                    className={`${currentFilter == item.key ? " selectedSubHead" : ''}`}
                                >
                                    {item.label}
                                </p>
                            )
                        })
                        :
                        roleList && roleList.map((item) => {


                            return (
                                <p
                                    key={item.label}
                                    onClick={() => {
                                        callBack(item.key)
                                    }}
                                    className={`${currentFilter == item.key ? " selectedSubHead" : ''}`}
                                >
                                    {item.label}
                                </p>
                            )
                        })
                }
            </div>}
        </>

    )
}

export default FilterComp;