import React, {useEffect, useState} from 'react';
import TimeInput from '../Common/TimeInputs/index.js';
import Details from '../Description/Description.js';
import ContentEventIcon from '../../../../Svg/ContentEventIcon';
import NetworkEventIcon from '../../../../Svg/NetworkEventIcon';
import ArrowRightIcon from '@mui/icons-material/ArrowRight';
import ArrowDropDownIcon from '@mui/icons-material/ArrowDropDown';
import {Grid, TextField, FormControl, InputLabel, Select, MenuItem, Checkbox, Button, Link} from '@material-ui/core';
import _ from 'lodash';
import Helper from '../../../../../Helper.js';
import Validation from '../../../../../functions/ReduxFromValidation.js';
import momentRepo from "../../../../../repositories/EventMomentRepository";
import Constants from "../../../../../Constants";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Thia component is a common component for moments for both content type and networking type
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Boolean} props.accessMode To check the access mode
 * @param {Boolean} props.autoCreate To check the auto create mode
 * @param {MomentObj} props.data Moment related data
 * @param {Function} props.onChange Method to handle the on change event on input box
 * @param {Function} props.onDelete Method to handle the deletion of moments
 * @return {JSX.Element}
 * @constructor
 */
const MomentComp = (props) => {
    const [show, setShow] = useState(true);
    const [validated, setValidated] = useState(false);
    const [availableOp] = useState([]);

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is used for validate the selected value of moment
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} selected Selected broadcast type
     * @returns {boolean}
     */
    const validateSelected = (selected) => {
        if (selected === Constants.broadcastType_webinar) {
            return _.has(props, ['availableBroadcasts', 'webinar_moderators'])
                && _.isArray(props.availableBroadcasts.webinar_moderators)
                && props.availableBroadcasts.webinar_moderators.length > 0;
        } else {
            return _.has(props, ['availableBroadcasts', 'meeting_moderators'])
                && _.isArray(props.availableBroadcasts.meeting_moderators)
                && props.availableBroadcasts.meeting_moderators.length > 0;
        }
    }

    useEffect(() => {
        checkValidation();
    }, [props.data])


    const validateContent = (data) => {

        if (!_.has(data, ['moment_type']) && data.video_url && data.video_url != '' && (Validation.matchYoutubeUrl(data.video_url) == undefined || Validation.matchVimeoUrl(data.video_url) == undefined)) {
            return true
        }

        if (!_.has(data, ['moment_type']) || (!_.has(data, ['name']) || data.name == '')) {
            return false
        }

        switch (data.moment_type) {
            case 1:
                return _.has(data, ['video_url']) && data.video_url != '' && Validation.matchYoutubeUrl(data.video_url) == undefined;
            case 2:
                return _.has(data, ['video_url']) && data.video_url != '' && Validation.matchVimeoUrl(data.video_url) == undefined;
            case 3:
                return _.has(data, ['moderator']) && data.moderator && _.has(data, ['speakers']) && !_.isEmpty(data.speakers) && (!data.conference_type || validateSelected(data.conference_type));

            default:
                break;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for check the validation  for moment type and content type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const checkValidation = () => {
        const {data} = props;

        if (data.moment_type === Constants.momentType_networking) {
            // moment type is networking so just check for the name field only
            return setValidated(!_.has(data, ['name']) || data.name !== '');
        } else {
            let validate = false;
            // moment type is content, so validating things according to content type and broadcast
            if (_.has(data, ['moment_type'])) {
                // validating moment type dependent fields

                // validating youtube content type
                if (data.contentType === Constants.contentType_youtube) {
                    validate = _.has(data, ['video_url'])
                        && data.video_url.trim() !== ''
                        && Validation.matchYoutubeUrl(data.video_url) === undefined
                } else if (data.contentType === Constants.contentType_vimeo) {
                    validate = _.has(data, ['video_url'])
                        && data.video_url.trim() !== ''
                        && Validation.matchVimeoUrl(data.video_url) === undefined
                } else if (data.contentType === Constants.contentType_broadcasting_meeting ||
                    data.contentType === Constants.contentType_broadcasting_meeting) {
                    validate = _.has(data, ['moderator'])
                        && data.moderator;
                } else if (data.contentType === Constants.contentType_broadcasting_webinar ||
                    data.contentType === Constants.contentType_broadcasting_webinar) {
                    validate = _.has(data, ['moderator'])
                        && data.moderator;
                }
                if (validate) {
                    // validating common fields among the content type moments
                    validate = _.has(data, ['name']) && data.name !== '';
                }

            }
            setValidated(validate);
        }
    }

    const {content} = props;
    return (
        <>
            <Grid container spacing={3}>
                <Grid item lg={2} className="pt-4px IconPair_LeftDiv">
                    {
                        props.momentData.contentType !== null
                            ? <ContentEventIcon onClick={() => {
                                setShow(!show)
                            }} />
                            : <NetworkEventIcon onClick={() => {
                                setShow(!show)
                            }} />
                    }
                    {show ?
                        <ArrowDropDownIcon className={`Triangle-${props.momentData.contentType ? 'gray' : 'blue'}`}
                                           fontSize="large"
                                           onClick={() => {
                                               setShow(!show)
                                           }} /> :
                        <ArrowRightIcon className={`Triangle-${props.momentData.contentType ? 'gray' : 'blue'}`}
                                        fontSize="large"
                                        onClick={() => {
                                            setShow(!show)
                                        }} />}
                    {/* <ArrowRightIcon  onClick={()=>{setShow(!show)}} /> */}
                </Grid>
                <Grid item lg={4}>
                    <TimeInput disabled={props.accessMode ? props.accessMode : props.disabled} validated={validated}
                               index={props.index} autoCreate={props.autoCreate} onDelete={() => {
                        props.onDelete(props.index, content)
                    }} date={props.date} data={{start_time: props.start_time, end_time: props.end_time}}
                               convertDateToTime={props.onTimeChange} />
                </Grid>
            </Grid>
            <div style={!show ? {display: 'none'} : {}}>
                <Details
                    availableBroadcasts={props.availableBroadcasts}
                    disabled={props.accessMode}
                    momentData={props.momentData}
                    onMomentUpdate={props.onMomentUpdate}

                    // old props
                    validateSelectedConf={validateSelected}
                    index={props.index}
                    onChangeMultiple={props.onChangeMultiple}
                    content={props.content}
                    autoCreate={props.autoCreate}
                />
            </div>
        </>
    )
}

export default MomentComp;