import React, {useEffect, useState} from "react";
import LiveImageDeleteIcon from "../../Svg/LiveImageDeleteIcon";
import "./MediaGridImage.css";
import {confirmAlert} from "react-confirm-alert";
import {useDispatch} from 'react-redux';
import eventAction from '../../../redux/action/apiAction/event';
import {useParams} from 'react-router-dom'
import {useAlert} from 'react-alert';
import Helper from '../../../Helper'
import _ from 'lodash';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render uploaded assets(image/video) for content player in live tab
 * component. This will show all assets in a tile structure with delete option on hovering it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {String} props.keyValue Unique key of each asset
 * @param {Function} props.onDelete This method will delete an asset according to the given key
 * @param {Function} props.onUpload This method will upload the selected asset(Image/Video)
 * @param {String} props.type Type of asset (1. Images 2. Video)
 * @returns {JSX.Element}
 * @constructor
 */
const MediaGridImage = (props) => {
  const dispatch = useDispatch();
  const {event_uuid} = useParams()
  const alert = useAlert();


  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function will open a popup component and take confirmation to perform delete asset(image/video
   * from live tab) action.That popup component contains 2 button('Confirm', 'Cancel'). If user click on 'Confirm'
   * then it will pass asset's key(which need to be delete) to 'deleteLiveImage' function otherwise it will
   * close the popup if user clicks on 'Cancel' button.
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   * @param {String} key Unique key of the asset
   */
  const onDelete = (key) => {
    confirmAlert({
      message: `Are you sure you want to delete ?`,
      confirmLabel: "confirm",
      cancelLabel: "cancel",
      buttons: [
        {
          label: "Yes",
          onClick: () => {
            deleteLiveImage(key);
          },
        },
        {
          label: "No",
          onClick: () => {
            return null;
          },
        },
      ],
    });
  };

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function will handle an API call to remove an asset(uploaded image/video in live tab component).
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   * @param {String} key Unique key of the asset
   */
  const deleteLiveImage = (key) => {
    const dataVal = {
      "event_uuid": event_uuid,
      "_method": 'DELETE', "key": key,
      "type": props.type == 'images' ? 'image' : 'video'
    };

    try {
      dispatch(eventAction.deleteEventLiveImage(dataVal)).then((res) => {
        alert.show('Successfully Deleted', {type: "success"});
        // this will remove data from image array in front side
        props.onDelete(props.keyValue)
        // let draftEvents = draftEventData.filter(event => event.event_uuid !== data);

      }).catch((err) => {
        if (err && _.has(err.response.data, ['errors'])) {
          var errors = err.response.data.errors;
          for (let index in errors) {
            alert.show(errors[index], {type: 'error'});
          }
        }
        else if (err && _.has(err.response.data, ["msg"])) {
          var er = err.response.data.msg;
          if (er) {alert.show(er, {type: 'error'});}
        }
        else {
          alert.show(Helper.handleError(err), {type: 'error'});
        }
      })
    } catch (err) {
      alert.show(Helper.handleError(err), {type: "error"})
    }
  };


  return (
    <div key={props.keyValue} className="singleImageWrapper" style={{
      backgroundImage: `url(${props.src})`
    }}>
      <div
        className="deleteIconWrapper"
        onClick={() => onDelete(props.keyValue)}
      >
        <LiveImageDeleteIcon />
      </div>
    </div>
  );
};

export default MediaGridImage;
