import React, {useEffect, useState} from "react";
import MomentComp from "./MomentComponent/MomentComp.js";
import {reduxForm} from "redux-form";
import {useDispatch, useSelector} from "react-redux";
import "./KeyMoments.css";
import ContentEventFilledIcon from "../../../Svg/ContentEventFilledIcon";
import NetworkingEventFilledIcon from "../../../Svg/NetworkingEventFilledIcon";
import {Button, Grid, MenuItem, Select, Switch} from "@material-ui/core";
import MultiRange from "../../../Common/MultiRangeComponent/MultiRange.js";
import {confirmAlert} from "react-confirm-alert";
import _ from "lodash";
import moment from "moment-timezone";
import eventAction from "../../../../redux/action/apiAction/event.js";
import {useAlert} from "react-alert";
import Helper from "../../../../Helper.js";
import Constants from "../../../../Constants";
import momentRepo from "../../../../repositories/EventMomentRepository";
import {useTranslation} from "react-i18next";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is parent component for key moments section which contains child components for all type of
 * key moments functionality and shows the moments time section and details for each kind of moments and can also
 * be updated
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Boolean} props.accessMode To check the access mode
 * @param {Boolean} props.anyTouched To check if any input is touched or not
 * @returns {JSX.Element}
 * @constructor
 */
