import React, {useEffect, useState} from "react";
import "./Mapping.css";
import InfoOutlinedIcon from "@material-ui/icons/InfoOutlined";
import Radio from "@material-ui/core/Radio";
import RadioGroup from "@material-ui/core/RadioGroup";
import FormControlLabel from "@material-ui/core/FormControlLabel";
import FormControl from "@material-ui/core/FormControl";
import {Grid, TextField} from "@material-ui/core";
import Button from "@material-ui/core/Button";
import {connect, useDispatch} from "react-redux";
import eventAction from "../../../../redux/action/apiAction/event";
import {useAlert} from "react-alert";
import {Field, reduxForm} from "redux-form";
import Helper from "../../../../Helper";
import LoadingContainer from "../../../Common/Loading/Loading";
import Switch from "@material-ui/core/Switch";
import _ from "lodash";
import {useTranslation} from "react-i18next";
import MultiSpaces from "./MultiSpaces/MultiSpaces";
import Tooltip from "@material-ui/core/Tooltip";
import {confirmAlert} from "react-confirm-alert";
import {useParams} from "react-router-dom";
import MenuItem from "@mui/material/MenuItem";
import Select from "@mui/material/Select";
import ImgSlider from "../../../Common/ImgSlider/ImgSlider";
import ColorPicker from "../../../Common/ColorPickerNew/ColorPickerNew.js";
import Slider from "@mui/material/Slider";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is used to monitor user inputs and throw error if the input values are not matching the
 * proper criteria(eg - max capacity value must be greater then 12 and less then 1000)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the mapping venue values
 * @param {String} values.max_capacity Space user max capacity
 * @param {String} values.space_host Space host name
 * @return {Object}  different type of error message objects
 */
const validate = (values) => {
    const errors = {};
    const requiredFields = [
        // 'header_line_1',
        // 'header_line_2',
        "max_capacity",
        "space_host",
    ];
    requiredFields.forEach((field) => {
        if (!values[field]) {
            errors[field] = "Required";
        }
    });
    if (values["max_capacity"] < 12) {
        errors["max_capacity"] = "value should be between 12 and 1000";
    }
    if (values["max_capacity"] > 1000) {
        errors["max_capacity"] = "value should be between 12 and 1000";
    }
    if (values["header_line_1"] && values.header_line_1.length > 44) {
        errors["header_line_1"] =
            "The Event Header Line 1 of the event must not exceed 44 characters ";
    }
    if (values["header_line_2"] && values.header_line_2.length > 56) {
        errors["header_line_2"] =
            "The Event Header Line 2 of the event must not exceed 56 characters ";
    }

    return errors;
};

const renderTextField = ({
                             input,
                             value,
                             label,
                             defaultValue,
                             meta: {invalid, touched, error},
                             ...custom
                         }) => {
    return (
        <React.Fragment>
            <TextField
                name={input.name}
                value={value}
                onChange={input.onChange}
                errorText={touched && error}
                error={touched && error && invalid}
                {...input}
                {...custom}
            />
            {touched && error && <span className={"text-danger"}>{error}</span>}
        </React.Fragment>
    );
};

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This section (mapping the venue) is developed to allow pilot of the event to handle/manage spaces in an
 * event.In space a pilot can add space host,set space capacity,set scenery data(background image and color),add header
 * lines for the event.
 * <br>
 * <br>
 * Maximum User = 1000, Minimum User = 12 (per space)
 * Types of venue:-
 * 1. Mono Space - If venue type is mono then it means event will have only one space(default space).
 * 2. Spaces - If venue type is not mono then it means event can have multiple spaces(normal/VIP space/default
 * space).
 * <br>
 * <br>
 * Default Space:- default space is that space which is created by default at the time of event creation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent component
 * @param {Event} props.event_data Event related data
 * @param {Function} props.getSpaces [Dispatcher] Method to fetch all spaces of an event
 * @param {Boolean} props.isAutoCreated To indicate if event has auto created moment
 * @param {Function} props.updateSpaces [Dispatcher] Method to update space data like space name,max capacity,space host
 * @returns {JSX.Element}
 * @constructor
 */
