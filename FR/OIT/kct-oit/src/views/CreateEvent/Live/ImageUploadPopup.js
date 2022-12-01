import React, {useState, useEffect} from "react";
import {useAlert} from "react-alert";
import CloseIcon from "@mui/icons-material/Close";
import {useDispatch} from "react-redux";
import eventAction from "../../../redux/action/apiAction/event";
import ImgCropper from '../../Common/ImgCropperold/ImgCropper'
import {
  Dialog,
  DialogContent,
  IconButton,
} from "@mui/material";
import Helper from "../../../Helper";
import {useTranslation} from "react-i18next";
import {useParams} from "react-router-dom";
import _ from "lodash";


/**
 * @deprecated
 */
const ImageUploadPopup = (props) => {
  const alert = useAlert();
  const dispatch = useDispatch();
  const [fileName, setFileName] = useState("");
  const [showEditer, setShowEditer] = useState(false);
  const [file, setFile] = useState(null);
  const [image, setImage] = useState(null);
  const {event_uuid} = useParams();
  const {t} = useTranslation("details", "notification");


  const handlePopupClose = () => {
    setShowEditer(false);
    props.closePopup();
  };

  const uploadImage = (files) => {
    props.closePopup();
    const formData = new FormData();
    formData.append("event_uuid", event_uuid);
    formData.append("_method", "POST");
    formData.append("event_live_image", files);

    try {
      dispatch(eventAction.uploadEventLiveImage(formData))
        .then((res) => {
          alert.show("Successfully Uploaded", {type: "success"});
          props.onUpload();
        })
        .catch((err) => {
          if (err && _.has(err.response.data, ["errors"])) {
            var errors = err.response.data.errors;
            for (let index in errors) {
              alert.show(errors[index], {type: "error"});
            }
          } else if (err && _.has(err.response.data, ["msg"])) {
            var er = err.response.data.msg;
            if (er) {
              alert.show(er, {type: "error"});
            }
          } else {
            alert.show(Helper.handleError(err), {type: "error"});
          }
        });
    } catch (err) {
      alert.show(Helper.handleError(err), {type: "error"});
    }
  };


  return (
    <div>

      <Dialog open={props.popupVisibility} onClose={handlePopupClose}>
        <IconButton
          aria-label="close"
          onClick={handlePopupClose}
          sx={{
            position: "absolute",
            right: 8,
            top: 8,
            color: (theme) => theme.palette.grey[500],
          }}
        >
          <CloseIcon />
        </IconButton>
        <DialogContent>
          <ImgCropper
            saveImage={uploadImage}
            capturedImgURL={props.imageUrl ? props.imageUrl : ""}
            aspect={16 / 9}
          />
        </DialogContent>
      </Dialog>
    </div>
  );
};

export default ImageUploadPopup;
