import React from 'react';
import {Grid, Button} from '@material-ui/core';
import _ from 'lodash';
import ColorPicker from '../Common/ColorPicker/ColorPicker';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to manage background color of conversation block in attendee side.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received conversation settings related values and functions from it's parent
 * file("DesignSetting.js").
 * @param {String} props.SubHeading Small heading placed at top of conversation setting section in design setting.
 * @param {Function} props.callBack Function for color picker to take current value.
 * @param {Function} props.callBackCancel Function for cancel button in conversation setting section.
 * @param {Function} props.child Function which return child components for conversation setting setion when maiun
 * switch is ON.
 * @param {Boolean} props.color_modal Boolean to render reset color modal in conversation setting section.
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key eg - get data for conversation
 * setting section.
 * @param {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @param {String} props.heading Main heading text placed at top of conversation setting section in design setting.
 * @param {Object} props.icon Icon to render at top of conversation setting section in design setting.
 * @param {Function} props.resetColorHandler Function to reset all colors of conversation setting section in primary
 * color.
 * @param {String} props.reset_color Current section name to render reset color functionality into it eg - here
 * reset_color value will be "content".
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const ConversationSettings = (props) => {
    const conv_background = props.getKeyData('conv_background');
    const {checkGroupDisable} = props;

    return (
        <div>
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Background Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={conv_background} callBack={props.callBack} disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
            </Grid>
        </div>
    )
}

export default ConversationSettings;