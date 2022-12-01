import React, {useEffect, useState} from 'react';
import SelectInput from '../../Common/Inputs/SelectInput.js';
import NameInput from '../../Common/Inputs/Name.js';
import UserAutocomplete from '../../../../../Common/UserAutoComplete/UserAutoComplete.js';
import {Grid, TextField, FormControl, InputLabel, Select, MenuItem, Checkbox, Button, Link} from '@material-ui/core';
import MultiSelectUse from '../../Common/Inputs/MultiSelectUse.js';
import Validation from '../../../../../../functions/ReduxFromValidation.js';
import ErrorOutlineIcon from '@mui/icons-material/ErrorOutline';
import _ from 'lodash';
import Tooltip from "@material-ui/core/Tooltip";
import "./ContentDes.css";
import Helper from '../../../../../../Helper.js';
import Constants from "../../../../../../Constants";
import {useDispatch, useSelector} from 'react-redux'
import {useTranslation} from 'react-i18next';

/**
 * @constant
 * @type {array}
 */
const contentOptions = [

    {value: Constants.contentType_broadcasting_meeting, label: 'Broadcasting Meeting', disabled: false},
    {value: Constants.contentType_broadcasting_webinar, label: 'Broadcasting Webinar', disabled: false},
    {value: Constants.contentType_broadcasting_youTube_live, label: 'Broadcasting Youtube Live', disabled: true},
    {value: Constants.contentType_broadcasting_facebook_live, label: 'Broadcasting FaceBook Live', disabled: true},
    {value: Constants.contentType_youtube, label: 'Pre-Recorded Youtube', disabled: false},
    {value: Constants.contentType_vimeo, label: 'Pre-Recorded Vimeo', disabled: false},
    // {value: Constants.contentType_broadcasting, label: 'Broadcasting'},
];

/**
 * @constant - autoCreateContentOption
 * @type {array}
 */
const autoCreateContentOption = [
    {value: Constants.contentType_broadcasting_meeting, label: 'Broadcasting Meeting', disabled: false},
    {value: Constants.contentType_broadcasting_webinar, label: 'Broadcasting Webinar', disabled: false},

]


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component shows the information about Content Details that is used for content type moments
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Data related to moments
 * @param {MomentObj} props.momentData Data related to moments
 * @param {Function} props.onMomentUpdate To handle change in the inputs of moments and update moment time
 * @return {JSX.Element}
 * @constructor
 */
