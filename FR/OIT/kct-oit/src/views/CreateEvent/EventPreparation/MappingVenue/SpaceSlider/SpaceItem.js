import React, {useEffect, useRef, useState} from 'react';
import _ from 'lodash';
import {connect} from 'react-redux';
import {useTranslation} from 'react-i18next';
import PenIcon from '../../../../Svg/PenIcon';
import CloseIcon from '../../../../Svg/closeIcon.js';
import './SpaceItem.css';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render/display particular space(for mapping the venue space slider) with
 * some action buttons(selected space, edit space, delete space).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Object} props.value Space data
 * @param {String} props.value.space_uuid Space uuid
 * @param {Number} props.value.is_vip_space Is space VIP or not
 * @param {String} props.value.space_name Space name
 * @param {String} props.value.space_short_name Space short name
 * @param {Number} props.value.max_capacity Space max user capacity
 * @param {Function} props.deleteSpace Function is used to delete space
 * @param {String} props.venueType Space type(mono, normal)
 * @param {Function} props.showSpaceLine Function is used to show space data
 * @param {String} props.selectedItem Selected space
 * @returns {JSX.Element}
 * @constructor
 */
const SpaceItem = (props) => {
    // Intialisation fo message / alert ref to show alerts on success or error.
    const msg = useRef(null);
    const {t} = useTranslation(['spaces', 'notification']);
    const [data, setData] = useState();

    useEffect(() => {
        setData({props})
    }, [props])


  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description This function will trigger deleteSpace function(received from prop) to perform delete space action.
   * This will take space id from its parameter and pass it to the deleteSpace function.
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @method
   */
  const handleDeleteSpace = () => {
    if (_.has(props.value, ["space_uuid"])) {
      props.deleteSpace(props.value.space_uuid)
    }
  }

  return (
    <>
      <div className='spaceCircle'>
        {
          props.venueType != '1' &&
          <CloseIcon className="DeletSpaceIcon" style={{cursor: "pointer"}} onClick={handleDeleteSpace} />
        }


                <div onClick={() => props.showSpaceLine(props.value)} key={props.value.space_uuid}
                     className={props.selectedItem && props.selectedItem === props.value.space_uuid ?
                         `roundSpace spaceSelected  ${props.value.is_vip_space == 1 ? " vipSpace" : "normalSpace"}`
                         : props.value.is_vip_space == 1 ? "roundSpace vipSpace" : "roundSpace normalSpace"}>
                    <div className="HoverEdit">
                        <span><PenIcon/></span>
                    </div>
                    {props.selectedItem && props.selectedItem === props.value.space_uuid &&
                    <small>You are here</small>
                    }

                    <span className="SpaceName">
            {props.value.space_name}
          </span>
                    <span>
            {props.value.space_short_name}
          </span>
                    <span className="SpacePeopleNumber">{props.value.max_capacity}</span>

                    {/* <small>Guests</small> */}
                </div>

            </div>
        </>
    )

}

const mapStateToProps = (state) => {
    return {};
};

const mapDispatchToProps = (dispatch) => {
    return {}
}


export default connect(mapStateToProps, mapDispatchToProps)(SpaceItem);

