import React from 'react';
import {Grid} from '@material-ui/core';
import ColorPicker from '../Common/ColorPicker/ColorPicker';
import Icon from '../../../../Models/Icon';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to customize all event/professional/personal tags(tags category) color and
 * we can also change text color for all tags from here.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received tags related values and functions from it's parent component
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
const TagsSettings = (props) => {
    const data = {
        event_tag_color: props.getKeyData('event_tag_color'),
        professional_tag_color: props.getKeyData('professional_tag_color'),
        personal_tag_color: props.getKeyData('personal_tag_color'),
        tags_text_color: props.getKeyData('tags_text_color'),
    }
    const {checkGroupDisable} = props;

    return (
        <div>
            <Grid container xs={12}>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Event Tags Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.event_tag_color} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Professional Tags Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.professional_tag_color} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Personal Tags Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.personal_tag_color} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>

                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'> Tags Text Color : </p>
                    </Grid>
                    <Grid item xs={4}>
                        <ColorPicker color={data.tags_text_color} callBack={props.callBack}
                                     disabled={checkGroupDisable} />
                    </Grid>
                </Grid>
            </Grid>
        </div>
    )
};

export default TagsSettings;