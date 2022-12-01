import React from 'react';
import {Grid} from '@material-ui/core';
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import _ from 'lodash';
import Tooltip from '@material-ui/core/Tooltip';
import ColorPicker from '../Common/ColorPicker/ColorPicker';
import Icon from '../../../../Models/Icon';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to customize user grid(where all active participants of an event appear)
 * background and pagination color(it will appear if active user limit will be exceed from a certain number).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received user grid related values and functions from it's parent component
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
const UserGridSettings = (props) => {
    // getting data from key value
    const data = {
        user_grid_bg_color: props.getKeyData('user_grid_background'),
        user_grid_pagi_color: props.getKeyData('user_grid_pagination_color'),
    }
    const {checkGroupDisable} = props;

    return (
        <div>
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'>
                            Background Color :
                            <Tooltip arrow title={props.tooltip_labels.user_grid_bg_color}>
                                <InfoOutlinedIcon />
                            </Tooltip>
                        </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.user_grid_bg_color} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Pagination Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.user_grid_pagi_color} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
            </Grid>
        </div>
    )
}

export default UserGridSettings;