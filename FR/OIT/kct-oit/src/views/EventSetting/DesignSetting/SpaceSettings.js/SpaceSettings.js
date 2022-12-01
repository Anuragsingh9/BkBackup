import React from 'react';
import {Grid} from '@material-ui/core';
import Tooltip from '@material-ui/core/Tooltip';
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import ColorPicker from '../Common/ColorPicker/ColorPicker';
import ToggleKey from '../Common/ToggleKey/ToggleKey';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to customize space section. From this section we can customize background
 * color of space section, selected space design(rounded/square), unselected space design.We can also manage user grid
 * (where all active participants of an event will appear) height from here.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received space host related values and functions from it's parent component
 * @param {String} props.SubHeading Small heading placed at top of space section.
 * @param {Function} props.callBack Function for to take current value.
 * @param {Function} props.callBackCancel Function for cancel button in space section.
 * @param {Function} props.child Function which return child components for space section when main switch is ON.
 * @param {Boolean} props.color_modal Boolean to render reset color modal.
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key eg - get data for space section.
 * @param {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @param {String} props.heading Main heading text placed at top
 * @param {Object} props.icon Icon to update the value
 * @param {String} props.icon.field Icon field name (ex:-business_team_icon)
 * @param {String} props.icon.value Icon updated image url
 * (ex:-https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/label_icons/business_team_altImage/galaxy-space-mu-1366x768.jpg)
 * @param {Function} props.resetColorHandler Function to reset all colors of space section in primary color.
 * @param {String} props.reset_color Current section name to render reset color functionality into it eg - here
 * reset_color value will be "space host".
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const SpaceSetting = (props) => {
    const space_background = props.getKeyData('space_background')
    const {checkGroupDisable} = props;

    return (
        <div>
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'>
                            Background Color :
                            <Tooltip arrow title={props.tooltip_labels.space_setting}>
                                <InfoOutlinedIcon />
                            </Tooltip>
                        </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={space_background} callBack={props.callBack} disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
            </Grid>
            <ToggleKey dataSubKey={'extends_color_user_guide'} {...props} disabled={checkGroupDisable} />
            <ToggleKey dataSubKey={'selected_spaces_square'} {...props} isSpace={true} disabled={checkGroupDisable} />
            <ToggleKey dataSubKey={'unselected_spaces_square'}   {...props} isSpace={true}
                       disabled={checkGroupDisable} />
        </div>
    )
}

export default SpaceSetting;
