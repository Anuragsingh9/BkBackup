import React, {useEffect, useState} from "react";
import "./MediaSelector.css";
import Svg from "../../../Svg";
import {connect} from 'react-redux';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to handle media value
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.data Media data
 * @param {Number} props.data.length Length of media data
 * @param {ContentManagementMeta} props.contentManagementMeta Content Related Props from redux store for current content
 * @param {Function} props.handleMediaChange This function is used to change media
 * @param {Number} props.currentMediaType Current selected type of content, e.g. 1 for Video
 * @param {Function} props.handleMediaChange This function is used to handle the media change
 * @param {String} props.name media name
 * @param {Number} props.type media type
 *
 * @returns {JSX.Element}
 * @constructor
 */
const MediaSelector = (props) => {
    const [selectItem, setselectItem] = useState(props.data[0]);
    const [showSelectedItem, setShowSelectedItem] = useState(false);
    const [carrotIcon, setCarrotIcon] = useState(true);
    const [activeBtn, setActiveBtn] = useState();
    const currentSelectedItem = props.contentManagementMeta.currentMediaType;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle media click value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param link
     */
    const handleMediaClick = (link) => {
        setselectItem(link);
        props.handleMediaChange(link.key)
    }

    useEffect(() => {

        if (props.contentMediaType === props.contentManagementMeta.currentMediaType) {
            setselectItem(props.contentManagementMeta.currentMediaData);
        }
    }, [props.contentManagementMeta.currentMediaType]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to handle click value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleClick = () => {
        if (props.contentMediaType === props.contentManagementMeta.currentMediaType) {
            // current item is already selected and clicked again so set it to null
            props.handleMediaChange();
        } else {
            props.handleMediaChange(selectItem.key);
        }
    }

    const hoverIcon = {
        image: {
            play: Svg.ICON.eye_open_icon,
            pause: Svg.ICON.eye_close_icon
        },
        video: {
            play: Svg.ICON.play_video_icon,
            pause: Svg.ICON.pause_video_icon
        }
    }
    const selectedIcon = props.contentMediaType === props.contentManagementMeta.currentMediaType;

    return (
        <div>
            {/* <select className={"form-control"} onChange={(e) => props.handleMediaChange(e.target.value)}>
                <option value={null} >Select Media</option>
                {props.data.map((link) => {
                    return <option value={link.key}>{link.value}</option>;
                })}
            </select> */}

            <div className="dropdown dropup pannel_media_selector">
                {props.data.length > 1 &&
                <a
                    className="drop-btn dropdown-toggle pannelToggleBtn"
                    href="#"
                    role="button"
                    id="dropdownMenuLink"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                        <span onClick={() => setCarrotIcon(!carrotIcon)}
                              dangerouslySetInnerHTML={{__html: carrotIcon ? Svg.ICON.carrot_icon_up : Svg.ICON.carrot_icon_up}}></span>
                </a>
                }
                <button
                    className={`escape_other media_selector ${props.contentMediaType === props.contentManagementMeta.currentMediaType ? "pannel_active_media" : ""}`}
                    onClick={handleClick}
                    name={props.name}
                    id={props.type}
                >

                    <div style={{
                        backgroundImage: `url(${selectItem.thumbnail_path})`,
                        cursor: "pointer",
                        backgroundSize: "cover",
                        backgroundPosition: "center",
                        width: "100%",
                        height: "100%"
                    }}></div>
                    <div className="hover__overlay">
                        <div dangerouslySetInnerHTML={{
                            __html: props.name === "video" ?
                                selectedIcon ? hoverIcon.video.pause : hoverIcon.video.play
                                :
                                selectedIcon ? hoverIcon.image.pause : hoverIcon.image.play
                        }}></div>
                    </div>

                </button>
                {props.data.length > 1 &&
                <div className="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <p className="selectItem_txt">Select {props.name}</p>
                    {props.data.map((link) => {
                        return <div className="dropdown-item media_selector"
                                    value={link.key}
                                    onClick={() => handleMediaClick(link)}
                                    style={{
                                        backgroundImage: `url(${link.thumbnail_path})`
                                    }}
                        >
                            <div className="hover__overlay">
                                <div dangerouslySetInnerHTML={{
                                    __html: props.name === "video" ? hoverIcon.video.play : hoverIcon.image.play
                                }}></div>
                            </div>
                        </div>;
                    })}
                    {/* <div className="triangle__arrow"></div> */}
                </div>
                }
            </div>
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

const mapStateToProps = (state) => {
    return {
        contentManagementMeta: state.NewInterface.contentManagementMeta,
        gridMeta: state.NewInterface.gridMeta,
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(MediaSelector);
// export default MediaSelector;