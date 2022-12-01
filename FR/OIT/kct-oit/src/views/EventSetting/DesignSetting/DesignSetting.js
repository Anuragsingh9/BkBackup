import React, {useEffect, useState} from "react";
import SwitchAccordion from "./Common/ToggleComponent/SwitchAccordion";
import QuickSetting from "./QuickSetting/QuickSettings";
import {useSelector, useDispatch} from "react-redux";
import groupAction from "../../../redux/action/apiAction/group";
import groupReduxAction from "../../../redux/action/reduxAction/group";
import HeaderSettings from "./HeaderSettings/HeaderSettings";
import TextureSettings from "./TextureSettings/TextureSettings";
import SpaceSetting from "./SpaceSettings.js/SpaceSettings";
import TagsSettings from "./TagsSettings/TagsSettings";
import {useAlert} from "react-alert";
import Helper from "../../../Helper.js";
import QuickDesignIcon from "../../Svg/QuickDesignIcon.js";
import HeadersIcon from "../../Svg/HeadersIcon.js";
import ConversationSettingIcon from "../../Svg/ConversationSettingIcon.js";
import SpaceHostSettingIcon from "../../Svg/SpaceHostSettingIcon.js";
import SpaceSettingIcon from "../../Svg/SpaceSettingIcon.js";
import BadgeSettingIcon from "../../Svg/BadgeSettingIcon.js";
import ContentSettingIcon from "../../Svg/ContentSettingIcon.js";
import GridSettingIcon from "../../Svg/GridSettingIcon.js";
import SettingsOutlinedIcon from "@mui/icons-material/SettingsOutlined";
import TextureSettingIcon from "../../Svg/TextureSettingIcon.js";
import TagSettingIcon from "../../Svg/TagSettingIcon.js";
import "./DesignSetting.css";
import ButtonSettings from "./ButtonSettings/ButtonSettings";
import SpaceHost from "./SpaceHost/SpaceHost";
import ContentSetting from "./ContentSetting/ContentSetting";
import UserBadgeSettings from "./UserBadge/UserBadge";
import ButtonSettingIcon from "../../Svg/ButtonSettingIcon.js";
import ConversationSettings from "./ConversationSettings/ConvetsationSettings";
import UserGridSettings from "./UserGrid/UserGrid";
import LoadingContainer from "../../Common/Loading/Loading";
import LabelSettingIcon from "../../Svg/LabelSettingIcon";
import LabelSettings from "./LabelSettings/LabelSettings.js";
import _ from "lodash";
import {useTranslation} from "react-i18next";
import Tooltip from "@material-ui/core/Tooltip";
import GeneralSettings from "./GeneralSettings/GeneralSettings";

import {useParams} from "react-router-dom";
import GeneralSettingsIcon from "../../Svg/GeneralSettingsIcon";
import userReduxAction from "../../../redux/action/reduxAction/user.js";
import DesignSetting from "../../../Models/DesignSetting";
import LoadingSkeleton from "../../Common/Loading/LoadingSkeleton";
import DesignSettingSkeleton from "../../v4/Skeleton/DesignSettingSkeleton";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file/page allows the pilot of an account to modify and customise the skin of the Attendee side
 * interface (H-Events). Entities like colour, texture, default account images (like, logo, default images before event
 * starts) can be updated.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {JSX.Element}
 * @constructor
 */