const ContentDetails = (props) => {
    const [availableModerators, setAvailableModerators] = useState([]);

    const [selectedSpeakers, setSpeakers] = useState([]);
    const [confValidate, setValidated] = useState(true);

    let broadcastOptions = [
        {value: Constants.broadcastType_webinar, label: 'Zoom Webinar'},
        {value: Constants.broadcastType_meeting, label: 'Zoom Meeting'},
    ];

    const {t} = useTranslation(["labels"]);
    //for event labels data
    const eventRoleLabels = useSelector(data => data.Auth.eventRoleLabels.labels)
    const eventRolelabelCustomized = useSelector(data => data.Auth.eventRoleLabels.label_customized);

    useEffect(() => {
        // provided moment have some type
        if (_.has(props, ['momentData', 'moment_type'])) {
            if (props.momentData.contentType !== null && props.momentData.broadcastType) {
                // broadcasting type content
                refreshAvailableModerators(props.momentData.broadcastType, false);
                // firstModerator && setSelectedModerator(firstModeratorId);
            }
        }
    }, [props.momentData.moderator]);

//use state hook for update speaker 
    useState(() => {
        if (_.has(props.momentData, ['speaker_data'])) {
            setSpeakers(props.momentData.speaker_data);
        }
    }, [])

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling  changes for content type and send the data to moment update api
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleChangeContentType = (e) => {
        // setContentType(e.target.value);
        let newData = {
            ...props.momentData,
            broadcastType: null,
            contentType: e.target.value,
            moment_type: e.target.value,
        };

        if (e.target.value === Constants.contentType_broadcasting_meeting ||
            e.target.value === Constants.contentType_broadcasting_webinar
        ) {
            // selected content is broadcast so set the moderator according to current content type
            let moderator = refreshAvailableModerators(e.target.value, false);
            newData.broadcastType = Constants.broadcastType_default;
            newData.moderator = moderator;
            handleChangeBroadcastType(e)
        } else {
            if (newData.moderator) {
                delete newData.moderator;
            }
        }
        props.onMomentUpdate(newData);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling  changes for video urls and send the data to moment update api
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleChangeVideoUrl = (e) => {
        let newData = {...props.momentData};
        newData.video_url = e.target.value;
        props.onMomentUpdate(newData);
    }
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling  changes for moderator and send the data to moment update api
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleChangeModerator = (e) => {
        let newData = {...props.momentData};
        let moderator = availableModerators.find(existing => existing.data.id === e.target.value);
        newData.moderator = _.has(moderator, ['data']) ? moderator.data : null;
        props.onMomentUpdate(newData);
    }

    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling  changes for Speaker Change and send the data to moment update api
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {MomentObj} data Moments related data
     */
    const handleSpeakerChange = (data) => {
        let newData = {...props.momentData};
        newData.speakers = _.isEmpty(data) ? {} : data;
        props.onMomentUpdate(newData);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To refresh the moderator list, conference type 0 => webinar, 1 = meeting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} broadcastType Type of broadcast
     */
    const refreshAvailableModerators = (broadcastType) => {
        let moderators = [];
        console.log("broadcastType", props.availableBroadcasts)
        if (broadcastType === Constants.broadcastType_webinar) { //2
            // conference type is webinar
            moderators = _.has(props.availableBroadcasts, ['webinar_moderators'])
                ? props.availableBroadcasts.webinar_moderators
                : [];
        } else if (broadcastType === Constants.broadcastType_meeting) { //1
            // conference type is meeting
            moderators = _.has(props.availableBroadcasts, ['meeting_moderators'])
                ? props.availableBroadcasts.meeting_moderators
                : [];
        }

        let firstModerator = null;
        // mapping the moderators list required for the select component
        moderators = moderators.map((moderator, i) => {
            if (i === 0) {
                // for the first index store moderator id to show as selected item
                firstModerator = moderator;
            }
            return {
                value: moderator.id,
                label: `${moderator.fname} ${moderator.lname} (${moderator.email})`,
                data: moderator,
            }
        });
        console.log("mmmmmm", moderators)
        setAvailableModerators(moderators);
        return firstModerator
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling the broadcast type value changes
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleChangeBroadcastType = (e) => {
        setValidated(props.validateSelectedConf(e.target.value));
        let moderator = refreshAvailableModerators(e.target.value, false);
        let newData = {...props.momentData};
        if (moderator !== null) {
            newData.moderator = moderator;
        }
        newData.broadcastType = e.target.value;
        props.onMomentUpdate(newData);
    }
    const changeSpeakerData = (data) => {
        const userArr = data.map((item) => {
            return item.id
        });
        setSpeakers(data);
    }

    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for check the url value and validate the url and return false if
     * url is not correct
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {Boolean}
     */
    const validateUrl = () => {
        if (props.momentData.video_url && Validation.matchYoutubeUrl(props.momentData.video_url) == undefined) {
            return false;
        }

        if (props.momentData.video_url && Validation.matchVimeoUrl(props.momentData.video_url) == undefined) {
            return false;
        }
        return true
    }
    console.log("moment dattttttt", availableModerators, props.momentData.moderator)
    return (
        <div className="contentType-Div">
            <SelectInput
                name={`moment_type-${props.momentData.id !== undefined ? props.momentData : props.momentData.localKey}`}
                value={props.momentData.contentType}
                options={props.autoCreate || props.autoCreate === 1 ? autoCreateContentOption : contentOptions}
                id={props.momentData.id}
                handleChange={handleChangeContentType}
                disabled={props.disabled}
            />
            {props.momentData.contentType !== Constants.contentType_broadcasting_meeting &&
            props.momentData.contentType !== Constants.contentType_broadcasting_webinar ?
                <>

                    <p className='customPara' style={{position: "absolute", left: "-186px", top: "60px"}}>Video Url
                        :</p>
                    <NameInput
                        name={`video_url-${props.index}`}
                        placeholder="Enter Video Url"
                        index={props.index}
                        value={props.momentData.video_url ? props.momentData.video_url : ''}
                        validation={validateUrl() ? (props.momentData.contentType === Constants.contentType_youtube
                                ? Validation.matchYoutubeUrl
                                : Validation.matchVimeoUrl
                        ) : []}
                        onChange={handleChangeVideoUrl}
                        disabled={props.disabled}
                    />
                </>
                :
                <div className="confrencetypeDiv">
                    {/* <SelectInput
                        name={`conference_type-${props.index}`}
                        value={props.momentData.broadcastType}
                        options={broadcastOptions}
                        id={props.momentData.id}
                        handleChange={handleChangeBroadcastType}
                        disabled={props.disabled}
                    /> */}
                    {!confValidate &&
                    <Tooltip arrow title="Configure This Zoom Account" placement="top-start"><ErrorOutlineIcon
                        color="error" /></Tooltip>}
                    <div>
                        <span
                            className="customPara ModeratorLabel">{eventRolelabelCustomized === 1 ? Helper.getLabel('moderator', eventRoleLabels) : t("labels:Moderator")} :</span>
                        {/*<UserAutocomplete disabled={props.disabled} name={selectedMod} id={changeMod}/>*/}
                        <SelectInput
                            // name={`moderator-${props.index}`} // denotes moderator for the content index
                            value={props.momentData.moderator ? props.momentData.moderator.id : null}
                            options={availableModerators}
                            id={props.id}
                            handleChange={handleChangeModerator}
                            disabled={props.disabled}
                        />
                    </div>
                    <MultiSelectUse
                        disabled={props.disabled}
                        selectedSpeakers={props.momentData.speakers}
                        multiple={true}
                        // disabled={false}
                        onChange={handleSpeakerChange}
                        name={{name: '', id: ''}}
                    />
                </div>
            }
        </div>
    )
}

export default ContentDetails;