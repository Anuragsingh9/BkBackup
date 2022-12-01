import React, {useState} from 'react';
import _ from 'lodash';
import {Grid} from '@material-ui/core';
import ColorPicker from '../Common/ColorPicker/ColorPicker';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is designed to customize(change background and text color) all buttons for attendee side.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.getKeyData This method extract the data from redux store according to the key provided
 * @param {Function} props.updateDesignSetting This method is responsible for updating the design settings data
 * @param {Function} props.callBack This method is used to save current button value
 * @returns {JSX.Element}
 * @constructor
 */
const ButtonSettings = (props) => {
    const data = {
        customized_join_button_bg: props.getKeyData('customized_join_button_bg'),
        customized_join_button_text: props.getKeyData('customized_join_button_text'),
    }
    const {checkGroupDisable} = props;

    return (
        <div>
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Background Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.customized_join_button_bg} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Text Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.customized_join_button_text} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
            </Grid>
        </div>
    )
}

export default ButtonSettings;