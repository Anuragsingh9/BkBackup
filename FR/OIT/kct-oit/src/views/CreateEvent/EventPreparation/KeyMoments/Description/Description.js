import React from 'react';
import NameInput from '../Common/Inputs/Name.js';
import DescriptionInput from '../Common/Inputs/Description.js';
import ContentDetails from './ContentDes/index.js';
import {Grid, TextField, FormControl, InputLabel, Select, MenuItem, Checkbox, Button, Link} from '@material-ui/core';
import Validation from '../../../../../functions/ReduxFromValidation.js';
import _ from "lodash";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is parent component for handling changes and showing details for key moments
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {MomentObj} props Props passed from parent component
 * @returns {JSX.Element}
 * @constructor
 */
const Details = (props) => {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for validation of moment name
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {Boolean}
     */
    const validateName = () => {
        if (props.momentData.name) {
            return false;
        }
        return true
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handle changes for Content name changes and update the moment data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleContentNameChange = (e) => {
        let newData = {...props.momentData};
        newData.name = e.target.value;
        props.onMomentUpdate(newData);
    }

    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handle changes for Content description name changes and update the moment
     * data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     */
    const handleContentDescriptionChange = (e) => {
        let newData = {...props.momentData};
        newData.moment_description = e.target.value;
        props.onMomentUpdate(newData);
    }

    return (
        <div className="PB-20px">
            <Grid container spacing={3}>
                <Grid item lg={2} className="pt-4px">
                    <p className="customPara">Name:</p>
                </Grid>
                <Grid item lg={4}>
                    <NameInput
                        name={`name-${props.index}-${props.contentType !== null ? 'content' : ''}`}
                        value={props.momentData.name ? props.momentData.name : ''}
                        placeholder="Enter Name"
                        validation={validateName() ? Validation.required : []}
                        onChange={handleContentNameChange}
                        disabled={props.disabled}
                    />
                </Grid>
            </Grid>

            <Grid container spacing={3}>
                <Grid item lg={2} className="pt-4px">
                    <p className="customPara">Description:</p>
                </Grid>
                <Grid item lg={4} className="KeyMomentDis">
                    <DescriptionInput
                        name={`description-${props.index}-${props.contentType !== null ? 'content' : ''}`}
                        value={props.momentData.moment_description ? props.momentData.moment_description : ''}
                        index={props.index}
                        handleChange={handleContentDescriptionChange}
                        disabled={props.disabled}
                        autoCreate={props.autoCreate}
                    />
                </Grid>
            </Grid>


            {props.momentData.contentType !== null &&
            <Grid container spacing={3}>
                <Grid item lg={2} className="pt-4px">
                    <p className="customPara">Content Type:</p>
                </Grid>
                <Grid item lg={4} className="ContentDetailsDropDown">
                    <ContentDetails
                        availableBroadcasts={props.availableBroadcasts}
                        momentData={props.momentData}
                        disabled={props.disabled}
                        validateSelectedConf={props.validateSelectedConf}
                        onMomentUpdate={props.onMomentUpdate}
                        autoCreate={props.autoCreate}
                    />
                </Grid>
            </Grid>
            }
        </div>
    )

}

export default Details;