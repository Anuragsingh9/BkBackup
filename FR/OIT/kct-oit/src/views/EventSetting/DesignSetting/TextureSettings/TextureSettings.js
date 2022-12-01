import React from 'react';
import {Button, Grid} from '@material-ui/core';
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import ToggleKey from '../Common/ToggleKey/ToggleKey';
import Icon from '../../../../Models/Icon';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to handle texture customization. By using this component we can manage
 * 1. Corners are either sharp or rounded
 * 2. show/hide frame(white border around all main components eg- content player, conversation block, usergrid section).
 * 3. show/hide shadows.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received texture setting related values and functions from it's parent component
 * @param {String} props.SubHeading Small heading placed at top.
 * @param {Function} props.callBack Function for to take current value.
 * @param {Function} props.callBackCancel Function for cancel button when user want to go back
 * @param {Function} props.child Function which return child components when main switch is ON.
 * @param {Boolean} props.color_modal Boolean to render reset color modal
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key
 * @param {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @param {String} props.heading Main heading text placed at top
 * @param {Icon} props.icon Icon to update the value
 * @param {Function} props.resetColorHandler Function to reset all colors of primary color.
 * @param {String} props.reset_color Current section name to render reset color functionality into it
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const TextureSettings = (props) => {
    const {checkGroupDisable} = props;

    return (
        <div>
            <ToggleKey
                dataSubKey={'texture_square_corners'}
                toolTip={props.tooltip_labels.sharp_corners_tooltip}
                iconInfo={<InfoOutlinedIcon />} {...props}
                disabled={checkGroupDisable}
            />

            <ToggleKey
                dataSubKey={'texture_remove_frame'}
                disabled={checkGroupDisable}
                {...props}
            />

            <ToggleKey
                dataSubKey={'texture_remove_shadows'}
                disabled={checkGroupDisable}
                {...props}
            />
        </div>
    )
}

export default TextureSettings;