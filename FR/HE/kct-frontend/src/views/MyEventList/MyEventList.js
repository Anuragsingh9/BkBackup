import React, {useEffect, useRef, useState} from "react";
import _ from 'lodash';
import {NavLink} from "react-router-dom";
import {useTranslation} from "react-i18next";
import ReactTooltip from "react-tooltip";
import {Provider as AlertContainer, useAlert} from 'react-alert';
import {connect} from "react-redux";
import ShowAgenda from './ShowAgenda/ShowAgenda';
import eventActions from "../../redux/actions/eventActions";
import Helper from "../../Helper";
import authActions from "../../redux/actions/authActions";
import Svg from "../../Svg";
import "./MyEventList.css";
import GroupLableDropdown from '../NewInterFace/Common/GroupLabelDropdown/GroupLableDropdown'
import Constants from "../../Constants";

var ReactBSTable = require("react-bootstrap-table");
var BootstrapTable = ReactBSTable.BootstrapTable;
var TableHeaderColumn = ReactBSTable.TableHeaderColumn;

let order = "desc";
const SIZEPERPAGE = 10;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to manage all future and past events data in tabular form.These event tables
 * contain some details(name, agenda, organizer name, date & time, agenda, roles and access button) of the event with
 * some additional feature to search specific event(From search component, placed on the top of event's past & future
 * table) and we can directly access the event from the table.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.event_list To fetch the event list from api
 * @param {Function} props.registerSpaceMood To register the user into event
 * @property {String} props.video_url Url Value for Video to show on QSS
 * @param {Label[]} props.event_labels All the event labels with different locales
 *
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
var MyEventList = (props) => {
    let msg = useAlert();
    const {t} = useTranslation(['eventList', 'qss'])
    const state = {eventsList: {}}
    const [tense, setTense] = useState("future");
    const [eventList, setEventList] = useState([]);
    const [data, setData] = useState([]);
    const [sizePerPage, setSizePerPage] = useState(SIZEPERPAGE);
    const [totalSize, setTotalSize] = useState(0);
    const [page, setPage] = useState(0);
    const [lastPage, setLastPage] = useState(1);
    const [isDataFetched, setIsDataFetched] = useState(false);
    const [isPageDataFetched, setIsPageDataFetched] = useState(false);
    const [sortName, setSortName] = useState(undefined);
    const [eventDetailsLoad, setEventDetailsLoad] = useState(false);
    const [showFilter, setShowFilter] = useState(false);
    const [list, setList] = useState([]);
    const [selectedKey, setSelectedKey] = useState('')

    // useEffect(() => {


    //   if (!reactLocalStorage.get('accessToken')) {
    //     props.history && props.history.push(`/quick-login`)
    //   }
    //   let data = {
    //     "tense ": 'future',
    //     "page" : 0,
    //     "sizePerPage" : SIZEPERPAGE,
    //     "order" : 'desc'
    //   }
    //   getEventList(data)
    //   // getEventList('future', 0, SIZEPERPAGE,)
    // }, [])
    // // }

    useEffect(() => {
        let data = {
            "tense": tense,
            "page": 0,
            "sizePerPage": SIZEPERPAGE,
            "groupKey": selectedKey,
            "order": 'desc'
        }
        getEventList(data)
        // getEventList(tense, 0, SIZEPERPAGE,"","","",selectedKey)
    }, [selectedKey])

    const redirection = () => {
        const {state} = props.location;
        if (_.has(state, ['location'])) {
            let lastLoc = state.location;
            if (lastLoc.includes('register')) {
                lastLoc = lastLoc.replace('register', 'otp');
                props.history && props.history.push(lastLoc);
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to get all events(future & past) data and once the request
     * completed successfully it will update all states with their related data(received from response).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data props passed from parent
     * @param {String} data.tense Url of page to redirect future/past wise
     * @param {Number} data.page Current page value
     * @param {Number} data.sizePerPage Number of items on per page
     * @param {String} data.searchText Text to search if any
     * @param {String} data.orderBy Field name by which order needs to be applied
     * @param {String} data.order Order of the list, descending or ascending
     */
    const getEventList = (data) => {
        const url = data.tense ? data.tense : tense
        setIsPageDataFetched(false);
        setIsDataFetched(false);
        setData([]);
        let sizePerPage = parseInt(data.sizePerPage);


        const sendingData = {
            "tense": data.tense,
            "page": data.page,
            "sizePerPage": sizePerPage,
            "searchText": data.searchText,
            "orderBy": data.orderBy,
            "order": data.order,
            "groupKey": selectedKey
        }

        props.eventList(sendingData).then((res) => {
            // props.eventList(url, page, sizePerPage, searchText, orderBy, order ,groupKey).then((res) => {

            setData(res.data.data);
            setEventDetailsLoad(true);
            setIsPageDataFetched(true);
            setIsDataFetched(true);
            setTotalSize(parseInt(res.data.meta.total));
            setPage(res.data.meta.current_page);
            setLastPage(res.data.meta.last_page);
            setSizePerPage(parseInt(res.data.meta.per_page))
            setShowFilter(res.data.meta.groups && res.data.meta.groups.length > 1)
            setList(res.data.meta.groups && res.data.meta.groups)
        }).catch(err => {
            setEventDetailsLoad(true);
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on 'Past Events' horizontal tab(placed at top of the list
     * on event list page) and this will call 'getEventList' function to get all past events data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const pastEvents = () => {
        setTense('past');
        setPage(0);

        let data = {
            "tense": 'past',
            "page": 0,
            "sizePerPage": SIZEPERPAGE,
            "order": 'desc'
        }
        getEventList(data)
        // getEventList('past', 0, SIZEPERPAGE,)

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user click on 'Future Events' horizontal tab(placed at top of the
     * list on event list page) and this will call 'getEventList' function to get all future events data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const futureEvents = () => {
        setTense('future');
        setPage(0);
        let data = {
            "tense": 'future',
            "page": 0,
            "sizePerPage": SIZEPERPAGE,
            "order": 'desc'
        }
        getEventList(data)
        // getEventList('future', 0, SIZEPERPAGE,)

    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function handle joining process and redirect it to next page
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} event_uuid
     * @method
     */
    const joinEvent = (event_uuid) => {
        props.history && props.history.push(`/quick-user-info/${event_uuid}`)
    }

    const joinTheEvent = (event_uuid) => {
        let formData = new FormData();
        formData.append('event_uuid', event_uuid)
        props.registerSpaceMood(formData).then((response) => {
            if (props.getOrganisationWithToken) {
                props.getOrganisationWithToken()
            }


            if (response.data.status) {
                props.history.push(`/dashboard/${event_uuid}`)
            } else {
            }
        })
            .catch((err) => {
                msg && msg.show(Helper.handleError(err), {type: 'error'});
            })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will handle(call) the API call method to get data as per selected pagination value and size per
     * page(how many events will show at a time) value.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page Current page value
     * @param {Number} sizePerPage Number of items on single page
     */
    const handlePageChange = (page, sizePerPage) => {

        setPage(page);
        setEventList([]);
        let data = {
            "tense": tense,
            "page": page,
            "sizePerPage": sizePerPage,

        }
        getEventList(data);
        // getEventList(tense, page, sizePerPage);
    };


    const handleSizePerPageChange = (sizePerPage) => {

        setSizePerPage(sizePerPage);
        setEventList([]);
        setIsDataFetched(false);
        let data = {
            "tense": tense,
            "page": 1,
            "sizePerPage": sizePerPage
        }
        getEventList(data);
        // getEventList(tense, 1, sizePerPage);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when we click on sort arrow(added in the table header eg- event name).
     * This function will call the API handler method(handleSortApi) to perform sorting.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} sortName Field name on which the sorting is applying
     * @param {String} sortOrder Order value for the list, descending or ascending
     */
    const onSortChange = (sortName, sortOrder) => {
        handleSortApi(sortName, sortOrder);
        setSortName(sortName);
        setIsPageDataFetched(false);
        setEventDetailsLoad(false);
        setIsDataFetched(false);
        setEventList([]);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when we click on sort arrow(added in the table header eg- event name).
     * This function will handle an API call to get sorted data according to column(in which user click sort button) in
     * ascending/descending order and update all the states related to it.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} sortName Field name on which the sorting is applying
     * @param {String} sortOrder Order value for the list, descending or ascending
     * @param {String} orderBy Field name by which order needs to be applied
     * @param {String} searchText Text to apply the search on list
     */
    const handleSortApi = (sortName, sortOrder, orderBy, searchText) => {

        let data = {
            "tense": tense,
            "page": page,
            "sizePerPage": sizePerPage,
            "searchText": searchText,
            // "orderBy":orderBy ,
            "orderBy": sortName,
            "order": sortOrder
        }
        try {
            getEventList(data)
                // getEventList(page, sizePerPage, searchText, orderBy, sortName,sortOrder)
                .then((res) => {
                    if (res.status) {
                        setEventList(res.data.data);
                        setIsPageDataFetched(true);
                        setIsDataFetched(true);
                        setTotalSize(res.data.meta.total);
                        setPage(res.data.meta.current_page);
                        setLastPage(res.data.meta.last_page);
                        setSizePerPage(res.data.meta.per_page)
                    }
                })
                .catch((err) => {
                    setIsPageDataFetched(true);
                    setIsDataFetched(true);
                });
        } catch (err) {
            setIsPageDataFetched(true);
            setIsDataFetched(true);
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function handle  searching on event list
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onSearchChange = (searchText, colInfos, multiColumnSearch) => {

        if (searchText.trim() && searchText.trim().length > 2) {
            setIsPageDataFetched(false);

            setEventList([]);
            try {
                let data = {
                    "tense": tense,
                    "page": 0,
                    "sizePerPage": sizePerPage,
                    "searchText": searchText
                }
                props.eventList(data)
                    // props.eventList(tense, 0, sizePerPage, searchText)
                    .then((res) => {
                        if (res.status) {
                            setData(res.data.data);
                            setIsPageDataFetched(true);
                            setIsDataFetched(true);
                            setTotalSize(res.data.data.length);
                            setPage(1);
                            setSizePerPage(res.data.data.length)
                        }
                    })
                    .catch((err) => {
                        console.error(err);
                        setIsPageDataFetched(true);
                        setIsDataFetched(true);
                        msg.show(Helper.handleError(err), {type: "error"})
                    });
            } catch (err) {
                console.error(err);
                setIsPageDataFetched(true);
                setIsDataFetched(true);
            }
        } else {
            if (searchText.length == 0) {
                let data = {
                    "tense": tense,
                    "page": 0,
                    "sizePerPage": SIZEPERPAGE,
                }
                getEventList(data);
                // getEventList(tense, 0, SIZEPERPAGE);
            }
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function handles time data for events and fill them in column regarding to events
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {*} cell
     * @param {*} row
     */
    const setTimeColumn = (cell, row) => {
        const {event_date} = row;
        const getDate = new Date(`${event_date} ${cell}`)
        const date = Helper.getTimeUserTimeZone('Europe/Paris', getDate, 'hh:mm A');
        return (
            <div>
                {Helper.dateTimeFormat(date, 'hh:mm A')}
            </div>
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will render event name with their respective registration status(User can register for
     * the specific event) - open/will open/closed for all events in the list (past/future).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {EventData} row Single event data
     * @param {Number} cell number of the event
     * @returns {JSX.Element}
     */
    const setEventName = (cell, row) => {
        const {event_title} = row;
        const {registration_closed, registration_not_open, registration_open} = row;

        return (
            <div>
                <p className="event_list_title">{event_title}</p>
                <p className="event_status">Registration:&nbsp;
                    <span
                        className={
                            registration_closed == 1
                                ? "closed"
                                : registration_not_open == 1
                                    ? "will_open"
                                    : registration_open == 1
                                        ? "open"
                                        : ""
                        }
                    >
            {registration_closed == 1 ? "CLOSED"
                : registration_not_open == 1 ? "NOT OPENED"
                    : registration_open == 1 ? "OPEN" : ""}
          </span>
                </p>
            </div>
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will render event date and time for all events in the list(past/future).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {EventData} row Single event data
     * @param {Number} cell number of the event
     * @returns {JSX.Element}
     */
    const setDateTime = (cell, row) => {
        const {event_date, event_start_time, event_end_time} = row;
        const getDate = new Date(`${event_date.replace(/-/g, "/")} ${event_start_time}`);
        const getEndDate = new Date(`${event_date.replace(/-/g, "/")} ${event_end_time}`);
        const date = Helper.getTimeUserTimeZone('Europe/Paris', getDate);
        return (
            <div>
                {Helper.dateTimeFormat(date, 'MMMM DD, YYYY')}<br />{Helper.dateTimeFormat(getDate, 'hh:mm A')} to {Helper.dateTimeFormat(getEndDate, 'hh:mm A')}

            </div>
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function handles date data for events and fill them in column regarding to events
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} cell Table cell related data
     * @param {Object} row Table row related data
     */
    const setDateColumn = (cell, row) => {
        const {event_date, event_start_time} = row;
        const getDate = new Date(`${event_date} ${event_start_time}`);
        const date = Helper.getTimeUserTimeZone('Europe/Paris', getDate);
        return (
            <div>
                {Helper.dateTimeFormat(date, 'MMMM DD, YYYY')}
            </div>
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will render event type(networking/Content+Networking) for all events in the
     * list(past/future).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {EventData} row Single event data
     * @param {Number} cell number of the event
     * @returns {JSX.Element}
     */
    const setEventType = (cell, row) => {
        const {type} = row;
        // const getDate = new Date(`${event_date} ${event_start_time}`);
        // const date = Helper.getTimeUserTimeZone('Europe/Paris',getDate);
        return (
            <div>
                {type == 2 ? 'Content + Networking' : "Networking"}
            </div>
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will render event agenda(no of moments will be there in an event) icon for all events
     * in the list(past/future).
     * when user click on agenda button this will open event agenda page in a new tab.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {EventData} row Single event data
     * @param {Number} cell number of the event
     * @returns {JSX.Element}
     */
    const setEventAgenda = (cell, row) => {
        const {event_date, event_start_time} = row;
        const agenda = row;
        const event_type = row.type;
        const event_uuid = row.event_uuid;
        const share_agenda = row.share_agenda;
        const getDate = new Date(`${event_date} ${event_start_time}`);
        const date = Helper.getTimeUserTimeZone('Europe/Paris', getDate);
        return (
            <div>
                {event_type == 2 && share_agenda == 1 &&

                <NavLink to={{
                    pathname: `/event-agenda/event_uuid=${event_uuid}`,
                    state: agenda,
                }}
                         target="_blank"
                >
                    <ShowAgenda agenda={row}></ShowAgenda>
                </NavLink>
                }
            </div>
        );
    }

    const setEventVersion = (cell, row) => {
        const {conference_type} = row;
        var ver = '';
        if (conference_type) {
            ver = "Yes";
        } else {
            ver = "No";
        }
        return (
            <div>{ver}</div>
        )
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will render  role(participants/moderator/spacehost) for the logged in user for their
     * related events in the list(past/future).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {EventData} row Single event data
     * @param {Number} cell number of the event
     * @returns {JSX.Element}
     */
    const setTableActions = (cell, row) => {
        const {conference_type} = row;
        const {is_participant, is_vip, event_role, is_moderator, is_presenter, is_host} = row;
        return (
            <div className="clearfix icon-group">
                {/* <div className="user-role-icons d-inline pr-10" >
            { conference_type != null && row.is_presenter == 1&&<span className={`svgicon ${!row.is_presenter?'svg-gray':''} svg-25 d-inline v-middle ml-5 mr-5`} dangerouslySetInnerHTML={{__html:Svg.ICON.presenter}} data-tip={row.is_presenter === 1?t("You are a presenter for that event"): t("You are not a presenter for that event")}></span>}
            { conference_type != null && row.is_moderator == 1&&<span className={`svgicon ${!row.is_moderator?'svg-gray':''} svg-25 d-inline v-middle ml-5 mr-5`} dangerouslySetInnerHTML={{__html:Svg.ICON.reporter}} data-tip={row.is_moderator === 1?t("You are a moderator for that event") : t("You are not a moderator for that event") }></span>}
            {row.is_host == 1&&<span className={`svgicon ${!row.is_host?'svg-gray':''} svg-25 d-inline v-middle`} dangerouslySetInnerHTML={{__html:Svg.ICON.pointing_man}} data-tip={row.is_host === 1? t("You are a host for that event in space"): t ("You are a not host for that event in space")}></span>}
          </div>

          <div className="user-role-icons d-inline">
            {conference_type != null && <span className="svgicon svg-25 d-inline v-middle ml-5 mr-5" dangerouslySetInnerHTML={{__html:Svg.ICON.attendee}} data-for='user_roll1' data-tip={t("You are an attendee for the conference")}></span>}
            <span className="svgicon svg-25 d-inline v-middle ml-5 mr-5" dangerouslySetInnerHTML={{__html:Svg.ICON.juice}} data-for='user_roll1'  data-tip={t ("keepcontact networking post-event")}></span>
          </div> */}

                <div className="user-role-icons d-inline pr-10">

                    {is_participant == true && is_vip == 1 ? <span dangerouslySetInnerHTML={{__html: Svg.ICON.VIP_icon}}
                                                                   data-tip={Helper.getLabel('vip', props.event_labels)}></span> : ""}
                    {is_participant == true && is_vip == 0 && is_host == 0 && is_moderator == 0 && is_presenter == 0 && (event_role == null || event_role === 0)
                        ?
                        <span dangerouslySetInnerHTML={{__html: Svg.ICON.Participants_icon}}
                              data-tip={Helper.getLabel("participant", props.event_labels)}>

                        </span>
                        : event_role == 1 ? <span dangerouslySetInnerHTML={{__html: Svg.ICON.Team_a_icon}}
                                                  data-tip={Helper.getLabel("business_team", props.event_labels)}>

                                            </span>
                            : event_role == 2 ? <span dangerouslySetInnerHTML={{__html: Svg.ICON.Team_b_icon}}
                                                      data-tip={Helper.getLabel("expert", props.event_labels)}>

                                                </span>
                                : (
                                    is_host === 0 && is_moderator === 0 && is_presenter === 0
                                        ? "-"
                                        : ""
                                )}
                    {is_participant == true && is_presenter == 1 ?
                        <span dangerouslySetInnerHTML={{__html: Svg.ICON.Speaker_icon}}
                              data-tip={Helper.getLabel("speaker", props.event_labels)}>

                        </span> : ""}
                    {is_participant == true && is_moderator == 1 ?
                        <span dangerouslySetInnerHTML={{__html: Svg.ICON.Moderator_icon}}
                              data-tip={Helper.getLabel("moderator", props.event_labels)}>

                        </span> : ""}
                    {is_participant == true && is_host == 1 ?
                        <span dangerouslySetInnerHTML={{__html: Svg.ICON.Space_Host_icon}}
                              data-tip={Helper.getLabel("space_host", props.event_labels)}>

                        </span> : ""}


                    {/* {is_participant ?
            [(
              {}
            )]

            } */}
                </div>
                <ReactTooltip type="dark" effect="solid" id='user_roll1' />


            </div>
        );
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will render action button to perform join the event action with additional features:
     * 1. 'Join as moderator' if logged in user is a moderator of a specific event.
     * 2. 'Join as speaker' if logged in user is a speaker of a specific event.
     * 2. 'Join in Rehearsal Mode' if user wants to join rehearsal mode.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {EventData} row Single event data
     * @param {Number} cell number of the event
     * @returns {JSX.Element}
     */
    const join_link = (cell, row) => {
        const {is_banned} = row;
        return (
            <React.Fragment>
                <ReactTooltip type="dark" effect="solid" />

                {!row.is_participant ?
                    // <div
                    //     className={`btn event_join_btn ${is_banned ? 'disabled' : ''}`}
                    //     data-tip={is_banned == 1 ? t("You are banned for this event") : ''}
                    //     disabled={is_banned}
                    //     onClick={() => joinEvent(row.event_uuid)}
                    // >
                    //   {tense === 'past' ? t("Access") : t("Register")}
                    // </div>
                    <div className={`btn ${is_banned ? 'disabled' : ''} btn-group`}>
                        <NavLink
                            to={`/dashboard/${row.event_uuid}`}
                            className={`btn event_join_btn ${(_.has(row, ['links']) && !_.isEmpty(row.links)) 
                                ? 'event_join_btn_dropdown event_join_btn_split' 
                                : 'event_join_btn'}  ${is_banned ? 'disabled' : ''}`}
                        >
                            {tense === 'past'
                                ? t("Access")
                                : t("Register")
                            }
                        </NavLink>
                        {(_.has(row, ['links']) && !_.isEmpty(row.links)) &&
                        <>
                            <button type="button"
                                    className="btn  event_join_btn event_join_btn_dropdown dropdown-toggle dropdown-toggle-split"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span className="sr-only">Toggle Dropdown</span>
                            </button>
                            <div className="dropdown-menu dropMenu">
                                {
                                    (_.has(row.links, ['moderator_links']) &&
                                        row.links.moderator_links.map(link => {
                                            return <a className="dropdown-item" target="_blank" href={link.link}>Join as
                                                Moderator ({link.moment_name})</a>
                                        })
                                    )
                                }
                                {
                                    (_.has(row.links, ['speaker_links']) &&
                                        row.links.speaker_links.map(link => {
                                            return <a className="dropdown-item" target="_blank" href={link.link}>Join As
                                                Speaker ({link.moment_name})</a>
                                        })
                                    )
                                }
                                {
                                    (_.has(row.links, ['access_code']) && row.links.access_code &&
                                        <div className="dropdown-item"
                                             onClick={() => props.history.push(`/dashboard/${row.event_uuid}?access_code=${row.links.access_code}`)}>Join
                                            in Rehearsal Mode</div>
                                    )
                                }
                            </div>
                        </>
                        }
                    </div>
                    :
                    <div data-tip={is_banned == 1 ? t("You are banned for this event") : ''}>
                        <div className="btn-group ">
                            <NavLink
                                className={`btn event_join_btn ${(_.has(row, ['links']) && !_.isEmpty(row.links) && row.event_type !== Constants.EVENT_TYPES.ALL_DAY) ? 'event_join_btn_dropdown event_join_btn_split' : 'event_join_btn'}  ${is_banned ? 'disabled' : ''}`}
                                to={`/dashboard/${row.event_uuid}`}
                            >
                                {tense === 'past' ? t("Access") : t(`Join`)}
                            </NavLink>
                            {(_.has(row, ['links']) && !_.isEmpty(row.links)) && row.event_type !== Constants.EVENT_TYPES.ALL_DAY && tense !== 'past' &&
                            <>
                                <button type="button"
                                        className="btn  event_join_btn event_join_btn_dropdown dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span className="sr-only">Toggle Dropdown</span>
                                </button>
                                <div className="dropdown-menu dropMenu">
                                    {
                                        (_.has(row.links, ['moderator_links']) &&
                                            row.links.moderator_links.map(link => {
                                                return <a className="dropdown-item" target="_blank" href={link.link}>Join
                                                    as
                                                    Moderator ({link.moment_name})</a>
                                            })
                                        )
                                    }
                                    {
                                        (_.has(row.links, ['speaker_links']) &&
                                            row.links.speaker_links.map(link => {
                                                return <a className="dropdown-item" target="_blank" href={link.link}>Join
                                                    As
                                                    Speaker ({link.moment_name})</a>
                                            })
                                        )
                                    }
                                    {
                                        (_.has(row.links, ['access_code']) && row.links.access_code &&
                                            <div className="dropdown-item"
                                                 onClick={() => props.history.push(`/dashboard/${row.event_uuid}?access_code=${row.links.access_code}`)}>Join
                                                in Rehearsal Modejj</div>
                                        )
                                    }
                                </div>
                            </>
                            }
                        </div>


                        {/*<div className="dropdown dropdown-no-margin">*/}
                        {/*    <a className="drop-btn dropdown-toggle" href="#" role="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">*/}
                        {/*        <div className="btn event_join_btn">Access*/}
                        {/*            /!*<span className="fa fa-chevron-down"></span>*!/*/}
                        {/*        </div>*/}
                        {/*    </a>*/}
                        {/*    <div className="dropdown-menu" aria-labelledby="dropdownMenuLink">*/}
                        {/*        <NavLink className="dropdown-item" to={/dashboard/}>Access*/}
                        {/*        </NavLink>*/}
                        {/*        <NavLink className="dropdown-item" to="/event-list">Join As Moderator*/}
                        {/*        </NavLink>*/}
                        {/*        <div  className="dropdown-item" >Join As Speaker*/}
                        {/*        </div>*/}
                        {/*    </div>*/}
                        {/*</div>*/}
                        {/*<NavLink className={`btn event_join_btn ${is_banned?'disabled':''}`}  to={`/dashboard/${row.event_uuid}`}>{tense === 'past'? t("Access") : t("Join")}</NavLink>*/}
                    </div>
                }
            </React.Fragment>
        )
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function take first name and the last name of organizer for the specific event and merge them to
     * render in organizer column of the event list - past/future.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} row Single event data
     * @param {String} row.organiser_fname First name of organiser
     * @param {String} row.organiser_lname Last name of organiser
     * @param {Number} cell number of the event
     * @returns {String}
     */
    function nameFormatter(cell, row) {
        return `${row.organiser_fname} ${row.organiser_lname}`
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will sort event list data alphabetically according to organizer's first and last
     * name(received from parameter).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} a Single event data
     * @param {String} a.organiser_fname First name of organiser
     * @param {String} a.organiser_lname Last name of organiser
     * @param {Object} b Single event data
     * @param {String} b.organiser_fname First name of organiser
     * @param {String} b.organiser_lname Last name of organiser
     * @param {Object} order order data of response
     * @param {EventData[]} order.data List of event in sorted
     */
    const revertSortFunc = (a, b, order) => {

        // order is desc or asc
        const sortarray = order.data;
        if (b === "desc") {
            const data = [...sortarray].sort((a, b) => {
                if (b.organiser_fname.toLowerCase() < a.organiser_fname.toLowerCase())
                    return -1;
                if (b.organiser_fname.toLowerCase() > a.organiser_fname.toLowerCase())
                    return 1;
                return 0;
            });
            setData(data)
        }
        if (b === "asc") {
            const data = [...sortarray].sort((a, b) => {
                if (a.organiser_fname.toLowerCase() < b.organiser_fname.toLowerCase())
                    return -1;
                if (a.organiser_fname.toLowerCase() > b.organiser_fname.toLowerCase())
                    return 1;
                return 0;
            });
            setData(data)
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will check data in events list(past/future).If there is not a single event in the list
     * then it will show "There is no data to display" text in the event table.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {JSX.Element}
     */
    const setTableOption = () => {
        if (!isPageDataFetched) {
            return (
                <Helper.pageLoading />
            );
        }
        return t("There is no data to display");
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is using debouncing concept for search api call . it slow down the api calls on every
     * events on typing and set api call on 0.5 sec after last event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const timeout = useRef()
    const handleDebounceSearch = (text) => {
        clearTimeout(timeout.current)
        if (text.trim() && text.trim().length > 2) {
            timeout.current = setTimeout(() => {
                onSearchChange(text)
            }, 500)
        } else {
            if (text.length == 0) {
                let data = {
                    "tense": tense,
                    "page": 0,
                    "sizePerPage": SIZEPERPAGE,
                }
                getEventList(data);
            }
        }
    }

    return (


        <div className="clearfix w-100 future-event-pg" style={{"position": "relative"}}>
            <AlertContainer
                ref={msg}
                {...Helper.alertOptions}
            />
            <div className="container tab-section">
                <div className="clearfix d-inline w-100">
                    <h3 class="site-color2 mb-30 mt-30">{t("Events List")}</h3>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-8 eventList_filterColumn pl-0">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 customFilterDesign pl-0">
                            {showFilter && <GroupLableDropdown list={list} setSelectedKey={setSelectedKey} />}
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <ul class="nav navbar-nav tab-menu nav-tabs line-menu  mb-20">
                                <li>
                                    <a className={`cursor-pointer ${tense === 'future' && 'active'}`}
                                       onClick={futureEvents}>{t("Future Events")}</a>
                                </li>
                                <li>
                                    <a className={`cursor-pointer ${tense === 'past' && 'active'}`}
                                       onClick={pastEvents}>{t("Past Events")}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div className="col-xs-12 nopadding tab-menu-content">
                    <ReactTooltip type="dark" effect="solid" id='abc' />
                    <div className="clearfix mb-50 mt-30">
                        <BootstrapTable
                            data={data}
                            bordered={false}
                            hover
                            remote
                            ignoreSinglePage
                            pagination={
                                (totalSize > parseInt(sizePerPage)) ? true : false
                            }
                            searchPlaceholder={t("Search")}
                            search
                            fetchInfo={{dataTotalSize: totalSize}}
                            containerClass='table-style1 mt-30'
                            options={{
                                onPageChange: handlePageChange,
                                page: page,
                                onSortChange: onSortChange,
                                noDataText: setTableOption(),
                                onSearchChange: (e) => handleDebounceSearch(e)  //onSearchChange
                            }}
                        >
                            <TableHeaderColumn
                                dataField="event_title"
                                dataFormat={(cell, row) => setEventName(cell, row)}
                                dataAlign='center'
                                width='180'
                                isKey
                                dataSort={true}
                            >
                <span data-tip={t("Event Name")} data-for='abc'>
                  {t("Event Name")}
                </span>
                            </TableHeaderColumn>

                            <TableHeaderColumn
                                dataField="type"
                                dataFormat={(cell, row) => setEventType(cell, row)}
                                width='165'
                                dataAlign='center'
                            >
                <span data-tip={t("Event Type")} data-for='abc'>
                  {t("Event Type")}
                </span>
                            </TableHeaderColumn>

                            <TableHeaderColumn
                                dataField='organiser_lname'
                                dataAlign='center'
                                dataFormat={nameFormatter}
                                width='136'
                                sortFunc={() => revertSortFunc()}
                            >
                <span data-tip={t("Organizer")} data-for='abc'>
                  {t("Organizer")}
                </span>
                            </TableHeaderColumn>

                            <TableHeaderColumn
                                dataField="event_date"
                                dataFormat={(cell, row) => setDateTime(cell, row)}
                                width='165'
                                dataAlign='center'
                                dataSort={true}
                            >
                <span data-tip={t("Date & Time")} data-for='abc'>
                  {t("Date & Time")}
                </span>
                            </TableHeaderColumn>

                            {/*   <TableHeaderColumn dataField="event_date" dataFormat={(cell, row) => setDateColumn(cell, row)} width='145' dataAlign='center' dataSort={true}>
                  <span  data-tip={t("Date & Time")}  data-for='abc'>
                  {t("Event Date")}
                  </span>
                </TableHeaderColumn>
               <TableHeaderColumn dataField="event_start_time"  dataFormat={setTimeColumn}  width="141" dataAlign='center' dataSort={true}>
                <span  data-tip= {t("Start Time")}   data-for='abc'>
                  {t("Start Time")} 
                </span>
                            </TableHeaderColumn>

                            <TableHeaderColumn
               dataField="event_date" dataFormat={(cell, row) => setEventAgenda(cell, row)} width='115' dataAlign='center'>
                <span data-tip={t("Event Agenda")} data-for='abc'>
                  {t("Agenda")}
                </span>
                            </TableHeaderColumn>
                            {/* Event type */}
                            <TableHeaderColumn
                                dataField="myroles"
                                dataAlign='center'
                                width='145'
                                dataFormat={(cell, row) => setTableActions(cell, row)}
                            >
                <span data-tip={t("My Roles")} data-for='abc'>
                  {t("My Roles")}
                </span>
                            </TableHeaderColumn>

                            <TableHeaderColumn
                                dataField="join_link"
                                dataAlign='center'
                                width='146'
                                dataFormat={(cell, row) => join_link(cell, row)}
                            >
                <span data-tip={t("Action")} data-for='abc'>
                  {t("Action")}
                </span>
                            </TableHeaderColumn>
                        </BootstrapTable>
                        <ReactTooltip type="dark" effect="solid" id='abc' />
                    </div>
                </div>
            </div>
        </div>

    );

}

const mapDispatchToProps = (dispatch) => {
    return {
        eventList: (data) => dispatch(eventActions.Event.eventList(data)),
        // eventList: (id, page, sizePerPage, search, order_by, order ,group_key) => dispatch(eventActions.Event.eventList(id, page, sizePerPage, search, order_by, order, group_key)),
        registerSpaceMood: (data) => dispatch(authActions.Auth.registerSpaceMood(data)),
        // sortEvents: (id, page, sizePerPage, search) => dispatch(eventActions.Event.eventList(id, page, sizePerPage, search)),
    }
}

const mapStateToProps = (state) => {
    return {
        video_url: state.page_Customization.initData.graphics_data.video_url,
        event_labels: state.page_Customization.initData.labels,
    };
};

MyEventList = connect(mapStateToProps, mapDispatchToProps)(MyEventList);

/**
 * @param {Object} props Props passed from parent component
 * @returns {JSX.Element}
 * @constructor
 */
const EventList = (props) => {
    return (<div> Hlelo</div>)
}

export default MyEventList;

