import React from 'react';
import _ from 'lodash';
import {Button, Grid} from '@material-ui/core';
import ColorPicker from '../Common/ColorPicker/ColorPicker';
import ToggleKey from '../Common/ToggleKey/ToggleKey';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to customize space host(system role) section. It allow to manage 2 settings
 * from here :<br>
 * 1.Hide Host Component if host is offline - This will not render space host section on attendee side if space host is
 * not online(available).<br>
 * 2.Background Color - we can change color of space host section from here.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received space host related values and functions from it's parent component
 * @param {String} props.SubHeading Small heading placed at top of space host section.
 * @param {Function} props.callBack Function for to take current value.
 * @param {Function} props.callBackCancel Function for cancel button in space host section.
 * @param {Function} props.child Function which return child components for space host section when main switch is ON.
 * @param {Boolean} props.color_modal Boolean to render reset color modal
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key eg - get data for space host 
 * section.
 * @param {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @param {String} props.heading Main heading text placed at top
 * @param {Object} props.icon Icon to update the value
 * @param {String} props.icon.field Icon field name (ex:-business_team_icon)
 * @param {String} props.icon.value Icon updated image url
 * (ex:-https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/label_icons/business_team_altImage/galaxy-space-mu-1366x768.jpg)
 * @param {Function} props.resetColorHandler Function to reset all colors of space host section in primary color.
 * @param {String} props.reset_color Current section name to render reset color functionality into it eg - here
 * reset_color value will be "space host".
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const SpaceHost = (props) => {
    const sh_background = props.getKeyData('sh_background')

    return (
        <div>
            <ToggleKey dataSubKey={'sh_hide_on_off'}  {...props} />
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Background Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={sh_background} callBack={props.callBack} />
                    </Grid>
                </Grid>
            </Grid>
        </div>

    )
}

export default SpaceHost;