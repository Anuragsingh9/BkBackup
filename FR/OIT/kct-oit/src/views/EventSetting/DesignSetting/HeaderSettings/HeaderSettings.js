import React, {useState} from 'react';
import {TextField} from '@material-ui/core';
import ColorPicker from '../Common/ColorPicker/ColorPicker';
import _ from 'lodash';
import './HeaderSetting.css';
import {Button, Grid} from '@material-ui/core';
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import KeepMountedModal from '../Common/ModalBox/Modal';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to customize the header section.From here we can modify header line 1 and
 * header line 2 text(In attendee side it appear at left side on header)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received header settings related values and functions from it's parent
 * file("DesignSetting.js").
 * @param {String} props.SubHeading Small heading placed at top of header setting section in design setting.
 * @param {Function} props.callBack Function for color picker to take current value.
 * @param {Function} props.callBackCancel Function for cancel button in header setting section.
 * @param {Function} props.child Function which return child components for header setting section when main switch is
 * ON.
 * @param {Boolean} props.color_modal Boolean to render reset color modal in header setting section.
 * @param {String} props.dataKey Unique key for component.
 * @param {Function} props.getKeyData Function to get current value for a specific key eg - get data for header setting
 * section.
 * @param {Array} props.graphicSetting Array which consist all design settings value in key value pair object.
 * @param {String} props.heading Main heading text placed at top of header setting section in design setting.
 * @param {Object} props.icon Icon to render at top of header setting section in design setting.
 * @param {Function} props.resetColorHandler Function to reset all colors of header setting section in primary color.
 * @param {String} props.reset_color Current section name to render reset color functionality into it eg - here
 * reset_color value will be "content".
 * @param {Function} props.updateDesignSetting Function used to update design setting values.
 * @returns {JSX.Element}
 * @constructor
 */
const HeaderSettings = (props) => {
    // getting data and store in object
    const data = {
        header_line_1: props.getKeyData('header_line_1'),
        header_line_2: props.getKeyData('header_line_2'),
        color2: props.getKeyData('color2'),
        header_bg_color_1: props.getKeyData('header_bg_color_1'),
        header_text_color: props.getKeyData('header_text_color'),
        header_separation_line_color: props.getKeyData('header_separation_line_color'),
    }
    const mainColor = {
        color1: props.getKeyData('main_color_1'),
        color2: props.getKeyData('main_color_2'),
    }
    const {checkGroupDisable} = props;


    return (
        <div className="HeaderDesignDiv">
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Header Line 1 : </p>
                    </Grid>
                    <Grid item xs={2}>
                        <TextField
                            size="small"
                            className="longInput"
                            variant="outlined"
                            inputProps={{maxLength: 44}}
                            defaultValue={_.has(data.header_line_1, ['value']) ?
                                data.header_line_1.value :
                                ''}
                            onChange={(e) => {
                                props.callBack({
                                    field: 'header_line_1',
                                    value: e.target.value
                                })
                            }}
                            error={props.headerLine1Err}
                            helperText={
                                props.headerLine1Err
                                    ? 'The Event Header Line 1 of the event must not exceed 44 characters'
                                    : ' '
                            }
                            disabled={checkGroupDisable}
                        />
                    </Grid>
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Header Line 2 : </p>
                    </Grid>
                    <Grid item xs={2}>
                        <TextField
                            size="small"
                            className="longInput"
                            variant="outlined"
                            inputProps={{maxLength: 44}}
                            defaultValue={_.has(data.header_line_2, ['value']) ? data.header_line_2.value : ''}
                            onChange={(e) => {
                                props.callBack({
                                    field: 'header_line_2',
                                    value: e.target.value
                                })
                            }}
                            error={props.headerLine2Err}
                            helperText={
                                props.headerLine2Err
                                    ? 'The Event Header Line 2 of the event must not exceed 56 characters'
                                    : ' '
                            }
                            disabled={checkGroupDisable}
                        />
                    </Grid>
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Background Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.header_bg_color_1} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Text Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.header_text_color} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Separation Line Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.header_separation_line_color} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
            </Grid>
        </div>

    )
}

export default HeaderSettings;