import React from 'react';
import {Grid} from '@material-ui/core';
import _ from 'lodash';
import ColorPicker from '../Common/ColorPicker/ColorPicker';
import Icon from '../../../../Models/Icon';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component allow us to manage user's badge(where user can see their added details like name, company
 *  union etc.) color.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received user badge related values and functions from it's parent component
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
const UserBadgeSettings = (props) => {
    const {checkGroupDisable} = props;
    // getting data according to key value
    const data = {
        user_badge_bg_color: props.getKeyData('badge_background'),
    }

    return (
        <div>
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Badge Popup Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.user_badge_bg_color} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
            </Grid>
        </div>
    )
}

export default UserBadgeSettings;