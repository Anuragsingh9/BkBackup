import React from 'react';
import Helper from "../../../Helper";
import _ from 'lodash';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering/displaying customisable svg in header .
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {GraphicsData} graphics_data [State] This variable holds the current graphics data set in redux
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const SvgIcon = ({graphics_data}) => {
    let colorObj = {
        color1: '',
        color2: '',
    };

    if (_.has(graphics_data, ['event_color_1']) && _.has(graphics_data, ['event_color_2'])) {

        colorObj.color1 = Helper.rgbaObjectToStr(graphics_data.event_color_1);
        colorObj.color2 = Helper.rgbaObjectToStr(graphics_data.event_color_2);
    }
    return (
        <a href="https://www.humannconnect.com" target='_blank'>
        <span className="wonder-svg">
            
            <svg xmlns="http://www.w3.org/2000/svg" width="1912" height="1050.422" viewBox="0 0 1912 1050.422">
            <g id="Group_2" data-name="Group 2" transform="translate(565 177)">
                <g id="Group_1" data-name="Group 1" transform="translate(0 -335)">
                <path id="Rectangle_2" data-name="Rectangle 2"
                      d="M64,0H956a0,0,0,0,1,0,0V1048a0,0,0,0,1,0,0H64A64,64,0,0,1,0,984V64A64,64,0,0,1,64,0Z"
                      transform="translate(-565 158)" fill="#3b3b3b" />
                <path id="Rectangle_1" data-name="Path 2" d="M0,0H892a64,64,0,0,1,64,64V984a64,64,0,0,1-64,64H0Z"
                      transform="translate(391 158)" fill="#0589b8" />
                </g>
                <path id="Path_1" data-name="Path 1" d="M250,599.5c-74.219-11.719-218.75-93.75-250-250"
                      transform="translate(455.058 425.851) rotate(-45)" fill="none" stroke="#fff" stroke-width="34" />
            </g>
            </svg>
       </span>
        </a>
    )

}

export default SvgIcon;