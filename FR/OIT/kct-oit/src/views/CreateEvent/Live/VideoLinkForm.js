import React, {useState, useRef, useEffect} from "react";
import {useAlert} from "react-alert";
import {useTranslation} from "react-i18next";
import {useParams} from "react-router-dom";
import {Field, reduxForm} from "redux-form";
import {useDispatch} from "react-redux";
import {
  Button,
  InputLabel,
  MenuItem,
  Select,
  TextField,
} from "@mui/material";
import eventAction from "../../../redux/action/apiAction/event";
import Helper from "../../../Helper";
import Validation from "../../../functions/ReduxFromValidation";
import VimeoIcon from "../../Svg/VimeoIcon";
import YoutubeIcon from "../../Svg/YoutubeIcon";
import "./VideoUploadPopup.css";


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is used to monitor user inputs and throw error if the input values are not matching the
 * proper criteria.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} values Validate the video link values
 * @param {String} values.vlink Vimeo video link
 * @param {String} values.ylink Youtube video link
 * @return {Object}  different type of error message objects
 */
const validate = (values) => {
  const errors = {};
  const requiredFields = ["vlink", "ylink"];
  requiredFields.forEach((field) => {
    if (!values[field]) {
      errors[field] = "Required";
    }
  });
  if (values["ylink"] && Validation.matchYoutubeUrl(values["ylink"])) {
    errors["ylink"] = `Enter valid link address`;
  }
  if (values["vlink"] && Validation.matchVimeoUrl(values["vlink"])) {
    errors["vlink"] = `Enter valid link address`;
  }

  return errors;
};


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common structure for a input field to render input box for youtube link & vimeo link. This
 * will take data(from parameter where it called) which is necessary to render relative text fields.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Input} input Actual input box with its default properties
 * @param {String} value Value of the input box
 * @param {String} label Label of text field
 * @param {String} defaultValue Default value of input box
 * @param {Object} invalid Enter value is invalid
 * @param {Object} touched To indicate if the input box is touched or not
 * @param {Object} error Error message from input box
 * @param {Object} custom Custom number of input box
 * @returns {JSX.Element}
 */
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
      <div style={{marginBottom: "16px"}}>
        <TextField
          name={input.name}
          value={value}
          size="small"
          onChange={input.onChange}
          errorText={touched && error}
          error={touched && error && invalid}
          {...input}
          {...custom}
        />
        {touched && error && <div className={"text-danger"}>{error}</div>}
      </div>
    </React.Fragment>
  );
};

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to upload a video(youtube/vimeo) using video links for content player.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getLiveEventData This method will fetch event live page data
 * @param {Function} props.closePopup This method will close the popup to upload videos
 * @param {Function} props.onUpload This method will fetch event live page data after the video is uploaded
 * @returns {JSX.Element}
 * @constructor
 */
const VideoLinkForm = (props) => {
  const {handleSubmit, pristine, reset, submitting} = props;
  const {event_uuid} = useParams();
  const alert = useAlert();
  const dispatch = useDispatch();
  // video popup related states
  const [selectedVideoLinkType, setSelectedVideoLinkType] = useState(1);
  const [videoLinkValue, setVideoLinkValue] = useState("");
  const {t} = useTranslation(["notification"]);

  /**
   * ----------------------------------------------------------------------------------------------------------------------
   * @description This array  have two options  for youtube and vimeo
   * ---------------------------------------------------------------------------------------------------------------------
   */
  const videoTypeOptions = [
    {label: "Youtube", value: 1, icon: <YoutubeIcon />},
    {label: "Vimeo", value: 2, icon: <VimeoIcon />},
  ];

    useEffect(() => {
    });

    const handlePopupClose = () => {
        props.closePopup();
    };


  /**
   * --------------------------------------------------------------------------------------------------------------------
   * @description This function will trigger when user change video type and it will save current selecetd value in a
   * state(setSelectedVideoLinkType).
   * --------------------------------------------------------------------------------------------------------------------
   *
   * @method
   * @param {Object} e Javascript event object
   */
  const handleVideoTypeChange = (e) => {
    setSelectedVideoLinkType(e.target.value);
  };

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function will trigger when user add video link in the link field and this will save current
   * value of the link field in a state(handleVideoLinkChange).
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   * @param {Object} e Javascript event object
   */
  const handleVideoLinkChange = (e) => {
    setVideoLinkValue(e.target.value);
  };


  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function will trigger when user select video type and add correct video
   * link(related to its type - vimeo/youtube) and click on save button.This will handle an API call to upload added
   * video.
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   */
  const handleSaveButton = () => {
    // e.preventDefault();
    const dataToSend = {
      event_uuid: event_uuid,
      event_live_video_link: videoLinkValue,
      video_type: selectedVideoLinkType,
    };
    dispatch(eventAction.updateEventLiveData(dataToSend))
      .then((res) => {
        props.getLiveEventData();
        props.closePopup();
        props.onUpload()

        alert.show(t("notification:rec add 1"), {type: "success"});
      })
      .catch((err) => {
        alert.show(Helper.handleError(err), {type: "error"});
      });
  };


  return (
    <div>
      <InputLabel id="demo-simple-select-label">Video Type</InputLabel>
      <Select
        labelId="demo-simple-select-label"
        id="demo-simple-select"
        className="videoUploadModalSelect"
        value={selectedVideoLinkType}
        // label="Video Type"
        size="small"
        fullWidth
        onChange={handleVideoTypeChange}
      >
        {videoTypeOptions.map((type) => (
          <MenuItem value={type.value}>{type.icon}<span style={{padding: "0 8px"}}>{type.label}</span></MenuItem>
        ))}
      </Select>
      <form onSubmit={handleSubmit(handleSaveButton)}>
        {selectedVideoLinkType == 1 &&
          <Field
            name="ylink"
            placeholder="Youtube Link"
            variant="outlined"
            className="ThemeInputTag"
            component={renderTextField}
            onChange={handleVideoLinkChange}
            value={videoLinkValue}
          //  InputProps={{
          //      startAdornment: <EmailIcon />,
          //  }}
          />
        }
        {selectedVideoLinkType == 2 &&
          <Field
            name="vlink"
            placeholder="Vimeo Link"
            variant="outlined"
            className="ThemeInputTag"
            component={renderTextField}
            onChange={handleVideoLinkChange}
            value={videoLinkValue}
          //  InputProps={{
          //      startAdornment: <EmailIcon />,
          //  }}
          />
        }
        <div className="uploadVideo_footer">
          <Button
            type="submit"
            variant="contained"
            color="primary"
            className="long_btn"
          >
            Save
          </Button>
        </div>
      </form>
    </div>
  );
};

export default reduxForm({
  form: "MuiForm", // a unique identifier for this form
  validate,
})(VideoLinkForm);