var MappingVenue = (props) => {
    const {t} = useTranslation(["mapping", "notification", "events"]);
    const {handleSubmit, pristine, reset, submitting, initialize, accessMode} =
        props;
    const [showHeader, setShowHeader] = useState(false);
    const [monoSpaceList, setMonoSpaceList] = useState([]);
    const [multiSpaceList, setMultiSpaceList] = useState([]);
    const [allSpacesData, setAllSpacesData] = useState([]);
    const dispatch = useDispatch();
    const [headerLine1, setHeaderLine1] = useState(null);
    const [headerLine2, setHeaderLine2] = useState(null);
    const [availableSceneries, setAvailableSceneries] = useState([]);

    // states for scenery/categories collection data
    const [currentScenery, setCurrentScenery] = useState(null);
    const [availableSceneryImages, setAvailableSceneryImages] = useState([]);
    const [currentSceneryAsset, setCurrentSceneryAsset] = useState(null);

    // states for current assets manage
    const [currentAssetType, setCurrentAssetType] = useState(null);
    const [currentAssetColor, setCurrentAssetColor] = useState({
        field: "top_background_color",
        value: {r: 255, g: 255, b: 255, a: 1},
    });

    // states for top color and component opacity
    const [topBgColor, setTopBgColor] = useState({
        field: 'top_background_color',
        value: {"r": 255, "g": 255, "b": 255, "a": 1},
    });
    const [assetColor, setAssetColor] = useState(null);
    const [componentOpacity, setComponentOpacity] = useState(92);

    const [spaceData, setSpaceData] = useState({
        header_line_1: "",
        header_line_2: "",
        spaceHost: "",
        spaceId: "",
        max_capacity: 1000,
        is_mono_space: "",
        is_self_header: "",
    });
    const WHITE_COLOR = {
        r: 255, g: 255, b: 255, a: 1,
    }
    const [hostName, setHostName] = useState([]);
    const [selectHost, setSelectHost] = useState([]);
    const [spaceHostId, setSpaceHostId] = useState();
    const [loading, setLoading] = useState(true);
    const [check, setCheck] = useState(false);
    const [venueType, setVenueType] = useState("2");
    const [showSpaceLine, setShowSpaceLine] = useState(false);
    const [spaceLines, setSpaceLines] = useState({
        spaceLine1: "",
        spaceLine2: "",
    });
    const [event_status, setEvent_status] = useState();
    const [formOpenStatus, setFormOpenStatus] = useState(false);

    const alert = useAlert();
    const MONO_SPACE = "1";
    const MULTI_SPACE = "2";
    const BOOTH_SPACE = "3";

    const {gKey} = useParams();

    const ASSET_TYPE_IMAGE = 1;
    const ASSET_TYPE_COLOR = 2;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for getting data of scenery by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} id Scenery Id
     * @returns {SceneryDataObj}
     */
    const getSceneryById = (id) => {
        return availableSceneries.find((scenery) => {
            return scenery.category_id === id;
        });
    };

    const getAssetById = (id) => {
        if (currentScenery && currentScenery.category_assets) {
            return currentScenery.category_assets.find(asset => asset.asset_id === id);
        }
        return null;
    }
    const getSceneryType = (scenery) => {
        let assetType = null;
        if (scenery && scenery.category_assets && scenery.category_assets.length) {
            scenery.category_assets.forEach((asset) => {
                if (asset.asset_type === ASSET_TYPE_COLOR) {
                    assetType = ASSET_TYPE_COLOR;
                } else if (asset.asset_type === ASSET_TYPE_IMAGE) {
                    assetType = ASSET_TYPE_IMAGE;
                }
            });
        }
        return assetType;
    };

    useEffect(() => {
        if (props.event_data.event_uuid) {
            getData(props.event_data.event_uuid);
        }
        setHeaderLine1(props.event_data.header_line_1);
        setHeaderLine2(props.event_data.header_line_2);
        setCheck(props.event_data.is_self_header == 1);
    }, [props.event_data]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method updates selected scenery and updates state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleSceneryUpdate = (e) => {
        let currentScenery = getSceneryById(e.target.value);
        if (currentScenery) {
            setCurrentScenery(currentScenery);
        } else {
            setCurrentScenery(null);
        }
    };


    const handleTopBgColorChange = (data) => {
        if (!topBgColor) {
            // top color is not set, check if data is same as asset color or not
            if (assetColor && assetColor.value && JSON.stringify(data.value) === JSON.stringify(assetColor.value)) {
                return;
            }
        }
        setTopBgColor(data);
    };

    const handleAssetColorSelect = (data) => {
        setCurrentAssetColor(data);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for updating the component opacity
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleComponentOpacityChange = (e) => {
        setComponentOpacity(e.target.value);
    };

    useEffect(() => {
        if (currentScenery) {
            let currentAsset = currentScenery.category_assets.find(
                (asset) => asset.asset_id === currentSceneryAsset
            );
            let asset = currentAsset;
            if (!currentAsset) {
                asset = currentScenery && currentScenery.category_assets.length ? currentScenery.category_assets[0] : null
                // as current asset is not in current category so setting it null
                setCurrentSceneryAsset(asset ? asset.asset_id : null);
            }
            if (asset) {
                if (!topBgColor && asset.asset_default_color && currentScenery.category_type === ASSET_TYPE_IMAGE) {
                    setAssetColor({
                        field: 'asset',
                        value: asset.asset_default_color,
                    })
                }
                if (currentScenery.category_type === ASSET_TYPE_COLOR) {
                    setAssetColor({
                        field: 'asset',
                        value: {r: 255, g: 255, b: 255, a: 1}
                    })
                }
            }

        } else {
            setCurrentSceneryAsset(null);
        }
        setAvailableSceneryImages(
            currentScenery ? currentScenery.category_assets : []
        );
        // setting current asset type as image or color if its color and there is asset available
        setCurrentAssetType(
            currentScenery && currentScenery.category_type
                ? currentScenery.category_type
                : null
        );
    }, [currentScenery]);

    useEffect(() => {
        if (currentSceneryAsset) {
            let asset = getAssetById(currentSceneryAsset);

            setTopBgColor({
                field: 'asset',
                value: asset ? asset.asset_default_color : WHITE_COLOR,
            })
        } else {
            setTopBgColor(
                {
                    field: 'asset',
                    value: WHITE_COLOR
                }
            )
        }
    }, [currentSceneryAsset]);

    useEffect(() => {
        let multiSpacesData = [];
        allSpacesData.map((v) => {
            if (venueType === MULTI_SPACE) {
                multiSpacesData.push(v);
            } else if (venueType === MONO_SPACE && v.is_default === 1) {
                multiSpacesData.push(v);
            }
        });
        setMultiSpaceList(multiSpacesData);
    }, [venueType]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - this method updates headers lines valuse from response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} res API response
     * @param {String} res.meta.header_line_1 Event header line one
     * @param {String} res.meta.header_line_2 Event header line two
     * @param {String} res.meta.is_self_header To check if event should have self headers or default headers
     * @param {String} res.meta.is_mono_space To check if event have mono space or multispace
     */
    const setHeaderFromResponse = (res) => {
        let headerInfo = res.meta.header_info;
        setHeaderLine1(headerInfo.header_line_1);
        setHeaderLine2(headerInfo.header_line_2);
        setCheck(headerInfo.is_self_header);
        setVenueType(headerInfo.is_mono_space ? MONO_SPACE : MULTI_SPACE);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to fetch all spaces of an event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} id Event uuid
     */
    const getData = (id) => {
        setLoading(true);
        const formData = new FormData();
        if (venueType === MONO_SPACE) {
            formData.append("_method", "GET");
            formData.append("event_uuid", id);
            formData.append("key", "mono");
        } else {
            formData.append("_method", "GET");
            formData.append("event_uuid", id);
            formData.append("key", "normal");
        }
        try {
            props
                .getSpaces(formData)
                .then((res) => {
                    const data = res.data.data;
                    const metaData = res.data.meta;
                    let spaceType = MULTI_SPACE;
                    if (
                        _.has(metaData, ["event_state"]) &&
                        metaData.event_state !== undefined
                    ) {
                        setEvent_status(metaData.event_state);
                    }
                    if (
                        _.has(metaData, ["is_mono_event"]) &&
                        metaData.is_mono_event == 1
                    ) {
                        setVenueType(MONO_SPACE);
                        spaceType = MONO_SPACE;
                    }
                    if (_.has(metaData, ["all_scenery_data"])) {
                        setAvailableSceneries(metaData.all_scenery_data);
                        if (metaData.all_scenery_data.length) {
                            let currentScenery = null;
                            if (
                                metaData.current_scenery_data &&
                                metaData.current_scenery_data.asset_id
                            ) {
                                setCurrentSceneryAsset(metaData.current_scenery_data.asset_id);
                                metaData.all_scenery_data.forEach((scenery) => {
                                    scenery.category_assets.forEach((asset) => {
                                        if (
                                            asset.asset_id === metaData.current_scenery_data.asset_id
                                        ) {
                                            setCurrentScenery(scenery);
                                            currentScenery = scenery;
                                        }
                                    });
                                });
                            }
                            if (
                                _.has(metaData, ["current_scenery_data", "component_opacity"])
                            ) {
                                setComponentOpacity(
                                    metaData.current_scenery_data.component_opacity * 100
                                );
                            }
                            if (
                                _.has(metaData, ["current_scenery_data", "asset_color"]) &&
                                metaData.current_scenery_data.asset_color
                            ) {
                                setCurrentAssetColor({
                                    ...currentAssetColor,
                                    value: metaData.current_scenery_data.asset_color,
                                });
                            }

                            if (
                                _.has(metaData, [
                                    "current_scenery_data",
                                    "top_background_color",
                                ])
                            ) {
                                setTopBgColor({
                                    field: "top_background_color",
                                    value: metaData.current_scenery_data.top_background_color,
                                });
                            }

                            setHeaderFromResponse(res.data);
                        }
                    }

                    if (data) {
                        const monoSpaceData = [];
                        const multiSpacesData = [];
                        const spaces = data;
                        setAllSpacesData(spaces);
                        spaces.map((v) => {
                            if (spaceType === MULTI_SPACE) {
                                multiSpacesData.push(v);
                            } else if (spaceType === MONO_SPACE && v.is_default === 1) {
                                monoSpaceData.push(v);
                                multiSpacesData.push(v);
                            }
                        });
                        setMultiSpaceList(multiSpacesData);
                        getMonoSpace(monoSpaceData);
                        setLoading(false);
                    }
                })
                .catch((err) => {
                    setLoading(false);
                    if (
                        err &&
                        _.has(err.response) &&
                        _.has(err.response.data, ["errors"])
                    ) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(err.response, ["data", "msg"])) {
                        var er = err.response.data;
                        for (let key in er) {
                            alert.show(er[key], {type: "error"});
                        }
                        // alert.show(err.response.data.msg,{type:'error'});
                    } else {
                        alert.show(Helper.handleError(err), {type: "error"});
                    }
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            setLoading(false);
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for adding new space data and update existing space data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Space} spaceData Required Data to create a space
     */
    const addSpace = (spaceData) => {
        let spaces = [...allSpacesData];
        spaces.push(spaceData);
        setAllSpacesData(spaces);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to remove space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} spaceUuid Space uuid of a space
     */
    const removeSpace = (spaceUuid) => {
        let spaces = [...allSpacesData];
        setAllSpacesData(
            spaces.filter((space) => {
                return space.space_uuid !== spaceUuid;
            })
        );
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method handles the next step and checks validation form status
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Function} callback To allow user to move to the next step
     */
    const handleNext = (callback) => {
        if (formOpenStatus) {
            validateFormOpen(() => {
                callback();
            });
        } else {
            callback();
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method shows confirmation box and on confirm it calls callback function
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Function} callback It is a callback method used to save the data of space.
     */
    const validateFormOpen = (callback) => {
        if (formOpenStatus) {
            confirmAlert({
                message: `${t("confirm:proceedNext")}`,
                confirmLabel: t("confirm:confirm"),
                cancelLabel: t("confirm:cancel"),
                buttons: [
                    {
                        label: t("confirm:yes"),
                        onClick: () => {
                            callback();
                        },
                    },
                    {
                        label: t("confirm:no"),
                        onClick: () => {
                            return false;
                        },
                    },
                ],
            });
        }
    };


    const handleChange = (prop) => (event) => {
        setSpaceData({...spaceData, [prop]: event.target.value});
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This is used for handle check data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleCheck = () => {
        setCheck(!check);
    };
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for getting mono space data and updates ui
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Space} data All space related data
     */
    const getMonoSpace = (data) => {
        if (data) {
            data.map((v) => {
                setSpaceData({
                    max_capacity: v.max_capacity,
                    spaceHost: _.isEmpty(v.space_hosts) ? v.space_hosts : "",
                    spaceId: v.space_uuid,
                    header_line_1: v.header_line_1,
                    header_line_2: v.header_line_2,
                    is_mono_space: v.is_mono_space,
                    is_self_header: v.is_self_header ? v.is_self_header : 0,
                });
                setHostName(!_.isEmpty(v.space_hosts) ? v.space_hosts : "");
                setSpaceHostId(!_.isEmpty(v.space_hosts) ? v.space_hosts[0].id : "");
                setCheck(v.is_self_header === 1 ? true : false);

                initialize(v);
            });
        }
    };


    const changeName = () => {
        const host = spaceData.spaceHost;

        host.map((v) => {
            setHostName({
                fname: v.fname,
                lname: v.lname,
            });
        });
    };

    const onUpdate = () => {
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("max_capacity", parseInt(spaceData.max_capacity));
        formData.append("header_line_1", spaceData.header_line_1);
        formData.append("header_line_2", spaceData.header_line_2);
        formData.append("space_uuid", spaceData.spaceId);
        formData.append("hosts[]", spaceHostId);
        formData.append("is_self_header", check === true ? 1 : 0);
        formData.append("is_mono", venueType === MONO_SPACE);
        if (spaceHostId) {
            try {
                props
                    .updateSpaces(formData)
                    .then((res) => {
                        props.handleNext();
                    })
                    .catch((err) => {
                        alert.show(Helper.handleError(err), {type: "error"});
                    });
            } catch (err) {
                if (err && _.has(err.response.data, ["errors"])) {
                    var errors = err.response.data.errors;
                    for (let key in errors) {
                        alert.show(errors[key], {type: "error"});
                    }
                } else if (err && _.has(err.response.data, ["msg"])) {
                    var er = err.response.data;
                    for (let key in er) {
                        alert.show(er[key], {type: "error"});
                    }
                    // alert.show(err.response.data.msg,{type:'error'});
                } else {
                    alert.show(Helper.handleError(err), {type: "error"});
                }
            }
        } else {
            alert.show(t("req"), {type: "error"});
        }
    };
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will prepare the form data for space update and calls API to update the space data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onSave = () => {
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("max_capacity", parseInt(spaceData.max_capacity));
        formData.append("header_line_1", spaceData.header_line_1);
        formData.append("header_line_2", spaceData.header_line_2);
        formData.append("space_uuid", spaceData.spaceId);
        formData.append("hosts[]", spaceHostId);
        formData.append("is_self_header", check === true ? 1 : 0);
        if (spaceHostId) {
            try {
                props
                    .updateSpaces(formData)
                    .then((res) => {
                        alert.show(t("n" + "otification:rec add 1"), {type: "success"});
                    })
                    .catch((err) => {
                        alert.show(Helper.handleError(err), {type: "error"});
                    });
            } catch (err) {
                if (err && _.has(err.response.data, ["errors"])) {
                    var errors = err.response.data.errors;
                    for (let key in errors) {
                        alert.show(errors[key], {type: "error"});
                    }
                } else if (err && _.has(err.response.data, ["msg"])) {
                    var er = err.response.data;
                    for (let key in er) {
                        alert.show(er[key], {type: "error"});
                    }
                    // alert.show(err.response.data.msg,{type:'error'});
                } else {
                    alert.show(Helper.handleError(err), {type: "error"});
                }
            }
        } else {
            alert.show(t("req"), {type: "error"});
        }
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user change venue type radio buttons and set current value in a
     * state(setVenueType).
     * <br>
     * <br>
     * Types of venue:-
     * 1. Mono Space - If venue type is mono then it means event will have only one space(default space).
     * 2. Spaces - If venue type is not mono then it means event can have multiple spaces(normal/VIP space/default
     * space).
     * <br>
     * <br>
     * Default Space:- default space is that space which is created by default at the time of event creation.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleVenueTypeChange = (e) => {
        setVenueType(e.target.value)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will return asset background color object if exist otherwise it will return
     * white color object({r: 255, g: 255, b: 255, a: 1}).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @return {ColorRGBA} Color object
     */
    const getTopBgColor = () => {
        if (topBgColor && topBgColor.value) {
            return topBgColor.value;
        } else {
            return {
                r: 255, g: 255, b: 255, a: 1
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to save mapping the venue's current filled data and once the
     * call is successfully completed then it will show a successful message other wise show an error message.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Function} callback To save the current filled data of space
     */
    const saveEventData = (callback = () => {
    }) => {
        var params = props.match.params;
        // moment converts time in this format 09:05:00 (format("hh:mm:ss"))
        let eventData = {
            ...props.event_data,
            is_self_header: check ? 1 : 0,
            header_line_1: headerLine1,
            header_line_2: headerLine2,
            is_mono_event: venueType == MONO_SPACE ? 1 : 0,
            _method: "PUT"
        };
        try {
            let dataToSend = {
                event_uuid: props.event_data.event_uuid,
                asset_id: currentSceneryAsset,
                top_background_color: JSON.stringify(getTopBgColor()),
                component_opacity: componentOpacity * 0.01,
                is_self_header: check ? 1 : 0,
                header_line_1: headerLine1,
                header_line_2: headerLine2,
                is_mono_event: venueType == MONO_SPACE ? 1 : 0,
            }

            if (currentScenery && currentScenery.category_type === ASSET_TYPE_COLOR && currentAssetColor) {
                dataToSend['asset_color'] = JSON.stringify(currentAssetColor.value);
            }

            dispatch(eventAction.updateSceneryData(dataToSend))
                .then((res) => {
                    alert.show("Record Updated SuccessFully", {type: "success"});
                    callback();
                })
                .catch((err) => {
                    if (err && _.has(err.response.data, ["errors"])) {
                        var errors = err.response.data.errors;
                        for (let key in errors) {
                            alert.show(errors[key], {type: "error"});
                        }
                    } else if (err && _.has(err.response.data, ["msg"])) {
                        var er = err.response.data.errors;
                        for (let key in er) {
                            alert.show(er[key], {type: "error"});
                        }
                        // alert.show(err.response.data.msg,{type:'error'});
                    } else {
                        alert.show(Helper.handleError(err), {type: "error"});
                    }
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    const handleShowSpace = (data) => {
        if (data) {
            setSpaceLines({
                spaceLine1: data.spaceLine1,
                spaceLine2: data.spaceLine2,
            });
        }
        initialize(data);
        setShowSpaceLine(!showSpaceLine);
    };

    const handleSpaceLine = (prop) => (event) => {
        setSpaceLines({...spaceLines, [prop]: event.target.value});
    };

    const {event_data} = props;
    let active_event = false;

    if (_.has(event_data, ["time_state", "is_live"])) {
        active_event = event_data.time_state.is_live;
    }

    const disabled = active_event || accessMode;
    const cooler = {
        field: "sh_background",
        value: {
            r: "59",
            g: "59",
            b: "59",
            a: "1",
        },
    };
    return (
        <LoadingContainer loading={loading}>
            <div className="MappingMainDiv">
                {/*<form onSubmit={handleSubmit(saveEventData)}>*/}
                <Grid container spacing={4}>
                    <Grid item lg={3}>
                        <p>
                            Modify Header Info:{" "}
                            <Tooltip arrow title={t("modify header")}>
                                <InfoOutlinedIcon />
                            </Tooltip>
                        </p>
                    </Grid>
                    <Grid item lg={4}>
                        <Switch
                            checked={check}
                            onChange={handleCheck}
                            color="primary"
                            name="checkButton"
                            inputProps={{"aria-label": "primary checkbox"}}
                        />
                    </Grid>
                </Grid>
                {check === true && (
                    <div>
                        <Grid container spacing={4}>
                            <Grid item lg={3}>
                                <p>{t("headLine1")}</p>
                            </Grid>
                            <Grid item lg={4}>
                                <Field
                                    name="header_line_1"
                                    // id="header_line_1"
                                    // disabled={disabled}
                                    placeholder={t("placeHead1")}
                                    variant="outlined"
                                    className="ThemeInputTag"
                                    inputProps={{
                                        value: headerLine1,
                                    }}
                                    value={headerLine1}
                                    component={renderTextField}
                                    onChange={(e) => {
                                        setHeaderLine1(e.target.value);
                                    }}
                                />
                            </Grid>
                        </Grid>

                        <Grid container spacing={4}>
                            <Grid item lg={3}>
                                <p>{t("headLine2")}</p>
                            </Grid>
                            <Grid item lg={4}>
                                <Field
                                    name="header_line_2"
                                    // id="header_line_1"
                                    // disabled={disabled}
                                    placeholder={t("placeHead2")}
                                    variant="outlined"
                                    className="ThemeInputTag"
                                    value={headerLine2}
                                    inputProps={{
                                        value: headerLine2,
                                    }}
                                    component={renderTextField}
                                    onChange={(e) => {
                                        setHeaderLine2(e.target.value);
                                    }}
                                />
                            </Grid>
                        </Grid>
                    </div>
                )}

                <Grid container spacing={4}>
                    <Grid item lg={3}>
                        <p>
                            Scenery:{" "}
                            <Tooltip arrow title={t("Scenery")}>
                                <InfoOutlinedIcon />
                            </Tooltip>
                        </p>
                    </Grid>
                    <Grid item lg={9} className="scenery_wrap_div">
                        <div className="scenery_child_wrap">
                            <Select
                                value={currentScenery ? currentScenery.category_id : 0}
                                onChange={handleSceneryUpdate}
                                inputProps={{"aria-label": "Without label"}}
                                size="small"
                                className="scenery_main_selector"
                            >
                                <MenuItem value={0}>None</MenuItem>
                                {availableSceneries.map((scenery) => {
                                    if (scenery.category_assets.length) {
                                        return (
                                            <MenuItem value={scenery.category_id}>
                                                {scenery.category_name}
                                            </MenuItem>
                                        );
                                    }
                                })}
                            </Select>
                            {currentAssetType === ASSET_TYPE_IMAGE && (
                                <ImgSlider
                                    images={availableSceneryImages}
                                    sceneryImg={currentSceneryAsset}
                                    setSceneryImg={setCurrentSceneryAsset}
                                />
                            )}

                        </div>
                    </Grid>
                </Grid>
                {currentScenery && (
                    <>
                        <Grid container spacing={4}>
                            <Grid item lg={3}>
                                <p>
                                    Top Background Color:{" "}
                                    <Tooltip arrow title={t("Top background color")}>
                                        <InfoOutlinedIcon />
                                    </Tooltip>
                                </p>
                            </Grid>
                            <Grid item lg={3} className="scenery_wrap_div_bgColor">
                                <div className="scenery_child_wrap">
                                    {(topBgColor) && <ColorPicker
                                        callBack={handleTopBgColorChange}
                                        color={topBgColor}
                                        transparencyLabel={"Transparency"}
                                        // color={pass color values in form of rgba object same as design setting}
                                    />}
                                </div>
                            </Grid>
                        </Grid>
                        <Grid container spacing={4}>
                            <Grid item lg={3}>
                                <p>
                                    Components Opacity:
                                    <Tooltip arrow title={t("Component Opacity")}>
                                        <InfoOutlinedIcon />
                                    </Tooltip>
                                </p>
                            </Grid>
                            <Grid item lg={3} className="scenery_wrap_div_opacity">
                                <div className="scenery_child_wrap">
                  <span className="SliderSpan">
                    <Slider
                        className="MainSliderPadding"
                        defaultValue={90}
                        // value={92}
                        aria-labelledby="discrete-slider-small-steps"
                        step={1}
                        min={50}
                        size="small"
                        max={100}
                        value={componentOpacity}
                        onChange={handleComponentOpacityChange}
                        //  onChange={(e,v)=>{handeSliderChange(v)}}
                        valueLabelDisplay="auto"
                    />
                    <div className="ColorPickerLabelDiv">
                      <div className="PickerLabel-1">50</div>
                      <div className="PickerLabel-2">Opacity</div>
                      <div className="PickerLabel-3">100</div>
                    </div>
                  </span>
                                </div>
                            </Grid>
                        </Grid>
                    </>
                )}

                <Grid container spacing={4}>
                    <Grid item lg={3}>
                        <p className="VeneuTypeTxt">
                            {" "}
                            Venue Type:
                            <Tooltip arrow title={t("mapping:venue type")}>
                                <InfoOutlinedIcon />
                            </Tooltip>
                        </p>
                    </Grid>
                    <Grid item>
                        <FormControl component="fieldset">
                            <RadioGroup
                                value={venueType}
                                onChange={handleVenueTypeChange}
                                className="VenueTypeRadioBtn"
                            >
                                <FormControlLabel
                                    value={MONO_SPACE}
                                    control={<Radio />}
                                    label={t("MonoSpace")}
                                />
                                {/* checked={spaceData.is_mono_space === 1} */}
                                <FormControlLabel
                                    value={MULTI_SPACE}
                                    control={<Radio />}
                                    label={t("Spaces")}
                                />
                                <FormControlLabel
                                    value={BOOTH_SPACE}
                                    disabled
                                    control={<Radio />}
                                    label={t("Booth")}
                                />
                            </RadioGroup>
                        </FormControl>
                    </Grid>
                </Grid>
                {venueType === MONO_SPACE && (
                    <div>
                        <div className="relativeSpaceDiv">
                            <MultiSpaces
                                eventStatus={event_status}
                                getData={getData}
                                eventId={props.event_data.event_uuid}
                                multiSpaces={allSpacesData}
                                allowAdd={false}
                                venueType={venueType}
                                addSpace={addSpace}
                                removeSpace={removeSpace}
                                setFormOpenStatus={setFormOpenStatus}
                            />
                        </div>
                    </div>
                )}
                {venueType === MULTI_SPACE && (
                    <div>
                        <div className="relativeSpaceDiv">
                            <MultiSpaces
                                eventStatus={event_status}
                                getData={getData}
                                eventId={props.event_data.event_uuid}
                                multiSpaces={allSpacesData}
                                allowAdd={true}
                                venueType={venueType}
                                addSpace={addSpace}
                                removeSpace={removeSpace}
                                setFormOpenStatus={setFormOpenStatus}
                            />
                        </div>
                        <Button
                            variant="contained"
                            color="primary"
                            type="submit"
                            onClick={() => {
                                saveEventData();
                            }}
                        >
                            {t("save")}
                        </Button>
                        {"  "}
                        <Button
                            variant="contained"
                            color="primary"
                            onClick={() => {
                                handleNext(() => saveEventData(props.handleNext()));
                            }}
                        >
                            {t("Save and Next")}
                        </Button>
                    </div>
                )}
                {venueType === MONO_SPACE && (
                    <div className="BottomActionButtonDiv">
                        <Button
                            variant="contained"
                            color="primary"
                            type="submit"
                            onClick={() => {
                                saveEventData();
                            }}
                        >
                            {t("save")}
                        </Button>
                        {"  "}
                        <Button
                            variant="contained"
                            color="primary"
                            type="submit"
                            onClick={() => {
                                handleNext(() => saveEventData(props.handleNext()));
                            }}
                        >
                            {t("Save and Next")}
                        </Button>
                    </div>
                )}
                {/*</form>*/}
            </div>
        </LoadingContainer>
    );
};

const mapDispatchToProps = (dispatch) => {
    return {
        getSpaces: (data) => dispatch(eventAction.getSpaces(data)),
        updateSpaces: (data) => dispatch(eventAction.updateSpaces(data)),
        spaceHostSearch: (data) => dispatch(eventAction.spaceHostSearch(data)),
    };
};

const mapStateToProps = (state) => {
    return {
        event_data: state.Auth.eventDetailsData,
    };
};


MappingVenue = reduxForm({
    form: "spaceform", // a unique identifier for this form
    validate,
    keepDirtyOnReinitialize: true,
})(MappingVenue);

export default connect(mapStateToProps, mapDispatchToProps)(MappingVenue);
