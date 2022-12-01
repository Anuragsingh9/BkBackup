import React from 'react';
import MenuItem from '@mui/material/MenuItem';
import Select from '@mui/material/Select';
import "./ImgSlider.css";

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed for the image selector dropdown menu used for select scenery in mapping the
 * venue component.This component is getting data(scenery's image data) from its parameter.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Data to show images in mapping venue>scenery section
 * @param {SceneryImgSliderObj} props.images Scenery slider images data
 * @param {String} props.sceneryImg Url of the image
 * @param {Function} props.setSceneryImg Function to set selected image as a scenery image.
 * @returns {JSX.Element}
 */
const ImgSlider = (props) => {
    const {sceneryImg, setSceneryImg} = props;
    return (
        <div className="ImgSliderWrap">
            <Select
                value={sceneryImg}
                onChange={(e) => {
                    setSceneryImg(e.target.value)
                }}
                size="small"
                className="scenery_custom_ImgSelector"
            >
                {props.images.map((asset) => {
                    return <MenuItem value={asset.asset_id} className="Scenery_li_item">
                        <div className='img_scenery' style={{backgroundImage: `url(${asset.asset_path})`}}></div>
                    </MenuItem>
                })}

            </Select>
        </div>
    )
}

export default ImgSlider