const DesignSettings = () => {
    const {t} = useTranslation("designSetting");
    // alert hook
    const alert = useAlert();
    // graphic settings state
    const [graphicSetting, setGraphic] = useState([]);
    // default group setting
    const [followOrganisation, setFollowOrganisation] = useState();
    // temp state for update api data
    const [newState, updateState] = useState([]);
    // loading state
    const [loading, setLoading] = useState(true);
    // user data to get group id
    const user_badge = useSelector((data) => data.Auth.userSelfData)
    // dispatch hook from redux
    const graphic_data = useSelector((data) => data.Auth)
    const eventRolelabelData = useSelector(data => data.Auth.eventRoleLabels); //.label_customized
    const dispatch = useDispatch();
    // useEffect hook to trigger get api
    const [labels, setLabels] = useState()
    const [showContentCropPreview, setShowContentCropPreview] = useState(false)
    // allow group setting button state
    const [AllowDesignSetting, setAllowDesignSetting] = useState(0);
    // group design setting button state
    const [ownCustomization, setOwnCustomization] = useState(0);
    //content image default state to manage cross button
    const [contentImgDefaultData, setContentImgDefaultData] = useState(false)
    const [userGridImgDefaultData, setUserGridImgDefaultData] = useState(false);
    const [eventLogoDefaultData, setEventLogoDefaultData] = useState(false);

    const [groupId, setGroupId] = useState();
    const {gKey} = useParams();

    const [showGridImageField, setShowGridImageField] = useState(false);
    const [headerLine1Err, setHeaderLine1Err] = useState(false);
    const [headerLine2Err, setHeaderLine2Err] = useState(false);
    //state to mange instant refletion of group setting
    const [showGrupSettings, setShowGrupSettings] = useState(0)

    useEffect(() => {
        if (_.has(user_badge, ["current_group", "id"])) {
            setGroupId(user_badge.current_group.id);
            getSettings(gKey);
            // getLabels(gKey);
        } else {
            getSettings(gKey);
            // getLabels(gKey);
        }
    }, [showGrupSettings]);

    useEffect(() => {
        getLabels(gKey);
    }, []);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function handles api call and response handling for getting labels data for label Setting.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} groupKey  Group Key
     */
    const getLabels = (groupKey) => {
        try {
            dispatch(groupAction.getLabels(groupKey))
                .then((res) => {
                    setLabels(res.data.data);
                })
                .catch((err) => {
                    console.error(err);
                });
        } catch (err) {
            console.error(err);
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to update the labels if label customization is ON.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} data Labels object
     * @param {LabelObj[]} data.locales Array of labels value
     * @param {String} data.name Label name
     */
    const updateLabels = (data) => {
        const updateData = {
            group_key: gKey,
            labels: [data],
            method: "POST",
        };
        try {
            dispatch(groupAction.updateLabels(updateData))
                .then((res) => {
                    let data = res.data.data;
                    const changeData = [...labels];
                    changeData.map((v) => {
                        if (v.name == data.name) {
                            v = data;
                        }
                    });

                    setLabels(changeData);
                    // dispatch(userReduxAction.setLabelData({labels:data} ))
                    alert.show("Record added successfuly  ", {type: "success"});
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    useEffect(() => {
    }, [labels]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for filter the array object and provide the required data
     * Like event image, video explainer alternative image, group logo
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Array} arr Array of setting images
     */
    const filterArrayObj = (arr) => {
        const filteredArray = arr.filter(element => {
            if (element.field == "event_image") {
                // console.log('element.fieldd', element)

                if (_.has(element, ["is_default"])) {
                    setContentImgDefaultData(true);
                } else {
                    setContentImgDefaultData(false);
                }
            }
            if (element.field == "video_explainer_alternative_image") {
                if (_.has(element, ["is_default"])) {
                    setUserGridImgDefaultData(true);
                } else {
                    setUserGridImgDefaultData(false);
                }
            }
            if (element.field == "group_logo") {
                if (_.has(element, ["is_default"])) {
                    setEventLogoDefaultData(true);
                } else {
                    setEventLogoDefaultData(false);
                }
            }
            // return element.field == "event_image" || element.field == "video_explainer_alternative_image" || element.field == "group_logo"
        })
        // console.log('filteredArray', filteredArray)
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method will take current group id from parameter and make an API call to get all design setting
     * data for this group account. This function will trigger when page load first time.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} groupKey  Unique group's key
     */
    const getSettings = (groupKey) => {
        try {
            dispatch(groupAction.getGroupSettings(groupKey))
                .then((res) => {
                    setGraphic(res.data.data);
                    filterArrayObj(res.data.data);
                    // const setSettings = (settings) => {
                    //     setGraphic(settings);
                    //     dispatch(groupReduxAction.setGraphicSetting(settings));
                    // }

                    // console.log("res.data.data", res.data.meta.allow_design_setting);
                    setAllowDesignSetting(res.data.meta.allow_design_setting);
                    // setAllowDesignSetting(0)
                    setOwnCustomization(res.data.data.group_has_own_customization);
                    dispatch(groupReduxAction.setGraphicSetting(res.data.data));
                    setTimeout(() => {
                        setLoading(false);
                    }, 400);
                })
                .catch((err) => {
                    setLoading(false);
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            setLoading(false);
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function handles data providing for components of a specific field's(button, header, texture,etc)
     * customization settings.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} key  Data key is use for design setting.
     * @returns {Object} Settings object with key value pair.
     */
    const getKeyData = (key) => {
        const data =
            !_.isEmpty(graphicSetting) &&
            graphicSetting.filter((item) => {
                if (item.field == key) {
                    return item;
                }
            });
        return !_.isEmpty(data) ? data[0] : {};
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function handles API call and response handling of updated graphics data for the account.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {DesignSetting} updatedData  To update design settings
     */
    const updateDesignSetting = (updatedData, setShowLoader) => {
        setShowContentCropPreview(false)
        if (setShowLoader) {
            setShowLoader(true);
        }
        const deleteNotification = updatedData?.value === null
            ? "Item deleted successfully."
            :
            updatedData?.value === undefined
                ? "Record Updated Successfully"
                :
                "Record Updated Successfully";

        if (_.isEmpty(newState) && !_.has(updatedData, ["field"])) {
            return alert.show("Nothing to update here");
        }

        const updateData = new FormData();
        if (_.has(updatedData, ["field"])) {
            updateData.append(`settings[${0}][field]`, updatedData.field);
            if (updatedData.value != null) {
                updateData.append(`settings[${0}][value]`, updatedData.value);
            }
        } else {
            newState.map((item, key) => {
                updateData.append(`settings[${key}][field]`, item.field);

                if (item.field && _.has(item.value, ["r"])) {
                    updateData.append(
                        `settings[${key}][value]`,
                        JSON.stringify(item.value)
                    );
                } else {
                    updateData.append(`settings[${key}][value]`, item.value);
                }
            });
        }

        updateData.append("_method", "PUT");
        updateData.append("group_key", gKey);

        try {
            dispatch(groupAction.updateGroupSettings(updateData))
                .then((res) => {
                    updateState([]);
                    setUpdatedData(res.data.data);
                    console.log('res.data.data', res.data.data)

                    if (_.has(res.data.data, ["event_image"])) {
                        if (_.has(res.data.data, ["is_default"])) {
                            setContentImgDefaultData(true);
                        } else {
                            setContentImgDefaultData(false);
                        }
                    }
                    if (_.has(res.data.data, ["video_explainer_alternative_image"])) {
                        if (_.has(res.data.data, ["is_default"])) {
                            setUserGridImgDefaultData(true);
                        } else {
                            setUserGridImgDefaultData(false);
                        }
                    }
                    if (_.has(res.data.data, ["group_logo"])) {
                        if (_.has(res.data.data, ["is_default"])) {
                            setEventLogoDefaultData(true);
                        } else {
                            setEventLogoDefaultData(false);
                        }
                    }
                    // console.log('res.data.dataimgDefaultDataaaa--in', res.data.data)
                    // // if(key == group_has_customization){
                    //     setSettings(res.data.meta.design_settings);
                    // // }
                    alert.show(`${deleteNotification}`, {type: "success"});
                    const data = res.data.data;
                    //to show instant reflection of  default group's all settings
                    if (_.has(data, ["group_has_own_customization"])) {
                        setShowGrupSettings(data.group_has_own_customization)
                    }
                    // to show instant reflection of default labels
                    if (_.has(data, ["label_customized"])) {
                        const newData = {
                            ...eventRolelabelData,
                            label_customized: data.label_customized,
                        };
                        dispatch(userReduxAction.setLabelData(newData));
                    }
                    if (setShowLoader) {
                        setShowLoader(false);
                    }
                })
                .catch((err) => {
                    alert.show(Helper.handleError(err), {type: "error"});
                });
        } catch (err) {
            alert.show(Helper.handleError(err), {type: "error"});
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function handles response of update API(updateGroupSettings), and provides newly set data to
     * components.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {UserDataObject} data  Current logged in user's data
     */
    const setUpdatedData = (data) => {
        const newData = graphicSetting.map((val) => {
            let newObj = {...val};

            Object.keys(data).map((item) => {
                if (val.field == item) {
                    newObj.value = data[item];
                }
            });
            return newObj;
        });
        setGraphic(newData);

        dispatch(groupReduxAction.setGraphicSetting(newData));
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function sets temp state with new data changed by the user in sub/child components and avoids
     * duplicate.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {DesignSetting} data To update the design settings
     */
    const callBack = (data) => {
        const state = [...newState];
        if (_.isEmpty(state)) {
            updateState([data]);
        } else {
            let newStateData = [];
            let flag = false;

            state.map((item, index) => {
                if (item.field == data.field) {
                    flag = true;
                    newStateData.push(data);
                } else {
                    newStateData.push(item);
                }
            });

            if (data.field === "header_line_1" && data.value.length > 44) {
                setHeaderLine1Err(true);
            } else if (data.field === "header_line_1" && data.value.length <= 44) {
                setHeaderLine1Err(false);
            }
            if (data.field === "header_line_2" && data.value.length > 56) {
                setHeaderLine2Err(true);
            } else if (data.field === "header_line_2" && data.value.length <= 56) {
                setHeaderLine2Err(false);
            }

            if (!flag) {
                newStateData.push(data);
            }

            updateState(newStateData);
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when we reset the sub/child's settings in the form of main color 1 and main
     * color 2. This will call 'updateState' function and pass reset values of that specific sub setting into it to update
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {DesignSetting} data Value of update design setting
     */
    const resetColorCallback = (data) => {
        updateState(data);
    };

    const labelsImage = () => {
        if (!_.isEmpty(graphicSetting)) {
            graphicSetting.map((v) => {
            });
        }
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function sets color1 and 2 values just after logo upload and pass it to the function(updateState)
     * to update the main colors.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {ColorRGBA[]} newColors Array of the color object
     */
    const colorCallback = (newColors) => {
        updateState(newColors);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user clicks on 'cancel' button just below every sub/child setting's
     * section.This will remove unsaved setting and get last saved data for complete design setting.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const callBackCancel = () => {
        updateState([]);
        setLoading(true);
        setTimeout(() => {
            setLoading(false);
        }, 1000);
        setShowContentCropPreview(false);
    };
    const imageDataKey = {
        b_icon: "business_team_icon",
        b_alt: "business_team_altImage",
        v_icon: "vip_icon",
        v_alt: "vip_altImage",
        m_icon: "moderator_icon",
    };
    const tooltip_labels = {
        space_setting: t("space_bg_color"),
        event_image: t("event_image"),
        user_grid_bg_color: t("user_grid_bg_color"),
        show_invitation_tooltip:
            "During Event Registration, Allow Participant to invite other members to the Event.",
        invite_users:
            "Allow/Restrict Participants to send invites from Event Registration Form",
        user_grid_image:
            "For best viewer experience use 1158x630 image size(in px).",
        show_video_tooltip:
            "Show Product Welcome Video during Registration and Event Dashboard",
        default_image_tooltip:
            "This is the default image displayed on Dashboard page under Spaces",
        sharp_corners_tooltip: t("sharp_corners"),
    };


    const header_Data = {
        //header current colors
        header_bg_color_1: getKeyData("header_bg_color_1"),
        header_text_color: getKeyData("header_text_color"),
        header_separation_line_color: getKeyData("header_separation_line_color"),

        //button current colors
        customized_join_button_bg: getKeyData("customized_join_button_bg"),
        customized_join_button_text: getKeyData("customized_join_button_text"),

        //Space host current color
        sh_background: getKeyData("sh_background"),
        //content current color
        content_background: getKeyData("content_background"),
        //conversation bg current color
        conv_background: getKeyData("conv_background"),
        //user badge current color
        badge_background: getKeyData("badge_background"),
        //Space bg current color
        space_background: getKeyData("space_background"),
        //user grid current color
        user_grid_pagination_color: getKeyData("user_grid_pagination_color"),
        user_grid_background: getKeyData("user_grid_background"),
        //tags current colors
        event_tag_color: getKeyData("event_tag_color"),
        professional_tag_color: getKeyData("professional_tag_color"),
        personal_tag_color: getKeyData("personal_tag_color"),
        tags_text_color: getKeyData("tags_text_color"),
    };

    const mainColor = {
        color1: getKeyData("main_color_1"),
        color2: getKeyData("main_color_2"),
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will reset all the values of a specific sub/child setting's section data in the form of
     * primary color 1 and primary color 2.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} resetStatus 0 or 1 -> 0 for previous color, 1 for current color value
     * @param {String} colorDataKey Key of the different customization section(header footer, button, space host, content,
     * conversation, badge, space, user grid, tags)
     */
    const resetColorHandler = (resetStatus, colorDataKey) => {
        //array of default color values in terms of color1 & color2
        const resetValues = [
            {
                header_footer_customized: [
                    {
                        allColorVal: [
                            {
                                field: "header_bg_color_1",
                                value: JSON.stringify({r: 255, g: 255, b: 255, a: 1}),
                            },
                            {
                                field: "header_text_color",
                                value: JSON.stringify({
                                    r: mainColor.color2.value.r,
                                    g: mainColor.color2.value.g,
                                    b: mainColor.color2.value.b,
                                    a: 1,
                                }),
                            },
                            {
                                field: "header_separation_line_color",
                                value: JSON.stringify({r: 231, g: 231, b: 231, a: 1}),
                            },
                        ],
                    },
                    {
                        prevColor: [
                            {
                                field: "header_bg_color_1",
                                value: JSON.stringify({
                                    r: header_Data.header_bg_color_1.value.r,
                                    g: header_Data.header_bg_color_1.value.g,
                                    b: header_Data.header_bg_color_1.value.b,
                                    a: header_Data.header_bg_color_1.value.a,
                                }),
                            },
                            {
                                field: "header_text_color",
                                value: JSON.stringify({
                                    r: header_Data.header_text_color.value.r,
                                    g: header_Data.header_text_color.value.g,
                                    b: header_Data.header_text_color.value.b,
                                    a: header_Data.header_bg_color_1.value.a,
                                }),
                            },
                            {
                                field: "header_separation_line_color",
                                value: JSON.stringify({
                                    r: header_Data.header_separation_line_color.value.r,
                                    g: header_Data.header_separation_line_color.value.g,
                                    b: header_Data.header_separation_line_color.value.b,
                                    a: header_Data.header_separation_line_color.value.a,
                                }),
                            },
                        ],
                    },
                ],
            },
            {
                button_customized: [
                    {
                        allColorVal: [
                            {
                                field: "customized_join_button_bg",
                                value: JSON.stringify({
                                    r: mainColor.color1.value.r,
                                    g: mainColor.color1.value.g,
                                    b: mainColor.color1.value.b,
                                    a: 1,
                                }),
                            },
                            {
                                field: "customized_join_button_text",
                                value: JSON.stringify({r: 255, g: 255, b: 255, a: 1}),
                            },
                        ],
                    },
                    {
                        prevColor: [
                            {
                                field: "customized_join_button_bg",
                                value: JSON.stringify({
                                    r: header_Data.customized_join_button_bg.value.r,
                                    g: header_Data.customized_join_button_bg.value.g,
                                    b: header_Data.customized_join_button_bg.value.b,
                                    a: header_Data.customized_join_button_bg.value.a,
                                }),
                            },
                            {
                                field: "customized_join_button_text",
                                value: JSON.stringify({
                                    r: header_Data.customized_join_button_text.value.r,
                                    g: header_Data.customized_join_button_text.value.g,
                                    b: header_Data.customized_join_button_text.value.b,
                                    a: header_Data.customized_join_button_text.value.a,
                                }),
                            },
                        ],
                    },
                ],
            },
            {
                sh_customized: [
                    {
                        allColorVal: [
                            {
                                field: "sh_background",
                                value: JSON.stringify({r: 59, g: 59, b: 59, a: 1}),
                            },
                        ],
                    },
                    {
                        prevColor: [
                            {
                                field: "sh_background",
                                value: JSON.stringify({
                                    r: header_Data.sh_background.value.r,
                                    g: header_Data.sh_background.value.g,
                                    b: header_Data.sh_background.value.b,
                                    a: header_Data.sh_background.value.a,
                                }),
                            },
                        ],
                    },
                ],
            },
            {
                content_customized: [
                    {
                        allColorVal: [
                            {
                                field: "content_background",
                                value: JSON.stringify({
                                    r: mainColor.color1.value.r,
                                    g: mainColor.color1.value.g,
                                    b: mainColor.color1.value.b,
                                    a: 1,
                                }),
                            },
                        ],
                    },
                    {
                        prevColor: [
                            {
                                field: "content_background",
                                value: JSON.stringify({
                                    r: header_Data.content_background.value.r,
                                    g: header_Data.content_background.value.g,
                                    b: header_Data.content_background.value.b,
                                    a: header_Data.content_background.value.a,
                                }),
                            },
                        ],
                    },
                ],
            },
            {
                conv_customization: [
                    {
                        allColorVal: [
                            {
                                field: "conv_background",
                                value: JSON.stringify({
                                    r: mainColor.color2.value.r,
                                    g: mainColor.color2.value.g,
                                    b: mainColor.color2.value.b,
                                    a: 1,
                                }),
                            },
                        ],
                    },
                    {
                        prevColor: [
                            {
                                field: "conv_background",
                                value: JSON.stringify({
                                    r: header_Data.conv_background.value.r,
                                    g: header_Data.conv_background.value.g,
                                    b: header_Data.conv_background.value.b,
                                    a: header_Data.conv_background.value.a,
                                }),
                            },
                        ],
                    },
                ],
            },
            {
                badge_customization: [
                    {
                        allColorVal: [
                            {
                                field: "badge_background",
                                value: JSON.stringify({r: 255, g: 255, b: 255, a: 1}),
                            },
                        ],
                    },
                    {
                        prevColor: [
                            {
                                field: "badge_background",
                                value: JSON.stringify({
                                    r: header_Data.badge_background.value.r,
                                    g: header_Data.badge_background.value.g,
                                    b: header_Data.badge_background.value.b,
                                    a: header_Data.badge_background.value.a,
                                }),
                            },
                        ],
                    },
                ],
            },
            {
                space_customization: [
                    {
                        allColorVal: [
                            {
                                field: "space_background",
                                value: JSON.stringify({
                                    r: mainColor.color2.value.r,
                                    g: mainColor.color2.value.g,
                                    b: mainColor.color2.value.b,
                                    a: 0.75,
                                }),
                            },
                        ],
                    },
                    {
                        prevColor: [
                            {
                                field: "space_background",
                                value: JSON.stringify({
                                    r: header_Data.space_background.value.r,
                                    g: header_Data.space_background.value.g,
                                    b: header_Data.space_background.value.b,
                                    a: header_Data.space_background.value.a,
                                }),
                            },
                        ],
                    },
                ],
            },
            {
                user_grid_customization: [
                    {
                        allColorVal: [
                            {
                                field: "user_grid_pagination_color",
                                value: JSON.stringify({
                                    r: mainColor.color2.value.r,
                                    g: mainColor.color2.value.g,
                                    b: mainColor.color2.value.b,
                                    a: 1,
                                }),
                            },
                            {
                                field: "user_grid_background",
                                value: JSON.stringify({
                                    r: mainColor.color2.value.r,
                                    g: mainColor.color2.value.g,
                                    b: mainColor.color2.value.b,
                                    a: 1,
                                }),
                            },
                        ],
                    },
                    {
                        prevColor: [
                            {
                                field: "user_grid_pagination_color",
                                value: JSON.stringify({
                                    r: header_Data.user_grid_pagination_color.value.r,
                                    g: header_Data.user_grid_pagination_color.value.g,
                                    b: header_Data.user_grid_pagination_color.value.b,
                                    a: header_Data.user_grid_pagination_color.value.a,
                                }),
                            },
                            {
                                field: "user_grid_background",
                                value: JSON.stringify({
                                    r: header_Data.user_grid_background.value.r,
                                    g: header_Data.user_grid_background.value.g,
                                    b: header_Data.user_grid_background.value.b,
                                    a: header_Data.user_grid_background.value.a,
                                }),
                            },
                        ],
                    },
                ],
            },
            {
                tags_customization: [
                    {
                        allColorVal: [
                            {
                                field: "event_tag_color",
                                value: JSON.stringify({
                                    r: mainColor.color2.value.r,
                                    g: mainColor.color2.value.g,
                                    b: mainColor.color2.value.b,
                                    a: 1,
                                }),
                            },
                            {
                                field: "professional_tag_color",
                                value: JSON.stringify({
                                    r: mainColor.color2.value.r,
                                    g: mainColor.color2.value.g,
                                    b: mainColor.color2.value.b,
                                    a: 0.75,
                                }),
                            },
                            {
                                field: "personal_tag_color",
                                value: JSON.stringify({
                                    r: mainColor.color2.value.r,
                                    g: mainColor.color2.value.g,
                                    b: mainColor.color2.value.b,
                                    a: 0.75,
                                }),
                            },
                            {
                                field: "tags_text_color",
                                value: JSON.stringify({r: 255, g: 255, b: 255, a: 1}),
                            },
                        ],
                    },
                    {
                        prevColor: [
                            {
                                field: "event_tag_color",
                                value: JSON.stringify({
                                    r: header_Data.event_tag_color.value.r,
                                    g: header_Data.event_tag_color.value.g,
                                    b: header_Data.event_tag_color.value.b,
                                    a: header_Data.event_tag_color.value.a,
                                }),
                            },
                            {
                                field: "professional_tag_color",
                                value: JSON.stringify({
                                    r: header_Data.professional_tag_color.value.r,
                                    g: header_Data.professional_tag_color.value.g,
                                    b: header_Data.professional_tag_color.value.b,
                                    a: header_Data.professional_tag_color.value.a,
                                }),
                            },
                            {
                                field: "personal_tag_color",
                                value: JSON.stringify({
                                    r: header_Data.personal_tag_color.value.r,
                                    g: header_Data.personal_tag_color.value.g,
                                    b: header_Data.personal_tag_color.value.b,
                                    a: header_Data.personal_tag_color.value.a,
                                }),
                            },
                            {
                                field: "tags_text_color",
                                value: JSON.stringify({
                                    r: header_Data.tags_text_color.value.r,
                                    g: header_Data.tags_text_color.value.g,
                                    b: header_Data.tags_text_color.value.b,
                                    a: header_Data.tags_text_color.value.a,
                                }),
                            },
                        ],
                    },
                ],
            },
        ];

        /**
         * -------------------------------------------------------------------------------------------------------------------
         * @description This method handles selected color values
         * -------------------------------------------------------------------------------------------------------------------
         *
         * @method
         */
        const seletedColorVal = resetValues.filter((ele) => {
            if (
                _.has(ele, [`${colorDataKey}`]) &&
                ele.hasOwnProperty(`${colorDataKey}`)
            ) {
                return ele;
            }
        });

        if (resetStatus == 1) {
            switch (colorDataKey) {
                case "header_footer_customized":
                    resetColorCallback(
                        seletedColorVal[0].header_footer_customized[0].allColorVal
                    );
                    break;
                case "button_customized":
                    resetColorCallback(
                        seletedColorVal[0].button_customized[0].allColorVal
                    );
                    break;
                case "sh_customized":
                    resetColorCallback(seletedColorVal[0].sh_customized[0].allColorVal);
                    break;
                case "content_customized":
                    resetColorCallback(
                        seletedColorVal[0].content_customized[0].allColorVal
                    );
                    break;
                case "conv_customization":
                    resetColorCallback(
                        seletedColorVal[0].conv_customization[0].allColorVal
                    );
                    break;
                case "badge_customization":
                    resetColorCallback(
                        seletedColorVal[0].badge_customization[0].allColorVal
                    );
                    break;
                case "space_customization":
                    resetColorCallback(
                        seletedColorVal[0].space_customization[0].allColorVal
                    );
                    break;
                case "user_grid_customization":
                    resetColorCallback(
                        seletedColorVal[0].user_grid_customization[0].allColorVal
                    );
                    break;
                case "tags_customization":
                    resetColorCallback(
                        seletedColorVal[0].tags_customization[0].allColorVal
                    );
                    break;
            }
        }

        if (resetStatus == 0) {
            switch (colorDataKey) {
                case "header_footer_customized":
                    resetColorCallback(
                        seletedColorVal[0].header_footer_customized[0].prevColor
                    );
                    break;
                case "button_customized":
                    resetColorCallback(seletedColorVal[0].button_customized[0].prevColor);
                    break;
                case "sh_customized":
                    resetColorCallback(seletedColorVal[0].sh_customized[0].prevColor);
                    break;
                case "content_customized":
                    resetColorCallback(
                        seletedColorVal[0].content_customized[0].prevColor
                    );
                    break;
                case "conv_customization":
                    resetColorCallback(
                        seletedColorVal[0].conv_customization[0].prevColor
                    );
                    break;
                case "badge_customization":
                    resetColorCallback(
                        seletedColorVal[0].badge_customization[0].prevColor
                    );
                    break;
                case "space_customization":
                    resetColorCallback(
                        seletedColorVal[0].space_customization[0].prevColor
                    );
                    break;
                case "user_grid_customization":
                    resetColorCallback(
                        seletedColorVal[0].user_grid_customization[0].prevColor
                    );
                    break;
                case "tags_customization":
                    resetColorCallback(
                        seletedColorVal[0].tags_customization[0].prevColor
                    );
                    break;
            }
        }
        let day;
    };

    return (
        <div className="DesignSettingContentDiv">
            <LoadingSkeleton loading={loading} skeleton={<DesignSettingSkeleton />}>
                {gKey !== "default" && (
                    <SwitchAccordion
                        colorCallback={colorCallback}
                        ownCustomization={ownCustomization}
                        callBackCancel={callBackCancel}
                        callBack={callBack}
                        dataKey={"group_has_own_customization"}
                        updateDesignSetting={updateDesignSetting}
                        getKeyData={getKeyData}
                        graphicSetting={graphicSetting}
                        AllowDesignSetting={AllowDesignSetting}
                        heading={"Group Setting Customisation"}
                        SubHeading={"Turn On to manage your Group design settings"}
                        icon={<QuickDesignIcon />}
                        color_modal={false}
                        setFollowOrganisation={setFollowOrganisation}
                        followOrganisation={followOrganisation}
                    />
                )}

                <SwitchAccordion
                    colorCallback={colorCallback}
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"apply_customisation"}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={QuickSetting}
                    heading={"Quick Design Settings"}
                    SubHeading={
                        "Customize your Logo and Primary Colors for your Event's Live Page"
                    }
                    icon={<QuickDesignIcon />}
                    color_modal={false}
                    setFollowOrganisation={setFollowOrganisation}
                    followOrganisation={followOrganisation}
                    AllowDesignSetting={AllowDesignSetting}
                    eventLogoDefaultData={eventLogoDefaultData}
                    setEventLogoDefaultData={setEventLogoDefaultData} />
                {/* <EventLogo /> */}
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"header_footer_customized"}
                    resetColorHandler={resetColorHandler}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={HeaderSettings}
                    heading={"Headers"}
                    SubHeading={"Customize the Headers of your live Event pages"}
                    icon={<HeadersIcon />}
                    color_modal={true}
                    reset_color={"headers"}
                    headerLine1Err={headerLine1Err}
                    headerLine2Err={headerLine2Err}
                    //   headerLineErr= {headerLineErr}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"button_customized"}
                    resetColorHandler={resetColorHandler}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={ButtonSettings}
                    heading={"Buttons"}
                    SubHeading={"Manage the colors of your Event's primary button"}
                    icon={<ButtonSettingIcon />}
                    color_modal={true}
                    reset_color={"buttons"}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"texture_customized"}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={TextureSettings}
                    heading={"Texture"}
                    SubHeading={"Customize the components design"}
                    icon={<TextureSettingIcon />}
                    color_modal={false}
                    tooltip_labels={tooltip_labels}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"general_setting"}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={GeneralSettings}
                    heading={"General Settings"}
                    SubHeading={"Customize the general design"}
                    icon={<GeneralSettingsIcon />}
                    color_modal={false}
                    tooltip_labels={tooltip_labels}
                    setShowGridImageField={setShowGridImageField}
                    showGridImageState={showGridImageField}
                    userGridImgDefaultData={userGridImgDefaultData}
                />

                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"content_customized"}
                    resetColorHandler={resetColorHandler}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={ContentSetting}
                    getSettings={getSettings}
                    heading={"Content"}
                    SubHeading={"Customise your Event's Content section"}
                    icon={<ContentSettingIcon />}
                    color_modal={true}
                    reset_color={"content"}
                    tooltip_labels={tooltip_labels}
                    //img crop related props--->
                    showContentCropPreview={showContentCropPreview}
                    setShowContentCropPreview={setShowContentCropPreview}
                    contentImgDefaultData={contentImgDefaultData}
                />

                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"conv_customization"}
                    resetColorHandler={resetColorHandler}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={ConversationSettings}
                    heading={"Conversation"}
                    SubHeading={"Customize the color settings for Conversation Section"}
                    icon={<ConversationSettingIcon />}
                    color_modal={true}
                    reset_color={"conversation"}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"badge_customization"}
                    resetColorHandler={resetColorHandler}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={UserBadgeSettings}
                    heading={"User's Badge"}
                    SubHeading={"Customize User's Badge color"}
                    icon={<BadgeSettingIcon />}
                    color_modal={true}
                    reset_color={"userbadge"}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"space_customization"}
                    resetColorHandler={resetColorHandler}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={SpaceSetting}
                    heading={"Space"}
                    SubHeading={"Manage the design and colors of your virtual rooms"}
                    icon={<SpaceSettingIcon />}
                    color_modal={true}
                    reset_color={"spaces"}
                    tooltip_labels={tooltip_labels}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"user_grid_customization"}
                    resetColorHandler={resetColorHandler}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={UserGridSettings}
                    heading={"User Grid"}
                    SubHeading={"Customize online User's pool section"}
                    icon={<GridSettingIcon />}
                    color_modal={true}
                    reset_color={"userGrid"}
                    tooltip_labels={tooltip_labels}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"tags_customization"}
                    resetColorHandler={resetColorHandler}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={TagsSettings}
                    heading={"Tags"}
                    SubHeading={"Customize Tags color"}
                    icon={<TagSettingIcon />}
                    color_modal={true}
                    reset_color={"tags"}
                />

                {/* <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"conv_customization"}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={ConversationSettings}
                    heading={"Conversation"}
                    SubHeading={"Customize the color settings for Conversation Section"}
                    icon={<ConversationSettingIcon />}
                    color_modal={true}
                    reset_color={"conversation"}
                    setFollowOrganisation={setFollowOrganisation}
                    followOrganisation={followOrganisation}
                    AllowDesignSetting={AllowDesignSetting}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"badge_customization"}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={UserBadgeSettings}
                    heading={"User's Badge"}
                    SubHeading={"Customize User's Badge color"}
                    icon={<BadgeSettingIcon />}
                    color_modal={true}
                    reset_color={"userbadge"}
                    setFollowOrganisation={setFollowOrganisation}
                    followOrganisation={followOrganisation}
                    AllowDesignSetting={AllowDesignSetting}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"space_customization"}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={SpaceSetting}
                    heading={"Space"}
                    SubHeading={"Manage the design and colors of your virtual rooms"}
                    icon={<SpaceSettingIcon />}
                    color_modal={true}
                    reset_color={"spaces"}
                    tooltip_labels={tooltip_labels}
                    setFollowOrganisation={setFollowOrganisation}
                    followOrganisation={followOrganisation}
                    AllowDesignSetting={AllowDesignSetting}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"user_grid_customization"}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={UserGridSettings}
                    heading={"User Grid"}
                    SubHeading={"Customize online User's pool section"}
                    icon={<GridSettingIcon />}
                    color_modal={true}
                    reset_color={"userGrid"}
                    tooltip_labels={tooltip_labels}
                    setFollowOrganisation={setFollowOrganisation}
                    followOrganisation={followOrganisation}
                    AllowDesignSetting={AllowDesignSetting}
                />
                <SwitchAccordion
                    callBackCancel={callBackCancel}
                    callBack={callBack}
                    dataKey={"tags_customization"}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={TagsSettings}
                    heading={"Tags"}
                    SubHeading={"Customize Tags color"}
                    icon={<TagSettingIcon />}
                    color_modal={true}
                    reset_color={"tags"}
                    setFollowOrganisation={setFollowOrganisation}
                    followOrganisation={followOrganisation}
                    AllowDesignSetting={AllowDesignSetting}
                /> */}

                <SwitchAccordion
                    update={updateLabels}
                    labels={labels}
                    dataKey={"label_customized"}
                    hideButtons={true}
                    updateDesignSetting={updateDesignSetting}
                    getKeyData={getKeyData}
                    graphicSetting={graphicSetting}
                    child={LabelSettings}
                    heading={"Labels"}
                    SubHeading={"Manage the labels for team roles here"}
                    icon={<LabelSettingIcon />}
                    color_modal={true}
                    setFollowOrganisation={setFollowOrganisation}
                    followOrganisation={followOrganisation}
                    AllowDesignSetting={AllowDesignSetting}
                />
            </LoadingSkeleton>
        </div>
    );
};

export default DesignSettings;
