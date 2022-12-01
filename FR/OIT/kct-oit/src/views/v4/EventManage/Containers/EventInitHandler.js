import React, {useEffect, useState} from 'react';
import "../EventManage.css"
import {useParams} from "react-router-dom";
import {useDispatch} from "react-redux";
import eventV4Api from "../../../../redux/action/apiAction/v4/event";
import _ from "lodash";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Event data manager to provide and manipulate the event data from api or from redux
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param props
 *
 * @component
 * @class
 */
let useEventInitData = (props) => {
    const [eventInitData, setEventInitData] = useState({
        scenery: {
            allSceneryData: [],
            currentSceneryData: null,
        },
        broadcasting: {
            meetingModerators: [],
            webinarModerators: [],
            isConfigured: 0,
        }
    });


    const {event_uuid} = useParams();
    const dispatch = useDispatch();

    /**
     * This use effect will trigger when form or all scenery is fetched
     * and it will check if form value selected scenery id is present in all scenery data or not
     * and current form asset is present or not in current scenery else take the first asset from current scenery
     */
    useEffect(() => {
        if (props.formValues && _.has(props.formValues, ['event_scenery'])) {
            // fetching the current scenery selected in form
            let currentScenery = findSceneryFromForm();

            // fetching the asset if form asset found in current scenery else taking the first of current scenery
            let currentAsset = findAssetFromForm(currentScenery);

            setEventInitData({
                ...eventInitData,
                scenery: {
                    ...eventInitData.scenery,
                    currentSceneryData: currentScenery
                },
            })

            // if form selected asset and current asset does not match then updating the form
            // console.log('dddddddddddd current asset is ', currentAsset);
            if (currentAsset && props.formValues?.event_scenery_asset !== currentAsset.asset_id) {
                props.updateEventForm('event_scenery_asset', currentAsset.asset_id)
            }
        }
    }, [props.formValues, eventInitData.scenery.allSceneryData]);

    useEffect(() => {
        if (props.formValues?.event_scenery_asset) {
            let currentScenery = findSceneryFromForm();
            let currentAsset = findAssetFromForm(currentScenery);
            currentAsset && props.updateEventForm('event_top_bg_color', {
                'field': 'event_top_bg_color',
                'value': currentAsset.asset_default_color,
            });
        }
    }, [props.formValues?.event_scenery_asset]);

    const moderatorMapFromApi = moderators => {
        return moderators ? moderators.map(moderator => {
            return {
                value: moderator.id,
                label: `${moderator?.fname} ${moderator.lname} ${moderator.email}`
            }
        }) : [];
    }

    useEffect(() => {
        // to check if its edit mode or create mode

        dispatch(eventV4Api.getEventInitData(event_uuid || null))
            .then((res) => {
                try {
                    setEventInitData({
                        ...eventInitData,
                        scenery: {
                            ...eventInitData.scenery,
                            allSceneryData: res.data.data.scenery.all_scenery_data,
                        },
                        broadcasting: {
                            ...eventInitData.broadcasting,
                            meetingModerators: moderatorMapFromApi(res.data.data.broadcasting?.meeting_moderators),
                            webinarModerators: moderatorMapFromApi(res.data.data.broadcasting?.webinar_moderators),
                            isConfigured: res.data.data.broadcasting !== null,
                        }
                    })
                } catch (err) {
                    console.log(err)
                }
            })
            .catch(err => {
            });
    }, []);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find the scenery , if form have selected scenery then it will be searched inside all sceenry
     * and return that.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @returns {*}
     */
    const findSceneryFromForm = () => {
        return eventInitData.scenery.allSceneryData.find(e => e.category_id === props.formValues.event_scenery);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will check the form asset and if form asset is found in current asset then it will return
     * else the first asset from current scenery will be return
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {SceneryModel} currentScenery
     * @returns {SceneryAssetModel|null}
     */
    const findAssetFromForm = (currentScenery) => {
        if (props.formValues && !props.formValues.event_scenery_asset && currentScenery) {
            return currentScenery.category_assets.length ? currentScenery.category_assets[0] : null;
        } else if (currentScenery && props.formValues.event_scenery_asset && currentScenery.category_type === 1) {
            // checking if form scenery asset is from current scenery or not
            let currentAsset = currentScenery.category_assets.find(asset => asset.asset_id === props.formValues.event_scenery_asset);
            if (!currentAsset) {
                // form selected asset is not present in current scenery so updating the form asset to first asset of current scenery
                return currentScenery.category_assets.length ? currentScenery.category_assets[0] : null;
            }
            // asset from current scenery
            return currentAsset;
        }

        return null;
    }

    return eventInitData;
}

export default useEventInitData;