const KeyMoments = (props) => {
    const dispatch = useDispatch();
    const alert = useAlert();
    const {handleSubmit, initialize} = props;
    // const [contentMoments, setContentData] = useState([]);
    // const [networkingMoments, setNetworkData] = useState([]);
    const [eventData, setEventData] = useState({});
    const [momentsData, setMomentsData] = useState([]);
    const [availableBroadcasts, setAvailableBroadcast] = useState([]);
    const newEvent = useSelector((data) => data.Auth.eventDetailsData);
    const [accessMode, setMode] = useState(false);
    const [isAutoCreate, setAutoCreate] = useState(0);
    const [autoSwitch, setAutoSwitch] = useState(false);
    // for localization
    const {t} = useTranslation([
        "mapping",
        "notification",
        "eventList",
        "confirm",
    ]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the moments data for a particular event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const getMomentsData = () => {
        let params = props.match.params;
        try {
            dispatch(eventAction.getMoments(params.event_uuid))
                .then((res) => {
                    if (_.has(res.data, ["available_broadcast"])) {
                        setAvailableBroadcast(res.data.available_broadcast);
                    }
                    setAutoCreate(
                        res.data.is_auto_key_moment_event
                            ? res.data.is_auto_key_moment_event
                            : false
                    );
                    setAutoSwitch(
                        res.data.is_auto_key_moment_event &&
                        res.data.is_auto_key_moment_event == 1
                            ? true
                            : false
                    );
                    setMomentsData(momentRepo.mapResponseToMoments(res, newEvent.date));
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is used for updating moments data.This method is used in other components as well.
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} newMoment New moment data
     * @param {Number} newMoment.contentType Type of the content
     * @param {Number} newMoment.broadcastType Type of broadcast
     */
    const handleMomentUpdate = (newMoment) => {
        if (newMoment.contentType !== null) {
            newMoment.moment_type =
                Constants.contentToMomentAlias[newMoment.contentType];
            if (newMoment.broadcastType !== null) {
                newMoment.moment_type =
                    Constants.broadcastToMomentAlias[newMoment.broadcastType];
            }
        }

        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description - this method is used for listing the new moment data and updating state for moment data
         * -------------------------------------------------------------------------------------------------------------
         *
         * @method
         */
        const newMomentsData = momentsData.map((oldMoment) => {
            return (oldMoment.id !== undefined && oldMoment.id === newMoment.id) ||
            (oldMoment.localKey !== undefined &&
                oldMoment.localKey === newMoment.localKey)
                ? newMoment
                : oldMoment;
        });
        setMomentsData(newMomentsData);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - this method is used for updating time from newEvents data and new event data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    useEffect(() => {
        const {time_state} = newEvent;
        setMode(time_state.is_live == 1);
        setEventData(newEvent);
        getMomentsData();
    }, []);

    //for getting language data from redux store
    const language = useSelector((state) => state.Auth.language);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for updating time from newEvents data and new event data when langauge values
     * changes
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    useEffect(() => {
        const {time_state} = newEvent;
        setMode(time_state.is_live == 1);
        setEventData(newEvent);
        getMomentsData();
    }, [language]);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for fetch api and sending the final data on server
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const submitForm = () => {
        // if (momentRepo.filterMomentsByType(momentsData, 'content').length === 0
        //     || momentRepo.filterMomentsByType(momentsData, 'network').length === 0) {
        //     return alert.show('There must be at least one moment for each type', {type: 'error'});
        // }

        // final data is preparing the final moment data that is modified according to sending the moment data on server
        let finalData = {
            moments: momentRepo.prepareMomentsForApi(momentsData),
            is_auto_key_moment_event: isAutoCreate == 1 ? 1 : 0,
            event_uuid: props.match.params.event_uuid,
        };
        try {
            dispatch(eventAction.postKeyMoments(finalData))
                .then((res) => {
                    alert.show("Record Updated Success", {type: "success"});
                    props.setIsAutoCreated(isAutoCreate == 1)
                    props.setShowLiveTab(isAutoCreate === 1 ? true : false)
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    // const addData = (state, type) => {
    //     const newStateData = [...state];
    //     const newTotal = momentsData;
    //     if (_.isEmpty(momentsData)) {
    //         const currentTime = moment(`${eventData.date} ${eventData.start_time}`)._d;
    //         const newStart = add_minutes(currentTime, 0);
    //         const newEnd = add_minutes(currentTime, 10);
    //             const newData = {
    //                 type: type,
    //                 start_time: convertDateToTime(newStart),
    //                 end_time: convertDateToTime(newEnd),
    //                 date: eventData.date,
    //                 name: `${type}-1`
    //             };
    //         const mainKey = `name-[0]-${type == 'content' ? 'content' : ''}`
    //         initialize({[mainKey]: `${type}-1`})
    //         newStateData.push(newData);
    //         newTotal.push(newData);
    //     } else {
    //         const {maxIndex, maxTime, types} = getLastIndex(momentsData);
    //
    //         const currentTime = moment(`${eventData.date} ${eventData.end_time}`)._d;
    //         const max = moment(`${eventData.date} ${maxTime}`)._d;
    //
    //         const length = newStateData.length;
    //
    //         if (currentTime.getTime() > max.getTime()) {
    //             // event time is  more.
    //             // const newStart = types == type ? add_minutes(max,10) : add_minutes(max,0) ;
    //             // const newEnd = types == type ? add_minutes(max,20) : add_minutes(max,10);
    //             const newStart = add_minutes(max, 0);
    //             const newEnd = add_minutes(max, 10);
    //             const newData = {
    //                 type: type,
    //                 start_time: convertDateToTime(newStart),
    //                 end_time: convertDateToTime(newEnd),
    //                 date: eventData.date,
    //                 name: `${type}-${length + 1}`
    //             };
    //
    //             newStateData.push(newData);
    //             newTotal.push(newData);
    //         } else {
    //             // event time is less
    //             // const newStart = types == type ? add_minutes(max,10) : add_minutes(max,0);
    //             // const newEnd = types == type ? add_minutes(max,20) : add_minutes(max,10);
    //
    //             const newStart = add_minutes(max, 0);
    //             const newEnd = add_minutes(max, 10);
    //             const newData = {
    //                 type: type,
    //                 start_time: convertDateToTime(newStart),
    //                 end_time: convertDateToTime(newEnd),
    //                 date: eventData.date,
    //                 name: `${type}-${length + 1}`
    //             };
    //
    //             newStateData.push(newData);
    //             newTotal.push(newData);
    //             setEventData({...newEvent, end_time: convertDateToTime(newEnd)});
    //         }
    //
    //     }
    //     setMomentsData(newTotal)
    //     return newStateData;
    // }

    // const getLastIndex = (state) => {
    //     let maxIndex = 0;
    //     let maxTime = '00:00:00';
    //     let types = '';
    //
    //     state.map((item, key) => {
    //         const currentTime = moment(`${eventData.date} ${item.end_time}`)._d;
    //         const max = moment(`${eventData.date} ${maxTime}`)._d;
    //
    //         if (currentTime.getTime() > max.getTime()) {
    //             maxTime = item.end_time;
    //             maxIndex = key;
    //             types = item.type
    //         }
    //
    //     });
    //     return {maxIndex, maxTime, types};
    // }

    // const add_minutes = (dt, minutes) => {
    //     return moment(dt.getTime() + minutes * 60000)._d;
    // }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This is method is used to delete a specific moment
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} key Unique key for the moment
     */
    const deleteContent = (key) => {
        const newData = momentsData.filter((item, index) => key != index);
        setMomentsData(newData);
    };

    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This is method is used to delete all moments data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} key Unique key for the moment
     */
    const onDeleteTotal = (key) => {
        if (momentsData.length == 1) {
            return setMomentsData([]);
        }

        const newData = momentsData.filter((item, index) => key != index);
        setMomentsData(newData);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for delete a specific networking data
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} key Unique key for a moment
     */
    const deleteNetworking = (key) => {
        return deleteContent(key);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method shows a confirmation model before delete the data and on 'confirm' it delete the data
     * and on 'cancel'  it returns null value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} key Key of the moment
     * @param {String} content Content data
     * @param {String} index Index of the selected moment
     */
    const onDelete = (key, content, index) => {
        confirmAlert({
            message: `Are you sure you want to delete`,
            confirmLabel: "confirm",
            cancelLabel: "cancel",
            buttons: [
                {
                    label: "yes",
                    onClick: () => {
                        if (content) {
                            deleteContent(key);
                            onDeleteTotal(index);
                        } else {
                            deleteNetworking(key);
                            onDeleteTotal(index);
                        }
                    },
                },
                {
                    label: "no",
                    onClick: () => {
                        return null;
                    },
                },
            ],
        });
    };

    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used adding new moment
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} type Type of the moment
     */
    const addMoment = (type) => {
        // as by default the content type moment is pre-recorded
        let newMomentsData = momentsData.map((i) => i);
        newMomentsData.push(
            momentRepo.createMoment(
                type,
                momentsData,
                moment(`${eventData.date} ${eventData.start_time}`)
            )
        );
        const mainKey = `name-[0]-${
            type !== Constants.momentType_networking ? "content" : ""
        }`;
        initialize({[mainKey]: `${type}-1`});
        setMomentsData(newMomentsData);
        // const newData = addData(momentsData, type);
        // setMomentsData(newData);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handle time changes
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {MomentObj} state Moments data
     * @param {Object} e Moments time data
     * @param {String} k Key for the moment
     * @param {String} i Index for the moment
     * @returns {Object} Object of moment
     */
    const handleTimeChange = (state, e, k, i) => {
        const index = i;
        const key = k;
        const value = e;
        const updatedValue = `${value.h}:${value.m}:00`;
        return state.map((item, ind) => {
            if (ind == index) {
                return {
                    ...item,
                    [key]: updatedValue,
                };
            }
            return item;
        });
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This methods is used for creating auto moments that has already selected the initial broadcasting
     * type and moderator and other data as well
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const addAutoCreateMoment = (type) => {
        // as by default the content type moment is pre-recorded
        //remove all data to add on auto create data only
        setMomentsData([]);
        // const array = newMomentsData
        let newMomentsData = momentsData.map((i) => i);
        newMomentsData.push(
            momentRepo.createAutoMoment(
                Constants.momentType_meeting,
                momentsData,
                moment(`${eventData.date} ${eventData.start_time}`),
                moment(`${eventData.date} ${eventData.end_time}`)
            )
        );
        newMomentsData.push(
            momentRepo.createAutoMoment(
                Constants.momentType_networking,
                momentsData,
                moment(`${eventData.date} ${eventData.start_time}`),
                moment(`${eventData.date} ${eventData.end_time}`)
            )
        );
        setMomentsData(newMomentsData);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling auto create switch that creates the auto moments
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleAutoCreate = () => {
        setAutoSwitch(autoSwitch == true ? false : true);
        setAutoCreate(autoSwitch == true ? 0 : 1);
    };
    useEffect(() => {
        confirmDeleteOldMoments();
        if (isAutoCreate && _.isEmpty(momentsData)) {
            checkCurrentState();
        }

        // checkCurrentState();
    }, [isAutoCreate]);

    useEffect(() => {
        if (isAutoCreate && _.isEmpty(momentsData)) {
            checkCurrentState();
        }

        // checkCurrentState();
    }, [momentsData, isAutoCreate]);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method shows a confirmation model before delete the data and on 'confirm' it delete the data
     * and on 'cancel'  it returns null value
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const confirmDeleteOldMoments = (id) => {
        if (isAutoCreate && !_.isEmpty(momentsData)) {
            confirmAlert({
                message: `${t("confirm:sure")}`,
                confirmLabel: t("confirm:confirm"),
                cancelLabel: t("confirm:cancel"),
                buttons: [
                    {
                        label: t("confirm:yes"),
                        onClick: () => {
                            setMomentsData([]);
                        },
                    },
                    {
                        label: t("confirm:no"),
                        onClick: () => {
                            setAutoSwitch(false);
                            setAutoCreate(false);
                        },
                    },
                ],
            });
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for checking the current state for auto create moments
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const checkCurrentState = () => {
        if (isAutoCreate == 1) {
            addAutoCreateMoment();
        } else {
            setMomentsData([]);
            // getMomentsData();
        }
    };

    const onTimeChange = (e, k, i, type) => {
        // if (type == 'content') {
        //     const newState = handleTimeChange(contentMoments, e, k, i);
        //     setContentData(newState);
        // } else {
        //     const newState = handleTimeChange(networkingMoments, e, k, i);
        //     setNetworkData(newState);
        // }
    };

    const handleChangeState = (state, e, index) => {
        const key = e.target.name.split("-")[0];
        const updatedValue = e.target.value;
        return state.map((item, ind) => {
            if (ind == index) {
                return {
                    ...item,
                    [key]: updatedValue,
                };
            }
            return item;
        });
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method is used for time change and update the moment time
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Moments time data
     * @param {String} k Key for the moment
     * @param {String} i Index for the moment
     */
    const totalTimeChange = (e, k, i) => {
        const newState = handleTimeChange(momentsData, e, k, i);
        setMomentsData(newState);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for  update the total data for moments
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     * @param {String} index Index of the moment
     */
    const updateTotal = (e, index) => {
        const key = e.target.name.split("-")[0];
        const updatedValue = e.target.value;
        const updated = momentsData.map((item, ind) => {
            if (ind == index) {
                return {
                    ...item,
                    [key]: updatedValue,
                };
            }
            return item;
        });
        setMomentsData(updated);
    };

    const handleChange = (e, i, type) => {
        // if (type == 'content') {
        //     const newState = handleChangeState(contentMoments, e, i);
        //     return setContentData(newState);
        // } else {
        //     const newState = handleChangeState(networkingMoments, e, i);
        //     return setNetworkData(newState);
        // }
    };

    const handleMultipleChange = (values, i, type, index) => {
        // let newData = {};
        // values.forEach(e => {
        //     const key = e.target.name.split('-')[0];
        //     newData[key] = e.target.value;
        // });
        // let moments = type === 'content' ? contentMoments : networkingMoments;
        // let newState = moments.map((item, ind) => {
        //     if (ind == 1) {
        //         return {
        //             ...item,
        //             ...newData,
        //         }
        //     }
        //     return item
        // });
        // setContentData(newState);
        // const updated = momentsData.map((item, ind) => {
        //     if (ind == index) {
        //         return {
        //             ...item,
        //             ...newData,
        //         }
        //     }
        //     return item
        // });
        // setMomentsData(updated);
    };

    const findIndex = (state, data) => {
        let index = 0;
        state.filter((val, i) => {
            if (_.isEqual(val, data)) {
                index = i;
            }
        });

        return index;
    };

    const getIndex = (item) => {
        const {type} = item;

        // if (type == 'content') {
        //     return findIndex(contentMoments, item)
        // } else {
        //     return findIndex(networkingMoments, item)
        // }
    };


    const renderMoments = () => {
        return (
            !_.isEmpty(momentsData) &&
            momentsData.map((item, index) => {
                return (
                    <MomentComp
                        availableBroadcasts={availableBroadcasts}
                        accessMode={props.accessMode || accessMode}
                        name={item.name}
                        start_time={item.start_time}
                        end_time={item.end_time}
                        date={item.date}
                        type={item.moment_type}
                        id={item.id}
                        localKey={item.localKey}
                        momentData={item}
                        onMomentUpdate={handleMomentUpdate}
                        // old props
                        onTimeChange={(e, k, i) => {
                            onTimeChange(e, k, i, item.type);
                            totalTimeChange(e, k, index);
                        }}
                        onDelete={(key, content) => {
                            onDelete(key, content, index);
                        }}
                        index={getIndex(item)}
                        onChange={(e, k) => {
                            handleChange(e, k, item.type);
                            updateTotal(e, index);
                        }}
                        onChangeMultiple={(values, k) => {
                            handleMultipleChange(values, k, item.type, index);
                        }}
                        content={item.type == "content" ? 1 : 0}
                        data={item}
                        disabled={isAutoCreate === 1 ? true : false}
                        autoCreate={isAutoCreate === 1 ? true : false}
                    />
                );
            })
        );
    };
    return (
        <React.Fragment>
            {/* Content moments time bar */}
            <Grid container spacing={3} className="multiRangeDivFlex">
                <Grid item lg={2} className="verticalFlexDiv">
                    <ContentEventFilledIcon />
                </Grid>
                <Grid item lg={10}>
                    <MultiRange
                        content={true}
                        data={momentRepo.filterMomentsByType(momentsData)}
                        total={{
                            start_time: eventData.start_time,
                            end_time: eventData.end_time,
                        }}
                        date={eventData.date}
                        type="Content"
                    />
                </Grid>
            </Grid>
            {/* Networking moments time bar */}
            <Grid container spacing={3} className="multiRangeDivFlex">
                <Grid item lg={2} className="verticalFlexDiv">
                    <NetworkingEventFilledIcon />
                </Grid>
                <Grid item lg={10}>
                    <MultiRange
                        data={momentRepo.filterMomentsByType(
                            momentsData,
                            Constants.momentType_networking
                        )}
                        total={{
                            start_time: eventData.start_time,
                            end_time: eventData.end_time,
                        }}
                        date={eventData.date}
                        type="Networking"
                    />
                </Grid>
            </Grid>

            <form onSubmit={handleSubmit(submitForm)}>
                <Grid container spacing={3} className="addKeyMomentDiv">
                    <Grid item lg={2} className="verticalFlexDiv">
                        <p classname="customPara">Auto-Create:</p>
                    </Grid>
                    <Grid item lg={10} className="addkeyMoment_btn">
                        <Switch
                            checked={autoSwitch}
                            color="primary"
                            name="checkButton"
                            onChange={handleAutoCreate}
                            accessMode={props.accessMode || accessMode}
                        />
                    </Grid>
                </Grid>
                {renderMoments()}
                {!isAutoCreate && (
                    <Grid container spacing={3} className="addKeyMomentDiv">
                        <Grid item lg={2} className="verticalFlexDiv">
                            <p classname="customPara">Key Moment:</p>
                        </Grid>
                        <Grid item lg={10} className="addkeyMoment_btn">
                            <Button
                                color="primary"
                                variant="contained"
                                className="addKeyMomentBtn"
                            >
                                <Select
                                    labelId={`demo-simple-select-label`}
                                    name={"add"}
                                    id="demo-simple-select"
                                    value={0}
                                    disabled={props.accessMode || accessMode}
                                >
                                    <MenuItem
                                        value={0}
                                        className="selectedHiddenOption"
                                        disabled
                                        hidden
                                    >
                                        &nbsp; Add
                                    </MenuItem>
                                    <MenuItem
                                        value={1}
                                        onClick={() => addMoment(Constants.momentType_youtube)}
                                    >
                                        Add a content moment
                                    </MenuItem>
                                    <MenuItem
                                        value={2}
                                        onClick={() => addMoment(Constants.momentType_networking)}
                                    >
                                        Add a networking moment
                                    </MenuItem>
                                </Select>
                            </Button>
                        </Grid>
                    </Grid>
                )}

                <div className="KeyMomentsSaveDiv">
                    <Button type="submit" color="primary" variant="contained">
                        Save
                    </Button>
                </div>
            </form>
        </React.Fragment>
    );
};

export default reduxForm({
    form: "MomentsForm", // a unique identifier for this form
    keepDirtyOnReinitialize: true,
})(KeyMoments);
